<?php

declare(strict_types=1);

namespace app\gii\model;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Yii;
use app\attributes\Implement;
use app\attributes\InjectTo;
use yii\base\NotSupportedException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\ExpressionInterface;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\generators\model\Generator as BaseGenerator;
use yii\helpers\ArrayHelper;

use function array_keys;
use function array_map;
use function array_merge;
use function array_slice;
use function array_unique;
use function count;
use function gettype;
use function implode;
use function in_array;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strlen;
use function substr;
use function vsprintf;

class Generator extends BaseGenerator
{
    private const LINE_WIDTH_LIMIT = 120 - 4 * 4;

    /** @var string */
    public $generateJunctionRelationMode = self::JUNCTION_RELATION_VIA_MODEL;

    /** @var bool */
    public $useTablePrefix = true;

    private array $traits = [];

    /** @return void */
    public function init()
    {
        parent::init();
        $this->traits = $this->findTraits();
    }

    public function generateProperties(/*TableSchema*/ $table) //: array
    {
        $list = parent::generateProperties($table);
        uksort(
            $list,
            fn($columnA, $columnB) => $this->propertySortGroup($columnA) <=> $this->propertySortGroup($columnB)
                ?: strnatcasecmp($columnA, $columnB)
                ?: strcmp($columnA, $columnB)
        );
        return $list;
    }

    public function generateLabels(/*TableSchema*/ $table) //: array
    {
        $list = parent::generateLabels($table);
        uksort(
            $list,
            fn($columnA, $columnB) => $this->propertySortGroup($columnA) <=> $this->propertySortGroup($columnB)
                ?: strnatcasecmp($columnA, $columnB)
                ?: strcmp($columnA, $columnB)
        );
        return $list;
    }

    private function propertySortGroup(string $propName): int
    {
        switch ($propName) {
            case 'id':
                return 0;
            default:
                return 1;
            case 'created_at':
                return 2;
            case 'created_by':
                return 3;
            case 'updated_at':
                return 4;
            case 'updated_by':
                return 5;
        }
    }

    public function generateUses(string $tableName): array
    {
        $use = [
            Yii::class,
            ActiveQuery::class,
            ltrim($this->baseClass, '\\'),
        ];

        $db = $this->getDbConnection();
        $schema = $db->getTableSchema($tableName);
        foreach (array_keys($schema->columns) as $columnName) {
            switch ($columnName) {
                case 'created_at':
                case 'updated_at':
                    $use[] = TimestampBehavior::class;
                    break;

                case 'created_by':
                case 'updated_by':
                    $use[] = BlameableBehavior::class;
                    break;
            }
        }

        foreach ($this->getInjectedTraits($tableName) as $trait) {
            $use[] = $trait['fqcn'];
            foreach ($trait['interfaces'] as $if) {
                $use[] = $if;
            }
        }

        sort($use, SORT_STRING);
        return array_values(array_unique($use));
    }

    public function generateBehaviors(string $tableName, int $indentCount = 8): ?string
    {
        $db = $this->getDbConnection();
        $schema = $db->getTableSchema($tableName);
        $columns = array_keys($schema->columns);

        if (
            !in_array('created_at', $columns, true) &&
            !in_array('created_by', $columns, true) &&
            !in_array('updated_at', $columns, true) &&
            !in_array('updated_by', $columns, true)
        ) {
            return null;
        }

        $indent = str_repeat(' ', $indentCount);
        $results = [];
        $results[] = 'return [';
        if (
            in_array('created_by', $columns, true) ||
            in_array('updated_by', $columns, true)
        ) {
            if (
                in_array('created_by', $columns, true) &&
                in_array('updated_by', $columns, true)
            ) {
                $results[] = '    BlameableBehavior::class,';
            } else {
                $results[] = '    [';
                $results[] = "        '__class' => BlameableBehavior::class,";
                $results[] = vsprintf("        'createdByAttribute' => %s,", [
                    in_array('created_by', $columns, true)
                        ? "'created_by'"
                        : 'false',
                ]);
                $results[] = vsprintf("        'updatedByAttribute' => %s,", [
                    in_array('updated_by', $columns, true)
                        ? "'updated_by'"
                        : 'false',
                ]);
                $results[] = '    ],';
            }
        }
        if (
            in_array('created_at', $columns, true) ||
            in_array('updated_at', $columns, true)
        ) {
            if (
                in_array('created_at', $columns, true) &&
                in_array('updated_at', $columns, true)
            ) {
                $results[] = '    TimestampBehavior::class,';
            } else {
                $results[] = '    [';
                $results[] = "        '__class' => TimestampBehavior::class,";
                $results[] = vsprintf("        'createdAtAttribute' => %s,", [
                    in_array('created_at', $columns, true)
                        ? "'created_at'"
                        : 'false',
                ]);
                $results[] = vsprintf("        'updatedAtAttribute' => %s,", [
                    in_array('updated_at', $columns, true)
                        ? "'updated_at'"
                        : 'false',
                ]);
                $results[] = '    ],';
            }
        }
        $results[] = '];';

        return implode("\n{$indent}", $results);
    }

    // public function generateRules(TableSchema $table): array
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        $urls = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }

            if (
                !$column->allowNull &&
                $column->defaultValue === null &&
                !in_array($column->name, ['created_at', 'updated_at', 'created_by', 'updated_by'], true)
            ) {
                $types['required'][] = $column->name;
            }

            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_TINYINT:
                    $types['integer'][] = $column->name;
                    break;

                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;

                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;

                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_JSON:
                    $types['safe'][] = $column->name;
                    break;

                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
                    if ($column->name === 'url' || preg_match('/_url$/i', $column->name)) {
                        $urls[] = $column->name;
                    }
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            foreach ($this->formatRule($columns, $type) as $rule) {
                $rules[] = $rule;
            }
        }

        foreach ($lengths as $length => $columns) {
            $attrs = ['max' => (int)$length];
            foreach ($this->formatRule($columns, 'string', $attrs) as $rule) {
                $rules[] = $rule;
            }
        }

        foreach ($this->formatRule($urls, 'url') as $rule) {
            $rules[] = $rule;
        }

        $db = $this->getDbConnection();

        // Unique indexes rules
        try {
            $uniqueIndexes = array_merge($db->getSchema()->findUniqueIndexes($table), [$table->primaryKey]);
            $uniqueIndexes = array_unique($uniqueIndexes, SORT_REGULAR);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount === 1) {
                        $attrs = [
                            'skipOnEmpty' => true,
                            'skipOnError' => true,
                        ];
                        foreach ($this->formatRule((array)$uniqueColumns[0], 'unique') as $rule) {
                            $rules[] = $rule;
                        }
                    } elseif ($attributesCount > 1) {
                        $attrs = [
                            'skipOnEmpty' => true,
                            'skipOnError' => true,
                            'targetAttribute' => array_map(fn($v) => (string)$v, $uniqueColumns),
                        ];
                        foreach ($this->formatRule($uniqueColumns, 'unique', $attrs) as $rule) {
                            $rules[] = $rule;
                        }
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        // Exist rules for foreign keys
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            $refClassName = $this->generateClassName($refTable);
            unset($refs[0]);

            $attrs = [
                'skipOnError' => true,
                'targetClass' => new Expression(sprintf('%s::class', $refClassName)),
                'targetAttribute' => array_map(fn($v) => (string)$v, $refs),
            ];
            foreach ($this->formatRule(array_keys($refs), 'exist', $attrs) as $rule) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    private function formatRule(array $columns, string $type, array $attributes = [], bool $doNotSplit = false): array
    {
        if (empty($columns)) {
            return [];
        }

        if (empty($attributes)) {
            $results = [];
            if ($doNotSplit) {
                $results[] = vsprintf('[[%s], %s],', [
                    implode(', ', array_map(
                        fn($v) => $this->quote($v),
                        $columns,
                    )),
                    $this->quote($type),
                ]);
            } else {
                while (!empty($columns)) {
                    for ($i = count($columns);; --$i) {
                        $rule = vsprintf('[[%s], %s],', [
                            implode(', ', array_map(
                                fn($v) => $this->quote($v),
                                array_slice($columns, 0, $i),
                            )),
                            $this->quote($type),
                        ]);
                        if ($i === 1 || strlen($rule) <= static::LINE_WIDTH_LIMIT) {
                            $results[] = $rule;
                            $columns = array_slice($columns, $i);
                            break; // for
                        }
                    }
                }
            }
            return $results;
        }

        $results = [];
        if ($doNotSplit) {
            $columnsLine = vsprintf('[%s], %s,', [
                implode(', ', array_map(
                    fn($v) => $this->quote($v),
                    $columns,
                )),
                $this->quote($type),
            ]);
            $results[] = "[{$columnsLine}";
            foreach ($attributes as $attrName => $attrValue) {
                $results[] = vsprintf('    %s => %s,', [
                    $this->quote((string)$attrName),
                    $this->autoQuote($attrValue, 8),
                ]);
            }
            $results[] = '],';
        } else {
            while (!empty($columns)) {
                for ($i = count($columns);; --$i) {
                    $columnsLine = vsprintf('[%s], %s,', [
                        implode(', ', array_map(
                            fn($v) => $this->quote($v),
                            array_slice($columns, 0, $i),
                        )),
                        $this->quote($type),
                    ]);
                    if ($i === 1 || strlen($columnsLine) <= static::LINE_WIDTH_LIMIT - 5) {
                        $results[] = "[{$columnsLine}";
                        foreach ($attributes as $attrName => $attrValue) {
                            $results[] = vsprintf('    %s => %s,', [
                                $this->quote((string)$attrName),
                                $this->autoQuote($attrValue, 8),
                            ]);
                        }
                        $results[] = '],';
                        $columns = array_slice($columns, $i);
                        break; // for
                    }
                }
            }
        }
        return $results;
    }

    private function quote(string $text): string
    {
        return sprintf("'%s'", str_replace(
            ['\\', "'"],
            ['\\\\', "\\'"],
            $text
        ));
    }

    /** @param mixed $value */
    private function autoQuote($value, int $indentWidth = 0): string
    {
        if ($value === null) {
            return 'null';
        } elseif (is_string($value)) {
            return $this->quote($value);
        } elseif (is_int($value) || is_float($value)) {
            return (string)$value;
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_object($value) && ($value instanceof ExpressionInterface) && method_exists($value, '__toString')) {
            return (string)$value;
        } elseif (is_array($value) || is_object($value)) {
            $result = "[\n";
            if (is_array($value) && ArrayHelper::isIndexed($value)) {
                foreach ($value as $v) {
                    $result .= str_repeat(' ', $indentWidth + 4) . $this->autoQuote($v, $indentWidth + 4) . ",\n";
                }
            } else {
                foreach ($value as $k => $v) {
                    $result .= vsprintf("%s%s => %s,\n", [
                        str_repeat(' ', $indentWidth + 4),
                        is_int($k) ? (string)$k : $this->quote((string)$k),
                        $this->autoQuote($v, $indentWidth + 4),
                    ]);
                }
            }
            $result .= str_repeat(' ', $indentWidth) . ']';
            return $result;
        }

        throw new NotSupportedException('Unknown or unsupported type: ' . gettype($value));
    }

    private function findTraits(): array
    {
        $basePath = Yii::getAlias('@app/models/traits');
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));

        $results = [];
        foreach ($it as $entry) {
            if (
                $entry->isFile() &&
                $entry->getExtension() === 'php'
            ) {
                $fqcn = 'app\models\traits' .
                    str_replace(
                        '/',
                        '\\',
                        substr($entry->getPath(), strlen($basePath))
                    ) .
                    '\\' .
                    $entry->getBasename('.php');

                $info = [
                    'class' => $entry->getBasename('.php'),
                    'fqcn' => $fqcn,
                    'injectTo' => [],
                    'interfaces' => [],
                ];
                $ref = new ReflectionClass($fqcn);
                foreach ($ref->getAttributes() as $attr) {
                    switch ($attr->getName()) {
                        case Implement::class:
                            $info['interfaces'] = $attr->newInstance()->interfaces;
                            break;

                        case InjectTo::class:
                            $info['injectTo'] = $attr->newInstance()->targetClasses;
                            break;
                    }
                }

                if ($info['injectTo']) {
                    $results[] = $info;
                }
            }
        }
        usort($results, fn($a, $b) => strcmp($a['fqcn'], $b['fqcn']));
        return $results;
    }

    public function getInjectedTraits(string $tableName): array
    {
        $genClass = $this->ns . '\\' . $this->generateClassName($tableName);
        return array_filter(
            $this->traits,
            fn($info) => in_array($genClass, $info['injectTo'], true)
        );
    }

    public function getImplementedInterfaces(string $tableName): array
    {
        $results = [];
        foreach ($this->getInjectedTraits($tableName) as $trait) {
            $results = array_merge($results, $trait['interfaces']);
        }
        sort($results, SORT_STRING);
        return $results;
    }
}

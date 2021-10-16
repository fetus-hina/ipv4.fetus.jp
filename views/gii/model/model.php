<?php

//phpcs:disable Squiz.WhiteSpace.ControlStructureSpacing.SpacingAfterOpen

declare(strict_types=1);

use app\gii\model\Generator;
use yii\db\TableSchema;
use yii\web\View;

/**
 * @var View $this
 * @var Generator $generator
 * @var string $tableName
 * @var string $className
 * @var string $queryClassName
 * @var TableSchema $tableSchema
 * @var string[] $labels
 * @var string[] $rules
 * @var array[] $relations
 * @var array[] $properties
 */

$renderRules = function (int $indentWidth, array $rules): string {
    if (!$rules) {
        return 'return [];';
    }

    $text = "return [\n";
    foreach ($rules as $rule) {
        $text .= '    ' . $rule . "\n";
    }
    $text .= '];';

    $lines = preg_split('/\x0d\x0a|\x0d|\x0a/', $text);
    return implode("\n" . str_repeat(' ', $indentWidth), $lines);
};

$renderRelation = function (int $indentWidth, string $code): string {
    $code = str_replace('::className()', '::class', $code);
    $code = preg_replace_callback(
        '/->via/',
        fn ($m) => "\n" . str_repeat(' ', $indentWidth) . $m[0],
        $code
    );
    return $code;
};

$traits = $generator->getInjectedTraits($tableName);
$interfaces = $generator->getImplementedInterfaces($tableName);

echo "<?php\n";
?>

declare(strict_types=1);

namespace <?= $generator->ns ?>;

<?php foreach ($generator->generateUses($tableName) as $useClass) { ?>
use <?= $useClass ?>;
<?php } ?>

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data) { ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php } ?>
<?php if ($relations) { ?>
 *
<?php foreach ($relations as $name => $relation) { ?>
 * @property <?= ($relation[2] ? '' : '?') . $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php } ?>
<?php } ?>
<?php if ($className === 'Migration') { ?>
 *
 * @codeCoverageIgnore
<?php } ?>
 */
<?php if ($interfaces) { ?>
<?php $declLine = vsprintf('class %s extends %s implements %s', [
    $className,
    preg_replace('!^.+\x5c([^\x5c]+)!', '$1', $generator->baseClass),
    implode(', ', array_map(
        fn ($fqcn) => preg_replace('!^.+\x5c([^\x5c]+)!', '$1', $fqcn),
        $interfaces
    )),
]) ?>
<?php } else { ?>
<?php $declLine = vsprintf('final class %s extends %s', [
    $className,
    preg_replace('!^.+\x5c([^\x5c]+)!', '$1', $generator->baseClass),
]) ?>
<?php } ?>
<?php if (strlen($declLine) <= 120) { ?>
<?php echo $declLine . "\n" ?>
<?php } else { ?>
final class <?= $className . "\n" ?>
    extends <?= preg_replace('!^.+\x5c([^\x5c]+)!', '$1', $generator->baseClass) . "\n" ?>
<?php if ($interfaces) { ?>
    implements
        <?= implode(",\n        ", array_map(
            fn ($fqcn) => preg_replace('!^.+\x5c([^\x5c]+)!', '$1', $fqcn),
            $interfaces
        )) . "\n" ?>
<?php } ?>
<?php } ?>
{
<?php if ($traits) { ?>
<?php foreach ($traits as $trait) { ?>
    use <?= $trait['class'] ?>;
<?php } ?>

<?php } ?>
    public static function tableName(): string
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db') { ?>

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php } ?>
<?php $behaviors = $generator->generateBehaviors($tableName); ?>
<?php if ($behaviors) { ?>

    /**
     * @return mixed[]
     */
    public function behaviors()
    {
        <?= $behaviors . "\n" ?>
    }
<?php } ?>

    /**
     * @return array[]
     */
    public function rules()
    {
        <?= $renderRules(8, $rules) . "\n" ?>
    }

    /**
     * @codeCoverageIgnore
     * @return array<string, string>
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label) { ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php } ?>
        ];
    }
<?php foreach ($relations as $name => $relation) { ?>

    public function get<?= $name ?>(): ActiveQuery
    {
        <?= $renderRelation(12, $relation[0]) . "\n" ?>
    }
<?php } ?>
<?php if ($queryClassName) { ?>
<?php $queryClassFullName = $generator->ns === $generator->queryNs ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName; ?>

    public static function find(): ActiveQuery
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php } ?>
}

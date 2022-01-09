<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Connection;
use yii\db\Expression as DbExpression;

/**
 * @property-read ?string $normalizedIP
 */
final class SearchForm extends Model
{
    public ?string $query = null;

    public function formName()
    {
        return '';
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['query'], 'trim'],
            [['query'], 'required'],
            [['query'], 'string'],
            [['query'], 'ip',
                'ipv4' => true,
                'ipv6' => false,
                'subnet' => false,
            ],
        ];
    }

    /**
     * @codeCoverageIgnore
     * @return array<string, string>
     */
    public function attributeLabels()
    {
        return [
            'query' => Yii::t('app', 'Search Query'),
        ];
    }

    public function getNormalizedIP(): ?string
    {
        if (
            !preg_match(
                '/^((2([0-4][0-9]|5[0-5])|[0-1]?[0-9]?[0-9])\.){3}((2([0-4][0-9]|5[0-5])|[0-1]?[0-9]?[0-9]))$/',
                (string)$this->query
            )
        ) {
            return $this->query;
        }

        return implode('.', array_map(
            fn ($v) => (string)intval($v, 10),
            explode('.', (string)$this->query)
        ));
    }

    public function search(): ?SearchResult
    {
        if (!$this->validate()) {
            return null;
        }

        $allocCidr = AllocationCidr::find()
            ->andWhere(['>>=', '{{%allocation_cidr}}.[[cidr]]', static::pgInet((string)$this->query)])
            ->one();
        if (
            !$allocCidr ||
            !$allocCidr->block ||
            !$allocCidr->block->region
        ) {
            return null;
        }

        $mergedCidr = MergedCidr::find()
            ->andWhere(['and',
                ['region_id' => $allocCidr->block->region_id],
                ['>>=', '{{%merged_cidr}}.[[cidr]]', static::pgInet((string)$this->query)],
            ])
            ->one();
        if (!$mergedCidr) {
            return null;
        }

        return Yii::createObject([
            'class' => SearchResult::class,
            'region' => $allocCidr->block->region,
            'block' => $allocCidr->block,
            'cidr' => $allocCidr,
            'mergedCidr' => $mergedCidr,
        ]);
    }

    private static function pgInet(string $inet, ?Connection $db = null): DbExpression
    {
        return new DbExpression(vsprintf('inet %s', [
            ($db ?? Yii::$app->db)->quoteValue($inet),
        ]));
    }
}

<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Connection;
use yii\db\Expression as DbExpression;

use function array_map;
use function explode;
use function implode;
use function intval;
use function preg_match;
use function vsprintf;

/**
 * @property-read ?string $normalizedIP
 */
final class SearchForm extends Model
{
    public ?string $query = null;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
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
                (string)$this->query,
            )
        ) {
            return $this->query;
        }

        return implode('.', array_map(
            fn (string $v): string => (string)intval($v, 10),
            explode('.', (string)$this->query),
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

<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\db\Migration;

final class m210503_035044_download_template extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%comment_style}}', [
            'id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'line_begin' => $this->string()->null(),
            'line_end' => $this->string()->null(),
            'block_begin' => $this->string()->null(),
            'block_end' => $this->string()->null(),
            'PRIMARY KEY ([[id]])',
        ]);
        $this->batchInsert('{{%comment_style}}', ['id', 'name', 'line_begin', 'block_begin', 'block_end'], [
            [1, 'shell', '# ', null, null],
            [2, 'xml', '# ', '<!--', '-->'],
        ]);

        $this->createTable('{{%download_template}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(16)->notNull()->unique(),
            'name' => $this->string()->notNull()->unique(),
            'template' => $this->string()->notNull(),
            'allow' => $this->string()->null(),
            'deny' => $this->string()->null(),
            'comment_style_id' => $this->integer()->notNull()->append('REFERENCES {{%comment_style}} ([[id]])'),
            'file_begin' => $this->text()->null(),
            'file_end' => $this->text()->null(),
            'list_begin' => $this->text()->null(),
            'list_end' => $this->text()->null(),
            'usage' => $this->text()->null(),
            'can_use_in_url' => $this->boolean()->notNull()->defaultValue(true),
        ]);
        $this->batchInsert(
            '{{%download_template}}',
            [
                'key',
                'name',
                'template',
                'allow',
                'deny',
                'comment_style_id',
                'file_begin',
                'list_begin',
                'list_end',
                'usage',
                'can_use_in_url',
            ],
            [
                [
                    'plain',
                    'プレインテキスト',
                    '{cidr}',
                    null,
                    null,
                    1,
                    null,
                    null,
                    null,
                    null,
                    false,
                ],
                [
                    'apache',
                    'Apache (.htaccess)',
                    '{control} from {cidr}',
                    'allow',
                    'deny',
                    1,
                    null,
                    null,
                    null,
                    null,
                    true,
                ],
                [
                    'apache24',
                    'Apache 2.4',
                    '{control} ip {cidr}',
                    'Require',
                    'Require not',
                    1,
                    null,
                    null,
                    null,
                    null,
                    true,
                ],
                [
                    'nginx',
                    'Nginx',
                    '{control} {cidr};',
                    'allow',
                    'deny',
                    1,
                    null,
                    null,
                    null,
                    null,
                    true,
                ],
                [
                    'nginx-geo',
                    'Nginx (Geo)',
                    '  {cidr:fillSpace} 1;',
                    null,
                    null,
                    1,
                    null,
                    "geo \$ipv4_{cc} {\n  default 0;\n",
                    '}',
                    null,
                    true,
                ],
                [
                    'ipsecurity',
                    'IIS/Azure (ipSecurity)',
                    '  <add allowed="{control:xml}" ipAddress="{network:xml}" subnetMask="{subnet:xml}" />',
                    'true',
                    'false',
                    2,
                    '<?xml version="1.0" encoding="UTF-8"?>',
                    '<ipSecurity allowUnlisted="{control_not:xml}">',
                    '</ipSecurity>',
                    null,
                    true,
                ],
                [
                    'iptables',
                    'iptables',
                    '-A RULE1 -s {cidr:fillSpace} -j RULE2',
                    null,
                    null,
                    1,
                    null,
                    null,
                    null,
                    'RULE1 を "INPUT" などに、RULE2 を "ACCEPT" や "DROP" に置き換えて利用してください',
                    true,
                ],
                [
                    'postfix',
                    'Postfix',
                    '{cidr:fillSpace} {control}',
                    'OK',
                    'REJECT',
                    1,
                    null,
                    null,
                    null,
                    'smtpd_client_restrictions などに「check_client_access cidr:/path/to/file」のように指定します',
                    true,
                ],
            ],
        );
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%download_template}}');
        $this->dropTable('{{%comment_style}}');

        return true;
    }
}

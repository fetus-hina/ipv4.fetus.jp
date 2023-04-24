<?php

declare(strict_types=1);

use yii\db\Migration;

final class m210506_120942_firewalld_ipset extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%download_template}}', [
            'key' => 'firewalld-ipset',
            'name' => 'ipset (firewalld)',
            'template' => '  <entry>{cidr:xml}</entry>',
            'allow' => null,
            'deny' => null,
            'comment_style_id' => 2, // xml
            'file_begin' => '<?xml version="1.0" encoding="utf-8"?>',
            'file_end' => null,
            'list_begin' => '<ipset type="hash:net">',
            'list_end' => '</ipset>',
            'usage' => '/etc/firewalld/ipsets/*.xml として配置します',
            'can_use_in_url' => true,
        ]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%download_template}}', ['key' => 'firewalld-ipset']);
        return true;
    }
}

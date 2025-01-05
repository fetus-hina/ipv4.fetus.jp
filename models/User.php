<?php //phpcs:disable

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\base\BaseObject;
use yii\web\IdentityInterface;

/** @codeCoverageIgnore */
class User extends BaseObject implements IdentityInterface
{
    public ?string $id;
    public ?string $username;
    public ?string $password;
    public ?string $authKey;
    public ?string $accessToken;

    public static function findIdentity($id)
    {
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername(string $username): ?IdentityInterface
    {
        return null;
    }

    public function getId()
    {
        return -1;
    }

    public function getAuthKey()
    {
        return '';
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }
}

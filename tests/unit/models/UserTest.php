<?php

declare(strict_types=1);

namespace tests\unit\models;

use Codeception\Test\Unit;
use app\models\User;

class UserTest extends Unit
{
    public function testFindUserById()
    {
        $this->assertNull(User::findIdentity(999));
    }
}

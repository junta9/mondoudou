<?php

namespace App\Tests;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Null_;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    //function uni-test is true
    public function testIsTRue()
    {
        $user = new User();

        $user->setEmail('true@test.com')
        ->setFirstname('John')
        ->setLastname('Doe')
        ->setPassword('password')
        ->setMobile('0102030405');

        $this->assertTrue($user->getEmail() === 'true@test.com');
        $this->assertTrue($user->getFirstname() === 'John');
        $this->assertTrue($user->getLastname() === 'Doe');
        $this->assertTrue($user->getPassword() === 'password');
        $this->assertTrue($user->getMobile() === '0102030405');
    }

    public function testIsFalse()
    {
        $user = new User();

        $user->setEmail('true@test.com')
        ->setFirstname('John')
        ->setLastname('Doe')
        ->setPassword('password')
        ->setMobile('0102030405');

        $this->assertFalse($user->getEmail() === 'false@test.com');
        $this->assertFalse($user->getFirstname() === 'false');
        $this->assertFalse($user->getLastname() === 'false');
        $this->assertFalse($user->getPassword() === 'false');
        $this->assertFalse($user->getMobile() === 'false');
    }

    public function testIsEmpty()
    {
        $user = new User();

        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getFirstname());
        $this->assertEmpty($user->getLastname());
        // $this->assertEmpty($user->getPassword());
        $this->assertEmpty($user->getMobile());
    }
}

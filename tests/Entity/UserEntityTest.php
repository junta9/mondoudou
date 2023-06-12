<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{
    //On definit les constantes de variables pour les tests
    private const EMAIL_CONSTRAINT_MESSAGE = "L'email atchoum-du-973@gmail n'est pas valide";
    private const NOT_BLANK = "Veuillez saisir une valeur";
    private const INVALID_EMAIL_VALUE = "atchoum-du-973@gmail";
    private const INVALID_PASSWORD_VALUE = "atchoum123456";
    private const PASSWORD_REGEX_CONSTRAINT_MESSAGE = "Il faut un mot de passe de 8 caractères avec 1 majuscule, 1 miniscule, 1 chiffre et 1 caractère spécial";
    private const VALID_EMAIL_VALUE = "atchoum-du-973@gmail.com";
    private const VALID_PASSWORD_VALUE = "Atchoum@123456";

    private ValidatorInterface $validator;

    protected function setUp(): void {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    public function testUserIsValid(): void {
        $user = new User();

        $user
            ->setEmail(self::VALID_EMAIL_VALUE)
            ->setPassword(self::VALID_PASSWORD_VALUE);

        $this->getValidationErrors($user, 0);// on attend 0 erreur
    }

    public function testUserIsInvalidBecausePasswordInvalid(): void {
        $user = new User();

        $user
            ->setEmail(self::VALID_EMAIL_VALUE)
            ->setPassword(self::INVALID_PASSWORD_VALUE);
        // $errors = $this->getValidationErrors($user, 1);
        $this->getValidationErrors($user, 2);
        // $this->assertEquals(self::PASSWORD_REGEX_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
            
    }

    public function testUserIsInvalidBecauseEmailInvalid(): void {
        $user = new User();

        $user
            ->setEmail(self::INVALID_EMAIL_VALUE)
            ->setPassword(self::VALID_PASSWORD_VALUE);
        // $errors = $this->getValidationErrors($user, 1);
        $this->getValidationErrors($user, 1);// on attend 1 erreur
        // $this->assertEquals(self::PASSWORD_REGEX_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
            
    }

    private function getValidationErrors(User $user, int $numberOfExpectedErrors): ConstraintViolationList {
        $errors = $this->validator->validate($user);

        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors;
    }

}

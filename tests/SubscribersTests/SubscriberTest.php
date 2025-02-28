<?php

namespace App\Tests\SubscribersTests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Entity\Subscriber;

class SubscriberTest extends TestCase
{
    public function testSubscriberCreation()
    {
        // Création de l'abonné
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        // Vérification des valeurs
        $this->assertEquals('9785', $subscriber->getCode());
        $this->assertEquals('Dupont', $subscriber->getLastname());
        $this->assertEquals('Jean', $subscriber->getFirstname());
        $this->assertEquals('01-01-1990', $subscriber->getBirthdate()->format('d-m-Y'));
        $this->assertEquals('M', $subscriber->getCivilite());
    }

    public function testSubscriberCreationWithValidBirthdate()
    {
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('10-10-1990');
        $subscriber->setCivilite('M');

        // Assertions
        $this->assertEquals('9785', $subscriber->getCode());
        $this->assertEquals('Dupont', $subscriber->getLastname());
        $this->assertEquals('Jean', $subscriber->getFirstname());
        $this->assertInstanceOf(\DateTimeInterface::class, $subscriber->getBirthdate());
        $this->assertEquals('10-10-1990', $subscriber->getBirthdate()->format('d-m-Y'));
        $this->assertEquals('M', $subscriber->getCivilite());
    }

    public function testSubscriberCreationWithInvalidBirthdate_ShouldThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La date doit être au format 'dd-mm-yyyy'.");

        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');

        $subscriber->setBirthdate('01-32-1990');  // Format incorrect
        $subscriber->setCivilite('M');
    }

    public function testSubscriberCreationWithMissingCode_ShouldThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le code de l'adhérent est obligatoire.");

        // Créer un abonné sans code
        $subscriber = new Subscriber();
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('10-10-1990');
        $subscriber->setCivilite('M');

        // Tester l'exception lors de la tentative de création sans code
        $subscriber->setCode('');
    }

    public function testSubscriberCreationWithMissingLastname_ShouldThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le nom est obligatoire.");

        // Créer un abonné sans nom
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('10-10-1990');
        $subscriber->setCivilite('M');

        // Tester l'exception lors de la tentative de création sans nom
        $subscriber->setLastname('');
    }

    public function testSubscriberCreationWithMissingFirstname_ShouldThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le prénom est obligatoire.");

        // Créer un abonné sans prénom
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setBirthdate('10-10-1990');
        $subscriber->setCivilite('M');

        // Tester l'exception lors de la tentative de création sans prénom
        $subscriber->setFirstname('');
    }

    public function testSubscriberCreationWithInvalidCivilite_ShouldThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La civilité doit être 'M', 'Mme', ou 'Mlle'.");

        // Créer un abonné avec une civilité invalide
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('10-10-1990');

        // Tester l'exception lors de la tentative de création avec une civilité invalide
        $subscriber->setCivilite('Mr');
    }
}



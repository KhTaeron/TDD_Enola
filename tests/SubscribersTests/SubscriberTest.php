<?php

namespace App\Tests\SubscribersTests;

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
        $subscriber->setBirthdate(new \DateTime('1990-01-01'));
        $subscriber->setCivilite('M');

        // Vérification des valeurs
        $this->assertEquals('9785', $subscriber->getCode());
        $this->assertEquals('Dupont', $subscriber->getLastname());
        $this->assertEquals('Jean', $subscriber->getFirstname());
        $this->assertEquals('1990-01-01', $subscriber->getBirthdate()->format('Y-m-d'));  
        $this->assertEquals('M', $subscriber->getCivilite());
    }
}

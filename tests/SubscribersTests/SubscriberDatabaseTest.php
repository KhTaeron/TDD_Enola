<?php

namespace App\Tests\SubscribersTests;

use App\Entity\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubscriberDatabaseTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Récupérer la connexion à la base de données
        $conn = static::getContainer()->get('doctrine')->getConnection();

        // Commencer la transaction
        $conn->beginTransaction();
    }

    public function testSubscriberCreationInDBWithMissingCode_ShouldThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le code de l'adhérent est obligatoire.");

        $subscriber = new Subscriber();
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        // Valider les données avant la persistance
        $subscriber->validate();

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();
    }

    public function testSubscriberCreationInDBWithMissingFirstname_ShouldThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le prénom est obligatoire.");

        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        // Valider les données avant la persistance
        $subscriber->validate();

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();
    }

    public function testSubscriberCreationInDBWithMissingLastname_ShouldThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le nom est obligatoire.");

        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        // Valider les données avant la persistance
        $subscriber->validate();

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();
    }

    public function testBookCreationInDB_AllFields(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

        // Vérifier que l'utilisateur est bien enregistré en BDD
        $savedSubscriber = $this->entityManager->getRepository(Subscriber::class)->findOneBy(['code' => '9785']);

        $this->assertNotNull($savedSubscriber);
        $this->assertEquals('9785', $savedSubscriber->getCode());
        $this->assertEquals('Jean', $savedSubscriber->getFirstname());
        $this->assertEquals('Dupont', $savedSubscriber->getLastname());
        $this->assertEquals('01-01-1990', $savedSubscriber->getBirthdate());
        $this->assertEquals('M', $savedSubscriber->getCivilite());
    }

    public function testBookCreationInDB_WithDuplicateCode_ShouldThrowException(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        // Persister le premier livre
        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

        // Créer un deuxième utilisateur avec le même code
        $subscriber2 = new Subscriber();
        $subscriber2->setCode('9785');
        $subscriber2->setLastname('Dupont');
        $subscriber2->setFirstname('Jean');
        $subscriber2->setBirthdate('01-01-1990');
        $subscriber2->setCivilite('M');

        // Exception pour le duplicata
        $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
        $this->expectExceptionMessage('Duplicate entry');

        // Essayer d'ajouter le deuxième livre avec le même ISBN
        $this->entityManager->persist($subscriber2);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        $conn = static::getContainer()->get('doctrine')->getConnection();
        $conn->rollBack(); // Restaure l'état de la base de données après chaque test

        parent::tearDown();
    }
}

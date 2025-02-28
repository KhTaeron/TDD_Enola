<?php
namespace App\Tests\ReservationsTests\Functional;

use App\Entity\Reservation;
use App\Entity\Subscriber;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationControllerTest extends WebTestCase
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

    public function testMaxThreeSimultaneousReservations(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $reservationDate = new DateTime();
        new Reservation($subscriber, $reservationDate);
        new Reservation($subscriber, $reservationDate);
        new Reservation($subscriber, $reservationDate);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("L'adhérent ne peut pas avoir plus de 3 réservations ouvertes.");

        new Reservation($subscriber, $reservationDate); // 4e réservation
    }

    public function testFinishReservation(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $reservationDate = new DateTime();
        $reservation = new Reservation($subscriber, $reservationDate);

        $reservation->finishReservation();
        $this->assertTrue($reservation->isFinished());
    }

    public function testGetOpenReservations(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $reservationDate = new DateTime();
        $reservation = new Reservation($subscriber, $reservationDate);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        $openReservations = $this->entityManager->getRepository(Reservation::class)->findBy([
            'subscriber' => $subscriber,
            'isFinished' => false
        ]);

        $this->assertNotEmpty($openReservations);
    }

}
<?php
namespace App\Tests\ReservationsTests\Functional;

use App\Entity\Reservation;
use App\Entity\Subscriber;
use App\Repository\ReservationRepository;
use App\Service\ReservationService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationServiceTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private ReservationService $reservationService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $reservationRepository = static::getContainer()->get(ReservationRepository::class);

        $this->reservationService = new ReservationService($this->entityManager, $reservationRepository);

        // Récupérer la connexion à la base de données
        $conn = static::getContainer()->get('doctrine')->getConnection();

        // Commencer la transaction
        $conn->beginTransaction();
    }

    public function testMaxThreeSimultaneousReservations(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode((string) random_int(1000, 99999));
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

        // Création de 3 réservations
        for ($i = 0; $i < 3; $i++) {
            $this->reservationService->createReservation($subscriber);
        }

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Un adhérent ne peut pas avoir plus de 3 réservations simultanées.");

        // Création de la 4e réservation
        $this->reservationService->createReservation($subscriber);
    }

    public function testFinishReservation(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode((string) random_int(1000, 99999));
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

        $reservationDate = new DateTime();
        $reservation = new Reservation($subscriber, $reservationDate);

        $reservation->finishReservation();
        $this->assertTrue($reservation->isFinished());
    }

    public function testGetOpenReservations(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode((string) random_int(1000, 99999));
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

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

    public function testGetReservationHistoryForSubscriber(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode((string) random_int(1000, 99999));
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

        // Ajouter des réservations passées et présentes
        for ($i = 0; $i < 5; $i++) {
            $reservation = new Reservation($subscriber, new DateTime("-{$i} months"), true);

            $this->entityManager->persist($reservation);
        }

        $this->entityManager->flush(); // Sauvegarde en base

        // Récupérer l’historique
        $reservations = $this->reservationService->getReservationHistory($subscriber);

        $this->assertCount(5, $reservations, "L'historique doit contenir 5 réservations.");
    }

}
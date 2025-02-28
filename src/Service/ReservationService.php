<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Subscriber;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use DateTime;

class ReservationService
{
    private EntityManagerInterface $entityManager;
    private ReservationRepository $reservationRepository;

    public function __construct(EntityManagerInterface $entityManager, ReservationRepository $reservationRepository)
    {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
    }

    public function createReservation(Subscriber $subscriber): Reservation
    {
        // Vérifier si l'adhérent a déjà 3 réservations ouvertes
        $openReservationsCount = $this->entityManager->getRepository(Reservation::class)->count([
            'subscriber' => $subscriber,
            'isFinished' => false
        ]);

        if ($openReservationsCount >= 3) {
            throw new InvalidArgumentException("Un adhérent ne peut pas avoir plus de 3 réservations simultanées.");
        }

        // Créer la réservation
        $reservation = new Reservation($subscriber, new DateTime());

        // Persister l'entité
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }

    public function finishReservation(Reservation $reservation): void
    {
        // Terminer la réservation
        $reservation->finishReservation();
        $this->entityManager->flush();
    }

    public function getOpenReservations(Subscriber $subscriber)
    {
        return $this->entityManager->getRepository(Reservation::class)->findBy([
            'subscriber' => $subscriber,
            'isFinished' => false
        ]);
    }

}

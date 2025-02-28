<?php

namespace App\Tests\SubscribersTests;

use App\Entity\Reservation;
use DateTime;
use PHPUnit\Framework\TestCase;
use App\Entity\Subscriber;

class ReservationTest extends TestCase
{
    public function testReservationCreation(): void
    {
        $subscriber = new Subscriber();
        $subscriber->setCode('9785');
        $subscriber->setLastname('Dupont');
        $subscriber->setFirstname('Jean');
        $subscriber->setBirthdate('01-01-1990');
        $subscriber->setCivilite('M');

        $reservationDate = new DateTime();
        $reservation = new Reservation($subscriber, $reservationDate);

        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertEquals(4, $reservation->getExpirationDate()->diff($reservationDate)->m);
    }
}
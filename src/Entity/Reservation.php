<?php

namespace App\Entity;

use DateTime;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'reservations')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Subscriber::class)]
    #[ORM\JoinColumn(name: "subscriber_id", referencedColumnName: "id")]
    private Subscriber $subscriber;

    #[ORM\Column(type: "datetime")]
    private DateTime $reservationDate;

    #[ORM\Column(type: "datetime")]
    private DateTime $expirationDate;

    #[ORM\Column(type: "boolean")]
    private bool $isFinished = false;

    public function __construct(Subscriber $subscriber, DateTime $reservationDate)
    {
        $this->subscriber = $subscriber;
        $this->reservationDate = $reservationDate;
        $this->setExpirationDate($reservationDate);
    }

    public function setExpirationDate(DateTime $reservationDate): void
    {
        $expirationDate = clone $reservationDate;
        $expirationDate->modify('+4 months');
        if ($expirationDate > new DateTime()) {
            $this->expirationDate = $expirationDate;
        } else {
            throw new InvalidArgumentException("La date limite de réservation ne peut pas être dans le passé.");
        }
    }

    public function getExpirationDate(): DateTime
    {
        return $this->expirationDate;
    }

    public function finishReservation(): void
    {
        $this->isFinished = true;
    }

    public function getSubscriber(): Subscriber
    {
        return $this->subscriber;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }
}

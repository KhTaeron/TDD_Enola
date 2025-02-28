<?php

namespace App\Entity;

use DateTime;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subscribers')]
class Subscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer", unique: true)]
    private ?int $id = null;

    #[Groups(['subscriber:read'])]
    #[ORM\Column(length: 20, unique: true)]
    private string $code;

    #[Groups(['subscriber:read'])]
    #[ORM\Column(length: 255)]
    private string $lastname;

    #[Groups(['subscriber:read'])]
    #[ORM\Column(length: 255)]
    private string $firstname;

    #[Groups(['subscriber:read'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $birthdate = null;

    #[Groups(['subscriber:read'])]
    #[ORM\Column(length: 10)]
    private string $civilite;

    public function validate(): void
    {
        if (empty($this->code)) {
            throw new InvalidArgumentException("Le code de l'adhérent est obligatoire.");
        }
        if (empty($this->firstname)) {
            throw new InvalidArgumentException("Le prénom est obligatoire.");
        }
        if (empty($this->lastname)) {
            throw new InvalidArgumentException("Le nom est obligatoire.");
        }
        if (empty($this->civilite)) {
            throw new InvalidArgumentException("La civilité est obligatoire.");
        }
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        if (empty($code)) {
            throw new InvalidArgumentException("Le code de l'adhérent est obligatoire.");
        }
        $this->code = $code;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        if (empty($lastname)) {
            throw new InvalidArgumentException("Le nom est obligatoire.");
        }
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        if (empty($firstname)) {
            throw new InvalidArgumentException("Le prénom est obligatoire.");
        }
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthdate(): ?DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(string $birthdate): void
    {
        // Créer un objet DateTime à partir du format 'd-m-Y'
        $date = DateTime::createFromFormat('d-m-Y', $birthdate);

        // Vérifier si la date est valide
        if (!$date || $date->format('d-m-Y') !== $birthdate) {
            throw new InvalidArgumentException("La date doit être au format 'dd-mm-yyyy'.");
        }

        $this->birthdate = $date;
    }

    public function getCivilite(): string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): self
    {
        $validCivilites = ['M', 'Mme', 'Mlle'];

        if (!in_array($civilite, $validCivilites)) {
            throw new InvalidArgumentException("La civilité doit être 'M', 'Mme', ou 'Mlle'.");
        }
        $this->civilite = $civilite;

        return $this;
    }
}

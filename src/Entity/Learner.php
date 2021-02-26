<?php

namespace App\Entity;

use App\Repository\LearnerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LearnerRepository::class)
 * @UniqueEntity("login")
 */
class Learner
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max=100
     * )
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max=100
     * )
     */
    private $reference_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Language
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Timezone
     */
    private $timezone;

    /**
     * @ORM\Column(
     *     type="integer",
     *     nullable=true,
     *     options={
     *         "default":1
     *     }
     * )
     * @Assert\Positive
     */
    private $entity_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive
     */
    private $manager_id;

    /**
     * @ORM\Column(
     *     type="boolean",
     *     nullable=true,
     *     options={
     *         "default":true
     *     }
     * )
     * @Assert\Choice({true, false})
     */
    private $enabled;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date
     */
    private $enabled_from;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date
     * @Assert\GreaterThanOrEqual(propertyPath="enabled_from")
     */
    private $enabled_until;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $custom_fields = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->reference_number;
    }

    public function setReferenceNumber(?string $reference_number): self
    {
        $this->reference_number = $reference_number;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entity_id;
    }

    public function setEntityId(?int $entity_id): self
    {
        $this->entity_id = $entity_id;

        return $this;
    }

    public function getManagerId(): ?int
    {
        return $this->manager_id;
    }

    public function setManagerId(?int $manager_id): self
    {
        $this->manager_id = $manager_id;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEnabledFrom(): ?\DateTimeInterface
    {
        return $this->enabled_from;
    }

    public function setEnabledFrom(?\DateTimeInterface $enabled_from): self
    {
        $this->enabled_from = $enabled_from;

        return $this;
    }

    public function getEnabledUntil(): ?\DateTimeInterface
    {
        return $this->enabled_until;
    }

    public function setEnabledUntil(?\DateTimeInterface $enabled_until): self
    {
        $this->enabled_until = $enabled_until;

        return $this;
    }

    public function getCustomFields(): ?array
    {
        return $this->custom_fields;
    }

    public function setCustomFields(?array $custom_fields): self
    {
        $this->custom_fields = $custom_fields;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Configuration;
use App\Lib\Entity\AbstractEntity;
use App\Repository\LearnerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LearnerRepository::class)
 * @UniqueEntity("login")
 */
class Learner extends AbstractEntity
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
     * @ORM\Column(type="string", name="reference_number", length=255, nullable=true)
     * @Assert\Length(
     *     max=100
     * )
     */
    private $referenceNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", name="first_name", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $firstName;

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
     *     name="entity_id",
     *     options={
     *         "default":1
     *     }
     * )
     * @Assert\Positive
     */
    private $entityID;

    /**
     * @ORM\Column(type="integer", name="manager_id", nullable=true)
     * @Assert\Positive
     */
    private $managerID;

    /**
     * @ORM\Column(
     *     type="boolean",
     *     nullable=true,
     *     options={
     *         "default":true
     *     }
     * )
     * @Assert\Type("bool")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", name="enabled_from", nullable=true)
     * @Assert\Date
     */
    private $enabledFrom;

    /**
     * @ORM\Column(type="string", name="enabled_until", nullable=true)
     * @Assert\Date
     * @Assert\GreaterThanOrEqual(propertyPath="enabled_from")
     */
    private $enabledUntil;

    /**
     * @ORM\Column(type="json", name="custom_fields", nullable=true)
     */
    private $customFields = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(?string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;

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
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
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

        ($language) ? $this->language = $language : $this->language = Configuration::APP_LANGUAGE;

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

    public function getEntityID(): ?int
    {
        return $this->entityID;
    }

    public function setEntityID(?int $entityID = null): self
    {
        (is_null($entityID)) ? $this->entityID = 1 : $this->entityID = $entityID;

        return $this;
    }

    public function getManagerID(): ?int
    {
        return $this->managerID;
    }

    public function setManagerID(?int $managerID): self
    {
        $this->managerID = $managerID;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        (is_null($enabled)) ? $this->enabled = true : $this->enabled = $enabled;

        return $this;
    }

    public function getEnabledFrom(): ?string
    {
        return $this->enabledFrom;
    }

    public function setEnabledFrom(?string $enabledFrom): self
    {
        $this->enabledFrom = $enabledFrom;

        return $this;
    }

    public function getEnabledUntil(): ?string
    {
        return $this->enabledUntil;
    }

    public function setEnabledUntil(?string $enabledUntil): self
    {
        $this->enabledUntil = $enabledUntil;

        return $this;
    }

    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    public function setCustomFields(?array $customFields): self
    {
        $this->customFields = $customFields;

        return $this;
    }
}

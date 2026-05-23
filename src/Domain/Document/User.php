<?php

namespace App\Domain\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'Users')]
class User
{
    #[ODM\Id]
    private string $id;
    #[ODM\Field(type: 'string')]
    private string $firstName;
    #[ODM\Field(type: 'string')]
    private string $lastName;

    /**
     * @var list<string>
     */
    #[ODM\Field(type: 'collection')]
    #[ODM\UniqueIndex]
    private array $phoneNumbers = [];
    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $ip = null;
    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $country = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    /**
     * @param list<string> $phoneNumbers
     *
     * @return $this
     */
    public function setPhoneNumbers(array $phoneNumbers): static
    {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }
}

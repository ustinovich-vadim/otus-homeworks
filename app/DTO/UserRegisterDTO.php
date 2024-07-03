<?php

namespace App\DTO;

class UserRegisterDTO
{
    private string $name;
    private ?string $surname;
    private ?string $birth_date;
    private ?string $gender;
    private ?string $hobbies;
    private ?string $city;
    private string $email;
    private string $password;

    public function __construct(
        string $name,
        ?string $surname,
        ?string $birth_date,
        ?string $gender,
        ?string $hobbies,
        ?string $city,
        string $email,
        string $password
    ) {
        $this->name = $name;
        $this->surname = $surname;
        $this->birth_date = $birth_date;
        $this->gender = $gender;
        $this->hobbies = $hobbies;
        $this->city = $city;
        $this->email = $email;
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getBirthDate(): ?string
    {
        return $this->birth_date;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getHobbies(): ?string
    {
        return $this->hobbies;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}

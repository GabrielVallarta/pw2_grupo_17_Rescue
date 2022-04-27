<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class User
{

  private int $id;
  private string $email;
  private string $password;
  private string $username;
  private string $phone_number;
  private string $profile_picture;
  private string $membership;
  private Datetime $createdAt;
  private Datetime $updatedAt;

  public function __construct(
    string $email,
    string $password,
    string $phone_number,
    string $profile_picture,
    string $membership,
    Datetime $createdAt,
    Datetime $updatedAt
  ) {
    $this->email = $email;
    $this->password = $password;
    $this->phone_number = $phone_number;
    $this->profile_picture = $profile_picture;
    $this->membership = $membership;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  public function id()
  {
    return $this->id;
  }

    public function username()
    {
        return $this->username;
    }

  public function email()
  {
    return $this->email;
  }

  public function password()
  {
    return $this->password;
  }

    public function phone_number()
    {
        return $this->phone_number;
    }

    public function profile_picture()
    {
        return $this->profile_picture;
    }

    public function membership()
    {
        return $this->membership;
    }

  public function createdAt()
  {
    return $this->createdAt;
  }

  public function updatedAt()
  {
    return $this->updatedAt;
  }
}

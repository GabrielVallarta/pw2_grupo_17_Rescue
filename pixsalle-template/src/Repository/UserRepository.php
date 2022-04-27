<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\User;

interface UserRepository
{
    public function createUser(User $user): void;
    public function getUserByEmail(string $email);
    public function getUsername(string $id);
    public function getMail(string $id);
    public function getPhone(string $id);
    public function getPlan(string $id);
    public function getPassword(string $id);
    public function updateUser(string $id, string $username, string $phone, string $picture);
    public function updatePassword(string $id, string $password);
    public function updateMembershipPlan(string $id, string $plan);
    public function getPictures();
}

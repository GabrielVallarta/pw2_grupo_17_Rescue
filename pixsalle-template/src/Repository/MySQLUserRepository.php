<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Repository\UserRepository;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createUser(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO users(email, password, username, phone_number, profile_picture, membership, createdAt, updatedAt)
        VALUES(:email, :password, :username, :phone_number, :profile_picture, :membership, :createdAt, :updatedAt)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $id = strval($this->getUsers() + 1);
        $username = 'user' . $id;
        $phone = $user->phone_number();
        $picture = $user->profile_picture();
        $membership = $user->membership();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('phone_number', $phone, PDO::PARAM_STR);
        $statement->bindParam('profile_picture', $picture, PDO::PARAM_STR);
        $statement->bindParam('membership', $membership, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getUserByEmail(string $email)
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getUsers(){
        $query = <<<'QUERY'
        SELECT COUNT(*) FROM users
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        return $statement->fetchColumn();

    }

    public function getUsername(string $id){
        $query = <<<'QUERY'
        SELECT username FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getMail(string $id){
        $query = <<<'QUERY'
        SELECT email FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getPhone(string $id){
        $query = <<<'QUERY'
        SELECT phone_number FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getPlan(string $id){
        $query = <<<'QUERY'
        SELECT membership FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getPassword(string $id){
        $query = <<<'QUERY'
        SELECT password FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function updateUser(string $id, string $username, string $phone, string $picture){
        $query = <<<'QUERY'
        UPDATE users
        SET username = :username, phone_number = :phone_number, profile_picture = :profile_picture
        WHERE id = :id     
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('phone_number', $phone, PDO::PARAM_STR);
        $statement->bindParam('profile_picture', $picture, PDO::PARAM_STR);
        $statement->execute();
    }

    public function updatePassword(string $id, string $password){
        $query = <<<'QUERY'
        UPDATE users
        SET password = :password
        WHERE id = :id     
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->execute();
    }

    public function updateMembershipPlan(string $id, string $plan){
        $query = <<<'QUERY'
        UPDATE users
        SET membership = :membership
        WHERE id = :id     
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('membership', $plan, PDO::PARAM_STR);
        $statement->execute();
    }

    public function getPictures()
    {
        $query = <<<'QUERY'
        SELECT p.id, p.path, u.username
        FROM pictures as p, users as u
        WHERE p.user_id = u.id;
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }
}

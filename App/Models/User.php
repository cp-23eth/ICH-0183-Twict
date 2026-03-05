<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class User extends AppModel
{
    protected const QUERY_SELECT = <<< SQL
        SELECT *
        FROM (
            SELECT `id`, `firstname`, `lastname`, `mailAddress`, `password`, `createdAt`, `updatedAt`
            FROM `users`
        ) AS `users`
        SQL;

    public static function getAll(): array
    {
        $db = static::getDB();

        $stmt = $db->prepare(self::QUERY_SELECT);
        $stmt->execute();

        $models = $stmt->fetchAll();

        return $models;
    }

    public static function find(int $id): ?array
    {
        $db = static::getDB();

        $stmt = $db->prepare(self::QUERY_SELECT . <<< SQL
            WHERE `id` = :id
            LIMIT 1;
        SQL);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $model = $stmt->fetch() ?: null;

        return $model;
    }

    public static function add(array $model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<<SQL
            INSERT INTO `users` 
                (`firstname`, `lastname`, `mailAddress`, `password`) 
            VALUES
                (:firstname, :lastname, :mailAddress, :password);
            SQL);

        $stmt->bindParam(':firstname', $model['firstname'], PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $model['lastname'], PDO::PARAM_STR);
        $stmt->bindParam(':mailAddress', $model['mailAddress'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $model['password'], PDO::PARAM_STR);
        $success = $stmt->execute();

        return $success;
    }

    public static function update(array $model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<< SQL
            UPDATE `users` SET
                `firstname` = :firstname
                , `lastname` = :lastname
                , `mailAddress` = :mailAddress
                , `password` = :password
                , `updatedAt` = CURRENT_TIMESTAMP
            WHERE `id` = :id
            LIMIT 1;
            SQL);

        $stmt->bindParam(':id', $model['id'], PDO::PARAM_INT);
        $stmt->bindParam(':firstname', $model['firstname'], PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $model['lastname'], PDO::PARAM_STR);
        $stmt->bindParam(':mailAddress', $model['mailAddress'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $model['password'], PDO::PARAM_STR);
        $success = $stmt->execute();

        return $success;
    }

    public static function remove(array $model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<< SQL
            DELETE FROM `users`
            WHERE `id` = :id
            LIMIT 1;
            SQL);

        $stmt->bindParam(':id', $model['id'], PDO::PARAM_INT);
        $success = $stmt->execute();

        return $success;
    }

    public static function findByMailAddress(string $mailAddress): ?array
    {
        $db = static::getDB();

        $stmt = $db->prepare(self::QUERY_SELECT . <<< SQL
            WHERE `mailAddress`= :mailAddress
            LIMIT 1;
            SQL);

        $stmt->bindParam(':mailAddress', $mailAddress, PDO::PARAM_STR);
        $stmt->execute();

        $model = $stmt->fetch() ?: null;

        return $model;
    }

    public static function findByMailAddressAndPassword(string $mailAddress, string $password): ?array
    {
        $db = static::getDB();

        $stmt = $db->prepare(self::QUERY_SELECT . <<< SQL
                WHERE `mailAddress` = :mailAddress
                AND `password`= :password
                LIMIT 1;
                SQL);

        $stmt->bindParam(':mailAddress', $mailAddress, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        $model = $stmt->fetch() ?: null;

        return $model;
    }
}

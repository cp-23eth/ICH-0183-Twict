<?php

declare(strict_types=1);

namespace App\Models;

use \Core\Model;
use \PDO;

class User extends Model
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

        // $models = $db
        //     ->query(self::QUERY_SELECT)
        //     ->fetchAll();

        $stmt = $db->prepare(self::QUERY_SELECT);
        $stmt->execute();
        $models = $stmt->fetchAll();

        return $models;
    }

    public static function find(int $id): ?array
    {
        $db = static::getDB();

        // $model = $db
        //     ->query(self::QUERY_SELECT . <<< SQL
        //         WHERE `id` = {$id}
        //         LIMIT 1;
        //     SQL)
        //     ->fetch() ?: null;

        $stmt = $db->prepare(<<< SQL
            SELECT *
            FROM (
                SELECT `id`, `firstname`, `lastname`, `mailAddress`, `password`, `createdAt`, `updatedAt`
                FROM `users`
            ) AS `users`
            WHERE `id` = :id
            LIMIT 1;
            SQL);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $model = $stmt->fetch() ?: null;

        return $model;
    }

    public static function add(array $model): bool
    {
        $db = static::getDB();

        // $success = $db
        //     ->prepare(<<< SQL
        //         INSERT INTO `users`
        //             (`firstname`, `lastname`, `mailAddress`, `password`)
        //         VALUES
        //             ('{$model['firstname']}', '{$model['lastname']}', '{$model['mailAddress']}', '{$model['password']}');
        //         SQL)
        //     ->execute();

        $stmt = $db->prepare(<<< SQL
            INSERT INTO `users`
                (`firstname`, `lastname`, `mailAddress`, `password`)
            VALUES
                (:firstname, :lastname, :mailAddress, :password);
            SQL);

        $stmt->bindParam(':firstname', $model['firstname'], PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $model['lastname'], PDO::PARAM_STR);
        $stmt->bindParam(':mailAddress', $model['mailAddress'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $model['password'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function update(array $model): bool
    {
        $db = static::getDB();

        // $success = $db
        //     ->prepare(<<< SQL
        //         UPDATE `users` SET
        //             `firstname` = '{$model['firstname']}',
        //             `lastname` = '{$model['lastname']}',
        //             `mailAddress` = '{$model['mailAddress']}',
        //             `password` = '{$model['password']}',
        //             `updatedAt` = CURRENT_TIMESTAMP
        //         WHERE `id` = {$model['id']}
        //         LIMIT 1;
        //         SQL)
        //     ->execute();

        $stmt = $db->prepare(<<< SQL
            UPDATE `users` SET
                `firstname` = :firstname,
                `lastname` = :lastname,
                `mailAddress` = :mailAddress,
                `password` = :password,
                `updatedAt` = CURRENT_TIMESTAMP
            WHERE `id` = :id
            LIMIT 1;
            SQL);

        $stmt->bindParam(':firstname', $model['firstname'], PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $model['lastname'], PDO::PARAM_STR);
        $stmt->bindParam(':mailAddress', $model['mailAddress'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $model['password'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $model['id'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function remove(array $model): bool
    {
        $db = static::getDB();

        // $success = $db
        //     ->prepare(<<< SQL
        //         DELETE FROM `users`
        //         WHERE `id` = {$model['id']}
        //         LIMIT 1;
        //         SQL)
        //     ->execute();

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

        // $model = $db
        //     ->query(self::QUERY_SELECT . <<< SQL
        //         WHERE `mailAddress`= '{$mailAddress}'
        //         LIMIT 1;
        //         SQL)
        //     ->fetch() ?: null;

        $stmt = $db->prepare(<<< SQL
            SELECT *
            FROM `users`
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

        // $model = $db
        //     ->query(self::QUERY_SELECT . <<< SQL
        //         WHERE `mailAddress`= '{$mailAddress}'
        //         AND `password`= '{$password}'
        //         LIMIT 1;
        //         SQL)
        //     ->fetch() ?: null;

        $stmt = $db->prepare(<<< SQL
            SELECT *
            FROM `users`
            WHERE `mailAddress`= :mailAddress
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

<?php

declare(strict_types=1);

namespace App\Models;

use \Core\Model;

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

        $models = $db
            ->query(self::QUERY_SELECT)
            ->fetchAll();

        return $models;
    }

    public static function find(int $id): ?array
    {
        $db = static::getDB();

        $model = $db
            ->query(self::QUERY_SELECT . <<< SQL
                WHERE `id` = {$id}
                LIMIT 1;
            SQL)
            ->fetch() ?: null;

        return $model;
    }

    public static function add(array $model): bool
    {
        $db = static::getDB();

        $success = $db
            ->prepare(<<< SQL
                INSERT INTO `users`
                    (`firstname`, `lastname`, `mailAddress`, `password`)
                VALUES
                    ('{$model['firstname']}', '{$model['lastname']}', '{$model['mailAddress']}', '{$model['password']}');
                SQL)
            ->execute();

        return $success;
    }

    public static function update(array $model): bool
    {
        $db = static::getDB();

        $success = $db
            ->prepare(<<< SQL
                UPDATE `users` SET
                    `firstname` = '{$model['firstname']}',
                    `lastname` = '{$model['lastname']}',
                    `mailAddress` = '{$model['mailAddress']}',
                    `password` = '{$model['password']}',
                    `updatedAt` = CURRENT_TIMESTAMP
                WHERE `id` = {$model['id']}
                LIMIT 1;
                SQL)
            ->execute();

        return $success;
    }

    public static function remove(array $model): bool
    {
        $db = static::getDB();

        $success = $db
            ->prepare(<<< SQL
                DELETE FROM `users`
                WHERE `id` = {$model['id']}
                LIMIT 1;
                SQL)
            ->execute();

        return $success;
    }

    public static function findByMailAddress(string $mailAddress): ?array
    {
        $db = static::getDB();

        $model = $db
            ->query(self::QUERY_SELECT . <<< SQL
                WHERE `mailAddress`= '{$mailAddress}'
                LIMIT 1;
                SQL)
            ->fetch() ?: null;

        return $model;
    }

    public static function findByMailAddressAndPassword(string $mailAddress, string $password): ?array
    {
        $db = static::getDB();

        $model = $db
            ->query(self::QUERY_SELECT . <<< SQL
                WHERE `mailAddress`= '{$mailAddress}'
                AND `password`= '{$password}'
                LIMIT 1;
                SQL)
            ->fetch() ?: null;

        return $model;
    }
}

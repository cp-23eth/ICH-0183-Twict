<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class BankAccount extends AppModel
{
    protected const QUERY_SELECT = <<< SQL
        SELECT *
        FROM (
            SELECT
                `b`.`id`
                , `b`.`description`
                , `b`.`idOwner`
                , `o`.`id` AS `owner.id`
                , `o`.`firstname` AS `owner.firstname`
                , `o`.`lastname` AS `owner.lastname`
                , `o`.`mailAddress` AS `owner.mailAddress`
                , `b`.`createdAt`
                , `b`.`updatedAt`
                , IFNULL(SUM(`ft`.`amount`), 0) AS `balance`
            FROM `bankaccounts` AS `b`
            LEFT JOIN `users` AS `o` ON `b`.`idOwner` = `o`.`id`
            LEFT JOIN (
                SELECT 
                `fts`.`idSender` AS `idBankAccount`
                , `fts`.`amount` * -1 AS `amount`
                FROM `financialtransactions` AS `fts`
                UNION ALL
                SELECT 
                `ftr`.`idRecipient` AS `idBankAccount`
                , `ftr`.`amount` AS `amount`
                FROM `financialtransactions` AS `ftr`
            ) AS `ft` ON `ft`.`idBankAccount` = `b`.`id`
            GROUP BY `b`.`id`, `b`.`description`, `b`.`idOwner`, `owner.firstname`, `owner.lastname`, `owner.mailAddress`, `b`.`createdAt`, `b`.`updatedAt`
        ) AS `bankAccounts`
        SQL;

    public static function getAll(): array
    {
        $db = static::getDB();

        $stmt = $db->prepare(self::QUERY_SELECT);
        $stmt->execute();

        $models = $stmt->fetchAll();

        $models = self::expandRelationships($models);

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

        $model = self::expandRelationships($model);

        return $model;
    }

    public static function add(array $model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<< SQL
            INSERT INTO `bankaccounts` 
                (`description`, `idOwner`) 
            VALUES
                (:description, :idOwner);
            SQL);

        $stmt->bindParam(':description', $model['description'], PDO::PARAM_STR);
        $stmt->bindParam(':idOwner', $model['idOwner'], PDO::PARAM_INT);
        $success = $stmt->execute();

        return $success;
    }

    public static function update(array $model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<< SQL
            UPDATE `bankaccounts` SET
                `description` = :description
                , `idOwner` = :idOwner
                , `updatedAt` = CURRENT_TIMESTAMP
            WHERE `id` = :id
            LIMIT 1;
            SQL);

        $stmt->bindParam(':description', $model['description'], PDO::PARAM_STR);
        $stmt->bindParam(':idOwner', $model['idOwner'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $model['id'], PDO::PARAM_INT);
        $success = $stmt->execute();

        return $success;
    }

    public static function remove(array $model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<< SQL
                DELETE FROM `bankaccounts`
                WHERE `id` = :id
                LIMIT 1;
                SQL);

        $stmt->bindParam(':id', $model['id'], PDO::PARAM_INT);
        $success = $stmt->execute();

        return $success;
    }

    public static function findByIdOwner(int $idOwner): ?array
    {
        $db = static::getDB();

        $stmt = $db->prepare(self::QUERY_SELECT . <<< SQL
            WHERE `idOwner`= :idOwner
            SQL);

        $stmt->bindParam(':idOwner', $idOwner, PDO::PARAM_INT);
        $stmt->execute();

        $models = $stmt->fetchAll();

        $models = self::expandRelationships($models);

        return $models;
    }
}

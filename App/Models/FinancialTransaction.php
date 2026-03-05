<?php

namespace App\Models;

class FinancialTransaction extends AppModel
{
    protected const QUERY_SELECT = <<< SQL
        SELECT *
        FROM (
            SELECT
                `f`.`id`
                , `f`.`amount`
                , `f`.`idSender`
                , `s`.`id` AS `sender.id`
                , `s`.`description` AS `sender.description`
                , `so`.`id` AS `sender.owner.id`
                , `so`.`firstname` AS `sender.owner.firstname`
                , `so`.`lastname` AS `sender.owner.lastname`
                , `so`.`mailAddress` AS `sender.owner.mailAddress`
                , `f`.`idRecipient`
                , `r`.`id` AS `recipient.id`
                , `r`.`description` AS `recipient.description`
                , `ro`.`id` AS `recipient.owner.id`
                , `ro`.`firstname` AS `recipient.owner.firstname`
                , `ro`.`lastname` AS `recipient.owner.lastname`
                , `ro`.`mailAddress` AS `recipient.owner.mailAddress`
                , `f`.`createdAt`
                , `f`.`updatedAt`
            FROM `financialtransactions` AS `f`
            LEFT JOIN `bankaccounts` AS `s` ON `f`.`idSender` = `s`.`id`
            LEFT JOIN `users` AS `so` ON `s`.`idOwner` = `so`.`id`
            LEFT JOIN `bankaccounts` AS `r` ON `f`.`idRecipient` = `r`.`id`
            LEFT JOIN `users` AS `ro` ON `r`.`idOwner` = `ro`.`id`
        ) AS `financialtransactions`
        SQL;

    public static function getAll(): array
    {
        $db = static::getDB();

        $models = $db
            ->query(self::QUERY_SELECT)
            ->fetchAll();

        $models = self::expandRelationships($models);

        return $models;
    }

    public static function find(int $id): ?array
    {
        $db = static::getDB();

        $model = $db
            ->query(self::QUERY_SELECT . <<< SQL
                WHERE `id` = {$id}
                LIMIT 1
            SQL)
            ->fetch() ?: null;

        $model = self::expandRelationships($model);

        return $model;
    }

    public static function add($model): bool
    {
        $db = static::getDB();

        $success = $db
            ->prepare(<<< SQL
                INSERT INTO `financialtransactions` 
                    (`amount`, `idSender`, `idRecipient`) 
                VALUES
                    ({$model['amount']}, {$model['idSender']}, {$model['idRecipient']})
            SQL)
            ->execute();

        return $success;
    }

    public static function update($model): bool
    {
        $db = static::getDB();

        $success = $db
            ->prepare(<<< SQL
                UPDATE `financialtransactions` SET
                    `amount` = {$model['amount']}
                    , `idSender` = {$model['idSender']}
                    , `idRecipient` = {$model['idRecipient']}
                    , `updatedAt` = CURRENT_TIMESTAMP
                WHERE `id` = {$model['id']}
                LIMIT 1;
            SQL)
            ->execute();

        return $success;
    }

    public static function remove($model): bool
    {
        $db = static::getDB();

        $success = $db
            ->prepare(<<< SQL
                DELETE FROM `financialtransactions`
                WHERE `id` = {$model['id']}
                LIMIT 1;
            SQL)
            ->execute();

        return $success;
    }

    public static function findByIdSender(int $idSender): ?array
    {
        $db = static::getDB();

        $models = $db
            ->query(self::QUERY_SELECT . <<< SQL
                WHERE `idSender`= {$idSender}
            SQL)
            ->fetchAll();

        $models = self::expandRelationships($models);

        return $models;
    }

    public static function findByIdRecipient(int $idRecipient): ?array
    {
        $db = static::getDB();

        $models = $db
            ->query(self::QUERY_SELECT . <<< SQL
                WHERE `idRecipient`= {$idRecipient}
            SQL)
            ->fetchAll();

        $models = self::expandRelationships($models);

        return $models;
    }
}

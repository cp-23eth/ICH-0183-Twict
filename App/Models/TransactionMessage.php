<?php

namespace App\Models;

use PDO;

class TransactionMessage extends AppModel
{
    protected const QUERY_SELECT = <<< SQL
        SELECT *
        FROM (
            SELECT
            `t`.`id`,
            `t`.`content`,
            `t`.`idTransaction`,
            `f`.`id` AS `transaction.id`,
            `f`.`amount` AS `transaction.amount`,
            `f`.`idSender` AS `transaction.idSender`,
            `s`.`id` AS `transaction.sender.id`,
            `s`.`description` AS `transaction.sender.description`,
            `s`.`idOwner` AS `transaction.sender.idOwner`,
            `so`.`id` AS `transaction.sender.owner.id`,
            `so`.`firstname` AS `transaction.sender.owner.firstname`,
            `so`.`lastname` AS `transaction.sender.owner.lastname`,
            `so`.`mailAddress` AS `transaction.sender.owner.mailAddress`,
            `f`.`idRecipient` AS `transaction.idRecipient`,
            `r`.`id` AS `transaction.recipient.id`,
            `r`.`description` AS `transaction.recipient.description`,
            `r`.`idOwner` AS `transaction.recipient.idOwner`,
            `ro`.`id` AS `transaction.recipient.owner.id`,
            `ro`.`firstname` AS `transaction.recipient.owner.firstname`,
            `ro`.`lastname` AS `transaction.recipient.owner.lastname`,
            `ro`.`mailAddress` AS `transaction.recipient.owner.mailAddress`,
            `t`.`idAuthor`,
            `a`.`id` AS `author.id`,
            `a`.`firstname` AS `author.firstname`,
            `a`.`lastname` AS `author.lastname`,
            `a`.`mailAddress` AS `author.mailAddress`,
            `t`.`createdAt`,
            `t`.`updatedAt`
            FROM `transactionmessages` AS `t`
            LEFT JOIN `financialtransactions` AS `f` ON `t`.`idTransaction` = `f`.`id`
            LEFT JOIN `users` AS `a` ON `t`.`idAuthor` = `a`.`id`
            LEFT JOIN `bankaccounts` AS `s` ON `f`.`idSender` = `s`.`id`
            LEFT JOIN `users` AS `so` ON `s`.`idOwner` = `so`.`id`
            LEFT JOIN `bankaccounts` AS `r` ON `f`.`idRecipient` = `r`.`id`
            LEFT JOIN `users` AS `ro` ON `r`.`idOwner` = `ro`.`id`
        ) AS `transactionmessages`
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

    public static function add($model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<< SQL
            INSERT INTO `transactionmessages` 
                (`content`, `idTransaction`, `idAuthor`) 
            VALUES
                (:content, :idTransaction, :idAuthor);
            SQL);

        $stmt->bindParam(':content', $model['content'], PDO::PARAM_STR);
        $stmt->bindParam(':idTransaction', $model['idTransaction'], PDO::PARAM_INT);
        $stmt->bindParam(':idAuthor', $model['idAuthor'], PDO::PARAM_INT);
        $success = $stmt->execute();

        return $success;
    }

    public static function update($model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<< SQL
            UPDATE `transactionmessages` SET
                `content` = :content
                , `idTransaction` = :idTransaction
                , `idAuthor` = :idAuthor
                , `updatedAt` = CURRENT_TIMESTAMP
            WHERE `id` = :id
            LIMIT 1;
            SQL);

        $stmt->bindParam(':content', $model['content'], PDO::PARAM_STR);
        $stmt->bindParam(':idTransaction', $model['idTransaction'], PDO::PARAM_INT);
        $stmt->bindParam(':idAuthor', $model['idAuthor'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $model['id'], PDO::PARAM_INT);
        $success = $stmt->execute();

        return $success;
    }

    public static function remove($model): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare(<<< SQL
            DELETE FROM `transactionmessages`
            WHERE `id` = :id
            LIMIT 1;
            SQL);

        $stmt->bindParam(':id', $model['id'], PDO::PARAM_INT);
        $success = $stmt->execute();

        return $success;
    }

    public static function findByIdTransaction(int $idTransaction): ?array
    {
        $db = static::getDB();

        $stmt = $db->prepare(self::QUERY_SELECT . <<< SQL
            WHERE `idTransaction` = :idTransaction
            SQL);

        $stmt->bindParam(':idTransaction', $idTransaction, PDO::PARAM_INT);
        $stmt->execute();

        $models = $stmt->fetchAll();

        $models = self::expandRelationships($models);

        return $models;
    }
}

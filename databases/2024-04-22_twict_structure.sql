SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de données : `twict`
--
DROP DATABASE IF EXISTS `twict`;
CREATE DATABASE IF NOT EXISTS `twict` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `twict`;

-- --------------------------------------------------------

--
-- Structure de la table `bankaccounts`
--

DROP TABLE IF EXISTS `bankaccounts`;
CREATE TABLE IF NOT EXISTS `bankaccounts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `idOwner` int UNSIGNED NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `financialtransactions`
--

DROP TABLE IF EXISTS `financialtransactions`;
CREATE TABLE IF NOT EXISTS `financialtransactions` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` decimal(15,2) DEFAULT NULL,
  `idSender` int UNSIGNED NOT NULL,
  `idRecipient` int UNSIGNED NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transactionmessages`
--

DROP TABLE IF EXISTS `transactionmessages`;
CREATE TABLE IF NOT EXISTS `transactionmessages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `idTransaction` int UNSIGNED NOT NULL,
  `idAuthor` int UNSIGNED NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `mailAddress` varchar(255) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `mailAddress`, `password`, `createdAt`, `updatedAt`) VALUES
(1, 'Louis', 'Pasteur', 'pasteur@gmail.com', '123456', '2021-02-07 14:03:06', NULL),
(2, 'Marie', 'Curie', 'curie@gmail.com', '123456', '2021-02-07 14:03:06', NULL),
(3, 'Victor', 'Hugo', 'hugo@gmail.com', '123456', '2021-02-07 14:03:06', NULL),
(4, 'Jules', 'Verne', 'verne@gmail.com', '123456', '2021-02-07 14:03:06', NULL),
(5, 'Edith', 'Piaf', 'piaf@gmail.com', '123456', '2021-02-07 14:03:06', NULL);

-- --------------------------------------------------------

--
-- Déchargement des données de la table `bankaccounts`
--

INSERT INTO `bankaccounts` (`id`, `description`, `idOwner`, `createdAt`, `updatedAt`) VALUES
(1, 'Compte privé', 1, '2021-03-28 15:29:36', '2021-04-17 10:05:16'),
(2, 'Compte privé', 2, '2021-03-28 15:29:56', NULL),
(3, 'Compte privé', 3, '2021-04-17 10:05:30', NULL),
(4, 'Compte privé', 4, '2021-04-17 10:10:06', NULL),
(13, 'Compte privé', 5, '2021-04-17 10:51:05', NULL);

-- --------------------------------------------------------

--
-- Déchargement des données de la table `financialtransactions`
--

INSERT INTO `financialtransactions` (`id`, `amount`, `idSender`, `idRecipient`, `createdAt`, `updatedAt`) VALUES
(1, 30, 1, 2, '2021-03-28 15:31:20', NULL),
(2, 20, 1, 3, '2021-04-17 10:27:22', NULL),
(3, 10, 4, 1, '2021-04-17 10:34:29', NULL);

-- --------------------------------------------------------

--
-- Déchargement des données de la table `transactionmessages`
--

INSERT INTO `transactionmessages` (`id`, `content`, `idTransaction`, `idAuthor`, `createdAt`, `updatedAt`) VALUES
(1, 'Salut ! Merci pour ton coup de pouce !', 1, 2, '2021-03-28 15:38:05', '2021-04-17 11:01:50'),
(2, 'Merci pour ton aide !', 1, 1, '2021-03-28 16:00:01', NULL);

-- --------------------------------------------------------

--
-- Contraintes pour la table `bankaccounts`
--
ALTER TABLE `bankaccounts`
  ADD CONSTRAINT `fk_bankaccounts_users` FOREIGN KEY (`idOwner`) REFERENCES `users` (`id`);

-- --------------------------------------------------------

--
-- Contraintes pour la table `financialtransactions`
--
ALTER TABLE `financialtransactions`
  ADD CONSTRAINT `fk_financialtransactions_bankaccounts_sender` FOREIGN KEY (`idSender`) REFERENCES `bankaccounts` (`id`),
  ADD CONSTRAINT `fk_financialtransactions_bankaccounts_recipient` FOREIGN KEY (`idRecipient`) REFERENCES `bankaccounts` (`id`);

-- --------------------------------------------------------

--
-- Contraintes pour la table `transactionmessages`
--
ALTER TABLE `transactionmessages`
  ADD CONSTRAINT `fk_transactionmessages_financialtransactions` FOREIGN KEY (`idTransaction`) REFERENCES `financialtransactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transactionmessages_users` FOREIGN KEY (`idAuthor`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Contraintes pour la table `users`
--

ALTER TABLE `users`
  ADD UNIQUE(`mailAddress`);
  
-- --------------------------------------------------------

COMMIT;
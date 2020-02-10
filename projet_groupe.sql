-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : Dim 09 fév. 2020 à 00:14
-- Version du serveur :  10.4.11-MariaDB
-- Version de PHP : 7.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet_groupe`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_limite` datetime DEFAULT NULL,
  `active` smallint(6) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `description`, `date_creation`, `date_limite`, `active`, `user_id`) VALUES
(1, 'Recherche un photographe pour choucroute musicale', 'J\'ai un super concept de choucroute musicale et je cherche un bon photographe pour mettre ca en avant!', '2020-02-08 23:30:45', NULL, 1, 1),
(2, 'Recherche scénariste pour film', 'Je sais faire tourner une caméra mais j\'ai aucune idée', '2020-02-08 23:33:47', '2020-04-23 12:00:00', 1, 1),
(3, 'Guitariste recherche bassiste', 'Tout est dit', '2020-02-08 23:37:05', NULL, 1, 3),
(4, 'Graphiste recherche musicien', 'Je voudrais faire une chaise musicale, je recherche donc un ébéniste pour qui la luth greco-romaine c\'est pas du pipeau.', '2020-02-08 23:40:54', NULL, 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `annonces_users`
--

CREATE TABLE `annonces_users` (
  `annonces_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_at` datetime NOT NULL,
  `rgpd` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20200206141942', '2020-02-08 21:22:53'),
('20200206142645', '2020-02-08 21:22:54'),
('20200206144307', '2020-02-08 21:22:55'),
('20200206145108', '2020-02-08 21:22:55'),
('20200206145248', '2020-02-08 21:22:56'),
('20200206145401', '2020-02-08 21:22:57'),
('20200206145613', '2020-02-08 21:22:58'),
('20200206173543', '2020-02-08 21:22:58');

-- --------------------------------------------------------

--
-- Structure de la table `portfolio`
--

CREATE TABLE `portfolio` (
  `id` int(11) NOT NULL,
  `img_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` smallint(6) NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `roles`, `password`, `name`, `lastname`, `token`, `active`, `description`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'marc@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$Tk9aVTg2aXFZbnQzVDVZbg$UdrKR/8zIqAS0W2x3MFZZifR5iFhsoDnIMdij1vpMZc', 'Marc', 'BOYON', '0f10ee4818a41ea81014abdc9c5c2b3b4ceaaa689f', 1, 'Je suis un mec plutot gentil et passionné de burritos', 'avatar-15118.jpeg', '2020-02-08 23:06:38', '2020-02-08 23:06:38'),
(2, 'manon@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$U1dxVDNJL3E2akZiVkdUaw$kAEorSX8Cum+dUB6MKNWQJt5IvottY7gP1AEAyfsac0', 'Manon', 'Bissop', '0e4b7b4eb59460d0f60f299cbd6d9200d951d697c3', 1, NULL, NULL, '2020-02-08 23:07:36', '2020-02-08 23:07:36'),
(3, 'matthieu@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$c1ZtVXkvOS9BTnZ0SS9FTw$4e1z2i+bcZ2OJwnlvhPFULglHWO7NkSZnGEgofPFraU', 'Matthieu', 'Miloud', '2981d31c8445e0d11b6c2bc103434fd3e8fb9c978c', 1, 'Voila quoi', NULL, '2020-02-08 23:07:56', '2020-02-08 23:07:56'),
(4, 'olivier@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$QmMzeXJRMWZ6ei42SUpkSg$rGvOcMeYqZJiwZAAHxf+SzkvO8HNNvNCHE2PSI1DUk0', 'Olivier', 'legrand', '2c9c29f5f1cb3f5f5acecc3e8867759ff731ed7325', 1, NULL, NULL, '2020-02-08 23:08:21', '2020-02-08 23:08:21'),
(5, 'sandra@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$RERHbGUxblo2ZjRwL2Q1MA$s573YVksXVL3Ud4ueNnwVWebC93/ZTT2J9GzZ5FVZIY', 'Alexandra', 'roth', 'e2c39dec2d0a8bce9b0ec52c0643840c31f404dc12', 1, NULL, NULL, '2020-02-08 23:08:40', '2020-02-08 23:08:40'),
(6, 'victor@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$OUZYdENYNHlRSGpadTJ2RQ$JQd2On3SnlxFmUHrr+oXliYnkq9J0vptsdCRJqY/66E', 'Victor', 'Krumm', '2fcc208ddafd1c812320fb2dcd1bdab7a3d1a3732e', 1, NULL, NULL, '2020-02-08 23:14:03', '2020-02-08 23:14:03');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CB988C6FA76ED395` (`user_id`);

--
-- Index pour la table `annonces_users`
--
ALTER TABLE `annonces_users`
  ADD PRIMARY KEY (`annonces_id`,`users_id`),
  ADD KEY `IDX_F60119834C2885D7` (`annonces_id`),
  ADD KEY `IDX_F601198367B3B43D` (`users_id`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8F91ABF067B3B43D` (`users_id`);

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A9ED1062A76ED395` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD CONSTRAINT `FK_CB988C6FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `annonces_users`
--
ALTER TABLE `annonces_users`
  ADD CONSTRAINT `FK_F60119834C2885D7` FOREIGN KEY (`annonces_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_F601198367B3B43D` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `FK_8F91ABF067B3B43D` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `portfolio`
--
ALTER TABLE `portfolio`
  ADD CONSTRAINT `FK_A9ED1062A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

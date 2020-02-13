-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  jeu. 13 fév. 2020 à 08:38
-- Version du serveur :  5.7.24
-- Version de PHP :  7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projet_groupe`
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
  `user_id` int(11) NOT NULL,
  `prix` decimal(7,2) NOT NULL,
  `vues` int(11) NOT NULL,
  `prestataire_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `description`, `date_creation`, `date_limite`, `active`, `user_id`, `prix`, `vues`, `prestataire_id`) VALUES
(1, 'Recherche un photographe pour choucroute musicale', 'J\'ai un super concept de choucroute musicale et je cherche un bon photographe pour mettre ca en avant!', '2020-02-08 23:30:45', NULL, 1, 1, '0.00', 0, NULL),
(2, 'Recherche scénariste pour film', 'Je sais faire tourner une caméra mais j\'ai aucune idée', '2020-02-07 11:40:00', '2020-04-23 12:00:00', 1, 1, '0.00', 0, NULL),
(3, 'Guitariste recherche bassiste', 'Tout est dit', '2020-02-08 23:37:05', NULL, 1, 3, '0.00', 0, NULL),
(4, 'Graphiste recherche musicien', 'Je voudrais faire une chaise musicale, je recherche donc un ébéniste pour qui la luth greco-romaine c\'est pas du pipeau.', '2020-02-08 23:40:54', NULL, 1, 5, '0.00', 0, NULL),
(11, 'Nouvelle annonce', 'Ceci est une annonce', '2020-02-12 12:14:15', '2015-01-01 00:00:00', 1, 1, '300.00', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `annonces_tags`
--

CREATE TABLE `annonces_tags` (
  `annonces_id` int(11) NOT NULL,
  `tags_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonces_tags`
--

INSERT INTO `annonces_tags` (`annonces_id`, `tags_id`) VALUES
(11, 2);

-- --------------------------------------------------------

--
-- Structure de la table `annonces_users`
--

CREATE TABLE `annonces_users` (
  `annonces_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonces_users`
--

INSERT INTO `annonces_users` (`annonces_id`, `users_id`) VALUES
(1, 2),
(1, 5),
(2, 1),
(3, 1),
(4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_at` datetime NOT NULL,
  `rgpd` tinyint(1) NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `users_id`, `nom`, `contenu`, `create_at`, `rgpd`, `prenom`, `note`) VALUES
(1, 2, 'marc@hotmail.fr', 'C\'est une fille bien \r\nVraiment.\r\nJe recommande fortement!', '2020-02-11 11:41:17', 1, '', 0),
(2, 3, 'marc@hotmail.fr', 'Je te jure que ca marchait!!', '2020-02-11 14:02:43', 1, '', 0),
(3, 4, 'BOYON', 'C\'est un beau gosse il bosse bien ', '2020-02-13 09:16:45', 1, 'Marc', 5);

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
('20200213083517', '2020-02-13 08:36:08');

-- --------------------------------------------------------

--
-- Structure de la table `portfolio`
--

CREATE TABLE `portfolio` (
  `id` int(11) NOT NULL,
  `img_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `liens` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `portfolio`
--

INSERT INTO `portfolio` (`id`, `img_url`, `user_id`, `liens`) VALUES
(1, 'voleur.jpg', 1, NULL),
(2, 'bucheron.jpg', 1, NULL),
(5, 'chevalier.jpg', 3, NULL),
(6, 'foxy2.jpg', 1, 'https://soundcloud.com/liar-and-thief'),
(8, NULL, 1, 'http://www.capitaine-darahax.fr/'),
(11, NULL, 1, NULL),
(12, 'portfolio-5e43ce1c09eb3.jpeg', 1, NULL),
(13, 'portfolio-5e43ce3c197b8.png', 1, NULL),
(14, 'portfolio-5e43ce5e9d701.jpeg', 1, NULL),
(15, 'portfolio-5e43ce69bcc39.jpeg', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `signalement`
--

CREATE TABLE `signalement` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `annonce_id` int(11) DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `signalement`
--

INSERT INTO `signalement` (`id`, `user_id`, `annonce_id`, `message`) VALUES
(1, 3, NULL, 'C\'est un con'),
(2, NULL, 4, 'Le mec a l\'air louche'),
(3, NULL, 4, 'IL propose des trucs nazi c\'est pas cool'),
(4, NULL, 4, 'Franchement. VOila quoi'),
(5, NULL, 4, 'Cette article ca sent trop l\'arnaque, il me veut  un rein'),
(6, NULL, 1, 'Franchement voila quoi'),
(7, NULL, 4, 'Nul zero');

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tags`
--

INSERT INTO `tags` (`id`, `nom`) VALUES
(1, 'Musique '),
(2, 'Art graphique');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` smallint(6) NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission` double DEFAULT NULL,
  `vues` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `roles`, `password`, `lastname`, `token`, `active`, `description`, `avatar`, `created_at`, `updated_at`, `name`, `commission`, `vues`) VALUES
(1, 'marc@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$Q2J0MnJhZ1d1Tmx5NUNMNw$iXo8+mtzh73ik3NZw5QXaGsoQojV0dlDLNwFvTBSPS8', 'BOYON', '0f10ee4818a41ea81014abdc9c5c2b3b4ceaaa689f', 1, 'Je suis un mec plutot gentil et passionné de burritos', 'avatar-15118.jpeg', '2020-02-08 23:06:38', '2020-02-08 23:06:38', 'Marc', NULL, 22),
(2, 'manon@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$UWMvd3VYdk5GcjlUZzc1ag$4RB2ZwRzg1loM+tFVoStOLVazRGzzGSE6SGZUNn6X50', 'Bissop', '0e4b7b4eb59460d0f60f299cbd6d9200d951d697c3', 1, NULL, NULL, '2020-02-08 23:07:36', '2020-02-08 23:07:36', 'Manon', NULL, 0),
(3, 'matthieu@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$c1ZtVXkvOS9BTnZ0SS9FTw$4e1z2i+bcZ2OJwnlvhPFULglHWO7NkSZnGEgofPFraU', 'Miloud', '2981d31c8445e0d11b6c2bc103434fd3e8fb9c978c', 1, 'Voila quoi', NULL, '2020-02-08 23:07:56', '2020-02-08 23:07:56', 'Matthieu', 50, 3),
(4, 'olivier@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$QmMzeXJRMWZ6ei42SUpkSg$rGvOcMeYqZJiwZAAHxf+SzkvO8HNNvNCHE2PSI1DUk0', 'legrand', '2c9c29f5f1cb3f5f5acecc3e8867759ff731ed7325', 1, NULL, NULL, '2020-02-08 23:08:21', '2020-02-08 23:08:21', 'Olivier', 20, 14),
(5, 'sandra@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2i$v=19$m=65536,t=4,p=1$RERHbGUxblo2ZjRwL2Q1MA$s573YVksXVL3Ud4ueNnwVWebC93/ZTT2J9GzZ5FVZIY', 'roth', 'e2c39dec2d0a8bce9b0ec52c0643840c31f404dc12', 1, NULL, NULL, '2020-02-08 23:08:40', '2020-02-08 23:08:40', 'Alexandra', NULL, 0),
(6, 'victor@hotmail.fr', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$d2hnUlBveGpWNVhkL0RHYw$HmfdAD3530AjX7cHdawjnfG/FAlXs+fgIoR68ASFLCY', 'Krumm', '2fcc208ddafd1c812320fb2dcd1bdab7a3d1a3732e', 1, NULL, NULL, '2020-02-08 23:14:03', '2020-02-08 23:14:03', 'Victor', NULL, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CB988C6FA76ED395` (`user_id`),
  ADD KEY `IDX_CB988C6FBE3DB2B7` (`prestataire_id`);

--
-- Index pour la table `annonces_tags`
--
ALTER TABLE `annonces_tags`
  ADD PRIMARY KEY (`annonces_id`,`tags_id`),
  ADD KEY `IDX_557AEAEF4C2885D7` (`annonces_id`),
  ADD KEY `IDX_557AEAEF8D7B4FB4` (`tags_id`);

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
-- Index pour la table `signalement`
--
ALTER TABLE `signalement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F4B55114A76ED395` (`user_id`),
  ADD KEY `IDX_F4B551148805AB2F` (`annonce_id`);

--
-- Index pour la table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `signalement`
--
ALTER TABLE `signalement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  ADD CONSTRAINT `FK_CB988C6FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_CB988C6FBE3DB2B7` FOREIGN KEY (`prestataire_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `annonces_tags`
--
ALTER TABLE `annonces_tags`
  ADD CONSTRAINT `FK_557AEAEF4C2885D7` FOREIGN KEY (`annonces_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_557AEAEF8D7B4FB4` FOREIGN KEY (`tags_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

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

--
-- Contraintes pour la table `signalement`
--
ALTER TABLE `signalement`
  ADD CONSTRAINT `FK_F4B551148805AB2F` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`),
  ADD CONSTRAINT `FK_F4B55114A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

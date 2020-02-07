-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  ven. 07 fév. 2020 à 12:40
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
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `description`, `date_creation`, `date_limite`, `active`, `user_id`) VALUES
(2, 'Test', 'Je fais des tests mais je ne sais pas vraiment comment, pourriez vous m\'aider ? ', '2020-02-06 09:23:00', '2020-02-28 08:33:00', 1, 11),
(3, 'Nouvelle annonce', 'C\'est quoi ce truc ', '2020-02-06 08:34:00', '2020-02-10 07:29:00', 1, 7),
(4, 'Recherche un scenariste', 'Je dessine trop bien mais j\'ai pas de talent de scénariste', '2020-02-03 07:28:00', NULL, 1, 7),
(5, 'Recherche graphiste', 'Je code bien mais je dessine comme un brouette. Je cherche un graphiste', '2020-02-06 09:27:00', NULL, 1, 8),
(6, 'Recherche bassiste', 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.', '2020-02-05 09:28:00', NULL, 1, 7),
(7, 'Je recherche un metteur en scene pour ma nouvelle piece de théatre', 'J\'ai un super scenario mais j\'aurai besoin d\'aide pour mettre en scène ma pièce', '2020-02-04 08:30:00', NULL, 1, 12);

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
('20200206141942', '2020-02-06 14:21:23'),
('20200206142645', '2020-02-06 14:26:53'),
('20200206144307', '2020-02-06 14:43:15'),
('20200206145108', '2020-02-06 14:51:17'),
('20200206145248', '2020-02-06 14:52:55'),
('20200206145401', '2020-02-06 14:54:08'),
('20200206145613', '2020-02-06 14:56:19'),
('20200207084843', '2020-02-07 08:48:54');

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
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` smallint(6) NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `roles`, `password`, `name`, `lastname`, `token`, `active`, `description`, `avatar`, `created_at`, `updated_at`) VALUES
(7, 'marc@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$bS5PZDFCNVIveGp1ck9GeQ$HG127hXpiPay99aac1LSsxxVkIEgVvl5g3Jp0GKiJUY', 'Marc', 'BOYON', 'e4e2cbb7d1fab4fec675035bb0d8c0941b1f494760', 1, NULL, NULL, '2020-02-07 11:23:26', '2020-02-07 11:23:26'),
(8, 'juju@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$VmlacHkxeWN5RVEucWlMRg$fSyeu2M5x1s1IzW/xMjmhMK2HhHZAdQYgv111n9Xd6Y', 'Juju', 'Lefebvre', '0d272dbbe9b82fd3b0571ecb6d018a2ec4e8140941', 1, NULL, NULL, '2020-02-07 11:44:22', '2020-02-07 11:44:22'),
(9, 'henry@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$SEN6Sk9FdFhBckQyWEl0Sg$9yKzveC2Oge1oFKcBJiBPeFE2t/femyOGga6VPVz4e0', 'Henry', 'Jolifleur', '86e9dbc44899267e1eeeb243207d52e1f6a80ab54f', 1, NULL, NULL, '2020-02-07 13:29:55', '2020-02-07 13:29:55'),
(10, 'sandra@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$Y20vSG1ZUHFCaWwxMUNXQQ$F+1ARzL8WJWe7q3nSLaYBWvZAgVZWVr7sEcGHT4s5TA', 'Sandra', 'Roth', '72321d773ce94c897c85508e7913b122f7f132fa1d', 1, NULL, NULL, '2020-02-07 13:31:02', '2020-02-07 13:31:02'),
(11, 'matthieu@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$OE80OTV5Q2ZSRlkxUXVLbQ$bQaVeP0aGzfvlGNxZBD3dqkqoxXqjrGGm1qS8o0LoVE', 'Matthieu', 'Miloud', 'cfc87f0bf47b3bd4534e0b9f706af8beca72312157', 1, NULL, NULL, '2020-02-07 13:31:51', '2020-02-07 13:31:51'),
(12, 'olivier@hotmail.fr', '[\"ROLE_PUBLISHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$NVZ4MEtVM0MuOG1BOFo1WA$JWrjhiaXrnE5GSqyBp/3FhA3CxzbNDESe1BmiJaN0og', 'Olivier', 'Legrand', '81f4bc69f87bf1f9296fe0e37bdb8f848666e960fd', 1, NULL, NULL, '2020-02-07 13:34:35', '2020-02-07 13:34:35');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD CONSTRAINT `FK_CB988C6FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `portfolio`
--
ALTER TABLE `portfolio`
  ADD CONSTRAINT `FK_A9ED1062A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

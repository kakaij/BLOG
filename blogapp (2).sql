-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2025 at 01:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blogapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(2, 'adventure'),
(1, 'fantasy'),
(3, 'history');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 'interesting!!!', '2025-02-02 00:26:00'),
(2, 3, 1, 'then practice <?php?> daily and you\'ll get to see the results!!', '2025-02-02 00:27:40'),
(3, 7, 5, 'maritime engineering', '2025-02-02 00:37:15');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `user_id`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'the unspotted tiger ', 'the panther black like coal a &quot;cat specie&quot; that hunts during the darkest of hours', 1, 1, '2025-02-01 18:41:46', '2025-02-01 18:41:46'),
(2, 'the farm frenzy', 'the farm with all animals you know but only there is a catch where once in a while a pack of wolves suddenly appears to eat the farm animals. As the owner, you must be quick enough to prevent that from happening so the only way to do that is to adjust and improve the security of the farm.', 2, 2, '2025-02-01 19:02:09', '2025-02-01 19:02:09'),
(3, 'Looking foward to understand &lt;?PHP?&gt; more', 'i think compared to Node i guess PHP is quite straight foward', 3, 3, '2025-02-01 22:44:37', '2025-02-01 22:44:37'),
(6, '&lt;?PHP?&gt; FOUNDATION', 'Still alot to do but code camp and youtube plus a touch of AI is doing some magic ', 3, 2, '2025-02-01 22:52:59', '2025-02-01 22:52:59'),
(7, 'nautical science', 'marine study and the diverse sea life', 5, 3, '2025-02-02 00:35:18', '2025-02-02 00:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'kakxyofficial', 'julykirkxy@gmail.com', '$2y$10$mc3J7Vrt1I60x3OMJUDnMOOI.QZAVMKb.dffgwy9z3a67an4lXfR2', 'user', '2025-02-01 16:13:06'),
(2, 'julius', 'juliuskakai99@gmail.com', '$2y$10$LTzmlYoSIKFYAIM3yiFbL.Kt8ma/9FFvY17AI4tKuVLLvu.jE/oCW', 'user', '2025-02-01 18:59:34'),
(3, 'doombo', 'doombojr@gmail.com', '$2y$10$kKaIejBb0QNekxfjO3mDAeTXg1KZjE2m5yada5RQA3iVywzyBVxZO', 'user', '2025-02-01 22:42:30'),
(4, 'Doritos', 'dori@gmail.com', '$2y$10$wasvS.h.R408iTaD.lyCsOKsrgLqDySDst8KSOFFbayTmcH94hTGW', 'user', '2025-02-01 23:39:09'),
(5, 'kakai', 'kakaimary4@gmail.com', '$2y$10$mpcCvhhVLQimmn/MCLfm7uYEpor0b4s4F3CeYDUkXrYrmTA5oGO12', 'user', '2025-02-02 00:34:26'),
(6, 'makhanda', 'makhandaofficial@gmail.com', '$2y$10$1Fa2lxPGDTSPkFmJzcxeX.sIWzongM2hUeolsfHgcvIvMyKgSNe/a', 'user', '2025-02-02 01:03:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2026 at 04:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `youtube_clone`
--

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `ads` (
  `ads_id` int(10) NOT NULL,
  `ad_title` varchar(50) NOT NULL,
  `ad_video_url` varchar(50) NOT NULL,
  `ad_link` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`ads_id`, `ad_title`, `ad_video_url`, `ad_link`) VALUES
(12, '1', 'assets/video/ads/1756360817_Oreo.mp4', '1'),
(13, '5star-Ad', 'assets/video/ads/1756362526_5star-Ad.mp4', 'do nothing');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `comment_id` int(10) NOT NULL,
  `form_username` varchar(50) NOT NULL,
  `form_user_id` int(10) NOT NULL,
  `to_use_id` int(10) NOT NULL,
  `video_id` int(10) NOT NULL,
  `massage` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `likes_id` int(50) NOT NULL,
  `form_user_id` int(10) NOT NULL,
  `to_user_id` int(10) NOT NULL,
  `video_id` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`likes_id`, `form_user_id`, `to_user_id`, `video_id`) VALUES
(56, 50, 44, 50),
(57, 47, 43, 47),
(58, 42, 43, 42);

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `Reprot_id` int(10) NOT NULL,
  `video_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `Reason_for_Reporting` varchar(50) NOT NULL,
  `Additional_Details` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`Reprot_id`, `video_id`, `user_id`, `Reason_for_Reporting`, `Additional_Details`) VALUES
(18, 25, 25, 'Spam', 'hi'),
(19, 24, 25, 'Spam', 'hi'),
(21, 45, 25, 'Spam', 'for testing\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `subscribe`
--

CREATE TABLE `subscribe` (
  `subscribe_id` int(10) NOT NULL,
  `subscriber_user_id` int(10) NOT NULL,
  `subscribe_user_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscribe`
--

INSERT INTO `subscribe` (`subscribe_id`, `subscriber_user_id`, `subscribe_user_id`) VALUES
(21, 25, 27),
(23, 27, 27),
(24, 43, 43),
(25, 44, 44),
(26, 44, 43),
(27, 43, 44);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `about` text DEFAULT NULL,
  `dp_url` varchar(255) DEFAULT 'assets/img/dp/default.png',
  `role` varchar(50) DEFAULT 'user',
  `is_premium` tinyint(1) DEFAULT 0,
  `subscribers` int(11) DEFAULT 0,
  `videos_uploaded` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `about`, `dp_url`, `role`, `is_premium`, `subscribers`, `videos_uploaded`, `created_at`) VALUES
(25, 'admin', 'jalpitparmar1234@gmail.com', '$2y$10$x/XrCSoEljxPUy7HEiSNFuRlJNpzn0VHAMbqASFA2yi8lrd6gXXtm', 'admin id', 'assets\\img\\dp\\default.png', 'admin', 1, 0, 1, '2025-08-28 02:15:06'),
(43, 'Music', 'music@example.com', '$2y$10$D4FywL43pZPdjNEHmZkgFeKCMYpf/DGuS1a2VYHNUS9pYvFO/.72m', 'About Music Channel', 'assets/img/dp/1756626197_Musicdp.jpg', 'user', 0, 2, 4, '2025-08-31 07:26:13'),
(44, 'Sports', 'sports@example.com', '$2y$10$V60zTupTmjQLoRG/M7Z8Vegn8FtC2B3sj9TSaysW3oJzqchyeD/Yy', 'About Sports Channel', 'assets/img/dp/1756628246_download.jpeg', 'user', 1, 2, 5, '2025-08-31 08:13:30'),
(58, 'game', 'game@gmail.com', '$2y$10$4wwYVYbwznBUk9Lc03dE5.PfikqbEzq6UsLlrGZVAdxMbsiZOAPtG', 'for game', 'assets/img/dp/1756746065_game.jpeg', 'user', 0, 0, 1, '2025-09-01 13:20:48');

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE `video` (
  `video_id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `thumbnail_url` varchar(500) NOT NULL,
  `video_url` varchar(500) NOT NULL,
  `views` int(11) DEFAULT 0,
  `likes` int(11) DEFAULT 0,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video`
--

INSERT INTO `video` (`video_id`, `user_id`, `title`, `description`, `category`, `thumbnail_url`, `video_url`, `views`, `likes`, `time`, `username`) VALUES
(42, 43, 'Charlie Puth  Attention Official Video', 'Charlie Puth Attention Official Video\r\nFrom Charlie s album Voicenotes!', 'Music', 'assets/img/thumbnail/_nfs8NYg7yQM-HQ.jpg', 'assets/video/yt/_Attention-Official-.mp4', 7, 1, '2025-08-31 13:12:16', 'Music'),
(43, 43, 'Charlie Puth - We Don\'t Talk Anymore (feat. Selena Gomez) [Official Video]', 'Charlie Puth - We Don\'t Talk Anymore (feat. Selena Gomez) [Official Video]\r\nFrom Charlie\'s debut album Nine Track Mind!', 'Music', 'assets/img/thumbnail/_3AtDnEC4zak-SD.jpg', 'assets/video/yt/_Charlie-Puth-We-Don-t-Talk-.mp4', 5, 0, '2025-08-31 13:18:33', 'Music'),
(44, 43, 'Starboy', 'Starboy · The Weeknd · Daft Punk\r\n\r\nStarboy\r\n\r\n℗ 2016 The Weeknd XO, Inc., Manufactured and Marketed by Republic Records, a Division of UMG Recordings, Inc.\r\n\r\nReleased on: 2018-04-20', 'Music', 'assets/img/thumbnail/_3_g2un5M350-SD.jpg', 'assets/video/yt/_ssvid.net--Starboy_v720P.mp4', 2, 0, '2025-08-31 13:24:13', 'Music'),
(45, 43, ' Justin Bieber - Baby ft. Ludacris', 'REMASTERED IN HD! UP TO 4K!\r\nOfficial Music Video for Baby performed by Justin Bieber (ft. Ludacris).\r\n', 'Music', 'assets/img/thumbnail/_kffacxfA7G4-SD.jpg', 'assets/video/yt/_ssvid.net--Justin-Bieber-Baby-ft-Ludacris_v720P.mp4', 4, 0, '2025-08-31 13:26:36', 'Music'),
(46, 43, ' Lady Gaga, Bruno Mars - Die With A Smile (Official Music Video)', 'Directed by Daniel Ramos & Bruno Mars\r\nMusic video by Lady Gaga, Bruno Mars performing Die With A Smile.© 2024 Interscope Records', 'Music', 'assets/img/thumbnail/_kPa7bsKwL-c-SD.jpg', 'assets/video/yt/_ssvid.net--Lady-Gaga-Bruno-Mars-Die-With-A-Smile-Official_v720P.mp4', 2, 0, '2025-08-31 13:29:57', 'Music'),
(47, 43, 'DAN DA DAN - Opening | Otonoke de Creepy Nuts', 'DAN DA DAN - Opening | Otonoke de Creepy Nuts\r\n\r\n¡Ve DAN DA DAN ya mismo en Crunchyroll!', 'Music', 'assets/img/thumbnail/_qPdPjWkJZF8-SD.jpg', 'assets/video/yt/_ssvid.net--DAN-DA-DAN-Opening-Otonoke-de-Creepy-Nuts_v720P.mp4', 42, 1, '2025-08-31 13:39:21', 'Music'),
(49, 44, 'HIGHLIGHTS | Real Madrid 2-1 RCD Mallorca | LaLiga', 'Real Madrid, who had three goals disallowed, came from behind courtesy of goals from Arda Güler and Vini Jr. to continue their winning run in LaLiga.', 'Sports', 'assets/img/thumbnail/_2EGTD_d6Ubo-SD.jpg', 'assets/video/yt/_ssvid.net--HIGHLIGHTS-Real-Madrid-2-1-RCD-Mallorca-LaLiga_360p.mp4', 3, 0, '2025-08-31 13:54:14', 'Sports'),
(50, 44, 'LATE RIO NGUMOHA WINNER! Newcastle 2-3 Liverpool | Highlights', 'Watch key highlights from Liverpool’s 3-2 victory over Newcastle United in the Premier League, with goals from Ryan Gravenberch, Hugo Ekitike & Rio Ngumoha securing three points at St James\' Park.', 'Sports', 'assets/img/thumbnail/_EbRtHkEPz8c-HQ.jpg', 'assets/video/yt/_ssvid.net--LATE-RIO-NGUMOHA-WINNER-Newcastle-2-3-Liverpool-Highlights_360p.mp4', 4, 1, '2025-08-31 13:57:55', 'Sports'),
(51, 44, ' Alonso & Russell Drama! FP3 Highlights | 2025 Dutch Grand Prix', 'Catch up with all the action from the final practice session ahead of Qualifying for the Dutch Grand Prix!\r\n\r\nFor more F1® videos, visit: https://www.Formula1.com\r\n\r\nVisit our store: https://f1store.formula1.com/', 'Sports', 'assets/img/thumbnail/_TgSeI-qHwFA-HQ.jpg', 'assets/video/yt/_ssvid.net--Alonso-Russell-Drama-FP3-Highlights-2025-Dutch-Grand_360p.mp4', 2, 0, '2025-08-31 14:25:52', 'Sports'),
(52, 44, ' Novak Djokovic vs. Cameron Norrie Highlights | 2025 US Open Round 3', 'Watch the highlights of Novak Djokovic vs. Cameron Norrie in Round 3 of the 2025 US Open.\r\n\r\nDon\'t miss a moment of the US Open! Subscribe now: https://bit.ly/2Pdr81i', 'Sports', 'assets/img/thumbnail/_HZvmy7CE2eI-SD.jpg', 'assets/video/yt/_videoplayback.mp4', 0, 0, '2025-08-31 14:30:21', 'Sports'),
(53, 44, ' FP2 Highlights | 2025 Dutch Grand Prix', 'Plenty more action and incidents in our second practice run at Zandvoort - catch up now!\r\n\r\nFor more F1® videos, visit: https://www.Formula1.com\r\n\r\nVisit our store: https://f1store.formula1.com/', 'Sports', 'assets/img/thumbnail/_zRuF3P_JgVw-SD.jpg', 'assets/video/yt/_ssvid.net--FP2-Highlights-2025-Dutch-Grand-Prix_360p.mp4', 0, 0, '2025-08-31 14:36:47', 'Sports'),
(54, 58, 'YSS Full Guide in 5 Minutes! || Revamp 2025 || GAMEPLAY || MLBB', 'Welcome back to IndusML! In this short and powerful guide, I’ll teach you everything you need to know about Yu Sun-Shin, the deadly Marksman/Assassin in Mobile Legends. With the 2025 revamp, YSS now has a brand-new ultimate skill that grants global vision and deals insane damage with CC in a small circle', 'Gaming', 'assets/img/thumbnail/_wVJWmgwKJHg-SD.jpg', 'assets/video/yt/_yss.mp4', 0, 0, '2025-09-01 22:34:17', 'jalpit');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`ads_id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`likes_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`Reprot_id`);

--
-- Indexes for table `subscribe`
--
ALTER TABLE `subscribe`
  ADD PRIMARY KEY (`subscribe_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`video_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ads`
--
ALTER TABLE `ads`
  MODIFY `ads_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `likes_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `Reprot_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `subscribe`
--
ALTER TABLE `subscribe`
  MODIFY `subscribe_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `video`
--
ALTER TABLE `video`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

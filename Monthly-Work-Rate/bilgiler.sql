-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 13 Ağu 2024, 14:25:07
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `bilgiler`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$lLsA0joaw4pt.Y9.XtCXmuprPt/DPDu1k6CiIRLF.pomoFPM4cZFa', '2024-07-31 11:09:23');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `monthly_work_rates`
--

CREATE TABLE `monthly_work_rates` (
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `work_percentage` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `monthly_work_rates`
--

INSERT INTO `monthly_work_rates` (`user_id`, `project_id`, `month_year`, `work_percentage`) VALUES
(7, 18, '2024-11', 0.6),
(7, 18, '2024-12', 0.7),
(7, 18, '2025-01', 0.3),
(7, 18, '2025-02', 0),
(7, 18, '2025-03', 0),
(7, 18, '2025-04', 0),
(7, 18, '2025-05', 0),
(7, 18, '2025-06', 0),
(7, 18, '2025-07', 0),
(7, 18, '2025-08', 0),
(7, 18, '2025-09', 0),
(7, 18, '2025-10', 0),
(7, 20, '2024-01', 0.5),
(7, 20, '2024-02', 0.5),
(7, 20, '2024-03', 0.5),
(7, 20, '2024-04', 0.5),
(7, 20, '2024-05', 0.5),
(7, 20, '2024-06', 0),
(7, 20, '2024-07', 0),
(7, 20, '2024-08', 0),
(7, 20, '2024-09', 0.5),
(7, 20, '2024-10', 0),
(7, 20, '2024-11', 0),
(7, 20, '2024-12', 0),
(7, 20, '2025-01', 0),
(7, 20, '2025-02', 0),
(7, 20, '2025-03', 0),
(7, 20, '2025-04', 0),
(7, 20, '2025-05', 0),
(7, 20, '2025-06', 0),
(7, 20, '2025-07', 0),
(7, 20, '2025-08', 0),
(7, 20, '2025-09', 0),
(7, 20, '2025-10', 0),
(7, 20, '2025-11', 0),
(7, 20, '2025-12', 0),
(7, 20, '2026-01', 0),
(11, 20, '2024-01', 0.6),
(11, 20, '2024-02', 0.6),
(11, 20, '2024-03', 0.6),
(11, 20, '2024-04', 0.6),
(11, 20, '2024-05', 0.6),
(11, 20, '2024-06', 0),
(11, 20, '2024-07', 0.06),
(11, 20, '2024-08', 0),
(11, 20, '2024-09', 0),
(11, 20, '2024-10', 0),
(11, 20, '2024-11', 0),
(11, 20, '2024-12', 0),
(11, 20, '2025-01', 0),
(11, 20, '2025-02', 0),
(11, 20, '2025-03', 0),
(11, 20, '2025-04', 0),
(11, 20, '2025-05', 0),
(11, 20, '2025-06', 0),
(11, 20, '2025-07', 0),
(11, 20, '2025-08', 0),
(11, 20, '2025-09', 0),
(11, 20, '2025-10', 0),
(11, 20, '2025-11', 0),
(11, 20, '2025-12', 0),
(11, 20, '2026-01', 0),
(12, 17, '2024-01', 1),
(12, 17, '2024-02', 0),
(12, 17, '2024-03', 0),
(12, 17, '2024-04', 0),
(12, 17, '2024-05', 0),
(12, 17, '2024-06', 0),
(12, 17, '2024-07', 0),
(12, 17, '2024-08', 0),
(12, 17, '2024-09', 0),
(12, 17, '2024-10', 0),
(12, 17, '2024-11', 0),
(12, 17, '2024-12', 0),
(12, 17, '2025-01', 0),
(12, 17, '2025-02', 0),
(12, 17, '2025-03', 0),
(12, 17, '2025-04', 0),
(12, 17, '2025-05', 0),
(12, 17, '2025-06', 0),
(12, 17, '2025-07', 0),
(12, 17, '2025-08', 0),
(12, 17, '2025-09', 0),
(12, 17, '2025-10', 0),
(12, 17, '2025-11', 0),
(12, 17, '2025-12', 0),
(12, 17, '2026-01', 0),
(12, 20, '2024-01', 0),
(12, 20, '2024-02', 1),
(12, 20, '2024-03', 0),
(12, 20, '2024-04', 0),
(12, 20, '2024-05', 0),
(12, 20, '2024-06', 0),
(12, 20, '2024-07', 0),
(12, 20, '2024-08', 0),
(12, 20, '2024-09', 0),
(12, 20, '2024-10', 1),
(12, 20, '2024-11', 1),
(12, 20, '2024-12', 1),
(12, 20, '2025-01', 1),
(12, 20, '2025-02', 1),
(12, 20, '2025-03', 1),
(12, 20, '2025-04', 1),
(12, 20, '2025-05', 1),
(12, 20, '2025-06', 1),
(12, 20, '2025-07', 1),
(12, 20, '2025-08', 1),
(12, 20, '2025-09', 1),
(12, 20, '2025-10', 1),
(12, 20, '2025-11', 1),
(12, 20, '2025-12', 1),
(12, 20, '2026-01', 1),
(12, 21, '2024-03', 1),
(12, 21, '2024-04', 1),
(12, 21, '2024-05', 1),
(12, 21, '2024-06', 1),
(12, 21, '2024-07', 1),
(12, 21, '2024-08', 1),
(12, 21, '2024-09', 1),
(12, 21, '2024-10', 0),
(12, 21, '2024-11', 0),
(12, 21, '2024-12', 0),
(12, 21, '2025-01', 0),
(12, 21, '2025-02', 0),
(12, 21, '2025-03', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `project_user`
--

CREATE TABLE `project_user` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `personnel_type` enum('akademik','sözleşmeli') NOT NULL,
  `tcno` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tablo için indeksler `monthly_work_rates`
--
ALTER TABLE `monthly_work_rates`
  ADD PRIMARY KEY (`user_id`,`project_id`,`month_year`),
  ADD UNIQUE KEY `unique_work_rate` (`user_id`,`project_id`,`month_year`);

--
-- Tablo için indeksler `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD UNIQUE KEY `project_name` (`project_name`);

--
-- Tablo için indeksler `project_user`
--
ALTER TABLE `project_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_user` (`project_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `tcno` (`tcno`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `project_user`
--
ALTER TABLE `project_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `project_user`
--
ALTER TABLE `project_user`
  ADD CONSTRAINT `project_user_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

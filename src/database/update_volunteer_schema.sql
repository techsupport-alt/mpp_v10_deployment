-- SQL script to create the new volunteer_registrations table
-- This table matches the detailed volunteer form in participate-section.php

DROP TABLE IF EXISTS `volunteer_registrations`;

CREATE TABLE `volunteer_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `country` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `church_affiliation` varchar(255) DEFAULT NULL,
  `service_type` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `duration` varchar(50) NOT NULL,
  `time_of_day` varchar(50) NOT NULL,
  `born_again` varchar(10) NOT NULL,
  `bible_study` varchar(50) NOT NULL,
  `prayer_frequency` varchar(50) NOT NULL,
  `emergency_name` varchar(255) NOT NULL,
  `emergency_phone` varchar(50) NOT NULL,
  `emergency_relationship` varchar(100) NOT NULL,
  `comments` text DEFAULT NULL,
  `commitment_consent` tinyint(1) NOT NULL,
  `devotional_consent` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

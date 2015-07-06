-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Jul 06, 2015 at 11:03 AM
-- Server version: 5.5.38
-- PHP Version: 5.5.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `imp-her`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
`id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories_has_videos`
--

CREATE TABLE `categories_has_videos` (
  `datetime` datetime NOT NULL,
  `video_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
`id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `youtube_id` varchar(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Indexes for table `categories_has_videos`
--
ALTER TABLE `categories_has_videos`
 ADD PRIMARY KEY (`datetime`,`video_id`,`category_id`), ADD KEY `fk_categories_has_videos_videos_idx` (`video_id`), ADD KEY `fk_categories_has_videos_categories1_idx` (`category_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories_has_videos`
--
ALTER TABLE `categories_has_videos`
ADD CONSTRAINT `fk_categories_has_videos_videos` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_categories_has_videos_categories1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

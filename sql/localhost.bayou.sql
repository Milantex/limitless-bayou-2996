/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE IF NOT EXISTS `bayou` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `bayou`;

CREATE TABLE IF NOT EXISTS `post` (
  `post_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `visible` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`post_id`),
  UNIQUE KEY `link` (`link`),
  KEY `fk_post_user_id` (`user_id`),
  CONSTRAINT `fk_post_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40000 ALTER TABLE `post` DISABLE KEYS */;
INSERT IGNORE INTO `post` (`post_id`, `created_at`, `user_id`, `title`, `link`, `content`, `visible`) VALUES
	(1, '2015-02-12 20:44:04', 1, 'Limitless Bayou begins', 'limitsless-bayou-begins', 'This is a sample post in a database used by the Limitless Bayou API.', b'1'),
	(2, '2015-02-12 21:04:27', 1, 'Another post in the database', 'another-post-in-the-database', 'This is the content of the second post in the Limitless Bayou API project.', b'1');
/*!40000 ALTER TABLE `post` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_At` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(88) COLLATE utf8_unicode_ci NOT NULL,
  `active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT IGNORE INTO `user` (`user_id`, `created_At`, `username`, `password`, `active`) VALUES
	(1, '2015-02-12 20:41:12', 'milantex', 'MLGyOlbUqqe/lRNV1PPM+xV/LPeyfWc6G5FhPEMfKKdtPmIbnzj9PHlU1hmQnvTLtXTiJdbWDtxtQj5lIRJjOg==', b'1');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
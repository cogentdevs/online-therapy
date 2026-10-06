-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2026 at 01:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digital_magazine`
--

-- --------------------------------------------------------

--
-- Table structure for table `abouts`
--

CREATE TABLE `abouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) NOT NULL,
  `section_condition` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `title_2` varchar(255) DEFAULT NULL,
  `description_2` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_2` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `abouts`
--

INSERT INTO `abouts` (`id`, `language`, `section_condition`, `title`, `description`, `title_2`, `description_2`, `image`, `image_2`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'ur', 3, 'Digital Magazine', '<div>\r\n<h2>What is Lorem Ipsum?</h2>\r\n<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.</p>\r\n</div>\r\n<div>\r\n<h2>Why do we use it?</h2>\r\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>\r\n</div>\r\n<p>&nbsp;</p>\r\n<div>\r\n<h2>Where does it come from?</h2>\r\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p>\r\n</div>', NULL, NULL, 'images/backend-images/about/01M16M4QJC7C3XN20CE1MA35KB.png', NULL, 1, 1, 1, '2026-08-29 06:16:10', '2026-08-29 06:23:10'),
(2, 'ur', 2, NULL, NULL, NULL, NULL, 'images/backend-images/about/01M207K7QN0EPQNXJX6758M8KM.png', NULL, 1, 1, 1, '2026-09-08 05:04:09', '2026-09-08 05:04:09'),
(3, 'ur', 1, 'کاروباری مشکلات کا حل', '<p>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔</p>', NULL, NULL, NULL, NULL, 1, 1, 1, '2026-09-08 05:05:12', '2026-09-08 05:05:12'),
(4, 'ur', 6, NULL, NULL, NULL, NULL, 'images/backend-images/about/01M207Q9X0ZRW98VKMY0TB6GEC.jpg', 'images/backend-images/about/01M207QA3S32AD3BVA5QBKJM6D.jpg', 1, 1, 1, '2026-09-08 05:06:22', '2026-09-08 05:06:22'),
(5, 'ur', 5, NULL, '<p>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔</p>', NULL, '<p>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔</p>', NULL, NULL, 1, 1, 1, '2026-09-08 05:07:06', '2026-09-08 05:07:06'),
(6, 'ur', 4, 'کاروباری مشکلات کا حل', '<p>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔</p>', NULL, NULL, 'images/backend-images/about/01M207TRE034H19XVDKRY2KNG6.png', NULL, 1, 1, 1, '2026-09-08 05:08:15', '2026-09-08 05:08:15');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_name`, `module`, `action`, `subject_type`, `subject_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'Super Admin', 'super-admin', 'roles', 'updated', 'Spatie\\Permission\\Models\\Role', 4, 'Updated Admin role \"Content Manager\".', '{\"name\":\"Content Manager\",\"permissions\":[\"admin.access\",\"articles.create\",\"articles.delete\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.delete\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.delete\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.delete\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\",\"tags.create\",\"tags.delete\",\"tags.edit\",\"tags.view\"]}', '{\"name\":\"Content Manager\",\"permissions\":[\"admin.access\",\"articles.create\",\"articles.delete\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.delete\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.delete\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.delete\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\",\"tags.create\",\"tags.delete\",\"tags.edit\",\"tags.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:19:34'),
(2, 1, 'Super Admin', 'super-admin', 'users', 'permission_mode_changed', 'App\\Models\\User', 3, 'Changed Admin user \"Muhammad Muzammil\" permission mode.', '{\"permission_mode\":\"role\"}', '{\"permission_mode\":\"custom\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:22:12'),
(3, 1, 'Super Admin', 'super-admin', 'users', 'permissions_changed', 'App\\Models\\User', 3, 'Changed Admin user \"Muhammad Muzammil\" limited permissions.', '{\"permissions\":[]}', '{\"permissions\":[\"articles.create\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:22:12'),
(4, 3, 'Muhammad Muzammil', 'Content Manager', 'categories', 'updated', 'App\\Models\\Category', 1, 'Updated Category \"Islamic Business\".', '{\"name\":\"Business\"}', '{\"name\":\"Islamic Business\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:26:54'),
(5, 3, 'Muhammad Muzammil', 'Content Manager', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 2, 'Updated Meta Tag \"Islamic Business\".', '{\"title\":\"Business\",\"slug_url\":\"business\"}', '{\"title\":\"Islamic Business\",\"slug_url\":\"islamic-business\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:26:54'),
(6, 3, 'Muhammad Muzammil', 'Content Manager', 'articles', 'updated', 'App\\Models\\Article', 1, 'Updated Article \"Where does it come from\".', '{\"title\":\"Where does it come from?\"}', '{\"title\":\"Where does it come from\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:27:52'),
(7, 3, 'Muhammad Muzammil', 'Content Manager', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 5, 'Updated Meta Tag \"Where does it come from\".', '{\"title\":\"Where does it come from?\"}', '{\"title\":\"Where does it come from\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:27:52'),
(8, 3, 'Muhammad Muzammil', 'Content Manager', 'magazines', 'updated', 'App\\Models\\Magazine', 1, 'Updated Magazine \"Business In Islam\".', '{\"title\":\"Business in Islam\"}', '{\"title\":\"Business In Islam\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:28:21'),
(9, 3, 'Muhammad Muzammil', 'Content Manager', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 3, 'Updated Meta Tag \"Business In Islam\".', '{\"title\":\"Business in Islam\"}', '{\"title\":\"Business In Islam\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:28:21'),
(10, 3, 'Muhammad Muzammil', 'Content Manager', 'authors', 'updated', 'App\\Models\\Author', 2, 'Updated Author \"Nabeel Ahmed\".', '{\"email\":\"nabeel@gamil.com\"}', '{\"email\":\"nabeelahmed@gamil.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 00:28:39'),
(11, 1, 'Super Admin', 'super-admin', 'roles', 'updated', 'Spatie\\Permission\\Models\\Role', 4, 'Updated Admin role \"Content Manager\".', '{\"name\":\"Content Manager\",\"permissions\":[\"admin.access\",\"articles.create\",\"articles.delete\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.delete\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.delete\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.delete\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\",\"tags.create\",\"tags.delete\",\"tags.edit\",\"tags.view\"]}', '{\"name\":\"Content Manager\",\"permissions\":[\"admin.access\",\"articles.create\",\"articles.delete\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.delete\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.delete\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.delete\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\",\"tags.create\",\"tags.delete\",\"tags.edit\",\"tags.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:25:59'),
(12, 1, 'Super Admin', 'super-admin', 'roles', 'permissions_changed', 'Spatie\\Permission\\Models\\Role', 4, 'Changed Content Manager role permissions.', '{\"name\":\"Content Manager\",\"permissions\":[\"admin.access\",\"articles.create\",\"articles.delete\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.delete\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.delete\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.delete\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\",\"tags.create\",\"tags.delete\",\"tags.edit\",\"tags.view\"]}', '{\"name\":\"Content Manager\",\"permissions\":[\"admin.access\",\"articles.create\",\"articles.delete\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.delete\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.delete\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.delete\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\",\"roles.create\",\"roles.delete\",\"roles.edit\",\"roles.view\",\"tags.create\",\"tags.delete\",\"tags.edit\",\"tags.view\",\"users.create\",\"users.delete\",\"users.edit\",\"users.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:36:27'),
(13, 1, 'Super Admin', 'super-admin', 'users', 'permissions_changed', 'App\\Models\\User', 3, 'Changed Admin user \"Muhammad Muzammil\" limited permissions.', '{\"permissions\":[\"articles.create\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\"]}', '{\"permissions\":[\"articles.create\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"authors.create\",\"authors.edit\",\"authors.view\",\"categories.create\",\"categories.edit\",\"categories.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\",\"roles.create\",\"roles.edit\",\"roles.view\",\"users.create\",\"users.edit\",\"users.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:37:55'),
(14, 3, 'Muhammad Muzammil', 'Content Manager', 'roles', 'created', 'Spatie\\Permission\\Models\\Role', 5, 'Created Admin role \"Content Editor\".', NULL, '{\"name\":\"Content Editor\",\"permissions\":[\"admin.access\",\"articles.create\",\"articles.edit\",\"articles.publish\",\"articles.view\",\"change-password.edit\",\"change-password.view\",\"dashboard.view\",\"magazines.create\",\"magazines.edit\",\"magazines.pdf.download\",\"magazines.pdf.view\",\"magazines.publish\",\"magazines.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:39:30'),
(15, 3, 'Muhammad Muzammil', 'Content Manager', 'users', 'created', 'App\\Models\\User', 4, 'Created Admin user \"Hammad Khan\" with role Content Editor.', NULL, '{\"name\":\"Hammad Khan\",\"email\":\"hammadkhan@gmail.com\",\"phone\":\"03111010109\",\"is_active\":true,\"role\":\"Content Editor\",\"permission_mode\":\"role\",\"permissions\":[]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:40:52'),
(16, 4, 'Hammad Khan', 'Content Editor', 'articles', 'created', 'App\\Models\\Article', 2, 'Created Article \"Role Base Testing\".', NULL, '{\"isFree\":\"1\",\"isFeatured\":\"0\",\"isActive\":true,\"status\":\"draft\",\"language\":\"ur\",\"title\":\"Role Base Testing\",\"issue_number\":\"A-20260002\",\"publish_date\":\"2026-08-20 00:00:00\",\"short_description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London\",\"article\":\"<div>\\r\\n<h2>What is Lorem Ipsum?<\\/h2>\\r\\n<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.<\\/p>\\r\\n<\\/div>\\r\\n<div>\\r\\n<h2>Why do we use it?<\\/h2>\\r\\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here,\\u2026\",\"free_until\":\"2026-09-05 00:00:00\",\"image\":null,\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:44:24'),
(17, 4, 'Hammad Khan', 'Content Editor', 'meta_tags', 'created', 'App\\Models\\MetaTag', 6, 'Created Meta Tag \"Role Base Testing\".', NULL, '{\"table_name\":\"articles\",\"table_id\":2,\"language\":\"ur\",\"title\":\"Role Base Testing\",\"keywords\":\"testing\",\"description\":\"role base testing\",\"slug_url\":\"role-base-testing\",\"canonical_url\":null,\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:44:24'),
(18, 4, 'Hammad Khan', 'Content Editor', 'articles', 'published', 'App\\Models\\Article', 2, 'Published Article \"Role Base Testing\".', '{\"status\":\"draft\",\"published_at\":null}', '{\"status\":\"published\",\"published_at\":\"2026-08-28 09:44:37\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:44:37'),
(19, 1, 'Super Admin', 'super-admin', 'users', 'created', 'App\\Models\\User', 5, 'Created Admin user \"Adil Khan\" with role Content Manager.', NULL, '{\"name\":\"Adil Khan\",\"email\":\"adilkhan@gmail.com\",\"phone\":null,\"is_active\":true,\"role\":\"Content Manager\",\"permission_mode\":\"role\",\"permissions\":[]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:03:49'),
(20, 1, 'Super Admin', 'super-admin', 'users', 'ownership_transferred', 'App\\Models\\User', 3, 'Transferred content ownership from \"Muhammad Muzammil\" to \"Adil Khan\".', '{\"source_admin\":{\"id\":3,\"name\":\"Muhammad Muzammil\"},\"content_types\":[\"magazines\",\"articles\"]}', '{\"new_owner\":{\"id\":5,\"name\":\"Adil Khan\"},\"magazines_transferred\":0,\"articles_transferred\":0,\"source_deactivated\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:05:35'),
(21, 3, 'Muhammad Muzammil', 'Content Manager', 'articles', 'created', 'App\\Models\\Article', 3, 'Created Article \"My Work on roles\".', NULL, '{\"isFree\":\"0\",\"isFeatured\":\"1\",\"isActive\":true,\"status\":\"draft\",\"language\":\"ur\",\"title\":\"My Work on roles\",\"issue_number\":\"A-20260003\",\"publish_date\":\"2026-08-29 00:00:00\",\"short_description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley\",\"article\":\"<div>\\r\\n<h2>What is Lorem Ipsum?<\\/h2>\\r\\n<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.<\\/p>\\r\\n<\\/div>\\r\\n<div>\\r\\n<h2>Why do we use it?<\\/h2>\\r\\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here,\\u2026\",\"free_until\":null,\"image\":null,\"owner_admin_id\":3,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:08:49'),
(22, 3, 'Muhammad Muzammil', 'Content Manager', 'meta_tags', 'created', 'App\\Models\\MetaTag', 7, 'Created Meta Tag \"My Work on roles\".', NULL, '{\"table_name\":\"articles\",\"table_id\":3,\"language\":\"ur\",\"title\":\"My Work on roles\",\"keywords\":\"work\",\"description\":\"work\",\"slug_url\":\"my-work-on-roles\",\"canonical_url\":null,\"id\":7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:08:51'),
(23, 3, 'Muhammad Muzammil', 'Content Manager', 'articles', 'published', 'App\\Models\\Article', 3, 'Published Article \"My Work on roles\".', '{\"status\":\"draft\",\"published_at\":null}', '{\"status\":\"published\",\"published_at\":\"2026-08-29 07:09:01\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:09:01'),
(24, 3, 'Muhammad Muzammil', 'Content Manager', 'users', 'created', 'App\\Models\\User', 6, 'Created Admin user \"Usama Khan\" with role Content Editor.', NULL, '{\"name\":\"Usama Khan\",\"email\":\"usamakhan@gmail.com\",\"phone\":\"03319998780\",\"is_active\":true,\"role\":\"Content Editor\",\"permission_mode\":\"role\",\"permissions\":[]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:12:32'),
(25, 6, 'Usama Khan', 'Content Editor', 'articles', 'created', 'App\\Models\\Article', 4, 'Created Article \"Islamic World\".', NULL, '{\"isFree\":\"0\",\"isFeatured\":\"0\",\"isActive\":true,\"status\":\"draft\",\"language\":\"ur\",\"title\":\"Islamic World\",\"issue_number\":\"A-20260004\",\"publish_date\":\"2026-08-29 00:00:00\",\"short_description\":\"It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters,\",\"article\":\"<div>\\r\\n<h2>What is Lorem Ipsum?<\\/h2>\\r\\n<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.<\\/p>\\r\\n<\\/div>\\r\\n<div>\\r\\n<h2>Why do we use it?<\\/h2>\\r\\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here,\\u2026\",\"free_until\":null,\"image\":null,\"owner_admin_id\":6,\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:14:44'),
(26, 6, 'Usama Khan', 'Content Editor', 'meta_tags', 'created', 'App\\Models\\MetaTag', 8, 'Created Meta Tag \"Islamic World\".', NULL, '{\"table_name\":\"articles\",\"table_id\":4,\"language\":\"ur\",\"title\":\"Islamic World\",\"keywords\":\"world\",\"description\":\"world\",\"slug_url\":\"islamic-world\",\"canonical_url\":null,\"id\":8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:14:45'),
(27, 6, 'Usama Khan', 'Content Editor', 'articles', 'published', 'App\\Models\\Article', 4, 'Published Article \"Islamic World\".', '{\"status\":\"draft\",\"published_at\":null}', '{\"status\":\"published\",\"published_at\":\"2026-08-29 07:14:51\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:14:51'),
(28, 3, 'Muhammad Muzammil', 'Content Manager', 'users', 'ownership_transferred', 'App\\Models\\User', 4, 'Transferred content ownership from \"Hammad Khan\" to \"Usama Khan\".', '{\"source_admin\":{\"id\":4,\"name\":\"Hammad Khan\"},\"content_types\":[\"magazines\",\"articles\"]}', '{\"new_owner\":{\"id\":6,\"name\":\"Usama Khan\"},\"magazines_transferred\":0,\"articles_transferred\":1,\"source_deactivated\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:18:00'),
(29, 6, 'Usama Khan', 'Content Editor', 'articles', 'updated', 'App\\Models\\Article', 2, 'Updated Article \"Role Base Testing updated\".', '{\"title\":\"Role Base Testing\"}', '{\"title\":\"Role Base Testing updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:19:40'),
(30, 6, 'Usama Khan', 'Content Editor', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 6, 'Updated Meta Tag \"Role Base Testing updated\".', '{\"title\":\"Role Base Testing\",\"keywords\":\"testing\",\"slug_url\":\"role-base-testing\"}', '{\"title\":\"Role Base Testing updated\",\"keywords\":\"testing role\",\"slug_url\":\"role-base-testing-updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 02:19:40'),
(31, 1, 'Super Admin', 'super-admin', 'users', 'ownership_transferred', 'App\\Models\\User', 3, 'Transferred Admin responsibilities from \"Muhammad Muzammil\" to \"Adil Khan\".', '{\"source_admin\":{\"id\":3,\"name\":\"Muhammad Muzammil\"},\"content_types\":[\"magazines\",\"articles\",\"child_admins\"]}', '{\"new_owner\":{\"id\":5,\"name\":\"Adil Khan\"},\"magazines_transferred\":0,\"articles_transferred\":1,\"child_admins_reassigned\":2,\"source_deactivated\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 04:41:36'),
(32, 1, 'Super Admin', 'super-admin', 'abouts', 'created', 'App\\Models\\About', 1, 'Created About \"Digital Magazine\".', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"section_condition\":\"1\",\"title\":\"Digital Magazine\",\"description\":\"<div>\\r\\n<h2>What is Lorem Ipsum?<\\/h2>\\r\\n<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.<\\/p>\\r\\n<\\/div>\\r\\n<div>\\r\\n<h2>Why do we use it?<\\/h2>\\r\\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here,\\u2026\",\"title_2\":null,\"description_2\":null,\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 06:16:10'),
(33, 1, 'Super Admin', 'super-admin', 'abouts', 'updated', 'App\\Models\\About', 1, 'Updated About \"Digital Magazine\".', '{\"section_condition\":1,\"image\":null}', '{\"section_condition\":\"3\",\"image\":\"images\\/backend-images\\/about\\/01M16KT0X0CFME5KFD6YWHKWDV.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 06:17:19'),
(34, 1, 'Super Admin', 'super-admin', 'abouts', 'updated', 'App\\Models\\About', 1, 'Updated About \"Digital Magazine\".', '{\"image\":\"images\\/backend-images\\/about\\/01M16KT0X0CFME5KFD6YWHKWDV.png\"}', '{\"image\":\"images\\/backend-images\\/about\\/01M16M4QJC7C3XN20CE1MA35KB.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 06:23:10'),
(35, 1, 'Super Admin', 'super-admin', 'home_cards', 'created', 'App\\Models\\HomeCard', 1, 'Created Home Card \"Our Mission\".', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"below slider\",\"title\":\"Our Mission\",\"description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scr\",\"image\":\"images\\/backend-images\\/home-cards\\/01M16PG3P0P72BMAEVEYWN1PQ8.png\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 07:04:20'),
(36, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 1, 'Updated Home Card \"Our Mission\".', '{\"description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scr\"}', '{\"description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 07:07:56'),
(37, 1, 'Super Admin', 'super-admin', 'home_sections', 'created', 'App\\Models\\HomeSection', 1, 'Created Home Section \"Home Section\".', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"below slider\",\"section_condition\":\"3\",\"title\":\"Home Section\",\"description\":\"<div>\\r\\n<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.<\\/p>\\r\\n<\\/div>\\r\\n<div>&nbsp;<\\/div>\",\"title_2\":null,\"description_2\":null,\"image\":\"images\\/backend-images\\/home-sections\\/01M1BC8W3NE7N4N4HYDD207GNZ.png\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-31 02:41:49'),
(38, 1, 'Super Admin', 'super-admin', 'home_sections', 'updated', 'App\\Models\\HomeSection', 1, 'Updated Home Section \"Home Section Updated\".', '{\"title\":\"Home Section\"}', '{\"title\":\"Home Section Updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-31 02:45:11'),
(39, 1, 'Super Admin', 'super-admin', 'general_settings', 'updated', 'App\\Models\\GeneralSetting', 1, 'Updated General Setting #1.', '{\"logo\":\"images\\/backend-images\\/logo\\/logo-cf6dc7cf-6c1d-4e09-ac87-4936534268c7.webp\",\"footer_logo\":\"images\\/backend-images\\/logo\\/footer-logo-59fcfff3-e4d1-4ac8-9c28-6acb696c74d8.webp\"}', '{\"logo\":\"images\\/backend-images\\/logo\\/logo-header.png\",\"footer_logo\":\"images\\/backend-images\\/logo\\/logo-footer.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-09-01 04:57:46'),
(40, 1, 'Super Admin', 'super-admin', 'sliders', 'updated', 'App\\Models\\Slider', 1, 'Updated Slider \"Your Weekly Digital Magazine\".', '{\"content_position\":null,\"image\":\"images\\/backend-images\\/slider\\/slider-78b3cfc3-1b88-41d5-a62a-ae190045c9ed.png\"}', '{\"content_position\":\"top\",\"image\":\"images\\/backend-images\\/slider\\/slider-1.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-09-01 05:52:15'),
(41, 1, 'Super Admin', 'super-admin', 'sliders', 'updated', 'App\\Models\\Slider', 1, 'Updated Slider \"بدلتے شہروں میں زندگی، ثقافت اور نئی نسل کے خواب\".', '{\"top_heading\":\"Digital Magazine\",\"main_heading\":\"Your Weekly Digital Magazine\",\"bottom_text\":\"It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout\",\"button_label\":\"Magazine\"}', '{\"top_heading\":\"\\u0627\\u06c1\\u0645 \\u0645\\u0648\\u0636\\u0648\\u0639\",\"main_heading\":\"\\u0628\\u062f\\u0644\\u062a\\u06d2 \\u0634\\u06c1\\u0631\\u0648\\u06ba \\u0645\\u06cc\\u06ba \\u0632\\u0646\\u062f\\u06af\\u06cc\\u060c \\u062b\\u0642\\u0627\\u0641\\u062a \\u0627\\u0648\\u0631 \\u0646\\u0626\\u06cc \\u0646\\u0633\\u0644 \\u06a9\\u06d2 \\u062e\\u0648\\u0627\\u0628\",\"bottom_text\":\"\\u067e\\u0627\\u06a9\\u0633\\u062a\\u0627\\u0646 \\u06a9\\u06d2 \\u0634\\u06c1\\u0631\\u06cc \\u0645\\u0646\\u0638\\u0631\\u0646\\u0627\\u0645\\u06d2 \\u067e\\u0631 \\u0627\\u06cc\\u06a9 \\u062c\\u0627\\u0645\\u0639 \\u0627\\u0648\\u0631 \\u0641\\u06a9\\u0631 \\u0627\\u0646\\u06af\\u06cc\\u0632 \\u062e\\u0635\\u0648\\u0635\\u06cc \\u0631\\u067e\\u0648\\u0631\\u0679\\u060c \\u062c\\u0648 \\u0622\\u062c \\u0627\\u0648\\u0631 \\u0622\\u0646\\u06d2 \\u0648\\u0627\\u0644\\u06d2 \\u06a9\\u0644                                         \\u06a9\\u06cc \\u0646\\u0626\\u06cc \\u062a\\u0635\\u0648\\u06cc\\u0631 \\u067e\\u06cc\\u0634 \\u06a9\\u0631\\u062a\\u06cc \\u06c1\\u06d2\\u06d4\",\"button_label\":\"\\u0645\\u06a9\\u0645\\u0644 \\u067e\\u0691\\u06be\\u06cc\\u06ba\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-09-01 05:55:05'),
(42, 1, 'Super Admin', 'super-admin', 'sliders', 'created', 'App\\Models\\Slider', 2, 'Created Slider \"کتاب، قاری اور ڈیجیٹل عہد میں مطالعے کی نئی صورتیں\".', NULL, '{\"language\":\"ur\",\"content_position\":\"center\",\"top_heading\":\"\\u0627\\u062f\\u0628 \\u0648 \\u062b\\u0642\\u0627\\u0641\\u062a\",\"main_heading\":\"\\u06a9\\u062a\\u0627\\u0628\\u060c \\u0642\\u0627\\u0631\\u06cc \\u0627\\u0648\\u0631 \\u0688\\u06cc\\u062c\\u06cc\\u0679\\u0644 \\u0639\\u06c1\\u062f \\u0645\\u06cc\\u06ba \\u0645\\u0637\\u0627\\u0644\\u0639\\u06d2 \\u06a9\\u06cc \\u0646\\u0626\\u06cc \\u0635\\u0648\\u0631\\u062a\\u06cc\\u06ba\",\"bottom_text\":\"\\u0645\\u0637\\u0627\\u0644\\u0639\\u06d2 \\u06a9\\u06cc \\u0628\\u062f\\u0644\\u062a\\u06cc \\u0639\\u0627\\u062f\\u0627\\u062a \\u0627\\u0648\\u0631 \\u0627\\u0631\\u062f\\u0648 \\u0627\\u062f\\u0628 \\u06a9\\u06d2 \\u0646\\u0626\\u06d2 \\u0627\\u0645\\u06a9\\u0627\\u0646\\u0627\\u062a \\u06a9\\u0627 \\u0627\\u06cc\\u06a9 \\u062e\\u0635\\u0648\\u0635\\u06cc \\u062c\\u0627\\u0626\\u0632\\u06c1\\u06d4\",\"button_label\":\"\\u0645\\u06a9\\u0645\\u0644 \\u067e\\u0691\\u06be\\u06cc\\u06ba\",\"button_url\":null,\"image\":\"images\\/backend-images\\/slider\\/slider-2.png\",\"isActive\":true,\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-09-01 05:56:29'),
(43, 1, 'Super Admin', 'super-admin', 'sliders', 'created', 'App\\Models\\Slider', 3, 'Created Slider \"مصنوعی ذہانت: مستقبل کے امکانات اور نئے چیلنجز\".', NULL, '{\"language\":\"ur\",\"content_position\":\"bottom\",\"top_heading\":\"\\u0679\\u06cc\\u06a9\\u0646\\u0627\\u0644\\u0648\\u062c\\u06cc\",\"main_heading\":\"\\u0645\\u0635\\u0646\\u0648\\u0639\\u06cc \\u0630\\u06c1\\u0627\\u0646\\u062a: \\u0645\\u0633\\u062a\\u0642\\u0628\\u0644 \\u06a9\\u06d2 \\u0627\\u0645\\u06a9\\u0627\\u0646\\u0627\\u062a \\u0627\\u0648\\u0631 \\u0646\\u0626\\u06d2 \\u0686\\u06cc\\u0644\\u0646\\u062c\\u0632\",\"bottom_text\":\"\\u0679\\u06cc\\u06a9\\u0646\\u0627\\u0644\\u0648\\u062c\\u06cc \\u06a9\\u06cc \\u062a\\u06cc\\u0632 \\u0631\\u0641\\u062a\\u0627\\u0631 \\u062a\\u0628\\u062f\\u06cc\\u0644\\u06cc \\u06c1\\u0645\\u0627\\u0631\\u06cc \\u0631\\u0648\\u0632\\u0645\\u0631\\u06c1 \\u0632\\u0646\\u062f\\u06af\\u06cc \\u0627\\u0648\\u0631 \\u06a9\\u0627\\u0645 \\u06a9\\u06d2 \\u0627\\u0646\\u062f\\u0627\\u0632 \\u06a9\\u0648 \\u06a9\\u06cc\\u0633\\u06d2 \\u0628\\u062f\\u0644 \\u0631\\u06c1\\u06cc \\u06c1\\u06d2\\u061f\",\"button_label\":\"\\u0645\\u06a9\\u0645\\u0644 \\u067e\\u0691\\u06be\\u06cc\\u06ba\",\"button_url\":null,\"image\":\"images\\/backend-images\\/slider\\/slider-3.png\",\"isActive\":true,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-09-01 05:57:49'),
(44, 1, 'Super Admin', 'super-admin', 'general_settings', 'updated', 'App\\Models\\GeneralSetting', 1, 'Updated General Setting #1.', '{\"footer_text\":null,\"app_section_heading\":null,\"app_section_text\":null,\"play_store_icon\":null,\"play_store_link\":null,\"app_store_icon\":null,\"app_store_link\":null}', '{\"footer_text\":\"\\u0645\\u0639\\u06cc\\u0627\\u0631\\u06cc \\u0627\\u0631\\u062f\\u0648 \\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\\u060c \\u062e\\u0635\\u0648\\u0635\\u06cc \\u06af\\u0641\\u062a\\u06af\\u0648\\u060c \\u06c1\\u0641\\u062a\\u06c1 \\u0648\\u0627\\u0631 \\u0645\\u06cc\\u06af\\u0632\\u06cc\\u0646 \\u0627\\u0648\\u0631 \\u0628\\u062f\\u0644\\u062a\\u06cc \\u062f\\u0646\\u06cc\\u0627 \\u06a9\\u06d2 \\u0645\\u0639\\u062a\\u0628\\u0631 \\u062a\\u062c\\u0632\\u06cc\\u0648\\u06ba \\u06a9\\u0627 \\u0688\\u06cc\\u062c\\u06cc\\u0679\\u0644 \\u067e\\u0644\\u06cc\\u0679 \\u0641\\u0627\\u0631\\u0645\\u06d4\",\"app_section_heading\":\"\\u0627\\u06cc\\u067e \\u0688\\u0627\\u0624\\u0646 \\u0644\\u0648\\u0688 \\u06a9\\u0631\\u06cc\\u06ba\",\"app_section_text\":\"\\u06c1\\u0645\\u0627\\u0631\\u06cc \\u0627\\u06cc\\u067e \\u0688\\u0627\\u0624\\u0646 \\u0644\\u0648\\u0688 \\u06a9\\u0631\\u06cc\\u06ba \\u0627\\u0648\\u0631 \\u06a9\\u06c1\\u06cc\\u06ba \\u0628\\u06be\\u06cc\\u060c \\u06a9\\u0628\\u06be\\u06cc \\u0628\\u06be\\u06cc \\u067e\\u0691\\u06be\\u06cc\\u06ba\\u06d4\",\"play_store_icon\":\"images\\/backend-images\\/logo\\/play-logo.png\",\"play_store_link\":\"https:\\/\\/play-store.com\",\"app_store_icon\":\"images\\/backend-images\\/logo\\/app-store-logo.png\",\"app_store_link\":\"https:\\/\\/app-store.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-09-01 06:42:15'),
(45, 1, 'Super Admin', 'super-admin', 'home_cards', 'created', 'App\\Models\\HomeCard', 2, 'Created Home Card \"شرعی فریض\".', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"below slider\",\"title\":\"\\u0634\\u0631\\u0639\\u06cc \\u0641\\u0631\\u06cc\\u0636\",\"description\":\"\\u0627\\u0633\\u0644\\u0627\\u0645 \\u0627\\u06cc\\u06a9 \\u0627\\u06cc\\u0633\\u0627 \\u0645\\u0630\\u06c1\\u0628 \\u06c1\\u06d2 \\u062c\\u0633 \\u0645\\u06cc\\u06ba \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u06d2 \\u06c1\\u0631 \\u0628\\u0627\\u0634\\u0646\\u062f\\u06d2 \\u06a9\\u06cc \\u0636\\u0631\\u0648\\u0631\\u06cc\\u0627\\u062a \\u06a9\\u0627 \\u062e\\u06cc\\u0627\\u0644 \\u0631\\u06a9\\u06be\\u0627 \\u062c\\u0627\\u062a\\u0627 \\u06c1\\u06d2\\u06d4 \\u0627\\u0633\\u0644\\u0627\\u0645 \\u0646\\u06d2 \\u0631\\u0639\\u0627\\u06cc\\u0627 \\u06a9\\u06d2 \\u0628\\u0646\\u06cc\\u0627\\u062f\\u06cc \\u062d\\u0642\\u0648\\u0642 \\u062d\\u06a9\\u0648\\u0645\\u062a\\u0650 \\u0648\\u0642\\u062a \\u06a9\\u06d2 \\u0630\\u0645\\u06c1 \\u0648\\u0627\\u062c\\u0628 \\u06a9\\u06cc\\u06d2 \\u06c1\\u06cc\\u06ba\",\"image\":\"images\\/backend-images\\/home-cards\\/01M1G8A69WH3VFJ873Z4WJCBQQ.webp\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 00:08:50'),
(46, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 1, 'Updated Home Card \"تازہ ترین مضامین\".', '{\"title\":\"Our Mission\",\"description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and\"}', '{\"title\":\"\\u062a\\u0627\\u0632\\u06c1 \\u062a\\u0631\\u06cc\\u0646 \\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\",\"description\":\"\\u0627\\u0633\\u0644\\u0627\\u0645 \\u0627\\u06cc\\u06a9 \\u0627\\u06cc\\u0633\\u0627 \\u0645\\u0630\\u06c1\\u0628 \\u06c1\\u06d2 \\u062c\\u0633 \\u0645\\u06cc\\u06ba \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u06d2 \\u06c1\\u0631 \\u0628\\u0627\\u0634\\u0646\\u062f\\u06d2 \\u06a9\\u06cc \\u0636\\u0631\\u0648\\u0631\\u06cc\\u0627\\u062a \\u06a9\\u0627 \\u062e\\u06cc\\u0627\\u0644 \\u0631\\u06a9\\u06be\\u0627 \\u062c\\u0627\\u062a\\u0627 \\u06c1\\u06d2\\u06d4 \\u0627\\u0633\\u0644\\u0627\\u0645 \\u0646\\u06d2 \\u0631\\u0639\\u0627\\u06cc\\u0627 \\u06a9\\u06d2 \\u0628\\u0646\\u06cc\\u0627\\u062f\\u06cc \\u062d\\u0642\\u0648\\u0642 \\u062d\\u06a9\\u0648\\u0645\\u062a\\u0650 \\u0648\\u0642\\u062a \\u06a9\\u06d2 \\u0630\\u0645\\u06c1 \\u0648\\u0627\\u062c\\u0628 \\u06a9\\u06cc\\u06d2 \\u06c1\\u06cc\\u06ba\\u06d4 \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u0627 \\u06a9\\u0648\\u0626\\u06cc \\u0628\\u06be\\u06cc \\u0641\\u0631\\u062f \\u062d\\u0627\\u06a9\\u0645\\u0650 \\u0648\\u0642\\u062a \\u0633\\u06d2 \\u0627\\u0646 \\u06a9\\u0627 \\u0645\\u0637\\u0627\\u0644\\u0628\\u06c1 \\u06a9\\u0631 \\u0633\\u06a9\\u062a\\u0627 \\u06c1\\u06d2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 00:10:04'),
(47, 1, 'Super Admin', 'super-admin', 'home_cards', 'created', 'App\\Models\\HomeCard', 3, 'Created Home Card \"تازہ ترین مضامین\".', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"below slider\",\"title\":\"\\u062a\\u0627\\u0632\\u06c1 \\u062a\\u0631\\u06cc\\u0646 \\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\",\"description\":\"\\u0627\\u0633\\u0644\\u0627\\u0645 \\u0627\\u06cc\\u06a9 \\u0627\\u06cc\\u0633\\u0627 \\u0645\\u0630\\u06c1\\u0628 \\u06c1\\u06d2 \\u062c\\u0633 \\u0645\\u06cc\\u06ba \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u06d2 \\u06c1\\u0631 \\u0628\\u0627\\u0634\\u0646\\u062f\\u06d2 \\u06a9\\u06cc \\u0636\\u0631\\u0648\\u0631\\u06cc\\u0627\\u062a \\u06a9\\u0627 \\u062e\\u06cc\\u0627\\u0644 \\u0631\\u06a9\\u06be\\u0627 \\u062c\\u0627\\u062a\\u0627 \\u06c1\\u06d2\",\"image\":\"images\\/backend-images\\/home-cards\\/01M1G8E5Q3ZJFVCHKPJPH9VB4P.png\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 00:11:01'),
(48, 1, 'Super Admin', 'super-admin', 'home_cards', 'created', 'App\\Models\\HomeCard', 4, 'Created Home Card \"اغراض و مقاصد\".', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"below slider\",\"title\":\"\\u0627\\u063a\\u0631\\u0627\\u0636 \\u0648 \\u0645\\u0642\\u0627\\u0635\\u062f\",\"description\":\"\\u0634\\u0631\\u06cc\\u0639\\u06c1 \\u0627\\u06cc\\u0646\\u0688 \\u0628\\u0632\\u0646\\u0633 \\u0627\\u06cc\\u06a9 \\u0627\\u06cc\\u0633\\u0627 \\u0645\\u06cc\\u06af\\u0632\\u06cc\\u0646 \\u06c1\\u06d2 \\u062c\\u0633 \\u0645\\u06cc\\u06ba \\u062a\\u0627\\u062c\\u0631\\u0648\\u06ba \\u06a9\\u06cc \\u0634\\u0631\\u0639\\u06cc \\u0631\\u06c1\\u0646\\u0645\\u0627\\u0626\\u06cc \\u06a9\\u06d2 \\u0633\\u0627\\u062a\\u06be \\u0633\\u0627\\u062a\\u06be \\u062a\\u062c\\u0627\\u0631\\u062a\\u06cc \\u0648 \\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0627\\u0653\\u06af\\u06c1\\u06cc \\u0628\\u06be\\u06cc \\u062f\\u06cc \\u062c\\u0627\\u0626\\u06d2 \\u06af\\u06cc\\u06d4 \\u062c\\u0633 \\u0645\\u06cc\\u06ba \\u0645\\u0641\\u062a\\u06cc\\u0627\\u0646 \\u06a9\\u0631\\u0627\\u0645 \\u06a9\\u0648 \\u062a\\u062c\\u0627\\u0631\\u062a\\u06cc \\u0627\\u0635\\u0637\\u0644\\u0627\\u062d\\u0627\\u062a \\u0627\\u0648\\u0631 \\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631 \\u0645\\u0639\\u0627\\u06c1\\u062f\\u0627\\u062a \\u0633\\u06d2 \\u0627\\u0653\\u06af\\u0627\\u06c1 \\u06a9\\u0631\\u0646\\u06d2 \\u06a9\\u06d2 \\u0644\\u06cc\\u06d2 \\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\\u060c \\u0633\\u0631\\u0648\\u06d2 \\u0627\\u0648\\u0631 \\u0641\\u06cc\\u0686\\u0631\\u0632 \\u0634\\u0627\\u0626\\u0639 \\u06a9\\u06cc\\u06d2 \\u062c\\u0627\\u0626\\u06cc\\u06ba \\u06af\\u06d2\\u06d4\",\"image\":\"images\\/backend-images\\/home-cards\\/01M1G8JN1WMCER1CQ8V1AE8H73.webp\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 00:13:27'),
(49, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 3, 'Updated Home Card \"تازہ ترین مضامین\".', '{\"position\":\"below slider\"}', '{\"position\":\"above footer\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 00:17:03'),
(50, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 3, 'Updated Home Card \"تازہ ترین مضامین\".', '{\"position\":\"above footer\"}', '{\"position\":\"below slider\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 00:17:22'),
(51, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 1, 'Updated Home Card \"تازہ ترین مضامین\".', '{\"image\":\"images\\/backend-images\\/home-cards\\/01M16PG3P0P72BMAEVEYWN1PQ8.png\"}', '{\"image\":\"images\\/backend-images\\/home-cards\\/01M1G8Y8CX77EBBNQTYATX5QVX.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 00:19:48'),
(52, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 1, 'Updated Home Card \"تازہ ترین مضامین\".', '{\"image\":\"images\\/backend-images\\/home-cards\\/01M1G8Y8CX77EBBNQTYATX5QVX.png\"}', '{\"image\":\"images\\/backend-images\\/home-cards\\/01M1G9PAA4V5B88WX2S8H6KZ6E.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 00:32:56'),
(53, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 1, 'Updated Home Card #1.', '{\"title\":\"\\u062a\\u0627\\u0632\\u06c1 \\u062a\\u0631\\u06cc\\u0646 \\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\",\"description\":\"\\u0627\\u0633\\u0644\\u0627\\u0645 \\u0627\\u06cc\\u06a9 \\u0627\\u06cc\\u0633\\u0627 \\u0645\\u0630\\u06c1\\u0628 \\u06c1\\u06d2 \\u062c\\u0633 \\u0645\\u06cc\\u06ba \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u06d2 \\u06c1\\u0631 \\u0628\\u0627\\u0634\\u0646\\u062f\\u06d2 \\u06a9\\u06cc \\u0636\\u0631\\u0648\\u0631\\u06cc\\u0627\\u062a \\u06a9\\u0627 \\u062e\\u06cc\\u0627\\u0644 \\u0631\\u06a9\\u06be\\u0627 \\u062c\\u0627\\u062a\\u0627 \\u06c1\\u06d2\\u06d4 \\u0627\\u0633\\u0644\\u0627\\u0645 \\u0646\\u06d2 \\u0631\\u0639\\u0627\\u06cc\\u0627 \\u06a9\\u06d2 \\u0628\\u0646\\u06cc\\u0627\\u062f\\u06cc \\u062d\\u0642\\u0648\\u0642 \\u062d\\u06a9\\u0648\\u0645\\u062a\\u0650 \\u0648\\u0642\\u062a \\u06a9\\u06d2 \\u0630\\u0645\\u06c1 \\u0648\\u0627\\u062c\\u0628 \\u06a9\\u06cc\\u06d2 \\u06c1\\u06cc\\u06ba\\u06d4 \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u0627 \\u06a9\\u0648\\u0626\\u06cc \\u0628\\u06be\\u06cc \\u0641\\u0631\\u062f \\u062d\\u0627\\u06a9\\u0645\\u0650 \\u0648\\u0642\\u062a \\u0633\\u06d2 \\u0627\\u0646 \\u06a9\\u0627 \\u0645\\u0637\\u0627\\u0644\\u0628\\u06c1 \\u06a9\\u0631 \\u0633\\u06a9\\u062a\\u0627 \\u06c1\\u06d2\"}', '{\"title\":null,\"description\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 01:08:44'),
(54, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 1, 'Updated Home Card \"تازہ ترین مضامین\".', '{\"title\":null,\"description\":null}', '{\"title\":\"\\u062a\\u0627\\u0632\\u06c1 \\u062a\\u0631\\u06cc\\u0646 \\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\",\"description\":\"\\u0627\\u0633\\u0644\\u0627\\u0645 \\u0627\\u06cc\\u06a9 \\u0627\\u06cc\\u0633\\u0627 \\u0645\\u0630\\u06c1\\u0628 \\u06c1\\u06d2 \\u062c\\u0633 \\u0645\\u06cc\\u06ba \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u06d2 \\u06c1\\u0631 \\u0628\\u0627\\u0634\\u0646\\u062f\\u06d2 \\u06a9\\u06cc \\u0636\\u0631\\u0648\\u0631\\u06cc\\u0627\\u062a \\u06a9\\u0627 \\u062e\\u06cc\\u0627\\u0644 \\u0631\\u06a9\\u06be\\u0627 \\u062c\\u0627\\u062a\\u0627 \\u06c1\\u06d2\\u06d4 \\u0627\\u0633\\u0644\\u0627\\u0645 \\u0646\\u06d2 \\u0631\\u0639\\u0627\\u06cc\\u0627 \\u06a9\\u06d2 \\u0628\\u0646\\u06cc\\u0627\\u062f\\u06cc \\u062d\\u0642\\u0648\\u0642 \\u062d\\u06a9\\u0648\\u0645\\u062a\\u0650 \\u0648\\u0642\\u062a \\u06a9\\u06d2 \\u0630\\u0645\\u06c1 \\u0648\\u0627\\u062c\\u0628 \\u06a9\\u06cc\\u06d2 \\u06c1\\u06cc\\u06ba\\u06d4 \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u0627 \\u06a9\\u0648\\u0626\\u06cc \\u0628\\u06be\\u06cc \\u0641\\u0631\\u062f \\u062d\\u0627\\u06a9\\u0645\\u0650 \\u0648\\u0642\\u062a \\u0633\\u06d2 \\u0627\\u0646 \\u06a9\\u0627 \\u0645\\u0637\\u0627\\u0644\\u0628\\u06c1 \\u06a9\\u0631 \\u0633\\u06a9\\u062a\\u0627 \\u06c1\\u06d2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 01:10:37'),
(55, 1, 'Super Admin', 'super-admin', 'home_cards', 'created', 'App\\Models\\HomeCard', 5, 'Created Home Card #5.', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"above footer\",\"title\":null,\"description\":null,\"image\":\"images\\/backend-images\\/home-cards\\/01M1GCSAHW25W151R7YKD25778.png\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 01:27:00'),
(56, 1, 'Super Admin', 'super-admin', 'home_cards', 'created', 'App\\Models\\HomeCard', 6, 'Created Home Card #6.', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"above footer\",\"title\":null,\"description\":null,\"image\":\"images\\/backend-images\\/home-cards\\/01M1GCTEYFBX5NATZFMM4PP48A.png\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 01:27:38'),
(57, 1, 'Super Admin', 'super-admin', 'home_cards', 'created', 'App\\Models\\HomeCard', 7, 'Created Home Card #7.', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"above footer\",\"title\":null,\"description\":null,\"image\":\"images\\/backend-images\\/home-cards\\/01M1GCV3TC3FBD002J3R7R96CX.png\",\"id\":7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 01:27:59'),
(58, 1, 'Super Admin', 'super-admin', 'home_cards', 'created', 'App\\Models\\HomeCard', 8, 'Created Home Card #8.', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"position\":\"above footer\",\"title\":null,\"description\":null,\"image\":\"images\\/backend-images\\/home-cards\\/01M1GCX4RAPJT1SVFR92YYZP51.png\",\"id\":8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 01:29:05'),
(59, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 5, 'Updated Home Card \"تازہ ترین مضامین\".', '{\"title\":null}', '{\"title\":\"\\u062a\\u0627\\u0632\\u06c1 \\u062a\\u0631\\u06cc\\u0646 \\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 02:10:16');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_name`, `module`, `action`, `subject_type`, `subject_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(60, 1, 'Super Admin', 'super-admin', 'home_cards', 'updated', 'App\\Models\\HomeCard', 5, 'Updated Home Card \"تازہ ترین مضامین\".', '{\"description\":null}', '{\"description\":\"\\u0627\\u0633\\u0644\\u0627\\u0645 \\u0627\\u06cc\\u06a9 \\u0627\\u06cc\\u0633\\u0627 \\u0645\\u0630\\u06c1\\u0628 \\u06c1\\u06d2 \\u062c\\u0633 \\u0645\\u06cc\\u06ba \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u06d2 \\u06c1\\u0631 \\u0628\\u0627\\u0634\\u0646\\u062f\\u06d2 \\u06a9\\u06cc \\u0636\\u0631\\u0648\\u0631\\u06cc\\u0627\\u062a \\u06a9\\u0627 \\u062e\\u06cc\\u0627\\u0644 \\u0631\\u06a9\\u06be\\u0627 \\u062c\\u0627\\u062a\\u0627 \\u06c1\\u06d2\\u06d4 \\u0627\\u0633\\u0644\\u0627\\u0645 \\u0646\\u06d2 \\u0631\\u0639\\u0627\\u06cc\\u0627 \\u06a9\\u06d2 \\u0628\\u0646\\u06cc\\u0627\\u062f\\u06cc \\u062d\\u0642\\u0648\\u0642 \\u062d\\u06a9\\u0648\\u0645\\u062a\\u0650 \\u0648\\u0642\\u062a \\u06a9\\u06d2 \\u0630\\u0645\\u06c1 \\u0648\\u0627\\u062c\\u0628 \\u06a9\\u06cc\\u06d2 \\u06c1\\u06cc\\u06ba\\u06d4 \\u0631\\u06cc\\u0627\\u0633\\u062a \\u06a9\\u0627 \\u06a9\\u0648\\u0626\\u06cc \\u0628\\u06be\\u06cc \\u0641\\u0631\\u062f \\u062d\\u0627\\u06a9\\u0645\\u0650 \\u0648\\u0642\\u062a \\u0633\\u06d2 \\u0627\\u0646 \\u06a9\\u0627 \\u0645\\u0637\\u0627\\u0644\\u0628\\u06c1 \\u06a9\\u0631 \\u0633\\u06a9\\u062a\\u0627 \\u06c1\\u06d2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 02:18:31'),
(61, 1, 'Super Admin', 'super-admin', 'categories', 'updated', 'App\\Models\\Category', 1, 'Updated Category \"کاروبار\".', '{\"name\":\"Islamic Business\"}', '{\"name\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 04:59:51'),
(62, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 2, 'Updated Meta Tag \"کاروبار\".', '{\"title\":\"Islamic Business\",\"slug_url\":\"islamic-business\"}', '{\"title\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\",\"slug_url\":\"karobar\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 04:59:51'),
(63, 1, 'Super Admin', 'super-admin', 'categories', 'created', 'App\\Models\\Category', 2, 'Created Category \"اسلامی تعلیم\".', NULL, '{\"language\":\"ur\",\"name\":\"\\u0627\\u0633\\u0644\\u0627\\u0645\\u06cc \\u062a\\u0639\\u0644\\u06cc\\u0645\",\"image\":null,\"isActive\":true,\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:02:43'),
(64, 1, 'Super Admin', 'super-admin', 'meta_tags', 'created', 'App\\Models\\MetaTag', 9, 'Created Meta Tag \"اسلامی تعلیم\".', NULL, '{\"table_name\":\"categories\",\"table_id\":2,\"language\":\"ur\",\"title\":\"\\u0627\\u0633\\u0644\\u0627\\u0645\\u06cc \\u062a\\u0639\\u0644\\u06cc\\u0645\",\"keywords\":\"\\u0627\\u0633\\u0644\\u0627\\u0645\\u06cc \\u062a\\u0639\\u0644\\u06cc\\u0645\",\"description\":\"\\u0627\\u0633\\u0644\\u0627\\u0645\\u06cc \\u062a\\u0639\\u0644\\u06cc\\u0645\",\"slug_url\":\"aslamy-taalym\",\"canonical_url\":null,\"id\":9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:02:43'),
(65, 1, 'Super Admin', 'super-admin', 'categories', 'created', 'App\\Models\\Category', 3, 'Created Category \"ٹیکنالوجی\".', NULL, '{\"language\":\"ur\",\"name\":\"\\u0679\\u06cc\\u06a9\\u0646\\u0627\\u0644\\u0648\\u062c\\u06cc\",\"image\":null,\"isActive\":true,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:02:44'),
(66, 1, 'Super Admin', 'super-admin', 'meta_tags', 'created', 'App\\Models\\MetaTag', 10, 'Created Meta Tag \"ٹیکنالوجی\".', NULL, '{\"table_name\":\"categories\",\"table_id\":3,\"language\":\"ur\",\"title\":\"\\u0679\\u06cc\\u06a9\\u0646\\u0627\\u0644\\u0648\\u062c\\u06cc\",\"keywords\":\"\\u0679\\u06cc\\u06a9\\u0646\\u0627\\u0644\\u0648\\u062c\\u06cc\",\"description\":\"\\u0679\\u06cc\\u06a9\\u0646\\u0627\\u0644\\u0648\\u062c\\u06cc\",\"slug_url\":\"yknalogy\",\"canonical_url\":null,\"id\":10}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:02:44'),
(67, 1, 'Super Admin', 'super-admin', 'categories', 'created', 'App\\Models\\Category', 4, 'Created Category \"صحت\".', NULL, '{\"language\":\"ur\",\"name\":\"\\u0635\\u062d\\u062a\",\"image\":null,\"isActive\":true,\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:02:44'),
(68, 1, 'Super Admin', 'super-admin', 'meta_tags', 'created', 'App\\Models\\MetaTag', 11, 'Created Meta Tag \"صحت\".', NULL, '{\"table_name\":\"categories\",\"table_id\":4,\"language\":\"ur\",\"title\":\"\\u0635\\u062d\\u062a\",\"keywords\":\"\\u0635\\u062d\\u062a\",\"description\":\"\\u0635\\u062d\\u062a\",\"slug_url\":\"sht\",\"canonical_url\":null,\"id\":11}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:02:44'),
(69, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 4, 'Updated Article \"Islamic World\".', '{\"image\":null}', '{\"image\":\"images\\/backend-images\\/articles\\/slider-3.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:24:50'),
(70, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 2, 'Updated Article \"Role Base Testing updated\".', '{\"image\":null}', '{\"image\":\"images\\/backend-images\\/articles\\/placeholder.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:25:01'),
(71, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 3, 'Updated Article \"My Work on roles\".', '{\"image\":null}', '{\"image\":\"images\\/backend-images\\/articles\\/slider-2.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:25:31'),
(72, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 4, 'Updated Article \"Islamic World\".', '{\"show_on_latest\":0,\"show_on_editorial_center\":0}', '{\"show_on_latest\":true,\"show_on_editorial_center\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:45:21'),
(73, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 3, 'Updated Article \"My Work on roles\".', '{\"show_on_latest\":0,\"show_on_editorial_center\":0}', '{\"show_on_latest\":true,\"show_on_editorial_center\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:45:34'),
(74, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 1, 'Updated Article \"Where does it come from\".', '{\"show_on_latest\":0,\"show_on_editorial_center\":0,\"show_on_editorial_featured\":0}', '{\"show_on_latest\":true,\"show_on_editorial_center\":true,\"show_on_editorial_featured\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 05:45:56'),
(75, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 1, 'Updated Article \"کاروباری مشکلات کا حل\".', '{\"title\":\"Where does it come from\"}', '{\"title\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u062d\\u0644\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:01:10'),
(76, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 5, 'Updated Meta Tag \"کاروباری مشکلات کا حل\".', '{\"title\":\"Where does it come from\",\"slug_url\":\"where-does-it-come-from\"}', '{\"title\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u062d\\u0644\",\"slug_url\":\"karobary-mshklat-ka-hl\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:01:10'),
(77, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 4, 'Updated Article \"مستقل حل پر توجہ دینا\".', '{\"title\":\"Islamic World\"}', '{\"title\":\"\\u0645\\u0633\\u062a\\u0642\\u0644 \\u062d\\u0644 \\u067e\\u0631 \\u062a\\u0648\\u062c\\u06c1 \\u062f\\u06cc\\u0646\\u0627\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:01:44'),
(78, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 8, 'Updated Meta Tag \"مستقل حل پر توجہ دینا\".', '{\"title\":\"Islamic World\",\"slug_url\":\"islamic-world\"}', '{\"title\":\"\\u0645\\u0633\\u062a\\u0642\\u0644 \\u062d\\u0644 \\u067e\\u0631 \\u062a\\u0648\\u062c\\u06c1 \\u062f\\u06cc\\u0646\\u0627\",\"slug_url\":\"mstkl-hl-pr-tog-dyna\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:01:45'),
(79, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 3, 'Updated Article \"کسی بھی قوم کی ترقی کا دارومدار اس کے نوجوانوں کی تعلیم اور ہنر پر ہوتا ہے۔ وقت آ گیا ہے کہ ہم اپنی ترجیحات درست کریں۔\".', '{\"title\":\"My Work on roles\"}', '{\"title\":\"\\u06a9\\u0633\\u06cc \\u0628\\u06be\\u06cc \\u0642\\u0648\\u0645 \\u06a9\\u06cc \\u062a\\u0631\\u0642\\u06cc \\u06a9\\u0627 \\u062f\\u0627\\u0631\\u0648\\u0645\\u062f\\u0627\\u0631 \\u0627\\u0633 \\u06a9\\u06d2 \\u0646\\u0648\\u062c\\u0648\\u0627\\u0646\\u0648\\u06ba \\u06a9\\u06cc \\u062a\\u0639\\u0644\\u06cc\\u0645 \\u0627\\u0648\\u0631 \\u06c1\\u0646\\u0631 \\u067e\\u0631 \\u06c1\\u0648\\u062a\\u0627 \\u06c1\\u06d2\\u06d4 \\u0648\\u0642\\u062a \\u0622 \\u06af\\u06cc\\u0627 \\u06c1\\u06d2 \\u06a9\\u06c1 \\u06c1\\u0645 \\u0627\\u067e\\u0646\\u06cc \\u062a\\u0631\\u062c\\u06cc\\u062d\\u0627\\u062a \\u062f\\u0631\\u0633\\u062a \\u06a9\\u0631\\u06cc\\u06ba\\u06d4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:02:05'),
(80, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 7, 'Updated Meta Tag \"کسی بھی قوم کی ترقی کا دارومدار اس کے نوجوانوں کی تعلیم اور ہنر پر ہوتا ہے۔ وقت آ گیا ہے کہ ہم اپنی ترجیحات درست کریں۔\".', '{\"title\":\"My Work on roles\",\"slug_url\":\"my-work-on-roles\"}', '{\"title\":\"\\u06a9\\u0633\\u06cc \\u0628\\u06be\\u06cc \\u0642\\u0648\\u0645 \\u06a9\\u06cc \\u062a\\u0631\\u0642\\u06cc \\u06a9\\u0627 \\u062f\\u0627\\u0631\\u0648\\u0645\\u062f\\u0627\\u0631 \\u0627\\u0633 \\u06a9\\u06d2 \\u0646\\u0648\\u062c\\u0648\\u0627\\u0646\\u0648\\u06ba \\u06a9\\u06cc \\u062a\\u0639\\u0644\\u06cc\\u0645 \\u0627\\u0648\\u0631 \\u06c1\\u0646\\u0631 \\u067e\\u0631 \\u06c1\\u0648\\u062a\\u0627 \\u06c1\\u06d2\\u06d4 \\u0648\\u0642\\u062a \\u0622 \\u06af\\u06cc\\u0627 \\u06c1\\u06d2 \\u06a9\\u06c1 \\u06c1\\u0645 \\u0627\\u067e\\u0646\\u06cc \\u062a\\u0631\\u062c\\u06cc\\u062d\\u0627\\u062a \\u062f\\u0631\\u0633\\u062a \\u06a9\\u0631\\u06cc\\u06ba\\u06d4\",\"slug_url\":\"ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:02:05'),
(81, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 2, 'Updated Article \"ہر کاروباری شخص کو اپنی کاروباری زندگی میں وقت بر وقت متعدد اور مختلف نوعیت کی مشکلات کا سامنا کرنا پڑتا ہے،\".', '{\"title\":\"Role Base Testing updated\"}', '{\"title\":\"\\u06c1\\u0631 \\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0634\\u062e\\u0635 \\u06a9\\u0648 \\u0627\\u067e\\u0646\\u06cc \\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0632\\u0646\\u062f\\u06af\\u06cc \\u0645\\u06cc\\u06ba \\u0648\\u0642\\u062a \\u0628\\u0631 \\u0648\\u0642\\u062a \\u0645\\u062a\\u0639\\u062f\\u062f \\u0627\\u0648\\u0631 \\u0645\\u062e\\u062a\\u0644\\u0641 \\u0646\\u0648\\u0639\\u06cc\\u062a \\u06a9\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u0633\\u0627\\u0645\\u0646\\u0627 \\u06a9\\u0631\\u0646\\u0627 \\u067e\\u0691\\u062a\\u0627 \\u06c1\\u06d2\\u060c\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:02:35'),
(82, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 6, 'Updated Meta Tag \"ہر کاروباری شخص کو اپنی کاروباری زندگی میں وقت بر وقت متعدد اور مختلف نوعیت کی مشکلات کا سامنا کرنا پڑتا ہے،\".', '{\"title\":\"Role Base Testing updated\",\"slug_url\":\"role-base-testing-updated\"}', '{\"title\":\"\\u06c1\\u0631 \\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0634\\u062e\\u0635 \\u06a9\\u0648 \\u0627\\u067e\\u0646\\u06cc \\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0632\\u0646\\u062f\\u06af\\u06cc \\u0645\\u06cc\\u06ba \\u0648\\u0642\\u062a \\u0628\\u0631 \\u0648\\u0642\\u062a \\u0645\\u062a\\u0639\\u062f\\u062f \\u0627\\u0648\\u0631 \\u0645\\u062e\\u062a\\u0644\\u0641 \\u0646\\u0648\\u0639\\u06cc\\u062a \\u06a9\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u0633\\u0627\\u0645\\u0646\\u0627 \\u06a9\\u0631\\u0646\\u0627 \\u067e\\u0691\\u062a\\u0627 \\u06c1\\u06d2\\u060c\",\"slug_url\":\"r-karobary-shkhs-ko-apny-karobary-zndgy-my-okt-br-okt-mtaadd-aor-mkhtlf-noaayt-ky-mshklat-ka-samna-krna-pta\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:02:35'),
(83, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 1, 'Updated Article \"کاروباری مشکلات کا حل\".', '{\"show_on_latest\":0,\"show_on_editorial_center\":1}', '{\"show_on_latest\":true,\"show_on_editorial_center\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 06:05:19'),
(84, 1, 'Super Admin', 'super-admin', 'taza_shumara', 'created', 'App\\Models\\TazaShumara', 1, 'Created Taza Shumara #1.', NULL, '{\"show_title\":\"1\",\"show_short_description\":\"1\",\"is_active\":\"1\",\"language\":\"ur\",\"magazine_id\":\"1\",\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903075912-hb6deB78UR3B.png\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 02:59:12'),
(85, 1, 'Super Admin', 'super-admin', 'taza_shumara_articles', 'created', 'App\\Models\\TazaShumaraArticle', 1, 'Created Taza Shumara Article #1.', NULL, '{\"sort_order\":\"0\",\"article_id\":\"3\",\"display_width\":\"full\",\"position\":\"top\",\"taza_shumara_id\":1,\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 02:59:12'),
(86, 1, 'Super Admin', 'super-admin', 'taza_shumara_articles', 'created', 'App\\Models\\TazaShumaraArticle', 2, 'Created Taza Shumara Article #2.', NULL, '{\"sort_order\":\"2\",\"article_id\":\"4\",\"display_width\":\"half\",\"position\":\"center\",\"taza_shumara_id\":1,\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 02:59:12'),
(87, 1, 'Super Admin', 'super-admin', 'taza_shumara_articles', 'created', 'App\\Models\\TazaShumaraArticle', 3, 'Created Taza Shumara Article #3.', NULL, '{\"sort_order\":\"3\",\"article_id\":\"2\",\"display_width\":\"half\",\"position\":\"center\",\"taza_shumara_id\":1,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 02:59:12'),
(88, 1, 'Super Admin', 'super-admin', 'taza_shumara_articles', 'created', 'App\\Models\\TazaShumaraArticle', 4, 'Created Taza Shumara Article #4.', NULL, '{\"sort_order\":\"4\",\"article_id\":\"1\",\"display_width\":\"full\",\"position\":\"bottom\",\"taza_shumara_id\":1,\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 02:59:12'),
(89, 1, 'Super Admin', 'super-admin', 'magazines', 'updated', 'App\\Models\\Magazine', 2, 'Updated Magazine \"مستقل حل پر توجہ دینا\".', '{\"title\":\"Modern Days\"}', '{\"title\":\"\\u0645\\u0633\\u062a\\u0642\\u0644 \\u062d\\u0644 \\u067e\\u0631 \\u062a\\u0648\\u062c\\u06c1 \\u062f\\u06cc\\u0646\\u0627\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:07:06'),
(90, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 4, 'Updated Meta Tag \"مستقل حل پر توجہ دینا\".', '{\"title\":\"Modern Days\",\"slug_url\":\"modern-days\"}', '{\"title\":\"\\u0645\\u0633\\u062a\\u0642\\u0644 \\u062d\\u0644 \\u067e\\u0631 \\u062a\\u0648\\u062c\\u06c1 \\u062f\\u06cc\\u0646\\u0627\",\"slug_url\":\"mstkl-hl-pr-tog-dyna\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:07:06'),
(91, 1, 'Super Admin', 'super-admin', 'magazines', 'updated', 'App\\Models\\Magazine', 1, 'Updated Magazine \"کاروباری مشکلات کا حل\".', '{\"title\":\"Business In Islam\"}', '{\"title\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u062d\\u0644\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:07:22'),
(92, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 3, 'Updated Meta Tag \"کاروباری مشکلات کا حل\".', '{\"title\":\"Business In Islam\",\"slug_url\":\"business-in-islam\"}', '{\"title\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u062d\\u0644\",\"slug_url\":\"karobary-mshklat-ka-hl\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:07:22'),
(93, 1, 'Super Admin', 'super-admin', 'taza_shumara', 'updated', 'App\\Models\\TazaShumara', 1, 'Updated Taza Shumara #1.', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903075912-hb6deB78UR3B.png\"}', '{\"cover_image\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:24:57'),
(94, 1, 'Super Admin', 'super-admin', 'taza_shumara', 'updated', 'App\\Models\\TazaShumara', 1, 'Updated Taza Shumara #1.', '{\"cover_image\":null}', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903102539-XWPuztpGssFF.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:25:39'),
(95, 1, 'Super Admin', 'super-admin', 'taza_shumara', 'updated', 'App\\Models\\TazaShumara', 1, 'Updated Taza Shumara #1.', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903102539-XWPuztpGssFF.png\"}', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903103043-VoOGWAvpm2G8.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:30:43'),
(96, 1, 'Super Admin', 'super-admin', 'taza_shumara', 'updated', 'App\\Models\\TazaShumara', 1, 'Updated Taza Shumara #1.', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903103043-VoOGWAvpm2G8.png\"}', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903103341-F62RPHfpsxdw.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:33:41'),
(97, 1, 'Super Admin', 'super-admin', 'taza_shumara', 'updated', 'App\\Models\\TazaShumara', 1, 'Updated Taza Shumara #1.', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903103341-F62RPHfpsxdw.png\"}', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903103643-zh9QTdEIecwp.jpg\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:36:43'),
(98, 1, 'Super Admin', 'super-admin', 'taza_shumara', 'updated', 'App\\Models\\TazaShumara', 1, 'Updated Taza Shumara #1.', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903103643-zh9QTdEIecwp.jpg\"}', '{\"cover_image\":\"images\\/backend-images\\/taza-shumara\\/20260903104636-AkQgDaXV5736.jpg\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 05:46:36'),
(99, 1, 'Super Admin', 'super-admin', 'banners', 'updated', 'App\\Models\\Banner', 2, 'Updated Banner #2.', '{\"image\":\"images\\/backend-images\\/banner\\/screencapture-localhost-8000-admin-slider-2026-08-19-14-12-54.png\",\"image_2\":\"images\\/backend-images\\/banner\\/screencapture-localhost-8000-admin-2026-08-19-12-02-40.png\"}', '{\"image\":\"images\\/backend-images\\/banner\\/sidddd.jpg\",\"image_2\":\"images\\/backend-images\\/banner\\/siddebyside.jpg\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 06:49:03'),
(100, 1, 'Super Admin', 'super-admin', 'ads', 'created', 'App\\Models\\Ad', 1, 'Created Ad \"Header Ads\".', NULL, '{\"click_count\":0,\"language\":\"ur\",\"title\":\"Header Ads\",\"page_name\":\"header\",\"place\":\"header_ad\",\"ad_url\":\"https:\\/\\/cogentdevs.com\",\"google_ad_code\":null,\"start_date\":null,\"expiry_date\":null,\"isActive\":\"1\",\"ad_image\":\"images\\/backend-images\\/ads\\/header-ads.png\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:41:00'),
(101, 1, 'Super Admin', 'super-admin', 'ads', 'created', 'App\\Models\\Ad', 2, 'Created Ad \"Home page ad for Muzammil\".', NULL, '{\"click_count\":0,\"language\":\"ur\",\"title\":\"Home page ad for Muzammil\",\"page_name\":\"home\",\"place\":\"home_horizontal_large\",\"ad_url\":\"https:\\/\\/ctr-ksa.com\",\"google_ad_code\":null,\"start_date\":\"2026-09-03 00:00:00\",\"expiry_date\":\"2026-09-05 00:00:00\",\"isActive\":\"1\",\"ad_image\":\"images\\/backend-images\\/ads\\/home-ads.png\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:42:06'),
(102, 1, 'Super Admin', 'super-admin', 'ads', 'created', 'App\\Models\\Ad', 3, 'Created Ad \"Taza shumara ad\".', NULL, '{\"click_count\":0,\"language\":\"ur\",\"title\":\"Taza shumara ad\",\"page_name\":\"taza_shumara\",\"place\":\"taza_sidebar_1_normal\",\"ad_url\":\"https:\\/\\/wiindot.com\",\"google_ad_code\":null,\"start_date\":null,\"expiry_date\":null,\"isActive\":\"1\",\"ad_image\":\"images\\/backend-images\\/ads\\/taza-ads.png\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:43:03'),
(103, 1, 'Super Admin', 'super-admin', 'ads', 'updated', 'App\\Models\\Ad', 2, 'Updated Ad \"Home page ad for Muzammil khan\".', '{\"title\":\"Home page ad for Muzammil\"}', '{\"title\":\"Home page ad for Muzammil khan\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:44:07'),
(104, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 1, 'Updated Article \"کاروباری مشکلات کا حل\".', '{\"magazine_id\":null,\"issue_number\":\"A-20260001\"}', '{\"magazine_id\":\"2\",\"issue_number\":\"M-2026002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 01:41:24'),
(105, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 4, 'Updated Article \"مستقل حل پر توجہ دینا\".', '{\"magazine_id\":null,\"issue_number\":\"A-20260004\"}', '{\"magazine_id\":\"1\",\"issue_number\":\"M-2026001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 01:41:28'),
(106, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 3, 'Updated Article \"کسی بھی قوم کی ترقی کا دارومدار اس کے نوجوانوں کی تعلیم اور ہنر پر ہوتا ہے۔ وقت آ گیا ہے کہ ہم اپنی ترجیحات درست کریں۔\".', '{\"magazine_id\":null,\"issue_number\":\"A-20260003\"}', '{\"magazine_id\":\"2\",\"issue_number\":\"M-2026002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 01:41:35'),
(107, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 2, 'Updated Article \"ہر کاروباری شخص کو اپنی کاروباری زندگی میں وقت بر وقت متعدد اور مختلف نوعیت کی مشکلات کا سامنا کرنا پڑتا ہے،\".', '{\"magazine_id\":null,\"issue_number\":\"A-20260002\",\"free_until\":\"2026-09-05\"}', '{\"magazine_id\":\"2\",\"issue_number\":\"M-2026002\",\"free_until\":\"2026-09-12 00:00:00\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 01:42:01'),
(108, 1, 'Super Admin', 'super-admin', 'banners', 'deleted', 'App\\Models\\Banner', 1, 'Deleted Banner #1.', '{\"id\":1,\"language\":\"ur\",\"type\":\"full\",\"position\":\"left\",\"image\":\"images\\/backend-images\\/banner\\/slider.png\",\"image_2\":null,\"isActive\":1}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 07:09:23'),
(109, 1, 'Super Admin', 'super-admin', 'banners', 'created', 'App\\Models\\Banner', 3, 'Created Banner #3.', NULL, '{\"language\":\"ur\",\"type\":\"full\",\"position\":\"category-detail-top-full\",\"image\":\"images\\/backend-images\\/banner\\/cat-det-banner.png\",\"image_2\":null,\"isActive\":true,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 07:31:05'),
(110, 1, 'Super Admin', 'super-admin', 'authors', 'updated', 'App\\Models\\Author', 1, 'Updated Author \"وجاہت شیخ\".', '{\"name\":\"Wajahat Shaikh\",\"qualification\":\"Aalim\",\"speciality\":\"Aqaid\"}', '{\"name\":\"\\u0648\\u062c\\u0627\\u06c1\\u062a \\u0634\\u06cc\\u062e\",\"qualification\":\"\\u0639\\u0627\\u0644\\u0645\",\"speciality\":\"\\u0639\\u0642\\u0627\\u0626\\u062f\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 23:50:16'),
(111, 1, 'Super Admin', 'super-admin', 'authors', 'updated', 'App\\Models\\Author', 2, 'Updated Author \"نبیل احمد\".', '{\"name\":\"Nabeel Ahmed\",\"qualification\":\"Mufti\",\"speciality\":\"Toheed\"}', '{\"name\":\"\\u0646\\u0628\\u06cc\\u0644 \\u0627\\u062d\\u0645\\u062f\",\"qualification\":\"MA In Islam\",\"speciality\":\"\\u0627\\u0633\\u0644\\u0627\\u0645\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 23:51:51'),
(112, 1, 'Super Admin', 'super-admin', 'authors', 'updated', 'App\\Models\\Author', 1, 'Updated Author \"وجاہت شیخ\".', '{\"experience_detail\":\"since 5 year\"}', '{\"experience_detail\":\"\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 01:09:59'),
(113, 1, 'Super Admin', 'super-admin', 'authors', 'updated', 'App\\Models\\Author', 2, 'Updated Author \"نبیل احمد\".', '{\"experience_detail\":null}', '{\"experience_detail\":\"\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 01:10:11'),
(114, 1, 'Super Admin', 'super-admin', 'abouts', 'created', 'App\\Models\\About', 2, 'Created About #2.', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"section_condition\":\"2\",\"title\":null,\"description\":null,\"title_2\":null,\"description_2\":null,\"image\":\"images\\/backend-images\\/about\\/01M207K7QN0EPQNXJX6758M8KM.png\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 05:04:09'),
(115, 1, 'Super Admin', 'super-admin', 'abouts', 'created', 'App\\Models\\About', 3, 'Created About \"کاروباری مشکلات کا حل\".', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"section_condition\":\"1\",\"title\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u062d\\u0644\",\"description\":\"<p>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u2026\",\"title_2\":null,\"description_2\":null,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 05:05:12'),
(116, 1, 'Super Admin', 'super-admin', 'abouts', 'created', 'App\\Models\\About', 4, 'Created About #4.', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"section_condition\":\"6\",\"title\":null,\"description\":null,\"title_2\":null,\"description_2\":null,\"image\":\"images\\/backend-images\\/about\\/01M207Q9X0ZRW98VKMY0TB6GEC.jpg\",\"image_2\":\"images\\/backend-images\\/about\\/01M207QA3S32AD3BVA5QBKJM6D.jpg\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 05:06:22');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_name`, `module`, `action`, `subject_type`, `subject_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(117, 1, 'Super Admin', 'super-admin', 'abouts', 'created', 'App\\Models\\About', 5, 'Created About #5.', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"section_condition\":\"5\",\"title\":null,\"description\":\"<p>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<\\/p>\",\"title_2\":null,\"description_2\":\"<p>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<\\/p>\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 05:07:06'),
(118, 1, 'Super Admin', 'super-admin', 'abouts', 'created', 'App\\Models\\About', 6, 'Created About \"کاروباری مشکلات کا حل\".', NULL, '{\"is_active\":\"1\",\"language\":\"ur\",\"section_condition\":\"4\",\"title\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u062d\\u0644\",\"description\":\"<p>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<\\/p>\",\"title_2\":null,\"description_2\":null,\"image\":\"images\\/backend-images\\/about\\/01M207TRE034H19XVDKRY2KNG6.png\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 05:08:15'),
(119, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'created', 'App\\Models\\SubscriptionProduct', 3, 'Created Subscription Plan \"Quarterly Pack\".', NULL, '{\"name\":\"Quarterly Pack\",\"currency_id\":\"1\",\"price\":\"700\",\"duration_value\":\"3\",\"duration_unit\":\"month\",\"discount_type\":null,\"discount_value\":null,\"product_for\":\"plan\",\"promotion_id\":null,\"isActive\":true,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 02:01:14'),
(120, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'created', 'App\\Models\\SubscriptionProduct', 4, 'Created Subscription Plan \"Yearly Pack\".', NULL, '{\"name\":\"Yearly Pack\",\"currency_id\":\"1\",\"price\":\"2500\",\"duration_value\":\"1\",\"duration_unit\":\"year\",\"discount_type\":null,\"discount_value\":null,\"product_for\":\"plan\",\"promotion_id\":null,\"isActive\":true,\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 02:01:59'),
(121, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'created', 'App\\Models\\SubscriptionProduct', 5, 'Created Subscription Plan \"Monthly Pack\".', NULL, '{\"name\":\"Monthly Pack\",\"currency_id\":\"1\",\"price\":\"200\",\"duration_value\":\"1\",\"duration_unit\":\"month\",\"discount_type\":null,\"discount_value\":null,\"product_for\":\"plan\",\"promotion_id\":null,\"isActive\":true,\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 02:05:07'),
(122, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'created', 'App\\Models\\SubscriptionProduct', 6, 'Created Subscription Plan \"Quarterly Pack\".', NULL, '{\"name\":\"Quarterly Pack\",\"currency_id\":\"1\",\"price\":\"500\",\"duration_value\":\"3\",\"duration_unit\":\"month\",\"discount_type\":null,\"discount_value\":null,\"product_for\":\"plan\",\"promotion_id\":null,\"isActive\":true,\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 02:05:29'),
(123, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'created', 'App\\Models\\SubscriptionProduct', 7, 'Created Subscription Plan \"Yearly Pack\".', NULL, '{\"name\":\"Yearly Pack\",\"currency_id\":\"1\",\"price\":\"2000\",\"duration_value\":\"1\",\"duration_unit\":\"year\",\"discount_type\":null,\"discount_value\":null,\"product_for\":\"plan\",\"promotion_id\":null,\"isActive\":true,\"id\":7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 02:06:14'),
(124, 1, 'Super Admin', 'super-admin', 'memberships', 'updated', 'App\\Models\\SubscriptionProduct', 2, 'Updated Membership \"Quarterly\".', '{\"name\":\"Monthly Pack\",\"price\":\"350.00\",\"duration_value\":1}', '{\"name\":\"Quarterly\",\"price\":\"1000\",\"duration_value\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 02:09:22'),
(125, 1, 'Super Admin', 'super-admin', 'memberships', 'created', 'App\\Models\\SubscriptionProduct', 8, 'Created Membership \"Half Yearly\".', NULL, '{\"name\":\"Half Yearly\",\"currency_id\":\"1\",\"price\":\"2000\",\"duration_value\":\"6\",\"duration_unit\":\"month\",\"discount_type\":null,\"discount_value\":null,\"product_for\":\"membership\",\"promotion_id\":null,\"isActive\":true,\"id\":8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 02:11:46'),
(126, 1, 'Super Admin', 'super-admin', 'memberships', 'created', 'App\\Models\\SubscriptionProduct', 9, 'Created Membership \"Yearly\".', NULL, '{\"name\":\"Yearly\",\"currency_id\":\"1\",\"price\":\"4000\",\"duration_value\":\"1\",\"duration_unit\":\"year\",\"discount_type\":null,\"discount_value\":null,\"product_for\":\"membership\",\"promotion_id\":null,\"isActive\":true,\"id\":9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 02:12:18'),
(127, 1, 'Super Admin', 'super-admin', 'subscription_reminder_settings', 'created', 'App\\Models\\SubscriptionNotificationSetting', 1, 'Created Subscription Reminder Setting #1.', NULL, '{\"isActive\":\"1\",\"id\":1,\"first_reminder_days\":\"5\",\"second_reminder_days\":\"3\",\"third_reminder_days\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-11 05:12:18'),
(128, 1, 'Super Admin', 'super-admin', 'contacts', 'updated', 'App\\Models\\Contact', 1, 'Updated Contact \"Muhammad Muzammil\".', '{\"is_read\":0}', '{\"is_read\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 10:10:38'),
(129, 1, 'Super Admin', 'super-admin', 'info_pages', 'updated', 'App\\Models\\InfoPage', 5, 'Updated Info Page \"دستبرداری\".', '{\"description\":null}', '{\"description\":\"<p>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u2026\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 10:51:09'),
(130, 1, 'Super Admin', 'super-admin', 'info_pages', 'updated', 'App\\Models\\InfoPage', 1, 'Updated Info Page \"رازداری پالیسی\".', '{\"description\":null}', '{\"description\":\"<p>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u2026\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 10:51:18'),
(131, 1, 'Super Admin', 'super-admin', 'info_pages', 'updated', 'App\\Models\\InfoPage', 3, 'Updated Info Page \"شرائط و ضوابط\".', '{\"description\":null}', '{\"description\":\"<p>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u2026\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 10:51:23'),
(132, 1, 'Super Admin', 'super-admin', 'faq_categories', 'created', 'App\\Models\\FaqCategory', 1, 'Created Faq Category \"ڈیجیٹل میگزین\".', NULL, '{\"language\":\"ur\",\"name\":\"\\u0688\\u06cc\\u062c\\u06cc\\u0679\\u0644 \\u0645\\u06cc\\u06af\\u0632\\u06cc\\u0646\",\"icon\":null,\"isActive\":true,\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 11:48:28'),
(133, 1, 'Super Admin', 'super-admin', 'faq_categories', 'updated', 'App\\Models\\FaqCategory', 1, 'Updated Faq Category \"ڈیجیٹل میگزین\".', '{\"icon\":null}', '{\"icon\":\"images\\/backend-images\\/faq-category\\/01M2JEEQNB80MESNA7QC7P2679.png\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 11:50:21'),
(134, 1, 'Super Admin', 'super-admin', 'faqs', 'updated', 'App\\Models\\Faq', 1, 'Updated Faq \"What is Digital Magazine ?\".', '{\"faq_category_id\":null}', '{\"faq_category_id\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 11:50:57'),
(135, 1, 'Super Admin', 'super-admin', 'faqs', 'created', 'App\\Models\\Faq', 2, 'Created Faq \"پریزنٹیشن\".', NULL, '{\"language\":\"ur\",\"faq_category_id\":\"1\",\"question\":\"\\u067e\\u0631\\u06cc\\u0632\\u0646\\u0679\\u06cc\\u0634\\u0646\",\"answer\":\"\\u0644\\u0627\\u0637\\u06cc\\u0646\\u06cc \\u0646\\u0698\\u0627\\u062f \\u0627\\u0648\\u0631 \\u0628\\u06a9\\u0648\\u0627\\u0633 \\u0645\\u0648\\u0627\\u062f \\u0622\\u0631\\u06a9\\u0627\\u0626\\u06cc\\u0648 \\u06a9\\u0627 \\u0641\\u0627\\u0626\\u062f\\u06c1 \\u06af\\u0631\\u0627\\u0641\\u06a9 \\u0688\\u06cc\\u0632\\u0627\\u0626\\u0646 \\u067e\\u0631 \\u0627\\u067e\\u0646\\u06cc \\u062a\\u0648\\u062c\\u06c1 \\u0645\\u0631\\u06a9\\u0648\\u0632 \\u06a9\\u0631 \\u0633\\u06a9\\u062a\\u06d2 \\u06c1\\u06cc\\u06ba \\u0627\\u0648\\u0631 \\u0627\\u0633 \\u0637\\u0631\\u062d \\u0645\\u062a\\u0646 \\u06a9\\u06d2 \\u0645\\u0648\\u0627\\u062f \\u06a9\\u06cc \\u0637\\u0631\\u0641 \\u0633\\u06d2 \\u0645\\u0634\\u063a\\u0648\\u0644 \\u06c1\\u0648\\u0646\\u06d2 \\u0633\\u06d2 \\u0642\\u0627\\u0631\\u06cc \\u06a9\\u0648 \\u0631\\u0648\\u06a9\\u062a\\u0627 \\u06c1\\u06d2 \\u0627\\u0648\\u0631. \\u0628\\u06d2 \\u0634\\u06a9 \\u0679\\u06cc\\u06a9\\u0633\\u0679 Lorem Ipsum \\u0635\\u0631\\u0641 \\u06cc\\u06c1 \\u062d\\u062a\\u0645\\u06cc \\u0645\\u0635\\u0646\\u0648\\u0639\\u0627\\u062a \\u0633\\u06d2 \\u0645\\u06cc\\u0644 \\u06a9\\u06be\\u0627\\u062a\\u0627 \\u06c1\\u06d2 \\u062a\\u0648 \\u0645\\u0627\\u0688\\u0644 \\u06a9\\u0627 \\u0627\\u06cc\\u06a9 \\u0639\\u0627\\u0645 \\u0642\\u0628\\u0636\\u06d2 \\u0627\\u0646\\u06a9\\u0631\\u0646 \\u06a9\\u0631\\u0646\\u06d2 \\u06a9\\u06d2 \\u0644\\u0626\\u06d2 \\u0627\\u0648\\u0631 \\u0645\\u0633\\u062a\\u0642\\u0628\\u0644 \\u062a\\u0628\\u062f\\u06cc\\u0644\\u06cc \\u06a9\\u06cc\\u06d2 \\u0628\\u063a\\u06cc\\u0631 \\u06a9\\u06cc \\u0627\\u0634\\u0627\\u0639\\u062a \\u06cc\\u0642\\u06cc\\u0646\\u06cc \\u0628\\u0646\\u0627\\u0646\\u06d2 \\u06a9\\u06d2 \\u0644\\u0626\\u06d2 \\u0645\\u062a\\u063a\\u06cc\\u0631\",\"isActive\":true,\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 12:02:51'),
(136, 1, 'Super Admin', 'super-admin', 'faq_categories', 'created', 'App\\Models\\FaqCategory', 2, 'Created Faq Category \"مضامین\".', NULL, '{\"language\":\"ur\",\"name\":\"\\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\",\"icon\":\"images\\/backend-images\\/faq-category\\/01M2JFCASS1HA8H6DT3ETKC3AJ.png\",\"isActive\":true,\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 12:06:31'),
(137, 1, 'Super Admin', 'super-admin', 'ad_requests', 'updated', 'App\\Models\\AdRequestPlacement', 1, 'Updated Ad Request #1.', '{\"status\":\"pending\"}', '{\"status\":\"confirmed\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:08:08'),
(138, 1, 'Super Admin', 'super-admin', 'ad_requests', 'updated', 'App\\Models\\AdRequest', 1, 'Updated Ad Request \"AD-000001\".', '{\"status\":\"pending\"}', '{\"status\":\"confirmed\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:08:08'),
(139, 1, 'Super Admin', 'super-admin', 'ads', 'created', 'App\\Models\\Ad', 4, 'Created Ad \"Muzammil\".', NULL, '{\"click_count\":0,\"language\":\"ur\",\"title\":\"Muzammil\",\"page_name\":\"taza_shumara\",\"place\":\"taza_sidebar_1_normal\",\"ad_url\":\"https:\\/\\/cogentdevs.com\",\"google_ad_code\":null,\"start_date\":\"2026-09-23 00:00:00\",\"expiry_date\":\"2026-10-10 00:00:00\",\"isActive\":\"1\",\"ad_image\":\"images\\/backend-images\\/ads\\/contact-image.png\",\"ad_request_id\":1,\"ad_request_placement_id\":1,\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:11:02'),
(140, 1, 'Super Admin', 'super-admin', 'ad_requests', 'updated', 'App\\Models\\AdRequestPlacement', 1, 'Updated Ad Request #1.', '{\"status\":\"confirmed\"}', '{\"status\":\"published\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:11:02'),
(141, 1, 'Super Admin', 'super-admin', 'ad_requests', 'updated', 'App\\Models\\AdRequest', 1, 'Updated Ad Request \"AD-000001\".', '{\"status\":\"confirmed\"}', '{\"status\":\"published\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:11:02'),
(142, 1, 'Super Admin', 'super-admin', 'ad_requests', 'updated', 'App\\Models\\AdRequestPlacement', 2, 'Updated Ad Request #2.', '{\"status\":\"pending\"}', '{\"status\":\"cancelled\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:23:39'),
(143, 1, 'Super Admin', 'super-admin', 'ad_requests', 'updated', 'App\\Models\\AdRequest', 2, 'Updated Ad Request \"AD-000002\".', '{\"status\":\"pending\"}', '{\"status\":\"cancelled\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:23:39');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_name`, `module`, `action`, `subject_type`, `subject_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(144, 1, 'Super Admin', 'super-admin', 'ask_questions', 'updated', 'App\\Models\\AskQuestion', 1, 'Updated Ask Question \"ASK-000001\".', '{\"admin_response\":null,\"status\":\"pending\"}', '{\"admin_response\":\"\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\r\\n\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\",\"status\":\"answered\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 05:34:40'),
(145, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'created', 'App\\Models\\NewsletterCampaign', 1, 'Created Newsletter Campaign \"ہفتہ وار مواد\".', NULL, '{\"status\":\"draft\",\"title\":\"\\u06c1\\u0641\\u062a\\u06c1 \\u0648\\u0627\\u0631 \\u0645\\u0648\\u0627\\u062f\",\"short_description\":\"\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:32:45'),
(146, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 1, 'Updated Newsletter Campaign \"ہفتہ وار مواد\".', '{\"status\":\"draft\",\"brevo_status\":null}', '{\"status\":\"sending\",\"brevo_status\":\"sending\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:36:20'),
(147, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 1, 'Updated Newsletter Campaign \"ہفتہ وار مواد\".', '{\"status\":\"sending\",\"brevo_status\":\"sending\",\"brevo_error\":null}', '{\"status\":\"failed\",\"brevo_status\":\"failed\",\"brevo_error\":\"Newsletter Brevo campaign configuration is incomplete.\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:36:20'),
(148, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 1, 'Updated Newsletter Campaign \"ہفتہ وار مواد\".', '{\"status\":\"failed\",\"brevo_status\":\"failed\",\"brevo_error\":\"Newsletter Brevo campaign configuration is incomplete.\"}', '{\"status\":\"sending\",\"brevo_status\":\"sending\",\"brevo_error\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:36:34'),
(149, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 1, 'Updated Newsletter Campaign \"ہفتہ وار مواد\".', '{\"status\":\"sending\",\"brevo_status\":\"sending\",\"brevo_error\":null}', '{\"status\":\"failed\",\"brevo_status\":\"failed\",\"brevo_error\":\"Newsletter Brevo campaign configuration is incomplete.\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:36:34'),
(150, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 1, 'Updated Newsletter Campaign \"ہفتہ وار مواد\".', '{\"status\":\"failed\",\"brevo_status\":\"failed\",\"brevo_error\":\"Newsletter Brevo campaign configuration is incomplete.\"}', '{\"status\":\"sending\",\"brevo_status\":\"sending\",\"brevo_error\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:38:28'),
(151, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 1, 'Updated Newsletter Campaign \"ہفتہ وار مواد\".', '{\"brevo_campaign_id\":null,\"brevo_status\":\"sending\"}', '{\"brevo_campaign_id\":5,\"brevo_status\":\"created\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:38:29'),
(152, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 1, 'Updated Newsletter Campaign \"ہفتہ وار مواد\".', '{\"status\":\"sending\",\"sent_at\":null,\"brevo_status\":\"created\"}', '{\"status\":\"sent\",\"sent_at\":\"2026-09-23 17:38:29\",\"brevo_status\":\"sent\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:38:29'),
(153, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'created', 'App\\Models\\NewsletterCampaign', 2, 'Created Newsletter Campaign \"ہفتہ وار مضامین\".', NULL, '{\"status\":\"draft\",\"title\":\"\\u06c1\\u0641\\u062a\\u06c1 \\u0648\\u0627\\u0631 \\u0645\\u0636\\u0627\\u0645\\u06cc\\u0646\",\"short_description\":\"\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:31:57'),
(154, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 2, 'Updated Newsletter Campaign \"ہفتہ وار مضامین\".', '{\"status\":\"draft\",\"brevo_status\":null}', '{\"status\":\"sending\",\"brevo_status\":\"sending\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:32:26'),
(155, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 2, 'Updated Newsletter Campaign \"ہفتہ وار مضامین\".', '{\"status\":\"sending\",\"brevo_status\":\"sending\",\"brevo_error\":null}', '{\"status\":\"failed\",\"brevo_status\":\"failed\",\"brevo_error\":\"There are no contacts associated with the given recipients info\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:32:28'),
(156, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 2, 'Updated Newsletter Campaign \"ہفتہ وار مضامین\".', '{\"status\":\"failed\",\"brevo_status\":\"failed\",\"brevo_error\":\"There are no contacts associated with the given recipients info\"}', '{\"status\":\"sending\",\"brevo_status\":\"sending\",\"brevo_error\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:43:51'),
(157, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 2, 'Updated Newsletter Campaign \"ہفتہ وار مضامین\".', '{\"brevo_campaign_id\":null,\"brevo_status\":\"sending\"}', '{\"brevo_campaign_id\":6,\"brevo_status\":\"created\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:43:52'),
(158, 1, 'Super Admin', 'super-admin', 'newsletter_campaigns', 'updated', 'App\\Models\\NewsletterCampaign', 2, 'Updated Newsletter Campaign \"ہفتہ وار مضامین\".', '{\"status\":\"sending\",\"sent_at\":null,\"brevo_status\":\"created\"}', '{\"status\":\"sent\",\"sent_at\":\"2026-09-24 10:43:53\",\"brevo_status\":\"sent\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:43:53'),
(159, 1, 'Super Admin', 'super-admin', 'services', 'created', 'App\\Models\\Service', 1, 'Created Service \"Anxiety Therapy\".', NULL, '{\"isactive\":\"1\",\"name\":\"Anxiety  Therapy\",\"description\":\"Anxiety can affect your sleep, focus, relationships, and everyday life. I\\u2019ll help you understand what\\u2019s behind it, manage anxious thoughts, and feel more in control.\",\"image\":\"backend-images\\/services\\/01M3XQ6NHYZ0GQZNSQ3N9N65DZ.webp\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 07:10:35'),
(160, 1, 'Super Admin', 'super-admin', 'services', 'updated', 'App\\Models\\Service', 1, 'Updated Service \"Anxiety Therapy\".', '{\"description\":\"Anxiety can affect your sleep, focus, relationships, and everyday life. I\\u2019ll help you understand what\\u2019s behind it, manage anxious thoughts, and feel more in control.\"}', '{\"description\":\"Anxiety can affect your sleep, focus, relationships, and everyday life. I\\u2019ll help you understand what\\u2019s behind it, manage anxious thoughts, and feel more in control.updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 07:10:52'),
(161, 1, 'Super Admin', 'super-admin', 'videos', 'created', 'App\\Models\\Video', 1, 'Created Video \"Depression Therapy\".', NULL, '{\"is_free\":\"0\",\"is_active\":\"1\",\"is_share\":\"0\",\"title\":\"Depression Therapy\",\"video_link\":\"https:\\/\\/youtu.be\\/w5dTKDMQLb8?si=Vf3Gx3i1q1A3rSwG\",\"short_description\":\"Feeling exhausted, disconnected, or unlike yourself lately?\",\"thumbnail\":\"backend-images\\/video\\/01M3XSZP6NNRRPJ9NG2G9A8220.webp\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 07:59:12'),
(162, 1, 'Super Admin', 'super-admin', 'videos', 'updated', 'App\\Models\\Video', 1, 'Updated Video \"Depression Therapy\".', '{\"short_description\":\"Feeling exhausted, disconnected, or unlike yourself lately?\"}', '{\"short_description\":\"Feeling exhausted, disconnected, or unlike yourself lately?updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 08:00:00'),
(163, 1, 'Super Admin', 'super-admin', 'payment_accounts', 'created', 'App\\Models\\PaymentAccount', 1, 'Created Payment Account \"Muhammad hamamd khan\".', NULL, '{\"is_active\":true,\"bank_name\":\"Askari Bank\",\"account_title\":\"Muhammad hamamd khan\",\"branch_code\":\"0012\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 12:08:00'),
(164, 1, 'Super Admin', 'super-admin', 'payment_accounts', 'updated', 'App\\Models\\PaymentAccount', 1, 'Updated Payment Account \"Muhammad hamamd khan\".', '{\"bank_name\":\"Askari Bank\"}', '{\"bank_name\":\"Askari Bank updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 12:08:50'),
(165, 14, 'Muhammad Muzammil', 'user', 'user_subscriptions', 'payment_submitted', 'App\\Models\\UserSubscription', 7, 'Submitted subscription payment #7 for review.', NULL, '{\"user_id\":14,\"subscription_product_id\":3,\"payment_status\":\"pending\",\"status\":\"pending\",\"is_active\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 10:37:18'),
(166, 1, 'Super Admin', 'super-admin', 'user_subscriptions', 'payment_approved', 'App\\Models\\UserSubscription', 7, 'Approved subscription payment #7 for user #14.', '{\"user_id\":14,\"payment_status\":\"pending\",\"status\":\"pending\",\"is_active\":false,\"start_date\":null,\"end_date\":null,\"reviewed_by\":null,\"reviewed_at\":null}', '{\"user_id\":14,\"payment_status\":\"approved\",\"status\":\"active\",\"is_active\":true,\"start_date\":\"2026-10-03\",\"end_date\":\"2027-01-03\",\"reviewed_by\":1,\"reviewed_at\":\"2026-10-03 15:41:48\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 10:41:48'),
(167, 1, 'Super Admin', 'super-admin', 'videos', 'updated', 'App\\Models\\Video', 1, 'Updated Video \"Depression Therapy\".', '{\"video_link\":\"https:\\/\\/youtu.be\\/w5dTKDMQLb8?si=Vf3Gx3i1q1A3rSwG\"}', '{\"video_link\":\"https:\\/\\/youtu.be\\/oPWxv5tdltE\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:37:14'),
(168, 1, 'Super Admin', 'super-admin', 'categories', 'deleted', 'App\\Models\\Category', 1, 'Deleted Category \"کاروبار\".', '{\"id\":1,\"language\":\"ur\",\"name\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\",\"image\":\"images\\/backend-images\\/categories\\/752241.png\",\"isActive\":1}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 09:18:22'),
(169, 1, 'Super Admin', 'super-admin', 'categories', 'deleted', 'App\\Models\\Category', 2, 'Deleted Category \"اسلامی تعلیم\".', '{\"id\":2,\"language\":\"ur\",\"name\":\"\\u0627\\u0633\\u0644\\u0627\\u0645\\u06cc \\u062a\\u0639\\u0644\\u06cc\\u0645\",\"image\":null,\"isActive\":1}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 09:18:26'),
(170, 1, 'Super Admin', 'super-admin', 'categories', 'deleted', 'App\\Models\\Category', 4, 'Deleted Category \"صحت\".', '{\"id\":4,\"language\":\"ur\",\"name\":\"\\u0635\\u062d\\u062a\",\"image\":null,\"isActive\":1}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 09:18:30'),
(171, 1, 'Super Admin', 'super-admin', 'categories', 'deleted', 'App\\Models\\Category', 3, 'Deleted Category \"ٹیکنالوجی\".', '{\"id\":3,\"language\":\"ur\",\"name\":\"\\u0679\\u06cc\\u06a9\\u0646\\u0627\\u0644\\u0648\\u062c\\u06cc\",\"image\":null,\"isActive\":1}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 09:18:33'),
(172, 1, 'Super Admin', 'super-admin', 'categories', 'created', 'App\\Models\\Category', 5, 'Created Category \"Technology / Software Development\".', NULL, '{\"language\":\"en\",\"name\":\"Technology \\/ Software Development\",\"image\":\"images\\/backend-images\\/categories\\/screencapture-localhost-8000-mazameen-2026-10-03-17-04-22.png\",\"isActive\":true,\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 09:45:13'),
(173, 1, 'Super Admin', 'super-admin', 'meta_tags', 'created', 'App\\Models\\MetaTag', 12, 'Created Meta Tag \"Technology / Software Development\".', NULL, '{\"table_name\":\"categories\",\"table_id\":5,\"language\":\"en\",\"title\":\"Technology \\/ Software Development\",\"keywords\":null,\"description\":null,\"slug_url\":\"technology-software-development\",\"canonical_url\":null,\"id\":12}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 09:45:13'),
(174, 1, 'Super Admin', 'super-admin', 'categories', 'updated', 'App\\Models\\Category', 5, 'Updated Category \"Technology / Software Development updated\".', '{\"name\":\"Technology \\/ Software Development\",\"image\":\"images\\/backend-images\\/categories\\/screencapture-localhost-8000-mazameen-2026-10-03-17-04-22.png\"}', '{\"name\":\"Technology \\/ Software Development updated\",\"image\":\"images\\/backend-images\\/categories\\/WhatsApp-Image-2026-10-03-at-9-23-28-AM.jpg\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 09:48:19'),
(175, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 12, 'Updated Meta Tag \"Technology / Software Development updated\".', '{\"title\":\"Technology \\/ Software Development\",\"slug_url\":\"technology-software-development\"}', '{\"title\":\"Technology \\/ Software Development updated\",\"slug_url\":\"technology-software-development-updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 09:48:19'),
(176, 1, 'Super Admin', 'super-admin', 'articles', 'created', 'App\\Models\\Article', 5, 'Created Article \"The Future of AI in Mobile App Development: Trends for 2026\".', NULL, '{\"isFree\":\"1\",\"isFeatured\":\"1\",\"show_on_latest\":false,\"show_on_editorial_center\":false,\"show_on_editorial_featured\":false,\"show_visit_counter\":false,\"isActive\":true,\"status\":\"draft\",\"language\":\"en\",\"title\":\"The Future of AI in Mobile App Development: Trends for 2026\",\"publish_date\":\"2026-10-05 00:00:00\",\"short_description\":\"Explore how modern AI models, automated workflows, and smart cross-platform frameworks are transforming mobile software development in 2026.\",\"article\":\"<p data-path-to-node=\\\"2,5,2,0\\\">Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.<\\/p>\\r\\n<p data-path-to-node=\\\"2,5,2,1\\\">Key shifts in 2026 include:<\\/p>\\r\\n<ul data-path-to-node=\\\"2,5,2,2\\\">\\r\\n<li>\\r\\n<p data-path-to-node=\\\"2,5,2,2,0,0\\\">Smart automated code scaffolding<\\/p>\\r\\n<\\/li>\\r\\n<li>\\r\\n<p data-path-to-node=\\\"2,5,2,2,1,0\\\">On-device optimized AI models for cross-platform apps<\\/p>\\r\\n<\\/li>\\r\\n<li>\\r\\n<p data-path-to-node=\\\"2,5,2,2,2,0\\\">Enhanced UI\\/UX design automation<\\/p>\\r\\n<\\/li>\\r\\n<\\/ul>\\r\\n<p id=\\\"p-rc_e49a696261bc725b-46\\\" data-path-to-node=\\\"2,5,2,3\\\"><span data-path-to-node=\\\"2,5,2,3,0\\\">Integrating these technologies not only improves product quality but also reduces turnaround time significantly for production-grade software.<\\/span><span data-path-to-node=\\\"2,5,2,3,1\\\"><!----><!----><!----><!-\\u2026\",\"free_until\":\"2026-10-31 00:00:00\",\"image\":\"images\\/backend-images\\/articles\\/screencapture-localhost-8000-mazameen-2026-10-03-17-04-22.png\",\"owner_admin_id\":1,\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:08:35'),
(177, 1, 'Super Admin', 'super-admin', 'meta_tags', 'created', 'App\\Models\\MetaTag', 13, 'Created Meta Tag \"The Future of AI in Mobile App Development: Trends for 2026\".', NULL, '{\"table_name\":\"articles\",\"table_id\":5,\"language\":\"en\",\"title\":\"The Future of AI in Mobile App Development: Trends for 2026\",\"keywords\":null,\"description\":null,\"slug_url\":\"the-future-of-ai-in-mobile-app-development-trends-for-2026\",\"canonical_url\":null,\"id\":13}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:08:35'),
(178, 1, 'Super Admin', 'super-admin', 'articles', 'deleted', 'App\\Models\\Article', 1, 'Deleted Article \"کاروباری مشکلات کا حل\".', '{\"id\":1,\"language\":\"ur\",\"magazine_id\":2,\"title\":\"\\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u062d\\u0644\",\"issue_number\":\"M-2026002\",\"publish_date\":\"2025-08-01\",\"short_description\":\"\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\",\"image\":\"images\\/backend-images\\/articles\\/article-image.png\",\"article\":\"<p>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4 \\u0631\\u06cc\\u0644 \\u0628\\u0648\\u0644\\u06cc \\u0686\\u06a9\\u06be\\u0627 \\u0686\\u06a9\\u06be\\u060c \\u0646\\u0627\\u0646 \\u067e\\u0627\\u0624 \\u0628\\u0633\\u06a9\\u0679\\u06d4 \\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u0627\\u06af\\u0627\\u060c \\u0686\\u0648\\u0631 \\u0646\\u06a9\\u0644 \\u06a9\\u06d2 \\u0628\\u06be\\u0627\\u06af\\u0627\\u06d4 \\u0633\\u067e\\u0627\\u06c1\\u06cc \\u0628\\u0646 \\u06a9\\u06d2 \\u0622\\u0624\\u06ba \\u06af\\u0627\\u060c \\u0627\\u0686\\u06be\\u0627 \\u06a9\\u06be\\u0627\\u0646\\u0627 \\u06a9\\u06be\\u0627\\u0624\\u06ba \\u06af\\u0627\\u06d4<br>\\u0627\\u06a9\\u0691 \\u0628\\u06a9\\u0691 \\u0628\\u0645\\u0628\\u06d2 \\u0628\\u0648\\u060c \\u0627\\u0633\\u06cc \\u0646\\u0648\\u06d2 \\u067e\\u0648\\u0631\\u06d2 \\u0633\\u0648\\u06d4 \\u0633\\u0648 \\u0645\\u06cc\\u06ba \\u0644\\u06af\\u0627 \\u062f\\u06be\\u2026\",\"isFree\":0,\"free_until\":null,\"isFeatured\":1,\"show_on_latest\":1,\"show_on_editorial_center\":0,\"show_on_editorial_featured\":1,\"show_visit_counter\":1,\"isActive\":1,\"status\":\"published\",\"scheduled_at\":null,\"published_at\":\"2026-08-22 11:25:48\",\"owner_admin_id\":1}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:14:42'),
(179, 1, 'Super Admin', 'super-admin', 'articles', 'deleted', 'App\\Models\\Article', 3, 'Deleted Article \"کسی بھی قوم کی ترقی کا دارومدار اس کے نوجوانوں کی تعلیم اور ہنر پر ہوتا ہے۔ وقت آ گیا ہے کہ ہم اپنی ترجیحات درست کریں۔\".', '{\"id\":3,\"language\":\"ur\",\"magazine_id\":2,\"title\":\"\\u06a9\\u0633\\u06cc \\u0628\\u06be\\u06cc \\u0642\\u0648\\u0645 \\u06a9\\u06cc \\u062a\\u0631\\u0642\\u06cc \\u06a9\\u0627 \\u062f\\u0627\\u0631\\u0648\\u0645\\u062f\\u0627\\u0631 \\u0627\\u0633 \\u06a9\\u06d2 \\u0646\\u0648\\u062c\\u0648\\u0627\\u0646\\u0648\\u06ba \\u06a9\\u06cc \\u062a\\u0639\\u0644\\u06cc\\u0645 \\u0627\\u0648\\u0631 \\u06c1\\u0646\\u0631 \\u067e\\u0631 \\u06c1\\u0648\\u062a\\u0627 \\u06c1\\u06d2\\u06d4 \\u0648\\u0642\\u062a \\u0622 \\u06af\\u06cc\\u0627 \\u06c1\\u06d2 \\u06a9\\u06c1 \\u06c1\\u0645 \\u0627\\u067e\\u0646\\u06cc \\u062a\\u0631\\u062c\\u06cc\\u062d\\u0627\\u062a \\u062f\\u0631\\u0633\\u062a \\u06a9\\u0631\\u06cc\\u06ba\\u06d4\",\"issue_number\":\"M-2026002\",\"publish_date\":\"2026-08-29\",\"short_description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley\",\"image\":\"images\\/backend-images\\/articles\\/slider-2.png\",\"article\":\"<div>\\r\\n<h2>What is Lorem Ipsum?<\\/h2>\\r\\n<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.<\\/p>\\r\\n<\\/div>\\r\\n<div>\\r\\n<h2>Why do we use it?<\\/h2>\\r\\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here,\\u2026\",\"isFree\":1,\"free_until\":null,\"isFeatured\":1,\"show_on_latest\":1,\"show_on_editorial_center\":1,\"show_on_editorial_featured\":0,\"show_visit_counter\":1,\"isActive\":1,\"status\":\"published\",\"scheduled_at\":null,\"published_at\":\"2026-08-29 07:09:01\",\"owner_admin_id\":5}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:14:47'),
(180, 1, 'Super Admin', 'super-admin', 'articles', 'deleted', 'App\\Models\\Article', 4, 'Deleted Article \"مستقل حل پر توجہ دینا\".', '{\"id\":4,\"language\":\"ur\",\"magazine_id\":1,\"title\":\"\\u0645\\u0633\\u062a\\u0642\\u0644 \\u062d\\u0644 \\u067e\\u0631 \\u062a\\u0648\\u062c\\u06c1 \\u062f\\u06cc\\u0646\\u0627\",\"issue_number\":\"M-2026001\",\"publish_date\":\"2026-08-29\",\"short_description\":\"It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters,\",\"image\":\"images\\/backend-images\\/articles\\/slider-3.png\",\"article\":\"<div>\\r\\n<h2>What is Lorem Ipsum?<\\/h2>\\r\\n<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.<\\/p>\\r\\n<\\/div>\\r\\n<div>\\r\\n<h2>Why do we use it?<\\/h2>\\r\\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here,\\u2026\",\"isFree\":0,\"free_until\":null,\"isFeatured\":0,\"show_on_latest\":0,\"show_on_editorial_center\":1,\"show_on_editorial_featured\":0,\"show_visit_counter\":1,\"isActive\":1,\"status\":\"published\",\"scheduled_at\":null,\"published_at\":\"2026-08-29 07:14:51\",\"owner_admin_id\":6}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:14:53'),
(181, 1, 'Super Admin', 'super-admin', 'articles', 'deleted', 'App\\Models\\Article', 2, 'Deleted Article \"ہر کاروباری شخص کو اپنی کاروباری زندگی میں وقت بر وقت متعدد اور مختلف نوعیت کی مشکلات کا سامنا کرنا پڑتا ہے،\".', '{\"id\":2,\"language\":\"ur\",\"magazine_id\":2,\"title\":\"\\u06c1\\u0631 \\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0634\\u062e\\u0635 \\u06a9\\u0648 \\u0627\\u067e\\u0646\\u06cc \\u06a9\\u0627\\u0631\\u0648\\u0628\\u0627\\u0631\\u06cc \\u0632\\u0646\\u062f\\u06af\\u06cc \\u0645\\u06cc\\u06ba \\u0648\\u0642\\u062a \\u0628\\u0631 \\u0648\\u0642\\u062a \\u0645\\u062a\\u0639\\u062f\\u062f \\u0627\\u0648\\u0631 \\u0645\\u062e\\u062a\\u0644\\u0641 \\u0646\\u0648\\u0639\\u06cc\\u062a \\u06a9\\u06cc \\u0645\\u0634\\u06a9\\u0644\\u0627\\u062a \\u06a9\\u0627 \\u0633\\u0627\\u0645\\u0646\\u0627 \\u06a9\\u0631\\u0646\\u0627 \\u067e\\u0691\\u062a\\u0627 \\u06c1\\u06d2\\u060c\",\"issue_number\":\"M-2026002\",\"publish_date\":\"2026-08-20\",\"short_description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London\",\"image\":\"images\\/backend-images\\/articles\\/placeholder.png\",\"article\":\"<div>\\r\\n<h2>What is Lorem Ipsum?<\\/h2>\\r\\n<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.<\\/p>\\r\\n<\\/div>\\r\\n<div>\\r\\n<h2>Why do we use it?<\\/h2>\\r\\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here,\\u2026\",\"isFree\":1,\"free_until\":\"2026-09-12\",\"isFeatured\":0,\"show_on_latest\":1,\"show_on_editorial_center\":1,\"show_on_editorial_featured\":0,\"show_visit_counter\":1,\"isActive\":1,\"status\":\"published\",\"scheduled_at\":null,\"published_at\":\"2026-08-28 09:44:37\",\"owner_admin_id\":6}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:14:57'),
(182, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 5, 'Updated Article \"The Future of AI in Mobile App Development: updated\".', '{\"title\":\"The Future of AI in Mobile App Development: Trends for 2026\",\"short_description\":\"Explore how modern AI models, automated workflows, and smart cross-platform frameworks are transforming mobile software development in 2026.\"}', '{\"title\":\"The Future of AI in Mobile App Development: updated\",\"short_description\":\"Explore how modern AI models, automated workflows, and smart cross-platform frameworks are transforming mobile software development in updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:17:32'),
(183, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 13, 'Updated Meta Tag \"The Future of AI in Mobile App Development: updated\".', '{\"title\":\"The Future of AI in Mobile App Development: Trends for 2026\",\"slug_url\":\"the-future-of-ai-in-mobile-app-development-trends-for-2026\"}', '{\"title\":\"The Future of AI in Mobile App Development: updated\",\"slug_url\":\"the-future-of-ai-in-mobile-app-development-updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:17:32'),
(184, 1, 'Super Admin', 'super-admin', 'categories', 'created', 'App\\Models\\Category', 6, 'Created Category \"category 1\".', NULL, '{\"language\":\"en\",\"name\":\"category 1\",\"image\":\"images\\/backend-images\\/categories\\/image-baa7b2c6-1.jpg\",\"isActive\":true,\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:18:11'),
(185, 1, 'Super Admin', 'super-admin', 'meta_tags', 'created', 'App\\Models\\MetaTag', 14, 'Created Meta Tag \"category 1\".', NULL, '{\"table_name\":\"categories\",\"table_id\":6,\"language\":\"en\",\"title\":\"category 1\",\"keywords\":null,\"description\":null,\"slug_url\":\"category-1\",\"canonical_url\":null,\"id\":14}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:18:11'),
(186, 1, 'Super Admin', 'super-admin', 'currencies', 'updated', 'App\\Models\\Currency', 1, 'Updated Currency \"Rupees updated\".', '{\"name\":\"Rupees\"}', '{\"name\":\"Rupees updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:18:35'),
(187, 1, 'Super Admin', 'super-admin', 'currencies', 'created', 'App\\Models\\Currency', 3, 'Created Currency \"Rupees\".', NULL, '{\"isActive\":true,\"name\":\"Rupees\",\"code\":\"PKRL\",\"symbol\":\"Rs\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:19:04'),
(188, 1, 'Super Admin', 'super-admin', 'currencies', 'deleted', 'App\\Models\\Currency', 3, 'Deleted Currency \"Rupees\".', '{\"id\":3,\"name\":\"Rupees\",\"code\":\"PKRL\",\"symbol\":\"Rs\",\"isActive\":1}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:19:13'),
(189, 1, 'Super Admin', 'super-admin', 'currencies', 'updated', 'App\\Models\\Currency', 1, 'Updated Currency \"Rupees\".', '{\"name\":\"Rupees updated\"}', '{\"name\":\"Rupees\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:19:22'),
(190, 1, 'Super Admin', 'super-admin', 'meta_tags', 'created', 'App\\Models\\MetaTag', 15, 'Created Meta Tag \"Home\".', NULL, '{\"language\":\"en\",\"title\":\"Home\",\"keywords\":\"Technology \\/ Software Development\",\"description\":\"Technology \\/ Software Development\",\"table_name\":null,\"table_id\":null,\"slug_url\":\"\\/\",\"canonical_url\":null,\"id\":15}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:19:55'),
(191, 1, 'Super Admin', 'super-admin', 'videos', 'created', 'App\\Models\\Video', 2, 'Created Video \"Family k sath vlog\".', NULL, '{\"is_free\":\"0\",\"is_active\":\"1\",\"is_share\":\"0\",\"title\":\"Family k sath vlog\",\"video_link\":\"https:\\/\\/www.youtube.com\\/watch?v=yMY6o77cvn8\",\"short_description\":\"First family long drive in new EV car \\ud83d\\ude99 aur bachon ko VIP surprise mil gaya \\ud83d\\ude02\",\"thumbnail\":\"backend-images\\/video\\/01M45SQQ1MMPAJBKZM0D076DJH.jpg\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:28:46'),
(192, 1, 'Super Admin', 'super-admin', 'videos', 'updated', 'App\\Models\\Video', 2, 'Updated Video \"Family k sath vlog\".', '{\"short_description\":\"First family long drive in new EV car \\ud83d\\ude99 aur bachon ko VIP surprise mil gaya \\ud83d\\ude02\"}', '{\"short_description\":\"First family long drive in new EV car \\ud83d\\ude99 aur bachon ko VIP surprise mil gaya \\ud83d\\ude02 updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:29:03'),
(193, 1, 'Super Admin', 'super-admin', 'videos', 'deleted', 'App\\Models\\Video', 2, 'Deleted Video \"Family k sath vlog\".', '{\"id\":2,\"title\":\"Family k sath vlog\",\"thumbnail\":\"backend-images\\/video\\/01M45SQQ1MMPAJBKZM0D076DJH.jpg\",\"video_link\":\"https:\\/\\/www.youtube.com\\/watch?v=yMY6o77cvn8\",\"short_description\":\"First family long drive in new EV car \\ud83d\\ude99 aur bachon ko VIP surprise mil gaya \\ud83d\\ude02 updated\",\"is_free\":0,\"is_active\":1,\"is_share\":0}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:29:10'),
(194, 1, 'Super Admin', 'super-admin', 'meta_tags', 'deleted', 'App\\Models\\MetaTag', 1, 'Deleted Meta Tag \"Home\".', '{\"id\":1,\"table_name\":null,\"table_id\":null,\"language\":\"ur\",\"title\":\"Home\",\"keywords\":\"Digitalmagazine, islamicmagazine\",\"description\":\"Digitalmagazine, islamicmagazine\",\"slug_url\":\"\\/\",\"canonical_url\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:29:51'),
(195, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 15, 'Updated Meta Tag \"Home\".', '{\"keywords\":\"Technology \\/ Software Development\",\"description\":\"Technology \\/ Software Development\"}', '{\"keywords\":\"Technology \\/ Software Development updated\",\"description\":\"Technology \\/ Software Development updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:30:12');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_name`, `module`, `action`, `subject_type`, `subject_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(196, 1, 'Super Admin', 'super-admin', 'meta_tags', 'updated', 'App\\Models\\MetaTag', 14, 'Updated Meta Tag \"category 1\".', '{\"keywords\":null,\"description\":null}', '{\"keywords\":\"Fixed the remaining visible Language leak in SEO \\/ Meta Tags.\\r\\n\\r\\n- Removed the visible Language selector from Meta Tag Create.\\r\\n- Removed the visible readonly \\u201cLanguage: English\\u201d field from Meta Tag Edit.\\r\\n- Rebalanced `Title \\/ Page` to full width in both forms.\\r\\n- Meta Tag create continues to save with server-side fixed language `en`; no browser language value is needed.\\r\\n- Meta Tag edit preserves its internal language identity and only updates SEO fields.\\r\\n- General Settings\\u2019 Default Language selector is now Blade-suppressed as well; stored `default_language_id` is untouched.\\r\\n\\r\\nFiles changed:\\r\\n\\r\\n- `app\\/Http\\/Controllers\\/Admin\\/MetaTagController.php`\\r\\n- `resources\\/views\\/admin\\/meta-tags\\/add-meta-tags.blade.php`\\r\\n- `resources\\/views\\/admin\\/meta-tags\\/edit-meta-tags.blade.php`\\r\\n- `resources\\/views\\/admin\\/general-setting\\/edit-general-setting.blade.php`\\r\\n- `tests\\/Feature\\/AdminMetaTagLanguageVisibilityTest.php`\\r\\n\\r\\nVerification:\\r\\n\\r\\n- Meta Tag Create\\/Edit language visibility and fixed-English save\\u2026\",\"description\":\"Fixed the remaining visible Language leak in SEO \\/ Meta Tags.\\r\\n\\r\\n- Removed the visible Language selector from Meta Tag Create.\\r\\n- Removed the visible readonly \\u201cLanguage: English\\u201d field from Meta Tag Edit.\\r\\n- Rebalanced `Title \\/ Page` to full width in both forms.\\r\\n- Meta Tag create continues to save with server-side fixed language `en`; no browser language value is needed.\\r\\n- Meta Tag edit preserves its internal language identity and only updates SEO fields.\\r\\n- General Settings\\u2019 Default Language selector is now Blade-suppressed as well; stored `default_language_id` is untouched.\\r\\n\\r\\nFiles changed:\\r\\n\\r\\n- `app\\/Http\\/Controllers\\/Admin\\/MetaTagController.php`\\r\\n- `resources\\/views\\/admin\\/meta-tags\\/add-meta-tags.blade.php`\\r\\n- `resources\\/views\\/admin\\/meta-tags\\/edit-meta-tags.blade.php`\\r\\n- `resources\\/views\\/admin\\/general-setting\\/edit-general-setting.blade.php`\\r\\n- `tests\\/Feature\\/AdminMetaTagLanguageVisibilityTest.php`\\r\\n\\r\\nVerification:\\r\\n\\r\\n- Meta Tag Create\\/Edit language visibility and fixed-English save\\u2026\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:30:34'),
(197, 1, 'Super Admin', 'super-admin', 'meta_tags', 'deleted', 'App\\Models\\MetaTag', 14, 'Deleted Meta Tag \"category 1\".', '{\"id\":14,\"table_name\":\"categories\",\"table_id\":6,\"language\":\"en\",\"title\":\"category 1\",\"keywords\":\"Fixed the remaining visible Language leak in SEO \\/ Meta Tags.\\r\\n\\r\\n- Removed the visible Language selector from Meta Tag Create.\\r\\n- Removed the visible readonly \\u201cLanguage: English\\u201d field from Meta Tag Edit.\\r\\n- Rebalanced `Title \\/ Page` to full width in both forms.\\r\\n- Meta Tag create continues to save with server-side fixed language `en`; no browser language value is needed.\\r\\n- Meta Tag edit preserves its internal language identity and only updates SEO fields.\\r\\n- General Settings\\u2019 Default Language selector is now Blade-suppressed as well; stored `default_language_id` is untouched.\\r\\n\\r\\nFiles changed:\\r\\n\\r\\n- `app\\/Http\\/Controllers\\/Admin\\/MetaTagController.php`\\r\\n- `resources\\/views\\/admin\\/meta-tags\\/add-meta-tags.blade.php`\\r\\n- `resources\\/views\\/admin\\/meta-tags\\/edit-meta-tags.blade.php`\\r\\n- `resources\\/views\\/admin\\/general-setting\\/edit-general-setting.blade.php`\\r\\n- `tests\\/Feature\\/AdminMetaTagLanguageVisibilityTest.php`\\r\\n\\r\\nVerification:\\r\\n\\r\\n- Meta Tag Create\\/Edit language visibility and fixed-English save\\u2026\",\"description\":\"Fixed the remaining visible Language leak in SEO \\/ Meta Tags.\\r\\n\\r\\n- Removed the visible Language selector from Meta Tag Create.\\r\\n- Removed the visible readonly \\u201cLanguage: English\\u201d field from Meta Tag Edit.\\r\\n- Rebalanced `Title \\/ Page` to full width in both forms.\\r\\n- Meta Tag create continues to save with server-side fixed language `en`; no browser language value is needed.\\r\\n- Meta Tag edit preserves its internal language identity and only updates SEO fields.\\r\\n- General Settings\\u2019 Default Language selector is now Blade-suppressed as well; stored `default_language_id` is untouched.\\r\\n\\r\\nFiles changed:\\r\\n\\r\\n- `app\\/Http\\/Controllers\\/Admin\\/MetaTagController.php`\\r\\n- `resources\\/views\\/admin\\/meta-tags\\/add-meta-tags.blade.php`\\r\\n- `resources\\/views\\/admin\\/meta-tags\\/edit-meta-tags.blade.php`\\r\\n- `resources\\/views\\/admin\\/general-setting\\/edit-general-setting.blade.php`\\r\\n- `tests\\/Feature\\/AdminMetaTagLanguageVisibilityTest.php`\\r\\n\\r\\nVerification:\\r\\n\\r\\n- Meta Tag Create\\/Edit language visibility and fixed-English save\\u2026\",\"slug_url\":\"category-1\",\"canonical_url\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:30:45'),
(198, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 5, 'Updated Article \"The Future of AI in Mobile App Development: updated\".', '{\"free_until\":\"2026-10-31\"}', '{\"free_until\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:33:32'),
(199, 1, 'Super Admin', 'super-admin', 'articles', 'updated', 'App\\Models\\Article', 5, 'Updated Article \"The Future of AI in Mobile App Development: updated\".', '{\"isFeatured\":1}', '{\"isFeatured\":\"0\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:34:44'),
(200, 1, 'Super Admin', 'super-admin', 'articles', 'created', 'App\\Models\\Article', 6, 'Created Article \"The Future of AI in Mobile App Development\".', NULL, '{\"isFree\":\"0\",\"isFeatured\":\"0\",\"show_on_latest\":false,\"show_on_editorial_center\":false,\"show_on_editorial_featured\":false,\"show_visit_counter\":false,\"isActive\":true,\"status\":\"draft\",\"language\":\"en\",\"title\":\"The Future of AI in Mobile App Development\",\"publish_date\":null,\"short_description\":\"Explore how modern AI models, automated workflows\",\"article\":\"<p data-path-to-node=\\\"2,5,2,0\\\">Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.<\\/p>\\r\\n<p data-path-to-node=\\\"2,5,2,1\\\">Key shifts in 2026 include:<\\/p>\\r\\n<ul data-path-to-node=\\\"2,5,2,2\\\">\\r\\n<li>\\r\\n<p data-path-to-node=\\\"2,5,2,2,0,0\\\">Smart automated code scaffolding<\\/p>\\r\\n<\\/li>\\r\\n<li>\\r\\n<p data-path-to-node=\\\"2,5,2,2,1,0\\\">On-device optimized AI models for cross-platform apps<\\/p>\\r\\n<\\/li>\\r\\n<li>\\r\\n<p data-path-to-node=\\\"2,5,2,2,2,0\\\">Enhanced UI\\/UX design automation<\\/p>\\r\\n<\\/li>\\r\\n<\\/ul>\\r\\n<p id=\\\"p-rc_e49a696261bc725b-46\\\" data-path-to-node=\\\"2,5,2,3\\\"><span data-path-to-node=\\\"2,5,2,3,0\\\">Integrating these technologies not only improves product quality but also reduces turnaround time significantly for production-grade software.<\\/span><\\/p>\",\"free_until\":null,\"image\":\"images\\/backend-images\\/articles\\/Gemini-Generated-Image-qxnb84qxnb84qxnb.png\",\"owner_admin_id\":1,\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:36:27'),
(201, 1, 'Super Admin', 'super-admin', 'meta_tags', 'created', 'App\\Models\\MetaTag', 16, 'Created Meta Tag \"The Future of AI in Mobile App Development\".', NULL, '{\"table_name\":\"articles\",\"table_id\":6,\"language\":\"en\",\"title\":\"The Future of AI in Mobile App Development\",\"keywords\":null,\"description\":null,\"slug_url\":\"the-future-of-ai-in-mobile-app-development\",\"canonical_url\":null,\"id\":16}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:36:27'),
(202, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'created', 'App\\Models\\SubscriptionProduct', 10, 'Created Subscription Plan \"Yearly\".', NULL, '{\"name\":\"Yearly\",\"currency_id\":\"1\",\"price\":\"1200\",\"duration_value\":\"1\",\"duration_unit\":\"year\",\"discount_type\":\"fixed\",\"discount_value\":\"250\",\"product_for\":\"plan\",\"promotion_id\":null,\"isActive\":true,\"id\":10}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:44:26'),
(203, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'deleted', 'App\\Models\\SubscriptionProduct', 10, 'Deleted Subscription Plan \"Yearly\".', '{\"id\":10,\"product_for\":\"plan\",\"name\":\"Yearly\",\"currency_id\":1,\"price\":\"1200.00\",\"duration_value\":1,\"duration_unit\":\"year\",\"discount_type\":\"fixed\",\"discount_value\":\"250.00\",\"promotion_id\":null,\"isActive\":1}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:50:08'),
(204, 1, 'Super Admin', 'super-admin', 'videos', 'created', 'App\\Models\\Video', 3, 'Created Video \"RS 5000 SURVIVAL CHALLENGE\".', NULL, '{\"is_free\":\"0\",\"is_active\":\"1\",\"is_share\":\"0\",\"title\":\"RS 5000 SURVIVAL CHALLENGE\",\"video_link\":\"https:\\/\\/www.youtube.com\\/watch?v=4ySwXseEP3M\",\"short_description\":\"Thanks for watching and feel free to leave a comment, suggestion or critique in the comments below.\\r\\nMake sure to SUBSCRIBE, it\\u2019s the best way to keep my videos in your feed and give me the thumbs up if you liked this video.\\r\\nTHANKS..\",\"thumbnail\":\"backend-images\\/video\\/01M45V9038MJG71P7PNN61FAAC.jpg\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:55:41'),
(205, 1, 'Super Admin', 'super-admin', 'videos', 'updated', 'App\\Models\\Video', 3, 'Updated Video \"RS 5000 SURVIVAL CHALLENGE\".', '{\"short_description\":\"Thanks for watching and feel free to leave a comment, suggestion or critique in the comments below.\\r\\nMake sure to SUBSCRIBE, it\\u2019s the best way to keep my videos in your feed and give me the thumbs up if you liked this video.\\r\\nTHANKS..\"}', '{\"short_description\":\"Thanks for watching and feel free to leave a comment, suggestion or critique in the comments below.\\r\\nMake sure to SUBSCRIBE, it\\u2019s the best way to keep my videos in your feed and give me the thumbs up if you liked this video.\\r\\nTHANKS..updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:55:55'),
(206, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'created', 'App\\Models\\SubscriptionProduct', 11, 'Created Subscription Plan \"Yearly Pack\".', NULL, '{\"name\":\"Yearly Pack\",\"currency_id\":\"1\",\"price\":\"2000\",\"duration_value\":\"1\",\"duration_unit\":\"year\",\"discount_type\":null,\"discount_value\":null,\"product_for\":\"plan\",\"promotion_id\":null,\"isActive\":true,\"id\":11}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:56:57'),
(207, 1, 'Super Admin', 'super-admin', 'subscription_plans', 'updated', 'App\\Models\\SubscriptionProduct', 11, 'Updated Subscription Plan \"Yearly Pack updated\".', '{\"name\":\"Yearly Pack\"}', '{\"name\":\"Yearly Pack updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:57:17'),
(208, 1, 'Super Admin', 'super-admin', 'authors', 'created', 'App\\Models\\Author', 3, 'Created Author \"M Hammad khan\".', NULL, '{\"picture\":\"images\\/backend-images\\/author\\/WhatsApp-Image-2026-03-27-at-2-38-47-AM.jpg\",\"name\":\"M Hammad khan\",\"contact_number\":\"03172930029\",\"email\":\"hammad2930029@gmail.com\",\"qualification\":\"Intermediate\",\"experience_detail\":null,\"experience_years\":null,\"speciality\":null,\"isActive\":true,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 10:59:17'),
(209, 1, 'Super Admin', 'super-admin', 'roles', 'created', 'Spatie\\Permission\\Models\\Role', 6, 'Created Admin role \"APP_ORIGIN\".', NULL, '{\"name\":\"APP_ORIGIN\",\"permissions\":[\"admin.access\",\"banners.view\",\"change-password.view\",\"dashboard.view\",\"general-settings.view\",\"sliders.view\",\"home-article.view\",\"home-headings.view\",\"site-analytics.view\",\"contacts.view\",\"newsletter-campaigns.view\",\"newsletter-subscribers.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:00:54'),
(210, 1, 'Super Admin', 'super-admin', 'roles', 'updated', 'Spatie\\Permission\\Models\\Role', 6, 'Updated Admin role \"App Name\".', '{\"name\":\"APP_ORIGIN\",\"permissions\":[\"admin.access\",\"banners.view\",\"change-password.view\",\"contacts.view\",\"dashboard.view\",\"general-settings.view\",\"home-article.view\",\"home-headings.view\",\"newsletter-campaigns.view\",\"newsletter-subscribers.view\",\"site-analytics.view\",\"sliders.view\"]}', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.view\",\"change-password.view\",\"contacts.view\",\"dashboard.view\",\"general-settings.view\",\"home-article.view\",\"home-headings.view\",\"newsletter-campaigns.view\",\"newsletter-subscribers.view\",\"site-analytics.view\",\"sliders.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:01:19'),
(211, 1, 'Super Admin', 'super-admin', 'articles', 'published', 'App\\Models\\Article', 6, 'Published Article \"The Future of AI in Mobile App Development\".', '{\"status\":\"draft\",\"published_at\":null}', '{\"status\":\"published\",\"published_at\":\"2026-10-05 16:03:22\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:03:22'),
(212, 1, 'Super Admin', 'super-admin', 'payment_accounts', 'created', 'App\\Models\\PaymentAccount', 2, 'Created Payment Account \"Muhammad hamamd\".', NULL, '{\"is_active\":true,\"bank_name\":\"Askari Bank\",\"account_title\":\"Muhammad hamamd\",\"branch_code\":\"012\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:06:50'),
(213, 1, 'Super Admin', 'super-admin', 'payment_accounts', 'updated', 'App\\Models\\PaymentAccount', 2, 'Updated Payment Account \"Muhammad hamamd updated\".', '{\"account_title\":\"Muhammad hamamd\"}', '{\"account_title\":\"Muhammad hamamd updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:07:02'),
(214, 1, 'Super Admin', 'super-admin', 'subscription_reminder_settings', 'updated', 'App\\Models\\SubscriptionNotificationSetting', 1, 'Updated Subscription Reminder Setting #1.', '{\"first_reminder_days\":5}', '{\"first_reminder_days\":\"6\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:07:32'),
(215, 1, 'Super Admin', 'super-admin', 'users', 'created', 'App\\Models\\User', 17, 'Created Admin user \"hammad khan\" with role App Name.', NULL, '{\"name\":\"hammad khan\",\"email\":\"hammad2930029@gmail.com\",\"phone\":\"03172930029\",\"is_active\":true,\"role\":\"App Name\",\"permission_mode\":\"role\",\"permissions\":[]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:12:05'),
(216, 1, 'Super Admin', 'super-admin', 'roles', 'permissions_changed', 'Spatie\\Permission\\Models\\Role', 6, 'Changed App Name role permissions.', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.view\",\"change-password.view\",\"contacts.view\",\"dashboard.view\",\"general-settings.view\",\"home-article.view\",\"home-headings.view\",\"newsletter-campaigns.view\",\"newsletter-subscribers.view\",\"site-analytics.view\",\"sliders.view\"]}', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:15:23'),
(217, 1, 'Super Admin', 'super-admin', 'users', 'permission_mode_changed', 'App\\Models\\User', 17, 'Changed Admin user \"hammad khan\" permission mode.', '{\"permission_mode\":\"role\"}', '{\"permission_mode\":\"custom\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:19:13'),
(218, 1, 'Super Admin', 'super-admin', 'users', 'permissions_changed', 'App\\Models\\User', 17, 'Changed Admin user \"hammad khan\" limited permissions.', '{\"permissions\":[]}', '{\"permissions\":[\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:19:13'),
(219, 1, 'Super Admin', 'super-admin', 'roles', 'updated', 'Spatie\\Permission\\Models\\Role', 6, 'Updated Admin role \"App Name\".', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:20:13'),
(220, 1, 'Super Admin', 'super-admin', 'users', 'permission_mode_changed', 'App\\Models\\User', 17, 'Changed Admin user \"hammad khan\" permission mode.', '{\"permission_mode\":\"custom\"}', '{\"permission_mode\":\"role\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:21:37'),
(221, 1, 'Super Admin', 'super-admin', 'users', 'permissions_changed', 'App\\Models\\User', 17, 'Changed Admin user \"hammad khan\" limited permissions.', '{\"permissions\":[\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '{\"permissions\":[]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:21:37'),
(222, 1, 'Super Admin', 'super-admin', 'users', 'password_changed', 'App\\Models\\User', 17, 'Changed Admin user \"hammad khan\" password.', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:21:37'),
(223, 1, 'Super Admin', 'super-admin', 'roles', 'updated', 'Spatie\\Permission\\Models\\Role', 6, 'Updated Admin role \"App Name\".', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:27:55'),
(224, 1, 'Super Admin', 'super-admin', 'roles', 'permissions_changed', 'Spatie\\Permission\\Models\\Role', 6, 'Changed App Name role permissions.', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '{\"name\":\"App Name\",\"permissions\":[\"admin.access\",\"banners.create\",\"banners.delete\",\"banners.edit\",\"banners.view\",\"dashboard.view\",\"home-article.edit\",\"home-article.view\",\"home-cards.create\",\"home-cards.delete\",\"home-cards.edit\",\"home-cards.view\",\"home-headings.edit\",\"home-headings.view\",\"home-sections.create\",\"home-sections.delete\",\"home-sections.edit\",\"home-sections.view\",\"services.create\",\"services.delete\",\"services.edit\",\"services.view\",\"sliders.create\",\"sliders.delete\",\"sliders.edit\",\"sliders.view\",\"taza-shumara.create\",\"taza-shumara.delete\",\"taza-shumara.edit\",\"taza-shumara.view\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:29:54'),
(225, 17, 'hammad khan', 'App Name', 'sliders', 'created', 'App\\Models\\Slider', 4, 'Created Slider \"Demo\".', NULL, '{\"language\":\"en\",\"content_position\":\"top\",\"top_heading\":\"Demo\",\"main_heading\":\"Demo\",\"bottom_text\":\"demo headings testing\",\"button_label\":\"arrow\",\"button_url\":null,\"image\":\"images\\/backend-images\\/slider\\/App-banner.png\",\"isActive\":true,\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:34:31'),
(226, 17, 'hammad khan', 'App Name', 'home_cards', 'created', 'App\\Models\\HomeCard', 9, 'Created Home Card \"demo\".', NULL, '{\"is_active\":\"1\",\"language\":\"en\",\"position\":\"below slider\",\"title\":\"demo\",\"description\":\"demo testing\",\"image\":\"images\\/backend-images\\/home-cards\\/01M45XHT3GAWX0BWM1S5DGHN24.jpg\",\"id\":9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:35:27'),
(227, 1, 'Super Admin', 'super-admin', 'videos', 'created', 'App\\Models\\Video', 4, 'Created Video \"demo\".', NULL, '{\"is_free\":\"0\",\"is_active\":\"1\",\"is_share\":\"0\",\"status\":\"draft\",\"title\":\"demo\",\"video_link\":\"https:\\/\\/youtu.be\\/w5dTKDMQLb8?si=Vf3Gx3i1q1A3rSwG\",\"short_description\":\"demo\",\"thumbnail\":\"backend-images\\/video\\/01M47SP7DFK38QNKVS843ECQGA.png\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 05:06:26'),
(228, 1, 'Super Admin', 'super-admin', 'videos', 'published', 'App\\Models\\Video', 4, 'Published Video \"demo\".', '{\"status\":\"draft\",\"published_at\":null}', '{\"status\":\"published\",\"published_at\":\"2026-10-06 10:06:39\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 05:06:39'),
(229, 1, 'Super Admin', 'super-admin', 'videos', 'created', 'App\\Models\\Video', 5, 'Created Video \"demo\".', NULL, '{\"is_free\":\"0\",\"is_active\":\"1\",\"is_share\":\"0\",\"status\":\"draft\",\"title\":\"demo\",\"video_link\":\"https:\\/\\/www.youtube.com\\/watch?v=yMY6o77cvn8\",\"short_description\":\"demo2\",\"thumbnail\":\"backend-images\\/video\\/01M47TD7RCXW4B8WDX1W89D08N.jpg\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 05:19:00'),
(230, 14, 'Muhammad Muzammil', 'user', 'user_subscriptions', 'payment_submitted', 'App\\Models\\UserSubscription', 8, 'Submitted subscription payment #8 for review.', NULL, '{\"user_id\":14,\"subscription_product_id\":11,\"payment_status\":\"pending\",\"status\":\"pending\",\"is_active\":false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 06:07:02'),
(231, 1, 'Super Admin', 'super-admin', 'user_subscriptions', 'payment_approved', 'App\\Models\\UserSubscription', 8, 'Approved subscription payment #8 for user #14.', '{\"user_id\":14,\"payment_status\":\"pending\",\"status\":\"pending\",\"is_active\":false,\"start_date\":null,\"end_date\":null,\"reviewed_by\":null,\"reviewed_at\":null}', '{\"user_id\":14,\"payment_status\":\"approved\",\"status\":\"active\",\"is_active\":true,\"start_date\":\"2026-10-06\",\"end_date\":\"2027-10-06\",\"reviewed_by\":1,\"reviewed_at\":\"2026-10-06 11:09:48\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 06:09:48'),
(232, 1, 'Super Admin', 'super-admin', 'consultancies', 'created', 'App\\Models\\Consultancy', 1, 'Created Consultancy \"demo\".', NULL, '{\"isActive\":true,\"isFeatured\":true,\"status\":\"draft\",\"title\":\"demo\",\"button_label\":\"View Details\",\"short_description\":\"If you need the postal code for a specific block, street, or nearby neighborhood in Karachi, please let me know!\",\"description\":\"<p>If you need the postal code for a <strong class=\\\"rQesXe MPyX\\\" data-sfc-cp=\\\"\\\" data-sfc-root=\\\"ep\\\" data-epip=\\\"\\\">specific block, street, or nearby neighborhood<!--TgQPHd|||[]--><\\/strong> in Karachi, please let me know!<\\/p>\\r\\n<p>If you need the postal code for a <strong class=\\\"rQesXe MPyX\\\" data-sfc-cp=\\\"\\\" data-sfc-root=\\\"ep\\\" data-epip=\\\"\\\">specific block, street, or nearby neighborhood<!--TgQPHd|||[]--><\\/strong> in Karachi, please let me know!<\\/p>\\r\\n<p>If you need the postal code for a <strong class=\\\"rQesXe MPyX\\\" data-sfc-cp=\\\"\\\" data-sfc-root=\\\"ep\\\" data-epip=\\\"\\\">specific block, street, or nearby neighborhood<!--TgQPHd|||[]--><\\/strong> in Karachi, please let me know!<\\/p>\",\"duration_type\":\"hours\",\"duration_value\":\"2\",\"consultancy_medium\":\"zoom\",\"language\":\"en\",\"image\":\"backend-images\\/consultancy\\/01M48139H8EES5EMRFQQ41TKTE.png\",\"published_at\":null,\"owner_admin_id\":1,\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 07:15:54'),
(233, 1, 'Super Admin', 'super-admin', 'consultancies', 'updated', 'App\\Models\\Consultancy', 1, 'Updated Consultancy \"demo updated\".', '{\"title\":\"demo\"}', '{\"title\":\"demo updated\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 07:16:36'),
(234, 1, 'Super Admin', 'super-admin', 'consultancies', 'published', 'App\\Models\\Consultancy', 1, 'Published Consultancy \"demo updated\".', '{\"status\":\"draft\",\"published_at\":null}', '{\"status\":\"published\",\"published_at\":\"2026-10-06 12:17:04\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 07:17:04'),
(235, 1, 'Super Admin', 'super-admin', 'consultancies', 'created', 'App\\Models\\Consultancy', 2, 'Created Consultancy \"Demo 2\".', NULL, '{\"isActive\":true,\"isFeatured\":true,\"status\":\"draft\",\"title\":\"Demo 2\",\"button_label\":\"View Details\",\"short_description\":\"Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace.\",\"description\":\"<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.<\\/p>\\r\\n<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.<\\/p>\\r\\n<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.<\\/p>\",\"duration_type\":\"days\",\"duration_value\":\"5\",\"consultancy_medium\":\"video_call\",\"language\":\"en\",\"image\":\"backend-images\\/consultancy\\/01M481CP7VEM0Q9RYGT5M5CN4B.png\",\"published_at\":null,\"owner_admin_id\":1,\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 07:21:02'),
(236, 1, 'Super Admin', 'super-admin', 'consultancies', 'created', 'App\\Models\\Consultancy', 3, 'Created Consultancy \"New Consultancy\".', NULL, '{\"isActive\":true,\"isFeatured\":true,\"status\":\"draft\",\"title\":\"New Consultancy\",\"button_label\":\"Book\",\"short_description\":\"Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.\",\"description\":\"<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.<\\/p>\\r\\n<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.<\\/p>\\r\\n<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.<\\/p>\",\"duration_type\":\"days\",\"duration_value\":\"5\",\"consultancy_medium\":\"video_call\",\"language\":\"en\",\"image\":\"backend-images\\/consultancy\\/01M481JACTMASYGW0RQCBYPGK3.png\",\"published_at\":null,\"owner_admin_id\":1,\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 07:24:06'),
(237, 1, 'Super Admin', 'super-admin', 'consultancies', 'published', 'App\\Models\\Consultancy', 2, 'Published Consultancy \"Demo 2\".', '{\"status\":\"draft\",\"published_at\":null}', '{\"status\":\"published\",\"published_at\":\"2026-10-06 12:27:35\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 07:27:35'),
(238, 1, 'Super Admin', 'super-admin', 'courses', 'published', 'App\\Models\\Course', 1, 'Published Course \"React Native Complete Course (Demo)\".', '{\"status\":\"draft\",\"published_at\":null}', '{\"status\":\"published\",\"published_at\":\"2026-10-06 15:36:18\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 10:36:18'),
(239, 1, 'Super Admin', 'super-admin', 'courses', 'updated', 'App\\Models\\Course', 1, 'Updated Course \"React Native Complete Course (Demo) update\".', '{\"title\":\"React Native Complete Course (Demo)\",\"isFeatured\":0}', '{\"title\":\"React Native Complete Course (Demo) update\",\"isFeatured\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 10:43:35');

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `ads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `place` varchar(255) NOT NULL,
  `ad_image` varchar(255) DEFAULT NULL,
  `ad_url` varchar(2048) DEFAULT NULL,
  `google_ad_code` longtext DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `click_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ad_request_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ad_request_placement_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`id`, `language`, `title`, `page_name`, `place`, `ad_image`, `ad_url`, `google_ad_code`, `start_date`, `expiry_date`, `isActive`, `click_count`, `created_by`, `updated_by`, `created_at`, `updated_at`, `ad_request_id`, `ad_request_placement_id`) VALUES
(1, 'ur', 'Header Ads', 'header', 'header_ad', 'images/backend-images/ads/header-ads.png', 'https://cogentdevs.com', NULL, NULL, NULL, 1, 2, 1, 1, '2026-09-04 02:41:00', '2026-10-05 05:17:47', NULL, NULL),
(2, 'ur', 'Home page ad for Muzammil khan', 'home', 'home_horizontal_large', 'images/backend-images/ads/home-ads.png', 'https://ctr-ksa.com', NULL, '2026-09-03', '2026-09-30', 1, 2, 1, 1, '2026-09-04 02:42:06', '2026-09-22 06:28:56', NULL, NULL),
(3, 'ur', 'Taza shumara ad', 'taza_shumara', 'taza_sidebar_1_normal', 'images/backend-images/ads/taza-ads.png', 'https://wiindot.com', NULL, NULL, '2026-09-21', 0, 1, 1, 1, '2026-09-04 02:43:03', '2026-09-04 03:01:36', NULL, NULL),
(4, 'ur', 'Muzammil', 'taza_shumara', 'taza_sidebar_1_normal', 'images/backend-images/ads/contact-image.png', 'https://cogentdevs.com', NULL, '2026-09-23', '2026-10-10', 1, 0, 1, 1, '2026-09-22 11:11:02', '2026-09-22 11:11:02', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ad_clicks`
--

CREATE TABLE `ad_clicks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ad_id` bigint(20) UNSIGNED NOT NULL,
  `clicked_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ad_clicks`
--

INSERT INTO `ad_clicks` (`id`, `ad_id`, `clicked_at`) VALUES
(1, 1, '2026-09-04 08:01:27'),
(2, 3, '2026-09-04 08:01:36'),
(3, 2, '2026-09-17 16:57:02'),
(4, 2, '2026-09-22 11:28:56'),
(5, 1, '2026-10-05 10:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `ad_requests`
--

CREATE TABLE `ad_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_no` varchar(64) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `details` text DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ad_requests`
--

INSERT INTO `ad_requests` (`id`, `request_no`, `user_id`, `name`, `email`, `phone`, `company`, `from_date`, `to_date`, `details`, `status`, `created_at`, `updated_at`) VALUES
(1, 'AD-000001', 14, 'Muhammad Muzammil', 'muzammilken95@gmail.com', '03121234567', 'Cogent Devs', '2026-09-23', '2026-10-10', 'apni company ka ads lgwana h', 'published', '2026-09-22 11:04:10', '2026-09-22 11:11:02'),
(2, 'AD-000002', 14, 'Muhammad Muzammil', 'muzammilken95@gmail.com', '03121234567', 'Cogent Devs', '2026-09-30', '2026-10-10', 'my publication', 'cancelled', '2026-09-22 11:20:48', '2026-09-22 11:23:39'),
(3, 'AD-000003', 14, 'Muhammad Muzammil', 'muzammilken95@gmail.com', '03121234567', 'Cogent Devs', '2026-10-10', '2026-10-20', 'Homepage advertising campaign.', 'pending', '2026-09-24 09:46:26', '2026-09-24 09:46:26');

-- --------------------------------------------------------

--
-- Table structure for table `ad_request_placements`
--

CREATE TABLE `ad_request_placements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ad_request_id` bigint(20) UNSIGNED NOT NULL,
  `page_name` varchar(50) NOT NULL,
  `place` varchar(100) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ad_request_placements`
--

INSERT INTO `ad_request_placements` (`id`, `ad_request_id`, `page_name`, `place`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'taza_shumara', 'taza_sidebar_1_normal', 'published', '2026-09-22 11:04:10', '2026-09-22 11:11:02'),
(2, 2, 'header', 'header_ad', 'cancelled', '2026-09-22 11:20:49', '2026-09-22 11:23:39'),
(3, 3, 'home', 'home_horizontal_small', 'pending', '2026-09-24 09:46:26', '2026-09-24 09:46:26');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `magazine_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `issue_number` varchar(255) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `article` longtext DEFAULT NULL,
  `isFree` tinyint(1) DEFAULT 0,
  `free_until` date DEFAULT NULL,
  `isFeatured` tinyint(1) DEFAULT 0,
  `show_on_latest` tinyint(1) NOT NULL DEFAULT 0,
  `show_on_editorial_center` tinyint(1) NOT NULL DEFAULT 0,
  `show_on_editorial_featured` tinyint(1) NOT NULL DEFAULT 0,
  `show_visit_counter` tinyint(1) NOT NULL DEFAULT 0,
  `isActive` tinyint(1) DEFAULT 1,
  `status` varchar(255) DEFAULT 'draft',
  `scheduled_at` datetime DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `published_by` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_admin_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `language`, `magazine_id`, `title`, `issue_number`, `publish_date`, `short_description`, `image`, `article`, `isFree`, `free_until`, `isFeatured`, `show_on_latest`, `show_on_editorial_center`, `show_on_editorial_featured`, `show_visit_counter`, `isActive`, `status`, `scheduled_at`, `published_at`, `created_at`, `updated_at`, `created_by`, `updated_by`, `published_by`, `owner_admin_id`) VALUES
(5, 'en', NULL, 'The Future of AI in Mobile App Development: updated', NULL, '2026-10-05', 'Explore how modern AI models, automated workflows, and smart cross-platform frameworks are transforming mobile software development in updated', 'images/backend-images/articles/screencapture-localhost-8000-mazameen-2026-10-03-17-04-22.png', '<p data-path-to-node=\"2,5,2,0\">Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.</p>\r\n<p data-path-to-node=\"2,5,2,1\">Key shifts in 2026 include:</p>\r\n<ul data-path-to-node=\"2,5,2,2\">\r\n<li>\r\n<p data-path-to-node=\"2,5,2,2,0,0\">Smart automated code scaffolding</p>\r\n</li>\r\n<li>\r\n<p data-path-to-node=\"2,5,2,2,1,0\">On-device optimized AI models for cross-platform apps</p>\r\n</li>\r\n<li>\r\n<p data-path-to-node=\"2,5,2,2,2,0\">Enhanced UI/UX design automation</p>\r\n</li>\r\n</ul>\r\n<p id=\"p-rc_e49a696261bc725b-46\" data-path-to-node=\"2,5,2,3\"><span data-path-to-node=\"2,5,2,3,0\">Integrating these technologies not only improves product quality but also reduces turnaround time significantly for production-grade software.</span><span data-path-to-node=\"2,5,2,3,1\"><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><sup class=\"superscript\"><!----></sup><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----></span></p>', 1, NULL, 0, 0, 0, 0, 0, 1, 'draft', NULL, NULL, '2026-10-05 10:08:35', '2026-10-05 10:34:44', 1, 1, NULL, 1),
(6, 'en', NULL, 'The Future of AI in Mobile App Development', NULL, NULL, 'Explore how modern AI models, automated workflows', 'images/backend-images/articles/Gemini-Generated-Image-qxnb84qxnb84qxnb.png', '<p data-path-to-node=\"2,5,2,0\">Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.</p>\r\n<p data-path-to-node=\"2,5,2,1\">Key shifts in 2026 include:</p>\r\n<ul data-path-to-node=\"2,5,2,2\">\r\n<li>\r\n<p data-path-to-node=\"2,5,2,2,0,0\">Smart automated code scaffolding</p>\r\n</li>\r\n<li>\r\n<p data-path-to-node=\"2,5,2,2,1,0\">On-device optimized AI models for cross-platform apps</p>\r\n</li>\r\n<li>\r\n<p data-path-to-node=\"2,5,2,2,2,0\">Enhanced UI/UX design automation</p>\r\n</li>\r\n</ul>\r\n<p id=\"p-rc_e49a696261bc725b-46\" data-path-to-node=\"2,5,2,3\"><span data-path-to-node=\"2,5,2,3,0\">Integrating these technologies not only improves product quality but also reduces turnaround time significantly for production-grade software.</span></p>', 0, NULL, 0, 0, 0, 0, 0, 1, 'published', NULL, '2026-10-05 16:03:22', '2026-10-05 10:36:27', '2026-10-05 11:03:22', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `article_author_maps`
--

CREATE TABLE `article_author_maps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_author_maps`
--

INSERT INTO `article_author_maps` (`id`, `article_id`, `author_id`, `created_at`, `updated_at`) VALUES
(5, 5, 2, '2026-10-05 10:08:35', '2026-10-05 10:08:35'),
(6, 6, 1, '2026-10-05 10:36:27', '2026-10-05 10:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `article_category_maps`
--

CREATE TABLE `article_category_maps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_category_maps`
--

INSERT INTO `article_category_maps` (`id`, `article_id`, `category_id`, `created_at`, `updated_at`) VALUES
(6, 5, 5, '2026-10-05 10:08:35', '2026-10-05 10:08:35'),
(7, 6, 6, '2026-10-05 10:36:27', '2026-10-05 10:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `article_tag_maps`
--

CREATE TABLE `article_tag_maps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ask_questions`
--

CREATE TABLE `ask_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_no` varchar(64) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `sawal` text NOT NULL,
  `admin_response` text DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ask_questions`
--

INSERT INTO `ask_questions` (`id`, `question_no`, `user_id`, `name`, `email`, `phone`, `subject`, `sawal`, `admin_response`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ASK-000001', 14, 'Muhammad Muzammil', 'muzammilken95@gmail.com', '03121234567', 'تعلیم', 'اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔', 'اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔\r\nاکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔', 'answered', '2026-09-23 05:12:18', '2026-09-23 05:34:40'),
(2, 'ASK-000002', 14, 'Muhammad Muzammil', 'muzammil@example.com', '03121234567', 'Business question', 'Please explain this business matter.', NULL, 'pending', '2026-09-24 09:22:16', '2026-09-24 09:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experience_detail` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `speciality` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT 'images/backend-images/author/author.png',
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `contact_number`, `email`, `qualification`, `experience_detail`, `experience_years`, `speciality`, `picture`, `isActive`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'وجاہت شیخ', '03121234567', 'wajahat@gmail.com', 'عالم', 'اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔', 5, 'عقائد', 'images/backend-images/author/account.jpg', 1, '2026-08-21 05:07:21', '2026-09-08 01:09:59', 1, 1),
(2, 'نبیل احمد', '03120000000', 'nabeelahmed@gamil.com', 'MA In Islam', 'اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔', 5, 'اسلام', 'images/backend-images/author/author.png', 1, '2026-08-27 06:35:19', '2026-09-08 01:10:11', 1, 1),
(3, 'M Hammad khan', '03172930029', 'hammad2930029@gmail.com', 'Intermediate', NULL, NULL, NULL, 'images/backend-images/author/WhatsApp-Image-2026-03-27-at-2-38-47-AM.jpg', 1, '2026-10-05 10:59:17', '2026-10-05 10:59:17', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `author_general_settings`
--

CREATE TABLE `author_general_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `column_name` varchar(255) DEFAULT NULL,
  `isView` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `author_general_settings`
--

INSERT INTO `author_general_settings` (`id`, `column_name`, `isView`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'name', 1, '2026-08-21 04:56:05', '2026-08-21 04:56:05', 1, NULL),
(2, 'contact_number', 0, '2026-08-21 04:56:05', '2026-08-21 04:56:05', 1, NULL),
(3, 'email', 0, '2026-08-21 04:56:05', '2026-08-21 04:56:05', 1, NULL),
(4, 'qualification', 1, '2026-08-21 04:56:05', '2026-08-21 04:56:05', 1, NULL),
(5, 'experience_detail', 1, '2026-08-21 04:56:05', '2026-08-21 04:56:05', 1, NULL),
(6, 'experience_years', 1, '2026-08-21 04:56:05', '2026-08-21 04:56:05', 1, NULL),
(7, 'speciality', 1, '2026-08-21 04:56:05', '2026-08-21 04:56:05', 1, NULL),
(8, 'picture', 1, '2026-08-21 04:56:05', '2026-08-21 04:56:05', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `author_visibilities`
--

CREATE TABLE `author_visibilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `column_name` varchar(255) DEFAULT NULL,
  `isView` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `author_visibilities`
--

INSERT INTO `author_visibilities` (`id`, `author_id`, `column_name`, `isView`, `created_at`, `updated_at`) VALUES
(1, 1, 'name', 1, '2026-08-21 05:07:21', '2026-08-21 05:07:21'),
(2, 1, 'contact_number', 1, '2026-08-21 05:07:21', '2026-09-08 01:46:42'),
(3, 1, 'email', 1, '2026-08-21 05:07:21', '2026-09-08 01:46:42'),
(4, 1, 'qualification', 1, '2026-08-21 05:07:21', '2026-08-21 05:07:21'),
(5, 1, 'experience_detail', 1, '2026-08-21 05:07:21', '2026-09-08 01:09:59'),
(6, 1, 'experience_years', 1, '2026-08-21 05:07:21', '2026-09-08 01:09:59'),
(7, 1, 'speciality', 1, '2026-08-21 05:07:21', '2026-08-21 05:07:21'),
(8, 1, 'picture', 1, '2026-08-21 05:07:21', '2026-08-21 05:07:21'),
(9, 2, 'name', 1, '2026-08-27 06:35:19', '2026-08-27 06:35:19'),
(10, 2, 'contact_number', 0, '2026-08-27 06:35:19', '2026-08-27 06:35:19'),
(11, 2, 'email', 0, '2026-08-27 06:35:19', '2026-08-27 06:35:19'),
(12, 2, 'qualification', 1, '2026-08-27 06:35:19', '2026-08-27 06:35:19'),
(13, 2, 'experience_detail', 1, '2026-08-27 06:35:19', '2026-08-27 06:35:19'),
(14, 2, 'experience_years', 1, '2026-08-27 06:35:19', '2026-08-27 06:35:19'),
(15, 2, 'speciality', 1, '2026-08-27 06:35:19', '2026-08-27 06:35:19'),
(16, 2, 'picture', 1, '2026-08-27 06:35:19', '2026-08-27 06:35:19'),
(17, 3, 'name', 1, '2026-10-05 10:59:17', '2026-10-05 10:59:17'),
(18, 3, 'contact_number', 0, '2026-10-05 10:59:17', '2026-10-05 10:59:17'),
(19, 3, 'email', 0, '2026-10-05 10:59:17', '2026-10-05 10:59:17'),
(20, 3, 'qualification', 1, '2026-10-05 10:59:17', '2026-10-05 10:59:17'),
(21, 3, 'experience_detail', 1, '2026-10-05 10:59:17', '2026-10-05 10:59:17'),
(22, 3, 'experience_years', 1, '2026-10-05 10:59:17', '2026-10-05 10:59:17'),
(23, 3, 'speciality', 1, '2026-10-05 10:59:17', '2026-10-05 10:59:17'),
(24, 3, 'picture', 1, '2026-10-05 10:59:17', '2026-10-05 10:59:17');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_2` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `language`, `type`, `position`, `image`, `image_2`, `isActive`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(2, 'ur', 'side-by-side', 'center', 'images/backend-images/banner/sidddd.jpg', 'images/backend-images/banner/siddebyside.jpg', 1, '2026-08-19 06:46:28', '2026-09-03 06:49:03', 1, 1),
(3, 'ur', 'full', 'category-detail-top-full', 'images/backend-images/banner/cat-det-banner.png', NULL, 1, '2026-09-07 07:31:05', '2026-09-07 07:31:05', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `bookmarkable_type` varchar(255) NOT NULL,
  `bookmarkable_id` bigint(20) UNSIGNED NOT NULL,
  `pdf_page` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookmarks`
--

INSERT INTO `bookmarks` (`id`, `user_id`, `bookmarkable_type`, `bookmarkable_id`, `pdf_page`, `created_at`, `updated_at`) VALUES
(2, 14, 'App\\Models\\Article', 1, NULL, '2026-09-12 01:23:18', '2026-09-12 01:23:18'),
(5, 14, 'App\\Models\\Article', 3, NULL, '2026-09-12 09:48:45', '2026-09-12 09:48:45'),
(6, 14, 'App\\Models\\Article', 4, NULL, '2026-09-12 09:48:52', '2026-09-12 09:48:52'),
(8, 14, 'App\\Models\\Magazine', 1, 1, '2026-09-12 09:50:51', '2026-09-12 09:50:51');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('digitalmagazine-cache-0716d9708d321ffb6a00818614779e779925365c', 'i:2;', 1791200324),
('digitalmagazine-cache-0716d9708d321ffb6a00818614779e779925365c:timer', 'i:1791200324;', 1791200324),
('digitalmagazine-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:2;', 1791184735),
('digitalmagazine-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1791184735;', 1791184735),
('digitalmagazine-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1791267013),
('digitalmagazine-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1791267013;', 1791267013),
('digitalmagazine-cache-fa35e192121eabf3dabf9f5ea6abdbcbc107ac3b', 'i:1;', 1791272076),
('digitalmagazine-cache-fa35e192121eabf3dabf9f5ea6abdbcbc107ac3b:timer', 'i:1791272076;', 1791272076);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `language`, `name`, `image`, `isActive`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(5, 'en', 'Technology / Software Development updated', 'images/backend-images/categories/WhatsApp-Image-2026-10-03-at-9-23-28-AM.jpg', 1, '2026-10-05 09:45:13', '2026-10-05 09:48:19', 1, 1),
(6, 'en', 'category 1', 'images/backend-images/categories/image-baa7b2c6-1.jpg', 1, '2026-10-05 10:18:11', '2026-10-05 10:18:11', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `consultancies`
--

CREATE TABLE `consultancies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `button_label` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext NOT NULL,
  `duration_type` varchar(255) NOT NULL,
  `duration_value` int(10) UNSIGNED NOT NULL,
  `consultancy_medium` varchar(255) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `isFeatured` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `published_by` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultancies`
--

INSERT INTO `consultancies` (`id`, `language`, `title`, `button_label`, `image`, `short_description`, `description`, `duration_type`, `duration_value`, `consultancy_medium`, `isActive`, `isFeatured`, `status`, `published_at`, `created_by`, `updated_by`, `published_by`, `owner_admin_id`, `created_at`, `updated_at`) VALUES
(1, 'en', 'demo updated', 'View Details', 'backend-images/consultancy/01M48139H8EES5EMRFQQ41TKTE.png', 'If you need the postal code for a specific block, street, or nearby neighborhood in Karachi, please let me know!', '<p>If you need the postal code for a <strong class=\"rQesXe MPyX\" data-sfc-cp=\"\" data-sfc-root=\"ep\" data-epip=\"\">specific block, street, or nearby neighborhood<!--TgQPHd|||[]--></strong> in Karachi, please let me know!</p>\r\n<p>If you need the postal code for a <strong class=\"rQesXe MPyX\" data-sfc-cp=\"\" data-sfc-root=\"ep\" data-epip=\"\">specific block, street, or nearby neighborhood<!--TgQPHd|||[]--></strong> in Karachi, please let me know!</p>\r\n<p>If you need the postal code for a <strong class=\"rQesXe MPyX\" data-sfc-cp=\"\" data-sfc-root=\"ep\" data-epip=\"\">specific block, street, or nearby neighborhood<!--TgQPHd|||[]--></strong> in Karachi, please let me know!</p>', 'hours', 2, 'zoom', 1, 1, 'published', '2026-10-06 12:17:04', 1, 1, 1, 1, '2026-10-06 07:15:54', '2026-10-06 07:17:04'),
(2, 'en', 'Demo 2', 'View Details', 'backend-images/consultancy/01M481CP7VEM0Q9RYGT5M5CN4B.png', 'Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace.', '<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.</p>\r\n<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.</p>\r\n<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.</p>', 'days', 5, 'video_call', 1, 1, 'published', '2026-10-06 12:27:35', 1, 1, 1, 1, '2026-10-06 07:21:02', '2026-10-06 07:27:35'),
(3, 'en', 'New Consultancy', 'Book', 'backend-images/consultancy/01M481JACTMASYGW0RQCBYPGK3.png', 'Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.', '<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.</p>\r\n<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.</p>\r\n<p>Artificial Intelligence is reshaping the mobile development ecosystem at an unprecedented pace. From intelligent automated testing to dynamic layout generation, modern app developers are leveraging cutting-edge machine learning models directly within their build pipelines.</p>', 'days', 5, 'video_call', 1, 1, 'draft', NULL, 1, 1, NULL, 1, '2026-10-06 07:24:06', '2026-10-06 07:24:06');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `phone`, `email`, `subject`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 'Muhammad Muzammil', '03460329659', 'muzammilken95@gmail.com', 'Add some more features', 'adhsahdjsa lasdhsahdjklash dahsldhskldhklsa daslhdlksadhlk', 1, '2026-09-15 09:59:52', '2026-09-15 10:10:38'),
(2, 'Muhammad Muzammil', '03121234567', 'muzammilken95@gmail.com', 'Magazine information', 'Please send me more information.', 0, '2026-09-24 09:00:28', '2026-09-24 09:00:28');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `button_label` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext NOT NULL,
  `duration_type` varchar(255) NOT NULL,
  `duration_value` int(10) UNSIGNED NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `isFeatured` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `published_by` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `language`, `title`, `button_label`, `image`, `short_description`, `description`, `duration_type`, `duration_value`, `isActive`, `isFeatured`, `status`, `published_at`, `created_by`, `updated_by`, `published_by`, `owner_admin_id`, `created_at`, `updated_at`) VALUES
(1, 'en', 'React Native Complete Course (Demo) update', 'View Course', NULL, 'A demo course covering React Native fundamentals and practical app development.', '<p>This is a demo Course entry for Admin review.</p>', 'hours', 8, 1, 1, 'published', '2026-10-06 15:36:18', NULL, 1, 1, NULL, '2026-10-06 10:33:18', '2026-10-06 10:43:35');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `symbol` varchar(20) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`, `symbol`, `isActive`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Rupees', 'PKR', 'Rs', 1, '2026-08-22 07:08:28', '2026-10-05 10:19:22', 1, 1),
(2, 'US Dollar', 'USD', '$', 1, '2026-08-27 23:45:01', '2026-08-27 23:45:01', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(1, 'daedb493-3fc6-4981-80d0-441c7672f5e7', 'database', 'default', '{\"uuid\":\"daedb493-3fc6-4981-80d0-441c7672f5e7\",\"displayName\":\"App\\\\Mail\\\\NewUserRegisterMailToAdmin\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:35:\\\"App\\\\Mail\\\\NewUserRegisterMailToAdmin\\\":4:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:7;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:20:\\\"cogentdevs@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:11:\\\"afterCommit\\\";b:1;}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";b:1;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1788939162,\"delay\":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\User]. in D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php:786\nStack trace:\n#0 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(112): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(63): App\\Mail\\NewUserRegisterMailToAdmin->restoreModel(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#2 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php(98): App\\Mail\\NewUserRegisterMailToAdmin->getRestoredPropertyValue(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#3 [internal function]: App\\Mail\\NewUserRegisterMailToAdmin->__unserialize(Array)\n#4 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(116): unserialize(\'O:34:\"Illuminat...\')\n#5 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(73): Illuminate\\Queue\\CallQueuedHandler->getCommand(Array)\n#6 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#7 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(559): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(505): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#9 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(257): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#10 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(149): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#11 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#12 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#15 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#16 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(801): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#17 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(292): Illuminate\\Container\\Container->call(Array)\n#18 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Command\\Command.php(284): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#19 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(261): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#20 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(1144): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#21 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(379): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#22 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(218): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#23 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#24 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1242): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#25 D:\\xampp\\htdocs\\dagital-magazine\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#26 {main}', '2026-09-11 06:49:59'),
(2, 'd7dc83b8-9e4a-4173-bf54-f11a30a3cb64', 'database', 'default', '{\"uuid\":\"d7dc83b8-9e4a-4173-bf54-f11a30a3cb64\",\"displayName\":\"App\\\\Mail\\\\AccountActivationLinkMailToUser\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:40:\\\"App\\\\Mail\\\\AccountActivationLinkMailToUser\\\":5:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:7;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:13:\\\"activationUrl\\\";s:109:\\\"http:\\/\\/localhost:8000\\/account\\/activate?token=1ya0EIz2OVcVPEEojkS05Rxo1Md5VNYIrpsNN3Zbx2WgKDCeSZVE5h0LC38er8oc\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";s:17:\\\"Muhammad Muzammil\\\";s:7:\\\"address\\\";s:23:\\\"muzammilken95@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:11:\\\"afterCommit\\\";b:1;}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";b:1;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1788939162,\"delay\":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\User]. in D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php:786\nStack trace:\n#0 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(112): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(63): App\\Mail\\AccountActivationLinkMailToUser->restoreModel(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#2 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php(98): App\\Mail\\AccountActivationLinkMailToUser->getRestoredPropertyValue(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#3 [internal function]: App\\Mail\\AccountActivationLinkMailToUser->__unserialize(Array)\n#4 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(116): unserialize(\'O:34:\"Illuminat...\')\n#5 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(73): Illuminate\\Queue\\CallQueuedHandler->getCommand(Array)\n#6 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#7 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(559): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(505): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#9 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(257): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#10 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(149): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#11 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#12 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#15 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#16 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(801): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#17 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(292): Illuminate\\Container\\Container->call(Array)\n#18 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Command\\Command.php(284): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#19 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(261): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#20 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(1144): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#21 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(379): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#22 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(218): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#23 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#24 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1242): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#25 D:\\xampp\\htdocs\\dagital-magazine\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#26 {main}', '2026-09-11 06:49:59'),
(3, '5c41552e-845d-492c-a604-1b6325920149', 'database', 'default', '{\"uuid\":\"5c41552e-845d-492c-a604-1b6325920149\",\"displayName\":\"App\\\\Mail\\\\NewUserRegisterMailToAdmin\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:35:\\\"App\\\\Mail\\\\NewUserRegisterMailToAdmin\\\":4:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:8;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:20:\\\"cogentdevs@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:11:\\\"afterCommit\\\";b:1;}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";b:1;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1788939683,\"delay\":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\User]. in D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php:786\nStack trace:\n#0 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(112): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(63): App\\Mail\\NewUserRegisterMailToAdmin->restoreModel(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#2 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php(98): App\\Mail\\NewUserRegisterMailToAdmin->getRestoredPropertyValue(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#3 [internal function]: App\\Mail\\NewUserRegisterMailToAdmin->__unserialize(Array)\n#4 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(116): unserialize(\'O:34:\"Illuminat...\')\n#5 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(73): Illuminate\\Queue\\CallQueuedHandler->getCommand(Array)\n#6 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#7 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(559): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(505): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#9 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(257): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#10 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(149): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#11 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#12 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#15 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#16 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(801): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#17 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(292): Illuminate\\Container\\Container->call(Array)\n#18 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Command\\Command.php(284): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#19 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(261): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#20 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(1144): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#21 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(379): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#22 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(218): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#23 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#24 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1242): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#25 D:\\xampp\\htdocs\\dagital-magazine\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#26 {main}', '2026-09-11 06:49:59'),
(4, 'c1b32109-c23e-4158-86f3-c61931085773', 'database', 'default', '{\"uuid\":\"c1b32109-c23e-4158-86f3-c61931085773\",\"displayName\":\"App\\\\Mail\\\\AccountActivationLinkMailToUser\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:40:\\\"App\\\\Mail\\\\AccountActivationLinkMailToUser\\\":5:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:8;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:13:\\\"activationUrl\\\";s:109:\\\"http:\\/\\/localhost:8000\\/account\\/activate?token=90v0chKdeiee88sYrFBEZF1GKixQE68xPdGm46F8EVAAkcK3mI2vEdsMUEKLfkv1\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";s:17:\\\"MUHAMMAD Muzammil\\\";s:7:\\\"address\\\";s:23:\\\"muzammilken95@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";s:11:\\\"afterCommit\\\";b:1;}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";b:1;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1788939683,\"delay\":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\User]. in D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php:786\nStack trace:\n#0 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(112): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(63): App\\Mail\\AccountActivationLinkMailToUser->restoreModel(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#2 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php(98): App\\Mail\\AccountActivationLinkMailToUser->getRestoredPropertyValue(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#3 [internal function]: App\\Mail\\AccountActivationLinkMailToUser->__unserialize(Array)\n#4 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(116): unserialize(\'O:34:\"Illuminat...\')\n#5 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(73): Illuminate\\Queue\\CallQueuedHandler->getCommand(Array)\n#6 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#7 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(559): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(505): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#9 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(257): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#10 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(149): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#11 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#12 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#15 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#16 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(801): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#17 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(292): Illuminate\\Container\\Container->call(Array)\n#18 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Command\\Command.php(284): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#19 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(261): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#20 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(1144): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#21 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(379): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#22 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\symfony\\console\\Application.php(218): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#23 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#24 D:\\xampp\\htdocs\\dagital-magazine\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1242): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#25 D:\\xampp\\htdocs\\dagital-magazine\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#26 {main}', '2026-09-11 06:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `faq_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` varchar(1000) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `language`, `faq_category_id`, `question`, `answer`, `isActive`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'ur', 1, 'What is Digital Magazine ?', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English.', 1, '2026-08-19 07:16:10', '2026-09-15 11:50:57', 1, 1),
(2, 'ur', 1, 'پریزنٹیشن', 'لاطینی نژاد اور بکواس مواد آرکائیو کا فائدہ گرافک ڈیزائن پر اپنی توجہ مرکوز کر سکتے ہیں اور اس طرح متن کے مواد کی طرف سے مشغول ہونے سے قاری کو روکتا ہے اور. بے شک ٹیکسٹ Lorem Ipsum صرف یہ حتمی مصنوعات سے میل کھاتا ہے تو ماڈل کا ایک عام قبضے انکرن کرنے کے لئے اور مستقبل تبدیلی کیے بغیر کی اشاعت یقینی بنانے کے لئے متغیر', 1, '2026-09-15 12:02:51', '2026-09-15 12:02:51', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `faq_categories`
--

CREATE TABLE `faq_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faq_categories`
--

INSERT INTO `faq_categories` (`id`, `language`, `name`, `icon`, `isActive`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'ur', 'ڈیجیٹل میگزین', 'images/backend-images/faq-category/01M2JEEQNB80MESNA7QC7P2679.png', 1, 1, 1, '2026-09-15 11:48:28', '2026-09-15 11:50:21'),
(2, 'ur', 'مضامین', 'images/backend-images/faq-category/01M2JFCASS1HA8H6DT3ETKC3AJ.png', 1, 1, 1, '2026-09-15 12:06:31', '2026-09-15 12:06:31');

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `app_name` varchar(255) DEFAULT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `google_ads_client_id` varchar(32) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `footer_logo` varchar(255) DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `contact_1` varchar(255) DEFAULT NULL,
  `contact_2` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `facebook` varchar(2048) DEFAULT NULL,
  `instagram` varchar(2048) DEFAULT NULL,
  `youtube` varchar(2048) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `tiktok` varchar(2048) DEFAULT NULL,
  `x` varchar(2048) DEFAULT NULL,
  `app_section_heading` varchar(255) DEFAULT NULL,
  `app_section_text` text DEFAULT NULL,
  `play_store_icon` varchar(255) DEFAULT NULL,
  `play_store_link` varchar(255) DEFAULT NULL,
  `app_store_icon` varchar(255) DEFAULT NULL,
  `app_store_link` varchar(255) DEFAULT NULL,
  `default_language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `max_devices_per_user` int(10) UNSIGNED DEFAULT NULL,
  `max_concurrent_sessions` int(10) UNSIGNED DEFAULT NULL,
  `cookie_consent_enabled` tinyint(1) DEFAULT NULL,
  `maintenance_mode` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `app_name`, `url`, `google_ads_client_id`, `logo`, `footer_logo`, `footer_text`, `favicon`, `contact_1`, `contact_2`, `email`, `address`, `facebook`, `instagram`, `youtube`, `linkedin`, `tiktok`, `x`, `app_section_heading`, `app_section_text`, `play_store_icon`, `play_store_link`, `app_store_icon`, `app_store_link`, `default_language_id`, `max_devices_per_user`, `max_concurrent_sessions`, `cookie_consent_enabled`, `maintenance_mode`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Digital Magazine', 'http://localhost:8000', NULL, 'images/backend-images/logo/logo-header.png', 'images/backend-images/logo/logo-footer.png', 'معیاری اردو مضامین، خصوصی گفتگو، ہفتہ وار میگزین اور بدلتی دنیا کے معتبر تجزیوں کا ڈیجیٹل پلیٹ فارم۔', 'images/backend-images/logo/favicon-545ca3a2-dcc3-4d91-a29d-9591cf74927d.webp', '03001234567', NULL, 'digitalmagazine@gmail.com', 'Karachi, Pakistan', 'https://facebook.com', 'https://instagram.com', 'https://youtube.com', NULL, NULL, NULL, 'ایپ ڈاؤن لوڈ کریں', 'ہماری ایپ ڈاؤن لوڈ کریں اور کہیں بھی، کبھی بھی پڑھیں۔', 'images/backend-images/logo/play-logo.png', 'https://play-store.com', 'images/backend-images/logo/app-store-logo.png', 'https://app-store.com', 1, 1, 1, NULL, NULL, '2026-08-19 01:26:27', '2026-09-01 06:42:15', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `home_cards`
--

CREATE TABLE `home_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_cards`
--

INSERT INTO `home_cards` (`id`, `language`, `position`, `title`, `image`, `description`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'ur', 'below slider', 'تازہ ترین مضامین', 'images/backend-images/home-cards/01M1G9PAA4V5B88WX2S8H6KZ6E.png', 'اسلام ایک ایسا مذہب ہے جس میں ریاست کے ہر باشندے کی ضروریات کا خیال رکھا جاتا ہے۔ اسلام نے رعایا کے بنیادی حقوق حکومتِ وقت کے ذمہ واجب کیے ہیں۔ ریاست کا کوئی بھی فرد حاکمِ وقت سے ان کا مطالبہ کر سکتا ہے', 1, 1, 1, '2026-08-29 07:04:20', '2026-09-02 01:10:37'),
(2, 'ur', 'below slider', 'شرعی فریض', 'images/backend-images/home-cards/01M1G8A69WH3VFJ873Z4WJCBQQ.webp', 'اسلام ایک ایسا مذہب ہے جس میں ریاست کے ہر باشندے کی ضروریات کا خیال رکھا جاتا ہے۔ اسلام نے رعایا کے بنیادی حقوق حکومتِ وقت کے ذمہ واجب کیے ہیں', 1, 1, 1, '2026-09-02 00:08:50', '2026-09-02 00:08:50'),
(3, 'ur', 'below slider', 'تازہ ترین مضامین', 'images/backend-images/home-cards/01M1G8E5Q3ZJFVCHKPJPH9VB4P.png', 'اسلام ایک ایسا مذہب ہے جس میں ریاست کے ہر باشندے کی ضروریات کا خیال رکھا جاتا ہے', 1, 1, 1, '2026-09-02 00:11:01', '2026-09-02 00:17:22'),
(4, 'ur', 'below slider', 'اغراض و مقاصد', 'images/backend-images/home-cards/01M1G8JN1WMCER1CQ8V1AE8H73.webp', 'شریعہ اینڈ بزنس ایک ایسا میگزین ہے جس میں تاجروں کی شرعی رہنمائی کے ساتھ ساتھ تجارتی و کاروباری آگہی بھی دی جائے گی۔ جس میں مفتیان کرام کو تجارتی اصطلاحات اور کاروبار معاہدات سے آگاہ کرنے کے لیے مضامین، سروے اور فیچرز شائع کیے جائیں گے۔', 1, 1, 1, '2026-09-02 00:13:27', '2026-09-02 00:13:27'),
(5, 'ur', 'above footer', 'تازہ ترین مضامین', 'images/backend-images/home-cards/01M1GCSAHW25W151R7YKD25778.png', 'اسلام ایک ایسا مذہب ہے جس میں ریاست کے ہر باشندے کی ضروریات کا خیال رکھا جاتا ہے۔ اسلام نے رعایا کے بنیادی حقوق حکومتِ وقت کے ذمہ واجب کیے ہیں۔ ریاست کا کوئی بھی فرد حاکمِ وقت سے ان کا مطالبہ کر سکتا ہے', 1, 1, 1, '2026-09-02 01:27:00', '2026-09-02 02:18:31'),
(6, 'ur', 'above footer', NULL, 'images/backend-images/home-cards/01M1GCTEYFBX5NATZFMM4PP48A.png', NULL, 1, 1, 1, '2026-09-02 01:27:38', '2026-09-02 01:27:38'),
(7, 'ur', 'above footer', NULL, 'images/backend-images/home-cards/01M1GCV3TC3FBD002J3R7R96CX.png', NULL, 1, 1, 1, '2026-09-02 01:27:59', '2026-09-02 01:27:59'),
(8, 'ur', 'above footer', NULL, 'images/backend-images/home-cards/01M1GCX4RAPJT1SVFR92YYZP51.png', NULL, 1, 1, 1, '2026-09-02 01:29:05', '2026-09-02 01:29:05'),
(9, 'en', 'below slider', 'demo', 'images/backend-images/home-cards/01M45XHT3GAWX0BWM1S5DGHN24.jpg', 'demo testing', 1, 17, 17, '2026-10-05 11:35:27', '2026-10-05 11:35:27');

-- --------------------------------------------------------

--
-- Table structure for table `home_card_title_positions`
--

CREATE TABLE `home_card_title_positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) NOT NULL,
  `card_position` varchar(255) NOT NULL,
  `title_position` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_card_title_positions`
--

INSERT INTO `home_card_title_positions` (`id`, `language`, `card_position`, `title_position`, `created_at`, `updated_at`) VALUES
(1, 'ur', 'below slider', 'center', '2026-09-02 01:51:01', '2026-09-02 01:51:01'),
(2, 'ur', 'above footer', 'right', '2026-09-02 01:51:01', '2026-09-02 01:51:01');

-- --------------------------------------------------------

--
-- Table structure for table `home_page_section_headings`
--

CREATE TABLE `home_page_section_headings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `section_name` varchar(255) DEFAULT NULL,
  `content_position` varchar(255) DEFAULT NULL,
  `short_title` varchar(255) DEFAULT NULL,
  `main_title` varchar(255) DEFAULT NULL,
  `short_detail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_page_section_headings`
--

INSERT INTO `home_page_section_headings` (`id`, `language`, `section_name`, `content_position`, `short_title`, `main_title`, `short_detail`, `created_at`, `updated_at`) VALUES
(1, 'ur', 'latest_articles', 'right', NULL, 'تازہ ترین', NULL, '2026-09-02 04:37:47', '2026-09-02 04:47:26'),
(2, 'ur', 'below_slider_home_cards', 'center', 'تازہ انتخاب', 'آج کے اہم موضوعات', 'تجارت پیشہ افراد کو تازہ صورت حال سے باخبر رکھنا', '2026-09-02 04:37:47', '2026-09-02 04:37:47'),
(3, 'ur', 'editorial', 'right', NULL, 'اداریہ', NULL, '2026-09-02 04:37:47', '2026-09-02 04:37:47'),
(4, 'ur', 'weekly_magazine', 'center', NULL, 'ہفتہ وار میگزین', NULL, '2026-09-02 04:37:47', '2026-09-02 04:37:47'),
(5, 'ur', 'audio', 'right', NULL, 'آڈیو انٹرویوز', NULL, '2026-09-02 04:37:47', '2026-09-02 04:37:47'),
(6, 'ur', 'newsletter', 'right', NULL, 'ہمارا نیوز لیٹر سبسکرائب کریں', 'تازہ ترین مضامین، انٹرویوز اور خصوصی اپ ڈیٹس براہِ راست اپنے ای میل پر حاصل کریں۔', '2026-09-02 04:37:47', '2026-09-02 04:37:47'),
(7, 'ur', 'advertise_with_us', 'right', NULL, 'اپنے برانڈ کو ہزاروں قارئین تک پہنچائیں', 'ہماری اشتہاراتی پیشکش کے ذریعے اپنے کاروبار کو نئی بلندیوں تک لے جائیں۔', '2026-09-02 04:37:47', '2026-09-02 04:47:26'),
(8, 'ur', 'categories', 'center', 'تازہ انتخاب', 'مقبول موضوعات', NULL, '2026-09-02 04:37:47', '2026-09-02 04:37:47'),
(9, 'ur', 'above_footer_home_cards', 'right', 'تازہ انتخاب', 'آج کے اہم موضوعات', 'تجارت پیشہ افراد کو تازہ صورت حال سے باخبر رکھنا', '2026-09-02 04:37:47', '2026-09-02 04:37:47');

-- --------------------------------------------------------

--
-- Table structure for table `home_sections`
--

CREATE TABLE `home_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `section_condition` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `title_2` varchar(255) DEFAULT NULL,
  `description_2` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_2` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_sections`
--

INSERT INTO `home_sections` (`id`, `language`, `position`, `section_condition`, `title`, `description`, `title_2`, `description_2`, `image`, `image_2`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'ur', 'below slider', 3, 'Home Section Updated', '<div>\r\n<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.</p>\r\n</div>\r\n<div>&nbsp;</div>', NULL, NULL, 'images/backend-images/home-sections/01M1BC8W3NE7N4N4HYDD207GNZ.png', NULL, 1, 1, 1, '2026-08-31 02:41:49', '2026-08-31 02:45:11');

-- --------------------------------------------------------

--
-- Table structure for table `info_pages`
--

CREATE TABLE `info_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) NOT NULL,
  `page` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `info_pages`
--

INSERT INTO `info_pages` (`id`, `language`, `page`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ur', 'privacy_policy', 'رازداری پالیسی', '<p>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔</p>', '2026-09-15 10:47:51', '2026-09-15 10:51:18'),
(2, 'en', 'privacy_policy', 'Privacy Policy', NULL, '2026-09-15 10:47:52', '2026-09-15 10:47:52'),
(3, 'ur', 'terms_conditions', 'شرائط و ضوابط', '<p>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔</p>', '2026-09-15 10:47:52', '2026-09-15 10:51:23'),
(4, 'en', 'terms_conditions', 'Terms & Conditions', NULL, '2026-09-15 10:47:52', '2026-09-15 10:47:52'),
(5, 'ur', 'disclaimer', 'دستبرداری', '<p>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔<br>اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔</p>', '2026-09-15 10:47:52', '2026-09-15 10:51:09'),
(6, 'en', 'disclaimer', 'Disclaimer', NULL, '2026-09-15 10:47:52', '2026-09-15 10:47:52');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Urdu', 'ur', 0, 0, '2026-08-19 06:43:48', '2026-08-19 06:43:48'),
(2, 'English', 'en', 1, 1, '2026-08-19 06:43:48', '2026-08-19 06:43:48');

-- --------------------------------------------------------

--
-- Table structure for table `magazines`
--

CREATE TABLE `magazines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `issue_number` varchar(255) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `isFree` tinyint(1) DEFAULT 0,
  `free_until` date DEFAULT NULL,
  `is_downloadable` tinyint(1) NOT NULL DEFAULT 0,
  `isFeatured` tinyint(1) DEFAULT 0,
  `show_visit_counter` tinyint(1) NOT NULL DEFAULT 0,
  `isActive` tinyint(1) DEFAULT 1,
  `status` varchar(255) DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `published_by` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_admin_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `magazines`
--

INSERT INTO `magazines` (`id`, `language`, `title`, `issue_number`, `publish_date`, `cover_image`, `scheduled_at`, `published_at`, `description`, `isFree`, `free_until`, `is_downloadable`, `isFeatured`, `show_visit_counter`, `isActive`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `published_by`, `owner_admin_id`) VALUES
(1, 'ur', 'کاروباری مشکلات کا حل', 'M-2026001', '2025-07-01', 'images/backend-images/magazines/cover.jpg', NULL, '2026-08-22 07:51:59', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.', 1, NULL, 0, 0, 1, 1, 'published', '2026-08-22 02:47:01', '2026-09-03 05:07:22', 1, 1, 1, 1),
(2, 'ur', 'مستقل حل پر توجہ دینا', 'M-2026002', '2026-08-01', 'images/backend-images/magazines/cover-2.jpg', NULL, '2026-08-22 10:33:03', NULL, 0, NULL, 0, 1, 1, 1, 'published', '2026-08-22 05:32:51', '2026-09-03 05:07:06', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `magazine_author_maps`
--

CREATE TABLE `magazine_author_maps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `magazine_id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `magazine_author_maps`
--

INSERT INTO `magazine_author_maps` (`id`, `magazine_id`, `author_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-08-22 02:47:01', '2026-08-22 02:47:01'),
(2, 2, 1, '2026-08-22 05:32:51', '2026-08-22 05:32:51'),
(3, 2, 2, '2026-09-03 04:05:41', '2026-09-03 04:05:41');

-- --------------------------------------------------------

--
-- Table structure for table `magazine_category_maps`
--

CREATE TABLE `magazine_category_maps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `magazine_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `magazine_tag_maps`
--

CREATE TABLE `magazine_tag_maps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `magazine_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `magazine_tag_maps`
--

INSERT INTO `magazine_tag_maps` (`id`, `magazine_id`, `tag_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-08-22 02:47:01', '2026-08-22 02:47:01'),
(2, 1, 1, '2026-08-22 02:47:01', '2026-08-22 02:47:01'),
(3, 2, 2, '2026-08-22 05:32:51', '2026-08-22 05:32:51');

-- --------------------------------------------------------

--
-- Table structure for table `media_storage_locations`
--

CREATE TABLE `media_storage_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_type` varchar(255) DEFAULT NULL,
  `media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `storage_provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `checksum` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `verification_status` varchar(255) DEFAULT NULL,
  `last_verified_at` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_storage_locations`
--

INSERT INTO `media_storage_locations` (`id`, `media_type`, `media_id`, `storage_provider_id`, `path`, `file_name`, `size`, `mime_type`, `checksum`, `is_primary`, `priority`, `status`, `verification_status`, `last_verified_at`, `error_message`, `created_at`, `updated_at`) VALUES
(2, 'magazine', 2, 1, 'magazine/2/01M0MGFJTRPDA95T9N2ARM99BK.pdf', 'DIgital-magazine-updated.pdf', 3430140, 'application/pdf', 'a0411deede8e46696b57af7fe3646fa0857c2c7b4e990a92c39a4635fba616ae', 1, 1, 'available', 'pending', NULL, NULL, '2026-08-22 05:32:51', '2026-08-22 05:32:52'),
(3, 'magazine', 1, 1, 'magazine/1/01M1RJWXJSXH335YHJQRKV6EG9.pdf', 'Digital-Magazine-Layout.pdf', 881394, 'application/pdf', '4e072d7f91c65996a9ecf668c7872b6df48b611f430dc9b1fa55d9dbad9228d8', 1, 1, 'available', 'pending', NULL, NULL, '2026-09-05 05:47:45', '2026-09-05 05:47:45');

-- --------------------------------------------------------

--
-- Table structure for table `meta_tags`
--

CREATE TABLE `meta_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `slug_url` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(2048) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meta_tags`
--

INSERT INTO `meta_tags` (`id`, `table_name`, `table_id`, `language`, `title`, `keywords`, `description`, `slug_url`, `canonical_url`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(3, 'magazines', 1, 'ur', 'کاروباری مشکلات کا حل', 'Islam, Business,', 'Islam, Business,', 'karobary-mshklat-ka-hl', NULL, '2026-08-22 02:47:01', '2026-09-03 05:07:22', 1, 1),
(4, 'magazines', 2, 'ur', 'مستقل حل پر توجہ دینا', 'modern days, islam', 'modern days, islam', 'mstkl-hl-pr-tog-dyna', NULL, '2026-08-22 05:32:51', '2026-09-03 05:07:06', 1, 1),
(12, 'categories', 5, 'en', 'Technology / Software Development updated', NULL, NULL, 'technology-software-development-updated', NULL, '2026-10-05 09:45:13', '2026-10-05 09:48:19', 1, 1),
(13, 'articles', 5, 'en', 'The Future of AI in Mobile App Development: updated', NULL, NULL, 'the-future-of-ai-in-mobile-app-development-updated', NULL, '2026-10-05 10:08:35', '2026-10-05 10:17:32', 1, 1),
(15, NULL, NULL, 'en', 'Home', 'Technology / Software Development updated', 'Technology / Software Development updated', '/', NULL, '2026-10-05 10:19:55', '2026-10-05 10:30:12', 1, 1),
(16, 'articles', 6, 'en', 'The Future of AI in Mobile App Development', NULL, NULL, 'the-future-of-ai-in-mobile-app-development', NULL, '2026-10-05 10:36:27', '2026-10-05 10:36:27', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_18_094711_create_permission_tables', 2),
(5, '2026_08_18_095837_create_personal_access_tokens_table', 3),
(6, '2026_08_18_114903_create_general_settings_table', 4),
(7, '2026_08_19_063626_create_languages_table', 5),
(8, '2026_08_19_072945_create_sliders_table', 6),
(9, '2026_08_19_113132_create_banners_table', 7),
(10, '2026_08_19_120929_create_faqs_table', 8),
(11, '2026_08_21_094500_create_authors_table', 9),
(12, '2026_08_21_094501_create_author_general_settings_table', 9),
(13, '2026_08_21_094502_create_author_visibilities_table', 9),
(14, '2026_08_21_100601_add_default_picture_to_authors_table', 10),
(15, '2026_08_21_115253_create_tags_table', 10),
(16, '2026_08_21_115256_create_categories_table', 10),
(17, '2026_08_22_050733_create_meta_tags_table', 11),
(18, '2026_08_22_055815_create_storage_providers_table', 12),
(19, '2026_08_22_055816_create_media_storage_locations_table', 12),
(20, '2026_08_22_071812_create_magazines_table', 13),
(21, '2026_08_22_071813_create_magazine_category_maps_table', 13),
(22, '2026_08_22_071814_create_magazine_tag_maps_table', 13),
(23, '2026_08_22_071815_create_magazine_author_maps_table', 13),
(24, '2026_08_22_101804_create_related_magazines_table', 14),
(25, '2026_08_22_110201_create_articles_table', 15),
(26, '2026_08_22_110202_create_article_category_maps_table', 15),
(27, '2026_08_22_110204_create_article_tag_maps_table', 15),
(28, '2026_08_22_110205_create_article_author_maps_table', 15),
(29, '2026_08_22_110206_create_related_articles_table', 15),
(30, '2026_08_22_120441_create_currencies_table', 16),
(31, '2026_08_25_093837_create_subscription_types_table', 17),
(32, '2026_08_25_095531_create_subscription_products_table', 18),
(33, '2026_08_27_092924_add_phone_and_is_active_to_users_table', 19),
(34, '2026_08_25_095534_create_subscription_product_types_table', 19),
(35, '2026_08_27_105233_add_permission_mode_to_users_table', 20),
(36, '2026_08_28_042126_add_audit_ownership_columns_to_admin_tables', 21),
(37, '2026_08_28_045030_create_activity_logs_table', 22),
(38, '2026_08_28_054308_add_is_downloadable_to_magazines_table', 23),
(39, '2026_08_28_062146_add_free_until_to_magazines_and_articles_tables', 24),
(40, '2026_08_29_042502_add_parent_admin_id_to_users_table', 25),
(41, '2026_08_29_050741_add_owner_admin_id_to_magazines_and_articles_tables', 26),
(42, '2026_08_29_050742_backfill_magazine_and_article_owner_admin_ids', 26),
(43, '2026_08_29_104114_create_abouts_table', 27),
(44, '2026_08_29_113813_create_home_cards_table', 28),
(45, '2026_08_31_072215_create_home_sections_table', 29),
(46, '2026_09_01_104727_add_content_position_to_sliders_table', 30),
(47, '2026_09_01_112006_add_footer_social_and_app_info_to_general_settings_table', 31),
(48, '2026_09_02_063732_create_home_card_title_positions_table', 32),
(49, '2026_09_02_092103_create_home_page_section_headings_table', 33),
(50, '2026_09_02_102801_add_homepage_placement_flags_to_articles_table', 34),
(51, '2026_09_03_072300_create_taza_shumara_table', 35),
(52, '2026_09_03_072306_create_taza_shumara_articles_table', 35),
(53, '2026_09_04_063619_create_ads_table', 36),
(54, '2026_09_04_063620_add_google_ads_client_id_to_general_settings_table', 36),
(55, '2026_09_04_065520_add_click_count_to_ads_table', 37),
(56, '2026_09_04_065521_create_ad_clicks_table', 37),
(57, '2026_09_07_055944_add_magazine_id_to_articles_table', 38),
(58, '2026_09_09_064054_add_avatar_and_activation_fields_to_users_table', 39),
(59, '2026_09_09_065754_add_consumed_activation_token_hash_to_users_table', 40),
(60, '2026_09_09_114058_add_profile_image_to_users_table', 41),
(61, '2026_09_10_044523_create_user_two_factor_settings_table', 42),
(62, '2026_09_10_044524_create_user_two_factor_challenges_table', 42),
(63, '2026_09_10_100756_create_user_subscriptions_table', 43),
(64, '2026_09_10_100758_create_user_sub_types_table', 43),
(65, '2026_09_11_090237_create_subscription_notification_settings_table', 44),
(66, '2026_09_11_092105_create_subscription_expiry_reminders_table', 44),
(67, '2026_09_12_050900_create_subscription_expiry_notifications_table', 45),
(68, '2026_09_12_055856_create_bookmarks_table', 46),
(69, '2026_09_12_070000_create_user_content_visits_table', 47),
(70, '2026_09_12_080000_create_site_visits_table', 48),
(71, '2026_09_14_120000_add_show_visit_counter_to_magazines_table', 49),
(72, '2026_09_14_163059_create_search_contents_table', 50),
(73, '2026_09_15_102539_add_invoice_fields_to_user_subscriptions_table', 51),
(74, '2026_09_15_140108_create_contacts_table', 52),
(75, '2026_09_15_153232_create_info_pages_table', 53),
(76, '2026_09_15_162134_create_faq_categories_table', 54),
(77, '2026_09_15_162136_add_faq_category_id_to_faqs_table', 54),
(78, '2026_09_22_143005_create_ad_requests_table', 55),
(79, '2026_09_22_143008_create_ad_request_placements_table', 55),
(80, '2026_09_22_153215_add_ad_request_links_to_ads_table', 56),
(81, '2026_09_22_171916_create_ask_questions_table', 57),
(82, '2026_09_23_000000_create_newsletter_subscribers_table', 58),
(83, '2026_09_23_010000_create_newsletter_campaigns_table', 59),
(84, '2026_09_23_010100_create_newsletter_campaign_contents_table', 60),
(85, '2026_09_24_000000_add_unsubscribe_token_to_newsletter_subscribers_table', 61),
(86, '2026_10_02_115314_create_services_table', 62),
(87, '2026_10_02_123724_create_videos_table', 63),
(88, '2026_10_02_165635_create_payment_accounts_table', 64),
(89, '2026_10_03_094121_add_payment_review_fields_to_user_subscriptions_table', 65),
(90, '2026_10_05_000000_create_subscription_product_videos_table', 66),
(91, '2026_10_06_095124_add_publication_fields_to_videos_table', 67),
(92, '2026_10_06_113447_create_consultancies_table', 68),
(93, '2026_10_06_113448_create_related_consultancies_table', 69),
(94, '2026_10_06_121245_add_button_label_to_consultancies_table', 70),
(95, '2026_10_06_141848_create_courses_table', 71),
(96, '2026_10_06_141849_create_related_courses_table', 72);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(2, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 3),
(5, 'App\\Models\\User', 3),
(7, 'App\\Models\\User', 3),
(10, 'App\\Models\\User', 3),
(12, 'App\\Models\\User', 3),
(13, 'App\\Models\\User', 3),
(18, 'App\\Models\\User', 3),
(20, 'App\\Models\\User', 3),
(21, 'App\\Models\\User', 3),
(22, 'App\\Models\\User', 3),
(23, 'App\\Models\\User', 3),
(28, 'App\\Models\\User', 3),
(35, 'App\\Models\\User', 3),
(37, 'App\\Models\\User', 3),
(38, 'App\\Models\\User', 3),
(39, 'App\\Models\\User', 3),
(40, 'App\\Models\\User', 3),
(42, 'App\\Models\\User', 3),
(63, 'App\\Models\\User', 3),
(65, 'App\\Models\\User', 3),
(66, 'App\\Models\\User', 3),
(67, 'App\\Models\\User', 3),
(69, 'App\\Models\\User', 3),
(70, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 14),
(2, 'App\\Models\\User', 15),
(2, 'App\\Models\\User', 16),
(4, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 5),
(5, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 6),
(6, 'App\\Models\\User', 17);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_campaigns`
--

CREATE TABLE `newsletter_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `brevo_campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brevo_status` varchar(255) DEFAULT NULL,
  `brevo_error` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `newsletter_campaigns`
--

INSERT INTO `newsletter_campaigns` (`id`, `title`, `short_description`, `status`, `scheduled_at`, `sent_at`, `brevo_campaign_id`, `brevo_status`, `brevo_error`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'ہفتہ وار مواد', 'اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔', 'sent', NULL, '2026-09-23 12:38:29', 5, 'sent', NULL, 1, 1, '2026-09-23 12:32:45', '2026-09-23 12:38:29'),
(2, 'ہفتہ وار مضامین', 'اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔ اکڑ بکڑ بمبے بو، اسی نوے پورے سو۔ سو میں لگا دھاگا، چور نکل کے بھاگا۔ سپاہی بن کے آؤں گا، اچھا کھانا کھاؤں گا۔ ریل بولی چکھا چکھ، نان پاؤ بسکٹ۔', 'sent', NULL, '2026-09-24 05:43:53', 6, 'sent', NULL, 1, 1, '2026-09-24 05:31:57', '2026-09-24 05:43:53');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_campaign_contents`
--

CREATE TABLE `newsletter_campaign_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `newsletter_campaign_id` bigint(20) UNSIGNED NOT NULL,
  `content_type` varchar(255) NOT NULL,
  `content_id` bigint(20) UNSIGNED NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `newsletter_campaign_contents`
--

INSERT INTO `newsletter_campaign_contents` (`id`, `newsletter_campaign_id`, `content_type`, `content_id`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'article', 3, 1, '2026-09-23 12:32:45', '2026-09-23 12:32:45'),
(2, 1, 'article', 4, 2, '2026-09-23 12:32:45', '2026-09-23 12:32:45'),
(3, 1, 'article', 2, 3, '2026-09-23 12:32:45', '2026-09-23 12:32:45'),
(4, 1, 'magazine', 1, 4, '2026-09-23 12:32:45', '2026-09-23 12:32:45'),
(5, 2, 'article', 3, 1, '2026-09-24 05:31:57', '2026-09-24 05:31:57'),
(6, 2, 'article', 4, 2, '2026-09-24 05:31:57', '2026-09-24 05:31:57'),
(7, 2, 'magazine', 2, 3, '2026-09-24 05:31:57', '2026-09-24 05:31:57');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `unsubscribe_token` varchar(64) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'subscribed',
  `unsubscribe_reason` text DEFAULT NULL,
  `subscribed_at` timestamp NULL DEFAULT NULL,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  `brevo_sync_status` varchar(255) NOT NULL DEFAULT 'pending',
  `brevo_synced_at` timestamp NULL DEFAULT NULL,
  `brevo_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `unsubscribe_token`, `status`, `unsubscribe_reason`, `subscribed_at`, `unsubscribed_at`, `brevo_sync_status`, `brevo_synced_at`, `brevo_error`, `created_at`, `updated_at`) VALUES
(1, 'muzammilken95@gmail.com', '3ctsP1MZkgqyzoFwi5kqU14eHhC7Hfnb02McJ4qZ470L06A2L59SPvO6hvOqvDgG', 'subscribed', NULL, '2026-09-24 05:48:24', NULL, 'synced', '2026-09-24 05:48:26', NULL, '2026-09-24 05:43:29', '2026-09-24 05:48:26'),
(2, 'reader@example.com', 'XRxQukCBuQJoO27ZMdRx7k5PU6tH1pTQz06xhlizMoXMLzunwE383VEDoFR8Fbh8', 'subscribed', NULL, '2026-09-24 09:03:58', NULL, 'synced', '2026-09-24 09:04:02', NULL, '2026-09-24 09:03:58', '2026-09-24 09:04:02');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('muzammilken95@gmail.com', '$2y$12$T5tApG1FSpyb644.Gqx8gueIN67mYsd2uffsTc.Et2MCd/CzI09OC', '2026-09-22 07:40:01');

-- --------------------------------------------------------

--
-- Table structure for table `payment_accounts`
--

CREATE TABLE `payment_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_title` varchar(255) NOT NULL,
  `iban` varchar(100) DEFAULT NULL,
  `account_no` varchar(100) NOT NULL,
  `branch_code` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_accounts`
--

INSERT INTO `payment_accounts` (`id`, `bank_name`, `account_title`, `iban`, `account_no`, `branch_code`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Askari Bank updated', 'Muhammad hamamd khan', 'PK68MEZN0000000101234567', '0000000101234567', '0012', 1, 1, 1, '2026-10-02 12:08:00', '2026-10-02 12:08:50'),
(2, 'Askari Bank', 'Muhammad hamamd updated', 'IBAN0001231236548790000', '0001231236548790000', '012', 1, 1, 1, '2026-10-05 11:06:50', '2026-10-05 11:07:02');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin.access', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(2, 'articles.create', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(3, 'articles.delete', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(4, 'articles.edit', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(5, 'articles.publish', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(6, 'articles.related.remove', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(7, 'articles.view', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(8, 'author-settings.edit', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(9, 'author-settings.view', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(10, 'authors.create', 'web', '2026-08-27 04:49:38', '2026-08-27 04:49:38'),
(11, 'authors.delete', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(12, 'authors.edit', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(13, 'authors.view', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(14, 'banners.create', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(15, 'banners.delete', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(16, 'banners.edit', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(17, 'banners.view', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(18, 'categories.create', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(19, 'categories.delete', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(20, 'categories.edit', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(21, 'categories.view', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(22, 'change-password.edit', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(23, 'change-password.view', 'web', '2026-08-27 04:49:39', '2026-08-27 04:49:39'),
(24, 'currency.create', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(25, 'currency.delete', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(26, 'currency.edit', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(27, 'currency.view', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(28, 'dashboard.view', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(29, 'faqs.create', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(30, 'faqs.delete', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(31, 'faqs.edit', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(32, 'faqs.view', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(33, 'general-settings.edit', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(34, 'general-settings.view', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(35, 'magazines.create', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(36, 'magazines.delete', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(37, 'magazines.edit', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(38, 'magazines.pdf.download', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(39, 'magazines.pdf.view', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(40, 'magazines.publish', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(41, 'magazines.related.remove', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(42, 'magazines.view', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(43, 'memberships.create', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(44, 'memberships.delete', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(45, 'memberships.edit', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(46, 'memberships.view', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(47, 'meta-tags.create', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(48, 'meta-tags.delete', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(49, 'meta-tags.edit', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(50, 'meta-tags.view', 'web', '2026-08-27 04:49:40', '2026-08-27 04:49:40'),
(51, 'sliders.create', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(52, 'sliders.delete', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(53, 'sliders.edit', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(54, 'sliders.view', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(55, 'subscription-plans.create', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(56, 'subscription-plans.delete', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(57, 'subscription-plans.edit', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(58, 'subscription-plans.view', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(59, 'tags.create', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(60, 'tags.delete', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(61, 'tags.edit', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(62, 'tags.view', 'web', '2026-08-27 04:49:41', '2026-08-27 04:49:41'),
(63, 'roles.create', 'web', '2026-08-28 04:34:23', '2026-08-28 04:34:23'),
(64, 'roles.delete', 'web', '2026-08-28 04:34:23', '2026-08-28 04:34:23'),
(65, 'roles.edit', 'web', '2026-08-28 04:34:23', '2026-08-28 04:34:23'),
(66, 'roles.view', 'web', '2026-08-28 04:34:23', '2026-08-28 04:34:23'),
(67, 'users.create', 'web', '2026-08-28 04:34:23', '2026-08-28 04:34:23'),
(68, 'users.delete', 'web', '2026-08-28 04:34:23', '2026-08-28 04:34:23'),
(69, 'users.edit', 'web', '2026-08-28 04:34:23', '2026-08-28 04:34:23'),
(70, 'users.view', 'web', '2026-08-28 04:34:23', '2026-08-28 04:34:23'),
(71, 'abouts.create', 'web', '2026-08-29 05:58:04', '2026-08-29 05:58:04'),
(72, 'abouts.delete', 'web', '2026-08-29 05:58:04', '2026-08-29 05:58:04'),
(73, 'abouts.edit', 'web', '2026-08-29 05:58:04', '2026-08-29 05:58:04'),
(74, 'abouts.view', 'web', '2026-08-29 05:58:04', '2026-08-29 05:58:04'),
(75, 'home-cards.create', 'web', '2026-08-29 06:49:44', '2026-08-29 06:49:44'),
(76, 'home-cards.delete', 'web', '2026-08-29 06:49:44', '2026-08-29 06:49:44'),
(77, 'home-cards.edit', 'web', '2026-08-29 06:49:44', '2026-08-29 06:49:44'),
(78, 'home-cards.view', 'web', '2026-08-29 06:49:44', '2026-08-29 06:49:44'),
(79, 'home-sections.create', 'web', '2026-08-31 02:37:23', '2026-08-31 02:37:23'),
(80, 'home-sections.delete', 'web', '2026-08-31 02:37:23', '2026-08-31 02:37:23'),
(81, 'home-sections.edit', 'web', '2026-08-31 02:37:23', '2026-08-31 02:37:23'),
(82, 'home-sections.view', 'web', '2026-08-31 02:37:23', '2026-08-31 02:37:23'),
(83, 'ads.create', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(84, 'ads.delete', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(85, 'ads.edit', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(86, 'ads.view', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(87, 'home-article.edit', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(88, 'home-article.view', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(89, 'home-headings.edit', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(90, 'home-headings.view', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(91, 'taza-shumara.create', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(92, 'taza-shumara.delete', 'web', '2026-09-04 01:50:49', '2026-09-04 01:50:49'),
(93, 'taza-shumara.edit', 'web', '2026-09-04 01:50:50', '2026-09-04 01:50:50'),
(94, 'taza-shumara.view', 'web', '2026-09-04 01:50:50', '2026-09-04 01:50:50'),
(95, 'site-analytics.export', 'web', '2026-09-12 12:18:09', '2026-09-12 12:18:09'),
(96, 'site-analytics.view', 'web', '2026-09-12 12:18:09', '2026-09-12 12:18:09'),
(97, 'subscription-reminder-settings.edit', 'web', '2026-09-12 12:18:09', '2026-09-12 12:18:09'),
(98, 'subscription-reminder-settings.view', 'web', '2026-09-12 12:18:10', '2026-09-12 12:18:10'),
(99, 'ad-requests.edit', 'web', '2026-09-23 04:38:58', '2026-09-23 04:38:58'),
(100, 'ad-requests.view', 'web', '2026-09-23 04:38:58', '2026-09-23 04:38:58'),
(101, 'ask-questions.edit', 'web', '2026-09-23 04:38:58', '2026-09-23 04:38:58'),
(102, 'ask-questions.view', 'web', '2026-09-23 04:38:58', '2026-09-23 04:38:58'),
(103, 'contacts.delete', 'web', '2026-09-23 04:38:58', '2026-09-23 04:38:58'),
(104, 'contacts.view', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(105, 'disclaimer.edit', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(106, 'disclaimer.view', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(107, 'faq-categories.create', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(108, 'faq-categories.delete', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(109, 'faq-categories.edit', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(110, 'faq-categories.view', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(111, 'privacy-policy.edit', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(112, 'privacy-policy.view', 'web', '2026-09-23 04:38:59', '2026-09-23 04:38:59'),
(113, 'terms-conditions.edit', 'web', '2026-09-23 04:39:00', '2026-09-23 04:39:00'),
(114, 'terms-conditions.view', 'web', '2026-09-23 04:39:00', '2026-09-23 04:39:00'),
(115, 'newsletter-campaigns.create', 'web', '2026-10-02 12:05:27', '2026-10-02 12:05:27'),
(116, 'newsletter-campaigns.delete', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(117, 'newsletter-campaigns.edit', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(118, 'newsletter-campaigns.send', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(119, 'newsletter-campaigns.view', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(120, 'newsletter-subscribers.edit', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(121, 'newsletter-subscribers.view', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(122, 'payment-accounts.create', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(123, 'payment-accounts.delete', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(124, 'payment-accounts.edit', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(125, 'payment-accounts.view', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(126, 'services.create', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(127, 'services.delete', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(128, 'services.edit', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(129, 'services.view', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(130, 'user-subscriptions.view', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(131, 'videos.create', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(132, 'videos.delete', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(133, 'videos.edit', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(134, 'videos.view', 'web', '2026-10-02 12:05:28', '2026-10-02 12:05:28'),
(135, 'user-subscriptions.approve', 'web', '2026-10-03 09:18:09', '2026-10-03 09:18:09'),
(136, 'user-subscriptions.reject', 'web', '2026-10-03 09:18:09', '2026-10-03 09:18:09'),
(137, 'consultancies.create', 'web', '2026-10-06 06:50:10', '2026-10-06 06:50:10'),
(138, 'consultancies.delete', 'web', '2026-10-06 06:50:10', '2026-10-06 06:50:10'),
(139, 'consultancies.edit', 'web', '2026-10-06 06:50:10', '2026-10-06 06:50:10'),
(140, 'consultancies.publish', 'web', '2026-10-06 06:50:10', '2026-10-06 06:50:10'),
(141, 'consultancies.related.remove', 'web', '2026-10-06 06:50:10', '2026-10-06 06:50:10'),
(142, 'consultancies.view', 'web', '2026-10-06 06:50:10', '2026-10-06 06:50:10'),
(143, 'videos.publish', 'web', '2026-10-06 06:50:10', '2026-10-06 06:50:10'),
(144, 'courses.create', 'web', '2026-10-06 09:53:47', '2026-10-06 09:53:47'),
(145, 'courses.delete', 'web', '2026-10-06 09:53:47', '2026-10-06 09:53:47'),
(146, 'courses.edit', 'web', '2026-10-06 09:53:47', '2026-10-06 09:53:47'),
(147, 'courses.publish', 'web', '2026-10-06 09:53:47', '2026-10-06 09:53:47'),
(148, 'courses.related.remove', 'web', '2026-10-06 09:53:47', '2026-10-06 09:53:47'),
(149, 'courses.view', 'web', '2026-10-06 09:53:47', '2026-10-06 09:53:47');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 15, 'mobile-app', '489cf786c9c9ea634f45d0ac0bb53f0b3f02d0902818d0027564ffd69e944916', '[\"*\"]', NULL, NULL, '2026-09-19 09:17:08', '2026-09-19 09:17:08'),
(2, 'App\\Models\\User', 14, 'mobile-app', 'fac5e97d32d7091be228f53b1e30f1b413c94ab6ec175796988300983acd3264', '[\"*\"]', NULL, NULL, '2026-09-19 10:08:19', '2026-09-19 10:08:19'),
(3, 'App\\Models\\User', 15, 'mobile-app', '2cff0f50a34bb8e877a1ed5071cdfd4130f0b40d362200b10d6cdf3fbc9d7ac7', '[\"*\"]', '2026-09-19 11:22:57', NULL, '2026-09-19 10:46:45', '2026-09-19 11:22:57'),
(4, 'App\\Models\\User', 14, 'mobile-app', '125017c05e5aabab44c9f660f370acd150c026b25de4028d8cd342240cba2f92', '[\"*\"]', '2026-09-19 11:31:34', NULL, '2026-09-19 11:25:14', '2026-09-19 11:31:34'),
(5, 'App\\Models\\User', 14, 'mobile-app', '7e4816ccd258e36a203fddc229e25eb98639f6a1b3ea176719320eed0ee1e3d4', '[\"*\"]', '2026-09-19 11:43:33', NULL, '2026-09-19 11:33:41', '2026-09-19 11:43:33'),
(6, 'App\\Models\\User', 14, 'mobile-app', '79c50d45922abf7aa9db573c9edde255e6134e5077ffc513e74110651f4ef475', '[\"*\"]', NULL, NULL, '2026-09-22 07:31:03', '2026-09-22 07:31:03'),
(8, 'App\\Models\\User', 14, 'mobile-app', '7f87f33ba8e8d9a3c565c4b384ce4b344c644e936ddc756e3f56b1c8da85bb3b', '[\"*\"]', '2026-09-24 09:34:52', NULL, '2026-09-22 07:43:34', '2026-09-24 09:34:52'),
(9, 'App\\Models\\User', 14, 'mobile-app', 'a1baa4e4246981b0d6e62f6cfeaae637392b9e2e0b2890999e55f4cba43cac46', '[\"*\"]', '2026-09-24 09:53:59', NULL, '2026-09-24 06:42:35', '2026-09-24 09:53:59');

-- --------------------------------------------------------

--
-- Table structure for table `related_articles`
--

CREATE TABLE `related_articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `related_article_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `related_consultancies`
--

CREATE TABLE `related_consultancies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `consultancy_id` bigint(20) UNSIGNED NOT NULL,
  `related_consultancy_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `related_consultancies`
--

INSERT INTO `related_consultancies` (`id`, `consultancy_id`, `related_consultancy_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-10-06 07:21:02', '2026-10-06 07:21:02'),
(2, 3, 2, '2026-10-06 07:24:06', '2026-10-06 07:24:06'),
(3, 3, 1, '2026-10-06 07:24:06', '2026-10-06 07:24:06');

-- --------------------------------------------------------

--
-- Table structure for table `related_courses`
--

CREATE TABLE `related_courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `related_course_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `related_magazines`
--

CREATE TABLE `related_magazines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `magazine_id` bigint(20) UNSIGNED NOT NULL,
  `related_magazine_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `related_magazines`
--

INSERT INTO `related_magazines` (`id`, `magazine_id`, `related_magazine_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-08-22 05:32:51', '2026-08-22 05:32:51'),
(2, 1, 2, '2026-08-22 05:34:01', '2026-08-22 05:34:01');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2026-08-18 10:14:35', '2026-08-18 10:14:35'),
(2, 'user', 'web', '2026-08-18 10:14:35', '2026-08-18 10:14:35'),
(3, 'consultant', 'web', '2026-08-18 10:14:35', '2026-08-18 10:14:35'),
(4, 'Content Manager', 'web', '2026-08-27 05:31:27', '2026-08-27 05:31:27'),
(5, 'Content Editor', 'web', '2026-08-28 04:39:30', '2026-08-28 04:39:30'),
(6, 'App Name', 'web', '2026-10-05 11:00:53', '2026-10-05 11:01:19');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 4),
(1, 5),
(1, 6),
(2, 4),
(2, 5),
(3, 4),
(4, 4),
(4, 5),
(5, 4),
(5, 5),
(7, 4),
(7, 5),
(10, 4),
(11, 4),
(12, 4),
(13, 4),
(14, 6),
(15, 6),
(16, 6),
(17, 6),
(18, 4),
(19, 4),
(20, 4),
(21, 4),
(22, 4),
(22, 5),
(23, 4),
(23, 5),
(28, 4),
(28, 5),
(28, 6),
(35, 4),
(35, 5),
(36, 4),
(37, 4),
(37, 5),
(38, 4),
(38, 5),
(39, 4),
(39, 5),
(40, 4),
(40, 5),
(42, 4),
(42, 5),
(51, 6),
(52, 6),
(53, 6),
(54, 6),
(59, 4),
(60, 4),
(61, 4),
(62, 4),
(63, 4),
(64, 4),
(65, 4),
(66, 4),
(67, 4),
(68, 4),
(69, 4),
(70, 4),
(75, 6),
(76, 6),
(77, 6),
(78, 6),
(79, 6),
(80, 6),
(81, 6),
(82, 6),
(87, 6),
(88, 6),
(89, 6),
(90, 6),
(91, 6),
(92, 6),
(93, 6),
(94, 6),
(126, 6),
(127, 6),
(128, 6),
(129, 6);

-- --------------------------------------------------------

--
-- Table structure for table `search_contents`
--

CREATE TABLE `search_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content_type` varchar(255) NOT NULL,
  `content_id` bigint(20) UNSIGNED NOT NULL,
  `search_keyword` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `search_contents`
--

INSERT INTO `search_contents` (`id`, `content_type`, `content_id`, `search_keyword`, `created_at`) VALUES
(1, 'App\\Models\\Magazine', 1, 'کاروباری', '2026-09-14 11:40:46'),
(2, 'App\\Models\\Article', 2, 'کاروباری', '2026-09-14 11:41:03'),
(3, 'App\\Models\\Article', 1, 'کاروباری', '2026-09-14 11:41:09'),
(4, 'App\\Models\\Magazine', 2, 'مستقل', '2026-09-14 11:42:26'),
(5, 'App\\Models\\Article', 4, 'مستقل', '2026-09-14 11:42:47'),
(6, 'App\\Models\\Article', 4, 'مستقل', '2026-09-14 11:43:56'),
(7, 'App\\Models\\Article', 2, 'تعلیم', '2026-09-19 06:05:44'),
(8, 'App\\Models\\Magazine', 2, 'تعلیم', '2026-09-19 06:06:43'),
(9, 'App\\Models\\Magazine', 2, 'تعلیم', '2026-09-22 07:07:57'),
(10, 'App\\Models\\Article', 2, 'تعلیم', '2026-09-22 07:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `image`, `description`, `isactive`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Anxiety  Therapy', 'backend-images/services/01M3XQ6NHYZ0GQZNSQ3N9N65DZ.webp', 'Anxiety can affect your sleep, focus, relationships, and everyday life. I’ll help you understand what’s behind it, manage anxious thoughts, and feel more in control.updated', 1, 1, 1, '2026-10-02 07:10:35', '2026-10-02 07:10:52');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7s92aZnrSKIxkywNrdCpTJVQaF7kQvY0c5RPpfqo', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'eyJfdG9rZW4iOiJMU2FsalFIUTRIQ1NQa21zcFlXNXRuSnJPaUZDcWw2S1BPSVBSMTVqIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hZG1pblwvY291cnNlIiwicm91dGUiOiJhZG1pbi5jb3Vyc2UuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1791283848),
('sxJdDhQ6PXwe6wS2EHGLbu9tDJm9iYRIhbbcbkRQ', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJTZ0IxcmVSMjR4SnJuY1lNM0Nyb1JTQ3FuRlNzYTR5NFVqN0k1R0hQIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2NvdXJzZXNcLzEiLCJyb3V0ZSI6ImZyb250LmNvdXJzZXMuc2hvdyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MTR9', 1791282999),
('yine3tSGnJXr1sMCK5fAAGaY8ZNPHlXagODx2rvh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'eyJfdG9rZW4iOiJHWkpJMnRkc2dLampxdkU5RVFYYVE3RUtIYXJIN1lSWTBIeEp5NTdQIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOiJmcm9udGVuZC5ob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1791202495),
('ZSfT2jdIsi6teaYQXMDqrcQxunF7IIiCSHElhCon', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIwWnRTaDA3aXduT0thMVJ2UHdkWDN0dmRNMEMwZ0I2aFJMZ3FYekFXIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2FkbWluXC9zZXJ2aWNlcyIsInJvdXRlIjoiYWRtaW4uc2VydmljZXMuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', 1791201896);

-- --------------------------------------------------------

--
-- Table structure for table `site_visits`
--

CREATE TABLE `site_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visitor_id` char(36) NOT NULL,
  `public_token_hash` varchar(64) NOT NULL,
  `page_key` varchar(80) NOT NULL,
  `route_name` varchar(255) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `referrer` text DEFAULT NULL,
  `visitable_type` varchar(255) DEFAULT NULL,
  `visitable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `device_type` varchar(30) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `browser_version` varchar(40) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `os_version` varchar(40) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `duration_seconds` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_visits`
--

INSERT INTO `site_visits` (`id`, `user_id`, `visitor_id`, `public_token_hash`, `page_key`, `route_name`, `url`, `referrer`, `visitable_type`, `visitable_id`, `ip_address`, `country`, `country_code`, `city`, `region`, `latitude`, `longitude`, `device`, `device_type`, `browser`, `browser_version`, `os`, `os_version`, `user_agent`, `started_at`, `last_activity_at`, `duration_seconds`, `ended_at`, `created_at`, `updated_at`) VALUES
(1, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'c9b0b167499d2c3d164e42e0853dcd99e7db3e3e1a57d7209a170a4d07d94c52', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/account/bookmarks', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:03:27', '2026-09-12 12:03:55', 3, '2026-09-12 12:03:55', '2026-09-12 12:03:27', '2026-09-12 12:03:55'),
(2, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e6d9208ebf650c5f6efa275acd66f941d5b3fbe3e7130dda6bf21aff3596ec1a', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:03:53', '2026-09-12 12:07:15', 15, '2026-09-12 12:07:15', '2026-09-12 12:03:53', '2026-09-12 12:07:15'),
(3, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '7e465d3c6cdd30eaea56c275388ab6e388f56e52025f0dbcc67a21c24b0f1219', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/4/mstkl-hl-pr-tog-dyna', 'http://localhost/mazameen', 'App\\Models\\Article', 4, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:06:52', '2026-09-12 12:07:26', 4, '2026-09-12 12:07:26', '2026-09-12 12:06:52', '2026-09-12 12:07:26'),
(4, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '9ea0c52b5dfa77ebcb6eb56a8fcbc1f44284f599143aeddea478f72a799edb42', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:06:54', '2026-09-12 12:07:57', 11, NULL, '2026-09-12 12:06:54', '2026-09-12 12:07:57'),
(5, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'a32161ba3250558858975e52d48e6bf7b071c91c38ef0884a298d340c737625f', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:06:58', NULL, 0, NULL, '2026-09-12 12:06:58', '2026-09-12 12:06:58'),
(6, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '8cb75b3bed5dcf73e7c3d9f74ce415ed7d55001daa5ebcbf8fbc097c8007cf1f', 'categories', 'front.mozoaat', 'http://localhost:8000/mozoaat', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:07:01', NULL, 0, NULL, '2026-09-12 12:07:01', '2026-09-12 12:07:01'),
(7, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '32194fb01ce53798186ee238984b4a2b5fc6b2f9e223e01cfec4433480327cde', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/2/aslamy-taalym', 'http://localhost/mazameen', 'App\\Models\\Category', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:07:02', NULL, 0, NULL, '2026-09-12 12:07:02', '2026-09-12 12:07:02'),
(8, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f6311f5dfb720c8caaeea529c288fb021512af9d040c043216d2a26c5f3fe4f2', 'authors', 'front.mazmoon-nigaar', 'http://localhost:8000/mazmoon-nigaar', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:07:04', NULL, 0, NULL, '2026-09-12 12:07:04', '2026-09-12 12:07:04'),
(9, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'c1b33d46aa3aa7f8a1ac34ffeef3d6b37a639e1fe0fcd2443c63faac03585248', 'about', 'front.taaruf', 'http://localhost:8000/taaruf', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:07:06', '2026-09-12 12:07:18', 6, '2026-09-12 12:07:18', '2026-09-12 12:07:06', '2026-09-12 12:07:18'),
(10, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '6534577c3d4d66f74221b8fd2fb7cdb2ff08a0370404c614e4638633f83267bc', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:08:29', NULL, 0, NULL, '2026-09-12 12:08:29', '2026-09-12 12:08:29'),
(11, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'eb5a367484b7da2ddbcd3d26368ff698f9f958927fb82217c9589799a9a8e118', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:08:31', '2026-09-12 12:09:41', 1, '2026-09-12 12:09:41', '2026-09-12 12:08:31', '2026-09-12 12:09:41'),
(12, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '266b0a2f7bd68e554f39d87249be43e0f4e71d213916d61d3d82a05343e5206a', 'authors', 'front.mazmoon-nigaar', 'http://localhost:8000/mazmoon-nigaar', 'http://localhost/mazmoon-nigaar', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:08:32', NULL, 0, NULL, '2026-09-12 12:08:32', '2026-09-12 12:08:32'),
(13, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '45452adcdeffc24ee84ff6b8d9fa6b53bac88e580415e9d1724b0df6d8f6916e', 'categories', 'front.mozoaat', 'http://localhost:8000/mozoaat', 'http://localhost/mozoaat', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:08:34', NULL, 0, NULL, '2026-09-12 12:08:34', '2026-09-12 12:08:34'),
(14, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '4ec6fbc2ae0c83eef07f1545812cf88012ff9afe488853a763ba0b9acbfb99a0', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/2/aslamy-taalym', 'http://localhost/mozu/2/aslamy-taalym', 'App\\Models\\Category', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:08:35', NULL, 0, NULL, '2026-09-12 12:08:35', '2026-09-12 12:08:35'),
(15, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '8938f08a4d02f991175550d1d5e6b801c0930531ac8298a677c85c63bdf07a3f', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:08:36', NULL, 0, NULL, '2026-09-12 12:08:36', '2026-09-12 12:08:36'),
(16, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f8259500d8a3c4789dcdba33db527c0ff49cbb9752b81cae5a4f3c1a912dfc1b', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:14', NULL, 0, NULL, '2026-09-12 12:09:14', '2026-09-12 12:09:14'),
(17, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '028e10f32b7319a1de6ff7b2ae72f4e01cfbb553045f4299b897a9f010212efc', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:15', '2026-09-12 12:09:29', 2, NULL, '2026-09-12 12:09:15', '2026-09-12 12:09:29'),
(18, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '78c96ec214c155a3b038fc1916175c4b67e1491b3d7a6115d3fe0e2dcd71e4bd', 'authors', 'front.mazmoon-nigaar', 'http://localhost:8000/mazmoon-nigaar', 'http://localhost/mazmoon-nigaar', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:17', '2026-09-12 12:09:46', 4, '2026-09-12 12:09:46', '2026-09-12 12:09:17', '2026-09-12 12:09:46'),
(19, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd9942681149a20bc95333714e1ec96ad09ccd61e2409d1342ffc9a1e18fce958', 'categories', 'front.mozoaat', 'http://localhost:8000/mozoaat', 'http://localhost/mozoaat', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:18', '2026-09-12 12:09:35', 1, NULL, '2026-09-12 12:09:18', '2026-09-12 12:09:35'),
(20, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '054eedd551d5d392d038edce8d9c18d8954a75498377a57056f4463b718f869a', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/2/aslamy-taalym', 'http://localhost/mozu/2/aslamy-taalym', 'App\\Models\\Category', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:19', '2026-09-12 12:09:43', 6, '2026-09-12 12:09:43', '2026-09-12 12:09:19', '2026-09-12 12:09:43'),
(21, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '80d3b8bcb617f296a907d5100818b7974a87c3d0bcf1c37762a5681da61b294a', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/sabqa-shumare', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:21', NULL, 0, NULL, '2026-09-12 12:09:21', '2026-09-12 12:09:21'),
(22, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e458138a3eb3ea43a0bf1d50950dd8a7e3405efe0281db5ac6910e97c3379360', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:23', NULL, 0, NULL, '2026-09-12 12:09:23', '2026-09-12 12:09:23'),
(23, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e79fecbea05f3a2672ca130f5c165f0dcf55e381e506a2c92f328cd53091ee98', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/2/mstkl-hl-pr-tog-dyna', 'http://localhost/sabqa-shumare', 'App\\Models\\Magazine', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:25', NULL, 0, NULL, '2026-09-12 12:09:25', '2026-09-12 12:09:25'),
(24, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd7742aa56120b0dddb942205bd87506a80f9cc193e432ff3c1c6860256644531', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/3/yknalogy', 'http://localhost/mozoaat', 'App\\Models\\Category', 3, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:31', '2026-09-12 12:10:01', 5, NULL, '2026-09-12 12:09:31', '2026-09-12 12:10:01'),
(25, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '6ca3610fea00a006123dd371b23b3be2fb9c7058a8cf5d9b4a481f828a0e5e75', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/2/aslamy-taalym', 'http://localhost/mozu/2/aslamy-taalym', 'App\\Models\\Category', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:37', NULL, 0, NULL, '2026-09-12 12:09:37', '2026-09-12 12:09:37'),
(26, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '5bbe0064b9439da3df51f2203179c6571fca841390f2034007ddd6158f836d15', 'author-detail', 'front.mazmoon-nigaar.detail', 'http://localhost:8000/mazmoon-nigaar/1/%D9%88%D8%AC%D8%A7%DB%81%D8%AA-%D8%B4%DB%8C%D8%AE', 'http://localhost/mazmoon-nigaar', 'App\\Models\\Author', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:09:44', '2026-09-12 12:09:53', 5, '2026-09-12 12:09:53', '2026-09-12 12:09:44', '2026-09-12 12:09:53'),
(27, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'bcaa4736c4bd136356b5ef8476af7d3e9f8c32ace51c6870eed739448748c7d8', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/1/karobar', 'http://localhost/mozu/3/yknalogy', 'App\\Models\\Category', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:10:00', '2026-09-12 12:10:07', 5, '2026-09-12 12:10:07', '2026-09-12 12:10:00', '2026-09-12 12:10:07'),
(28, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd9444045c3cbd4e3b5d5feb6f2c365c144be45630ac6c1f5abb01076f22cc359', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/1/karobar', 'http://localhost/mozu/1/karobar', 'App\\Models\\Category', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:10:06', '2026-09-12 12:10:10', 1, '2026-09-12 12:10:10', '2026-09-12 12:10:06', '2026-09-12 12:10:10'),
(29, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '77f35a8453d41a93299172e489e02a56112509fdaa17faeefd54e31b71895da2', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/shumara-detail/2/mstkl-hl-pr-tog-dyna', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:10:15', '2026-09-12 12:10:56', 3, NULL, '2026-09-12 12:10:15', '2026-09-12 12:10:56'),
(30, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '45455ddc699a65e71739a0978fddb8492036c4e9c4793324f57ff4c38736ed52', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:10:59', '2026-09-12 12:11:05', 1, NULL, '2026-09-12 12:10:59', '2026-09-12 12:11:05'),
(31, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'c8f3a678fa461bd26a6aa32e130c9cdf56ec09fa50bf9ef2603881b4ff7e7931', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 04:28:37', '2026-09-14 04:32:03', 23, NULL, '2026-09-14 04:28:37', '2026-09-14 04:32:03'),
(32, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'c395e6d675cc8296607ffca1331a6ecb738ca0a95a8a681cb4d5bab21e0e7cca', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 06:23:35', '2026-09-14 06:36:24', 8, '2026-09-14 06:36:24', '2026-09-14 06:23:35', '2026-09-14 06:36:24'),
(33, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '883ea544f91baaae64d291d7db407ff7590e3f832cf8ee6dd8a79bab00383374', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 06:36:14', '2026-09-14 06:38:49', 15, NULL, '2026-09-14 06:36:14', '2026-09-14 06:38:49'),
(34, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '2dfa59e805793da5c2ac6975c7ffea197c65f29d20ba03f6c50eef0b8d7474b5', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 06:36:17', '2026-09-14 06:36:46', 4, '2026-09-14 06:36:46', '2026-09-14 06:36:17', '2026-09-14 06:36:46'),
(35, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f61dfae58a759b12c9b52308e988b0a5bffa9b6be63458d548674b643dac37ac', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 06:36:20', '2026-09-14 06:36:27', 3, '2026-09-14 06:36:27', '2026-09-14 06:36:20', '2026-09-14 06:36:27'),
(36, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '90d71c3c6c89c6674cb534a6e4a280119b02d84dd721dbbd57c686906bfa9012', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', 'http://localhost/mazameen', 'App\\Models\\Article', 3, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 06:36:26', '2026-09-14 06:42:12', 14, '2026-09-14 06:42:12', '2026-09-14 06:36:26', '2026-09-14 06:42:12'),
(37, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd266a6ddc44f03e4f083ae6981e72ba2ce527716019493092237843b928b6706', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/sabqa-shumare', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 06:36:44', '2026-09-14 06:38:37', 19, NULL, '2026-09-14 06:36:44', '2026-09-14 06:38:37'),
(38, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '0373326c6bb8b1c5aa3d0a710c0480934272219d7d32f8f3a75912aac9906c82', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 06:42:09', '2026-09-14 06:45:21', 32, '2026-09-14 06:45:21', '2026-09-14 06:42:09', '2026-09-14 06:45:21'),
(39, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '4bf5505b94f6d8aca7b7c759e3d12f70beaacc5b712f505a762425c01fcbc0ee', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/forgot-password', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 07:13:12', '2026-09-14 07:17:02', 11, NULL, '2026-09-14 07:13:12', '2026-09-14 07:17:02'),
(40, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '488a5224d1a75d37ebea7efeeff5ceafdeb8f009b98da033c22c245df78f5965', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 07:27:14', '2026-09-14 07:48:15', 15, NULL, '2026-09-14 07:27:14', '2026-09-14 07:48:15'),
(41, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd907502c19ef523aa1fe86188caf72306296801387efa8c8dcb1475ddee1aa12', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 07:48:19', '2026-09-14 07:49:12', 5, '2026-09-14 07:49:12', '2026-09-14 07:48:19', '2026-09-14 07:49:12'),
(42, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '371ddad04db6a89e5c1fb0434607e04fa0f8c5af3cfa428f920126e39cd790f4', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 07:49:11', '2026-09-14 07:51:52', 10, '2026-09-14 07:51:52', '2026-09-14 07:49:11', '2026-09-14 07:51:52'),
(43, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'fa3d9f8c16ebef002b9bb7336e4d256bf3afeecdbc1fc684e77ee179dd408990', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/taza-shumara', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 07:51:50', '2026-09-14 07:52:00', 5, NULL, '2026-09-14 07:51:50', '2026-09-14 07:52:00'),
(44, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '3dacb5536d8f766f135989a807ad4e1da96b6d30321eb159d65629bf40674721', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 07:58:32', NULL, 0, NULL, '2026-09-14 07:58:32', '2026-09-14 07:58:32'),
(45, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e984d156a1bcd2df0231a34d7bd3de26fb6c7483d19aeec04e1bc5979db6353d', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 07:58:36', NULL, 0, NULL, '2026-09-14 07:58:36', '2026-09-14 07:58:36'),
(46, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '818fdd0103b4c6ff8ccc920d82c9aea18e9e618e2b8c426181da8fe546960319', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:23:06', '2026-09-14 08:24:10', 3, NULL, '2026-09-14 08:23:06', '2026-09-14 08:24:10'),
(47, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'fe17350a8295b69584930dbeb1c1c599135c5910374072678a3cf87d761edcdd', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/1/%DA%A9%D8%A7%D8%B1%D9%88%D8%A8%D8%A7%D8%B1', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Category', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:02', '2026-09-14 08:24:16', 1, '2026-09-14 08:24:16', '2026-09-14 08:24:02', '2026-09-14 08:24:16'),
(48, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'c3eba55aef72462307ef12c31ea9d53df0800701e3999165d6ae65bce425b4c3', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:04', NULL, 0, NULL, '2026-09-14 08:24:04', '2026-09-14 08:24:04'),
(49, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '98793167b37061166a86dc95de0b5c9fc7790f74b37eee35e10648a3e6e1a72a', 'author-detail', 'front.mazmoon-nigaar.detail', 'http://localhost:8000/mazmoon-nigaar/1/%D9%88%D8%AC%D8%A7%DB%81%D8%AA%20%D8%B4%DB%8C%D8%AE', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Author', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:07', '2026-09-14 08:24:20', 4, '2026-09-14 08:24:20', '2026-09-14 08:24:07', '2026-09-14 08:24:20'),
(50, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '85a9ba8f7acc1a7a85f0ca397889a8eb585bfe99b7e61997dd59d9dec5412884', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazmoon-nigaar/1/%D9%88%D8%AC%D8%A7%DB%81%D8%AA%20%D8%B4%DB%8C%D8%AE', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:19', '2026-09-14 08:24:27', 4, '2026-09-14 08:24:27', '2026-09-14 08:24:19', '2026-09-14 08:24:27'),
(51, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'a773abddeb6c825775bc6e160b3bbc70e26e31d3427c8202857e0c0945eae256', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:26', '2026-09-14 08:24:31', 3, '2026-09-14 08:24:31', '2026-09-14 08:24:26', '2026-09-14 08:24:31'),
(52, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd9cb1a09eebd9850d55552552de5fdbbb371470ee3cecbddf028a0f9c4fc2104', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', 'http://localhost/mazameen', 'App\\Models\\Article', 3, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:29', '2026-09-14 08:24:39', 4, '2026-09-14 08:24:39', '2026-09-14 08:24:29', '2026-09-14 08:24:39'),
(53, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '98eeaa0d7352c026e3728f3c9472d6f3442be6e0d21f944c0928736685601e35', 'author-detail', 'front.mazmoon-nigaar.detail', 'http://localhost:8000/mazmoon-nigaar/2/%D9%86%D8%A8%DB%8C%D9%84%20%D8%A7%D8%AD%D9%85%D8%AF', 'http://localhost/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', 'App\\Models\\Author', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:33', '2026-09-14 08:24:44', 2, '2026-09-14 08:24:44', '2026-09-14 08:24:33', '2026-09-14 08:24:44'),
(54, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '4912732ff9a49394c90b082855e793005ca144f6c464295a9719f5b288697df9', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/1/%DA%A9%D8%A7%D8%B1%D9%88%D8%A8%D8%A7%D8%B1', 'http://localhost/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', 'App\\Models\\Category', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:35', '2026-09-14 08:24:46', 1, '2026-09-14 08:24:46', '2026-09-14 08:24:35', '2026-09-14 08:24:46'),
(55, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e45ff593b1a7d040bb2ed14de02a5e1f69d51ba6f34f9a84d795f49ad3c8b978', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:36', '2026-09-14 08:24:51', 4, '2026-09-14 08:24:51', '2026-09-14 08:24:36', '2026-09-14 08:24:51'),
(56, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '839c0f5525ca82674902994da401758c4b848c935e448b24791f2fd4963d4a1c', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:49', '2026-09-14 08:25:03', 5, '2026-09-14 08:25:03', '2026-09-14 08:24:49', '2026-09-14 08:25:03'),
(57, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '6499edc0e17f965537aedf7136799794c9b211b91046c71c59ffd47ea5123572', 'author-detail', 'front.mazmoon-nigaar.detail', 'http://localhost:8000/mazmoon-nigaar/1/%D9%88%D8%AC%D8%A7%DB%81%D8%AA%20%D8%B4%DB%8C%D8%AE', 'http://localhost/taza-shumara', 'App\\Models\\Author', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:56', '2026-09-14 08:25:12', 1, NULL, '2026-09-14 08:24:56', '2026-09-14 08:25:12'),
(58, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '525f3c7e94422a9e96d16979373c8dc999aa01db6ab365b0d8c82dd95e6a6bfb', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/1/%DA%A9%D8%A7%D8%B1%D9%88%D8%A8%D8%A7%D8%B1', 'http://localhost/taza-shumara', 'App\\Models\\Category', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:24:58', '2026-09-14 08:25:15', 2, '2026-09-14 08:25:15', '2026-09-14 08:24:58', '2026-09-14 08:25:15'),
(59, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '2d8e4f465baad153a31b01dbee16f4217e33effde67f0cd6b47f7dd4063a4b2e', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:25:00', '2026-09-14 08:25:07', 3, '2026-09-14 08:25:07', '2026-09-14 08:25:00', '2026-09-14 08:25:07'),
(60, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'bde2dd48db7d2fbaf2e888c5826f6a5252fbb3b9b8823125617f58758374e600', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:25:06', '2026-09-14 08:25:19', 5, '2026-09-14 08:25:19', '2026-09-14 08:25:06', '2026-09-14 08:25:19'),
(61, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '0c5ae8f6a127e6f46a529e38c0298d4fd147ad7fd29722aae7358c32941542df', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:25:18', '2026-09-14 08:53:49', 14, '2026-09-14 08:53:49', '2026-09-14 08:25:18', '2026-09-14 08:53:49'),
(62, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '19dbc58c7568910a85a8b674fdf60ab3d4d09c2a9c4344bb9cc2258f6b82572b', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:53:48', '2026-09-14 08:54:16', 6, '2026-09-14 08:54:16', '2026-09-14 08:53:48', '2026-09-14 08:54:16'),
(63, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f7b36a3f9588faf233dcc1ad3a1dd6d0fbec4d89f724df099fdad7f5e854221c', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', 'http://localhost/mazameen', 'App\\Models\\Article', 3, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:53:53', '2026-09-14 08:54:12', 10, NULL, '2026-09-14 08:53:53', '2026-09-14 08:54:12'),
(64, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'ae56139a38b10f1b7a3de35a619e9016f0fa88b7e90878bb6b90656da2b262b0', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:54:15', '2026-09-14 09:01:17', 35, NULL, '2026-09-14 08:54:15', '2026-09-14 09:01:17'),
(65, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'cfe794df394913f9eeb1e70ef1bde7af814a50f958345bd840562f3fa043a31d', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/3/yknalogy', 'http://localhost/', 'App\\Models\\Category', 3, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:54:23', '2026-09-14 08:54:27', 1, '2026-09-14 08:54:27', '2026-09-14 08:54:23', '2026-09-14 08:54:27'),
(66, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '4f9716a93b0fd2c7642fca5339092a9e0595632bf0fb766262dd9cb5faf38877', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:54:33', '2026-09-14 08:54:38', 1, '2026-09-14 08:54:38', '2026-09-14 08:54:33', '2026-09-14 08:54:38'),
(67, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '00f8317694f7b355074a3b2e75c62c4d6a16e0ca27c555bd94778b880114089e', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 08:54:49', '2026-09-14 08:54:58', 4, '2026-09-14 08:54:58', '2026-09-14 08:54:49', '2026-09-14 08:54:58'),
(68, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '45a4d186a48cba4ef2468752f3c135ea0c78aa2dc4c10e45e3cb45cd5238ab9b', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:01:16', '2026-09-14 09:01:24', 3, '2026-09-14 09:01:24', '2026-09-14 09:01:16', '2026-09-14 09:01:24'),
(69, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '74db4c96c2f1f3428813088c89e7f2945d2cd0c60950b784a30b0ec29d16957c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:01:20', '2026-09-14 09:01:39', 11, '2026-09-14 09:01:39', '2026-09-14 09:01:20', '2026-09-14 09:01:39'),
(70, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '2e4f93fa4236a840d03e1f8c18aa0c074f9b11916301971bc11a0aebe4bba79f', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:01:38', '2026-09-14 09:01:47', 2, NULL, '2026-09-14 09:01:38', '2026-09-14 09:01:47'),
(71, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'bb46ef43f70e5fc5726961de9846a5f7054c9890be6665aea02fde805ca7c944', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:01:44', '2026-09-14 09:01:57', 5, NULL, '2026-09-14 09:01:44', '2026-09-14 09:01:57'),
(72, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'fbf92814ae53cff0f3590184a9f0a40c4e8ddb2402e3cb63508358348655eea5', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:02:00', '2026-09-14 09:02:11', 3, '2026-09-14 09:02:11', '2026-09-14 09:02:00', '2026-09-14 09:02:11'),
(73, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '24f5cb33981172f4850fe231f8382899e20b3f7ac6b6d2e61f82002cb1028fc7', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:02:05', '2026-09-14 09:02:13', 5, '2026-09-14 09:02:13', '2026-09-14 09:02:05', '2026-09-14 09:02:13'),
(74, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '96913408a2a2a6c11dc8c282aed7c7110d4257c6f710b159dda0752e11ed2736', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:02:07', '2026-09-14 09:07:08', 17, NULL, '2026-09-14 09:02:07', '2026-09-14 09:07:08'),
(75, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '3532db955e082b72dd8affe74e14e9a07bf1b60a028e950e3d6a7c8d95cfb609', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/1/karobary-mshklat-ka-hl', 'http://localhost/mazameen', 'App\\Models\\Article', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:03:31', '2026-09-14 09:07:13', 14, NULL, '2026-09-14 09:03:31', '2026-09-14 09:07:13'),
(76, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '57bee14b370ab702370d0e22b9247d1a0932250bc6b2ddb4240a3968d11a5fbf', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:17:08', NULL, 0, NULL, '2026-09-14 09:17:08', '2026-09-14 09:17:08'),
(77, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '99e00fed4b1698491e54961b76b1d9534a61af92f1d865c4a2566390a58da514', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/1/karobary-mshklat-ka-hl', 'http://localhost/mazmoon/1/karobary-mshklat-ka-hl', 'App\\Models\\Article', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:17:10', NULL, 0, NULL, '2026-09-14 09:17:10', '2026-09-14 09:17:10'),
(78, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '0f24d13506216df8852fe736d2194220006f1088cfdcd94bdda03ef445b4e597', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/1/karobary-mshklat-ka-hl', 'http://localhost/mazmoon/1/karobary-mshklat-ka-hl', 'App\\Models\\Article', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:17:38', '2026-09-14 09:20:37', 2, '2026-09-14 09:20:37', '2026-09-14 09:17:38', '2026-09-14 09:20:37'),
(79, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'b0417be1606ec45c8c2e256dad46b8be2a6299a7abd0922488ed431f354edd02', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:17:40', '2026-09-14 09:20:32', 2, NULL, '2026-09-14 09:17:40', '2026-09-14 09:20:32'),
(80, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'df3bff4b17e82c304e4801a3b6da58543a604c803a77afc725ce952aad58bebd', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:20:30', '2026-09-14 09:21:05', 1, '2026-09-14 09:21:05', '2026-09-14 09:20:30', '2026-09-14 09:21:05'),
(81, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '5437d4214620449413b0dcbb71ae0334cdb9c0e4295abbf77b53e4b1e5110b9a', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/1/karobary-mshklat-ka-hl', 'http://localhost/mazmoon/1/karobary-mshklat-ka-hl', 'App\\Models\\Article', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:20:32', '2026-09-14 09:21:01', 2, NULL, '2026-09-14 09:20:32', '2026-09-14 09:21:01'),
(82, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '6667b1a23800ebe19e28c0847f1c85b411e62e596b8b24436612b692109478fb', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/1/karobary-mshklat-ka-hl', 'http://localhost/mazmoon/1/karobary-mshklat-ka-hl', 'App\\Models\\Article', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:21:00', '2026-09-14 09:21:20', 1, '2026-09-14 09:21:20', '2026-09-14 09:21:00', '2026-09-14 09:21:20'),
(83, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '18f793fd88ad3274c52c79d3fa166149062207d0270d43d3d56f7ba7aa367101', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:21:02', '2026-09-14 09:21:14', 3, '2026-09-14 09:21:14', '2026-09-14 09:21:02', '2026-09-14 09:21:14'),
(84, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '38d09f202d7f151d5cc0421266ce19ed1f67003e1fa6077036d26801c36f6d1e', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:21:13', '2026-09-14 09:21:24', 4, '2026-09-14 09:21:24', '2026-09-14 09:21:13', '2026-09-14 09:21:24');
INSERT INTO `site_visits` (`id`, `user_id`, `visitor_id`, `public_token_hash`, `page_key`, `route_name`, `url`, `referrer`, `visitable_type`, `visitable_id`, `ip_address`, `country`, `country_code`, `city`, `region`, `latitude`, `longitude`, `device`, `device_type`, `browser`, `browser_version`, `os`, `os_version`, `user_agent`, `started_at`, `last_activity_at`, `duration_seconds`, `ended_at`, `created_at`, `updated_at`) VALUES
(85, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '4b29378eaec18ed8cdcfaadd3a84d6cb2cbee6f6419cf5d935ead012dea3ed89', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/1/karobary-mshklat-ka-hl', 'http://localhost/mazmoon/1/karobary-mshklat-ka-hl', 'App\\Models\\Article', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:21:19', '2026-09-14 09:22:47', 7, '2026-09-14 09:22:47', '2026-09-14 09:21:19', '2026-09-14 09:22:47'),
(86, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd180612e3501b440e4085b01666bc96651a9f68a4d6ad2b94c6f485035a0ef78', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:21:23', '2026-09-14 09:23:12', 6, '2026-09-14 09:23:12', '2026-09-14 09:21:23', '2026-09-14 09:23:12'),
(87, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '5f781670088b4ab730a4d3fbbef856d727dc512fac71dbd41b5ed447fa3b5bd8', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/mazmoon/1/karobary-mshklat-ka-hl', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:22:46', '2026-09-14 09:23:00', 9, '2026-09-14 09:23:00', '2026-09-14 09:22:46', '2026-09-14 09:23:00'),
(88, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '9a0bd0ffb66c5546f1f671cebff1442b4d8c5c1b4002a612dc2b7cf344c4ee9d', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/sabqa-shumare', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:22:59', '2026-09-14 09:29:19', 15, NULL, '2026-09-14 09:22:59', '2026-09-14 09:29:19'),
(89, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '8609dcb08932468fdaa6bd16b0b09b3b23bce5611c065ef7e4275a51cc4707b2', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:23:11', '2026-09-14 09:23:24', 4, NULL, '2026-09-14 09:23:11', '2026-09-14 09:23:24'),
(90, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'a9625c82fc39b05eacd939aa9ddaf843fa4372565be7abb35ef3d4c482d13b8a', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:37:17', NULL, 0, NULL, '2026-09-14 09:37:17', '2026-09-14 09:37:17'),
(91, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'ae903bc16298a8f0fee47672fcc9d81847756a895efd5e7243b31e79be35aa05', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:37:55', NULL, 0, NULL, '2026-09-14 09:37:55', '2026-09-14 09:37:55'),
(92, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd2f33af5657adf4081583bdf7b02eab120b939857c40d2d50adfd95a94d11350', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:38:11', '2026-09-14 09:38:51', 2, NULL, '2026-09-14 09:38:11', '2026-09-14 09:38:51'),
(93, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '3fbd443fde2a3bb3d1413cb67c7da0a4151dc5e03ae31a7bdd09f20928e0a0e1', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:43:05', NULL, 0, NULL, '2026-09-14 09:43:05', '2026-09-14 09:43:05'),
(94, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'c594425d97523a1f7b8dcacee1535b6b938327e2bf8822abbd2b245ab1424880', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:43:07', '2026-09-14 09:47:29', 1, NULL, '2026-09-14 09:43:07', '2026-09-14 09:47:29'),
(95, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e4672660d779366312c4cecf752f630feb49a4188594064f5802da7af78a7e36', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:48:32', NULL, 0, NULL, '2026-09-14 09:48:32', '2026-09-14 09:48:32'),
(96, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '3a49944f2837b4646b42e54f3868c156b5d4617247b23c75a170693891942b6e', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:48:38', NULL, 0, NULL, '2026-09-14 09:48:38', '2026-09-14 09:48:38'),
(97, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '1adc987d234d477ec161d5248fa1e25c83fe4fe4b71099c30968b264f3a3e797', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:48:51', NULL, 0, NULL, '2026-09-14 09:48:51', '2026-09-14 09:48:51'),
(98, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'ce186d6a44d6ced143ba5984ead5c5212f08984f74cfd19ce6171c5a6a6fa6d0', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:48:52', NULL, 0, NULL, '2026-09-14 09:48:52', '2026-09-14 09:48:52'),
(99, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '99762046e58cddbb313df6ac7a8cefe653a5e5f734014a867d5bc2d965ee8550', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:49:01', NULL, 0, NULL, '2026-09-14 09:49:01', '2026-09-14 09:49:01'),
(100, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '89284cc210c4c08baba2e891ffe285274a41addbcf581e67c9db288ee77cd5b1', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:49:02', NULL, 0, NULL, '2026-09-14 09:49:02', '2026-09-14 09:49:02'),
(101, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '48e2dc777b286a8a91dd06c96863deccc5a7e73d3359904bd53415bf3dca6009', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:49:16', NULL, 0, NULL, '2026-09-14 09:49:16', '2026-09-14 09:49:16'),
(102, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'a65af9cab5fe6131ae33a29ff7d962d70801e8379f9eb1448555bdd89c44987c', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:49:17', NULL, 0, NULL, '2026-09-14 09:49:17', '2026-09-14 09:49:17'),
(103, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '9bb1a0c2dab73bd124fdcdc744456565c2cde07c00d264a04e67808c2e7b1c57', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:49:31', NULL, 0, NULL, '2026-09-14 09:49:31', '2026-09-14 09:49:31'),
(104, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '0707c0ccd3a17c5d170448a9a98a919387f9e9c04fcc9aba149320eaf2ddd87b', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:49:32', '2026-09-14 09:49:52', 2, NULL, '2026-09-14 09:49:32', '2026-09-14 09:49:52'),
(105, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'bc09a36de2b7c05172fbde8a84b54923dbe95bfbb7c968a7aeca5077c043ffeb', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:50:01', '2026-09-14 09:50:25', 8, '2026-09-14 09:50:25', '2026-09-14 09:50:01', '2026-09-14 09:50:25'),
(106, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '7889096e3fd6e9682682063d6ff77b21129c33325f97fedab491a58432884c34', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/shumara-detail/1/karobary-mshklat-ka-hl', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:50:02', '2026-09-14 09:50:10', 3, '2026-09-14 09:50:10', '2026-09-14 09:50:02', '2026-09-14 09:50:10'),
(107, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '21a7eb616e009ff945a10855993d1c6212a6ecb0e3bc82b308ed7643ddd1adb0', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:50:22', '2026-09-14 10:10:17', 68, NULL, '2026-09-14 09:50:22', '2026-09-14 10:10:17'),
(108, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '36c3032b8f6345f9db7cad336894b9fdec366062f19cb214fa6df8b434b72493', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:50:30', '2026-09-14 09:50:42', 2, '2026-09-14 09:50:42', '2026-09-14 09:50:30', '2026-09-14 09:50:42'),
(109, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'da95acf43ab8d68d49418d9f46236e98816b44b28507e7ff8cfc47cad05cb75a', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', 'http://localhost/', 'App\\Models\\Article', 3, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:50:32', NULL, 0, NULL, '2026-09-14 09:50:32', '2026-09-14 09:50:32'),
(110, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'cbef26ba76e2b3c85884b983e41f3b507ed96f95d9fc36a15feb3363467018a2', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/4/mstkl-hl-pr-tog-dyna', 'http://localhost/', 'App\\Models\\Article', 4, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 09:50:36', NULL, 0, NULL, '2026-09-14 09:50:36', '2026-09-14 09:50:36'),
(111, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '66f479d80b5b1b7a02e5487ffb4606bd8fbe3153f5720af14c899cfa96811833', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 10:32:57', '2026-09-14 10:33:54', 11, '2026-09-14 10:33:54', '2026-09-14 10:32:57', '2026-09-14 10:33:54'),
(112, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '85940e5844c3014e0e4e8761b5628d0977f6fedd6d55502e2b671a0396fb234e', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/2/r-karobary-shkhs-ko-apny-karobary-zndgy-my-okt-br-okt-mtaadd-aor-mkhtlf-noaayt-ky-mshklat-ka-samna-krna-pta', 'http://localhost/', 'App\\Models\\Article', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 10:33:53', '2026-09-14 10:33:58', 2, '2026-09-14 10:33:58', '2026-09-14 10:33:53', '2026-09-14 10:33:58'),
(113, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '69d67505253279118b8366f6fa86db06a3f149ee23868f08239492b97dc3862d', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/mazmoon/2/r-karobary-shkhs-ko-apny-karobary-zndgy-my-okt-br-okt-mtaadd-aor-mkhtlf-noaayt-ky-mshklat-ka-samna-krna-pta', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 10:33:58', '2026-09-14 10:55:34', 211, NULL, '2026-09-14 10:33:58', '2026-09-14 10:55:34'),
(114, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '7d8312119b1577528d07a05ee6739a62ed3db6b2df8149872489eef515d1e7e3', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/4/mstkl-hl-pr-tog-dyna', 'http://localhost/', 'App\\Models\\Article', 4, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 10:34:32', NULL, 0, NULL, '2026-09-14 10:34:32', '2026-09-14 10:34:32'),
(115, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '0131511951a95ee80e40dba02822679f48ae30d6cbdd2fc5fc7cc3ce5d75ce23', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:32:03', NULL, 0, NULL, '2026-09-14 11:32:03', '2026-09-14 11:32:03'),
(116, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '0efdc8e9e0f42b0a327ba0bdf1ef0a598313ad4e8467dc0be5818604ae59814c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:40:08', '2026-09-14 11:40:32', 13, NULL, '2026-09-14 11:40:08', '2026-09-14 11:40:32'),
(117, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '3348b784cc6ab57fd332e64bb3f63b10eef673ee8bb75738d04b72bd5e57825e', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:40:28', '2026-09-14 11:40:58', 3, '2026-09-14 11:40:58', '2026-09-14 11:40:28', '2026-09-14 11:40:58'),
(118, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd8f458bac933c0eb8cf4a30ba56b6008b7b189aecadc3e123ccd8bc5009a558a', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:40:31', '2026-09-14 11:40:43', 6, '2026-09-14 11:40:43', '2026-09-14 11:40:31', '2026-09-14 11:40:43'),
(119, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '95740ca1cac2a2f9d25ab17be3a48f0bc9e6231112816dac07bd12f14dc90134', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:40:42', '2026-09-14 11:42:12', 6, '2026-09-14 11:42:12', '2026-09-14 11:40:42', '2026-09-14 11:42:12'),
(120, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '8a40b9ccb12757074c59f470dfb2ee5813aec40564a7b36aacf5721f4b03e73a', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/1/karobary-mshklat-ka-hl', 'http://localhost/sabqa-shumare', 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:40:47', '2026-09-14 11:42:02', 3, '2026-09-14 11:42:02', '2026-09-14 11:40:47', '2026-09-14 11:42:02'),
(121, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'dafce7bfd2b2d727be32027efcecadb47888ceb40d2ae4db75f1e71580d09aff', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:40:58', '2026-09-14 11:42:35', 17, '2026-09-14 11:42:35', '2026-09-14 11:40:58', '2026-09-14 11:42:35'),
(122, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '9918e763d9a7008fe881e60d19d3794797fe17d66939bc5f686a1b3b2afe8950', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/2/r-karobary-shkhs-ko-apny-karobary-zndgy-my-okt-br-okt-mtaadd-aor-mkhtlf-noaayt-ky-mshklat-ka-samna-krna-pta', 'http://localhost/mazameen', 'App\\Models\\Article', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:41:04', '2026-09-14 11:41:07', 1, '2026-09-14 11:41:07', '2026-09-14 11:41:04', '2026-09-14 11:41:07'),
(123, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '2280896983ce11b280998cfe6a2ff835243e6fcdbc7ba794c8d0f55435901657', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/1/karobary-mshklat-ka-hl', 'http://localhost/mazameen', 'App\\Models\\Article', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:41:10', '2026-09-14 11:41:13', 1, '2026-09-14 11:41:13', '2026-09-14 11:41:10', '2026-09-14 11:41:13'),
(124, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'ccda1ed8b04623870f2b1dae9407658e840724c0784955474fdba35376571fe5', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:42:11', '2026-09-14 11:42:24', 6, '2026-09-14 11:42:24', '2026-09-14 11:42:11', '2026-09-14 11:42:24'),
(125, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '018fa6474a8937778e4818d9982182e1a1ecc6cad38dcff2fa26d385776ca34f', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:42:23', '2026-09-14 11:43:14', 6, '2026-09-14 11:43:14', '2026-09-14 11:42:23', '2026-09-14 11:43:14'),
(126, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '8ae58fc0c3d867a93c6186580b59653c72fc5cb1cc6284cefc870083bcf69017', 'shumara-detail', 'shumara-detail', 'http://localhost:8000/shumara-detail/2/mstkl-hl-pr-tog-dyna', 'http://localhost/sabqa-shumare', 'App\\Models\\Magazine', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:42:27', '2026-09-14 11:42:55', 3, '2026-09-14 11:42:55', '2026-09-14 11:42:27', '2026-09-14 11:42:55'),
(127, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '596267a4fd3033e3e61432269fdf004817785d4bfb995b91a07600329626755d', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:42:34', '2026-09-14 11:42:44', 5, '2026-09-14 11:42:44', '2026-09-14 11:42:34', '2026-09-14 11:42:44'),
(128, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'acae737641d2657a742a918a0b690012523481536bbd34f337af8b882fff855a', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:42:43', '2026-09-14 11:42:49', 3, '2026-09-14 11:42:49', '2026-09-14 11:42:43', '2026-09-14 11:42:49'),
(129, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '5d752f18985c0e7ff2685ec27f601545b41c40d95191ce7fb4ef2067ecbbb42e', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/4/mstkl-hl-pr-tog-dyna', 'http://localhost/mazameen', 'App\\Models\\Article', 4, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:42:48', '2026-09-14 11:42:51', 2, '2026-09-14 11:42:51', '2026-09-14 11:42:48', '2026-09-14 11:42:51'),
(130, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e80a376661c5f0dfa331321a499fbd02603e549798d945e60f1a5768c0507a56', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:42:52', NULL, 0, NULL, '2026-09-14 11:42:52', '2026-09-14 11:42:52'),
(131, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f4822f1e67a5873a0f7de7f70ae09974b63fa7a0f2cef2b1f2681c51b7527cce', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:43:12', '2026-09-14 11:43:50', 24, '2026-09-14 11:43:50', '2026-09-14 11:43:12', '2026-09-14 11:43:50'),
(132, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'cc4d72dd03a9f25e739be7dab1e8c6a8a0dbc7641f4dd583ccd51fbd1790b8f4', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:43:49', '2026-09-14 11:43:54', 3, '2026-09-14 11:43:54', '2026-09-14 11:43:49', '2026-09-14 11:43:54'),
(133, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '01431c50ba60dc5e32aac493735dec870500956139775e3cd760f10c361768cb', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:43:53', '2026-09-14 11:44:04', 4, '2026-09-14 11:44:04', '2026-09-14 11:43:53', '2026-09-14 11:44:04'),
(134, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', 'fb953a2d39f46cda960253b76ced3f2228946bd096f5b823d4c915525a486ea0', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/4/mstkl-hl-pr-tog-dyna', 'http://localhost/mazameen', 'App\\Models\\Article', 4, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:43:57', '2026-09-14 11:44:00', 1, '2026-09-14 11:44:00', '2026-09-14 11:43:57', '2026-09-14 11:44:00'),
(135, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '14bd1c514028f3fb507886db22d83ae70ce7d77db4d0b48f62a163d15f64896e', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 11:44:03', '2026-09-14 11:45:41', 20, '2026-09-14 11:45:41', '2026-09-14 11:44:03', '2026-09-14 11:45:41'),
(136, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '56d3a407fef07ab69d0805a3d5098e42f747c819e86c94035510fbcf419b0ff9', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 04:26:29', NULL, 0, NULL, '2026-09-15 04:26:29', '2026-09-15 04:26:29'),
(137, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'a696d718529945ac6a797b8086a00752f7ec604ff76e0683c33e0e5862fb2560', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 04:26:32', '2026-09-15 04:40:28', 191, '2026-09-15 04:40:28', '2026-09-15 04:26:32', '2026-09-15 04:40:28'),
(138, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '095c5b4ff30c5e5b977bc749242d93ddf0641dbc444c581101bcc2bc6e9a664b', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/account/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 05:59:11', '2026-09-15 05:59:24', 3, '2026-09-15 05:59:24', '2026-09-15 05:59:11', '2026-09-15 05:59:24'),
(139, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '3587beea3e217ae0dd0f4519049b8b9309fe05e352b9b5ea7285cfbe04a31e43', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/subscriptions/2/checkout', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 05:59:42', '2026-09-15 05:59:52', 7, '2026-09-15 05:59:52', '2026-09-15 05:59:42', '2026-09-15 05:59:52'),
(140, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '541954d5582cd231ef6de678f044b95bf7bc1fd116a44640babbd87a96d97af4', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 05:59:51', '2026-09-15 06:00:00', 4, '2026-09-15 06:00:00', '2026-09-15 05:59:51', '2026-09-15 06:00:00'),
(141, 1, '6c744164-9699-4500-8d55-1a3dc170ed75', 'bb16814756d1e10fabf44db2c3e280430b688f9313682bf8a4e0c38aa1dce455', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 10:51:51', '2026-09-15 10:52:44', 9, '2026-09-15 10:52:44', '2026-09-15 10:51:51', '2026-09-15 10:52:44'),
(142, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '21eec1e970b524b9f348b60c33a86c0237ca7a18bca7ffca42de62f3dba4542e', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 12:03:07', '2026-09-15 12:03:35', 5, '2026-09-15 12:03:35', '2026-09-15 12:03:07', '2026-09-15 12:03:35'),
(143, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '4ba78262b7a03ff01e82a44f8ec1b77c953f9c0fbc5b728b0a7fa8da07d0ab9f', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 04:22:12', '2026-09-16 04:57:14', 1303, '2026-09-16 04:57:14', '2026-09-16 04:22:12', '2026-09-16 04:57:14'),
(144, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '2047702c763e3a728b7a2fec8ed6a63d873ae55179f7223b35c56daee6ee6600', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/faqs', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 05:04:12', '2026-09-16 05:07:10', 24, NULL, '2026-09-16 05:04:12', '2026-09-16 05:07:10'),
(145, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f72430eb275413502c1bbfd655b0f9760e5171d51724ca8454a7989c2f5449e7', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 05:18:09', NULL, 0, NULL, '2026-09-16 05:18:09', '2026-09-16 05:18:09'),
(146, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'c2eeaca48ed08a0ab77e42adf4b430eac1b67fc72ebb6f056a5d9330ba1fdcee', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 05:18:23', NULL, 0, NULL, '2026-09-16 05:18:23', '2026-09-16 05:18:23'),
(147, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '218a6dbb2b3620d7b4df35a42bacae4ea944437feeaddc178e941432009be7a7', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 05:18:28', '2026-09-16 05:23:43', 2, '2026-09-16 05:23:43', '2026-09-16 05:18:28', '2026-09-16 05:23:43'),
(148, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd1a9488677a8b7740d20c0afcca67c066887f438d05e74ae029f8584c87cc849', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/faqs', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 08:52:13', '2026-09-16 08:55:54', 112, NULL, '2026-09-16 08:52:13', '2026-09-16 08:55:54'),
(149, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'bed6f3e6216d00729ce39a320ea4d85ab29f22d3842e7aef327273f7eb4bac61', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 10:54:16', '2026-09-16 11:37:02', 2, '2026-09-16 11:37:02', '2026-09-16 10:54:16', '2026-09-16 11:37:02'),
(150, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '531cbbdc910d9aee4a07326b201dbf9572601c4f9bbf54e2e898b19cfcad9a71', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 11:37:00', '2026-09-16 11:37:13', 8, NULL, '2026-09-16 11:37:00', '2026-09-16 11:37:13'),
(151, NULL, 'ba2828bd-a353-4b86-8076-8e4a49c4dafd', '5ab0ba621288403492f45cffedaafceac5ebf16b5e1bbaf101227e8baec99350', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 11:37:13', '2026-09-16 11:37:24', 6, '2026-09-16 11:37:24', '2026-09-16 11:37:13', '2026-09-16 11:37:24'),
(152, NULL, 'ba2828bd-a353-4b86-8076-8e4a49c4dafd', '63fc02b8d3b6794a7f11d436a823b66350b1a07aef9ebec0f9b5d31aeb3a1298', 'category-detail', 'mozu-detail', 'http://localhost:8000/mozu/1/karobar', 'http://localhost/', 'App\\Models\\Category', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 11:37:23', '2026-09-16 11:37:32', 6, '2026-09-16 11:37:32', '2026-09-16 11:37:23', '2026-09-16 11:37:32'),
(153, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '7a91644b22baa0347f90dc7b31c708a59f82e30737aa2404bc42f868898f7af5', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 11:45:35', NULL, 0, NULL, '2026-09-16 11:45:35', '2026-09-16 11:45:35'),
(154, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'dbc764892cb90d0395870d741e6c27dc8d4a7c97f73291c746164e1f505db51b', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 04:22:32', NULL, 0, NULL, '2026-09-17 04:22:32', '2026-09-17 04:22:32'),
(155, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '60eba749aa580a7b888bda26ce03c3990c1637e650ff68914a9ae6cfc8118bd7', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 05:11:36', '2026-09-17 05:42:40', 3, NULL, '2026-09-17 05:11:36', '2026-09-17 05:42:40'),
(156, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '94b231859afd921d4f0535edd7aeb19f0681ec32331ee6b6643b4f24e3503822', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 06:16:46', '2026-09-17 07:10:27', 5, NULL, '2026-09-17 06:16:46', '2026-09-17 07:10:27'),
(157, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'b450660a7491f70a08d970034b9737705800e3682c9c0255baf7dc6a51251cb2', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 07:18:32', '2026-09-17 07:56:59', 2, '2026-09-17 07:56:59', '2026-09-17 07:18:32', '2026-09-17 07:56:59'),
(158, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '6488e6fbd1c724d09eff1dbf9a8157f165474cd98796373d1df27db10c74658e', 'authors', 'front.mazmoon-nigaar', 'http://localhost:8000/mazmoon-nigaar', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 07:56:57', '2026-09-17 07:57:08', 7, '2026-09-17 07:57:08', '2026-09-17 07:56:57', '2026-09-17 07:57:08'),
(159, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '700b0cfd2549b399e2ce955e3b2e15ec15f247914eba08f70950aab1c811f3dc', 'author-detail', 'front.mazmoon-nigaar.detail', 'http://localhost:8000/mazmoon-nigaar/1/%D9%88%D8%AC%D8%A7%DB%81%D8%AA-%D8%B4%DB%8C%D8%AE', 'http://localhost/mazmoon-nigaar', 'App\\Models\\Author', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 07:57:07', '2026-09-17 09:44:47', 76, NULL, '2026-09-17 07:57:07', '2026-09-17 09:44:47'),
(160, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f65818909486bab7784f91743b4ab8a35501d454b03453655565712c3acf1cff', 'author-detail', 'front.mazmoon-nigaar.detail', 'http://localhost:8000/mazmoon-nigaar/1/%D9%88%D8%AC%D8%A7%DB%81%D8%AA-%D8%B4%DB%8C%D8%AE', 'http://localhost/mazmoon-nigaar/1/%D9%88%D8%AC%D8%A7%DB%81%D8%AA-%D8%B4%DB%8C%D8%AE', 'App\\Models\\Author', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 07:57:22', '2026-09-17 07:57:31', 3, '2026-09-17 07:57:31', '2026-09-17 07:57:22', '2026-09-17 07:57:31'),
(161, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '9558a42277ecb1d331f89c766d6b36f0acf61c0da78b938ab7b8e11d6056de83', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '152.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-18 04:31:07', '2026-09-18 06:38:57', 2, NULL, '2026-09-18 04:31:07', '2026-09-18 06:38:57'),
(162, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '2415e768127d9772a8bec0b8bb622ff83ee83a3dfc47ca35d191779c588e35f7', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 06:03:20', '2026-09-19 06:11:30', 15, NULL, '2026-09-19 06:03:20', '2026-09-19 06:11:30'),
(163, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '57070312d2ee31737e3c5d4db9d4939cad403e397eacdb5c2bd2d784c3ef841f', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 06:21:48', NULL, 0, NULL, '2026-09-19 06:21:48', '2026-09-19 06:21:48'),
(164, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '5d733183e5a88b15ac0d213618be7c4acd0d32052163796a4dd7397cec0bddc2', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 06:24:19', NULL, 0, NULL, '2026-09-19 06:24:19', '2026-09-19 06:24:19'),
(165, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '8b6c02ece807648cbdf236e57271aa5b53370724b19860a926a93dc7b72a7116', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 06:24:34', NULL, 0, NULL, '2026-09-19 06:24:34', '2026-09-19 06:24:34'),
(166, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '6da3aa73d849d87d4e863b329199298716f7173316e7b2e94ce2938b704f7a07', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 06:28:04', NULL, 0, NULL, '2026-09-19 06:28:04', '2026-09-19 06:28:04'),
(167, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd5ea610759f1bdf6fe53920917416ab94f0ed5a2c0cf895e82b99e959ece5427', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 06:29:47', NULL, 0, NULL, '2026-09-19 06:29:47', '2026-09-19 06:29:47'),
(168, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '8dee76acbbef3d674e546282afce2afe9d4b70ef912dab500bbe3eb3c88d3687', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 07:03:34', '2026-09-19 07:10:21', 7, NULL, '2026-09-19 07:03:34', '2026-09-19 07:10:21'),
(169, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '3e9fea1ddfe86a5c52eedc86ad21f485541fe0c05d68fe968aaeb7ac8d489267', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:24:26', NULL, 0, NULL, '2026-09-22 04:24:26', '2026-09-22 04:24:26'),
(170, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e9b513e7ba1736b9846e2f32ecf5031c48b531e2fa85a97f9ed1f1800d8224cb', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:33:01', NULL, 0, NULL, '2026-09-22 04:33:01', '2026-09-22 04:33:01');
INSERT INTO `site_visits` (`id`, `user_id`, `visitor_id`, `public_token_hash`, `page_key`, `route_name`, `url`, `referrer`, `visitable_type`, `visitable_id`, `ip_address`, `country`, `country_code`, `city`, `region`, `latitude`, `longitude`, `device`, `device_type`, `browser`, `browser_version`, `os`, `os_version`, `user_agent`, `started_at`, `last_activity_at`, `duration_seconds`, `ended_at`, `created_at`, `updated_at`) VALUES
(171, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'aa1e6a4962931035f67565b94ab8eca3bb61020b48e0ec03c9250ac705888e98', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:34:49', '2026-09-22 07:34:51', 45, '2026-09-22 07:34:51', '2026-09-22 04:34:49', '2026-09-22 07:34:51'),
(172, NULL, '8fc27fab-ac90-4edf-a265-8653c554b2c2', '8b15ea065123a7bcb5032de7daef52396ba84fe9304d2cb566e5f0c15f28ce75', 'shumara-detail', 'api.magazines.show', 'http://127.0.0.1:8000/api/magazines/1', NULL, 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', NULL, NULL, NULL, NULL, 'PostmanRuntime/2.7.0', '2026-09-22 05:20:53', NULL, 0, NULL, '2026-09-22 05:20:53', '2026-09-22 05:20:53'),
(173, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '12117db990d9f83e96948d6b54f1a6b2c35707b3e58bade4157e339a5bf435e7', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/advertise', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 10:48:49', '2026-09-22 10:48:55', 3, '2026-09-22 10:48:55', '2026-09-22 10:48:49', '2026-09-22 10:48:55'),
(174, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '5f4f90c6c8fbeb2c657d11263d947dab71a1630ac7f9be7c63821b312857e6dd', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/advertise', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 10:49:13', '2026-09-22 10:49:26', 4, '2026-09-22 10:49:26', '2026-09-22 10:49:13', '2026-09-22 10:49:26'),
(175, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '4cf310369cf24eeb686e8af55e9aa3894f11aba90a0b582132f28cbdc73da68a', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/advertise', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:06:28', '2026-09-22 11:06:35', 2, '2026-09-22 11:06:35', '2026-09-22 11:06:28', '2026-09-22 11:06:35'),
(176, NULL, '9b192ff8-5aeb-454c-bca0-9474d11453ca', '5f70930d9059404c91f060fbf51b2bf9c248d549459a84cdff1867b96c373d6c', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:11:29', '2026-09-22 11:11:48', 4, '2026-09-22 11:11:48', '2026-09-22 11:11:29', '2026-09-22 11:11:48'),
(177, NULL, '9b192ff8-5aeb-454c-bca0-9474d11453ca', '33bb36de91b0131de4f6bbb02fda5d5dbf436ec682541feb2c278620c7c2b49b', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:11:47', '2026-09-22 11:11:58', 6, '2026-09-22 11:11:58', '2026-09-22 11:11:47', '2026-09-22 11:11:58'),
(178, NULL, '9b192ff8-5aeb-454c-bca0-9474d11453ca', 'd24c6e9df02eb5252735b3e33b46bc8ec9e4812d9cfd50d82e961cff46fc8cac', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:11:57', '2026-09-22 11:12:09', 6, '2026-09-22 11:12:09', '2026-09-22 11:11:57', '2026-09-22 11:12:09'),
(179, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f75a5b783ec1df2a2cb5256a0c5b2dd17b71c327c11787f049982b081ec30ff6', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:13:32', '2026-09-22 11:13:38', 3, '2026-09-22 11:13:38', '2026-09-22 11:13:32', '2026-09-22 11:13:38'),
(180, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '0f01d718d89f614077e4ecb9cd9c1741872f6c3f8d655da6e46a7c409a868b2f', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/login', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:13:45', '2026-09-22 11:13:52', 5, NULL, '2026-09-22 11:13:45', '2026-09-22 11:13:52'),
(181, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '8c57c12a1066f738187af305f630849eee3ec84b4c44cf4549bca31c724519cd', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/login', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:14:23', '2026-09-22 11:17:44', 12, NULL, '2026-09-22 11:14:23', '2026-09-22 11:17:44'),
(182, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '58766b970da87eb0a6fcc474fdc38795d3a864474cdc28ef05d9e11461499165', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:19:47', '2026-09-22 11:19:54', 3, '2026-09-22 11:19:54', '2026-09-22 11:19:47', '2026-09-22 11:19:54'),
(183, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '6a9f298989195fbaf2a3abc8b110a7c2a32e2309ffa2418df61664919c66ddd1', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/account/advertising-requests', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 11:22:03', '2026-09-22 11:22:09', 1, '2026-09-22 11:22:09', '2026-09-22 11:22:03', '2026-09-22 11:22:09'),
(184, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'eaffa485f4b2e4b1722e65c1cc86989599201f70721b337945a919445f349950', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 04:16:48', '2026-09-23 04:21:53', 5, '2026-09-23 04:21:53', '2026-09-23 04:16:48', '2026-09-23 04:21:53'),
(185, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'b71bebf855bee4ea40a500e281f9657d4c39887dff22f7417abfa25e0a8b7ac2', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/account/advertising-requests', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 05:30:20', '2026-09-23 05:30:38', 1, '2026-09-23 05:30:38', '2026-09-23 05:30:20', '2026-09-23 05:30:38'),
(186, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '99ce4c2df812c66d552b0e73bc32f0849d38bf8ed86f48c0fef5ee248bf66938', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/login', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:46:46', '2026-09-23 09:46:53', 2, NULL, '2026-09-23 09:46:46', '2026-09-23 09:46:53'),
(187, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd71780df801d4a55fe72ebf5aa82e2232509d090a64b0293f60da3a9c053928d', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:47:57', NULL, 0, NULL, '2026-09-23 09:47:57', '2026-09-23 09:47:57'),
(188, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'b0447a75b5d7640c3af126937fbcb0b2097aa0408f17a7dfa3a27d451dd2d9d6', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:48:10', NULL, 0, NULL, '2026-09-23 09:48:10', '2026-09-23 09:48:10'),
(189, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '96f601f0546b3858cb5efbf84634ea5a8d9d25cbf1c70c21864594bb047072b5', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:48:25', NULL, 0, NULL, '2026-09-23 09:48:25', '2026-09-23 09:48:25'),
(190, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '3ec92e364303917c4ae021037a1f0c868bfe251bd9eba6127109dabe3bf50f5e', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:48:56', NULL, 0, NULL, '2026-09-23 09:48:56', '2026-09-23 09:48:56'),
(191, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '5df69471deebfc78222b4facba768115edd25846f1d88e7bb4cf9911b9ed3924', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:49:56', NULL, 0, NULL, '2026-09-23 09:49:56', '2026-09-23 09:49:56'),
(192, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '6fcf045f24c3ca7b541b1a48f9c8edd42d6e1bff2bfa9b84610ca11971d6bc6b', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:50:57', NULL, 0, NULL, '2026-09-23 09:50:57', '2026-09-23 09:50:57'),
(193, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '5283bbe30f3ee826254a306f5d9f4cd440c36ffa235c61c76a395335b95d3649', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:51:46', NULL, 0, NULL, '2026-09-23 09:51:46', '2026-09-23 09:51:46'),
(194, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'bb4940968d46de3fedf94d65c796c48a0bd5052079592fecbc1127c62ab1e0b3', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:51:57', NULL, 0, NULL, '2026-09-23 09:51:57', '2026-09-23 09:51:57'),
(195, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '1ade04743ad130be86c47d29da457f789c1b36c61e157e7b6c98bc94d27a8d39', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:52:15', NULL, 0, NULL, '2026-09-23 09:52:15', '2026-09-23 09:52:15'),
(196, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'cc8d5e0c1e9174e67c53c6414329f11033e4a6cfafa01e94035519395f95314c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 09:52:40', '2026-09-23 09:53:02', 10, NULL, '2026-09-23 09:52:40', '2026-09-23 09:53:02'),
(197, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '2f81f449e880dbbe89ef8a987680a7c07b93f5e44c832e3e98e43c494944a464', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 10:00:58', NULL, 0, NULL, '2026-09-23 10:00:58', '2026-09-23 10:00:58'),
(198, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'ad9175b3e2cad282b0edf7145768cb9ef237e7d4c0b368d21872261ff56706c0', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 10:01:57', NULL, 0, NULL, '2026-09-23 10:01:57', '2026-09-23 10:01:57'),
(199, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '8dfc89d9fe1491c3662ad7cc20ad2e79dd44b7fae574d55dec84f3744fbaccc9', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 10:02:57', NULL, 0, NULL, '2026-09-23 10:02:57', '2026-09-23 10:02:57'),
(200, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'be4f27ca565f9c782041743754125f07f253aac4e4ec7d36e3f1f20f3f336e9d', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 10:03:57', '2026-09-23 10:09:22', 3, '2026-09-23 10:09:22', '2026-09-23 10:03:57', '2026-09-23 10:09:22'),
(201, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'f9b779a0b44165fd2b48a2e3d9eac8f1672c37b9b52a7d128de05d64dc9164ed', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 11:22:20', '2026-09-23 11:22:37', 8, NULL, '2026-09-23 11:22:20', '2026-09-23 11:22:37'),
(202, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '8677689749823fb29c1cc0ada870f6e5a34ef0c686c8c8d4951b7aee43cc5f5c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 11:24:54', '2026-09-23 11:57:53', 15, NULL, '2026-09-23 11:24:54', '2026-09-23 11:57:53'),
(203, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'eef8771813cd4a75d29b67efc806f6fba40932dd77b6a081ceae1bd336dccdfd', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 11:57:47', NULL, 0, NULL, '2026-09-23 11:57:47', '2026-09-23 11:57:47'),
(204, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '5f08af0b578d8754b1edb0325d60c4bbfe290b32c6c215e1935f9137bcf832d3', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 11:58:40', NULL, 0, NULL, '2026-09-23 11:58:40', '2026-09-23 11:58:40'),
(205, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '609d14c3216126e3a826d483a035a9fc027c7685330f11c6eaa926094aff8437', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:02:16', '2026-09-23 12:11:19', 11, NULL, '2026-09-23 12:02:16', '2026-09-23 12:11:19'),
(206, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'c0b1d2e031f8a18c66ed5384ddd7406b3e6d5a1e4bba6206dded6f445bbcd3e4', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:15:36', '2026-09-23 12:16:11', 10, NULL, '2026-09-23 12:15:36', '2026-09-23 12:16:11'),
(207, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '44cf8604d10d586d98074625097f111357ce1e0429b024a8f6126fff0457f11f', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 04:26:51', '2026-09-24 04:31:29', 7, '2026-09-24 04:31:29', '2026-09-24 04:26:51', '2026-09-24 04:31:29'),
(208, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'd458a93b6f276a61116bb5b6a926fc8a36d72f16f638581b420bc2c5b6d7c6ad', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 04:31:27', '2026-09-24 04:31:44', 12, NULL, '2026-09-24 04:31:27', '2026-09-24 04:31:44'),
(209, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', '85580b59d2ff7b725b6ea040d8b05003647337647042c2161025109faa334a6a', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 04:32:19', '2026-09-24 04:33:56', 9, '2026-09-24 04:33:56', '2026-09-24 04:32:19', '2026-09-24 04:33:56'),
(210, NULL, 'c88bbd05-3bfc-4cdf-a4bb-3c66de33bdd1', '5541824c330864d3026f1ab96433adc02fd133b7c1b1c05d4fdb62390cf128d9', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:25:02', '2026-09-24 05:25:29', 4, '2026-09-24 05:25:29', '2026-09-24 05:25:02', '2026-09-24 05:25:29'),
(211, NULL, 'c88bbd05-3bfc-4cdf-a4bb-3c66de33bdd1', '9b9ff32d12770957538b3ea73070bc25e74f3b42f99a1889411c0f449488ab81', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:25:27', NULL, 0, NULL, '2026-09-24 05:25:27', '2026-09-24 05:25:27'),
(212, NULL, 'c88bbd05-3bfc-4cdf-a4bb-3c66de33bdd1', '9e0b4822ac11dbc8e70e157379141a6d533bb7ada07176a5b394d55922ecadd3', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:25:28', '2026-09-24 05:29:02', 9, '2026-09-24 05:29:02', '2026-09-24 05:25:28', '2026-09-24 05:29:02'),
(213, NULL, 'c88bbd05-3bfc-4cdf-a4bb-3c66de33bdd1', 'bc62a5095040b7e465ffe3cdefe19c49165fb7ad2197b41619f6ef10464c5a41', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:29:03', '2026-09-24 05:29:45', 10, '2026-09-24 05:29:45', '2026-09-24 05:29:03', '2026-09-24 05:29:45'),
(214, NULL, '5165dbbb-0e8e-4a38-a758-48e8fb256d72', 'd17a34be41065454f4246dcae98b45655a458cab2309f1d15ba913a8b6456266', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:43:10', '2026-09-24 05:43:34', 8, '2026-09-24 05:43:34', '2026-09-24 05:43:10', '2026-09-24 05:43:34'),
(215, 1, '6c744164-9699-4500-8d55-1a3dc170ed75', '1fe803063ed0a0fa8a17b08644dcea50821c8a84f57572d0555d3d095913bbdd', 'mazmoon-detail', 'mazmoon-detail', 'http://localhost:8000/mazmoon/3/ksy-by-kom-ky-trky-ka-daromdar-as-k-nogoano-ky-taalym-aor-nr-pr-ota-okt-a-gya-k-m-apny-trgyhat-drst-kry', NULL, 'App\\Models\\Article', 3, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:44:34', '2026-09-24 05:44:40', 1, '2026-09-24 05:44:40', '2026-09-24 05:44:34', '2026-09-24 05:44:40'),
(216, 1, '6c744164-9699-4500-8d55-1a3dc170ed75', 'e2afeeba27091a5d560c618cbd1bea79506cfc9d20f2c54fe94bfea94dcac3df', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:44:37', '2026-09-24 05:44:47', 3, '2026-09-24 05:44:47', '2026-09-24 05:44:37', '2026-09-24 05:44:47'),
(217, 1, '6c744164-9699-4500-8d55-1a3dc170ed75', '836b23ba1039506fdefe177c3719f12b8c479cd436af3f4afb753a9f61b3d352', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/newsletter/unsubscribe/success', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 05:48:08', '2026-09-24 05:48:29', 13, NULL, '2026-09-24 05:48:08', '2026-09-24 05:48:29'),
(218, NULL, 'fe8c14b3-64c4-4b55-ae1c-3aa8e308da49', '59dbada80f916f23667a5a86ae5b0207ef0bceef7002406e292e5b2c52d722a6', 'mazmoon-detail', 'api.articles.show', 'http://localhost:8000/api/articles/2', NULL, 'App\\Models\\Article', 2, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', NULL, NULL, NULL, NULL, 'PostmanRuntime/2.7.0', '2026-09-24 07:13:30', NULL, 0, NULL, '2026-09-24 07:13:30', '2026-09-24 07:13:30'),
(219, NULL, 'e4576eda-fcb4-40cc-b1c5-ceedc76b6957', '1e940a70a20802d1e43c9da839951272523be32f624403a6d76be8cdcdfa713d', 'shumara-detail', 'api.magazines.show', 'http://localhost:8000/api/magazines/1', NULL, 'App\\Models\\Magazine', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', NULL, NULL, NULL, NULL, 'PostmanRuntime/2.7.0', '2026-09-24 07:14:35', NULL, 0, NULL, '2026-09-24 07:14:35', '2026-09-24 07:14:35'),
(220, NULL, 'a640ecd1-06fa-42a5-b06c-7f391b82217c', 'c545d19c7a7cb9f37ba41bef01ad433cbc97055ae8680c829176a32d932311cc', 'mazmoon-detail', 'api.articles.show', 'http://localhost:8000/api/articles/1', NULL, 'App\\Models\\Article', 1, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', NULL, NULL, NULL, NULL, 'PostmanRuntime/2.7.0', '2026-09-24 07:18:31', NULL, 0, NULL, '2026-09-24 07:18:31', '2026-09-24 07:18:31'),
(221, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '9932f84f230e6a7ddd1e6907e0d3e6922993bd9887cb6d62526b8ebf7a7cdb65', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/account', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 08:31:45', '2026-09-24 08:32:09', 6, NULL, '2026-09-24 08:31:45', '2026-09-24 08:32:09'),
(222, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '4e8f75621c606121cb0d3492c31ea43666dff021a7423d6418be6ad4480e4519', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 08:36:29', '2026-09-24 09:05:53', 89, '2026-09-24 09:05:53', '2026-09-24 08:36:29', '2026-09-24 09:05:53'),
(223, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '6e8c4c8dd31f6285890d4ece63d8db1105f55cb206b095de42de50ad1f39dc3d', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:05:51', '2026-09-24 09:14:42', 7, '2026-09-24 09:14:42', '2026-09-24 09:05:51', '2026-09-24 09:14:42'),
(224, 14, '6c744164-9699-4500-8d55-1a3dc170ed75', '218e83bb4d443378d1f375405f3f05e4a3143e18975f49af9ee29502fcb4c68a', 'taza-shumara', 'taza.shumara', 'http://localhost:8000/taza-shumara', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:14:40', '2026-09-24 09:18:58', 136, NULL, '2026-09-24 09:14:40', '2026-09-24 09:18:58'),
(225, NULL, '6c744164-9699-4500-8d55-1a3dc170ed75', 'b3e645243110ca7607d7c99d12a37bf73820df562c64b5ff103b61b4e6198b8d', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/taza-shumara', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '153.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 09:32:31', NULL, 0, NULL, '2026-09-24 09:32:31', '2026-09-24 09:32:31'),
(226, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ad980476cc9ac84189a1fedbf9d32bd11c8015e6eba273fc130f64c29d74bcf5', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:27:01', '2026-10-02 05:27:01', 9, '2026-10-02 05:27:01', '2026-10-02 05:06:25', '2026-10-02 05:27:01'),
(227, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'c0dd201dd312415cff1e4ae28cb42feec28d69020580ef5b193130eb235c4a47', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:35:58', '2026-10-02 05:35:58', 21, NULL, '2026-10-02 05:34:58', '2026-10-02 05:35:58'),
(228, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'b5a74446ed01def168362b564e1abe2758ba5d81f280d1d4fcb4c61fe9ee989a', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:37:44', NULL, 0, NULL, '2026-10-02 05:37:44', '2026-10-02 05:37:44'),
(229, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ae69bf2fca740ce968e70e7f4fc81150fd799373d0cde55f2dd18a7e3f8ce184', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:38:44', NULL, 0, NULL, '2026-10-02 05:38:44', '2026-10-02 05:38:44'),
(230, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'e361bfa8b6c96b9415cecb3872ddf26e7377af2786914f173cb5d9c8ffb15270', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:39:44', NULL, 0, NULL, '2026-10-02 05:39:44', '2026-10-02 05:39:44'),
(231, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '4e7d4914143ff58fd4abb473c6c1e7a5353762155d9fab3824b800aa43d1ca36', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:40:44', NULL, 0, NULL, '2026-10-02 05:40:44', '2026-10-02 05:40:44'),
(232, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '59bcd34e386d40bf1a570895efa3f3681d015d25b1ce081bf77bcc457719f48c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:41:44', NULL, 0, NULL, '2026-10-02 05:41:44', '2026-10-02 05:41:44'),
(233, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ba9aea575dabda278349c96bdb7d37d89b8f794dd0e40811d3ab7a5890c2904f', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:42:44', NULL, 0, NULL, '2026-10-02 05:42:44', '2026-10-02 05:42:44'),
(234, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '88a6565a52e49154a846ed889c7935d081a7a23146a958240020c954464423f0', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:43:44', NULL, 0, NULL, '2026-10-02 05:43:44', '2026-10-02 05:43:44'),
(235, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '3260bb9cdcb755b5431e6cf9f3660164280008f500e75c94577f71eaac31a29f', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:44:44', NULL, 0, NULL, '2026-10-02 05:44:44', '2026-10-02 05:44:44'),
(236, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '53b3eeea516aed9e52dbd6e9f241cf193804c93c7c6c0cba3ab812db288bc998', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:45:44', NULL, 0, NULL, '2026-10-02 05:45:44', '2026-10-02 05:45:44'),
(237, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'bed8a345a3c6f2346a5b2da5860dc76953c4aa1819678d51f80add41c5b4ee2b', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:46:44', NULL, 0, NULL, '2026-10-02 05:46:44', '2026-10-02 05:46:44'),
(238, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ca959d199c8fc8be6341becfaa50e7681898f39efa7e1e3c3edfc4492aa49b5a', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:47:44', NULL, 0, NULL, '2026-10-02 05:47:44', '2026-10-02 05:47:44'),
(239, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '77371e01ddd67f86d06f4e1a727cee04c8db8ad61a2b4233d4e9c767bd83ddd7', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:49:10', '2026-10-02 05:49:10', 1, '2026-10-02 05:49:10', '2026-10-02 05:48:44', '2026-10-02 05:49:10'),
(240, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '65351b29bd35a7cc3b2b7603d698369506743f51e5d5e3ab5153e50b200dab52', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 05:49:13', '2026-10-02 05:49:13', 2, NULL, '2026-10-02 05:49:07', '2026-10-02 05:49:13'),
(241, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'b31dfa3b31201e365233f2c39ff43e438a1c7a3a25774b43e53076d77c91c074', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:25:44', NULL, 0, NULL, '2026-10-02 06:25:44', '2026-10-02 06:25:44'),
(242, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'e132675455ffd8dad5ab559a0f66a10f91b26d9d0051848cf7cc04d114d19a84', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:26:44', NULL, 0, NULL, '2026-10-02 06:26:44', '2026-10-02 06:26:44'),
(243, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '855e6a5d58cb41fdee6ce070720ecb15b6d8bf87349beb51316d6e6dbb4a49bf', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:27:44', NULL, 0, NULL, '2026-10-02 06:27:44', '2026-10-02 06:27:44'),
(244, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '1d8fdebd339b37567bc9643380f1490fc274a4e10699f04f1c2050ebd5dd1b93', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:28:44', NULL, 0, NULL, '2026-10-02 06:28:44', '2026-10-02 06:28:44'),
(245, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'c83836181fc906b5a4ad53c9a6635de56d6a9e73105dbc0d63a2e7f7b5d6f835', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:29:44', NULL, 0, NULL, '2026-10-02 06:29:44', '2026-10-02 06:29:44'),
(246, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'a28b9a1a8926658974d213130ff90773ad5a659d962912de4b80734e088e67d6', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:30:44', NULL, 0, NULL, '2026-10-02 06:30:44', '2026-10-02 06:30:44'),
(247, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '29d6a5c9d55179426feaf3fe4a44a1d1174353a97c1dcc614aa7f46e2950cde9', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:31:44', NULL, 0, NULL, '2026-10-02 06:31:44', '2026-10-02 06:31:44'),
(248, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'e8a09fff63aa5837187e41c1baaa066ab5d925494c7cff38ec069233e077ca81', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:32:44', NULL, 0, NULL, '2026-10-02 06:32:44', '2026-10-02 06:32:44'),
(249, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '15bba86fe7426fe85c534d9afb85569823fa0100ef8353f868b1a3a0386675d0', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 06:35:28', '2026-10-02 06:35:28', 6, NULL, '2026-10-02 06:33:08', '2026-10-02 06:35:28'),
(250, NULL, 'b5bf2e38-d03c-4840-8695-9591bfc5fd67', '97888a2e2dca83f1bb6270ff30112ca7b6763547c8b8348663b20b160b6f9fbf', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '150.0.7871.250', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.140.0 Chrome/150.0.7871.250 Electron/43.7.3 Safari/537.36', '2026-10-03 05:39:53', NULL, 0, NULL, '2026-10-03 05:39:53', '2026-10-03 05:39:53'),
(251, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '734f6f8621ee2d8c5f58521dbf6db2bb4d922655d957ae22fac632d8058aec63', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 05:40:34', '2026-10-03 05:40:34', 1, '2026-10-03 05:40:34', '2026-10-03 05:40:00', '2026-10-03 05:40:34'),
(252, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'fcea701cd261b94c580cc69f67be0da63802ad42cf30ab56c9db1b6031ed0185', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 10:22:07', '2026-10-03 10:22:07', 2, '2026-10-03 10:22:07', '2026-10-03 10:21:50', '2026-10-03 10:22:07'),
(253, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'af283eba6f06ec9fd2446e98ec9cc0e3aafd8de7d2df6a0677640156030d24ea', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 10:27:38', '2026-10-03 10:27:38', 36, NULL, '2026-10-03 10:22:06', '2026-10-03 10:27:38'),
(254, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '832f37d26deb913d0caf8eee096fcbdc095163453d6faa91bd622fd36f7efe5c', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 10:35:38', '2026-10-03 10:35:38', 3, '2026-10-03 10:35:38', '2026-10-03 10:35:31', '2026-10-03 10:35:38'),
(255, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '88f11f6bca43dd1a65d618d29ef1ab3ec1e89449682999abdc5591fe0003f4a9', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 10:35:58', '2026-10-03 10:35:58', 14, NULL, '2026-10-03 10:35:37', '2026-10-03 10:35:58'),
(256, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '727ae355eb6f5a8ae9f669e2565d6992e019ec85d288cec3b78905d83c24f1e4', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 10:36:31', '2026-10-03 10:36:31', 10, '2026-10-03 10:36:31', '2026-10-03 10:36:15', '2026-10-03 10:36:31'),
(257, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ef27e4f103193445ede3fa32d4ec59c3f3c8a397b79c4515778dc658debdc5fe', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/account/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 10:39:55', '2026-10-03 10:39:55', 1, '2026-10-03 10:39:55', '2026-10-03 10:39:47', '2026-10-03 10:39:55'),
(258, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '86f6c4e6e260edb383899fb514875f71ef4ef7f3d0408235d5b70889335d3eee', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:18:04', '2026-10-03 11:18:04', 5, '2026-10-03 11:18:04', '2026-10-03 11:16:28', '2026-10-03 11:18:04'),
(259, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'c849ab66f4558473ba47233cf7489b1bef9794cd87561fab594e0cfdc4d119e3', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:18:03', NULL, 0, NULL, '2026-10-03 11:18:03', '2026-10-03 11:18:03'),
(260, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '9e35cfc0d6f4273f7c8105f561e19f6b9cd9b1d96742728c909925c0451372cc', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:18:20', '2026-10-03 11:18:20', 2, '2026-10-03 11:18:20', '2026-10-03 11:18:14', '2026-10-03 11:18:20'),
(261, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'f3702a75039a68994131061732617b7fa1e2f1611d9636878e9671136780f028', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:38:44', '2026-10-03 11:38:44', 5, '2026-10-03 11:38:44', '2026-10-03 11:38:34', '2026-10-03 11:38:44'),
(262, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'abae7313d015ffd789ed9e00b4701e77fb7672f850782923dd587636d9943d28', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:38:54', '2026-10-03 11:38:54', 6, '2026-10-03 11:38:54', '2026-10-03 11:38:43', '2026-10-03 11:38:54');
INSERT INTO `site_visits` (`id`, `user_id`, `visitor_id`, `public_token_hash`, `page_key`, `route_name`, `url`, `referrer`, `visitable_type`, `visitable_id`, `ip_address`, `country`, `country_code`, `city`, `region`, `latitude`, `longitude`, `device`, `device_type`, `browser`, `browser_version`, `os`, `os_version`, `user_agent`, `started_at`, `last_activity_at`, `duration_seconds`, `ended_at`, `created_at`, `updated_at`) VALUES
(263, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '24d58dc1e3fe681bc89844f6a21c35b466c674097899a21b0dd27aa41531f264', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:39:41', '2026-10-03 11:39:41', 3, '2026-10-03 11:39:41', '2026-10-03 11:39:32', '2026-10-03 11:39:41'),
(264, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '2b99a1dcd02fbd27ce18c5bc4cfebb57f46a93d9bd45edc84d85794981c2a1a1', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/account', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:40:47', '2026-10-03 11:40:47', 31, NULL, '2026-10-03 11:39:58', '2026-10-03 11:40:47'),
(265, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '623aa3ff76d44e0554be281e3046766239ce54a7f8b44183a955fdfb28ff407c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:46:32', NULL, 0, NULL, '2026-10-03 11:46:32', '2026-10-03 11:46:32'),
(266, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '1d8a3a7657f9fc6125f57ce21ed661fafbc1ef637559679c9bb0bb2e7784d442', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:47:28', NULL, 0, NULL, '2026-10-03 11:47:28', '2026-10-03 11:47:28'),
(267, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'caf90088e75f1cbb9c7433cc65286d7d7d32699054aa67f96bfc9ef00b9fcd4d', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:48:28', NULL, 0, NULL, '2026-10-03 11:48:28', '2026-10-03 11:48:28'),
(268, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ec812360a7936735dd0ca9e93e3f8997692baa0def0a05bdd63047057e403d02', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 11:51:17', '2026-10-03 11:51:17', 14, '2026-10-03 11:51:17', '2026-10-03 11:48:58', '2026-10-03 11:51:17'),
(269, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '446148fd6b2af5a5ea1c47f87367d875ca0ad9d2f5991f0bc1277f5529603ea0', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/account', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 12:04:15', '2026-10-03 12:04:15', 12, '2026-10-03 12:04:15', '2026-10-03 12:03:40', '2026-10-03 12:04:15'),
(270, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'eb56dd839f1eca56ab264c880ceba3dd4ec37c0dc3eb19e844421a044915dd9b', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 12:09:22', '2026-10-03 12:09:22', 7, '2026-10-03 12:09:22', '2026-10-03 12:04:14', '2026-10-03 12:09:22'),
(271, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '34eaec3f5ee6aeaf2a89e48ccc58633b278114b4bc796da9e832b13eddbdec23', 'sabqa-shumare', 'sabqa-shumare', 'http://localhost:8000/sabqa-shumare', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 12:10:50', '2026-10-03 12:10:50', 43, '2026-10-03 12:10:50', '2026-10-03 12:09:21', '2026-10-03 12:10:50'),
(272, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'b70c2c5c13024964a5b5acf9eb349b158d7d3258d637fedc04dc5dad09da2c00', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 12:12:02', '2026-10-03 12:12:02', 13, '2026-10-03 12:12:02', '2026-10-03 12:10:49', '2026-10-03 12:12:02'),
(273, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'c3d91ad73f446d7a0bfc36f71fb458491429fd58bb677c4383efb78bdd29be15', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/sabqa-shumare', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-03 12:13:08', '2026-10-03 12:13:08', 22, NULL, '2026-10-03 12:12:01', '2026-10-03 12:13:08'),
(274, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '8d21992417ee2b45ae65ea29a951e1b34e58a10321752aac7646b4c71a63bbb6', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 04:59:27', '2026-10-05 04:59:27', 646, NULL, '2026-10-05 04:23:47', '2026-10-05 04:59:27'),
(275, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '8f95e978ed1084682dbac083ce5091f4cddfae82be109f6528b5e4d88a7cb11c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:15:02', '2026-10-05 05:15:02', 1, '2026-10-05 05:15:02', '2026-10-05 05:04:21', '2026-10-05 05:15:02'),
(276, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '13b3518f24002e8573fe663177fc1f66c797445f01718bc99803df2a869333bd', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:18:34', '2026-10-05 05:18:34', 10, NULL, '2026-10-05 05:15:02', '2026-10-05 05:18:34'),
(277, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '12238b6f303bc10f8d8053e9e23270b3944be65190a918d76200885e86c90093', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:19:59', '2026-10-05 05:19:59', 5, NULL, '2026-10-05 05:19:05', '2026-10-05 05:19:59'),
(278, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '53c1db2a91c23a7ad3ab67e77f31a3e05c852cb3bff81fec06051944b4a7ff6b', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:25:08', '2026-10-05 05:25:08', 2, NULL, '2026-10-05 05:21:24', '2026-10-05 05:25:08'),
(279, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'e8ff79b3d2f17179d56c5316abdcc63ef4d0f2d497fac9e46fcd7676f73514f8', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:25:22', '2026-10-05 05:25:22', 5, NULL, '2026-10-05 05:25:07', '2026-10-05 05:25:22'),
(280, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '994bea32b4c8d506701fd47f85dfc76f2fb56edbf4a94e8369b212c7110225ad', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:26:45', NULL, 0, NULL, '2026-10-05 05:26:45', '2026-10-05 05:26:45'),
(281, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '7c4062cad3b01901a12f7cde3db250ccc89e2939bd95e3786975b8b25b6f6245', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:27:38', NULL, 0, NULL, '2026-10-05 05:27:38', '2026-10-05 05:27:38'),
(282, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '7d3706004d8f371de16c15619d40ac8bc469f229dd4e3b9f562ce6083bd8b559', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:28:17', NULL, 0, NULL, '2026-10-05 05:28:17', '2026-10-05 05:28:17'),
(283, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '72ecc6034db580e95fc544b7a751c3679d2b50b8399279a7d667c8726e289787', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:31:29', '2026-10-05 05:31:29', 12, NULL, '2026-10-05 05:29:05', '2026-10-05 05:31:29'),
(284, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '64252cf13e7e79c6cb334a3f305e3dc023ce4577b1d69dc8e3e96da0b82565b7', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:42:52', '2026-10-05 05:42:52', 123, NULL, '2026-10-05 05:32:11', '2026-10-05 05:42:52'),
(285, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '31f9bf9726dce7e5ef4e5384ba83830d70bbf2173dfabec63cd94663b4326731', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:51:32', '2026-10-05 05:51:32', 3, NULL, '2026-10-05 05:43:48', '2026-10-05 05:51:32'),
(286, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'e4595249baabaa082a7afe821d6703ec05f3b17a11313bea6a61f4ba807c7cf1', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:57:53', NULL, 0, NULL, '2026-10-05 05:57:53', '2026-10-05 05:57:53'),
(287, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '2ab6f31375e64d3fca16c1738e32bd07da56cd594293ebfc681f552a746bca19', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:58:02', NULL, 0, NULL, '2026-10-05 05:58:02', '2026-10-05 05:58:02'),
(288, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ac728a3bdb74a24ba1bdaa8c2e403616ef61f56a0d089ee4d20f3dbb406e437b', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:58:27', NULL, 0, NULL, '2026-10-05 05:58:27', '2026-10-05 05:58:27'),
(289, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '9f873aa7f6b0d001034b0f02c3c951bd3db335b9d79efe8a1541f5ac6a4e6e93', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:58:37', NULL, 0, NULL, '2026-10-05 05:58:37', '2026-10-05 05:58:37'),
(290, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'bc852d1cb2da899aa60a0b94144e285cd3913ee85e76579aa0a248a0232d31ab', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:58:46', NULL, 0, NULL, '2026-10-05 05:58:46', '2026-10-05 05:58:46'),
(291, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'b5ba16b60bce8ab403c41333a5c7070cf672d016efa869e0c89497c7803a43ff', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:58:54', NULL, 0, NULL, '2026-10-05 05:58:54', '2026-10-05 05:58:54'),
(292, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'd60f99ca34d5be6a8601978093dc57b26d8d8ec26549f78d967957abf680d33b', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:59:01', NULL, 0, NULL, '2026-10-05 05:59:01', '2026-10-05 05:59:01'),
(293, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ff811c7483241bebeefec818c7c6bc2b6f312548d8bb06da891d91a0988029a1', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 05:59:22', NULL, 0, NULL, '2026-10-05 05:59:22', '2026-10-05 05:59:22'),
(294, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'aa5cd7b4db72dfa62b5b8f10e706ccf7481692646892f18b9c5334cb7160d47b', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:03:09', NULL, 0, NULL, '2026-10-05 06:03:09', '2026-10-05 06:03:09'),
(295, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '8c9d4ad1288d3d612c7590ab07dbfa29089e53f7292863700bd1878dd75c7f98', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:04:05', NULL, 0, NULL, '2026-10-05 06:04:05', '2026-10-05 06:04:05'),
(296, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'f4074e806731b38acc9c6b5dd0c716b463ccdcccd7e26111b679de421d161709', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:05:30', NULL, 0, NULL, '2026-10-05 06:05:30', '2026-10-05 06:05:30'),
(297, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '444f72572ea410186ff369a14a1df63bee8068bb96332209c6cb591ca2024bf8', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:07:37', NULL, 0, NULL, '2026-10-05 06:07:37', '2026-10-05 06:07:37'),
(298, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'f5108ac41bbe6effd63cdff8b833846b8d73c6ccb56348eac7a7426340786c76', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:08:53', NULL, 0, NULL, '2026-10-05 06:08:53', '2026-10-05 06:08:53'),
(299, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'e1e2e07a224fcf20c4061a5cd70acc46886a85e8a0ae7e73b82671de96d31978', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:09:02', NULL, 0, NULL, '2026-10-05 06:09:02', '2026-10-05 06:09:02'),
(300, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '0601dc3e3155509e458f66bd2f0fe1291a05fcd9e0633f4bf09ce3bf883abce6', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:10:47', NULL, 0, NULL, '2026-10-05 06:10:47', '2026-10-05 06:10:47'),
(301, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '71f1dfa7811f324d9b0ab8925e4ef8f2ba8cfb059453ce2290a99b1db4d767b9', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:11:18', NULL, 0, NULL, '2026-10-05 06:11:18', '2026-10-05 06:11:18'),
(302, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '6dc859a6f57a423682b3b0c4ebb87dcc39d1a16814fc127b29e78906d2773a5e', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:34:34', '2026-10-05 06:34:34', 48, NULL, '2026-10-05 06:32:36', '2026-10-05 06:34:34'),
(303, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'dcf1e3be9b057c886397c3ea084f3e5780e458a3ec20cd1e32901c983765473a', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:40:41', NULL, 0, NULL, '2026-10-05 06:40:41', '2026-10-05 06:40:41'),
(304, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'b7376cd75d5de1c451f590e931d5fbfc13680e3416ccd5f60614427f59e9b813', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:41:41', NULL, 0, NULL, '2026-10-05 06:41:41', '2026-10-05 06:41:41'),
(305, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '60b10bbb55e734daf01470857f0ae5fecc838fe1fb3ac40005584e0b10494e4e', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:42:10', NULL, 0, NULL, '2026-10-05 06:42:10', '2026-10-05 06:42:10'),
(306, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'd7df446b5ddcab457fd0f1a9a4eebcaf3d1d216234ab8c791494a7fbbdc8444d', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:44:58', NULL, 0, NULL, '2026-10-05 06:44:58', '2026-10-05 06:44:58'),
(307, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'dafe59481109bf09536f5b79e0053542c9e07582d75b8656025d0f52433c7287', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:46:58', '2026-10-05 06:46:58', 2, NULL, '2026-10-05 06:45:45', '2026-10-05 06:46:58'),
(308, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '7060c85558596882bb501dde9f6c038c874ba8a2ad6fd04352f8204c59abe32e', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:48:45', NULL, 0, NULL, '2026-10-05 06:48:45', '2026-10-05 06:48:45'),
(309, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'c06960e29d4b8fa80bb74bdd8f0df00070e281118d1467f38f278a03f5838288', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:49:56', NULL, 0, NULL, '2026-10-05 06:49:56', '2026-10-05 06:49:56'),
(310, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'b687dbf943d360cf7d130f83e981f6b0e460ed05c86e1af48c52937e23270f7a', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:50:35', NULL, 0, NULL, '2026-10-05 06:50:35', '2026-10-05 06:50:35'),
(311, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '5fc1b824f42882d3c5b48d8def11d0bb2c0d3d5d39d52128a6935441787663fb', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:51:52', '2026-10-05 06:51:52', 1, '2026-10-05 06:51:52', '2026-10-05 06:51:22', '2026-10-05 06:51:52'),
(312, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'f4d596ecaed632f00405b252d13a8568e2131a395576a48b8368ad68bab70608', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:52:15', '2026-10-05 06:52:15', 10, '2026-10-05 06:52:15', '2026-10-05 06:51:51', '2026-10-05 06:52:15'),
(313, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '5f95e494a801708e61d9ea6143a2ad83102ee4a6521d02edadb85461df2984e6', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:56:14', '2026-10-05 06:56:14', 131, '2026-10-05 06:56:14', '2026-10-05 06:52:14', '2026-10-05 06:56:14'),
(314, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '6d3d16cf415adedf0c6cba81548b6cdac6431d2a44605e838555b2724b488d6c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:57:18', '2026-10-05 06:57:18', 29, '2026-10-05 06:57:18', '2026-10-05 06:56:13', '2026-10-05 06:57:18'),
(315, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '7575b735c61bb0cdf5654c8ba5212f00d8914b31b9fbab13955a295ee1e0f1db', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/mazameen', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 06:59:07', '2026-10-05 06:59:07', 53, NULL, '2026-10-05 06:57:17', '2026-10-05 06:59:07'),
(316, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '2828f39034c82a011d3c76ecbfed6443fe1a44ac6403b0280e5e70a76e501fb9', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 07:02:24', NULL, 0, NULL, '2026-10-05 07:02:24', '2026-10-05 07:02:24'),
(317, 1, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '139dfbc038f8bdc373451418da09cb9813256deeb750382522db0e105f0cb85c', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 07:18:07', '2026-10-05 07:18:07', 4, NULL, '2026-10-05 07:02:45', '2026-10-05 07:18:07'),
(318, 17, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '324299d6db212a2e957ae956bdce58fcdd884b7afa1df801dd592ba2b8c44cf5', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:37:51', '2026-10-05 11:37:51', 14, '2026-10-05 11:37:51', '2026-10-05 11:37:16', '2026-10-05 11:37:51'),
(319, NULL, 'cf003d69-36ad-41cb-810d-f12a12f11756', '263dcbff5f9dd56bf7783d198e7d9d6856fe2ecfb8de7279b1765af4219a6cc5', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-05 11:42:55', '2026-10-05 11:42:55', 12, '2026-10-05 11:42:55', '2026-10-05 11:42:29', '2026-10-05 11:42:55'),
(320, NULL, 'cf003d69-36ad-41cb-810d-f12a12f11756', '8ea1f5e63997722107e45795a99f236d06943a61c73a505235523fc6c927837f', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-05 11:47:38', '2026-10-05 11:47:38', 8, NULL, '2026-10-05 11:42:54', '2026-10-05 11:47:38'),
(321, NULL, 'cf003d69-36ad-41cb-810d-f12a12f11756', '63d348726581e2de809010b7e02b5ddabc42a6ef980db41c571299621a22c5ac', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-05 12:01:40', '2026-10-05 12:01:40', 11, '2026-10-05 12:01:40', '2026-10-05 11:52:21', '2026-10-05 12:01:40'),
(322, NULL, 'cf003d69-36ad-41cb-810d-f12a12f11756', '242d44ae21453043cb2d1270d1579a1167a983f18482cd1a92a660846c3f95d3', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-05 12:14:55', '2026-10-05 12:14:55', 6, '2026-10-05 12:14:55', '2026-10-05 12:01:40', '2026-10-05 12:14:55'),
(323, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '4face5ca040688a6de39806acd58f55f7a195346f1d319ccd948d377a99ddde2', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 04:37:03', NULL, 0, NULL, '2026-10-06 04:37:03', '2026-10-06 04:37:03'),
(324, NULL, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '42860c8fe53c7a21f0b770ac9c63e8715e7aaee097e89dd8724d7300e3636504', 'home', 'frontend.home', 'http://localhost:8000', NULL, NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 06:02:51', '2026-10-06 06:02:51', 2, '2026-10-06 06:02:51', '2026-10-06 06:02:44', '2026-10-06 06:02:51'),
(325, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'e2fd47e78ef47d99322698d1df3bbad9fa780cbbc2e39aa67c41c7758f7d5902', 'subscriptions', 'front.subscriptions', 'http://localhost:8000/subscriptions', 'http://localhost/account/subscriptions/3/videos', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 06:06:39', '2026-10-06 06:06:39', 4, '2026-10-06 06:06:39', '2026-10-06 06:06:31', '2026-10-06 06:06:39'),
(326, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ea566360c8740a6b998c93651aec4889f3cedef71828c114452d5a45c18f0d85', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/account/subscriptions', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 06:11:53', '2026-10-06 06:11:53', 16, '2026-10-06 06:11:53', '2026-10-06 06:11:21', '2026-10-06 06:11:53'),
(327, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '274932a24528489def639542010f92fdd3ba6b3368cb061c5a21cea9b7fde0fa', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/account/subscriptions/8/videos', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 06:51:23', NULL, 0, NULL, '2026-10-06 06:51:23', '2026-10-06 06:51:23'),
(328, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'b307c6fa3f5003a637bb64a8380b3756c490bb48b20c8116c4d670a582410287', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 06:53:56', NULL, 0, NULL, '2026-10-06 06:53:56', '2026-10-06 06:53:56'),
(329, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '0726a2cea592aad186b85e1d8fd4f6f1197f00a1c8eadbbd89848aedd6940ca5', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 06:59:16', NULL, 0, NULL, '2026-10-06 06:59:16', '2026-10-06 06:59:16'),
(330, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'c3a24eb86095c29c65876555094af6ac3d01044b7d895c27dff2b3593c8015f5', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:20:11', NULL, 0, NULL, '2026-10-06 07:20:11', '2026-10-06 07:20:11'),
(331, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '0f18c85df3cf07923265ce6a52ccbe54e95e3db241a6555f290f73de83996cdd', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:20:36', NULL, 0, NULL, '2026-10-06 07:20:36', '2026-10-06 07:20:36'),
(332, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '80f9d0273e7ee0413c92071e3d90d4dbd191cedc362186acd84234c86da62fc1', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:21:30', NULL, 0, NULL, '2026-10-06 07:21:30', '2026-10-06 07:21:30'),
(333, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ab67ab11010af87729e439f29f79c3f1c866b29790d409d58ae0323bba59ea27', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:22:11', NULL, 0, NULL, '2026-10-06 07:22:11', '2026-10-06 07:22:11'),
(334, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'b4cda893261227121187f988094a4ecf87492af22a1a984d1395a6903ff15440', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:23:11', NULL, 0, NULL, '2026-10-06 07:23:11', '2026-10-06 07:23:11'),
(335, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'bd1b59ce849aff28fb25782a18c2a682ecae143f54ad3a6aa08176e875ef3624', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:24:10', NULL, 0, NULL, '2026-10-06 07:24:10', '2026-10-06 07:24:10'),
(336, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '5bfe15e0e52a958e5bb5ec810cbeca61284da21677c00056194f29b5e9c9c27a', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:24:52', NULL, 0, NULL, '2026-10-06 07:24:52', '2026-10-06 07:24:52'),
(337, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'ee4286cf9251deca2914eedd308f9ef489d6e67c3403f3d344a8e9fa0aef7eeb', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:25:10', NULL, 0, NULL, '2026-10-06 07:25:10', '2026-10-06 07:25:10'),
(338, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '879492a8a489e26729c6b883b58bff73b3c29180d02cb732d44785518d1e8516', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:26:11', NULL, 0, NULL, '2026-10-06 07:26:11', '2026-10-06 07:26:11'),
(339, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'e181631d474558d508b9e252004ad38efdd3182624a90b98b2890714d4aa153b', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:26:47', NULL, 0, NULL, '2026-10-06 07:26:47', '2026-10-06 07:26:47'),
(340, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', '8363d2e2a6d298f1d60adf7cfa575b3f6c136acafc4d52310967ac9629fbb33a', 'home', 'frontend.home', 'http://localhost:8000', 'http://localhost/', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:27:25', '2026-10-06 07:27:25', 9, '2026-10-06 07:27:25', '2026-10-06 07:27:07', '2026-10-06 07:27:25'),
(341, 14, '28ccac0e-128e-4ebf-941f-d696a49b06f3', 'f7c4ce44761cd2cd98cc0062585f8417c22c13805909a0aab17b9d7820708899', 'mazameen', 'mazameen', 'http://localhost:8000/mazameen', 'http://localhost/consultancies/1', NULL, NULL, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 'Computer', 'desktop', 'Chrome', '154.0.0.0', 'Windows NT', '10.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 07:33:36', '2026-10-06 07:33:36', 3, '2026-10-06 07:33:36', '2026-10-06 07:33:30', '2026-10-06 07:33:36');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `content_position` varchar(255) DEFAULT NULL,
  `top_heading` varchar(255) DEFAULT NULL,
  `main_heading` varchar(255) DEFAULT NULL,
  `bottom_text` varchar(500) DEFAULT NULL,
  `button_label` varchar(255) DEFAULT NULL,
  `button_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `language`, `content_position`, `top_heading`, `main_heading`, `bottom_text`, `button_label`, `button_url`, `image`, `isActive`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'ur', 'top', 'اہم موضوع', 'بدلتے شہروں میں زندگی، ثقافت اور نئی نسل کے خواب', 'پاکستان کے شہری منظرنامے پر ایک جامع اور فکر انگیز خصوصی رپورٹ، جو آج اور آنے والے کل                                         کی نئی تصویر پیش کرتی ہے۔', 'مکمل پڑھیں', 'weekly-magazines', 'images/backend-images/slider/slider-1.png', 1, '2026-08-19 04:11:51', '2026-09-01 05:55:05', 1, 1),
(2, 'ur', 'center', 'ادب و ثقافت', 'کتاب، قاری اور ڈیجیٹل عہد میں مطالعے کی نئی صورتیں', 'مطالعے کی بدلتی عادات اور اردو ادب کے نئے امکانات کا ایک خصوصی جائزہ۔', 'مکمل پڑھیں', 'weekly-magazines', 'images/backend-images/slider/slider-2.png', 1, '2026-09-01 05:56:29', '2026-09-01 05:56:29', 1, 1),
(3, 'ur', 'bottom', 'ٹیکنالوجی', 'مصنوعی ذہانت: مستقبل کے امکانات اور نئے چیلنجز', 'ٹیکنالوجی کی تیز رفتار تبدیلی ہماری روزمرہ زندگی اور کام کے انداز کو کیسے بدل رہی ہے؟', 'مکمل پڑھیں', 'weekly-magazines', 'images/backend-images/slider/slider-3.png', 1, '2026-09-01 05:57:49', '2026-09-01 05:57:49', 1, 1),
(4, 'en', 'top', 'Demo', 'Demo', 'demo headings testing', 'arrow', NULL, 'images/backend-images/slider/App-banner.png', 1, '2026-10-05 11:34:30', '2026-10-05 11:34:30', 17, 17);

-- --------------------------------------------------------

--
-- Table structure for table `storage_providers`
--

CREATE TABLE `storage_providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `disk` varchar(255) DEFAULT NULL,
  `provider_type` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `health_status` varchar(255) DEFAULT NULL,
  `last_checked_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storage_providers`
--

INSERT INTO `storage_providers` (`id`, `name`, `slug`, `disk`, `provider_type`, `is_active`, `is_default`, `priority`, `health_status`, `last_checked_at`, `created_at`, `updated_at`) VALUES
(1, 'Local Server', 'local', 'local_media', 'local', 1, 1, 1, 'unknown', NULL, '2026-08-22 01:13:50', '2026-08-22 01:13:50'),
(2, 'Cloudflare R2', 'r2', 'r2', 'r2', 0, 0, 2, 'unknown', NULL, '2026-08-22 01:13:50', '2026-08-22 01:13:50'),
(3, 'Amazon S3', 's3', 's3', 's3', 0, 0, 3, 'unknown', NULL, '2026-08-22 01:13:50', '2026-08-22 01:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_expiry_notifications`
--

CREATE TABLE `subscription_expiry_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_subscription_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_expiry_notifications`
--

INSERT INTO `subscription_expiry_notifications` (`id`, `user_subscription_id`, `status`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'sent', '2026-09-12 00:32:35', '2026-09-12 00:32:02', '2026-09-12 00:32:35'),
(2, 3, 'sent', '2026-09-12 00:32:35', '2026-09-12 00:32:02', '2026-09-12 00:32:35');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_expiry_reminders`
--

CREATE TABLE `subscription_expiry_reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_subscription_id` bigint(20) UNSIGNED NOT NULL,
  `reminder_days` int(10) UNSIGNED NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_expiry_reminders`
--

INSERT INTO `subscription_expiry_reminders` (`id`, `user_subscription_id`, `reminder_days`, `sent_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 5, '2026-09-11 07:21:53', 'sent', '2026-09-11 07:13:14', '2026-09-11 07:21:53'),
(2, 3, 3, '2026-09-11 07:21:53', 'sent', '2026-09-11 07:13:14', '2026-09-11 07:21:53'),
(3, 4, 1, '2026-09-11 07:21:54', 'sent', '2026-09-11 07:13:14', '2026-09-11 07:21:54');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_notification_settings`
--

CREATE TABLE `subscription_notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_reminder_days` int(10) UNSIGNED DEFAULT NULL,
  `second_reminder_days` int(10) UNSIGNED DEFAULT NULL,
  `third_reminder_days` int(10) UNSIGNED DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_notification_settings`
--

INSERT INTO `subscription_notification_settings` (`id`, `first_reminder_days`, `second_reminder_days`, `third_reminder_days`, `isActive`, `created_at`, `updated_at`) VALUES
(1, 6, 3, 1, 1, '2026-09-11 05:12:18', '2026-10-05 11:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_products`
--

CREATE TABLE `subscription_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_for` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `currency_id` bigint(20) UNSIGNED DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `duration_value` int(10) UNSIGNED DEFAULT NULL,
  `duration_unit` varchar(255) DEFAULT NULL,
  `discount_type` varchar(255) DEFAULT NULL,
  `discount_value` decimal(12,2) DEFAULT NULL,
  `promotion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_products`
--

INSERT INTO `subscription_products` (`id`, `product_for`, `name`, `currency_id`, `price`, `duration_value`, `duration_unit`, `discount_type`, `discount_value`, `promotion_id`, `isActive`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'plan', 'Monthly Pack', 1, 250.00, 1, 'month', NULL, NULL, NULL, 1, '2026-08-25 05:09:36', '2026-08-25 05:09:36', 1, NULL),
(2, 'membership', 'Quarterly', 1, 1000.00, 3, 'month', NULL, NULL, NULL, 1, '2026-08-25 05:10:08', '2026-09-10 02:09:22', 1, 1),
(3, 'plan', 'Quarterly Pack', 1, 700.00, 3, 'month', NULL, NULL, NULL, 1, '2026-09-10 02:01:14', '2026-09-10 02:01:14', 1, 1),
(4, 'plan', 'Yearly Pack', 1, 2500.00, 1, 'year', NULL, NULL, NULL, 1, '2026-09-10 02:01:59', '2026-09-10 02:01:59', 1, 1),
(5, 'plan', 'Monthly Pack', 1, 200.00, 1, 'month', NULL, NULL, NULL, 1, '2026-09-10 02:05:07', '2026-09-10 02:05:07', 1, 1),
(6, 'plan', 'Quarterly Pack', 1, 500.00, 3, 'month', NULL, NULL, NULL, 1, '2026-09-10 02:05:29', '2026-09-10 02:05:29', 1, 1),
(7, 'plan', 'Yearly Pack', 1, 2000.00, 1, 'year', NULL, NULL, NULL, 1, '2026-09-10 02:06:14', '2026-09-10 02:06:14', 1, 1),
(8, 'membership', 'Half Yearly', 1, 2000.00, 6, 'month', NULL, NULL, NULL, 1, '2026-09-10 02:11:46', '2026-09-10 02:11:46', 1, 1),
(9, 'membership', 'Yearly', 1, 4000.00, 1, 'year', NULL, NULL, NULL, 1, '2026-09-10 02:12:18', '2026-09-10 02:12:18', 1, 1),
(11, 'plan', 'Yearly Pack updated', 1, 2000.00, 1, 'year', NULL, NULL, NULL, 1, '2026-10-05 10:56:57', '2026-10-05 10:57:17', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_product_types`
--

CREATE TABLE `subscription_product_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_product_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_type_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_product_types`
--

INSERT INTO `subscription_product_types` (`id`, `subscription_product_id`, `subscription_type_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-08-25 05:09:36', '2026-08-25 05:09:36'),
(2, 2, 2, '2026-08-25 05:10:08', '2026-08-25 05:10:08'),
(3, 2, 1, '2026-08-25 05:10:08', '2026-08-25 05:10:08'),
(5, 3, 1, '2026-09-10 02:01:15', '2026-09-10 02:01:15'),
(6, 4, 1, '2026-09-10 02:01:59', '2026-09-10 02:01:59'),
(7, 5, 2, '2026-09-10 02:05:07', '2026-09-10 02:05:07'),
(8, 6, 2, '2026-09-10 02:05:29', '2026-09-10 02:05:29'),
(9, 7, 2, '2026-09-10 02:06:14', '2026-09-10 02:06:14'),
(10, 8, 2, '2026-09-10 02:11:46', '2026-09-10 02:11:46'),
(11, 8, 1, '2026-09-10 02:11:46', '2026-09-10 02:11:46'),
(12, 9, 2, '2026-09-10 02:12:18', '2026-09-10 02:12:18'),
(13, 9, 1, '2026-09-10 02:12:18', '2026-09-10 02:12:18'),
(15, 11, 1, '2026-10-05 10:56:57', '2026-10-05 10:56:57');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_product_videos`
--

CREATE TABLE `subscription_product_videos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_product_id` bigint(20) UNSIGNED NOT NULL,
  `video_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_product_videos`
--

INSERT INTO `subscription_product_videos` (`id`, `subscription_product_id`, `video_id`, `created_at`, `updated_at`) VALUES
(1, 11, 1, '2026-10-05 10:56:57', '2026-10-05 10:56:57'),
(2, 11, 3, '2026-10-05 10:56:57', '2026-10-05 10:56:57');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_types`
--

CREATE TABLE `subscription_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_types`
--

INSERT INTO `subscription_types` (`id`, `name`, `slug`, `isActive`, `created_at`, `updated_at`) VALUES
(1, 'Video', 'video', 1, '2026-08-25 04:45:30', '2026-08-25 04:45:30'),
(2, 'Articles', 'articles', 0, '2026-08-25 04:45:30', '2026-08-25 04:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `language`, `name`, `isActive`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'ur', 'Sharia', 1, '2026-08-21 07:20:48', '2026-08-21 07:20:48', 1, NULL),
(2, 'ur', 'Islam', 1, '2026-08-21 07:20:48', '2026-08-21 07:20:48', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `taza_shumara`
--

CREATE TABLE `taza_shumara` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(255) NOT NULL,
  `magazine_id` bigint(20) UNSIGNED NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `show_title` tinyint(1) NOT NULL DEFAULT 1,
  `show_short_description` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taza_shumara`
--

INSERT INTO `taza_shumara` (`id`, `language`, `magazine_id`, `cover_image`, `show_title`, `show_short_description`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'ur', 1, 'images/backend-images/taza-shumara/20260903104636-AkQgDaXV5736.jpg', 1, 1, 1, 1, 1, '2026-09-03 02:59:12', '2026-09-03 05:46:36');

-- --------------------------------------------------------

--
-- Table structure for table `taza_shumara_articles`
--

CREATE TABLE `taza_shumara_articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `taza_shumara_id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `display_width` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `permission_mode` varchar(20) NOT NULL DEFAULT 'role',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activation_token` varchar(64) DEFAULT NULL,
  `activation_token_expires_at` timestamp NULL DEFAULT NULL,
  `consumed_activation_token_hash` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `profile_image`, `email_verified_at`, `password`, `is_active`, `permission_mode`, `remember_token`, `created_at`, `updated_at`, `created_by`, `updated_by`, `parent_admin_id`, `activation_token`, `activation_token_expires_at`, `consumed_activation_token_hash`) VALUES
(1, 'Super Admin', 'superadmin@digitalmagazine.com', NULL, NULL, NULL, '$2y$12$zsAxw0UxOjMVOLZYNYUZFea3GRsJ/Bo4tK67gSb4FNKAkF3bK9P8i', 1, 'role', NULL, '2026-08-18 05:14:04', '2026-08-18 07:12:39', 1, NULL, NULL, NULL, NULL, NULL),
(2, 'Test User', 'test@example.com', NULL, NULL, '2026-08-25 04:45:32', '$2y$12$OcKKv/yUxm2mp4MUA1l6SOGLbjGPPU43mZiwnysWWuvr5v5d.ZWRO', 1, 'role', 'dipYcEj6Fn', '2026-08-25 04:45:33', '2026-08-25 04:45:33', 1, NULL, NULL, NULL, NULL, NULL),
(3, 'Muhammad Muzammil', 'muzammilken5@gmail.com', '03460329659', NULL, NULL, '$2y$12$G962YGOELBOKHZBgzhAgFebxlXkXCDS/eGOppO/exuM7s2NqYlab2', 1, 'custom', NULL, '2026-08-27 06:18:40', '2026-08-28 00:22:12', 1, 1, 1, NULL, NULL, NULL),
(4, 'Hammad Khan', 'hammadkhan@gmail.com', '03111010109', NULL, NULL, '$2y$12$ZxyAp.70YvekPtPbxoWxS.4jVCLsXgYhNZ0bKMSXO63PKLL34kx2C', 1, 'role', NULL, '2026-08-28 04:40:52', '2026-08-29 04:41:36', 3, 1, 5, NULL, NULL, NULL),
(5, 'Adil Khan', 'adilkhan@gmail.com', NULL, NULL, NULL, '$2y$12$q1bcZg1O4FaPY6r3hvxqUuAmpGGOZlWVuTqq7I5ZRfYw5p5eftnbS', 1, 'role', NULL, '2026-08-29 02:03:49', '2026-08-29 02:03:49', 1, 1, 1, NULL, NULL, NULL),
(6, 'Usama Khan', 'usamakhan@gmail.com', '03319998780', NULL, NULL, '$2y$12$d4fft4jPCriNafgkf9EPyeUdvStcHLr4vvdtLVbz4IAHefXEKH2a2', 1, 'role', NULL, '2026-08-29 02:12:32', '2026-08-29 04:41:36', 3, 1, 5, NULL, NULL, NULL),
(14, 'Muhammad Muzammil', 'muzammilken95@gmail.com', '03121234567', 'user-avatar.png', NULL, '$2y$12$su/aqEyCKg18m78C1guKBuB5ZEPoVIqC1AZ92iJNi2GwF9xAr.Ehu', 1, 'role', 'Q8jwFp0VEs2W7UkthoKSXb0RW1qKMcqKisIem010GMwThgLXOB1Y1KuaBULu', '2026-09-09 04:40:30', '2026-09-22 07:50:26', NULL, 14, NULL, NULL, NULL, '0e82496bc16cacd84fbcd33b772bf917d3d613145a6c0b18bd91dd6572e7c97c'),
(15, 'Updated New', 'example-updated@new.com', '03121234567', 'user-avatar.png', NULL, '$2y$12$507t3qhl.Ig97pAU4Ql8UOYDmMAw9/bbnypoKabsvOg46NLq9fxO.', 1, 'role', 'eKQ34r2YgsCaS1kaBiSXVLnHYTN0lpIan2pabZPMUlIvFzWMc0FAdkoSQw0L', '2026-09-19 07:50:07', '2026-09-19 11:14:43', NULL, 15, NULL, NULL, NULL, '6a94de4fe0121c3e4b9301a26d60b164e0b7c6a7e13b1796ab2e720b311f49e6'),
(16, 'API Test User', 'muzammil@gmail.com', '03001234567', 'user-avatar.png', NULL, '$2y$12$ECiuxnt4x/QvOjzg7BNjS.wMCtWOrg0vjsA9RpW5Y8QgyyD/UR69i', 0, 'role', NULL, '2026-09-22 07:29:35', '2026-09-22 07:29:35', NULL, NULL, NULL, '9b1beca8ae9f04debb66990650cc0fadb0871843df95ccd9299d555ed74c9be8', '2026-09-23 07:29:35', NULL),
(17, 'hammad khan', 'hammad2930029@gmail.com', '03172930029', NULL, NULL, '$2y$12$ZVfKcj4bgucWCAysv9lRleM98E4.RWes24wHvM0.Dw0P/2QwGsWhK', 1, 'role', NULL, '2026-10-05 11:12:05', '2026-10-05 11:21:37', 1, 1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_content_visits`
--

CREATE TABLE `user_content_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `visitable_type` varchar(255) NOT NULL,
  `visitable_id` bigint(20) UNSIGNED NOT NULL,
  `last_visited_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_content_visits`
--

INSERT INTO `user_content_visits` (`id`, `user_id`, `visitable_type`, `visitable_id`, `last_visited_at`, `created_at`, `updated_at`) VALUES
(1, 14, 'App\\Models\\Magazine', 1, '2026-09-24 07:14:35', '2026-09-12 09:40:48', '2026-09-24 07:14:35'),
(2, 14, 'App\\Models\\Magazine', 2, '2026-09-14 11:42:27', '2026-09-12 09:40:50', '2026-09-14 11:42:27'),
(3, 14, 'App\\Models\\Article', 4, '2026-09-14 11:43:57', '2026-09-12 09:41:14', '2026-09-14 11:43:57'),
(4, 14, 'App\\Models\\Article', 3, '2026-09-14 09:50:32', '2026-09-12 09:41:15', '2026-09-14 09:50:32'),
(5, 14, 'App\\Models\\Article', 2, '2026-09-24 07:13:30', '2026-09-12 09:41:18', '2026-09-24 07:13:30'),
(6, 14, 'App\\Models\\Article', 1, '2026-09-24 07:18:31', '2026-09-12 09:41:20', '2026-09-24 07:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_no` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_no` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_product_id` bigint(20) UNSIGNED NOT NULL,
  `product_for` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `currency_id` bigint(20) UNSIGNED DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_slip` varchar(255) DEFAULT NULL,
  `payment_status` varchar(32) DEFAULT NULL,
  `payment_submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `duration_value_snapshot` int(10) UNSIGNED DEFAULT NULL,
  `duration_unit_snapshot` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`id`, `order_no`, `invoice_no`, `transaction_id`, `user_id`, `subscription_product_id`, `product_for`, `product_name`, `currency_id`, `price`, `discount`, `total`, `payment_method`, `payment_account_id`, `payment_slip`, `payment_status`, `payment_submitted_at`, `reviewed_at`, `reviewed_by`, `rejection_reason`, `duration_value_snapshot`, `duration_unit_snapshot`, `start_date`, `end_date`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 14, 5, 'plan', 'Monthly Pack', 1, 200.00, 0.00, 200.00, 'card', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-10', '2026-10-10', 'deactive', 0, '2026-09-10 05:52:01', '2026-09-10 05:52:01'),
(2, NULL, NULL, NULL, 14, 5, 'plan', 'Monthly Pack', 1, 200.00, 0.00, 200.00, 'card', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-11', '2026-10-11', 'active', 1, '2026-09-11 06:14:35', '2026-09-12 00:32:02'),
(3, NULL, NULL, NULL, 14, 1, 'plan', 'Monthly Pack', 1, 250.00, 0.00, 250.00, 'card', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-11', '2026-10-11', 'active', 1, '2026-09-11 06:15:27', '2026-09-12 00:32:02'),
(4, NULL, NULL, NULL, 14, 2, 'membership', 'Quarterly', 1, 1000.00, 0.00, 1000.00, 'card', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-11', '2026-09-12', 'active', 1, '2026-09-11 06:16:08', '2026-09-11 06:16:08'),
(5, NULL, NULL, NULL, 14, 2, 'membership', 'Quarterly', 1, 1000.00, 0.00, 1000.00, 'card', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15', '2026-12-15', 'active', 1, '2026-09-15 05:59:29', '2026-09-15 05:59:29'),
(6, NULL, NULL, NULL, 14, 6, 'plan', 'Quarterly Pack', 1, 500.00, 0.00, 500.00, 'easypaisa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-24', '2026-12-24', 'active', 1, '2026-09-24 07:47:29', '2026-09-24 07:47:29'),
(7, 20261001, 20260001, NULL, 14, 3, 'plan', 'Quarterly Pack', 1, 700.00, 0.00, 700.00, 'bank_transfer', 1, 'payment-slips/OJY78i18WaO2ZtyFz6hsb3w9XDZlErK9OsQSVPTx.png', 'approved', '2026-10-03 10:37:18', '2026-10-03 10:41:48', 1, NULL, 3, 'month', '2026-10-03', '2027-01-03', 'active', 1, '2026-10-03 10:37:18', '2026-10-03 10:41:48'),
(8, 20261002, 20260002, '123456789', 14, 11, 'plan', 'Yearly Pack updated', 1, 2000.00, 0.00, 2000.00, 'bank_transfer', 2, 'payment-slips/t0cUtn42gRVVBE58rjqWLSFpc9uVSB3S2qyltKP4.png', 'approved', '2026-10-06 06:07:01', '2026-10-06 06:09:48', 1, NULL, 1, 'year', '2026-10-06', '2027-10-06', 'active', 1, '2026-10-06 06:07:01', '2026-10-06 06:09:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_sub_types`
--

CREATE TABLE `user_sub_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_subscription_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_type_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sub_types`
--

INSERT INTO `user_sub_types` (`id`, `user_subscription_id`, `subscription_type_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-09-10 05:52:01', '2026-09-10 05:52:01'),
(2, 2, 2, '2026-09-11 06:14:35', '2026-09-11 06:14:35'),
(3, 3, 1, '2026-09-11 06:15:27', '2026-09-11 06:15:27'),
(4, 4, 2, '2026-09-11 06:16:08', '2026-09-11 06:16:08'),
(5, 4, 1, '2026-09-11 06:16:08', '2026-09-11 06:16:08'),
(6, 5, 2, '2026-09-15 05:59:29', '2026-09-15 05:59:29'),
(7, 5, 1, '2026-09-15 05:59:29', '2026-09-15 05:59:29'),
(8, 6, 2, '2026-09-24 07:47:29', '2026-09-24 07:47:29'),
(9, 7, 1, '2026-10-03 10:37:18', '2026-10-03 10:37:18'),
(10, 8, 1, '2026-10-06 06:07:01', '2026-10-06 06:07:01');

-- --------------------------------------------------------

--
-- Table structure for table `user_two_factor_challenges`
--

CREATE TABLE `user_two_factor_challenges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `purpose` varchar(20) NOT NULL,
  `channel` varchar(10) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `consumed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_two_factor_challenges`
--

INSERT INTO `user_two_factor_challenges` (`id`, `user_id`, `purpose`, `channel`, `otp_hash`, `expires_at`, `attempts`, `consumed_at`, `created_at`, `updated_at`) VALUES
(1, 14, 'enable', 'email', '$2y$12$hMare0qkHKgbpVOzKPT46ujTrF690WoFVGsH9fntq8psR8hETs7BO', '2026-09-10 00:45:21', 0, '2026-09-10 00:37:35', '2026-09-10 00:35:21', '2026-09-10 00:37:35'),
(2, 14, 'login', 'email', '$2y$12$DerQLgnTBeBPoG3HjPZ74.KtPwjlFOevFKq/zT79xw2MfHUOmfeq2', '2026-09-10 00:48:47', 0, '2026-09-10 00:39:45', '2026-09-10 00:38:47', '2026-09-10 00:39:45'),
(3, 14, 'disable', 'email', '$2y$12$7l5YQYo3ZaXYWCq7.AmNweUvNNaDx0F9ma9/mOhdgYemSQbBlqvN6', '2026-09-10 00:50:15', 0, '2026-09-10 00:41:41', '2026-09-10 00:40:15', '2026-09-10 00:41:41'),
(4, 14, 'enable', 'email', '$2y$12$grXng3W4lDeMFFQ1.61Y1.iAT8hLM922g5XH8LINjfYRjn7Bone96', '2026-09-19 09:29:14', 0, '2026-09-19 09:35:04', '2026-09-19 09:19:14', '2026-09-19 09:35:04'),
(5, 14, 'enable', 'email', '$2y$12$FecJ2TyduuIKYg7vX3/HL.WNv5qyQP6tH1MuG6qOBcg1P4fU5MxP2', '2026-09-19 09:45:04', 0, '2026-09-19 09:35:24', '2026-09-19 09:35:04', '2026-09-19 09:35:24'),
(6, 14, 'login', 'email', '$2y$12$sYKQNOcMUypDNse6dY4sre337R1keqG4aG9jH0qT8tv9aaEnMzYp6', '2026-09-19 10:15:49', 1, '2026-09-19 10:08:19', '2026-09-19 10:05:49', '2026-09-19 10:08:19'),
(7, 15, 'enable', 'email', '$2y$12$5PVyvigvghwRCYeZM2VEkuFpqljRXyYfeCn0j2SRHvFcfWKPqCHK.', '2026-09-19 11:28:24', 0, '2026-09-19 11:22:58', '2026-09-19 11:18:24', '2026-09-19 11:22:58'),
(8, 15, 'enable', 'email', '$2y$12$TIQxRbeuQNGKtgg9G8MrzO42TPG8oKD.4QYGlmR33Q6XCDrP31CT2', '2026-09-19 11:32:58', 0, NULL, '2026-09-19 11:22:58', '2026-09-19 11:22:58'),
(9, 14, 'login', 'email', '$2y$12$wyTxSyoJCjlMH6Rb2VAWf.v7d4Zb6lcoF5HVCmpp5iv855X4dwgu2', '2026-09-19 11:33:53', 0, '2026-09-19 11:25:14', '2026-09-19 11:23:53', '2026-09-19 11:25:14'),
(10, 14, 'disable', 'email', '$2y$12$/CxpFpPruIMqQNP1l/uUb.05pd.hK4vXPNQW0cqPQgV1ObHC2FTB.', '2026-09-19 11:36:45', 0, '2026-09-19 11:27:03', '2026-09-19 11:26:45', '2026-09-19 11:27:03'),
(11, 14, 'enable', 'email', '$2y$12$AOtZKaa7ux95ZDjctlOW0.tz3UfNfdyGCmWrc6t5BZojX0sJkH3tO', '2026-09-19 11:37:18', 2, '2026-09-19 11:34:00', '2026-09-19 11:27:18', '2026-09-19 11:34:00'),
(12, 14, 'enable', 'email', '$2y$12$m3kPPuJRzh4NJRjOtL71SOp0Qmik6DApK20uxnymj68brOZLx5HI2', '2026-09-19 11:44:00', 0, '2026-09-19 11:35:28', '2026-09-19 11:34:00', '2026-09-19 11:35:28'),
(13, 14, 'disable', 'email', '$2y$12$aqnbHlHuEuof0ruoKKAu/OX4r6pRnLQAfu2o9X0.k0otLnAu1KR6G', '2026-09-19 11:49:39', 0, '2026-09-19 11:40:34', '2026-09-19 11:39:39', '2026-09-19 11:40:34'),
(14, 14, 'enable', 'email', '$2y$12$E0ATvWxxJiKTvfkuD0ovCeRUDvLtqnJ0GIDOilKcibf8wwJFm/w.6', '2026-09-22 07:45:09', 0, '2026-09-22 07:35:45', '2026-09-22 07:35:09', '2026-09-22 07:35:45'),
(15, 14, 'login', 'email', '$2y$12$WrGv9R8nutxaiqs/cBRWvOSlSkyseqNBK4w0LqiOxRpp1TVtdPTIy', '2026-09-22 07:45:58', 0, '2026-09-22 07:37:35', '2026-09-22 07:35:58', '2026-09-22 07:37:35'),
(16, 14, 'login', 'email', '$2y$12$3S9mvkdarymHXShEUWEzIud8bxvid2F8ao52KGnkR./4/hc74mK4y', '2026-09-22 07:49:18', 0, NULL, '2026-09-22 07:39:18', '2026-09-22 07:39:18'),
(17, 14, 'disable', 'email', '$2y$12$14m3NsAvtZqfXmHQlHDML.k27P6WElbTKh/SRzvBvHTNoqvh0oYqy', '2026-09-22 07:52:52', 0, '2026-09-22 07:43:23', '2026-09-22 07:42:52', '2026-09-22 07:43:23'),
(18, 14, 'enable', 'email', '$2y$12$DoPD6S.Yl5zHrtiGdCjmMelFh5EndUT5BkTibTxHgbLp.NhDk720W', '2026-09-22 08:02:15', 0, '2026-09-22 07:54:36', '2026-09-22 07:52:15', '2026-09-22 07:54:36'),
(19, 14, 'enable', 'email', '$2y$12$bvpYu0KcWGM2B9qgyfayIeNaeLTEufehJP3loJ13kT0aUOiiNOKrO', '2026-09-22 08:04:36', 0, '2026-09-22 07:55:25', '2026-09-22 07:54:36', '2026-09-22 07:55:25'),
(20, 14, 'disable', 'email', '$2y$12$CC9xmm6zAj9sXGnvR871aOWxUMyWAqhmLd47w6i0VZvqTsdirD7cO', '2026-09-22 08:06:59', 0, '2026-09-22 07:58:20', '2026-09-22 07:56:59', '2026-09-22 07:58:20'),
(21, 14, 'disable', 'email', '$2y$12$wnYwJ84Rx.4BgJW./trlz.aSVYq9HW3wpE6UQ3zIKXncsBET1QwRm', '2026-09-22 08:08:20', 0, '2026-09-22 09:24:25', '2026-09-22 07:58:20', '2026-09-22 09:24:25'),
(22, 14, 'disable', 'email', '$2y$12$v4Q0JHuaYlaV8Aib4NoLr.Em0EK8Q5eBo3rgbr4TpbL6rPqC5I3Xi', '2026-09-22 09:34:25', 0, '2026-09-22 09:24:53', '2026-09-22 09:24:25', '2026-09-22 09:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `user_two_factor_settings`
--

CREATE TABLE `user_two_factor_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `method` varchar(10) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_two_factor_settings`
--

INSERT INTO `user_two_factor_settings` (`id`, `user_id`, `method`, `is_enabled`, `verified_at`, `created_at`, `updated_at`) VALUES
(1, 14, NULL, 0, NULL, '2026-09-10 00:06:28', '2026-09-22 09:24:53'),
(2, 15, NULL, 0, NULL, '2026-09-19 07:50:07', '2026-09-19 07:50:07'),
(3, 16, NULL, 0, NULL, '2026-09-22 07:29:35', '2026-09-22 07:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `video_link` varchar(2048) NOT NULL,
  `short_description` text DEFAULT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_share` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) DEFAULT 'published',
  `published_at` datetime DEFAULT NULL,
  `published_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `thumbnail`, `video_link`, `short_description`, `is_free`, `is_active`, `is_share`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`, `published_at`, `published_by`) VALUES
(1, 'Depression Therapy', 'backend-images/video/01M3XSZP6NNRRPJ9NG2G9A8220.webp', 'https://youtu.be/oPWxv5tdltE', 'Feeling exhausted, disconnected, or unlike yourself lately?updated', 0, 1, 0, 1, 1, '2026-10-02 07:59:12', '2026-10-03 11:37:14', 'published', NULL, NULL),
(3, 'RS 5000 SURVIVAL CHALLENGE', 'backend-images/video/01M45V9038MJG71P7PNN61FAAC.jpg', 'https://www.youtube.com/watch?v=4ySwXseEP3M', 'Thanks for watching and feel free to leave a comment, suggestion or critique in the comments below.\r\nMake sure to SUBSCRIBE, it’s the best way to keep my videos in your feed and give me the thumbs up if you liked this video.\r\nTHANKS..updated', 0, 1, 0, 1, 1, '2026-10-05 10:55:41', '2026-10-05 10:55:55', 'published', NULL, NULL),
(4, 'demo', 'backend-images/video/01M47SP7DFK38QNKVS843ECQGA.png', 'https://youtu.be/w5dTKDMQLb8?si=Vf3Gx3i1q1A3rSwG', 'demo', 0, 1, 0, 1, 1, '2026-10-06 05:06:26', '2026-10-06 05:06:39', 'published', '2026-10-06 10:06:39', 1),
(5, 'demo', 'backend-images/video/01M47TD7RCXW4B8WDX1W89D08N.jpg', 'https://www.youtube.com/watch?v=yMY6o77cvn8', 'demo2', 0, 1, 0, 1, 1, '2026-10-06 05:19:00', '2026-10-06 05:19:00', 'draft', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abouts`
--
ALTER TABLE `abouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abouts_created_by_foreign` (`created_by`),
  ADD KEY `abouts_updated_by_foreign` (`updated_by`),
  ADD KEY `abouts_language_index` (`language`),
  ADD KEY `abouts_section_condition_index` (`section_condition`),
  ADD KEY `abouts_is_active_index` (`is_active`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`),
  ADD KEY `activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  ADD KEY `activity_logs_module_action_created_at_index` (`module`,`action`,`created_at`),
  ADD KEY `activity_logs_role_name_index` (`role_name`),
  ADD KEY `activity_logs_module_index` (`module`),
  ADD KEY `activity_logs_action_index` (`action`),
  ADD KEY `activity_logs_created_at_index` (`created_at`);

--
-- Indexes for table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ads_ad_request_placement_id_unique` (`ad_request_placement_id`),
  ADD KEY `ads_created_by_foreign` (`created_by`),
  ADD KEY `ads_updated_by_foreign` (`updated_by`),
  ADD KEY `ads_page_name_place_isactive_index` (`page_name`,`place`,`isActive`),
  ADD KEY `ads_language_index` (`language`),
  ADD KEY `ads_page_name_index` (`page_name`),
  ADD KEY `ads_place_index` (`place`),
  ADD KEY `ads_start_date_index` (`start_date`),
  ADD KEY `ads_expiry_date_index` (`expiry_date`),
  ADD KEY `ads_isactive_index` (`isActive`),
  ADD KEY `ads_ad_request_id_foreign` (`ad_request_id`);

--
-- Indexes for table `ad_clicks`
--
ALTER TABLE `ad_clicks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_clicks_ad_id_foreign` (`ad_id`),
  ADD KEY `ad_clicks_clicked_at_index` (`clicked_at`);

--
-- Indexes for table `ad_requests`
--
ALTER TABLE `ad_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ad_requests_request_no_unique` (`request_no`),
  ADD KEY `ad_requests_user_id_foreign` (`user_id`),
  ADD KEY `ad_requests_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `ad_request_placements`
--
ALTER TABLE `ad_request_placements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ad_request_placements_request_slot_unique` (`ad_request_id`,`page_name`,`place`),
  ADD KEY `ad_request_placements_slot_status_index` (`page_name`,`place`,`status`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articles_language_index` (`language`),
  ADD KEY `articles_publish_date_index` (`publish_date`),
  ADD KEY `articles_isfree_index` (`isFree`),
  ADD KEY `articles_isfeatured_index` (`isFeatured`),
  ADD KEY `articles_isactive_index` (`isActive`),
  ADD KEY `articles_status_index` (`status`),
  ADD KEY `articles_scheduled_at_index` (`scheduled_at`),
  ADD KEY `articles_published_at_index` (`published_at`),
  ADD KEY `articles_created_by_foreign` (`created_by`),
  ADD KEY `articles_updated_by_foreign` (`updated_by`),
  ADD KEY `articles_published_by_foreign` (`published_by`),
  ADD KEY `articles_owner_admin_id_index` (`owner_admin_id`),
  ADD KEY `articles_magazine_id_foreign` (`magazine_id`);

--
-- Indexes for table `article_author_maps`
--
ALTER TABLE `article_author_maps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article_author_maps_article_id_author_id_unique` (`article_id`,`author_id`),
  ADD KEY `article_author_maps_author_id_foreign` (`author_id`);

--
-- Indexes for table `article_category_maps`
--
ALTER TABLE `article_category_maps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article_category_maps_article_id_category_id_unique` (`article_id`,`category_id`),
  ADD KEY `article_category_maps_category_id_foreign` (`category_id`);

--
-- Indexes for table `article_tag_maps`
--
ALTER TABLE `article_tag_maps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article_tag_maps_article_id_tag_id_unique` (`article_id`,`tag_id`),
  ADD KEY `article_tag_maps_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `ask_questions`
--
ALTER TABLE `ask_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ask_questions_question_no_unique` (`question_no`),
  ADD KEY `ask_questions_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `ask_questions_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `authors_isactive_index` (`isActive`),
  ADD KEY `authors_created_by_foreign` (`created_by`),
  ADD KEY `authors_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `author_general_settings`
--
ALTER TABLE `author_general_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `author_general_settings_column_name_unique` (`column_name`),
  ADD KEY `author_general_settings_created_by_foreign` (`created_by`),
  ADD KEY `author_general_settings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `author_visibilities`
--
ALTER TABLE `author_visibilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `author_visibilities_author_id_column_name_unique` (`author_id`,`column_name`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banners_language_index` (`language`),
  ADD KEY `banners_type_index` (`type`),
  ADD KEY `banners_position_index` (`position`),
  ADD KEY `banners_isactive_index` (`isActive`),
  ADD KEY `banners_created_by_foreign` (`created_by`),
  ADD KEY `banners_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookmarks_user_id_bookmarkable_type_bookmarkable_id_unique` (`user_id`,`bookmarkable_type`,`bookmarkable_id`),
  ADD KEY `bookmarks_bookmarkable_type_bookmarkable_id_index` (`bookmarkable_type`,`bookmarkable_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_isactive_index` (`isActive`),
  ADD KEY `categories_created_by_foreign` (`created_by`),
  ADD KEY `categories_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `consultancies`
--
ALTER TABLE `consultancies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultancies_created_by_foreign` (`created_by`),
  ADD KEY `consultancies_updated_by_foreign` (`updated_by`),
  ADD KEY `consultancies_published_by_foreign` (`published_by`),
  ADD KEY `consultancies_language_index` (`language`),
  ADD KEY `consultancies_isactive_index` (`isActive`),
  ADD KEY `consultancies_isfeatured_index` (`isFeatured`),
  ADD KEY `consultancies_status_index` (`status`),
  ADD KEY `consultancies_published_at_index` (`published_at`),
  ADD KEY `consultancies_owner_admin_id_index` (`owner_admin_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_created_by_foreign` (`created_by`),
  ADD KEY `courses_updated_by_foreign` (`updated_by`),
  ADD KEY `courses_published_by_foreign` (`published_by`),
  ADD KEY `courses_language_index` (`language`),
  ADD KEY `courses_isactive_index` (`isActive`),
  ADD KEY `courses_isfeatured_index` (`isFeatured`),
  ADD KEY `courses_status_index` (`status`),
  ADD KEY `courses_published_at_index` (`published_at`),
  ADD KEY `courses_owner_admin_id_index` (`owner_admin_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currencies_code_unique` (`code`),
  ADD KEY `currencies_isactive_index` (`isActive`),
  ADD KEY `currencies_created_by_foreign` (`created_by`),
  ADD KEY `currencies_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faqs_language_index` (`language`),
  ADD KEY `faqs_isactive_index` (`isActive`),
  ADD KEY `faqs_created_by_foreign` (`created_by`),
  ADD KEY `faqs_updated_by_foreign` (`updated_by`),
  ADD KEY `faqs_faq_category_id_foreign` (`faq_category_id`);

--
-- Indexes for table `faq_categories`
--
ALTER TABLE `faq_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faq_categories_language_name_unique` (`language`,`name`),
  ADD KEY `faq_categories_created_by_foreign` (`created_by`),
  ADD KEY `faq_categories_updated_by_foreign` (`updated_by`),
  ADD KEY `faq_categories_isactive_index` (`isActive`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `general_settings_created_by_foreign` (`created_by`),
  ADD KEY `general_settings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `home_cards`
--
ALTER TABLE `home_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `home_cards_created_by_foreign` (`created_by`),
  ADD KEY `home_cards_updated_by_foreign` (`updated_by`),
  ADD KEY `home_cards_language_index` (`language`),
  ADD KEY `home_cards_position_index` (`position`),
  ADD KEY `home_cards_is_active_index` (`is_active`);

--
-- Indexes for table `home_card_title_positions`
--
ALTER TABLE `home_card_title_positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `home_card_title_positions_language_card_position_unique` (`language`,`card_position`);

--
-- Indexes for table `home_page_section_headings`
--
ALTER TABLE `home_page_section_headings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `home_page_section_headings_language_section_name_unique` (`language`,`section_name`);

--
-- Indexes for table `home_sections`
--
ALTER TABLE `home_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `home_sections_created_by_foreign` (`created_by`),
  ADD KEY `home_sections_updated_by_foreign` (`updated_by`),
  ADD KEY `home_sections_language_index` (`language`),
  ADD KEY `home_sections_position_index` (`position`),
  ADD KEY `home_sections_section_condition_index` (`section_condition`),
  ADD KEY `home_sections_is_active_index` (`is_active`);

--
-- Indexes for table `info_pages`
--
ALTER TABLE `info_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `info_pages_language_page_unique` (`language`,`page`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `languages_is_active_index` (`is_active`);

--
-- Indexes for table `magazines`
--
ALTER TABLE `magazines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `magazines_language_index` (`language`),
  ADD KEY `magazines_publish_date_index` (`publish_date`),
  ADD KEY `magazines_scheduled_at_index` (`scheduled_at`),
  ADD KEY `magazines_published_at_index` (`published_at`),
  ADD KEY `magazines_isfree_index` (`isFree`),
  ADD KEY `magazines_isfeatured_index` (`isFeatured`),
  ADD KEY `magazines_isactive_index` (`isActive`),
  ADD KEY `magazines_status_index` (`status`),
  ADD KEY `magazines_created_by_foreign` (`created_by`),
  ADD KEY `magazines_updated_by_foreign` (`updated_by`),
  ADD KEY `magazines_published_by_foreign` (`published_by`),
  ADD KEY `magazines_owner_admin_id_index` (`owner_admin_id`);

--
-- Indexes for table `magazine_author_maps`
--
ALTER TABLE `magazine_author_maps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `magazine_author_maps_magazine_id_author_id_unique` (`magazine_id`,`author_id`),
  ADD KEY `magazine_author_maps_author_id_foreign` (`author_id`);

--
-- Indexes for table `magazine_category_maps`
--
ALTER TABLE `magazine_category_maps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `magazine_category_maps_magazine_id_category_id_unique` (`magazine_id`,`category_id`),
  ADD KEY `magazine_category_maps_category_id_foreign` (`category_id`);

--
-- Indexes for table `magazine_tag_maps`
--
ALTER TABLE `magazine_tag_maps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `magazine_tag_maps_magazine_id_tag_id_unique` (`magazine_id`,`tag_id`),
  ADD KEY `magazine_tag_maps_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `media_storage_locations`
--
ALTER TABLE `media_storage_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_storage_locations_storage_provider_id_foreign` (`storage_provider_id`),
  ADD KEY `media_storage_locations_media_type_media_id_index` (`media_type`,`media_id`),
  ADD KEY `media_location_resolution_index` (`media_type`,`media_id`,`status`,`is_primary`),
  ADD KEY `media_storage_locations_is_primary_index` (`is_primary`),
  ADD KEY `media_storage_locations_status_index` (`status`),
  ADD KEY `media_storage_locations_verification_status_index` (`verification_status`);

--
-- Indexes for table `meta_tags`
--
ALTER TABLE `meta_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meta_tags_table_name_table_id_language_unique` (`table_name`,`table_id`,`language`),
  ADD KEY `meta_tags_language_title_index` (`language`,`title`),
  ADD KEY `meta_tags_created_by_foreign` (`created_by`),
  ADD KEY `meta_tags_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `newsletter_campaigns`
--
ALTER TABLE `newsletter_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `newsletter_campaigns_created_by_foreign` (`created_by`),
  ADD KEY `newsletter_campaigns_updated_by_foreign` (`updated_by`),
  ADD KEY `newsletter_campaigns_status_index` (`status`);

--
-- Indexes for table `newsletter_campaign_contents`
--
ALTER TABLE `newsletter_campaign_contents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nl_campaign_content_uq` (`newsletter_campaign_id`,`content_type`,`content_id`),
  ADD KEY `nl_campaign_sort_idx` (`newsletter_campaign_id`,`sort_order`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `newsletter_subscribers_email_unique` (`email`),
  ADD UNIQUE KEY `nl_sub_unsub_token_uq` (`unsubscribe_token`),
  ADD KEY `newsletter_subscribers_status_index` (`status`),
  ADD KEY `newsletter_subscribers_brevo_sync_status_index` (`brevo_sync_status`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_accounts`
--
ALTER TABLE `payment_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_accounts_created_by_foreign` (`created_by`),
  ADD KEY `payment_accounts_updated_by_foreign` (`updated_by`),
  ADD KEY `payment_accounts_is_active_index` (`is_active`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `related_articles`
--
ALTER TABLE `related_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `related_articles_article_id_related_article_id_unique` (`article_id`,`related_article_id`),
  ADD KEY `related_articles_related_article_id_foreign` (`related_article_id`);

--
-- Indexes for table `related_consultancies`
--
ALTER TABLE `related_consultancies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rel_consultancy_unique` (`consultancy_id`,`related_consultancy_id`),
  ADD KEY `related_consultancies_related_consultancy_id_foreign` (`related_consultancy_id`);

--
-- Indexes for table `related_courses`
--
ALTER TABLE `related_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rel_course_unique` (`course_id`,`related_course_id`),
  ADD KEY `related_courses_related_course_id_foreign` (`related_course_id`);

--
-- Indexes for table `related_magazines`
--
ALTER TABLE `related_magazines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `related_magazines_magazine_id_related_magazine_id_unique` (`magazine_id`,`related_magazine_id`),
  ADD KEY `related_magazines_related_magazine_id_foreign` (`related_magazine_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `search_contents`
--
ALTER TABLE `search_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `search_contents_content_type_content_id_index` (`content_type`,`content_id`),
  ADD KEY `search_contents_created_at_index` (`created_at`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_created_by_foreign` (`created_by`),
  ADD KEY `services_updated_by_foreign` (`updated_by`),
  ADD KEY `services_isactive_index` (`isactive`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `site_visits`
--
ALTER TABLE `site_visits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `site_visits_public_token_hash_unique` (`public_token_hash`),
  ADD KEY `site_visits_user_id_foreign` (`user_id`),
  ADD KEY `site_visits_visitable_type_visitable_id_index` (`visitable_type`,`visitable_id`),
  ADD KEY `site_visits_visitor_id_index` (`visitor_id`),
  ADD KEY `site_visits_page_key_index` (`page_key`),
  ADD KEY `site_visits_route_name_index` (`route_name`),
  ADD KEY `site_visits_country_index` (`country`),
  ADD KEY `site_visits_city_index` (`city`),
  ADD KEY `site_visits_device_type_index` (`device_type`),
  ADD KEY `site_visits_browser_index` (`browser`),
  ADD KEY `site_visits_started_at_index` (`started_at`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sliders_language_index` (`language`),
  ADD KEY `sliders_isactive_index` (`isActive`),
  ADD KEY `sliders_created_by_foreign` (`created_by`),
  ADD KEY `sliders_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `storage_providers`
--
ALTER TABLE `storage_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `storage_providers_slug_unique` (`slug`),
  ADD UNIQUE KEY `storage_providers_disk_unique` (`disk`),
  ADD KEY `storage_providers_is_active_index` (`is_active`),
  ADD KEY `storage_providers_is_default_index` (`is_default`),
  ADD KEY `storage_providers_priority_index` (`priority`),
  ADD KEY `storage_providers_health_status_index` (`health_status`);

--
-- Indexes for table `subscription_expiry_notifications`
--
ALTER TABLE `subscription_expiry_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_expiry_notifications_user_subscription_id_unique` (`user_subscription_id`);

--
-- Indexes for table `subscription_expiry_reminders`
--
ALTER TABLE `subscription_expiry_reminders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_expiry_reminders_subscription_days_unique` (`user_subscription_id`,`reminder_days`);

--
-- Indexes for table `subscription_notification_settings`
--
ALTER TABLE `subscription_notification_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_products`
--
ALTER TABLE `subscription_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_products_currency_id_foreign` (`currency_id`),
  ADD KEY `subscription_products_product_for_index` (`product_for`),
  ADD KEY `subscription_products_promotion_id_index` (`promotion_id`),
  ADD KEY `subscription_products_created_by_foreign` (`created_by`),
  ADD KEY `subscription_products_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `subscription_product_types`
--
ALTER TABLE `subscription_product_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_product_types_subscription_product_id_foreign` (`subscription_product_id`),
  ADD KEY `subscription_product_types_subscription_type_id_foreign` (`subscription_type_id`);

--
-- Indexes for table `subscription_product_videos`
--
ALTER TABLE `subscription_product_videos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sp_video_unique` (`subscription_product_id`,`video_id`),
  ADD KEY `subscription_product_videos_video_id_foreign` (`video_id`);

--
-- Indexes for table `subscription_types`
--
ALTER TABLE `subscription_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_types_slug_unique` (`slug`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tags_isactive_index` (`isActive`),
  ADD KEY `tags_created_by_foreign` (`created_by`),
  ADD KEY `tags_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `taza_shumara`
--
ALTER TABLE `taza_shumara`
  ADD PRIMARY KEY (`id`),
  ADD KEY `taza_shumara_magazine_id_foreign` (`magazine_id`),
  ADD KEY `taza_shumara_created_by_foreign` (`created_by`),
  ADD KEY `taza_shumara_updated_by_foreign` (`updated_by`),
  ADD KEY `taza_shumara_language_is_active_index` (`language`,`is_active`),
  ADD KEY `taza_shumara_language_index` (`language`),
  ADD KEY `taza_shumara_is_active_index` (`is_active`);

--
-- Indexes for table `taza_shumara_articles`
--
ALTER TABLE `taza_shumara_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `taza_shumara_articles_taza_shumara_id_article_id_unique` (`taza_shumara_id`,`article_id`),
  ADD KEY `taza_shumara_articles_article_id_foreign` (`article_id`),
  ADD KEY `taza_shumara_articles_taza_shumara_id_position_sort_order_index` (`taza_shumara_id`,`position`,`sort_order`),
  ADD KEY `taza_shumara_articles_position_index` (`position`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_activation_token_unique` (`activation_token`),
  ADD UNIQUE KEY `users_consumed_activation_token_hash_unique` (`consumed_activation_token_hash`),
  ADD KEY `users_is_active_index` (`is_active`),
  ADD KEY `users_created_by_foreign` (`created_by`),
  ADD KEY `users_updated_by_foreign` (`updated_by`),
  ADD KEY `users_parent_admin_id_index` (`parent_admin_id`);

--
-- Indexes for table `user_content_visits`
--
ALTER TABLE `user_content_visits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_content_visits_user_id_visitable_type_visitable_id_unique` (`user_id`,`visitable_type`,`visitable_id`),
  ADD KEY `user_content_visits_visitable_type_visitable_id_index` (`visitable_type`,`visitable_id`),
  ADD KEY `user_content_visits_last_visited_at_index` (`last_visited_at`);

--
-- Indexes for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_subscriptions_user_id_foreign` (`user_id`),
  ADD KEY `user_subscriptions_subscription_product_id_foreign` (`subscription_product_id`),
  ADD KEY `user_subscriptions_currency_id_foreign` (`currency_id`),
  ADD KEY `user_subscriptions_start_date_index` (`start_date`),
  ADD KEY `user_subscriptions_end_date_index` (`end_date`),
  ADD KEY `user_subscriptions_is_active_index` (`is_active`),
  ADD KEY `user_subscriptions_payment_account_id_foreign` (`payment_account_id`),
  ADD KEY `user_subscriptions_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `user_subscriptions_payment_status_index` (`payment_status`);

--
-- Indexes for table `user_sub_types`
--
ALTER TABLE `user_sub_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_sub_types_user_subscription_id_subscription_type_id_unique` (`user_subscription_id`,`subscription_type_id`),
  ADD KEY `user_sub_types_subscription_type_id_foreign` (`subscription_type_id`);

--
-- Indexes for table `user_two_factor_challenges`
--
ALTER TABLE `user_two_factor_challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_two_factor_challenges_user_id_purpose_consumed_at_index` (`user_id`,`purpose`,`consumed_at`);

--
-- Indexes for table `user_two_factor_settings`
--
ALTER TABLE `user_two_factor_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_two_factor_settings_user_id_unique` (`user_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `videos_created_by_foreign` (`created_by`),
  ADD KEY `videos_updated_by_foreign` (`updated_by`),
  ADD KEY `videos_is_free_index` (`is_free`),
  ADD KEY `videos_is_active_index` (`is_active`),
  ADD KEY `videos_is_share_index` (`is_share`),
  ADD KEY `videos_published_by_foreign` (`published_by`),
  ADD KEY `videos_status_index` (`status`),
  ADD KEY `videos_published_at_index` (`published_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abouts`
--
ALTER TABLE `abouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ad_clicks`
--
ALTER TABLE `ad_clicks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ad_requests`
--
ALTER TABLE `ad_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ad_request_placements`
--
ALTER TABLE `ad_request_placements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `article_author_maps`
--
ALTER TABLE `article_author_maps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `article_category_maps`
--
ALTER TABLE `article_category_maps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `article_tag_maps`
--
ALTER TABLE `article_tag_maps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ask_questions`
--
ALTER TABLE `ask_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `author_general_settings`
--
ALTER TABLE `author_general_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `author_visibilities`
--
ALTER TABLE `author_visibilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `consultancies`
--
ALTER TABLE `consultancies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faq_categories`
--
ALTER TABLE `faq_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `home_cards`
--
ALTER TABLE `home_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `home_card_title_positions`
--
ALTER TABLE `home_card_title_positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `home_page_section_headings`
--
ALTER TABLE `home_page_section_headings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `home_sections`
--
ALTER TABLE `home_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `info_pages`
--
ALTER TABLE `info_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `magazines`
--
ALTER TABLE `magazines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `magazine_author_maps`
--
ALTER TABLE `magazine_author_maps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `magazine_category_maps`
--
ALTER TABLE `magazine_category_maps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `magazine_tag_maps`
--
ALTER TABLE `magazine_tag_maps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `media_storage_locations`
--
ALTER TABLE `media_storage_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `meta_tags`
--
ALTER TABLE `meta_tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `newsletter_campaigns`
--
ALTER TABLE `newsletter_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `newsletter_campaign_contents`
--
ALTER TABLE `newsletter_campaign_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_accounts`
--
ALTER TABLE `payment_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `related_articles`
--
ALTER TABLE `related_articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `related_consultancies`
--
ALTER TABLE `related_consultancies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `related_courses`
--
ALTER TABLE `related_courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `related_magazines`
--
ALTER TABLE `related_magazines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `search_contents`
--
ALTER TABLE `search_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_visits`
--
ALTER TABLE `site_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=342;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `storage_providers`
--
ALTER TABLE `storage_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscription_expiry_notifications`
--
ALTER TABLE `subscription_expiry_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subscription_expiry_reminders`
--
ALTER TABLE `subscription_expiry_reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscription_notification_settings`
--
ALTER TABLE `subscription_notification_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscription_products`
--
ALTER TABLE `subscription_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subscription_product_types`
--
ALTER TABLE `subscription_product_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `subscription_product_videos`
--
ALTER TABLE `subscription_product_videos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subscription_types`
--
ALTER TABLE `subscription_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `taza_shumara`
--
ALTER TABLE `taza_shumara`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taza_shumara_articles`
--
ALTER TABLE `taza_shumara_articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_content_visits`
--
ALTER TABLE `user_content_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_sub_types`
--
ALTER TABLE `user_sub_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_two_factor_challenges`
--
ALTER TABLE `user_two_factor_challenges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user_two_factor_settings`
--
ALTER TABLE `user_two_factor_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abouts`
--
ALTER TABLE `abouts`
  ADD CONSTRAINT `abouts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `abouts_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `ads_ad_request_id_foreign` FOREIGN KEY (`ad_request_id`) REFERENCES `ad_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ads_ad_request_placement_id_foreign` FOREIGN KEY (`ad_request_placement_id`) REFERENCES `ad_request_placements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ads_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ads_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ad_clicks`
--
ALTER TABLE `ad_clicks`
  ADD CONSTRAINT `ad_clicks_ad_id_foreign` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ad_requests`
--
ALTER TABLE `ad_requests`
  ADD CONSTRAINT `ad_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ad_request_placements`
--
ALTER TABLE `ad_request_placements`
  ADD CONSTRAINT `ad_request_placements_ad_request_id_foreign` FOREIGN KEY (`ad_request_id`) REFERENCES `ad_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `articles_magazine_id_foreign` FOREIGN KEY (`magazine_id`) REFERENCES `magazines` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `articles_owner_admin_id_foreign` FOREIGN KEY (`owner_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `articles_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `articles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `article_author_maps`
--
ALTER TABLE `article_author_maps`
  ADD CONSTRAINT `article_author_maps_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_author_maps_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `article_category_maps`
--
ALTER TABLE `article_category_maps`
  ADD CONSTRAINT `article_category_maps_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_category_maps_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `article_tag_maps`
--
ALTER TABLE `article_tag_maps`
  ADD CONSTRAINT `article_tag_maps_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tag_maps_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ask_questions`
--
ALTER TABLE `ask_questions`
  ADD CONSTRAINT `ask_questions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `authors`
--
ALTER TABLE `authors`
  ADD CONSTRAINT `authors_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `authors_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `author_general_settings`
--
ALTER TABLE `author_general_settings`
  ADD CONSTRAINT `author_general_settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `author_general_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `author_visibilities`
--
ALTER TABLE `author_visibilities`
  ADD CONSTRAINT `author_visibilities_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `banners`
--
ALTER TABLE `banners`
  ADD CONSTRAINT `banners_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `banners_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `consultancies`
--
ALTER TABLE `consultancies`
  ADD CONSTRAINT `consultancies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultancies_owner_admin_id_foreign` FOREIGN KEY (`owner_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultancies_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultancies_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courses_owner_admin_id_foreign` FOREIGN KEY (`owner_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courses_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `currencies`
--
ALTER TABLE `currencies`
  ADD CONSTRAINT `currencies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `currencies_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `faqs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `faqs_faq_category_id_foreign` FOREIGN KEY (`faq_category_id`) REFERENCES `faq_categories` (`id`),
  ADD CONSTRAINT `faqs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faq_categories`
--
ALTER TABLE `faq_categories`
  ADD CONSTRAINT `faq_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `faq_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD CONSTRAINT `general_settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `general_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `home_cards`
--
ALTER TABLE `home_cards`
  ADD CONSTRAINT `home_cards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `home_cards_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `home_sections`
--
ALTER TABLE `home_sections`
  ADD CONSTRAINT `home_sections_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `home_sections_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `magazines`
--
ALTER TABLE `magazines`
  ADD CONSTRAINT `magazines_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `magazines_owner_admin_id_foreign` FOREIGN KEY (`owner_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `magazines_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `magazines_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `magazine_author_maps`
--
ALTER TABLE `magazine_author_maps`
  ADD CONSTRAINT `magazine_author_maps_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `magazine_author_maps_magazine_id_foreign` FOREIGN KEY (`magazine_id`) REFERENCES `magazines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `magazine_category_maps`
--
ALTER TABLE `magazine_category_maps`
  ADD CONSTRAINT `magazine_category_maps_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `magazine_category_maps_magazine_id_foreign` FOREIGN KEY (`magazine_id`) REFERENCES `magazines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `magazine_tag_maps`
--
ALTER TABLE `magazine_tag_maps`
  ADD CONSTRAINT `magazine_tag_maps_magazine_id_foreign` FOREIGN KEY (`magazine_id`) REFERENCES `magazines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `magazine_tag_maps_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `media_storage_locations`
--
ALTER TABLE `media_storage_locations`
  ADD CONSTRAINT `media_storage_locations_storage_provider_id_foreign` FOREIGN KEY (`storage_provider_id`) REFERENCES `storage_providers` (`id`);

--
-- Constraints for table `meta_tags`
--
ALTER TABLE `meta_tags`
  ADD CONSTRAINT `meta_tags_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `meta_tags_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `newsletter_campaigns`
--
ALTER TABLE `newsletter_campaigns`
  ADD CONSTRAINT `newsletter_campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `newsletter_campaigns_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `newsletter_campaign_contents`
--
ALTER TABLE `newsletter_campaign_contents`
  ADD CONSTRAINT `newsletter_campaign_contents_newsletter_campaign_id_foreign` FOREIGN KEY (`newsletter_campaign_id`) REFERENCES `newsletter_campaigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_accounts`
--
ALTER TABLE `payment_accounts`
  ADD CONSTRAINT `payment_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_accounts_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `related_articles`
--
ALTER TABLE `related_articles`
  ADD CONSTRAINT `related_articles_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `related_articles_related_article_id_foreign` FOREIGN KEY (`related_article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `related_consultancies`
--
ALTER TABLE `related_consultancies`
  ADD CONSTRAINT `related_consultancies_consultancy_id_foreign` FOREIGN KEY (`consultancy_id`) REFERENCES `consultancies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `related_consultancies_related_consultancy_id_foreign` FOREIGN KEY (`related_consultancy_id`) REFERENCES `consultancies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `related_courses`
--
ALTER TABLE `related_courses`
  ADD CONSTRAINT `related_courses_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `related_courses_related_course_id_foreign` FOREIGN KEY (`related_course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `related_magazines`
--
ALTER TABLE `related_magazines`
  ADD CONSTRAINT `related_magazines_magazine_id_foreign` FOREIGN KEY (`magazine_id`) REFERENCES `magazines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `related_magazines_related_magazine_id_foreign` FOREIGN KEY (`related_magazine_id`) REFERENCES `magazines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `site_visits`
--
ALTER TABLE `site_visits`
  ADD CONSTRAINT `site_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sliders`
--
ALTER TABLE `sliders`
  ADD CONSTRAINT `sliders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sliders_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subscription_expiry_notifications`
--
ALTER TABLE `subscription_expiry_notifications`
  ADD CONSTRAINT `subscription_expiry_notifications_user_subscription_id_foreign` FOREIGN KEY (`user_subscription_id`) REFERENCES `user_subscriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_expiry_reminders`
--
ALTER TABLE `subscription_expiry_reminders`
  ADD CONSTRAINT `subscription_expiry_reminders_user_subscription_id_foreign` FOREIGN KEY (`user_subscription_id`) REFERENCES `user_subscriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_products`
--
ALTER TABLE `subscription_products`
  ADD CONSTRAINT `subscription_products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `subscription_products_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `subscription_products_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subscription_product_types`
--
ALTER TABLE `subscription_product_types`
  ADD CONSTRAINT `subscription_product_types_subscription_product_id_foreign` FOREIGN KEY (`subscription_product_id`) REFERENCES `subscription_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_product_types_subscription_type_id_foreign` FOREIGN KEY (`subscription_type_id`) REFERENCES `subscription_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_product_videos`
--
ALTER TABLE `subscription_product_videos`
  ADD CONSTRAINT `subscription_product_videos_subscription_product_id_foreign` FOREIGN KEY (`subscription_product_id`) REFERENCES `subscription_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_product_videos_video_id_foreign` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tags_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `taza_shumara`
--
ALTER TABLE `taza_shumara`
  ADD CONSTRAINT `taza_shumara_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taza_shumara_magazine_id_foreign` FOREIGN KEY (`magazine_id`) REFERENCES `magazines` (`id`),
  ADD CONSTRAINT `taza_shumara_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `taza_shumara_articles`
--
ALTER TABLE `taza_shumara_articles`
  ADD CONSTRAINT `taza_shumara_articles_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  ADD CONSTRAINT `taza_shumara_articles_taza_shumara_id_foreign` FOREIGN KEY (`taza_shumara_id`) REFERENCES `taza_shumara` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_parent_admin_id_foreign` FOREIGN KEY (`parent_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_content_visits`
--
ALTER TABLE `user_content_visits`
  ADD CONSTRAINT `user_content_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD CONSTRAINT `user_subscriptions_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  ADD CONSTRAINT `user_subscriptions_payment_account_id_foreign` FOREIGN KEY (`payment_account_id`) REFERENCES `payment_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_subscriptions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_subscriptions_subscription_product_id_foreign` FOREIGN KEY (`subscription_product_id`) REFERENCES `subscription_products` (`id`),
  ADD CONSTRAINT `user_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sub_types`
--
ALTER TABLE `user_sub_types`
  ADD CONSTRAINT `user_sub_types_subscription_type_id_foreign` FOREIGN KEY (`subscription_type_id`) REFERENCES `subscription_types` (`id`),
  ADD CONSTRAINT `user_sub_types_user_subscription_id_foreign` FOREIGN KEY (`user_subscription_id`) REFERENCES `user_subscriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_two_factor_challenges`
--
ALTER TABLE `user_two_factor_challenges`
  ADD CONSTRAINT `user_two_factor_challenges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_two_factor_settings`
--
ALTER TABLE `user_two_factor_settings`
  ADD CONSTRAINT `user_two_factor_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `videos_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `videos_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

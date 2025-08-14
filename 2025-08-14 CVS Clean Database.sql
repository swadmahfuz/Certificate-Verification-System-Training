-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 07:34 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `verifycert`
--

-- --------------------------------------------------------

--
-- Table structure for table `calibration_certificates`
--

CREATE TABLE `calibration_certificates` (
  `id` int(10) UNSIGNED NOT NULL,
  `certificate_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `calibrator` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `equipment_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `equipment_brand` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `equipment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `calibration_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_issue_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `calibration_due_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validity_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calibration_remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_internal_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Approved',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bulk uploaded',
  `created_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `review_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `approval_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `certificate_pdf` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_at` timestamp NULL DEFAULT NULL,
  `deleted_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by_id` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(10) UNSIGNED NOT NULL,
  `certificate_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `participant_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passport_nid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driving_license` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `training_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trainer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `training_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `training_end` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiry_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''Approved''',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bulk uploaded',
  `created_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `review_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `approval_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `certificate_pdf` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_certificates`
--

CREATE TABLE `inspection_certificates` (
  `id` int(10) UNSIGNED NOT NULL,
  `certificate_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inspector` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inspection_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inspection_location` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `equipment_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `equipment_brand` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipment_serial_chassis` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipment_rated_capacity` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipment_swl` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validity_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_internal_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Approved',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bulk uploaded',
  `created_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `review_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `approval_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `certificate_pdf` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_by_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_uploaded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(140, '2014_10_12_000000_create_users_table', 1),
(141, '2014_10_12_100000_create_password_resets_table', 1),
(142, '2019_08_19_000000_create_failed_jobs_table', 1),
(143, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(144, '2022_01_21_050800_create_certificates_table', 1),
(145, '2024_05_03_124430_create_inspection_certificates_table', 2),
(146, '2024_05_10_011117_add_training_end_to_certificates_table', 3),
(147, '2024_05_11_170126_create_inspection_certificates_table', 4),
(148, '2025_02_25_012247_add_reviewed_and_approved_to_certificates_table', 4),
(149, '2025_06_23_135527_add_department_to_users_table', 4),
(150, '2025_07_05_091054_add_pdf_fields_to_certificates_table', 5),
(151, '2025_07_26_000001_add_review_approval_pdf_to_inspection_certificates_table', 6),
(153, '2025_08_14_093412_add_deleted_by_to_certificates_table', 6),
(154, '2025_08_14_083528_create_calibration_certificates_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `department`, `designation`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Muhammad Masud Rana', 'am.sales@tuvat.com.bd', 'Energy & Production', 'Assistant Sales Manager', NULL, '$2y$10$wqbgOiQQvDhwheew/jzImOe3FvX295iSf8lQZLMMbiusWg5o8IMei', 'wAvuGefskEA8gOLxas6KKkELIzS9Fcsgh9jJ6MlgsqWuNhcIMcwDrgiv7O5W', '2023-10-31 18:19:06', '2023-11-01 08:15:47'),
(3, 'Md. Borhanuddin', 'hse.ine01@tuvat.com.bd', 'Energy & Production', 'HSE Engineer', NULL, '$2y$10$f1ZLMBXnS/26ktllK8q3NOhG.wVuL3FPu4l5C7nZjXQCWjzwSZ2da', 'v4NlGC5G5oOgKNzLCcRRsSVGAubU87fzJS3nTkC8NkZSlLiiyl2m4AMRiOCJ', '2023-10-31 18:20:00', '2023-11-10 16:40:04'),
(4, 'Afsana Akter', 'admin.executive@tuvat.com.bd', 'Business Assurance & Training', 'Admin Executive', NULL, '$2y$10$xO4owTFWYXbOHW92o/JoTuCiYDISKJvhdwcOJhyFc9xEog/ZcFiTq', '9Dhll138kXFTH4UNU2SrimHqoapul3doY92dINpztVba5jGnp0PlAQU2CetT', '2023-10-31 19:07:25', '2023-11-12 05:06:49'),
(5, 'Shohidul Islam', 'manager.ine@tuvat.com.bd', 'Energy & Production', 'Country Manager', NULL, '$2y$10$Kdlug3imnNtAwItATovifOD.CWCpgYRGF3nylS5N/K4It/P7EnR/y', NULL, '2023-10-31 19:09:41', '2023-10-31 19:09:41'),
(6, 'Md. Shimul Hossain Shaon', 'am.ine01@tuvat.com.bd', 'Energy & Production', 'Technical Manager', NULL, '$2y$10$gnOlKWWW0vrOTTRfnnrq9ujMcrhrRqV8LFfvVDAWcsuVx7rUWPyiy', NULL, '2023-10-31 19:10:27', '2023-10-31 19:10:27'),
(7, 'Ahmad Rafiq', 'buh.ine@tuvat.com.pk', 'Energy & Production', 'Business Unit Head', NULL, '$2y$10$0Mbz0VyruSnVTD56McyFteZPejg.uKyihzB53X43eP.7FV9u6Nvg.', NULL, '2023-10-31 19:14:18', '2023-10-31 19:14:18'),
(8, 'Md Ridoy Hossain', 'am.operations.ep@tuvat.com.bd', 'Energy & Production', 'Assistant Manager - Operations', NULL, '$2y$10$s.Be3Af5A0F9ZO42EeD1W.xvcKSQeBOQ97LTWRIXLlQExaNJ5l3NC', NULL, '2024-01-24 11:10:06', '2024-01-24 11:10:06'),
(9, 'Md. Din Amin', 'tuv.tis.ine6@gmail.com', 'Energy & Production', 'Inspector', NULL, '$2y$10$yA4A9RZs3/nAGpITBQSpYOY/eCCUWzgTJcXDsEXf5j3q4j3sGhrrK', NULL, '2024-04-02 06:05:38', '2024-04-02 06:05:38'),
(10, 'Md. Shahariar Ahmed', 'am.sales.ba@tuvat.com.bd', 'Business Assurance & Training', 'Assistant Sales Manager - Training', NULL, '$2y$10$825vB53T2SJAUIE.XI09JuAeBr.SAI.Bir4OGKuaEb2PZCr1jRsVK', NULL, '2025-07-01 14:50:23', '2025-07-01 14:50:23'),
(11, 'Swad Ahmed Mahfuz', 'certification.manager@tuvat.com.bd', 'Business Assurance & Training', 'Head of BA & Trg Division - Bangladesh', NULL, '$2y$10$B6xWpnUzBwHled9KWf6rYu.fxKxukXsw54pTcsOMfRne6vPA1UaeC', NULL, '2025-07-01 14:51:38', '2025-07-01 14:51:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calibration_certificates`
--
ALTER TABLE `calibration_certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `calibration_certificates_certificate_number_unique` (`certificate_number`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificates_certificate_number_unique` (`certificate_number`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inspection_certificates`
--
ALTER TABLE `inspection_certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inspection_certificates_certificate_number_unique` (`certificate_number`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calibration_certificates`
--
ALTER TABLE `calibration_certificates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_certificates`
--
ALTER TABLE `inspection_certificates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 10, 2025 at 01:51 PM
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
-- Database: `hospital_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `staff_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `profile_pic`, `staff_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `address`, `email`, `contact_number`, `created_at`) VALUES
(1, '67e554671c41d.png', 'AD-01-001-2025', 'Ian', 'Mboya', '2003-05-21', 'Male', '0100, Nairobi', 'ianmboya@gmail.com', '0712346540', '2025-03-22 19:58:06'),
(2, 'Admin_2_1743347755.jpg', 'AD-01-002-2025', 'Alice', 'Muthoni', '1980-03-10', 'Female', 'Mombasa', 'alice.muthoni@hospital.com', '254734567890', '2025-03-22 20:01:35'),
(3, 'C:\\xampp\\htdocs\\FINALPROJECT2025\\img\\admin2.jpg', 'AD-01-003-2025', 'Peter', 'Ochieng', '1975-11-25', 'Male', 'Eldoret', 'peter.ochieng@hospital.com', '254745678901', '2025-03-22 20:01:35'),
(4, 'admineric.jpg', 'AD-01-004-2025', 'eric', 'maina', '2003-03-18', 'Male', 'Roysambu, Nairobi', 'ericmaina10@gmail.com', '0700135589', '2025-03-30 21:55:35'),
(6, 'Admin_AD_01_005_2025_1743381192.jpg', 'AD-01-005-2025', 'jameson', 'muirigi', '1990-02-13', 'Male', 'Nairobi', 'jamesonmuirigi@gmail.com', '0715691033', '2025-03-31 00:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `notification_type` enum('CRUD','signup','appointment','medical_record') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `user_id`, `message`, `notification_type`, `is_read`, `created_at`) VALUES
(1, 11, 'New user signed up: eric wainana', 'signup', 1, '2025-02-12 18:28:38'),
(2, 12, 'New user signed up: jackline matindi', 'signup', 1, '2025-02-13 12:44:32'),
(3, 18, 'New user signed up: dennis gachuiri', 'signup', 1, '2025-03-16 13:27:52'),
(4, 19, 'New user signed up: john kibera', 'signup', 1, '2025-03-17 21:54:36'),
(5, 21, 'New user signed up: fatuma onyango', 'signup', 1, '2025-03-17 22:41:02'),
(6, 22, 'New user signed up: mark nguyo', 'signup', 1, '2025-03-17 23:02:38'),
(7, 28, 'New user signed up: peter maina', 'signup', 1, '2025-03-18 13:52:50'),
(8, 29, 'New user signed up: grace wanjiku', 'signup', 1, '2025-03-18 14:00:32'),
(15, 37, 'New user signed up: patrick wekesa', 'signup', 1, '2025-03-18 19:44:54'),
(16, 38, 'New user signed up: konda mnono', 'signup', 1, '2025-03-18 21:17:07'),
(17, 39, 'New user signed up: james kamau', 'signup', 1, '2025-03-18 21:30:08'),
(18, 40, 'New user signed up: lilian koech', 'signup', 1, '2025-03-18 21:38:52'),
(19, 1, 'Derrick Gatu booked an appointment with Dr. Allan', 'appointment', 1, '2025-03-26 17:59:53'),
(20, 41, 'New user signed up: beatrice cherono', 'signup', 0, '2025-03-31 07:29:38'),
(21, 42, 'New user signed up: mary njeri', 'signup', 0, '2025-04-03 14:42:21'),
(22, 2, 'Doctor (ID: 1) added a new schedule (ID: 40) on 2025-05-01 from 06:00 AM to 11:55 PM.', 'CRUD', 0, '2025-04-07 12:15:16'),
(23, 2, 'Doctor (ID: 1) viewed medical record (ID: 6) for patient ID 3.', 'medical_record', 0, '2025-04-07 13:40:43'),
(24, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 13:43:45'),
(25, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 14:09:53'),
(26, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 14:11:47'),
(27, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 14:26:02'),
(28, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 14:26:04'),
(29, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 14:26:37'),
(30, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 14:49:08'),
(31, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:29:47'),
(32, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:33:16'),
(33, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:43:49'),
(34, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:43:52'),
(35, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:43:53'),
(36, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:43:54'),
(37, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:43:54'),
(38, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:43:54'),
(39, 2, 'Doctor (ID: 1) viewed medical record (ID: 5) for patient ID 3.', 'medical_record', 0, '2025-04-07 20:48:15'),
(40, 2, 'Doctor (ID: 1) viewed medical record (ID: 8) for patient ID 5.', 'medical_record', 0, '2025-04-07 21:14:03'),
(41, 2, 'Doctor (ID: 1) viewed medical record (ID: 8) for patient ID 5.', 'medical_record', 0, '2025-04-07 21:14:09'),
(42, 43, 'New user signed up: nicolas muriuki', 'signup', 0, '2025-04-09 08:00:45'),
(43, 2, 'Doctor (ID: 1) viewed medical record (ID: 10) for patient ID 5.', 'medical_record', 0, '2025-04-09 09:42:20');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` enum('Patient','Doctor','Hospital Staff','Admin','AI_assistant') DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `created_at`, `updated_at`, `modified_by`) VALUES
(1, 3, 1, '2025-02-24', '15:00:00', 'Completed', '2025-02-23 07:00:00', '2025-03-26 19:07:55', 'Admin'),
(3, 9, 3, '2025-02-22', '09:15:00', 'Completed', '2025-02-21 05:30:00', '2025-03-26 21:49:53', 'Admin'),
(5, 15, 1, '2025-02-18', '08:00:00', 'Completed', '2025-02-17 09:00:00', '2025-02-18 05:45:00', 'Admin'),
(7, 5, 1, '2025-04-01', '14:30:00', 'Cancelled', '2025-02-26 11:00:00', '2025-04-01 12:30:20', 'Admin'),
(8, 22, 2, '2025-02-28', '15:00:00', 'Completed', '2025-02-27 06:30:00', '2025-03-26 22:35:20', 'Admin'),
(10, 14, 2, '2025-04-03', '12:00:00', 'Cancelled', '2025-03-01 10:30:00', '2025-04-03 12:10:26', 'Admin'),
(11, 16, 1, '2025-04-24', '11:45:00', 'Scheduled', '2025-03-02 07:00:00', '2025-04-02 11:34:02', 'Admin'),
(12, 21, 1, '2025-03-05', '09:00:00', 'Cancelled', '2025-03-04 07:00:00', '2025-03-05 22:59:11', 'Admin'),
(13, 22, 2, '2025-03-06', '14:30:00', 'Cancelled', '2025-03-05 06:15:00', '2025-03-06 14:22:26', 'Admin'),
(14, 23, 3, '2025-03-07', '12:15:00', 'Cancelled', '2025-03-06 10:30:00', '2025-04-02 13:45:01', 'Admin'),
(15, 24, 1, '2025-03-08', '09:45:00', 'Completed', '2025-03-07 11:00:00', '2025-03-26 19:28:35', 'Admin'),
(17, 11, 9, '2025-03-07', '08:29:00', 'Cancelled', '2025-03-05 22:29:53', '2025-03-10 17:25:11', 'Admin'),
(18, 5, 1, '2025-03-07', '12:48:00', 'Completed', '2025-03-05 22:49:21', '2025-03-26 19:29:29', 'Admin'),
(21, 21, 9, '2025-03-11', '09:29:00', 'Completed', '2025-03-10 17:29:56', '2025-03-26 19:09:39', 'Admin'),
(22, 22, 6, '2025-03-30', '14:00:00', 'Cancelled', '2025-03-26 18:05:35', '2025-03-31 07:44:05', 'Admin'),
(23, 3, 1, '2025-03-31', '10:00:00', 'Cancelled', '2025-03-26 18:07:03', '2025-03-31 19:00:31', 'Admin'),
(24, 3, 20, '2025-04-02', '16:00:00', 'Cancelled', '2025-03-26 18:17:28', '2025-04-02 14:00:15', 'Admin'),
(25, 7, 6, '2025-04-03', '10:30:00', 'Cancelled', '2025-03-26 18:24:48', '2025-03-26 19:30:41', 'Admin'),
(26, 22, 2, '2025-04-01', '08:36:00', 'Cancelled', '2025-03-26 22:36:10', '2025-04-01 06:45:56', 'Admin'),
(28, 3, 1, '2025-04-09', '09:35:00', 'Cancelled', '2025-04-01 20:37:07', '2025-04-09 07:45:24', 'Doctor'),
(30, 56, 1, '2025-04-07', '13:40:29', 'Cancelled', '2025-04-01 20:41:24', '2025-04-09 07:45:24', 'Patient'),
(31, 383, 1, '2025-04-30', '14:40:00', 'Scheduled', '2025-04-02 11:40:37', '2025-04-03 10:53:18', 'Doctor'),
(32, 383, 3, '2025-04-24', '14:45:00', 'Scheduled', '2025-04-02 11:45:55', '2025-04-02 11:45:55', 'Admin'),
(33, 8, 1, '2025-04-16', '15:45:00', 'Scheduled', '2025-04-02 13:46:19', '2025-04-02 13:46:19', 'Admin'),
(34, 5, 1, '2025-04-25', '17:00:00', 'Scheduled', '2025-04-02 21:56:20', '2025-04-02 21:56:20', 'Admin'),
(35, 22, 1, '2025-04-07', '14:00:00', 'Completed', '2025-04-02 21:57:50', '2025-04-03 11:13:03', 'Doctor'),
(36, 9, 1, '2025-04-15', '09:14:00', 'Scheduled', '2025-04-03 18:15:07', '2025-04-03 18:15:07', 'Doctor'),
(37, 9, 1, '2025-04-06', '22:20:00', 'Cancelled', '2025-04-06 19:21:22', '2025-04-09 07:45:24', 'Doctor'),
(40, 3, 22, '2025-04-16', '08:01:00', '', '2025-04-08 13:07:29', '2025-04-08 13:07:29', 'Admin'),
(41, 3, 1, '2025-04-17', '20:36:00', 'Scheduled', '2025-04-08 13:32:28', '2025-04-08 13:32:28', 'Patient'),
(42, 5, 3, '2025-04-17', '02:11:00', 'Scheduled', '2025-04-08 18:11:44', '2025-04-08 18:11:44', 'Patient'),
(43, 26, 1, '2025-04-08', '11:15:00', 'Cancelled', '2025-04-08 18:15:09', '2025-04-09 07:45:24', 'Doctor'),
(44, 5, 1, '2025-04-08', '13:17:00', 'Cancelled', '2025-04-08 18:18:05', '2025-04-09 07:45:24', 'Doctor'),
(45, 5, 1, '2025-04-17', '11:59:00', 'Scheduled', '2025-04-08 18:59:13', '2025-04-08 18:59:13', 'Patient');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `timestamp`, `details`) VALUES
(1, 1, 'INSERT', 'patient_records', 1, '2025-03-16 12:14:58', NULL),
(2, 1, 'INSERT', 'patient_records', 2, '2025-03-16 12:17:36', NULL),
(3, 1, 'INSERT', 'patient_records', 3, '2025-03-16 12:31:16', NULL),
(4, 11, 'INSERT', 'patient_records', 4, '2025-03-17 20:41:42', NULL),
(5, 2, 'INSERT', 'visit_records', 2, '2025-03-18 23:30:15', NULL),
(6, 2, 'INSERT', 'visit_records', 7, '2025-03-19 00:10:14', NULL),
(7, 2, 'INSERT', 'visit_records', 8, '2025-03-19 00:13:22', NULL),
(8, 2, 'GENERATE_PDF', 'visit_records', 8, '2025-03-19 12:50:25', NULL),
(9, 2, 'GENERATE_PDF', 'visit_records', 7, '2025-03-19 12:52:17', NULL),
(10, 2, 'INSERT', 'visit_records', 9, '2025-03-19 13:06:33', NULL),
(11, 2, 'GENERATE_PDF', 'visit_records', 9, '2025-03-19 13:06:37', NULL),
(12, 2, 'INSERT', 'visit_records', 10, '2025-03-19 13:10:14', NULL),
(13, 2, 'GENERATE_PDF', 'visit_records', 10, '2025-03-19 13:10:16', NULL),
(14, 2, 'GENERATE_PDF', 'visit_records', 10, '2025-03-19 13:46:25', NULL),
(15, 2, 'GENERATE_PDF', 'visit_records', 9, '2025-03-19 13:49:54', NULL),
(16, 2, 'GENERATE_PDF', 'visit_records', 9, '2025-03-19 13:51:15', NULL),
(17, 2, 'GENERATE_PDF', 'visit_records', 10, '2025-03-19 14:05:51', NULL),
(18, 2, 'GENERATE_PDF', 'visit_records', 10, '2025-03-19 15:29:25', NULL),
(19, 1, 'INSERT', 'patient_records', 5, '2025-03-19 15:32:38', NULL),
(20, 2, 'GENERATE_PDF', 'visit_records', 10, '2025-03-19 15:33:37', NULL),
(21, 1, 'INSERT', 'patient_records', 6, '2025-03-24 18:56:16', NULL),
(22, 2, 'GENERATE_PDF', 'visit_records', 10, '2025-03-24 18:57:16', NULL),
(23, 1, 'Update Schedule', 'doctor_schedule', 22, '2025-03-29 13:01:36', 'Updated schedule for doctor ID 3 on 2025-04-03 at 12:00:00 with status Busy.'),
(24, 1, 'Update Schedule', 'doctor_schedule', 22, '2025-03-29 13:08:54', 'Updated schedule for doctor ID 3 on 2025-04-03 at 10:00:00 with status Busy.'),
(25, 7, 'Update Schedule', 'doctor_schedule', 22, '2025-03-30 11:22:03', NULL),
(26, 7, 'Update Schedule', 'doctor_schedule', 2, '2025-03-30 11:27:59', NULL),
(27, 7, 'Update Schedule', 'doctor_schedule', 22, '2025-03-30 11:28:31', NULL),
(28, 7, 'Update Schedule', 'doctor_schedule', 5, '2025-03-30 11:28:45', NULL),
(29, 7, 'Update Schedule', 'doctor_schedule', 10, '2025-03-30 11:29:02', NULL),
(30, 7, 'Update Schedule', 'doctor_schedule', 5, '2025-03-30 11:35:30', 'Updated schedule ID 5: Date changed from 2025-03-09 to 2025-04-04; Start time changed from 09:00:00 to 08:00:00; End time changed from 12:00:00 to 18:00:00; Status changed from Busy to On-Call; Notes changed from \'Open for appointments\' to \'On-Call\'.'),
(31, 7, 'Add Schedule', 'doctor_schedule', 23, '2025-03-30 12:49:00', 'Added new schedule ID 23: Doctor ID 14; Date 2025-04-02; Start time 09:00:00; End time 18:00:00; Status Blocked; Notes \'Day Off\'.'),
(32, 2, 'Appointment Deleted', 'appointments', 36, '2025-04-02 22:35:01', 'Appointment ID: 36 deleted by Doctor ID: 1'),
(33, 2, 'Appointment Updated', 'appointments', 35, '2025-04-03 10:19:28', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 22, Date: 2025-04-07, Time: 14:00, Status: Scheduled, Message: Appointment rescheduled to 2pm'),
(34, 2, 'Appointment Updated', 'appointments', 31, '2025-04-03 10:53:18', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 383, Date: 2025-04-30, Time: 14:40:00, Status: Scheduled'),
(35, 2, 'Appointment Updated', 'appointments', 28, '2025-04-03 10:56:14', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 3, Date: 2025-04-09, Time: 09:35:00, Status: Scheduled'),
(36, 2, 'Appointment Updated', 'appointments', 28, '2025-04-03 10:56:27', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 3, Date: 2025-04-09, Time: 09:35:00, Status: Cancelled'),
(37, 2, 'Appointment Updated', 'appointments', 28, '2025-04-03 10:56:58', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 3, Date: 2025-04-09, Time: 09:35:00, Status: Completed'),
(38, 2, 'Appointment Updated', 'appointments', 28, '2025-04-03 10:57:15', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 3, Date: 2025-04-09, Time: 09:35:00, Status: Scheduled'),
(39, 2, 'Appointment Updated', 'appointments', 28, '2025-04-03 11:12:42', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 3, Date: 2025-04-09, Time: 09:35:00, Status: Cancelled'),
(40, 2, 'Appointment Updated', 'appointments', 35, '2025-04-03 11:13:03', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 22, Date: 2025-04-07, Time: 14:00:00, Status: Completed'),
(41, 2, 'Appointment Created', 'appointments', 36, '2025-04-03 18:15:07', 'Appointment created by Doctor ID: 1. Values: Patient ID: 9, Date: 2025-04-15, Time: 09:14:00, Status: Scheduled, Message: Back Pains'),
(42, 2, 'Appointment Updated', 'appointments', 28, '2025-04-06 13:45:29', 'Appointment updated by Doctor ID: 1. New values: Patient ID: 3, Date: 2025-04-09, Time: 09:35:00, Status: Scheduled'),
(43, 2, 'Appointment Created', 'appointments', 37, '2025-04-06 19:21:22', 'Appointment created by Doctor ID: 1. Values: Patient ID: 9, Date: 2025-04-06, Time: 22:20:00, Status: Scheduled, Message: Headache'),
(44, 2, 'Delete', 'appointments', 29, '2025-04-07 10:35:13', 'Deleted appointment with ID 29 tied to schedule ID 27'),
(45, 2, 'Delete', 'doctor_schedule', 27, '2025-04-07 10:35:13', 'Deleted schedule with ID 27'),
(46, 2, 'Update', 'doctor_schedule', 25, '2025-04-07 11:14:35', 'Updated schedule with ID 25: Date=2025-04-30, Start Time=02:40 PM, End Time=03:30 PM, Status=Busy'),
(47, 2, 'Add', 'doctor_schedule', 40, '2025-04-07 12:15:16', 'Added new schedule ID 40: Doctor ID 1; Date 2025-05-01; Start time 06:00:00; End time 23:55:00; Status Blocked; Notes \'Off Day\'.'),
(48, 2, 'View', 'patient_records', 6, '2025-04-07 13:40:43', 'Doctor (ID: 1) viewed medical record ID 6 for patient ID 3.'),
(49, 2, 'View', 'patient_records', 5, '2025-04-07 13:43:45', 'Doctor (ID: 1) viewed medical record ID 5 for patient ID 3.'),
(50, 2, 'View', 'patient_records', 5, '2025-04-07 14:09:53', 'Doctor (ID: 1) viewed medical record ID 5 for patient ID 3.'),
(51, 2, 'View', 'patient_records', 5, '2025-04-07 14:11:47', 'Doctor (ID: 1) viewed medical record ID 5 for patient ID 3.'),
(52, 2, 'View', 'patient_records', 5, '2025-04-07 14:26:02', 'Doctor (ID: 1) viewed medical record ID 5 for patient ID 3.'),
(53, 2, 'View', 'patient_records', 5, '2025-04-07 14:26:04', 'Doctor (ID: 1) viewed medical record ID 5 for patient ID 3.'),
(54, 2, 'View', 'patient_records', 5, '2025-04-07 14:26:37', 'Doctor (ID: 1) viewed medical record ID 5 for patient ID 3.'),
(55, 2, 'View', 'patient_records', 5, '2025-04-07 14:49:08', 'Doctor (ID: 1) viewed medical record ID 5 for patient ID 3.'),
(56, 2, 'View', 'patient_records', 5, '2025-04-07 20:29:47', 'Doctor (ID: 1) viewed medical record ID 5 for patient ID 3.'),
(57, 2, 'INSERT', 'visit_records', 11, '2025-04-07 20:53:26', NULL),
(58, 2, 'GENERATE_PDF', 'visit_records', 11, '2025-04-07 20:53:31', NULL),
(59, 11, 'INSERT', 'patient_records', 8, '2025-04-07 21:05:47', NULL),
(60, 19, 'INSERT', 'patient_records', 9, '2025-04-07 21:12:52', NULL),
(61, 1, 'Updated invoice', 'billing', 1, '2025-04-08 07:28:43', '{\"invoice_number\":\"#INV-01-001-2025\",\"patient_id\":\"3\",\"amount\":\"1000.50\",\"payment_status\":\"Paid\",\"transaction_date\":\"2025-04-01 22:50:13\"}'),
(62, 1, 'Updated invoice', 'billing', 1, '2025-04-08 07:28:59', '{\"invoice_number\":\"#INV-01-001-2025\",\"patient_id\":\"3\",\"amount\":\"1000.50\",\"payment_status\":\"Pending\",\"transaction_date\":\"2025-04-01 22:50:13\"}'),
(63, 1, 'Updated invoice', 'billing', 1, '2025-04-08 07:50:16', '{\"invoice_number\":\"#INV-01-001-2025\",\"patient_id\":\"3\",\"amount\":\"1000.50\",\"payment_status\":\"Paid\",\"transaction_date\":\"2025-04-01 22:50:13\"}'),
(64, 1, 'Created invoice', 'billing', 2, '2025-04-08 08:28:19', '{\"invoice_number\":\"#INV-01-002-2025\",\"patient_id\":\"383\",\"amount\":\"5000.00\",\"payment_status\":\"Pending\",\"transaction_date\":null}'),
(65, 1, 'Created invoice', 'billing', 3, '2025-04-08 08:30:00', '{\"invoice_number\":\"#INV-01-003-2025\",\"patient_id\":\"5\",\"amount\":\"2000.00\",\"payment_status\":\"Pending\",\"transaction_date\":null}'),
(66, 1, 'Created invoice', 'billing', 4, '2025-04-08 08:33:45', '{\"invoice_number\":\"#INV-01-004-2025\",\"patient_id\":\"22\",\"amount\":\"3000.50\",\"payment_status\":\"Failed\",\"transaction_date\":\"2025-04-07 08:32:21\"}'),
(67, 1, 'Created invoice', 'billing', 5, '2025-04-08 14:55:00', '{\"invoice_number\":\"#INV-01-005-2025\",\"patient_id\":\"5\",\"amount\":\"3999.99\",\"payment_status\":\"Pending\",\"transaction_date\":null}'),
(68, 1, 'Created invoice', 'billing', 6, '2025-04-08 14:55:53', '{\"invoice_number\":\"#INV-01-006-2025\",\"patient_id\":\"5\",\"amount\":\"10000.00\",\"payment_status\":\"Pending\",\"transaction_date\":null}'),
(69, 2, 'Appointment Created', 'appointments', 43, '2025-04-08 18:15:09', 'Appointment created by Doctor ID: 1. Values: Patient ID: 26, Date: 2025-04-08, Time: 11:15:00, Status: Scheduled, Message: Booked'),
(70, 1, 'Created invoice', 'billing', 7, '2025-04-08 18:16:00', '{\"invoice_number\":\"#INV-01-007-2025\",\"patient_id\":\"26\",\"amount\":\"5000.00\",\"payment_status\":\"Pending\",\"transaction_date\":null}'),
(71, 2, 'Appointment Created', 'appointments', 44, '2025-04-08 18:18:05', 'Appointment created by Doctor ID: 1. Values: Patient ID: 5, Date: 2025-04-08, Time: 13:17:00, Status: Scheduled, Message: Appointment'),
(72, 1, 'Created invoice', 'billing', 8, '2025-04-08 18:18:47', '{\"invoice_number\":\"#INV-01-008-2025\",\"patient_id\":\"5\",\"amount\":\"10000.00\",\"payment_status\":\"Pending\",\"transaction_date\":null}'),
(73, 1, 'Created invoice', 'billing', 9, '2025-04-08 19:01:48', '{\"invoice_number\":\"#INV-01-009-2025\",\"patient_id\":\"5\",\"amount\":\"5000.00\",\"payment_status\":\"Pending\",\"transaction_date\":null}'),
(74, 11, 'INSERT', 'patient_records', 10, '2025-04-08 20:35:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Card','Mpesa') DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed','Refunded') DEFAULT 'Pending',
  `transaction_token` varchar(255) DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `patient_id`, `appointment_id`, `invoice_number`, `amount`, `payment_method`, `payment_status`, `transaction_token`, `transaction_date`, `created_at`, `updated_at`) VALUES
(1, 3, 28, '#INV-01-001-2025', 1000.50, 'Mpesa', 'Paid', '07*******9', '2025-04-01 22:50:13', '2025-04-01 20:51:53', '2025-04-08 07:50:16'),
(2, 383, 31, '#INV-01-002-2025', 5000.00, 'Card', 'Pending', '', NULL, '2025-04-08 08:28:19', '2025-04-08 08:28:19'),
(3, 5, 7, '#INV-01-003-2025', 2000.00, '', 'Paid', '65e15649d1768f07', '2025-04-08 17:20:41', '2025-04-08 08:30:00', '2025-04-08 14:20:41'),
(5, 5, 18, '#INV-01-005-2025', 3999.99, '', 'Paid', '907eea46e072681333f6cce9028af1eca9de8982e7093ee664c4e39f404fb4e6', '2025-04-08 19:05:16', '2025-04-08 14:55:00', '2025-04-08 16:05:16'),
(6, 5, 34, '#INV-01-006-2025', 10000.00, 'Card', 'Paid', '0705c3e5d52dc238', '2025-04-08 18:45:40', '2025-04-08 14:55:53', '2025-04-08 15:45:40'),
(7, 26, 43, '#INV-01-007-2025', 5000.00, 'Mpesa', 'Pending', '', NULL, '2025-04-08 18:16:00', '2025-04-08 18:16:00'),
(8, 5, 44, '#INV-01-008-2025', 10000.00, '', 'Paid', '907eea46e072681333f6cce9028af1eca9de8982e7093ee664c4e39f404fb4e6', '2025-04-08 21:19:41', '2025-04-08 18:18:47', '2025-04-08 18:19:41'),
(9, 5, 45, '#INV-01-009-2025', 5000.00, 'Card', 'Paid', '375bd947c93fbd38', '2025-04-08 22:02:44', '2025-04-08 19:01:48', '2025-04-08 19:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_logs`
--

CREATE TABLE `chatbot_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `response` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatbot_logs`
--

INSERT INTO `chatbot_logs` (`id`, `user_id`, `message`, `response`, `timestamp`) VALUES
(1, 1, 'When is doctor Allan available', 'Dr.Allan Thiongo has his schedule free on Friday Afternoon would like for me to book you and appointment?', '2025-03-22 18:51:34'),
(2, 11, 'Are there any cardiologist in this hospital?', 'Yes.\r\nDr. Andrew Romell\r\nDr. Daniel Karanja\r\nWould you like to book an appointment with one of this doctors?', '2025-03-22 18:55:12'),
(3, 14, 'I want access to my medical records', 'Okay. Just a moment please as I retrieve them for you....\r\n\r\nAnything else you\'d want help with?', '2025-03-22 18:57:45');

-- --------------------------------------------------------

--
-- Table structure for table `data_access_logs`
--

CREATE TABLE `data_access_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `access_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `action` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_access_logs`
--

INSERT INTO `data_access_logs` (`id`, `user_id`, `patient_id`, `access_time`, `action`) VALUES
(1, 2, 3, '2025-03-19 13:46:25', 'VIEW_PATIENT_RECORDS'),
(2, 2, 5, '2025-03-19 13:49:54', 'VIEW_PATIENT_RECORDS'),
(3, 2, 5, '2025-03-19 13:51:15', 'VIEW_PATIENT_RECORDS'),
(4, 2, 3, '2025-03-19 14:05:51', 'VIEW_PATIENT_RECORDS'),
(5, 2, 3, '2025-03-19 14:05:59', 'DOWNLOAD_PATIENT_FILE'),
(6, 2, 3, '2025-03-19 14:07:10', 'DOWNLOAD_PATIENT_FILE'),
(7, 2, 3, '2025-03-19 15:29:25', 'VIEW_PATIENT_RECORDS'),
(8, 2, 3, '2025-03-19 15:29:56', 'DOWNLOAD_PATIENT_FILE'),
(9, 2, 3, '2025-03-19 15:30:07', 'DOWNLOAD_PATIENT_FILE'),
(10, 2, 3, '2025-03-19 15:33:37', 'VIEW_PATIENT_RECORDS'),
(11, 2, 3, '2025-03-19 15:33:50', 'DOWNLOAD_PATIENT_FILE'),
(12, 2, 3, '2025-03-19 15:33:54', 'DOWNLOAD_PATIENT_FILE'),
(13, 2, 3, '2025-03-24 18:57:16', 'VIEW_PATIENT_RECORDS'),
(14, 2, 3, '2025-03-24 18:57:29', 'DOWNLOAD_PATIENT_FILE'),
(15, 2, 3, '2025-04-07 20:33:16', 'VIEW_PATIENT_RECORDS'),
(16, 2, 3, '2025-04-07 20:43:49', 'VIEW_PATIENT_RECORDS'),
(17, 2, 3, '2025-04-07 20:43:52', 'VIEW_PATIENT_RECORDS'),
(18, 2, 3, '2025-04-07 20:43:53', 'VIEW_PATIENT_RECORDS'),
(19, 2, 3, '2025-04-07 20:43:54', 'VIEW_PATIENT_RECORDS'),
(20, 2, 3, '2025-04-07 20:43:54', 'VIEW_PATIENT_RECORDS'),
(21, 2, 3, '2025-04-07 20:43:54', 'VIEW_PATIENT_RECORDS'),
(22, 2, 3, '2025-04-07 20:43:57', 'DOWNLOAD_MEDICAL_RECORD'),
(23, 2, 3, '2025-04-07 20:44:14', 'DOWNLOAD_PATIENT_FILE'),
(24, 2, 3, '2025-04-07 20:48:15', 'VIEW_PATIENT_RECORDS'),
(25, 2, 116, '2025-04-07 20:53:31', 'VIEW_PATIENT_RECORDS'),
(26, 2, 5, '2025-04-07 21:14:03', 'VIEW_PATIENT_RECORDS'),
(27, 2, 5, '2025-04-07 21:14:09', 'VIEW_PATIENT_RECORDS'),
(28, 2, 5, '2025-04-07 21:15:29', 'DOWNLOAD_MEDICAL_RECORD'),
(29, 2, 5, '2025-04-09 09:42:20', 'VIEW_PATIENT_RECORDS');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `staff_id` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `department` enum('Cardiologist','Neurologist','Dermatologist','Oncologist','Pediatrician','Internist','Orthopedist','Gynecologist','Urologist','Pulmonologist','Endocrinologist','Gastroenterologist','Radiologist','Anesthesiologist','Surgeon') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `profile_pic`, `staff_id`, `first_name`, `last_name`, `department`, `date_of_birth`, `gender`, `address`, `email`, `contact_number`, `created_at`) VALUES
(1, 'doc1.jpg', 'DR-01-001-2025', 'allan', 'thiong\'o', 'Surgeon', '2003-02-06', 'Male', '0100,nairobi', 'allanthiongo@gmail.com', '0712345678', '2025-02-03 20:35:21'),
(2, 'doctor_2_1743515929.png', 'DR-01-002-2025', 'andrew', 'romell', 'Cardiologist', '1969-02-18', 'Male', '0100, nairobi', 'andrewromell@gmail.com', '0798765452', '2025-02-06 13:03:45'),
(3, 'doctor_3_1741951533.jpg', 'DR-01-003-2025', 'jackline', 'matindi', 'Neurologist', '2003-02-01', 'Female', '546, Nairobi', 'jacklinematindi@gmail.com', '0754381004', '2025-02-13 12:43:50'),
(4, 'doctor-thumb-08.jpg', 'DR-01-004-2025', 'Fatuma', 'Onyango', 'Pediatrician', '1982-04-12', 'Female', 'Kisumu', 'fatuma.onyango@gmail.com', '254712987654', '2024-03-15 11:00:00'),
(5, 'doc02.jpg', 'DR-01-005-2025', 'Patrick', 'Wekesa', 'Orthopedist', '1975-07-25', 'Male', 'Eldoret', 'patrick.wekesa@yahoo.com', '254723876543', '2024-06-20 06:30:00'),
(6, 'doctor-thumb-10.jpg', 'DR-01-006-2025', 'Lilian', 'Koech', 'Gynecologist', '1988-09-03', 'Female', 'Nakuru', 'lilian.koech@hotmail.com', '254734765432', '2024-09-10 08:15:00'),
(7, 'doctor-thumb-11.jpg', 'DR-01-007-2025', 'Moses', 'Muriithi', 'Endocrinologist', '1980-11-18', 'Male', 'Nyeri', 'moses.muriithi@gmail.com', '254745654321', '2024-11-05 13:45:00'),
(8, 'fdoc4.jpg', 'DR-01-008-2025', 'Beatrice', 'Chepkwony', 'Internist', '1990-01-30', 'Female', 'Kericho', 'beatrice.chepkwony@outlook.com', '254756543210', '2025-01-12 05:00:00'),
(9, 'doc04.jpg', 'DR-01-009-2025', 'Daniel', 'Karanja', 'Cardiologist', '1983-06-14', 'Male', 'Nairobi', 'daniel.karanja@gmail.com', '254767432109', '2025-02-01 10:30:00'),
(10, 'fdoc5.jpg', 'DR-01-010-2025', 'Hellen', 'Muthoni', 'Neurologist', '1992-12-08', 'Female', 'Mombasa', 'hellen.muthoni@yahoo.com', '254778321098', '2024-04-22 07:00:00'),
(11, 'doc05.jpeg', 'DR-01-011-2025', 'Joseph', 'Kiptoo', 'Urologist', '1978-03-22', 'Male', 'Kakamega', 'joseph.kiptoo@gmail.com', '254789210987', '2024-08-15 12:20:00'),
(12, 'fdoc7.jpeg', 'DR-01-012-2025', 'Caroline', 'Nyambura', 'Dermatologist', '1987-05-19', 'Female', 'Thika', 'caroline.nyambura@hotmail.com', '254790109876', '2024-10-18 09:45:00'),
(13, 'doc06.jpg', 'DR-01-013-2025', 'Samuel', 'Mwangi', 'Oncologist', '1985-08-07', 'Male', 'Kiambu', 'samuel.mwangi@outlook.com', '254701098765', '2025-02-10 06:15:00'),
(14, 'fdoc8.jpeg', 'DR-01-014-2025', 'Agnes', 'Wambui', 'Pulmonologist', '1993-10-15', 'Female', 'Machakos', 'agnes.wambui@gmail.com', '254712098654', '2024-12-30 11:30:00'),
(15, 'doc07.jpeg', 'DR-01-015-2025', 'Eric', 'Otieno', 'Endocrinologist', '1981-02-28', 'Male', 'Bungoma', 'eric.otieno@yahoo.com', '254723987653', '2024-07-25 08:00:00'),
(19, 'doctor_1741959501.png', 'DR-01-016-2025', 'Sessy', 'Nyawira', 'Surgeon', '1974-04-06', 'Female', '560, Nairobi Kenya', 'sessynyawira@gmail.com', '0781300472', '2025-03-14 13:38:21'),
(20, 'doctor_1741959841.png', 'DR-01-017-2025', 'Fredrick', 'Mutiso', 'Endocrinologist', '0000-00-00', 'Male', 'Thika Town, 106 street', 'fredrickmutiso@gmail.com', '0798547650', '2025-03-14 13:44:01'),
(21, 'doctor_1741960810.png', 'DR-01-018-2025', 'Eric', 'Omondi', 'Pulmonologist', '0000-00-00', 'Male', '51, Westlands', 'erickomondi51@gmail.com', '0700015412', '2025-03-14 14:00:10'),
(22, 'doctor_22_1743111065.jpg', 'DR-01-019-2025', 'dennis', 'gachuiri', 'Surgeon', '1994-05-09', 'Male', 'nairobi', 'dennisgachuiri@gmail.com', '0743214550', '2025-03-16 13:23:14'),
(25, 'doctor_1743086851.jpg', 'DR-01-020-2025', 'Janet', 'Kuria', 'Pediatrician', '0000-00-00', 'Female', 'Tomboya street, Nairobi', 'janetkuria10@gmail.com', '0744419030', '2025-03-27 14:47:31'),
(26, 'doctor_1743110961.jpg', 'DR-01-021-2025', 'Judas', 'msaliti', 'Gynecologist', '1970-01-01', 'Male', 'Jerusalem', 'judasmsaliti@gmail.com', '0733333333', '2025-03-27 21:29:21'),
(27, 'doctor_1743375672.jpg', 'DR-01-022-2025', 'john', 'demathio', 'Pulmonologist', '0000-00-00', 'Male', 'Murang\'a', 'johndemathio@gmail.com', '0791234551', '2025-03-30 23:01:12'),
(29, NULL, 'DR-01-023-2025', 'john', 'laurence', 'Oncologist', '1985-09-19', 'Male', 'Nairobi', 'johnlaurence52@gmail.com', '0703101635', '2025-03-31 00:22:00');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule`
--

CREATE TABLE `doctor_schedule` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `schedule_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('Available','Busy','On-Call','Blocked') DEFAULT 'Available',
  `appointment_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedule`
--

INSERT INTO `doctor_schedule` (`id`, `doctor_id`, `schedule_date`, `start_time`, `end_time`, `status`, `appointment_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-03-06', '09:00:00', '12:00:00', 'Available', NULL, 'Morning consultation', '2025-03-06 18:06:27', NULL),
(3, 2, '2025-03-07', '09:00:00', '15:00:00', 'On-Call', NULL, 'Emergency duty', '2025-03-06 18:06:27', NULL),
(4, 3, '2025-03-08', '10:00:00', '14:00:00', 'Blocked', NULL, 'Vacation day', '2025-03-06 18:06:27', NULL),
(5, 2, '2025-04-04', '08:00:00', '18:00:00', 'On-Call', NULL, 'On-Call', '2025-03-06 18:06:27', '2025-03-30 11:35:30'),
(6, 4, '2025-03-05', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(7, 5, '2025-03-06', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(8, 6, '2025-03-07', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(9, 7, '2025-03-08', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(10, 8, '2025-03-09', '09:00:00', '17:00:00', 'Blocked', NULL, 'Regular shift', '2025-03-06 18:44:16', '2025-03-30 11:29:02'),
(11, 9, '2025-03-10', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(12, 10, '2025-03-11', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(13, 11, '2025-03-12', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(14, 12, '2025-03-13', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(15, 13, '2025-03-14', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(16, 14, '2025-03-15', '09:00:00', '17:00:00', 'Available', NULL, 'Regular shift', '2025-03-06 18:44:16', NULL),
(22, 3, '2025-04-02', '10:00:00', NULL, 'Busy', NULL, 'appointment with patient', '2025-03-28 21:57:56', '2025-03-30 11:28:31'),
(23, 14, '2025-04-02', '09:00:00', '18:00:00', 'Blocked', NULL, 'Day Off', '2025-03-30 12:49:00', NULL),
(24, 1, '2025-04-08', '08:45:28', NULL, 'Busy', 28, NULL, '2025-04-01 20:46:36', NULL),
(25, 1, '2025-04-30', '14:40:00', '15:30:00', 'Busy', 31, 'appointment with patient', '2025-04-02 11:40:37', '2025-04-07 11:14:35'),
(26, 3, '2025-04-24', '14:45:00', NULL, 'Busy', 32, 'appointment with patient', '2025-04-02 11:45:55', '2025-04-02 11:45:55'),
(28, 1, '2025-04-07', '13:40:29', NULL, 'Busy', 30, NULL, '2025-04-02 12:00:00', NULL),
(35, 1, '2025-04-24', '11:45:00', NULL, 'Busy', 31, NULL, '2025-04-02 12:14:44', NULL),
(36, 1, '2025-04-16', '15:45:00', NULL, 'Busy', 33, 'appointment with patient', '2025-04-02 13:46:19', '2025-04-02 13:46:19'),
(37, 1, '2025-04-25', '17:00:00', NULL, 'Busy', 34, 'appointment with patient', '2025-04-02 21:56:20', '2025-04-02 21:56:20'),
(38, 1, '2025-04-07', '12:00:00', NULL, 'Busy', 35, 'appointment with patient', '2025-04-02 21:57:50', '2025-04-02 21:57:50'),
(39, 1, '2025-04-10', '08:00:00', NULL, 'Busy', NULL, 'appointment with patient', '2025-04-02 21:59:43', '2025-04-02 21:59:43'),
(40, 1, '2025-05-01', '06:00:00', '23:55:00', 'Blocked', NULL, 'Off Day', '2025-04-07 12:15:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `education_informations`
--

CREATE TABLE `education_informations` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `institution` varchar(100) NOT NULL,
  `starting_date` date NOT NULL,
  `complete_date` date NOT NULL,
  `degree` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `grade` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education_informations`
--

INSERT INTO `education_informations` (`id`, `staff_id`, `institution`, `starting_date`, `complete_date`, `degree`, `subject`, `grade`, `created_at`, `updated_at`) VALUES
(9, 'DR-01-001-2025', 'Oxford University', '2002-06-01', '2006-05-31', 'BE Medicine', 'Medicine and Surgery', 'Grade A', '2025-03-26 10:01:12', NULL),
(10, 'AD-01-001-2025', 'Harvard University', '1998-09-01', '2002-05-31', 'MBA', 'Business Administration', 'Grade B', '2025-03-26 10:01:12', NULL),
(11, 'NR-01-001-2025', 'Nairobi Nursing School', '2010-01-15', '2013-12-15', 'Diploma in Nursing', 'Nursing', 'Distinction', '2025-03-26 10:01:12', NULL),
(12, 'REC-01-001-2025', 'Kenyatta University', '2005-09-01', '2009-05-31', 'BSc in Office Management', 'Office Management', 'Grade A', '2025-03-26 10:01:12', NULL),
(13, 'DR-01-001-2025', 'Oxford University', '1998-09-01', '2002-05-31', 'MBBS', 'Medicine', 'Distinction', '2025-03-26 14:30:35', NULL),
(14, 'DR-01-001-2025', 'Harvard Medical School', '2003-06-01', '2005-05-31', 'MD', 'Surgery', 'Grade A', '2025-03-26 14:30:35', NULL),
(15, 'DR-01-001-2025', 'Johns Hopkins University', '2006-01-15', '2008-12-15', 'PhD', 'Surgical Innovation', 'Distinction', '2025-03-26 14:30:35', NULL),
(16, 'DR-01-002-2025', 'University of Nairobi', '1995-09-01', '2000-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL),
(17, 'DR-01-003-2025', 'Kenyatta University', '1999-09-01', '2004-05-31', 'MBBS', 'Medicine', 'Grade A', '2025-03-26 14:30:35', NULL),
(18, 'DR-01-004-2025', 'Moi University', '2000-09-01', '2005-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL),
(19, 'DR-01-005-2025', 'University of Cape Town', '1994-09-01', '1999-05-31', 'MBBS', 'Medicine', 'Grade A', '2025-03-26 14:30:35', NULL),
(20, 'DR-01-005-2025', 'Stanford University', '2000-06-01', '2002-05-31', 'MD', 'Orthopedics', 'Distinction', '2025-03-26 14:30:35', NULL),
(21, 'DR-01-006-2025', 'Egerton University', '1997-09-01', '2002-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL),
(22, 'DR-01-007-2025', 'University of Nairobi', '1996-09-01', '2001-05-31', 'MBBS', 'Medicine', 'Grade A', '2025-03-26 14:30:35', NULL),
(23, 'DR-01-008-2025', 'Kenyatta University', '1993-09-01', '1998-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL),
(24, 'DR-01-009-2025', 'Moi University', '1998-09-01', '2003-05-31', 'MBBS', 'Medicine', 'Grade A', '2025-03-26 14:30:35', NULL),
(25, 'DR-01-010-2025', 'Cambridge University', '1995-09-01', '1999-05-31', 'MBBS', 'Medicine', 'Distinction', '2025-03-26 14:30:35', NULL),
(26, 'DR-01-010-2025', 'Yale University', '2000-06-01', '2002-05-31', 'MD', 'Neurology', 'Grade A', '2025-03-26 14:30:35', NULL),
(27, 'DR-01-010-2025', 'MIT', '2003-01-15', '2005-12-15', 'PhD', 'Medical Research', 'Distinction', '2025-03-26 14:30:35', NULL),
(28, 'DR-01-011-2025', 'University of Nairobi', '1994-09-01', '1999-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL),
(29, 'DR-01-012-2025', 'Kenyatta University', '1996-09-01', '2001-05-31', 'MBBS', 'Medicine', 'Grade A', '2025-03-26 14:30:35', NULL),
(30, 'DR-01-013-2025', 'Moi University', '1997-09-01', '2002-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL),
(31, 'DR-01-014-2025', 'Egerton University', '1998-09-01', '2003-05-31', 'MBBS', 'Medicine', 'Grade A', '2025-03-26 14:30:35', NULL),
(32, 'DR-01-015-2025', 'University of Nairobi', '1995-09-01', '2000-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL),
(33, 'DR-01-016-2025', 'Kenyatta University', '1999-09-01', '2004-05-31', 'MBBS', 'Medicine', 'Grade A', '2025-03-26 14:30:35', NULL),
(34, 'DR-01-017-2025', 'Moi University', '2000-09-01', '2005-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL),
(35, 'DR-01-018-2025', 'Egerton University', '1997-09-01', '2002-05-31', 'MBBS', 'Medicine', 'Grade A', '2025-03-26 14:30:35', NULL),
(36, 'DR-01-019-2025', 'University of Nairobi', '1996-09-01', '2001-05-31', 'MBBS', 'Medicine', 'Grade B', '2025-03-26 14:30:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `staff_id` varchar(50) NOT NULL,
  `role` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`staff_id`, `role`, `created_at`) VALUES
('AD-01-001-2025', 'Admin', '2025-03-22 22:58:06'),
('AD-01-002-2025', 'Admin', '2025-03-22 23:01:35'),
('AD-01-003-2025', 'Admin', '2025-03-22 23:01:35'),
('AD-01-004-2025', 'Admin', '2025-03-31 00:55:35'),
('AD-01-005-2025', 'Admin', '2025-03-31 03:33:12'),
('DR-01-001-2025', 'Doctor', '2025-02-03 23:35:21'),
('DR-01-002-2025', 'Doctor', '2025-02-06 16:03:45'),
('DR-01-003-2025', 'Doctor', '2025-02-13 15:43:50'),
('DR-01-004-2025', 'Doctor', '2024-03-15 14:00:00'),
('DR-01-005-2025', 'Doctor', '2024-06-20 09:30:00'),
('DR-01-006-2025', 'Doctor', '2024-09-10 11:15:00'),
('DR-01-007-2025', 'Doctor', '2024-11-05 16:45:00'),
('DR-01-008-2025', 'Doctor', '2025-01-12 08:00:00'),
('DR-01-009-2025', 'Doctor', '2025-02-01 13:30:00'),
('DR-01-010-2025', 'Doctor', '2024-04-22 10:00:00'),
('DR-01-011-2025', 'Doctor', '2024-08-15 15:20:00'),
('DR-01-012-2025', 'Doctor', '2024-10-18 12:45:00'),
('DR-01-013-2025', 'Doctor', '2025-02-10 09:15:00'),
('DR-01-014-2025', 'Doctor', '2024-12-30 14:30:00'),
('DR-01-015-2025', 'Doctor', '2024-07-25 11:00:00'),
('DR-01-016-2025', 'Doctor', '2025-03-14 16:38:21'),
('DR-01-017-2025', 'Doctor', '2025-03-14 16:44:01'),
('DR-01-018-2025', 'Doctor', '2025-03-14 17:00:10'),
('DR-01-019-2025', 'Doctor', '2025-03-16 16:23:14'),
('DR-01-020-2025', 'Doctor', '2025-03-27 17:47:31'),
('DR-01-021-2025', 'Doctor', '2025-03-28 00:29:21'),
('DR-01-022-2025', 'Doctor', '2025-03-31 02:01:12'),
('DR-01-023-2025', 'Doctor', '2025-03-31 03:22:00'),
('NR-01-001-2025', 'Nurse', '2025-02-06 17:26:52'),
('NR-01-002-2025', 'Nurse', '2025-02-07 19:31:36'),
('REC-01-001-2025', 'Receptionist', '2025-03-22 22:52:40'),
('REC-01-002-2025', 'Receptionist', '2025-03-22 22:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `experience_informations`
--

CREATE TABLE `experience_informations` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `job_position` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `period_from` date NOT NULL,
  `period_to` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experience_informations`
--

INSERT INTO `experience_informations` (`id`, `staff_id`, `company_name`, `job_position`, `location`, `period_from`, `period_to`, `created_at`, `updated_at`) VALUES
(5, 'DR-01-001-2025', 'Kenyatta Hospital', 'Surgeon', 'Nairobi', '2007-07-01', '2018-06-08', '2025-03-26 10:30:01', NULL),
(6, 'AD-01-001-2025', 'Global Health Corp', 'HR Manager', 'Kenya', '2010-01-15', '2020-12-31', '2025-03-26 10:30:01', NULL),
(7, 'NR-01-001-2025', 'Nairobi Hospital', 'Registered Nurse', 'Kenya', '2014-03-01', '2022-05-31', '2025-03-26 10:30:01', NULL),
(8, 'REC-01-001-2025', 'City Clinic', 'Receptionist', 'Kenya', '2010-09-01', '2015-05-31', '2025-03-26 10:30:01', NULL),
(9, 'DR-01-001-2025', 'Nairobi Hospital', 'Surgeon', 'Kenya', '2003-06-01', '2008-05-31', '2025-03-26 14:32:22', NULL),
(10, 'DR-01-001-2025', 'Aga Khan Hospital', 'Senior Surgeon', 'Kenya', '2008-06-01', '2015-12-31', '2025-03-26 14:32:22', NULL),
(11, 'DR-01-001-2025', 'Kenyatta National Hospital', 'Chief Surgeon', 'Kenya', '2016-01-01', '2023-12-31', '2025-03-26 14:32:22', NULL),
(12, 'DR-01-002-2025', 'Mater Hospital', 'Cardiologist', 'Kenya', '2001-06-01', '2010-05-31', '2025-03-26 14:32:22', NULL),
(13, 'DR-01-003-2025', 'Kenyatta National Hospital', 'Neurologist', 'Kenya', '2005-06-01', '2015-05-31', '2025-03-26 14:32:22', NULL),
(14, 'DR-01-004-2025', 'Aga Khan Hospital', 'Pediatrician', 'Kenya', '2006-06-01', '2016-05-31', '2025-03-26 14:32:22', NULL),
(15, 'DR-01-005-2025', 'Nairobi Hospital', 'Orthopedic Surgeon', 'Kenya', '2000-06-01', '2010-05-31', '2025-03-26 14:32:22', NULL),
(16, 'DR-01-005-2025', 'Kenyatta National Hospital', 'Senior Orthopedic Surgeon', 'Kenya', '2010-06-01', '2020-05-31', '2025-03-26 14:32:22', NULL),
(17, 'DR-01-006-2025', 'Mater Hospital', 'Gynecologist', 'Kenya', '2003-06-01', '2013-05-31', '2025-03-26 14:32:22', NULL),
(18, 'DR-01-007-2025', 'Aga Khan Hospital', 'Endocrinologist', 'Kenya', '2002-06-01', '2012-05-31', '2025-03-26 14:32:22', NULL),
(19, 'DR-01-008-2025', 'Kenyatta National Hospital', 'Internist', 'Kenya', '2000-06-01', '2010-05-31', '2025-03-26 14:32:22', NULL),
(20, 'DR-01-009-2025', 'Nairobi Hospital', 'Cardiologist', 'Kenya', '2004-06-01', '2014-05-31', '2025-03-26 14:32:22', NULL),
(21, 'DR-01-010-2025', 'Aga Khan Hospital', 'Neurologist', 'Kenya', '2003-06-01', '2008-05-31', '2025-03-26 14:32:22', NULL),
(22, 'DR-01-010-2025', 'Kenyatta National Hospital', 'Senior Neurologist', 'Kenya', '2008-06-01', '2015-12-31', '2025-03-26 14:32:22', NULL),
(23, 'DR-01-010-2025', 'Nairobi Hospital', 'Head of Neurology', 'Kenya', '2016-01-01', '2023-12-31', '2025-03-26 14:32:22', NULL),
(24, 'DR-01-011-2025', 'Mater Hospital', 'Urologist', 'Kenya', '2000-06-01', '2010-05-31', '2025-03-26 14:32:22', NULL),
(25, 'DR-01-012-2025', 'Kenyatta National Hospital', 'Surgeon', 'Kenya', '2002-06-01', '2012-05-31', '2025-03-26 14:32:22', NULL),
(26, 'DR-01-013-2025', 'Aga Khan Hospital', 'Cardiologist', 'Kenya', '2003-06-01', '2013-05-31', '2025-03-26 14:32:22', NULL),
(27, 'DR-01-014-2025', 'Nairobi Hospital', 'Neurologist', 'Kenya', '2004-06-01', '2014-05-31', '2025-03-26 14:32:22', NULL),
(28, 'DR-01-015-2025', 'Mater Hospital', 'Pediatrician', 'Kenya', '2001-06-01', '2011-05-31', '2025-03-26 14:32:22', NULL),
(29, 'DR-01-016-2025', 'Kenyatta National Hospital', 'Orthopedic Surgeon', 'Kenya', '2005-06-01', '2015-05-31', '2025-03-26 14:32:22', NULL),
(30, 'DR-01-017-2025', 'Aga Khan Hospital', 'Gynecologist', 'Kenya', '2006-06-01', '2016-05-31', '2025-03-26 14:32:22', NULL),
(31, 'DR-01-018-2025', 'Nairobi Hospital', 'Endocrinologist', 'Kenya', '2003-06-01', '2013-05-31', '2025-03-26 14:32:22', NULL),
(32, 'DR-01-019-2025', 'Mater Hospital', 'Internist', 'Kenya', '2002-06-01', '2012-05-31', '2025-03-26 14:32:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `patient_id`, `feedback_text`, `rating`, `submitted_at`) VALUES
(1, 3, 'The services here are so amazing. Got to book my appointment online and when i got there i did not have to wait a single moment.', 5, '2025-03-23 11:40:30'),
(8, 5, 'good', 5, '2025-04-08 13:39:36'),
(9, 3, 'This is the most amazing care I have ever gotten from a hospital. You make me feel like I want to come back.... lol', 5, '2025-04-08 13:46:35'),
(10, 5, 'Excellent services', 5, '2025-04-08 18:20:17'),
(11, 5, 'Excellent', 5, '2025-04-08 18:59:39');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_staffs`
--

CREATE TABLE `hospital_staffs` (
  `id` int(11) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `staff_id` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital_staffs`
--

INSERT INTO `hospital_staffs` (`id`, `profile_pic`, `staff_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `address`, `email`, `contact_number`, `created_at`) VALUES
(1, '\"C:\\xampp\\htdocs\\FINALPROJECT2025\\img\\HS_pic1.jpg\"', 'HS-123456', 'ndovu', 'kuu', '1999-02-05', 'Male', '0100, nairobi', 'ndovunikuu@gmail.com', '0754328100', '2025-02-06 12:20:33'),
(2, '\"C:\\xampp\\htdocs\\FINALPROJECT2025\\img\\Dr.Dre.jpg\"', 'HS-987654', 'alex', 'kamau', '1971-02-13', 'Male', '0100,nairobi', 'alex@gmail.com', '076587600', '2025-02-06 14:56:19');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `recipient_type` enum('Patient','Doctor','Hospital Staff','Nurse','Admin') NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `notification_type` enum('Reminder','Alert') DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `recipient_type`, `recipient_id`, `message`, `notification_type`, `is_read`, `created_at`) VALUES
(1, 'Patient', 3, 'Your appointment APT0001 has been updated on 24 Feb 2025 at 10:00 to Scheduled.\n\nAdditional Note: Appointment schedule has been changed to 2025-02-24 10am', 'Alert', 0, '2025-03-03 14:26:37'),
(2, 'Doctor', 2, 'Appointment APT0001 for derrick gatu has been updated on 24 Feb 2025 at 10:00 to Scheduled.\n\nAdditional Note: Appointment schedule has been changed to 2025-02-24 10am', 'Alert', 0, '2025-03-03 14:26:37'),
(3, 'Patient', 3, 'Your appointment APT0001 has been updated on 24 Feb 2025 at 11:00 to Scheduled.\n\nAdditional Note: Appointment updated to 11am', 'Alert', 0, '2025-03-03 22:30:51'),
(4, 'Doctor', 1, 'Appointment APT0001 for derrick gatu has been updated on 24 Feb 2025 at 11:00 to Scheduled.\n\nAdditional Note: Appointment updated to 11am', 'Alert', 1, '2025-03-03 22:30:51'),
(5, 'Patient', 3, 'Your appointment APT0001 has been updated on 24 Feb 2025 at 12:00 to Scheduled.', 'Alert', 0, '2025-03-03 22:59:55'),
(6, 'Doctor', 1, 'Appointment APT0001 for derrick gatu has been updated on 24 Feb 2025 at 12:00 to Scheduled.', 'Alert', 0, '2025-03-03 22:59:55'),
(7, 'Patient', 11, 'Your appointment APT0017 has been created on 07 Mar 2025 at 08:29 as Scheduled.\n\nAdditional Note: Appointment', 'Alert', 0, '2025-03-05 22:29:53'),
(9, 'Patient', 5, 'Your appointment APT0018 has been created on 07 Mar 2025 at 14:48 as Scheduled.\n\nAdditional Note: Appointment Booked', 'Alert', 0, '2025-03-05 22:49:21'),
(10, 'Doctor', 1, 'Appointment APT0018 for eric wainana has been created on 07 Mar 2025 at 14:48 as Scheduled.\n\nAdditional Note: Appointment Booked', 'Alert', 1, '2025-03-05 22:49:21'),
(12, 'Patient', 3, 'Your appointment APT0020 has been created on 17 Mar 2025 at 12:58 as Scheduled.\n\nAdditional Note: Appointment booked', 'Alert', 0, '2025-03-05 22:58:53'),
(13, 'Doctor', 2, 'Appointment APT0020 for derrick gatu has been created on 17 Mar 2025 at 12:58 as Scheduled.\n\nAdditional Note: Appointment booked', 'Alert', 0, '2025-03-05 22:58:53'),
(18, 'Patient', 22, 'Your appointment APT0022 has been created on 30 Mar 2025 at 14:00 as Scheduled.\n\nAdditional Note: Irritation and rush', 'Alert', 0, '2025-03-26 18:05:35'),
(19, 'Doctor', 6, 'Appointment APT0022 for Mercy Wambui has been created on 30 Mar 2025 at 14:00 as Scheduled.\n\nAdditional Note: Irritation and rush', 'Alert', 0, '2025-03-26 18:05:35'),
(20, 'Patient', 3, 'Your appointment APT0023 has been created on 31 Mar 2025 at 10:00 as Scheduled.\n\nAdditional Note: Regular Visit', 'Alert', 0, '2025-03-26 18:07:03'),
(21, 'Doctor', 1, 'Appointment APT0023 for derrick gatu has been created on 31 Mar 2025 at 10:00 as Scheduled.\n\nAdditional Note: Regular Visit', 'Alert', 0, '2025-03-26 18:07:03'),
(23, 'Patient', 7, 'Your appointment APT0025 has been created on 03 Apr 2025 at 14:30 as Scheduled.\n\nAdditional Note: Check up', 'Alert', 0, '2025-03-26 18:24:48'),
(24, 'Doctor', 6, 'Appointment APT0025 for grace wanjiku has been created on 03 Apr 2025 at 14:30 as Scheduled.\n\nAdditional Note: Check up', 'Alert', 0, '2025-03-26 18:24:48'),
(25, 'Patient', 7, 'Your appointment APT0025 has been updated on 03 Apr 2025 at 09:30 to Scheduled.', 'Alert', 0, '2025-03-26 18:35:18'),
(26, 'Doctor', 6, 'Appointment APT0025 for grace wanjiku has been updated on 03 Apr 2025 at 09:30 to Scheduled.', 'Alert', 0, '2025-03-26 18:35:18'),
(27, 'Patient', 3, 'Your appointment APT0001 has been updated on 24 Feb 2025 at 10:00 to Cancelled.', 'Alert', 0, '2025-03-26 18:43:14'),
(28, 'Doctor', 1, 'Appointment APT0001 for derrick gatu has been updated on 24 Feb 2025 at 10:00 to Cancelled.', 'Alert', 0, '2025-03-26 18:43:14'),
(29, 'Patient', 3, 'Your appointment APT0001 has been updated on 24 Feb 2025 at 15:00 to Completed.', 'Alert', 0, '2025-03-26 19:07:55'),
(30, 'Doctor', 1, 'Appointment APT0001 for derrick gatu has been updated on 24 Feb 2025 at 15:00 to Completed.', 'Alert', 0, '2025-03-26 19:07:55'),
(31, 'Patient', 21, 'Your appointment APT0021 has been updated on 11 Mar 2025 at 10:29 to Completed.', 'Alert', 0, '2025-03-26 19:09:30'),
(33, 'Patient', 21, 'Your appointment APT0021 has been updated on 11 Mar 2025 at 09:29 to Completed.', 'Alert', 0, '2025-03-26 19:09:39'),
(36, 'Patient', 5, 'Your appointment APT0018 has been updated on 07 Mar 2025 at 12:48 to Completed.', 'Alert', 0, '2025-03-26 19:29:29'),
(37, 'Doctor', 1, 'Appointment APT0018 for eric wainana has been updated on 07 Mar 2025 at 12:48 to Completed.', 'Alert', 0, '2025-03-26 19:29:29'),
(38, 'Patient', 7, 'Your appointment APT0025 has been updated on 03 Apr 2025 at 10:30 to Cancelled.', 'Alert', 0, '2025-03-26 19:30:41'),
(39, 'Doctor', 6, 'Appointment APT0025 for grace wanjiku has been updated on 03 Apr 2025 at 10:30 to Cancelled.', 'Alert', 0, '2025-03-26 19:30:41'),
(40, 'Patient', 3, 'Your appointment APT0024 has been updated on 02 Apr 2025 at 14:00 to Scheduled.\n\nAdditional Note: ssup', 'Alert', 0, '2025-03-26 19:34:44'),
(41, 'Doctor', 12, 'Appointment APT0024 for derrick gatu has been updated on 02 Apr 2025 at 14:00 to Scheduled.\n\nAdditional Note: ssup', 'Alert', 0, '2025-03-26 19:34:44'),
(42, 'Patient', 3, 'Your appointment APT0024 has been updated on 02 Apr 2025 at 16:00 to Scheduled.\n\nAdditional Note: wagwan', 'Alert', 0, '2025-03-26 19:35:30'),
(45, 'Patient', 7, 'Your appointment APT0007 has been updated on 01 Apr 2025 at 10:30 to Scheduled.', 'Alert', 0, '2025-03-26 22:21:12'),
(46, 'Doctor', 1, 'Appointment APT0007 for grace wanjiku has been updated on 01 Apr 2025 at 10:30 to Scheduled.', 'Alert', 0, '2025-03-26 22:21:12'),
(47, 'Patient', 7, 'Your appointment APT0007 has been updated on 01 Apr 2025 at 14:30 to Scheduled.', 'Alert', 0, '2025-03-26 22:21:34'),
(48, 'Doctor', 1, 'Appointment APT0007 for grace wanjiku has been updated on 01 Apr 2025 at 14:30 to Scheduled.', 'Alert', 0, '2025-03-26 22:21:34'),
(49, 'Patient', 5, 'Your appointment APT0007 has been updated on 01 Apr 2025 at 14:30 to Scheduled.', 'Alert', 0, '2025-03-26 22:22:42'),
(50, 'Doctor', 1, 'Appointment APT0007 for eric wainana has been updated on 01 Apr 2025 at 14:30 to Scheduled.', 'Alert', 0, '2025-03-26 22:22:42'),
(52, 'Patient', 22, 'Your appointment APT0008 has been updated on 28 Feb 2025 at 15:00 to Completed.', 'Alert', 0, '2025-03-26 22:35:20'),
(53, 'Doctor', 2, 'Appointment APT0008 for Mercy Wambui has been updated on 28 Feb 2025 at 15:00 to Completed.', 'Alert', 0, '2025-03-26 22:35:20'),
(54, 'Patient', 22, 'Your appointment APT0026 has been created on 01 Apr 2025 at 08:36 as Scheduled.', 'Alert', 0, '2025-03-26 22:36:10'),
(55, 'Doctor', 2, 'Appointment APT0026 for Mercy Wambui has been created on 01 Apr 2025 at 08:36 as Scheduled.', 'Alert', 0, '2025-03-26 22:36:10'),
(56, 'Patient', 3, 'Your appointment APT0027 has been created on 02 Apr 2025 at 12:00 as Scheduled.\n\nAdditional Note: sick', 'Alert', 0, '2025-03-28 21:57:56'),
(57, 'Doctor', 3, 'Appointment APT0027 for derrick gatu has been created on 02 Apr 2025 at 12:00 as Scheduled.\n\nAdditional Note: sick', 'Alert', 0, '2025-03-28 21:57:56'),
(58, 'Patient', 3, 'Your appointment APT0027 has been updated on 02 Apr 2025 at 16:00 to Scheduled.\n\nAdditional Note: Updated', 'Alert', 0, '2025-03-28 22:19:48'),
(59, 'Doctor', 3, 'Appointment APT0027 for derrick gatu has been updated on 02 Apr 2025 at 16:00 to Scheduled.\n\nAdditional Note: Updated', 'Alert', 0, '2025-03-28 22:19:48'),
(60, 'Patient', 3, 'Your appointment APT0027 has been updated on 02 Apr 2025 at 16:00 to Cancelled.', 'Alert', 0, '2025-03-28 22:20:57'),
(61, 'Doctor', 3, 'Appointment APT0027 for derrick gatu has been updated on 02 Apr 2025 at 16:00 to Cancelled.', 'Alert', 0, '2025-03-28 22:20:57'),
(62, 'Patient', 3, 'Your appointment APT0027 has been updated on 02 Apr 2025 at 16:00 to Scheduled.', 'Alert', 0, '2025-03-28 22:22:14'),
(63, 'Doctor', 3, 'Appointment APT0027 for derrick gatu has been updated on 02 Apr 2025 at 16:00 to Scheduled.', 'Alert', 0, '2025-03-28 22:22:14'),
(64, 'Patient', 14, 'Your appointment APT0010 has been updated on 03 Apr 2025 at 12:00 to Scheduled.\n\nAdditional Note: Scheduled', 'Alert', 0, '2025-03-29 09:58:55'),
(65, 'Doctor', 2, 'Appointment APT0010 for Samuel Kiptoo has been updated on 03 Apr 2025 at 12:00 to Scheduled.\n\nAdditional Note: Scheduled', 'Alert', 0, '2025-03-29 09:58:55'),
(68, 'Patient', 3, 'Your appointment APT0027 has been updated on 03 Apr 2025 at 10:00 to Completed.', 'Alert', 0, '2025-04-01 13:59:30'),
(69, 'Doctor', 3, 'Appointment APT0027 for derrick gatu has been updated on 03 Apr 2025 at 10:00 to Completed.', 'Alert', 0, '2025-04-01 13:59:30'),
(70, 'Patient', 16, 'Your appointment APT0011 has been updated on 24 Apr 2025 at 11:45 to Scheduled.\n\nAdditional Note: Scheduled Appointment', 'Alert', 0, '2025-04-02 11:34:02'),
(71, 'Doctor', 1, 'Appointment APT0011 for Paul Kariuki has been updated on 24 Apr 2025 at 11:45 to Scheduled.\n\nAdditional Note: Scheduled Appointment', 'Alert', 0, '2025-04-02 11:34:02'),
(80, 'Patient', 5, 'Your appointment APT0034 has been created on 25 Apr 2025 at 17:00 as Scheduled.\n\nAdditional Note: Chest Pains', 'Alert', 0, '2025-04-02 21:56:20'),
(81, 'Doctor', 1, 'Appointment APT0034 for eric wainana has been created on 25 Apr 2025 at 17:00 as Scheduled.\n\nAdditional Note: Chest Pains', 'Alert', 0, '2025-04-02 21:56:20'),
(82, 'Patient', 22, 'Your appointment APT0035 has been created on 07 Apr 2025 at 12:00 as Scheduled.\n\nAdditional Note: Joint Pains', 'Alert', 0, '2025-04-02 21:57:50'),
(83, 'Doctor', 1, 'Appointment APT0035 for Mercy Wambui has been created on 07 Apr 2025 at 12:00 as Scheduled.\n\nAdditional Note: Joint Pains', 'Alert', 0, '2025-04-02 21:57:50'),
(85, 'Patient', 22, 'Your appointment APT0035 has been updated on 07 Apr 2025 at 14:00 to Scheduled.\n\nAdditional Note: Appointment rescheduled to 2pm', 'Alert', 0, '2025-04-03 10:19:28'),
(86, 'Doctor', 1, 'Appointment APT0035 for Mercy Wambui has been updated on 07 Apr 2025 at 14:00 to Scheduled.\n\nAdditional Note: Appointment rescheduled to 2pm', 'Alert', 0, '2025-04-03 10:19:28'),
(92, 'Patient', 3, 'Your appointment APT0028 has been updated on 09 Apr 2025 at 09:35 to Scheduled.', 'Alert', 0, '2025-04-03 10:56:14'),
(93, 'Doctor', 1, 'Appointment APT0028 for derrick gatu has been updated on 09 Apr 2025 at 09:35 to Scheduled.', 'Alert', 0, '2025-04-03 10:56:14'),
(94, 'Patient', 3, 'Your appointment APT0028 has been updated on 09 Apr 2025 at 09:35 to Cancelled.', 'Alert', 0, '2025-04-03 10:56:27'),
(95, 'Doctor', 1, 'Appointment APT0028 for derrick gatu has been updated on 09 Apr 2025 at 09:35 to Cancelled.', 'Alert', 0, '2025-04-03 10:56:27'),
(96, 'Patient', 3, 'Your appointment APT0028 has been updated on 09 Apr 2025 at 09:35 to Completed.', 'Alert', 0, '2025-04-03 10:56:58'),
(97, 'Doctor', 1, 'Appointment APT0028 for derrick gatu has been updated on 09 Apr 2025 at 09:35 to Completed.', 'Alert', 0, '2025-04-03 10:56:58'),
(98, 'Patient', 3, 'Your appointment APT0028 has been updated on 09 Apr 2025 at 09:35 to Scheduled.', 'Alert', 0, '2025-04-03 10:57:15'),
(99, 'Doctor', 1, 'Appointment APT0028 for derrick gatu has been updated on 09 Apr 2025 at 09:35 to Scheduled.', 'Alert', 0, '2025-04-03 10:57:15'),
(100, 'Patient', 3, 'Your appointment APT0028 has been updated on 09 Apr 2025 at 09:35 to Cancelled.', 'Alert', 0, '2025-04-03 11:12:42'),
(101, 'Doctor', 1, 'Appointment APT0028 for derrick gatu has been updated on 09 Apr 2025 at 09:35 to Cancelled.', 'Alert', 1, '2025-04-03 11:12:42'),
(102, 'Patient', 22, 'Your appointment APT0035 has been updated on 07 Apr 2025 at 14:00 to Completed.', 'Alert', 0, '2025-04-03 11:13:03'),
(103, 'Doctor', 1, 'Appointment APT0035 for Mercy Wambui has been updated on 07 Apr 2025 at 14:00 to Completed.', 'Alert', 1, '2025-04-03 11:13:03'),
(105, 'Patient', 42, 'Your appointment APT0036 has been scheduled on 15 Apr 2025 at 09:14.\n\nAdditional Note: Back Pains', 'Alert', 0, '2025-04-03 18:15:07'),
(106, 'Doctor', 2, 'Appointment APT0036 for Mary Njeri has been scheduled on 15 Apr 2025 at 09:14.\n\nAdditional Note: Back Pains', 'Alert', 0, '2025-04-03 18:15:07'),
(107, 'Patient', 3, 'Your appointment APT0028 has been updated on 09 Apr 2025 at 09:35 to Scheduled.', 'Alert', 0, '2025-04-06 13:45:29'),
(108, 'Doctor', 1, 'Appointment APT0028 for derrick gatu has been updated on 09 Apr 2025 at 09:35 to Scheduled.', 'Alert', 1, '2025-04-06 13:45:29'),
(109, 'Patient', 42, 'Your appointment APT0037 has been scheduled on 06 Apr 2025 at 22:20.\n\nAdditional Note: Headache', 'Alert', 0, '2025-04-06 19:21:22'),
(110, 'Doctor', 2, 'Appointment APT0037 for Mary Njeri has been scheduled on 06 Apr 2025 at 22:20.\n\nAdditional Note: Headache', 'Alert', 0, '2025-04-06 19:21:22'),
(111, 'Patient', 41, 'Your appointment schedule (APT0031) has been updated. New date: 2025-04-30, Time: 02:40 PM - 03:30 PM', 'Alert', 0, '2025-04-07 11:14:35'),
(112, 'Patient', 3, 'Your invoice #INV-01-001-2025 has been updated by Dr. allan thiong\'o. New amount: Kshs 1,000.50, Status: Paid.', 'Alert', 0, '2025-04-08 07:28:43'),
(113, 'Patient', 3, 'Your invoice #INV-01-001-2025 has been updated by Dr. allan thiong\'o. New amount: Kshs 1,000.50, Status: Pending.', 'Alert', 0, '2025-04-08 07:28:59'),
(114, 'Patient', 3, 'Your invoice #INV-01-001-2025 has been updated by Dr. allan thiong\'o. New amount: Kshs 1,000.50, Status: Paid.', 'Alert', 0, '2025-04-08 07:50:16'),
(116, 'Patient', 5, 'A new invoice #INV-01-003-2025 has been created by Dr. allan thiong\'o. Amount: Kshs 2,000.00, Status: Pending.', 'Alert', 0, '2025-04-08 08:30:01'),
(117, 'Patient', 22, 'A new invoice #INV-01-004-2025 has been created by Dr. allan thiong\'o. Amount: Kshs 3,000.50, Status: Failed.', 'Alert', 0, '2025-04-08 08:33:45'),
(118, 'Patient', 5, 'A new invoice #INV-01-005-2025 has been created by Dr. allan thiong\'o. Amount: Kshs 3,999.99, Status: Pending.', 'Alert', 0, '2025-04-08 14:55:00'),
(119, 'Patient', 5, 'A new invoice #INV-01-006-2025 has been created by Dr. allan thiong\'o. Amount: Kshs 10,000.00, Status: Pending.', 'Alert', 0, '2025-04-08 14:55:53'),
(120, 'Patient', 19, 'Your appointment APT0043 has been scheduled on 08 Apr 2025 at 11:15.\n\nAdditional Note: Booked', 'Alert', 0, '2025-04-08 18:15:09'),
(121, 'Doctor', 2, 'Appointment APT0043 for john kibera has been scheduled on 08 Apr 2025 at 11:15.\n\nAdditional Note: Booked', 'Alert', 0, '2025-04-08 18:15:09'),
(123, 'Patient', 11, 'Your appointment APT0044 has been scheduled on 08 Apr 2025 at 13:17.\n\nAdditional Note: Appointment', 'Alert', 0, '2025-04-08 18:18:05'),
(124, 'Doctor', 2, 'Appointment APT0044 for eric wainana has been scheduled on 08 Apr 2025 at 13:17.\n\nAdditional Note: Appointment', 'Alert', 0, '2025-04-08 18:18:05'),
(125, 'Patient', 5, 'A new invoice #INV-01-008-2025 has been created by Dr. allan thiong\'o. Amount: Kshs 10,000.00, Status: Pending.', 'Alert', 0, '2025-04-08 18:18:47'),
(126, 'Patient', 5, 'A new invoice #INV-01-009-2025 has been created by Dr. allan thiong\'o. Amount: Kshs 5,000.00, Status: Pending.', 'Alert', 0, '2025-04-08 19:01:48');

-- --------------------------------------------------------

--
-- Table structure for table `nurses`
--

CREATE TABLE `nurses` (
  `id` int(11) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `staff_id` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nurses`
--

INSERT INTO `nurses` (`id`, `profile_pic`, `staff_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `address`, `email`, `contact_number`, `created_at`) VALUES
(1, '\"C:\\xampp\\htdocs\\FINALPROJECT2025\\img\\nursepic1.jpg\"', 'NR-01-001-2025', 'karen', 'kibet', '2000-02-04', 'Female', '0100, nairobi', 'karenkibet@gmail.com', '079876500', '2025-02-06 14:26:52'),
(2, '\"C:\\xampp\\htdocs\\FINALPROJECT2025\\img\\Dr.Dre.jpg\"', 'NR-01-002-2025', 'jimmy', 'mutheu', '1987-06-24', 'Male', '0110, kiambu tharakanithi', 'jimmymutheu@gmail.com', '0798564320', '2025-02-07 16:31:36');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `insurance` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `contact_number`, `email`, `address`, `medical_history`, `insurance`, `created_at`) VALUES
(3, 'derrick', 'gatu', '2003-01-04', 'Male', '0768686329', 'derrickgatuo3@gmail.com', '0100, nairobi', NULL, 'APA Insurance', '2025-02-03 20:11:45'),
(5, 'eric', 'wainana', '2001-01-28', 'Male', '0754321980', 'ericwainana@gmail.com', '410, kiambu', NULL, 'Jubilee Insurance', '2025-02-12 18:28:38'),
(6, 'peter', 'maina', '1985-03-15', 'Male', '254712345678', 'peter.maina@gmail.com', 'Nairobi', 'Hypertension', 'AAR Health Services', '2025-03-18 13:52:50'),
(7, 'grace', 'wanjiku', '1992-07-22', 'Female', '254723456789', 'grace.wanjiku@yahoo.com', 'Kisumu', 'Asthma', 'NHIF', '2025-03-18 14:00:32'),
(8, 'James', 'Kamau', '1978-11-30', 'Male', '254734567890', 'james.kamau@hotmail.com', 'Nakuru', 'Diabetes', 'BRITAM', '2025-02-22 13:04:15'),
(9, 'Mary', 'Njeri', '1990-04-10', 'Female', '254745678901', 'mary.njeri@gmail.com', 'Mombasa', 'None', 'APA Insurance', '2025-02-22 13:04:15'),
(11, 'Esther', 'Achieng', '1995-12-12', 'Female', '254767890123', 'esther.achieng@gmail.com', 'Kakamega', 'Allergies', 'Jubilee Insurance', '2025-02-22 13:04:15'),
(12, 'Joseph', 'Mwangi', '1983-06-25', 'Male', '254778901234', 'joseph.mwangi@yahoo.com', 'Thika', 'High cholesterol', 'AAR Health Services', '2025-02-22 13:04:15'),
(13, 'Lucy', 'Muthoni', '1993-02-18', 'Female', '254789012345', 'lucy.muthoni@gmail.com', 'Nyeri', 'None', 'NHIF', '2025-02-22 13:04:15'),
(14, 'Samuel', 'Kiptoo', '1987-08-09', 'Male', '254790123456', 'samuel.kiptoo@hotmail.com', 'Kericho', 'Broken leg (past)', 'BRITAM', '2025-02-22 13:04:15'),
(15, 'Faith', 'Chebet', '1996-10-03', 'Female', '254701234567', 'faith.chebet@gmail.com', 'Nairobi', 'Migraines', 'APA Insurance', '2025-02-22 13:04:15'),
(16, 'Paul', 'Kariuki', '1980-05-20', 'Male', '254712987654', 'paul.kariuki@gmail.com', 'Nairobi', 'Arthritis', 'CIC Insurance', '2024-07-15 06:30:00'),
(17, 'Rose', 'Atieno', '1994-09-08', 'Female', '254723876543', 'rose.atieno@yahoo.com', 'Kisumu', 'Thyroid issues', 'Jubilee Insurance', '2024-09-10 11:15:00'),
(18, 'Isaac', 'Njoroge', '1986-12-01', 'Male', '254734765432', 'isaac.njoroge@hotmail.com', 'Thika', 'Pneumonia (past)', 'AAR Health Services', '2024-10-22 08:45:00'),
(19, 'Jane', 'Mbithe', '1991-03-17', 'Female', '254745654321', 'jane.mbithe@gmail.com', 'Mombasa', 'Anemia', 'NHIF', '2024-12-05 13:00:00'),
(20, 'Simon', 'Kibet', '1989-11-11', 'Male', '254756543210', 'simon.kibet@outlook.com', 'Eldoret', 'Gastritis', 'BRITAM', '2025-01-18 05:20:00'),
(21, 'John', 'Karanja', '1990-03-15', 'Male', '254712345678', 'john.karanja@gmail.com', 'Nairobi', NULL, 'APA Insurance', '2024-06-10 06:00:00'),
(22, 'Mercy', 'Wambui', '1995-08-22', 'Female', '254723456789', 'mercy.wambui@yahoo.com', 'Kisumu', NULL, 'CIC Insurance', '2024-09-15 11:30:00'),
(23, 'David', 'Ochieng', '1988-11-05', 'Male', '254734567890', 'david.ochieng@hotmail.com', 'Mombasa', NULL, 'Jubilee Insurance', '2024-12-20 08:15:00'),
(24, 'Esther', 'Atieno', '1992-04-18', 'Female', '254745678901', 'esther.atieno@gmail.com', 'Nakuru', NULL, 'AAR Health Services', '2025-01-05 13:45:00'),
(26, 'john', 'kibera', '1968-01-31', 'Male', '0712760039', 'johnkibera@gmail.com', '011, tharakanithi', NULL, 'NHIF', '2025-03-17 21:54:36'),
(28, 'mark', 'nguyo', '1991-06-06', 'Male', '0754555418', 'marknguyo@gmial.com', '65, nanyuki', NULL, 'NHIF', '2025-03-17 23:02:38'),
(32, 'richard', 'mathenge', '1976-03-07', 'Male', '0793847561', 'richardmathenge@gmail.com', 'nairobi', NULL, NULL, '2025-03-18 13:18:55'),
(33, 'konda', 'mnono', '1996-04-02', 'Male', '0743433767', 'kondamnono@gmail.com', 'Kakamega', NULL, NULL, '2025-03-18 21:17:07'),
(34, 'first_name', 'last_name', '0000-00-00', '', 'contact_number', 'email', 'address', 'medical_history', 'insurance', '0000-00-00 00:00:00'),
(35, 'Joseph', 'wainana', '0000-00-00', 'Male', '768686329', 'josephwainana@gmail.com', 'Nairobi', 'Tuberculosis', 'Shif', '0000-00-00 00:00:00'),
(36, 'Simon', 'Maina', '0000-00-00', 'Female', '754321980', 'simonmaina@gmail.com', 'Mombasa', 'Malaria', 'Shif', '0000-00-00 00:00:00'),
(37, 'Esther', 'Muthoni', '0000-00-00', 'Female', '712345678', 'esthermuthoni@gmail.com', 'Kisumu', 'Malaria', 'Britam', '0000-00-00 00:00:00'),
(38, 'Paul', 'Chebet', '0000-00-00', 'Male', '723456789', 'paulchebet@gmail.com', 'Kajiado', 'Asthma', 'CIC Insurance.', '0000-00-00 00:00:00'),
(39, 'Joseph', 'Njoroge', '0000-00-00', 'Female', '734567890', 'josephnjoroge@gmail.com', 'Machakos', 'Diabetes', 'AAR Insurance', '0000-00-00 00:00:00'),
(40, 'Mary', 'Muthoni', '0000-00-00', 'Male', '745678901', 'marymuthoni@gmail.com', 'Kiambu', 'Heart Disease', 'Heritage Insurance', '0000-00-00 00:00:00'),
(41, 'Esther', 'Mbithe', '0000-00-00', 'Female', '756789012', 'esthermbithe@gmail.com', 'Kitusuri', 'Tuberculosis', 'Cannon Insurance', '0000-00-00 00:00:00'),
(42, 'Samuel', 'Njoroge', '0000-00-00', 'Female', '767890123', 'samuelnjoroge@gmail.com', 'Thidingwa', 'Heart Disease', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(43, 'Eric', 'Wanjiku', '0000-00-00', 'Female', '778901234', 'ericwanjiku@gmail.com', 'Uplands', 'Cancer', 'APA Insurance', '0000-00-00 00:00:00'),
(44, 'Esther', 'Chebet', '0000-00-00', 'Female', '789012345', 'estherchebet@gmail.com', 'Kanairo', 'Malaria', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(45, 'David', 'Atieno', '0000-00-00', 'Male', '790123456', 'davidatieno@gmail.com', 'Muchatha', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(46, 'Rose', 'Achieng', '0000-00-00', 'Male', '701234567', 'roseachieng@gmail.com', 'Kinagop', 'Kidney Failure', 'Shif', '0000-00-00 00:00:00'),
(47, 'James', 'Mbugua', '0000-00-00', 'Male', '712987654', 'jamesmbugua@gmail.com', 'Alfaker', 'HIV/AIDS', 'Shif', '0000-00-00 00:00:00'),
(48, 'Mercy', 'Atieno', '0000-00-00', 'Male', '723876543', 'mercyatieno@gmail.com', 'Nairobi', 'Heart Disease', 'Britam', '0000-00-00 00:00:00'),
(49, 'Derrick', 'Kiptoo', '0000-00-00', 'Male', '734765432', 'derrickkiptoo@gmail.com', 'Mombasa', 'Cancer', 'CIC Insurance.', '0000-00-00 00:00:00'),
(50, 'Joseph', 'Wambui', '0000-00-00', 'Male', '745654321', 'josephwambui@gmail.com', 'Kisumu', 'Tuberculosis', 'AAR Insurance', '0000-00-00 00:00:00'),
(51, 'Grace', 'Njoroge', '0000-00-00', 'Male', '756543210', 'gracenjoroge@gmail.com', 'Kajiado', 'Typhoid', 'Heritage Insurance', '0000-00-00 00:00:00'),
(52, 'Grace', 'Kiptoo', '0000-00-00', 'Female', '712345678', 'gracekiptoo@gmail.com', 'Machakos', 'Asthma', 'Cannon Insurance', '0000-00-00 00:00:00'),
(53, 'Mercy', 'gatu', '0000-00-00', 'Male', '723456789', 'mercygatu@gmail.com', 'Kiambu', 'Stroke', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(55, 'James', 'Njeri', '0000-00-00', 'Male', '745678901', 'jamesnjeri@gmail.com', 'Thidingwa', 'Heart Disease', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(56, 'David', 'wainana', '1995-04-01', 'Male', '756789012', 'davidwainana@gmail.com', 'Uplands', 'Heart Disease', 'Shif', '2024-10-09 15:19:58'),
(58, 'Paul', 'Wanjiku', '0000-00-00', 'Female', '788236251', 'paulwanjiku@gmail.com', 'Muchatha', 'Heart Disease', 'Shif', '0000-00-00 00:00:00'),
(59, 'John', 'wainana', '0000-00-00', 'Male', '792283015', 'johnwainana@gmail.com', 'Kinagop', 'Asthma', 'Britam', '0000-00-00 00:00:00'),
(60, 'Derrick', 'Kibet', '0000-00-00', 'Male', '793004349', 'derrickkibet@gmail.com', 'Alfaker', 'Stroke', 'CIC Insurance.', '0000-00-00 00:00:00'),
(61, 'Joseph', 'Kibet', '0000-00-00', 'Male', '782470197', 'josephkibet@gmail.com', 'Nairobi', 'Diabetes', 'AAR Insurance', '0000-00-00 00:00:00'),
(62, 'Esther', 'Ochieng', '0000-00-00', 'Male', '755812029', 'estherochieng@gmail.com', 'Mombasa', 'Diabetes', 'Heritage Insurance', '0000-00-00 00:00:00'),
(63, 'Samuel', 'Wanjiku', '0000-00-00', 'Male', '793671474', 'samuelwanjiku@gmail.com', 'Kisumu', 'Malaria', 'Cannon Insurance', '0000-00-00 00:00:00'),
(64, 'Joseph', 'Wanjiku', '0000-00-00', 'Female', '716436846', 'josephwanjiku@gmail.com', 'Kajiado', 'Asthma', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(65, 'Peter', 'Otieno', '0000-00-00', 'Female', '749197071', 'peterotieno@gmail.com', 'Machakos', 'Typhoid', 'APA Insurance', '0000-00-00 00:00:00'),
(66, 'Peter', 'Achieng', '0000-00-00', 'Male', '735364115', 'peterachieng@gmail.com', 'Kiambu', 'HIV/AIDS', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(67, 'Eric', 'Ochieng', '0000-00-00', 'Female', '755757203', 'ericochieng@gmail.com', 'Kitusuri', 'Typhoid', 'Shif', '0000-00-00 00:00:00'),
(68, 'Mary', 'Maina', '0000-00-00', 'Female', '766796611', 'marymaina@gmail.com', 'Thidingwa', 'Stroke', 'Shif', '0000-00-00 00:00:00'),
(69, 'Simon', 'Otieno', '0000-00-00', 'Male', '728844903', 'simonotieno@gmail.com', 'Uplands', 'Stroke', 'Shif', '0000-00-00 00:00:00'),
(70, 'James', 'Kibet', '0000-00-00', 'Female', '753111812', 'jameskibet@gmail.com', 'Kanairo', 'Diabetes', 'Britam', '0000-00-00 00:00:00'),
(71, 'Peter', 'Kibet', '0000-00-00', 'Male', '779579558', 'peterkibet@gmail.com', 'Muchatha', 'Tuberculosis', 'CIC Insurance.', '0000-00-00 00:00:00'),
(72, 'derrick', 'Wanjiku', '0000-00-00', 'Male', '748351018', 'derrickwanjiku@gmail.com', 'Kinagop', 'Typhoid', 'AAR Insurance', '0000-00-00 00:00:00'),
(73, 'David', 'Maina', '0000-00-00', 'Female', '741525324', 'davidmaina@gmail.com', 'Alfaker', 'Kidney Failure', 'Heritage Insurance', '0000-00-00 00:00:00'),
(74, 'Faith', 'Atieno', '0000-00-00', 'Female', '716628773', 'faithatieno@gmail.com', 'Nairobi', 'Liver Disease', 'Cannon Insurance', '0000-00-00 00:00:00'),
(75, 'Esther', 'Kibet', '0000-00-00', 'Male', '755136396', 'estherkibet@gmail.com', 'Mombasa', 'COVID-19', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(76, 'Joseph', 'Maina', '0000-00-00', 'Male', '719000184', 'josephmaina@gmail.com', 'Kisumu', 'Kidney Failure', 'APA Insurance', '0000-00-00 00:00:00'),
(77, 'Lucy', 'Wanjiku', '0000-00-00', 'Male', '749508618', 'lucywanjiku@gmail.com', 'Kajiado', 'Cancer', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(78, 'Paul', 'Achieng', '0000-00-00', 'Female', '780522328', 'paulachieng@gmail.com', 'Machakos', 'Pneumonia', 'Shif', '0000-00-00 00:00:00'),
(79, 'Isaac', 'Atieno', '0000-00-00', 'Male', '728004080', 'isaacatieno@gmail.com', 'Kiambu', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(80, 'Eric', 'Kiptoo', '0000-00-00', 'Female', '732805022', 'erickiptoo@gmail.com', 'Kitusuri', 'HIV/AIDS', 'Shif', '0000-00-00 00:00:00'),
(81, 'Rose', 'Mwangi', '0000-00-00', 'Male', '787046515', 'rosemwangi@gmail.com', 'Thidingwa', 'Kidney Failure', 'Britam', '0000-00-00 00:00:00'),
(82, 'James', 'Karanja', '0000-00-00', 'Female', '740632001', 'jameskaranja@gmail.com', 'Uplands', 'HIV/AIDS', 'CIC Insurance.', '0000-00-00 00:00:00'),
(84, 'Rose', 'Otieno', '0000-00-00', 'Female', '785359287', 'roseotieno@gmail.com', 'Muchatha', 'Liver Disease', 'Heritage Insurance', '0000-00-00 00:00:00'),
(85, 'Esther', 'Kariuki', '0000-00-00', 'Female', '747171637', 'estherkariuki@gmail.com', 'Kinagop', 'COVID-19', 'Cannon Insurance', '0000-00-00 00:00:00'),
(86, 'Grace', 'Kibet', '0000-00-00', 'Female', '793245075', 'gracekibet@gmail.com', 'Alfaker', 'Hypertension', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(88, 'Mary', 'Wambui', '0000-00-00', 'Male', '767350685', 'marywambui@gmail.com', 'Mombasa', 'Malaria', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(89, 'Peter', 'Muthoni', '0000-00-00', 'Female', '729172352', 'petermuthoni@gmail.com', 'Kisumu', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(90, 'Mary', 'Mbugua', '0000-00-00', 'Male', '794760812', 'marymbugua@gmail.com', 'Kajiado', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(94, 'Peter', 'gatu', '0000-00-00', 'Male', '729306560', 'petergatu@gmail.com', 'Thidingwa', 'Kidney Failure', 'AAR Insurance', '0000-00-00 00:00:00'),
(95, 'Eric', 'Karanja', '0000-00-00', 'Female', '792518685', 'erickaranja@gmail.com', 'Uplands', 'Tuberculosis', 'Heritage Insurance', '0000-00-00 00:00:00'),
(96, 'Jane', 'Kiptoo', '0000-00-00', 'Male', '749580601', 'janekiptoo@gmail.com', 'Kanairo', 'Cancer', 'Cannon Insurance', '0000-00-00 00:00:00'),
(97, 'David', 'Wanjiku', '0000-00-00', 'Male', '743674095', 'davidwanjiku@gmail.com', 'Muchatha', 'Pneumonia', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(98, 'John', 'Ochieng', '0000-00-00', 'Female', '730104576', 'johnochieng@gmail.com', 'Kinagop', 'Heart Disease', 'APA Insurance', '0000-00-00 00:00:00'),
(100, 'Mercy', 'Otieno', '0000-00-00', 'Male', '753854101', 'mercyotieno@gmail.com', 'Nairobi', 'Kidney Failure', 'Shif', '0000-00-00 00:00:00'),
(102, 'Esther', 'Achieng', '0000-00-00', 'Male', '788478956', 'estherachieng@gmail.com', 'Kisumu', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(104, 'Samuel', 'Maina', '0000-00-00', 'Male', '777182463', 'samuelmaina@gmail.com', 'Machakos', 'Typhoid', 'CIC Insurance.', '0000-00-00 00:00:00'),
(105, 'Simon', 'Njeri', '0000-00-00', 'Female', '795100981', 'simonnjeri@gmail.com', 'Kiambu', 'Tuberculosis', 'AAR Insurance', '0000-00-00 00:00:00'),
(106, 'Isaac', 'Kiptoo', '0000-00-00', 'Male', '773454121', 'isaackiptoo@gmail.com', 'Kitusuri', 'HIV/AIDS', 'Heritage Insurance', '0000-00-00 00:00:00'),
(107, 'Simon', 'Wanjiku', '0000-00-00', 'Female', '775589086', 'simonwanjiku@gmail.com', 'Thidingwa', 'Kidney Failure', 'Cannon Insurance', '0000-00-00 00:00:00'),
(109, 'Rose', 'Muthoni', '0000-00-00', 'Female', '752762662', 'rosemuthoni@gmail.com', 'Kanairo', 'Diabetes', 'APA Insurance', '0000-00-00 00:00:00'),
(110, 'Esther', 'Otieno', '0000-00-00', 'Female', '769118017', 'estherotieno@gmail.com', 'Muchatha', 'Malaria', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(111, 'Mary', 'Ochieng', '0000-00-00', 'Female', '757379480', 'maryochieng@gmail.com', 'Kinagop', 'Cancer', 'Shif', '0000-00-00 00:00:00'),
(112, 'Mary', 'Atieno', '0000-00-00', 'Male', '730542322', 'maryatieno@gmail.com', 'Alfaker', 'Diabetes', 'Shif', '0000-00-00 00:00:00'),
(113, 'James', 'Wanjiku', '0000-00-00', 'Male', '797194619', 'jameswanjiku@gmail.com', 'Nairobi', 'Malaria', 'Shif', '0000-00-00 00:00:00'),
(114, 'Rose', 'gatu', '0000-00-00', 'Male', '776112936', 'rosegatu@gmail.com', 'Mombasa', 'Malaria', 'Britam', '0000-00-00 00:00:00'),
(115, 'Mercy', 'Kariuki', '0000-00-00', 'Male', '738675054', 'mercykariuki@gmail.com', 'Kisumu', 'Tuberculosis', 'CIC Insurance.', '0000-00-00 00:00:00'),
(116, 'Mary', 'Njeri', '0000-00-00', 'Female', '724044910', 'marynjeri@gmail.com', 'Kajiado', 'Tuberculosis', 'AAR Insurance', '0000-00-00 00:00:00'),
(118, 'Rose', 'Wanjiku', '0000-00-00', 'Male', '754934909', 'rosewanjiku@gmail.com', 'Kiambu', 'Diabetes', 'Cannon Insurance', '0000-00-00 00:00:00'),
(119, 'Grace', 'Ochieng', '0000-00-00', 'Male', '780053227', 'graceochieng@gmail.com', 'Kitusuri', 'Diabetes', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(120, 'James', 'Njoroge', '0000-00-00', 'Male', '726518062', 'jamesnjoroge@gmail.com', 'Thidingwa', 'Cancer', 'APA Insurance', '0000-00-00 00:00:00'),
(121, 'John', 'Otieno', '0000-00-00', 'Male', '760682456', 'johnotieno@gmail.com', 'Uplands', 'Tuberculosis', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(122, 'Peter', 'Kiptoo', '0000-00-00', 'Male', '742279059', 'peterkiptoo@gmail.com', 'Kanairo', 'Kidney Failure', 'Shif', '0000-00-00 00:00:00'),
(123, 'Derrick', 'Njoroge', '0000-00-00', 'Male', '797116310', 'derricknjoroge@gmail.com', 'Muchatha', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(125, 'Peter', 'Atieno', '0000-00-00', 'Female', '725528655', 'peteratieno@gmail.com', 'Alfaker', 'Cancer', 'Britam', '0000-00-00 00:00:00'),
(126, 'Esther', 'Kiptoo', '0000-00-00', 'Male', '728183411', 'estherkiptoo@gmail.com', 'Nairobi', 'Malaria', 'CIC Insurance.', '0000-00-00 00:00:00'),
(128, 'Simon', 'gatu', '0000-00-00', 'Male', '735893636', 'simongatu@gmail.com', 'Kisumu', 'Stroke', 'Heritage Insurance', '0000-00-00 00:00:00'),
(129, 'James', 'Atieno', '0000-00-00', 'Male', '773987152', 'jamesatieno@gmail.com', 'Kajiado', 'Asthma', 'Cannon Insurance', '0000-00-00 00:00:00'),
(130, 'Esther', 'Njeri', '0000-00-00', 'Male', '773935662', 'esthernjeri@gmail.com', 'Machakos', 'HIV/AIDS', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(131, 'James', 'Otieno', '0000-00-00', 'Male', '787697546', 'jamesotieno@gmail.com', 'Kiambu', 'Malaria', 'APA Insurance', '0000-00-00 00:00:00'),
(133, 'Samuel', 'Ochieng', '0000-00-00', 'Male', '747544617', 'samuelochieng@gmail.com', 'Thidingwa', 'Stroke', 'Shif', '0000-00-00 00:00:00'),
(135, 'John', 'Kariuki', '0000-00-00', 'Female', '736188554', 'johnkariuki@gmail.com', 'Kanairo', 'COVID-19', 'Shif', '0000-00-00 00:00:00'),
(136, 'John', 'Kibet', '0000-00-00', 'Male', '782184707', 'johnkibet@gmail.com', 'Muchatha', 'Malaria', 'Britam', '0000-00-00 00:00:00'),
(138, 'Isaac', 'Njeri', '0000-00-00', 'Male', '759717886', 'isaacnjeri@gmail.com', 'Alfaker', 'Hypertension', 'AAR Insurance', '0000-00-00 00:00:00'),
(140, 'John', 'Kiptoo', '0000-00-00', 'Female', '792410040', 'johnkiptoo@gmail.com', 'Mombasa', 'Tuberculosis', 'Cannon Insurance', '0000-00-00 00:00:00'),
(141, 'Isaac', 'Mbugua', '0000-00-00', 'Female', '763006555', 'isaacmbugua@gmail.com', 'Kisumu', 'Typhoid', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(142, 'Simon', 'Atieno', '0000-00-00', 'Female', '724680140', 'simonatieno@gmail.com', 'Kajiado', 'Asthma', 'APA Insurance', '0000-00-00 00:00:00'),
(144, 'Jane', 'Achieng', '0000-00-00', 'Male', '768971645', 'janeachieng@gmail.com', 'Kiambu', 'Cancer', 'Shif', '0000-00-00 00:00:00'),
(145, 'David', 'gatu', '0000-00-00', 'Male', '751365203', 'davidgatu@gmail.com', 'Kitusuri', 'Tuberculosis', 'Shif', '0000-00-00 00:00:00'),
(146, 'Eric', 'Maina', '0000-00-00', 'Female', '727719840', 'ericmaina@gmail.com', 'Thidingwa', 'Stroke', 'Shif', '0000-00-00 00:00:00'),
(147, 'Mercy', 'Njeri', '0000-00-00', 'Male', '792874537', 'mercynjeri@gmail.com', 'Uplands', 'Asthma', 'Britam', '0000-00-00 00:00:00'),
(149, 'Rose', 'Mbugua', '0000-00-00', 'Female', '772568764', 'rosembugua@gmail.com', 'Muchatha', 'Stroke', 'AAR Insurance', '0000-00-00 00:00:00'),
(151, 'Lucy', 'Muthoni', '0000-00-00', 'Female', '766408149', 'lucymuthoni@gmail.com', 'Alfaker', 'Heart Disease', 'Cannon Insurance', '0000-00-00 00:00:00'),
(152, 'James', 'wainana', '0000-00-00', 'Male', '735097123', 'jameswainana@gmail.com', 'Nairobi', 'Diabetes', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(153, 'Lucy', 'Kariuki', '0000-00-00', 'Male', '722743977', 'lucykariuki@gmail.com', 'Mombasa', 'Tuberculosis', 'APA Insurance', '0000-00-00 00:00:00'),
(155, 'Isaac', 'Maina', '0000-00-00', 'Male', '768382794', 'isaacmaina@gmail.com', 'Kajiado', 'Dengue', 'Shif', '0000-00-00 00:00:00'),
(157, 'Jane', 'Mbugua', '0000-00-00', 'Female', '734556522', 'janembugua@gmail.com', 'Kiambu', 'Cancer', 'Shif', '0000-00-00 00:00:00'),
(158, 'Peter', 'Njoroge', '0000-00-00', 'Male', '745661156', 'peternjoroge@gmail.com', 'Kitusuri', 'Pneumonia', 'Britam', '0000-00-00 00:00:00'),
(159, 'Isaac', 'Muthoni', '0000-00-00', 'Male', '781485130', 'isaacmuthoni@gmail.com', 'Thidingwa', 'Hypertension', 'CIC Insurance.', '0000-00-00 00:00:00'),
(161, 'Mary', 'Wanjiku', '0000-00-00', 'Male', '728798657', 'marywanjiku@gmail.com', 'Kanairo', 'Heart Disease', 'Heritage Insurance', '0000-00-00 00:00:00'),
(163, 'Paul', 'Kariuki', '0000-00-00', 'Male', '751969155', 'paulkariuki@gmail.com', 'Kinagop', 'COVID-19', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(164, 'John', 'Chebet', '0000-00-00', 'Male', '714968421', 'johnchebet@gmail.com', 'Alfaker', 'Asthma', 'APA Insurance', '0000-00-00 00:00:00'),
(165, 'Paul', 'Atieno', '0000-00-00', 'Male', '745802719', 'paulatieno@gmail.com', 'Nairobi', 'Asthma', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(167, 'Isaac', 'Wambui', '0000-00-00', 'Male', '777326843', 'isaacwambui@gmail.com', 'Kisumu', 'Cancer', 'Shif', '0000-00-00 00:00:00'),
(169, 'Mary', 'Chebet', '0000-00-00', 'Male', '725072313', 'marychebet@gmail.com', 'Machakos', 'Diabetes', 'Britam', '0000-00-00 00:00:00'),
(171, 'Samuel', 'Atieno', '0000-00-00', 'Male', '750256947', 'samuelatieno@gmail.com', 'Kitusuri', 'Hypertension', 'AAR Insurance', '0000-00-00 00:00:00'),
(172, 'Jane', 'Wambui', '0000-00-00', 'Male', '748937963', 'janewambui@gmail.com', 'Thidingwa', 'Malaria', 'Heritage Insurance', '0000-00-00 00:00:00'),
(173, 'John', 'Achieng', '0000-00-00', 'Female', '730400833', 'johnachieng@gmail.com', 'Uplands', 'HIV/AIDS', 'Cannon Insurance', '0000-00-00 00:00:00'),
(174, 'John', 'Wanjiku', '0000-00-00', 'Male', '755648427', 'johnwanjiku@gmail.com', 'Kanairo', 'Asthma', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(175, 'Lucy', 'Karanja', '0000-00-00', 'Female', '716823013', 'lucykaranja@gmail.com', 'Muchatha', 'Diabetes', 'APA Insurance', '0000-00-00 00:00:00'),
(177, 'Lucy', 'wainana', '0000-00-00', 'Male', '741692868', 'lucywainana@gmail.com', 'Alfaker', 'Tuberculosis', 'Shif', '0000-00-00 00:00:00'),
(178, 'Faith', 'Mbugua', '0000-00-00', 'Male', '750075252', 'faithmbugua@gmail.com', 'Nairobi', 'Malaria', 'Shif', '0000-00-00 00:00:00'),
(179, 'Grace', 'Maina', '0000-00-00', 'Female', '760670656', 'gracemaina@gmail.com', 'Mombasa', 'Tuberculosis', 'Shif', '0000-00-00 00:00:00'),
(180, 'Isaac', 'Otieno', '0000-00-00', 'Female', '779107295', 'isaacotieno@gmail.com', 'Kisumu', 'Stroke', 'Britam', '0000-00-00 00:00:00'),
(181, 'Peter', 'Ochieng', '0000-00-00', 'Male', '725535000', 'peterochieng@gmail.com', 'Kajiado', 'Asthma', 'CIC Insurance.', '0000-00-00 00:00:00'),
(183, 'Esther', 'Mwangi', '0000-00-00', 'Male', '791223273', 'esthermwangi@gmail.com', 'Kiambu', 'Heart Disease', 'Heritage Insurance', '0000-00-00 00:00:00'),
(184, 'Peter', 'Mbugua', '0000-00-00', 'Female', '735182288', 'petermbugua@gmail.com', 'Kitusuri', 'Pneumonia', 'Cannon Insurance', '0000-00-00 00:00:00'),
(185, 'Faith', 'Muthoni', '0000-00-00', 'Female', '791216058', 'faithmuthoni@gmail.com', 'Thidingwa', 'Malaria', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(186, 'John', 'Kamau', '0000-00-00', 'Male', '750697303', 'johnkamau@gmail.com', 'Uplands', 'Malaria', 'APA Insurance', '0000-00-00 00:00:00'),
(187, 'Grace', 'Njeri', '0000-00-00', 'Female', '794558580', 'gracenjeri@gmail.com', 'Kanairo', 'Pneumonia', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(188, 'Grace', 'Otieno', '0000-00-00', 'Female', '780395245', 'graceotieno@gmail.com', 'Muchatha', 'HIV/AIDS', 'Shif', '0000-00-00 00:00:00'),
(190, 'David', 'Njeri', '0000-00-00', 'Female', '735689718', 'davidnjeri@gmail.com', 'Alfaker', 'Typhoid', 'Shif', '0000-00-00 00:00:00'),
(192, 'derrick', 'Muthoni', '0000-00-00', 'Male', '759811030', 'derrickmuthoni@gmail.com', 'Mombasa', 'HIV/AIDS', 'CIC Insurance.', '0000-00-00 00:00:00'),
(193, 'David', 'Kariuki', '0000-00-00', 'Male', '750773954', 'davidkariuki@gmail.com', 'Kisumu', 'Stroke', 'AAR Insurance', '0000-00-00 00:00:00'),
(194, 'Grace', 'Mwangi', '0000-00-00', 'Female', '759371456', 'gracemwangi@gmail.com', 'Kajiado', 'Diabetes', 'Heritage Insurance', '0000-00-00 00:00:00'),
(195, 'Mercy', 'Maina', '0000-00-00', 'Female', '766178085', 'mercymaina@gmail.com', 'Machakos', 'Asthma', 'Cannon Insurance', '0000-00-00 00:00:00'),
(198, 'Peter', 'Kamau', '0000-00-00', 'Male', '776180252', 'peterkamau@gmail.com', 'Thidingwa', 'Stroke', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(200, 'John', 'Maina', '0000-00-00', 'Female', '762253611', 'johnmaina@gmail.com', 'Kanairo', 'Pneumonia', 'Shif', '0000-00-00 00:00:00'),
(201, 'Peter', 'Karanja', '0000-00-00', 'Female', '796669022', 'peterkaranja@gmail.com', 'Muchatha', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(202, 'Joseph', 'Mwangi', '0000-00-00', 'Female', '722173863', 'josephmwangi@gmail.com', 'Kinagop', 'Malaria', 'Britam', '0000-00-00 00:00:00'),
(204, 'Esther', 'Wanjiku', '0000-00-00', 'Female', '770315394', 'estherwanjiku@gmail.com', 'Nairobi', 'Heart Disease', 'AAR Insurance', '0000-00-00 00:00:00'),
(205, 'Esther', 'wainana', '0000-00-00', 'Female', '750933299', 'estherwainana@gmail.com', 'Mombasa', 'Malaria', 'Heritage Insurance', '0000-00-00 00:00:00'),
(206, 'David', 'Otieno', '0000-00-00', 'Female', '761748487', 'davidotieno@gmail.com', 'Kisumu', 'COVID-19', 'Cannon Insurance', '0000-00-00 00:00:00'),
(210, 'David', 'Mwangi', '0000-00-00', 'Male', '764628034', 'davidmwangi@gmail.com', 'Kitusuri', 'Heart Disease', 'Shif', '0000-00-00 00:00:00'),
(211, 'Faith', 'Kariuki', '0000-00-00', 'Male', '718343173', 'faithkariuki@gmail.com', 'Thidingwa', 'Heart Disease', 'Shif', '0000-00-00 00:00:00'),
(212, 'David', 'Mbithe', '0000-00-00', 'Male', '780926617', 'davidmbithe@gmail.com', 'Uplands', 'Diabetes', 'Shif', '0000-00-00 00:00:00'),
(213, 'Esther', 'Atieno', '0000-00-00', 'Male', '775736489', 'estheratieno@gmail.com', 'Kanairo', 'Asthma', 'Britam', '0000-00-00 00:00:00'),
(214, 'Mercy', 'wainana', '0000-00-00', 'Male', '792958809', 'mercywainana@gmail.com', 'Muchatha', 'Cancer', 'CIC Insurance.', '0000-00-00 00:00:00'),
(216, 'Derrick', 'Ochieng', '0000-00-00', 'Male', '772184271', 'derrickochieng@gmail.com', 'Alfaker', 'Kidney Failure', 'Heritage Insurance', '0000-00-00 00:00:00'),
(217, 'Peter', 'Njeri', '0000-00-00', 'Female', '784263891', 'peternjeri@gmail.com', 'Nairobi', 'Diabetes', 'Cannon Insurance', '0000-00-00 00:00:00'),
(218, 'Isaac', 'Kibet', '0000-00-00', 'Male', '757397849', 'isaackibet@gmail.com', 'Mombasa', 'Typhoid', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(219, 'Grace', 'Atieno', '0000-00-00', 'Male', '740393742', 'graceatieno@gmail.com', 'Kisumu', 'Tuberculosis', 'APA Insurance', '0000-00-00 00:00:00'),
(221, 'Lucy', 'Kamau', '0000-00-00', 'Female', '743637206', 'lucykamau@gmail.com', 'Machakos', 'Diabetes', 'Shif', '0000-00-00 00:00:00'),
(222, 'David', 'Kamau', '0000-00-00', 'Female', '727391568', 'davidkamau@gmail.com', 'Kiambu', 'Cancer', 'Shif', '0000-00-00 00:00:00'),
(223, 'David', 'Muthoni', '0000-00-00', 'Male', '720848577', 'davidmuthoni@gmail.com', 'Kitusuri', 'Malaria', 'Shif', '0000-00-00 00:00:00'),
(225, 'James', 'Maina', '0000-00-00', 'Male', '787476076', 'jamesmaina@gmail.com', 'Uplands', 'Diabetes', 'CIC Insurance.', '0000-00-00 00:00:00'),
(226, 'Eric', 'Chebet', '0000-00-00', 'Female', '775423011', 'ericchebet@gmail.com', 'Kanairo', 'Malaria', 'AAR Insurance', '0000-00-00 00:00:00'),
(227, 'Joseph', 'Karanja', '0000-00-00', 'Male', '719763041', 'josephkaranja@gmail.com', 'Muchatha', 'Tuberculosis', 'Heritage Insurance', '0000-00-00 00:00:00'),
(229, 'eric', 'Kibet', '0000-00-00', 'Female', '723669657', 'erickibet@gmail.com', 'Alfaker', 'Tuberculosis', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(232, 'David', 'Karanja', '0000-00-00', 'Female', '764384090', 'davidkaranja@gmail.com', 'Kisumu', 'Typhoid', 'Shif', '0000-00-00 00:00:00'),
(233, 'Grace', 'Mbithe', '0000-00-00', 'Female', '755511205', 'gracembithe@gmail.com', 'Kajiado', 'Typhoid', 'Shif', '0000-00-00 00:00:00'),
(236, 'Isaac', 'wainana', '0000-00-00', 'Male', '717334666', 'isaacwainana@gmail.com', 'Kitusuri', 'Malaria', 'CIC Insurance.', '0000-00-00 00:00:00'),
(237, 'James', 'Achieng', '0000-00-00', 'Female', '768914634', 'jamesachieng@gmail.com', 'Thidingwa', 'Cancer', 'AAR Insurance', '0000-00-00 00:00:00'),
(238, 'Rose', 'Wambui', '0000-00-00', 'Female', '717068811', 'rosewambui@gmail.com', 'Uplands', 'Diabetes', 'Heritage Insurance', '0000-00-00 00:00:00'),
(239, 'eric', 'Achieng', '0000-00-00', 'Female', '791453897', 'ericachieng@gmail.com', 'Kanairo', 'Cancer', 'Cannon Insurance', '0000-00-00 00:00:00'),
(240, 'eric', 'Wambui', '0000-00-00', 'Female', '747244532', 'ericwambui@gmail.com', 'Muchatha', 'COVID-19', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(241, 'Isaac', 'Karanja', '0000-00-00', 'Female', '786783105', 'isaackaranja@gmail.com', 'Kinagop', 'Diabetes', 'APA Insurance', '0000-00-00 00:00:00'),
(242, 'Grace', 'Kamau', '0000-00-00', 'Female', '784541617', 'gracekamau@gmail.com', 'Alfaker', 'Tuberculosis', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(243, 'Grace', 'Wanjiku', '0000-00-00', 'Male', '714708592', 'gracewanjiku@gmail.com', 'Nairobi', 'Cancer', 'Shif', '0000-00-00 00:00:00'),
(245, 'Peter', 'Maina', '0000-00-00', 'Female', '730903990', 'petermaina@gmail.com', 'Kisumu', 'HIV/AIDS', 'Shif', '0000-00-00 00:00:00'),
(246, 'Peter', 'Mbithe', '0000-00-00', 'Male', '722145717', 'petermbithe@gmail.com', 'Kajiado', 'Tuberculosis', 'Britam', '0000-00-00 00:00:00'),
(247, 'Paul', 'Mwangi', '0000-00-00', 'Female', '725670574', 'paulmwangi@gmail.com', 'Machakos', 'Cancer', 'CIC Insurance.', '0000-00-00 00:00:00'),
(248, 'Isaac', 'Kamau', '0000-00-00', 'Male', '790748315', 'isaackamau@gmail.com', 'Kiambu', 'Hypertension', 'AAR Insurance', '0000-00-00 00:00:00'),
(250, 'Jane', 'Mbithe', '0000-00-00', 'Female', '748984308', 'janembithe@gmail.com', 'Thidingwa', 'Pneumonia', 'Cannon Insurance', '0000-00-00 00:00:00'),
(251, 'John', 'Mbithe', '0000-00-00', 'Male', '732083760', 'johnmbithe@gmail.com', 'Uplands', 'Asthma', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(252, 'Samuel', 'Mbithe', '0000-00-00', 'Male', '756976846', 'samuelmbithe@gmail.com', 'Kanairo', 'Typhoid', 'APA Insurance', '0000-00-00 00:00:00'),
(254, 'Lucy', 'Ochieng', '0000-00-00', 'Female', '750936740', 'lucyochieng@gmail.com', 'Kinagop', 'HIV/AIDS', 'Shif', '0000-00-00 00:00:00'),
(256, 'Esther', 'gatu', '0000-00-00', 'Female', '743417581', 'esthergatu@gmail.com', 'Nairobi', 'Dengue', 'Shif', '0000-00-00 00:00:00'),
(257, 'Peter', 'Wambui', '0000-00-00', 'Female', '755029854', 'peterwambui@gmail.com', 'Mombasa', 'Asthma', 'Britam', '0000-00-00 00:00:00'),
(260, 'Jane', 'Kariuki', '0000-00-00', 'Male', '734629452', 'janekariuki@gmail.com', 'Machakos', 'Diabetes', 'Heritage Insurance', '0000-00-00 00:00:00'),
(262, 'Joseph', 'Otieno', '0000-00-00', 'Male', '751764431', 'josephotieno@gmail.com', 'Kitusuri', 'Diabetes', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(263, 'Isaac', 'Ochieng', '0000-00-00', 'Female', '736418318', 'isaacochieng@gmail.com', 'Thidingwa', 'Asthma', 'APA Insurance', '0000-00-00 00:00:00'),
(270, 'Paul', 'Ochieng', '0000-00-00', 'Male', '794547225', 'paulochieng@gmail.com', 'Mombasa', 'HIV/AIDS', 'AAR Insurance', '0000-00-00 00:00:00'),
(272, 'Joseph', 'Atieno', '0000-00-00', 'Male', '735200954', 'josephatieno@gmail.com', 'Kajiado', 'Malaria', 'Cannon Insurance', '0000-00-00 00:00:00'),
(273, 'Lucy', 'Otieno', '0000-00-00', 'Male', '778391027', 'lucyotieno@gmail.com', 'Machakos', 'Asthma', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(275, 'Faith', 'Kibet', '0000-00-00', 'Male', '766042931', 'faithkibet@gmail.com', 'Kitusuri', 'Diabetes', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(276, 'Samuel', 'Kibet', '0000-00-00', 'Female', '754009499', 'samuelkibet@gmail.com', 'Thidingwa', 'HIV/AIDS', 'Shif', '0000-00-00 00:00:00'),
(278, 'derrick', 'Mbithe', '0000-00-00', 'Male', '749449231', 'derrickmbithe@gmail.com', 'Kanairo', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(279, 'James', 'Kiptoo', '0000-00-00', 'Male', '792046370', 'jameskiptoo@gmail.com', 'Muchatha', 'Asthma', 'Britam', '0000-00-00 00:00:00'),
(280, 'Rose', 'Karanja', '0000-00-00', 'Male', '739928983', 'rosekaranja@gmail.com', 'Kinagop', 'Stroke', 'CIC Insurance.', '0000-00-00 00:00:00'),
(281, 'Esther', 'Wambui', '0000-00-00', 'Female', '738639484', 'estherwambui@gmail.com', 'Alfaker', 'Typhoid', 'AAR Insurance', '0000-00-00 00:00:00'),
(285, 'Esther', 'Karanja', '0000-00-00', 'Male', '782886861', 'estherkaranja@gmail.com', 'Kajiado', 'Tuberculosis', 'APA Insurance', '0000-00-00 00:00:00'),
(286, 'Jane', 'Otieno', '0000-00-00', 'Male', '778574039', 'janeotieno@gmail.com', 'Machakos', 'Hypertension', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(287, 'eric', 'Mwangi', '0000-00-00', 'Male', '780297550', 'ericmwangi@gmail.com', 'Kiambu', 'Stroke', 'Shif', '0000-00-00 00:00:00'),
(288, 'Grace', 'Chebet', '0000-00-00', 'Female', '739301513', 'gracechebet@gmail.com', 'Kitusuri', 'Diabetes', 'Shif', '0000-00-00 00:00:00'),
(290, 'Peter', 'Chebet', '0000-00-00', 'Male', '770930570', 'peterchebet@gmail.com', 'Uplands', 'Liver Disease', 'Britam', '0000-00-00 00:00:00'),
(291, 'Peter', 'Wanjiku', '0000-00-00', 'Male', '773695227', 'peterwanjiku@gmail.com', 'Kanairo', 'HIV/AIDS', 'CIC Insurance.', '0000-00-00 00:00:00'),
(292, 'Samuel', 'Mbugua', '0000-00-00', 'Male', '725286541', 'samuelmbugua@gmail.com', 'Muchatha', 'Cancer', 'AAR Insurance', '0000-00-00 00:00:00'),
(294, 'Esther', 'Mbugua', '0000-00-00', 'Male', '713017369', 'esthermbugua@gmail.com', 'Alfaker', 'Typhoid', 'Cannon Insurance', '0000-00-00 00:00:00'),
(299, 'Mercy', 'Muthoni', '0000-00-00', 'Female', '788056805', 'mercymuthoni@gmail.com', 'Machakos', 'Diabetes', 'Shif', '0000-00-00 00:00:00'),
(306, 'Mercy', 'Kibet', '0000-00-00', 'Female', '759275253', 'mercykibet@gmail.com', 'Kinagop', 'Cancer', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(307, 'Lucy', 'Atieno', '0000-00-00', 'Male', '792840192', 'lucyatieno@gmail.com', 'Alfaker', 'Cancer', 'APA Insurance', '0000-00-00 00:00:00'),
(310, 'Rose', 'Kamau', '0000-00-00', 'Male', '795630404', 'rosekamau@gmail.com', 'Kisumu', 'Kidney Failure', 'Shif', '0000-00-00 00:00:00'),
(313, 'Rose', 'Mbithe', '0000-00-00', 'Female', '764634162', 'rosembithe@gmail.com', 'Kiambu', 'Heart Disease', 'CIC Insurance.', '0000-00-00 00:00:00'),
(317, 'derrick', 'Mbugua', '0000-00-00', 'Female', '756465277', 'derrickmbugua@gmail.com', 'Kanairo', 'HIV/AIDS', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(318, 'Samuel', 'Njeri', '0000-00-00', 'Male', '747050854', 'samuelnjeri@gmail.com', 'Muchatha', 'Asthma', 'APA Insurance', '0000-00-00 00:00:00'),
(319, 'Faith', 'wainana', '0000-00-00', 'Male', '721332656', 'faithwainana@gmail.com', 'Kinagop', 'Cancer', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(323, 'Simon', 'Kariuki', '0000-00-00', 'Male', '781021458', 'simonkariuki@gmail.com', 'Kisumu', 'Malaria', 'Britam', '0000-00-00 00:00:00'),
(324, 'Paul', 'Njeri', '0000-00-00', 'Male', '713548699', 'paulnjeri@gmail.com', 'Kajiado', 'Malaria', 'CIC Insurance.', '0000-00-00 00:00:00'),
(325, 'Mary', 'Mbithe', '0000-00-00', 'Female', '758765216', 'marymbithe@gmail.com', 'Machakos', 'Diabetes', 'AAR Insurance', '0000-00-00 00:00:00'),
(330, 'Paul', 'Kibet', '0000-00-00', 'Female', '739372766', 'paulkibet@gmail.com', 'Kanairo', 'Tuberculosis', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(332, 'Mercy', 'Chebet', '0000-00-00', 'Male', '751773901', 'mercychebet@gmail.com', 'Kinagop', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(339, 'Grace', 'gatu', '0000-00-00', 'Female', '725580087', 'gracegatu@gmail.com', 'Kiambu', 'Typhoid', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(342, 'Samuel', 'Kiptoo', '0000-00-00', 'Female', '770508623', 'samuelkiptoo@gmail.com', 'Uplands', 'Tuberculosis', 'Shif', '0000-00-00 00:00:00'),
(343, 'eric', 'Muthoni', '0000-00-00', 'Female', '795977555', 'ericmuthoni@gmail.com', 'Kanairo', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(344, 'Simon', 'Mbithe', '0000-00-00', 'Male', '744781062', 'simonmbithe@gmail.com', 'Muchatha', 'Tuberculosis', 'Shif', '0000-00-00 00:00:00'),
(348, 'James', 'Kariuki', '0000-00-00', 'Male', '777201165', 'jameskariuki@gmail.com', 'Mombasa', 'Cancer', 'Heritage Insurance', '0000-00-00 00:00:00'),
(350, 'James', 'gatu', '0000-00-00', 'Male', '784747650', 'jamesgatu@gmail.com', 'Kajiado', 'Pneumonia', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(352, 'Faith', 'Kamau', '0000-00-00', 'Male', '730862838', 'faithkamau@gmail.com', 'Kiambu', 'Malaria', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(353, 'Simon', 'Kiptoo', '0000-00-00', 'Male', '781572329', 'simonkiptoo@gmail.com', 'Kitusuri', 'Diabetes', 'Shif', '0000-00-00 00:00:00'),
(354, 'Mary', 'Kiptoo', '0000-00-00', 'Male', '768997854', 'marykiptoo@gmail.com', 'Thidingwa', 'Asthma', 'Shif', '0000-00-00 00:00:00'),
(358, 'Faith', 'Ochieng', '0000-00-00', 'Female', '775579273', 'faithochieng@gmail.com', 'Kinagop', 'Heart Disease', 'AAR Insurance', '0000-00-00 00:00:00'),
(360, 'Grace', 'Wambui', '0000-00-00', 'Male', '795595410', 'gracewambui@gmail.com', 'Nairobi', 'Diabetes', 'Cannon Insurance', '0000-00-00 00:00:00'),
(361, 'Paul', 'gatu', '0000-00-00', 'Female', '795757129', 'paulgatu@gmail.com', 'Mombasa', 'Malaria', 'Jubilee Insurance', '0000-00-00 00:00:00'),
(362, 'Joseph', 'Mbithe', '0000-00-00', 'Male', '777716116', 'josephmbithe@gmail.com', 'Kisumu', 'Malaria', 'APA Insurance', '0000-00-00 00:00:00'),
(363, 'Lucy', 'Kiptoo', '0000-00-00', 'Female', '749290723', 'lucykiptoo@gmail.com', 'Kajiado', 'Heart Disease', 'UAP Old Mutual', '0000-00-00 00:00:00'),
(364, 'Samuel', 'Muthoni', '0000-00-00', 'Female', '728404671', 'samuelmuthoni@gmail.com', 'Machakos', 'HIV/AIDS', 'Shif', '0000-00-00 00:00:00'),
(367, 'Mercy', 'Mwangi', '0000-00-00', 'Male', '778974247', 'mercymwangi@gmail.com', 'Thidingwa', 'Heart Disease', 'Britam', '0000-00-00 00:00:00'),
(368, 'Paul', 'Kamau', '0000-00-00', 'Male', '790342454', 'paulkamau@gmail.com', 'Uplands', 'Diabetes', 'CIC Insurance.', '0000-00-00 00:00:00'),
(369, 'John', 'Mbugua', '0000-00-00', 'Male', '734130558', 'johnmbugua@gmail.com', 'Kanairo', 'COVID-19', 'AAR Insurance', '0000-00-00 00:00:00'),
(375, 'Esther', 'Maina', '0000-00-00', 'Male', '777980645', 'esthermaina@gmail.com', 'Kisumu', 'Diabetes', 'Shif', '0000-00-00 00:00:00'),
(376, 'Joseph', 'Kariuki', '0000-00-00', 'Female', '774918250', 'josephkariuki@gmail.com', 'Kajiado', 'HIV/AIDS', 'Shif', '0000-00-00 00:00:00'),
(379, 'Grace', 'Achieng', '1980-02-08', 'Male', '766607807', 'graceachieng@gmail.com', 'Kitusuri', 'HIV/AIDS', 'SHA Insurance', '2025-03-19 17:32:55'),
(382, 'mkurugenzi', 'mgonjwa', '1999-11-10', 'Male', '0758100029', 'mkurugenzimgonjwa@gmail.com', 'Msituni, Kericho', 'Anemia', 'APA Insurance', '2025-03-27 23:39:02'),
(383, 'beatrice', 'cherono', '2000-07-31', 'Female', '0715915850', 'beatricecherono@gmail.com', 'Nairobi', NULL, NULL, '2025-03-31 07:29:38'),
(384, 'nicolas', 'muriuki', '1975-11-09', 'Male', '0743121212', 'nicolasmuriuki@gmail.com', 'Roysambu', NULL, NULL, '2025-04-09 08:00:44');

--
-- Triggers `patients`
--
DELIMITER $$
CREATE TRIGGER `before_patient_insert` BEFORE INSERT ON `patients` FOR EACH ROW BEGIN
    IF NEW.date_of_birth >= CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Date of birth must be in the past.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_patient_update` BEFORE UPDATE ON `patients` FOR EACH ROW BEGIN
    IF NEW.date_of_birth >= CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Date of birth must be in the past.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_in`
--

CREATE TABLE `patient_in` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `dept_type` enum('ICU','OPD') NOT NULL,
  `admission_date` datetime NOT NULL,
  `discharge_date` datetime DEFAULT NULL,
  `status` enum('active','discharged','pending') DEFAULT 'active',
  `bed_number` varchar(10) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_in`
--

INSERT INTO `patient_in` (`id`, `patient_id`, `dept_type`, `admission_date`, `discharge_date`, `status`, `bed_number`, `doctor_id`, `notes`, `created_at`) VALUES
(11, 3, 'ICU', '2025-02-20 08:00:00', NULL, 'active', 'ICU-03', 1, 'Critical - Hypertension spike', '2025-02-22 13:11:22'),
(12, 13, 'OPD', '2025-02-21 10:00:00', '2025-02-21 11:30:00', 'discharged', NULL, 2, 'Asthma checkup', '2025-02-22 13:11:22'),
(13, 5, 'ICU', '2025-02-19 14:00:00', '2025-02-21 09:00:00', 'discharged', 'ICU-05', 1, 'Diabetic coma - stabilized', '2025-02-22 13:11:22'),
(14, 6, 'OPD', '2025-02-22 09:00:00', NULL, 'pending', NULL, 2, 'Routine physical booked', '2025-02-22 13:11:22'),
(15, 7, 'ICU', '2025-02-18 22:00:00', NULL, 'active', 'ICU-02', 1, 'Severe malaria relapse', '2025-02-22 13:11:22'),
(16, 8, 'OPD', '2025-02-20 15:00:00', '2025-02-20 16:00:00', 'discharged', NULL, 2, 'Allergy follow-up', '2025-02-22 13:11:22'),
(17, 9, 'ICU', '2025-02-21 06:00:00', NULL, 'active', 'ICU-04', 1, 'Cholesterol-related chest pain', '2025-02-22 13:11:22'),
(19, 11, 'ICU', '2025-02-22 03:00:00', NULL, 'active', 'ICU-01', 1, 'Leg infection post-surgery', '2025-02-22 13:11:22'),
(20, 12, 'OPD', '2025-02-21 11:00:00', NULL, 'pending', NULL, 2, 'Migraine consultation', '2025-02-22 13:11:22'),
(21, 13, 'OPD', '2024-07-16 10:00:00', '2024-07-16 11:00:00', 'discharged', NULL, 2, 'Arthritis pain review', '2024-07-16 08:30:00'),
(22, 14, 'ICU', '2024-09-12 06:00:00', '2024-09-15 14:00:00', 'discharged', 'ICU-06', 1, 'Thyroid storm', '2024-09-15 11:30:00'),
(23, 15, 'OPD', '2024-10-23 09:00:00', NULL, 'pending', NULL, 2, 'Post-pneumonia check', '2024-10-23 06:15:00'),
(24, 16, 'ICU', '2024-12-06 20:00:00', NULL, 'active', 'ICU-03', 1, 'Severe anemia - transfusion', '2024-12-06 17:30:00'),
(25, 17, 'OPD', '2025-01-19 13:00:00', '2025-01-19 14:00:00', 'discharged', NULL, 2, 'Gastritis flare-up', '2025-01-19 11:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `patient_records`
--

CREATE TABLE `patient_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `medical_history_text` text NOT NULL,
  `uploaded_files` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_records`
--

INSERT INTO `patient_records` (`id`, `patient_id`, `medical_history_text`, `uploaded_files`, `submitted_at`) VALUES
(3, 3, 'RTdPbFZZbTBITmlYRElNRkcvcldnQT09OjpXZGlveFQwRDNXanVZRWFCUkRwRFpRPT0=', 'assets/fileuploads/patient_3/enc_1742128276_Real-time-software-systems-assgnment 1.pdf.enc', '2025-03-16 15:31:16'),
(8, 5, 'VmZ1VTZZSTNnb0hVcDQ0ZG9WZm9oMC9DNTd1MFMzZkJkM2Q2NFpPaXAwQXkraExlbmpCN00rWnNlVng4RXBVZ1drelU1cGlLa1FsS2lZYmYwcWlBU1dqOVk0ZENEaGNOSWVPR0FMVzlycWF4NjVFQi9aeHFHTzdhRTdpM3phWCs6OktoQ2hjVWFUZnNkNmcyczJ4NUZTbnc9PQ==', 'assets/fileuploads/patient_5/enc_1744059947_Sample-Adult-History-And-Physical-By-M2-Student.pdf.enc', '2025-04-08 00:05:47'),
(9, 26, 'TDNTbU9RMVFGNVhtaTF4R0k1YVlXQmVtR0hqdXQxcmZMdU1HRkJpakNaOTJZbWwzS3ZJR3pSdm9LbW85eUdkWVNIV0RKOG9veXVtb2ZyeG9uNFN2UTlMVWFweUkrbmdXSzdFM25VcGhoMGdNMXNVUGtrRnc0TWJFUk8yZkdWZVk6OlQ2NGRuaGZOSlB2QllVYUhIdmhyYVE9PQ==', 'assets/fileuploads/patient_26/enc_1744060372_Sample_file.pdf.enc', '2025-04-08 00:12:52'),
(10, 5, 'K28vaUxGeVVlTlE1czhFNlF6NnFYT3o1UUJod3FMd2lwT1Y0cmhKc1lZb1NJVzBkREhhRjZIRmhwZDNSeVBMVkdoNHU2WFFWdHY5cWRCbFRxQ2VPZ0g5Z1l2L1lXZERxdVVEZkxHSzRZYWFkcHhMVUtWblFrLzZKMFdCUFgySFBENmV0cUdHKytkU1NidnlYaEQ5bXZQUlRYY3A0MEtRZ0lGNkZJQi9CRGhtSVVuaTczWWRkdEpwOVpmOUs5eEdyd3I0OFZkVGNQU2xoazlNVlZYWGFMZ0doWWNIMEh0RU1sdjJ1UmZpdW9acWpkM2grbjFqb1VwL3oxMm5ibzFPRFE0SnFubHc0a3ZodklXNTQzL2FUUXc9PTo6VU5UKzZPdW9BYnpJdGRib3BWa243Zz09', 'assets/fileuploads/patient_5/enc_1744144520_Sample_file.pdf.enc', '2025-04-08 23:35:20');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receptionists`
--

CREATE TABLE `receptionists` (
  `id` int(11) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `staff_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receptionists`
--

INSERT INTO `receptionists` (`id`, `profile_pic`, `staff_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `address`, `email`, `contact_number`, `created_at`) VALUES
(1, 'C:\\xampp\\htdocs\\FINALPROJECT2025\\img\\receptionist1.jpg', 'REC-01-001-2025', 'Mary', 'Wanjiku', '1990-05-15', 'Female', 'Nairobi', 'mary.wanjiku@hospital.com', '254712345678', '2025-03-22 19:52:40'),
(2, 'C:\\xampp\\htdocs\\FINALPROJECT2025\\img\\receptionist2.jpg', 'REC-01-002-2025', 'John', 'Kamau', '1985-08-20', 'Male', 'Kisumu, Kenya', 'john.kamau@hospital.com', '254723456789', '2025-03-22 19:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Patient','Doctor','Admin','Nurse','Receptionist') NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `staff_id` varchar(255) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `profile_pic`, `first_name`, `last_name`, `date_of_birth`, `gender`, `address`, `password_hash`, `role`, `email`, `contact_number`, `created_at`, `staff_id`, `patient_id`) VALUES
(1, NULL, 'derrick', 'maina', '2003-01-04', 'Male', '0100, nairobi', '$2y$10$6j.Aa6idw9DzHSzig0QOPOw3O7sPJkNwHVJe4Kx2QP2eFvQjPoYzO', 'Patient', 'derrickgatuo3@gmail.com', '0768686329', '2025-02-03 20:11:45', NULL, 3),
(2, NULL, 'allan', 'thiong\'o', '2003-02-06', 'Male', '0100, nairobi', '$2y$10$VtkHhnGnMcLOkHNzMrz9V.mzlFaitckNKs1bn9Oh/UjltGCYsH9y2', 'Doctor', 'allanthiongo@gmail.com', '0712345678', '2025-02-03 21:05:10', 'DR-01-001-2025', NULL),
(3, NULL, 'andrew', 'romell', '1969-02-18', 'Male', '0100, nairobi', '$2y$10$0vgx57RA6tT9ff5nv23xh.7lGeBLszbMEKLkGJKVvl7lH40eksAam', 'Doctor', 'andrewromell@gmail.com', '0798765432', '2025-02-06 13:06:15', 'DR-01-002-2025', NULL),
(4, NULL, 'ndovu', 'kuu', '1999-02-05', 'Male', '0100, nairobi', '$2y$10$50L/x8qz6CJULD.jKJxhO./6eL9VxD3CffSrH/k695e73Cd3xw/0W', 'Nurse', 'ndovunikuu@gmail.com', '0754328100', '2025-02-06 14:20:45', 'HS-123456', NULL),
(5, NULL, 'karen', 'kibet', '2000-02-04', 'Female', '0100, nairobi', '$2y$10$7gD3Fh/y8WWZ0obSAhsX4.41pGJy7PkN4rfRIulzFXH6tFK0P8PCq', 'Nurse', 'karenkibet@gmail.com', '079876500', '2025-02-06 14:28:33', 'NR-123456', NULL),
(6, NULL, 'alex', 'kamau', '1971-02-13', 'Male', '0100, nairobi', '$2y$10$Mt6f4Djs.nJTYeWrm0qrCO35VZx.X8fnNrasW4mWTmlAAMwdfNZfy', 'Nurse', 'alex@gmail.com', '076587600', '2025-02-06 14:58:26', 'HS-987654', NULL),
(7, '67e554671c41d.png', 'Ian', 'Mboya', '2003-05-21', 'Male', '0100, Nairobi', '$2y$10$Xp0EdcSp3ks.4XArqzUqCe6Bb2sirYfuhzjcIQpWh395XG0kRIFoy', 'Admin', 'ianmboya@gmail.com', '0712346540', '2025-02-07 12:40:37', 'AD-01-001-2025', NULL),
(10, NULL, 'jimmy', 'mutheu', '1987-06-24', 'Male', '0110, kiambu tharakanithi', '$2y$10$0DM8c675zZoM87fjtB9fNe5EZBGaS50toNfZ5NI3ZrYIQoJRKylTm', 'Nurse', 'jimmymutheu@gmail.com', '0798564320', '2025-02-07 16:42:14', 'NR-243567', NULL),
(11, NULL, 'eric', 'wainana', '2001-01-28', 'Male', '410, kiambu', '$2y$10$Op4N93DLBE24A7d.XNWbweKsxhs12QD/1c32BVoC0H9eB/c/n7l9m', 'Patient', 'ericwainana@gmail.com', '0754321980', '2025-02-12 18:28:38', '', 5),
(12, NULL, 'jackline', 'matindi', '2003-02-01', 'Female', '546, Mombasa', '$2y$10$pxbRgEeICq6xEv0vvePGc.lDsuhdnDDLSzOWzeWb7/fRJrbufLfbe', 'Doctor', 'jacklinematindi@gmail.com', '0754381004', '2025-02-13 12:44:32', 'DR-01-003-2025', NULL),
(14, NULL, 'Mercy', 'Wambui', '1995-08-22', 'Female', 'Kisumu', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', 'Patient', 'mercy.wambui@yahoo.com', '254723456789', '2024-09-15 11:30:00', NULL, 22),
(15, NULL, 'David', 'Ochieng', '1988-11-05', 'Male', 'Mombasa', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', 'Patient', 'david.ochieng@hotmail.com', '254734567890', '2024-12-20 08:15:00', NULL, 23),
(16, NULL, 'Esther', 'Atieno', '1992-04-18', 'Female', 'Nakuru', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', 'Patient', 'esther.atieno@gmail.com', '254745678901', '2025-01-05 13:45:00', NULL, 24),
(18, NULL, 'dennis', 'gachuiri', '1994-05-09', 'Male', 'nairobi', '$2y$10$lcnTADUPsH/oMuhoKXKD8.sWulc1NUAE7HXntT5mrcZwusrJrzwoC', 'Doctor', 'dennisgachuiri@gmail.com', '0743214550', '2025-03-16 13:27:52', 'DR-01-019-2025', NULL),
(19, NULL, 'john', 'kibera', '1968-01-31', 'Male', '011, tharakanithi', '$2y$10$fYw53Ny39ne90FZT/l42G../uOXZPU8R6VDiReA/6XZcHJYBYSfKa', 'Patient', 'johnkibera@gmail.com', '0712760039', '2025-03-17 21:54:36', '', 26),
(21, NULL, 'fatuma', 'onyango', '1982-04-12', 'Female', 'Kisumu', '$2y$10$Ix1CpGQzO8HeTW8zylTkM.36LDm4emPiKsbNIehg46o7K7GvexYnO', 'Doctor', 'fatuma.onyango@gmail.com', '254712987654', '2025-03-17 22:41:02', 'DR-01-004-2025', NULL),
(22, NULL, 'mark', 'nguyo', '1991-06-06', 'Male', '65, nanyuki', '$2y$10$xiL2aWqoJ7pRAVm124g0mOGvnyYa/JJY7bhauRU8E8YeUoSvAlmh.', 'Patient', 'marknguyo@gmial.com', '0754555418', '2025-03-17 23:02:38', '', 28),
(28, NULL, 'peter', 'maina', '1985-03-15', 'Male', 'Nairobi', '$2y$10$afbNT1PRhAoIglNH5B32KetY1yMzz/QtjRPFumbpyj4MV.G5yQhje', 'Patient', 'peter.maina@gmail.com', '254712345678', '2025-03-18 13:52:50', '', 6),
(29, NULL, 'grace', 'wanjiku', '1992-07-22', 'Female', 'Kisumu', '$2y$10$a4d7wvPDlQ1u7/QYGxNXieA47XdmQy2B2hfRFu5ubOvm2LeGH/5ri', 'Patient', 'grace.wanjiku@yahoo.com', '254723456789', '2025-03-18 14:00:32', '', 7),
(37, NULL, 'patrick', 'wekesa', '1975-07-25', 'Male', 'Eldoret', '$2y$10$EYwF/Z/vCpLFY9FNdDNFKuOsJKmIHT4sRGE6izS2ch6/hTS7MKsbu', 'Doctor', 'patrick.wekesa@yahoo.com', '254723876543', '2025-03-18 19:44:54', 'DR-01-005-2025', NULL),
(38, NULL, 'konda', 'mnono', '1996-04-02', 'Male', 'Kakamega', '$2y$10$d2TVmhjqvfU0UPYZghorI.foiLsHmGXOVVpuPqQD3V.p7aNzXcnsy', 'Patient', 'kondamnono@gmail.com', '0743433767', '2025-03-18 21:17:07', '', 33),
(39, NULL, 'james', 'kamau', '1978-11-30', 'Male', 'Nakuru', '$2y$10$DD4CkcpkiiP0oVy5mGeO2O/SxfC038Vmo.6qnckfxyetvanvSa8rK', 'Patient', 'james.kamau@hotmail.com', '254734567890', '2025-03-18 21:30:08', '', 8),
(40, NULL, 'lilian', 'koech', '1988-09-03', 'Female', 'Kilimani, Nairobi', '$2y$10$L0ykA0pqDxnFiShCce3noer.cvmtwPeQixUZXQqMI7tT9iHa8TwT6', 'Doctor', 'lilian.koech@hotmail.com', '254734765432', '2025-03-18 21:38:52', 'DR-01-006-2025', NULL),
(41, 'user_41_1743431766.jpg', 'beatrice', 'cherono', '2000-07-31', 'Female', 'Nairobi', '$2y$10$G.y0v9Y8lfBehSZnNIbUr.NVsb9PGQUBoupSJe3Bazb/baSwHqQAq', 'Patient', 'beatricecherono@gmail.com', '0715915850', '2025-03-31 07:29:38', '', 383),
(42, NULL, 'mary', 'njeri', '1990-04-10', 'Female', 'Mombasa', '$2y$10$sLLyvy9HN3Kq4x98vH4uQOIkc42M/gd2TCWW3.d3JTUFsdGY5etmu', 'Patient', 'mary.njeri@gmail.com', '254745678901', '2025-04-03 14:42:21', '', 9),
(43, NULL, 'nicolas', 'muriuki', '1975-11-09', 'Male', 'Roysambu', '$2y$10$RQsp4DAKtoGjZxvZ0snZjej7BURb7pXBUu0PI/RLkV9kK5VtKvctG', 'Patient', 'nicolasmuriuki@gmail.com', '0743121212', '2025-04-09 08:00:45', '', 384);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    -- Check if the role is 'Patient' and data was updated
    IF NEW.role = 'Patient' THEN
        -- Update data in the patients table
        UPDATE patients
        SET first_name = NEW.first_name,
            last_name = NEW.last_name,
            date_of_birth = NEW.date_of_birth,
            gender = NEW.gender,
            contact_number = NEW.contact_number,
            email = NEW.email,
            address = NEW.address,
            created_at = NEW.created_at
        WHERE email = OLD.email;  -- Match based on old email or another unique identifier
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `visit_records`
--

CREATE TABLE `visit_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` varchar(50) NOT NULL,
  `visit_date` datetime NOT NULL,
  `reason_for_visit` text NOT NULL,
  `notes_outcome` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visit_records`
--

INSERT INTO `visit_records` (`id`, `patient_id`, `doctor_id`, `visit_date`, `reason_for_visit`, `notes_outcome`, `created_at`, `updated_at`) VALUES
(7, 23, 'DR-01-001-2025', '2025-03-18 00:00:00', 'RHJTYURsc2ppeFMyMnpCMWpWcXJBQT09Ojo3cHphM3piQVNaM0doeU5neGtSWU9nPT0=', 'YzV1cGVBbDZRdm5rUkRqZytGU0JPK2ZTNXpaZ1MvRDl5MVVicHQzT25KUm94TjZyK09GdDJpei9xWjRzL0p5VmdqNXN6NlE4NUdHb1J1aUczL2NiTElBb3h0QjNYcnUxUTNVVDNWWDRCaW10NTdpRVdza29paTJUN2pEVm1VSlJqQlpva1A4WWoyZEFYMjJmWmd0Sk1sUGVpNVRCdkxhSnJId3RLTStwTjhrPTo6Wi91MVE3dTUrQ21tRVVRd2I2a3l2dz09', '2025-03-19 03:10:14', '2025-03-19 03:10:14'),
(8, 11, 'DR-01-001-2025', '2025-03-17 00:00:00', 'UkVaUnNITTFvdytiS0JtMy9KVHhndz09OjpZVHF5eWdaSG1rMzVLeEVmYjdpWm1RPT0=', 'bEJhNTRpR2hTUnVjcUNJSmhLYmgyL2RuSDJBMzE0WjFzdjJwdjRSV2s5MTBhUTRHRmNuMlJnLzhsb2c4L01zTjo6TnZjY1lYZ2tvSFppSDBRaUNGcG51UT09', '2025-03-19 03:13:22', '2025-03-19 03:13:22'),
(9, 5, 'DR-01-001-2025', '2025-03-19 00:00:00', 'Nk5NWnkrbTdtWmpObmhWWUZmeHFCMHJWQXhqS2pyeXdyc3NqeGlmeHlXYz06OkRLM2l3VFl1Uk1VanNGWDFWWm85R2c9PQ==', 'Y201QnJSVGRBdWl3ZlBtWW1ERHI1WTN5Vy9yYnZHNko2YUlZOWc2MzlSNk5BZitrcHNkWGVXMkh5d2MvMDg3UERjN3pPYi9FOWJBQzI0QytLTy8vOUd0MDdyN084aytiNUFTYkh6SUdHVWFGR0JyLzVia3RPV0tvMWNpMnJ3MkF3MXN3WVg4YnUyZ2ZtbUZMNUZ6V0FnPT06OmRTYjZJNzVUbGxDUlZzN0lqN0NGb1E9PQ==', '2025-03-19 16:06:33', '2025-03-19 16:06:33'),
(10, 3, 'DR-01-001-2025', '2025-03-19 00:00:00', 'ZFBVM21hZGlkRHZFZ2R1M25oYVNqZz09OjpzcHJiMGtCSnNqaU5zU2JVV2hROWZ3PT0=', 'ZklZUytMa3ZoOHF1L2IwRVF1L3VTYnp5VTVNZjRkWFVLcTR6cEJ6bVFHQVFmOTNHVzhRaEl6dTJuN3k3Z1ZveEtzcjYyWTAwcmtVVmNnTGJuOWhGTFllVklnU2xvUzBRYTdVb0pBWnk5akE9OjpVLzIzdGlqaHNiOERjMHM4eGpyNThRPT0=', '2025-03-19 16:10:14', '2025-03-19 16:10:14'),
(11, 116, 'DR-01-001-2025', '2025-04-07 00:00:00', 'V3QrUk0rY1hGNE9kZHpvRVJKeW9wZz09OjpVZVRNUmE5NXZIZzY3eGF4Y1dFQitnPT0=', 'TlJZUWI0bUQybGtpLzJkelBWa2c4SlFHZkhnMTY2b05RTnJVeStnU0dxN2NxazVYQzRHR05JTkxFWTlDYlJLNkgyYWRKMVNrTnVzUnRhWlpoM1JpSXJXOGR1RHlBRjA5TkpZcEhxYTg2ZG89OjpHOVREcWhrVlhTMkxiN2xZTU4wWXlRPT0=', '2025-04-07 23:53:26', '2025-04-07 23:53:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_staff_id` (`staff_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_appointment_id` (`appointment_id`);

--
-- Indexes for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `data_access_logs`
--
ALTER TABLE `data_access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `staff_id_2` (`staff_id`);

--
-- Indexes for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `education_informations`
--
ALTER TABLE `education_informations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_staff_id` (`staff_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `experience_informations`
--
ALTER TABLE `experience_informations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_staff_id` (`staff_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `hospital_staffs`
--
ALTER TABLE `hospital_staffs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `nurses`
--
ALTER TABLE `nurses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patient_in`
--
ALTER TABLE `patient_in`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `receptionists`
--
ALTER TABLE `receptionists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_staff_id` (`staff_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `visit_records`
--
ALTER TABLE `visit_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `data_access_logs`
--
ALTER TABLE `data_access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `education_informations`
--
ALTER TABLE `education_informations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `experience_informations`
--
ALTER TABLE `experience_informations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hospital_staffs`
--
ALTER TABLE `hospital_staffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `nurses`
--
ALTER TABLE `nurses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=385;

--
-- AUTO_INCREMENT for table `patient_in`
--
ALTER TABLE `patient_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `patient_records`
--
ALTER TABLE `patient_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receptionists`
--
ALTER TABLE `receptionists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `visit_records`
--
ALTER TABLE `visit_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD CONSTRAINT `admin_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billing_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  ADD CONSTRAINT `chatbot_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `data_access_logs`
--
ALTER TABLE `data_access_logs`
  ADD CONSTRAINT `data_access_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `data_access_logs_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD CONSTRAINT `doctor_schedule_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_schedule_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `education_informations`
--
ALTER TABLE `education_informations`
  ADD CONSTRAINT `education_informations_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `employees` (`staff_id`);

--
-- Constraints for table `experience_informations`
--
ALTER TABLE `experience_informations`
  ADD CONSTRAINT `experience_informations_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `employees` (`staff_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `patient_in`
--
ALTER TABLE `patient_in`
  ADD CONSTRAINT `patient_in_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_in_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD CONSTRAINT `patient_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `visit_records`
--
ALTER TABLE `visit_records`
  ADD CONSTRAINT `visit_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

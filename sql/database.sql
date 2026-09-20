-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 21, 2026 at 12:51 AM
-- Server version: 10.11.19-MariaDB-cll-lve-log
-- PHP Version: 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `minvenmy_minvesta`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(80) NOT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `balance_adjustments`
--

CREATE TABLE `balance_adjustments` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `adj_date` date NOT NULL,
  `type` enum('plus','minus') NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `balance_before` decimal(18,2) NOT NULL,
  `balance_after` decimal(18,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `transaction_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `code`, `created_at`) VALUES
(1, 'Bank Sampah Utama', 'BSU', '2026-09-04 18:42:41'),
(2, 'Bank Sampah Cabang 2', 'BS002', '2026-09-04 18:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(30) DEFAULT 'other',
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `company_id`, `name`, `type`, `phone`, `email`) VALUES
(4, 1, 'erik', 'Pengepul', '089763657281', '-');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `customer_no` varchar(40) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `join_date` date NOT NULL,
  `status` enum('active','inactive','blacklist') DEFAULT 'active',
  `balance` decimal(18,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `company_id`, `customer_no`, `name`, `phone`, `address`, `join_date`, `status`, `balance`, `created_at`) VALUES
(6, 1, 'KC-09NSB-01', 'Asep Kusnandar', '+6287834803900', 'jln Sindang Sari 3 RT 4 RW 09', '2026-09-18', 'active', 5000.00, '2026-09-18 06:51:26'),
(7, 1, 'KC09-NSB-02', 'ida Farida', '083133515309', 'Jln Sindang sari 3 RT 4 RW 09', '2026-09-18', 'active', 1000.00, '2026-09-18 06:57:40'),
(8, 1, 'KC09-NSB-03', 'bapa Ujang', '083821379762', 'jln Sindang Sari 3 RT 4 RW 09', '2026-09-18', 'active', 2000.00, '2026-09-18 07:01:14'),
(9, 1, 'KC09-NSB-04', 'Agus', '083832310722', 'Jln Sindang Sari 3 RT 4 RW 09', '2026-09-18', 'active', 5000.00, '2026-09-18 07:03:33'),
(10, 1, 'KC09-NSB-05', 'yanto suharto', '083131060342', 'Jln Sindang Sari 3 RT 4 RW 09', '2026-09-18', 'blacklist', 0.00, '2026-09-18 07:06:19'),
(11, 1, 'KC09-NSB-06', 'Jajang', '+6287797029971', 'jln Sindang sari', '2026-09-19', 'active', 0.00, '2026-09-19 04:59:02'),
(12, 1, 'KC09-NSB-07', 'usep', '+6287897023971', 'jln Sindang sari', '2026-09-19', 'active', 2000.00, '2026-09-19 05:00:30'),
(13, 1, 'KC09-NSB-08', 'Pa Agung', '+6287834803999', 'jln Sindang sari 3', '2026-09-19', 'active', 0.00, '2026-09-19 05:02:05'),
(14, 1, 'KC09-NSB-09', 'usep', '+62878556739', 'jln Sindang sari 3', '2026-09-19', 'active', 0.00, '2026-09-19 05:03:27'),
(15, 1, 'KC09-NSB-10', 'uu', '+6287797329977', 'jln Sindang sari 3', '2026-09-19', 'active', 0.00, '2026-09-19 05:05:25'),
(16, 1, 'KC09-NSB-11', 'Wiliam', '+6281325490408', 'jln Sindang Sari3', '2026-09-19', 'active', 0.00, '2026-09-19 05:08:18'),
(17, 1, 'KC09-NSB-12', 'isep', '+6287863598288', 'jln sindang sari 3', '2026-09-19', 'active', 5000.00, '2026-09-19 05:10:26'),
(18, 1, 'KC09-NSB-13', 'dian', '+6289519462358', 'jln Sindang sari', '2026-09-19', 'active', 7000.00, '2026-09-19 05:11:48'),
(19, 1, 'KC09-NSB-14', 'pa Padil', '+6288808993278', 'jln Sindang sari 3', '2026-09-19', 'active', 0.00, '2026-09-19 05:13:46'),
(20, 1, 'KC09-NSB-15', 'kurwin', '+6285650229309', 'jln Sindang sari', '2026-09-19', 'active', 5000.00, '2026-09-19 05:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `customer_mutations`
--

CREATE TABLE `customer_mutations` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `mutation_date` date NOT NULL,
  `type` enum('credit','debit','adjustment') NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `receipt_no` varchar(50) NOT NULL,
  `deposit_date` date NOT NULL,
  `total_weight` decimal(18,3) DEFAULT 0.000,
  `total_amount` decimal(18,2) DEFAULT 0.00,
  `status` enum('posted','cancelled') DEFAULT 'posted',
  `notes` varchar(255) DEFAULT NULL,
  `documentation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposits`
--

INSERT INTO `deposits` (`id`, `company_id`, `customer_id`, `receipt_no`, `deposit_date`, `total_weight`, `total_amount`, `status`, `notes`, `documentation`) VALUES
(1, 1, 6, 'SET-20260920112907-688', '2026-09-20', 1.000, 5000.00, 'posted', '-', 'assets/uploads/transactions/deposit_20260920112907_29ac3d84a2.jpg'),
(2, 1, 12, 'SET-20260920113330-501', '2026-09-20', 1.000, 2000.00, 'posted', '-', 'assets/uploads/transactions/deposit_20260920113330_26350943d2.jpg'),
(3, 1, 7, 'SET-20260920113708-188', '2026-09-20', 1.000, 1000.00, 'posted', '', 'assets/uploads/transactions/deposit_20260920113708_4ee0d90c06.jpg'),
(4, 1, 9, 'SET-20260920113827-116', '2026-09-20', 1.000, 5000.00, 'posted', '-', 'assets/uploads/transactions/deposit_20260920113827_f33f84f4e0.jpg'),
(5, 1, 18, 'SET-20260920114258-853', '2026-09-20', 1.000, 7000.00, 'posted', '-', 'assets/uploads/transactions/deposit_20260920114258_55660012be.jpg'),
(6, 1, 8, 'SET-20260920115238-116', '2026-09-20', 1.000, 2000.00, 'posted', '-', 'assets/uploads/transactions/deposit_20260920115238_07b4f86539.jpg'),
(7, 1, 20, 'SET-20260920115429-631', '2026-09-20', 1.000, 5000.00, 'posted', '', 'assets/uploads/transactions/deposit_20260920115429_e5ca4bee31.jpg'),
(8, 1, 17, 'SET-20260920115509-360', '2026-09-20', 1.000, 5000.00, 'posted', '-', 'assets/uploads/transactions/deposit_20260920115509_89dba07123.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `deposit_details`
--

CREATE TABLE `deposit_details` (
  `id` bigint(20) NOT NULL,
  `deposit_id` bigint(20) NOT NULL,
  `waste_type_id` int(11) NOT NULL,
  `weight_kg` decimal(18,3) NOT NULL,
  `price_per_kg` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposit_details`
--

INSERT INTO `deposit_details` (`id`, `deposit_id`, `waste_type_id`, `weight_kg`, `price_per_kg`, `subtotal`) VALUES
(1, 1, 19, 1.000, 5000.00, 5000.00),
(2, 2, 20, 1.000, 2000.00, 2000.00),
(3, 3, 21, 1.000, 1000.00, 1000.00),
(4, 4, 19, 1.000, 5000.00, 5000.00),
(5, 5, 22, 1.000, 7000.00, 7000.00),
(6, 6, 24, 1.000, 2000.00, 2000.00),
(7, 7, 19, 1.000, 5000.00, 5000.00),
(8, 8, 23, 1.000, 5000.00, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `wallet_id` int(11) DEFAULT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `sale_date` date NOT NULL,
  `total_weight` decimal(18,3) DEFAULT 0.000,
  `total_amount` decimal(18,2) DEFAULT 0.00,
  `status` enum('paid','pending','cancelled') DEFAULT 'paid',
  `notes` varchar(255) DEFAULT NULL,
  `documentation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `id` bigint(20) NOT NULL,
  `sale_id` bigint(20) NOT NULL,
  `waste_type_id` int(11) NOT NULL,
  `weight_kg` decimal(18,3) NOT NULL,
  `price_per_kg` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `waste_type_id` int(11) NOT NULL,
  `adj_date` date NOT NULL,
  `stock_before` decimal(18,3) NOT NULL,
  `stock_after` decimal(18,3) NOT NULL,
  `difference` decimal(18,3) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `wallet_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type` enum('income','expense') NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transfers`
--

CREATE TABLE `transfers` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `from_wallet` int(11) NOT NULL,
  `to_wallet` int(11) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `transfer_date` date NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `username` varchar(80) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(30) DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `name`, `username`, `phone`, `profile_photo`, `password`, `role`, `created_at`) VALUES
(3, 1, 'Administrator', 'padil', '', 'assets/uploads/profiles/user_3_93dd8cb75543.png', '$2y$10$oXpOeS2Yh.nNC3JZVVZmUupsSYJ8haId..Jc.El8UDL4tsr3iiMAW', 'admin', '2026-09-08 09:06:40'),
(5, 1, 'Minven.id', 'minven', '081322629661', NULL, '$2y$10$ejHGBcyHtpgp8jRw/AZi8O/I1TMzyPtkr71urmegw.6lsDv04S21S', 'admin', '2026-09-15 15:40:44'),
(6, 1, 'syahrun', 'syahrun', '-', NULL, '$2y$10$bG5m37bMrhL4Ni8GLZKJSOiMjlu4zGXp.3yY.UKEaxOEid2mt1ewC', 'admin', '2026-09-15 15:42:08'),
(7, 1, 'dzkrul', 'dzkrul', '-', NULL, '$2y$10$EPPmGXIHScjJSnVHaFZbueY3a66vQ2eBnxpkCeRbmca2/6.mmpGIG', 'admin', '2026-09-15 15:42:32'),
(8, 1, 'Alka', 'alka', '+62 878-3480-3900', NULL, '$2y$10$kuFWvCJ9t/8vuibhC5MUoOAwtHh8h1pD9NP8YvcpFgXBxQHFLoFDi', 'staff', '2026-09-15 16:09:51');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(30) DEFAULT 'Cash',
  `balance` decimal(18,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `company_id`, `name`, `type`, `balance`) VALUES
(10, 1, 'tarka', 'Cash', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `waste_types`
--

CREATE TABLE `waste_types` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `unit` varchar(20) DEFAULT 'kg',
  `buy_price` decimal(18,2) DEFAULT 0.00,
  `sell_price` decimal(18,2) DEFAULT 0.00,
  `stock_kg` decimal(18,3) DEFAULT 0.000,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waste_types`
--

INSERT INTO `waste_types` (`id`, `company_id`, `name`, `unit`, `buy_price`, `sell_price`, `stock_kg`, `status`) VALUES
(19, 1, 'kardus', 'kg', 5000.00, 0.00, 3.000, 1),
(20, 1, 'wadah telur', 'kg', 2000.00, 0.00, 1.000, 1),
(21, 1, 'galon', 'kg', 1000.00, 0.00, 1.000, 1),
(22, 1, 'magicom', 'kg', 7000.00, 0.00, 1.000, 1),
(23, 1, 'ember', 'kg', 5000.00, 0.00, 1.000, 1),
(24, 1, 'meja', 'kg', 2000.00, 0.00, 1.000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint(20) NOT NULL,
  `company_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `wallet_id` int(11) DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `withdrawal_date` date NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('posted','cancelled') DEFAULT 'posted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `balance_adjustments`
--
ALTER TABLE `balance_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_id` (`company_id`,`customer_no`);

--
-- Indexes for table `customer_mutations`
--
ALTER TABLE `customer_mutations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `idx_customer_mut_date` (`company_id`,`mutation_date`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_id` (`company_id`,`receipt_no`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `idx_deposits_date` (`company_id`,`deposit_date`);

--
-- Indexes for table `deposit_details`
--
ALTER TABLE `deposit_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposit_id` (`deposit_id`),
  ADD KEY `waste_type_id` (`waste_type_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_id` (`company_id`,`invoice_no`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `wallet_id` (`wallet_id`),
  ADD KEY `idx_sales_date` (`company_id`,`sale_date`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `waste_type_id` (`waste_type_id`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `waste_type_id` (`waste_type_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_id` (`wallet_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_transactions_date` (`company_id`,`transaction_date`);

--
-- Indexes for table `transfers`
--
ALTER TABLE `transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_id` (`company_id`,`username`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `waste_types`
--
ALTER TABLE `waste_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `wallet_id` (`wallet_id`),
  ADD KEY `idx_withdrawals_date` (`company_id`,`withdrawal_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `balance_adjustments`
--
ALTER TABLE `balance_adjustments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `customer_mutations`
--
ALTER TABLE `customer_mutations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `deposit_details`
--
ALTER TABLE `deposit_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transfers`
--
ALTER TABLE `transfers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `waste_types`
--
ALTER TABLE `waste_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `balance_adjustments`
--
ALTER TABLE `balance_adjustments`
  ADD CONSTRAINT `balance_adjustments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `balance_adjustments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `balance_adjustments_ibfk_3` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_mutations`
--
ALTER TABLE `customer_mutations`
  ADD CONSTRAINT `customer_mutations_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_mutations_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deposits_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `deposit_details`
--
ALTER TABLE `deposit_details`
  ADD CONSTRAINT `deposit_details_ibfk_1` FOREIGN KEY (`deposit_id`) REFERENCES `deposits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deposit_details_ibfk_2` FOREIGN KEY (`waste_type_id`) REFERENCES `waste_types` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_details_ibfk_2` FOREIGN KEY (`waste_type_id`) REFERENCES `waste_types` (`id`);

--
-- Constraints for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `stock_adjustments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustments_ibfk_2` FOREIGN KEY (`waste_type_id`) REFERENCES `waste_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transfers`
--
ALTER TABLE `transfers`
  ADD CONSTRAINT `transfers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `waste_types`
--
ALTER TABLE `waste_types`
  ADD CONSTRAINT `waste_types_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `withdrawals_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `withdrawals_ibfk_3` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

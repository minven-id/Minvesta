-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2026 at 04:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banksampah_app`
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

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `company_id`, `name`, `type`) VALUES
(1, 1, 'Penjualan Sampah', 'income'),
(2, 1, 'Donasi', 'income'),
(3, 1, 'Operasional', 'expense'),
(4, 1, 'Transportasi', 'expense'),
(5, 1, 'Pembelian Peralatan', 'expense'),
(6, 2, 'Penjualan Sampah', 'income'),
(7, 2, 'Operasional', 'expense'),
(8, 1, 'Penjualan Sampah', 'income'),
(9, 1, 'Setoran Nasabah', 'income'),
(10, 1, 'Donasi / Hibah', 'income'),
(11, 1, 'Bunga Simpanan', 'income'),
(12, 1, 'Pendapatan Lainnya', 'income'),
(13, 1, 'Penarikan Nasabah', 'expense'),
(14, 1, 'Operasional Kantor', 'expense'),
(15, 1, 'Transportasi / Angkut', 'expense'),
(16, 1, 'Pembelian Peralatan', 'expense'),
(17, 1, 'Gaji Karyawan', 'expense'),
(18, 1, 'Listrik / Internet', 'expense'),
(19, 1, 'Biaya Lainnya', 'expense');

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
(1, 1, 'Pengepul Pak Joko', 'supplier', '0813-1111-2222', 'joko@pengepul.co.id'),
(3, 1, 'CV. Daur Ulang Sejahtera', 'customer', '021-5555-6666', 'info@daurulang.com');

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
(1, 1, 'NSB-0001', 'Nasabah Umum', '0800000000', '-', '2026-09-01', 'active', 0.00, '2026-09-04 18:42:41'),
(2, 1, 'NSB-0002', 'Budi Santoso', '0812-1234-5678', 'Jl. Gajah Mada No. 15', '2026-02-10', 'active', 0.00, '2026-09-05 03:24:46'),
(3, 1, 'NSB-0003', 'Siti Rahayu', '0813-8765-4321', 'Jl. Sudirman No. 22', '2026-03-05', 'active', 0.00, '2026-09-05 03:24:46'),
(4, 1, 'NSB-0004', 'Ahmad Yani', '0821-5555-1234', 'Jl. Diponegoro No. 8', '2026-04-12', 'active', 0.00, '2026-09-05 03:24:46'),
(5, 1, 'NSB-0005', 'Dewi Lestari', '0856-2222-9876', 'Jl. Sultan Agung No. 33', '2026-05-20', 'inactive', 0.00, '2026-09-05 03:24:46');

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

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `company_id`, `wallet_id`, `category_id`, `type`, `amount`, `description`, `transaction_date`, `created_at`) VALUES
(1, 1, 8, 11, 'income', 9000.00, '', '2026-09-05', '2026-09-05 07:05:08');

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

--
-- Dumping data for table `transfers`
--

INSERT INTO `transfers` (`id`, `company_id`, `from_wallet`, `to_wallet`, `amount`, `transfer_date`, `description`) VALUES
(1, 1, 8, 9, 9000.00, '2026-09-05', '');

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
(4, 1, 'ucup', 'ucup', '089000877', 'assets/uploads/profiles/user_4_71ec68800425.png', '$2y$10$oMKZwNW7Cs5Q6T8eZaSMp.ZHeVnh0swdZDGJH53DoiXGwwvFWzF0O', 'staff', '2026-09-08 13:21:32');

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
(1, 1, 'Kas Utama', 'Cash', 0.00),
(4, 2, 'Kas Utama', 'Cash', 0.00),
(5, 1, 'Kas Utama', 'Cash', 0.00),
(8, 1, 'Bank BCA', 'Bank', 0.00),
(9, 1, 'E-Wallet QRIS', 'E-Wallet', 9000.00);

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
(4, 1, 'Besi', 'kg', 4500.00, 6500.00, 0.000, 1),
(6, 1, 'Kaleng', 'kg', 5000.00, 8000.00, 0.000, 1),
(8, 1, 'Botol PET', 'kg', 4000.00, 6000.00, 0.000, 1),
(9, 1, 'Kardus', 'kg', 2500.00, 4000.00, 0.000, 1),
(11, 1, 'Besi Tua', 'kg', 4500.00, 6500.00, 0.000, 1),
(12, 1, 'Aluminium', 'kg', 12000.00, 16000.00, 0.000, 1),
(15, 1, 'Kaca Botol', 'kg', 1500.00, 2500.00, 0.000, 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer_mutations`
--
ALTER TABLE `customer_mutations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposit_details`
--
ALTER TABLE `deposit_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `waste_types`
--
ALTER TABLE `waste_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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

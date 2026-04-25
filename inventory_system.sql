-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260107.f7f22adaa8
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Apr 25, 2026 at 06:27 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `name` varchar(100) NOT NULL,
  `type` enum('cash','bank') NOT NULL,
  `balance` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_transactions`
--

CREATE TABLE `account_transactions` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `account_id` int NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `product_id` int NOT NULL,
  `type` enum('in','out','adjustment') NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `product_unit_id` int NOT NULL,
  `base_quantity` decimal(10,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`id`, `tenant_id`, `product_id`, `type`, `quantity`, `product_unit_id`, `base_quantity`, `reference_type`, `reference_id`, `created_at`) VALUES
(1, 1, 1, 'in', 2.00, 1, 500.00, 'purchase', 1, '2026-03-30 16:03:46'),
(2, 1, 1, 'in', 1.00, 1, 250.00, 'purchase', 1, '2026-03-30 16:03:47'),
(3, 1, 2, 'in', 2.00, 3, 4.00, 'purchase', 2, '2026-03-30 16:14:32'),
(4, 1, 2, 'in', 1.00, 2, 10.00, 'purchase', 2, '2026-03-30 16:14:32'),
(5, 1, 2, 'out', 2.00, 3, 4.00, 'sale', 1, '2026-04-07 01:14:45'),
(6, 1, 2, 'out', 1.00, 3, 2.00, 'sale', 2, '2026-04-11 14:40:20'),
(7, 1, 2, 'in', 100.00, 2, 1000.00, 'purchase', 3, '2026-04-11 15:01:53'),
(8, 1, 2, 'out', 2.00, 2, 20.00, 'sale', 3, '2026-04-11 15:36:12'),
(9, 1, 2, 'in', 4.00, 3, 8.00, 'purchase', 4, '2026-04-13 14:56:02'),
(10, 1, 2, 'out', 2.00, 3, 4.00, 'sale', 4, '2026-04-13 14:56:45'),
(11, 1, 2, 'in', 1.00, 3, 2.00, 'purchase', 5, '2026-04-13 14:57:26'),
(12, 1, 2, 'in', 1.00, 3, 2.00, 'purchase', 6, '2026-04-13 15:22:09'),
(13, 1, 1, 'out', 2.00, 1, 500.00, 'sale', 5, '2026-04-25 22:13:25'),
(14, 1, 2, 'out', 1.00, 2, 10.00, 'sale', 6, '2026-04-25 22:14:42');

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

CREATE TABLE `parties` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `name` varchar(150) NOT NULL,
  `type` enum('customer','supplier','both') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `opening_balance_type` enum('receivable','payable') DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `parties`
--

INSERT INTO `parties` (`id`, `tenant_id`, `name`, `type`, `phone`, `address`, `opening_balance`, `opening_balance_type`, `created_at`) VALUES
(1, 1, 'Supplier', 'supplier', '0000-0000000', 'ABC ST. XYZ', 0.00, NULL, '2026-03-30 15:33:25'),
(2, 1, 'Walk-in Customer', 'customer', '00000000000', '-', 0.00, NULL, '2026-04-07 00:04:45'),
(3, 1, 'Both', 'both', '123', '123', 0.00, NULL, '2026-04-13 14:55:04'),
(4, 1, 'Ignacia Hobbs', 'supplier', '+1 (598) 374-6738', 'Ut velit doloremque ', 0.00, NULL, '2026-04-25 21:51:03'),
(5, 1, 'Sara Eaton', 'customer', '+1 (735) 516-5277', 'Recusandae Et non q', 5000.00, 'receivable', '2026-04-25 22:12:13');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `party_id` int NOT NULL,
  `type` enum('incoming','outgoing') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` enum('cash','bank') NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `tenant_id`, `party_id`, `type`, `amount`, `method`, `reference_type`, `reference_id`, `created_at`) VALUES
(1, 1, 2, 'incoming', 1000.00, 'cash', 'sale', NULL, '2026-04-11 14:54:46'),
(2, 1, 1, 'outgoing', 2500.00, 'cash', 'purchase', 1, '2026-04-11 15:05:42'),
(3, 1, 2, 'incoming', 4000.00, 'bank', 'sale', 3, '2026-04-11 15:36:12'),
(4, 1, 2, 'incoming', 4000.00, 'cash', 'sale', 3, '2026-04-11 15:36:26'),
(5, 1, 3, 'outgoing', 4000.00, 'cash', 'purchase', 4, '2026-04-13 14:56:02'),
(6, 1, 3, 'incoming', 5000.00, 'bank', 'sale', 4, '2026-04-13 14:56:45'),
(7, 1, 2, 'outgoing', 2000.00, 'cash', 'purchase', 5, '2026-04-13 14:57:26'),
(8, 1, 5, 'incoming', 10000.00, 'cash', 'sale', 5, '2026-04-25 22:13:25'),
(9, 1, 5, 'incoming', 10000.00, 'bank', 'sale', 6, '2026-04-25 22:14:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `name` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `base_unit_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `tenant_id`, `name`, `category`, `base_unit_id`, `created_at`) VALUES
(1, 1, 'Product', 'Cat1', 1, '2026-03-30 14:52:36'),
(2, 1, 'Shoe', 'Cat2', 3, '2026-03-30 16:11:32');

-- --------------------------------------------------------

--
-- Table structure for table `product_units`
--

CREATE TABLE `product_units` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `product_id` int NOT NULL,
  `unit_name` varchar(50) NOT NULL,
  `conversion_to_base` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_units`
--

INSERT INTO `product_units` (`id`, `tenant_id`, `product_id`, `unit_name`, `conversion_to_base`) VALUES
(1, 1, 1, 'Drum', 250.00),
(2, 1, 2, 'Box', 10.00),
(3, 1, 2, 'Pair', 2.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `supplier_id` int NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) DEFAULT '0.00',
  `status` enum('pending','partial','paid') DEFAULT 'pending',
  `purchase_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `tenant_id`, `supplier_id`, `total_amount`, `paid_amount`, `status`, `purchase_date`, `created_at`) VALUES
(1, 1, 1, 7500.00, 7500.00, 'paid', '2026-03-30', '2026-03-30 16:03:46'),
(2, 1, 1, 7000.00, 6000.00, 'partial', '2026-03-30', '2026-03-30 16:14:32'),
(3, 1, 1, 20000.00, 5000.00, 'partial', '1977-08-13', '2026-04-11 15:01:53'),
(4, 1, 3, 4000.00, 4000.00, 'paid', '2026-04-13', '2026-04-13 14:56:02'),
(6, 1, 3, 1000.00, 0.00, 'pending', '2026-04-13', '2026-04-13 15:22:09');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `purchase_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_unit_id` int NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `tenant_id`, `purchase_id`, `product_id`, `product_unit_id`, `quantity`, `price`, `total`) VALUES
(1, 1, 1, 1, 1, 2.00, 2500.00, 5000.00),
(2, 1, 1, 1, 1, 1.00, 2500.00, 2500.00),
(3, 1, 2, 2, 3, 2.00, 1000.00, 2000.00),
(4, 1, 2, 2, 2, 1.00, 5000.00, 5000.00),
(5, 1, 3, 2, 2, 100.00, 200.00, 20000.00),
(6, 1, 4, 2, 3, 4.00, 1000.00, 4000.00),
(8, 1, 6, 2, 3, 1.00, 1000.00, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `customer_id` int NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) DEFAULT '0.00',
  `status` enum('pending','partial','paid') DEFAULT 'pending',
  `sale_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `tenant_id`, `customer_id`, `total_amount`, `paid_amount`, `status`, `sale_date`, `created_at`) VALUES
(1, 1, 2, 1000.00, 1000.00, 'paid', '2011-04-23', '2026-04-07 01:14:44'),
(2, 1, 2, 1000.00, 0.00, 'pending', '2026-04-11', '2026-04-11 14:40:20'),
(3, 1, 2, 8000.00, 8000.00, 'paid', '2026-04-11', '2026-04-11 15:36:12'),
(4, 1, 3, 6000.00, 5000.00, 'partial', '2026-04-13', '2026-04-13 14:56:45'),
(5, 1, 5, 10000.00, 10000.00, 'paid', '2026-04-25', '2026-04-25 22:13:25'),
(6, 1, 5, 5000.00, 10000.00, 'paid', '2026-04-25', '2026-04-25 22:14:42');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `sale_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_unit_id` int NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `tenant_id`, `sale_id`, `product_id`, `product_unit_id`, `quantity`, `price`, `total`) VALUES
(1, 1, 1, 2, 3, 2.00, 500.00, 1000.00),
(2, 1, 2, 2, 3, 1.00, 1000.00, 1000.00),
(3, 1, 3, 2, 2, 2.00, 4000.00, 8000.00),
(4, 1, 4, 2, 3, 2.00, 3000.00, 6000.00),
(5, 1, 5, 1, 1, 2.00, 5000.00, 10000.00),
(6, 1, 6, 2, 2, 1.00, 5000.00, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `name`, `email`, `status`, `created_at`) VALUES
(1, 'Default Client', 'client@example.com', 'active', '2026-04-25 17:37:43'),
(2, 'Aurora Sanford', 'inventory@gmail.com', 'active', '2026-04-25 17:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL DEFAULT '1',
  `name` varchar(50) NOT NULL,
  `symbol` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `tenant_id`, `name`, `symbol`) VALUES
(1, 1, 'Kilogram', 'Kg'),
(2, 1, 'Litre', 'L'),
(3, 1, 'Unit', 'U');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `tenant_id` int DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL DEFAULT '',
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `password_hash`, `auth_key`, `role`, `status`, `created_at`) VALUES
(1, NULL, 'Admin', 'admin@admin.com', '$2y$13$5LfSKp7IUm.iXBJ6Rbmi7eHV5A01a/PdFf6h2g4XyaKNshpWf2KKu', '1_Iy9KURIk6FxqSElRGUDc-K5Pm5_9_R', 'admin', 1, '2026-04-25 17:39:56'),
(2, 2, 'Test ', 'test@test.com', '$2y$13$rxAuENz4QhOD7aRir4z4xeWLJ0iOrYmwUwcfv4P7/SxzupoE.6OAW', 'YgNUAyFwQShzBfjou4q-vqS0Lpxe-RaL', 'user', 1, '2026-04-25 17:43:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_unit_id` (`product_unit_id`);

--
-- Indexes for table `parties`
--
ALTER TABLE `parties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `party_id` (`party_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `base_unit_id` (`base_unit_id`);

--
-- Indexes for table `product_units`
--
ALTER TABLE `product_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_unit_id` (`product_unit_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_unit_id` (`product_unit_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_transactions`
--
ALTER TABLE `account_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `parties`
--
ALTER TABLE `parties`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_units`
--
ALTER TABLE `product_units`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD CONSTRAINT `account_transactions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_transactions_ibfk_2` FOREIGN KEY (`product_unit_id`) REFERENCES `product_units` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`base_unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `product_units`
--
ALTER TABLE `product_units`
  ADD CONSTRAINT `product_units_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `parties` (`id`);

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `purchase_items_ibfk_3` FOREIGN KEY (`product_unit_id`) REFERENCES `product_units` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `parties` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_3` FOREIGN KEY (`product_unit_id`) REFERENCES `product_units` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

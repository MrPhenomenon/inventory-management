-- =============================================================
-- Accounts System Migration
-- Run this on your inventory_system database.
-- =============================================================

-- 1. accounts: drop stored balance, add opening_balance
ALTER TABLE `accounts`
    DROP COLUMN `balance`,
    ADD COLUMN `opening_balance` decimal(12,2) NOT NULL DEFAULT 0.00 AFTER `type`;

-- 2. account_transactions: add description column
ALTER TABLE `account_transactions`
    ADD COLUMN `description` varchar(255) DEFAULT NULL AFTER `amount`;

-- 3. payments: add account_id + notes columns
ALTER TABLE `payments`
    ADD COLUMN `account_id` int DEFAULT NULL AFTER `method`,
    ADD COLUMN `notes` varchar(255) DEFAULT NULL AFTER `account_id`,
    ADD CONSTRAINT `fk_payments_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

-- 4. New table: chemical_account (single-row config)
CREATE TABLE `chemical_account` (
    `id`              int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `opening_balance` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Seed with one row
INSERT INTO `chemical_account` (`id`, `opening_balance`) VALUES (1, 0.00);

-- 5. New table: office_expenses
CREATE TABLE `office_expenses` (
    `id`          int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `amount`      decimal(12,2) NOT NULL,
    `description` varchar(255) NOT NULL,
    `created_at`  datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 6. New table: nil_operations (audit trail for chemical ↔ office settlement)
CREATE TABLE `nil_operations` (
    `id`                      int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `chemical_balance_before` decimal(12,2) NOT NULL,
    `office_total`            decimal(12,2) NOT NULL,
    `chemical_balance_after`  decimal(12,2) NOT NULL,
    `performed_by`            int NOT NULL,
    `notes`                   varchar(255) DEFAULT NULL,
    `created_at`              datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_nil_performed_by` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 7. New table: account_transfers (sub-account to sub-account, chemical unaffected)
CREATE TABLE `account_transfers` (
    `id`              int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `from_account_id` int NOT NULL,
    `to_account_id`   int NOT NULL,
    `amount`          decimal(12,2) NOT NULL,
    `description`     varchar(255) DEFAULT NULL,
    `created_at`      datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_transfer_from` FOREIGN KEY (`from_account_id`) REFERENCES `accounts` (`id`),
    CONSTRAINT `fk_transfer_to`   FOREIGN KEY (`to_account_id`)   REFERENCES `accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =============================================================
-- Done. The tenant_id columns on existing tables can remain
-- harmlessly — they are no longer read or written by the app.
-- =============================================================

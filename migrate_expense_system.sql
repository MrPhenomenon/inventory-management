-- =============================================================
-- Expense System Migration
-- Replaces office_expenses with a dynamic expense_categories +
-- expenses system. Also fixes nil_operations and Chemical Account.
-- Run AFTER migrate_accounts.sql.
-- =============================================================

-- 1. Dynamic expense categories
CREATE TABLE `expense_categories` (
    `id`         int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`       varchar(100) NOT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Seed default categories
INSERT INTO `expense_categories` (`name`) VALUES
    ('Office'),
    ('House'),
    ('Car'),
    ('Natural Membership'),
    ('Zakat');

-- 2. Generic expenses table (replaces office_expenses)
CREATE TABLE `expenses` (
    `id`                   int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `category_id`          int NOT NULL,
    `amount`               decimal(12,2) NOT NULL,
    `description`          varchar(255) NOT NULL,
    `paid_from_account_id` int NOT NULL,
    `created_at`           datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_expense_category` FOREIGN KEY (`category_id`)          REFERENCES `expense_categories` (`id`),
    CONSTRAINT `fk_expense_account`  FOREIGN KEY (`paid_from_account_id`) REFERENCES `accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 3. Update nil_operations:
--    a) add category_id (which expense category is being nilled)
--    b) rename office_total -> expense_total
ALTER TABLE `nil_operations`
    ADD COLUMN `category_id` int DEFAULT NULL AFTER `id`,
    ADD CONSTRAINT `fk_nil_category` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`);

ALTER TABLE `nil_operations`
    CHANGE COLUMN `office_total` `expense_total` decimal(12,2) NOT NULL;

-- 4. Drop old office_expenses table (data is lost; migrate manually if needed)
DROP TABLE IF EXISTS `office_expenses`;

-- =============================================================
-- Chemical Account formula is now:
--   opening_balance + SUM(purchases.total_amount)
--                   - SUM(sales.total_amount)
--                   - SUM(nil_operations.expense_total)
-- Payments do NOT affect Chemical Account.
-- =============================================================

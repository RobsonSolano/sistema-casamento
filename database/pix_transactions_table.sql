CREATE TABLE IF NOT EXISTS `pix_transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `gift_id` INT NOT NULL,
  `gift_name` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `pix_code` TEXT NOT NULL,
  `donor_name` VARCHAR(255) NULL,
  `donor_phone` VARCHAR(20) NULL,
  `status` ENUM('pending', 'paid', 'confirmed', 'cancelled') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  INDEX `idx_gift_id` (`gift_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
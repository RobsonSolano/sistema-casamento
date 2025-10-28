-- =============================================
-- MeuCasamento.com.br - SaaS Database Schema
-- =============================================
-- Versão: 2.0 (Completa com Auth separado)
-- Data: 28/10/2025
-- Banco: pessoal_casamento_saas
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =============================================
-- 1. TENANTS (Multi-tenancy)
-- =============================================

CREATE TABLE `tenants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(36) UNIQUE NOT NULL COMMENT 'UUID único do tenant',
  `domain` VARCHAR(100) UNIQUE COMMENT 'Subdomínio: maria-joao.meucasamento.com.br',
  `custom_domain` VARCHAR(100) UNIQUE COMMENT 'Domínio próprio: nosso-casamento.com',
  `status` ENUM('active', 'suspended', 'cancelled') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_uuid` (`uuid`),
  INDEX `idx_domain` (`domain`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. USERS (Clientes - Noivos)
-- =============================================

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `role` ENUM('owner', 'collaborator') DEFAULT 'owner' COMMENT 'owner = noivo/noiva, collaborator = ajudante',
  `email_verified` BOOLEAN DEFAULT FALSE,
  `verification_token` VARCHAR(100),
  `is_active` BOOLEAN DEFAULT TRUE,
  `last_login` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  INDEX `idx_email` (`email`),
  INDEX `idx_tenant` (`tenant_id`),
  INDEX `idx_role` (`role`),
  INDEX `idx_email_verified` (`email_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. USER_PROFILES (Perfil completo dos noivos)
-- =============================================

CREATE TABLE `user_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `avatar` VARCHAR(255),
  `cpf` VARCHAR(14) UNIQUE COMMENT 'CPF para nota fiscal',
  `birth_date` DATE,
  `gender` ENUM('male', 'female', 'other', 'prefer_not_say'),
  `address_zipcode` VARCHAR(9),
  `address_street` VARCHAR(255),
  `address_number` VARCHAR(20),
  `address_complement` VARCHAR(100),
  `address_neighborhood` VARCHAR(100),
  `address_city` VARCHAR(100),
  `address_state` VARCHAR(2),
  `address_country` VARCHAR(2) DEFAULT 'BR',
  `whatsapp` VARCHAR(20),
  `instagram` VARCHAR(100),
  `facebook` VARCHAR(100),
  `preferences` JSON COMMENT 'Preferências de notificação, idioma, etc',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user` (`user_id`),
  INDEX `idx_cpf` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. ADMIN_USERS (Super Admin - Gestores da Plataforma)
-- =============================================

CREATE TABLE `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin', 'support', 'financial', 'developer') DEFAULT 'support',
  `permissions` JSON COMMENT 'Permissões específicas',
  `is_active` BOOLEAN DEFAULT TRUE,
  `last_login` TIMESTAMP NULL,
  `created_by` INT COMMENT 'admin_id que criou',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. SESSIONS (Controle de sessões JWT/Token)
-- =============================================

CREATE TABLE `sessions` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT COMMENT 'FK para users',
  `admin_user_id` INT COMMENT 'FK para admin_users',
  `token` VARCHAR(255) UNIQUE NOT NULL,
  `refresh_token` VARCHAR(255) UNIQUE,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `device_type` ENUM('desktop', 'mobile', 'tablet', 'unknown') DEFAULT 'unknown',
  `expires_at` TIMESTAMP NOT NULL,
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
  INDEX `idx_token` (`token`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_admin_user` (`admin_user_id`),
  INDEX `idx_expires` (`expires_at`),
  INDEX `idx_active` (`is_active`),
  
  CONSTRAINT `chk_user_type` CHECK (
    (user_id IS NOT NULL AND admin_user_id IS NULL) OR
    (user_id IS NULL AND admin_user_id IS NOT NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. PASSWORD_RESETS (Recuperação de senha)
-- =============================================

CREATE TABLE `password_resets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT COMMENT 'FK para users',
  `admin_user_id` INT COMMENT 'FK para admin_users',
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `used_at` TIMESTAMP NULL,
  `ip_address` VARCHAR(45),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
  INDEX `idx_token` (`token`),
  INDEX `idx_email` (`email`),
  INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. LOGIN_ATTEMPTS (Segurança - Brute Force)
-- =============================================

CREATE TABLE `login_attempts` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` TEXT,
  `success` BOOLEAN DEFAULT FALSE,
  `failure_reason` VARCHAR(100),
  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `idx_email` (`email`),
  INDEX `idx_ip` (`ip_address`),
  INDEX `idx_attempted` (`attempted_at`),
  INDEX `idx_email_ip` (`email`, `ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 8. TEMPLATES (Layouts disponíveis)
-- =============================================

CREATE TABLE `templates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) UNIQUE NOT NULL,
  `description` TEXT,
  `preview_image` VARCHAR(255),
  `category` ENUM('classic', 'modern', 'rustic', 'elegant', 'minimalist') DEFAULT 'classic',
  `is_active` BOOLEAN DEFAULT TRUE,
  `is_premium` BOOLEAN DEFAULT FALSE,
  `html_file` VARCHAR(255) COMMENT 'Caminho do arquivo HTML do template',
  `css_file` VARCHAR(255) COMMENT 'Caminho do arquivo CSS do template',
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_slug` (`slug`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 9. WEDDINGS (Dados do casamento)
-- =============================================

CREATE TABLE `weddings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT NOT NULL,
  `template_id` INT,
  
  -- Dados dos noivos
  `bride_name` VARCHAR(255) NOT NULL,
  `groom_name` VARCHAR(255) NOT NULL,
  `couple_photo` VARCHAR(255),
  
  -- Data e local
  `wedding_date` DATE NOT NULL,
  `wedding_time` TIME,
  `ceremony_location` VARCHAR(255),
  `ceremony_address` TEXT,
  `ceremony_maps_url` TEXT,
  `ceremony_maps_embed` TEXT,
  `reception_location` VARCHAR(255),
  `reception_address` TEXT,
  `reception_maps_url` TEXT,
  `reception_maps_embed` TEXT,
  
  -- Customização visual
  `primary_color` VARCHAR(7) DEFAULT '#D4AF37',
  `secondary_color` VARCHAR(7) DEFAULT '#FFFFFF',
  `accent_color` VARCHAR(7) DEFAULT '#000000',
  `font_family` VARCHAR(50) DEFAULT 'Tempting',
  `background_image` VARCHAR(255),
  
  -- Textos personalizados
  `welcome_title` VARCHAR(255),
  `welcome_message` TEXT,
  `love_story_title` VARCHAR(255) DEFAULT 'Nossa História',
  `love_story` TEXT,
  `gifts_section_title` VARCHAR(255) DEFAULT 'Lista de Presentes',
  `gifts_section_description` TEXT,
  `messages_section_title` VARCHAR(255) DEFAULT 'Deixe seu Recado',
  
  -- Configurações de módulos
  `show_countdown` BOOLEAN DEFAULT TRUE,
  `show_gallery` BOOLEAN DEFAULT TRUE,
  `show_gifts` BOOLEAN DEFAULT TRUE,
  `show_messages` BOOLEAN DEFAULT TRUE,
  `show_location` BOOLEAN DEFAULT TRUE,
  `show_rsvp` BOOLEAN DEFAULT FALSE,
  `music_enabled` BOOLEAN DEFAULT TRUE,
  `music_url` VARCHAR(255),
  `music_autoplay` BOOLEAN DEFAULT FALSE,
  
  -- PIX para presentes
  `pix_key` VARCHAR(255),
  `pix_key_type` ENUM('cpf', 'cnpj', 'email', 'phone', 'random'),
  `pix_recipient_name` VARCHAR(255),
  `pix_city` VARCHAR(100),
  
  -- Meta tags (SEO)
  `meta_title` VARCHAR(255),
  `meta_description` TEXT,
  `og_image` VARCHAR(255),
  
  -- Status
  `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
  `published_at` TIMESTAMP NULL,
  `archived_at` TIMESTAMP NULL,
  
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE SET NULL,
  INDEX `idx_tenant` (`tenant_id`),
  INDEX `idx_wedding_date` (`wedding_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 10. SUBSCRIPTIONS (Assinaturas/Pagamentos)
-- =============================================

CREATE TABLE `subscriptions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT NOT NULL,
  
  -- Dados da compra
  `plan_type` ENUM('monthly', 'upfront', 'custom') DEFAULT 'upfront',
  `months_purchased` INT NOT NULL DEFAULT 1,
  `amount_paid` DECIMAL(10,2) NOT NULL,
  `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
  `discount_code` VARCHAR(50),
  
  -- Período de validade
  `starts_at` DATE NOT NULL,
  `expires_at` DATE NOT NULL,
  
  -- Pagamento
  `payment_method` ENUM('pix', 'credit_card', 'boleto', 'manual') DEFAULT 'pix',
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
  `payment_provider` VARCHAR(50) COMMENT 'mercadopago, pagseguro, stripe',
  `payment_provider_id` VARCHAR(255) COMMENT 'ID da transação no gateway',
  `payment_provider_data` JSON COMMENT 'Dados extras do gateway',
  `payment_confirmed_at` TIMESTAMP NULL,
  
  -- PIX específico
  `pix_code` TEXT,
  `pix_qr_code` TEXT,
  `pix_expires_at` TIMESTAMP NULL,
  
  -- Auto-renovação (futuro)
  `auto_renew` BOOLEAN DEFAULT FALSE,
  `cancelled_at` TIMESTAMP NULL,
  `cancellation_reason` TEXT,
  
  -- Notas administrativas
  `admin_notes` TEXT,
  
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  INDEX `idx_tenant` (`tenant_id`),
  INDEX `idx_expires` (`expires_at`),
  INDEX `idx_payment_status` (`payment_status`),
  INDEX `idx_starts_expires` (`starts_at`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 11. PRESENTES (Lista de presentes)
-- =============================================

CREATE TABLE `presentes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `wedding_id` INT NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descricao` TEXT,
  `valor` DECIMAL(10,2) DEFAULT NULL,
  `imagem` VARCHAR(255) DEFAULT NULL,
  `categoria` VARCHAR(100) COMMENT 'cozinha, decoracao, eletronicos, etc',
  `link_loja` VARCHAR(500) COMMENT 'Link externo da loja',
  `quantidade_disponivel` INT DEFAULT 1,
  `quantidade_comprada` INT DEFAULT 0,
  `status` ENUM('disponivel', 'reservado', 'comprado') DEFAULT 'disponivel',
  `reserved_at` TIMESTAMP NULL,
  `reserved_by_name` VARCHAR(255),
  `reserved_by_phone` VARCHAR(20),
  `reserved_by_email` VARCHAR(255),
  `display_order` INT DEFAULT 0,
  `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deletado` BOOLEAN DEFAULT FALSE,
  
  FOREIGN KEY (`wedding_id`) REFERENCES `weddings`(`id`) ON DELETE CASCADE,
  INDEX `idx_wedding` (`wedding_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_categoria` (`categoria`),
  INDEX `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 12. PIX_TRANSACTIONS (Transações PIX)
-- =============================================

CREATE TABLE `pix_transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `wedding_id` INT NOT NULL,
  `gift_id` INT COMMENT 'NULL = contribuição livre',
  `gift_name` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `pix_code` TEXT NOT NULL,
  `qr_code_image` TEXT,
  
  -- Doador
  `donor_name` VARCHAR(255),
  `donor_email` VARCHAR(255),
  `donor_phone` VARCHAR(20),
  `donor_message` TEXT,
  `donor_ip` VARCHAR(45),
  
  -- Status e confirmação
  `status` ENUM('iniciado', 'aguardando', 'pre_confirmado', 'confirmado', 'cancelled', 'expired') DEFAULT 'iniciado',
  `confirmed_at` TIMESTAMP NULL,
  `expires_at` TIMESTAMP NULL,
  
  -- Integração com gateway
  `payment_provider` VARCHAR(50) COMMENT 'mercadopago, pagseguro, etc',
  `payment_provider_txid` VARCHAR(255),
  `payment_provider_data` JSON,
  
  -- Webhook tracking
  `webhook_received_at` TIMESTAMP NULL,
  `webhook_data` JSON,
  
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`wedding_id`) REFERENCES `weddings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`gift_id`) REFERENCES `presentes`(`id`) ON DELETE SET NULL,
  INDEX `idx_wedding` (`wedding_id`),
  INDEX `idx_gift` (`gift_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_donor_email` (`donor_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 13. RECADOS (Mensagens dos convidados)
-- =============================================

CREATE TABLE `recados` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `wedding_id` INT NOT NULL,
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255),
  `mensagem` TEXT NOT NULL,
  `is_approved` BOOLEAN DEFAULT TRUE COMMENT 'Moderação futura',
  `is_featured` BOOLEAN DEFAULT FALSE COMMENT 'Destacar na página',
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `data_envio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `approved_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`wedding_id`) REFERENCES `weddings`(`id`) ON DELETE CASCADE,
  INDEX `idx_wedding` (`wedding_id`),
  INDEX `idx_data_envio` (`data_envio`),
  INDEX `idx_approved` (`is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 14. GALLERY_IMAGES (Galeria de fotos)
-- =============================================

CREATE TABLE `gallery_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `wedding_id` INT NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `original_filename` VARCHAR(255),
  `file_path` VARCHAR(500) NOT NULL,
  `file_size` INT COMMENT 'Tamanho em bytes',
  `width` INT,
  `height` INT,
  `display_order` INT DEFAULT 0,
  `caption` TEXT,
  `is_featured` BOOLEAN DEFAULT FALSE,
  `is_cover` BOOLEAN DEFAULT FALSE,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`wedding_id`) REFERENCES `weddings`(`id`) ON DELETE CASCADE,
  INDEX `idx_wedding` (`wedding_id`),
  INDEX `idx_display_order` (`display_order`),
  INDEX `idx_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 15. RSVP (Confirmação de presença - Fase 2)
-- =============================================

CREATE TABLE `rsvp` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `wedding_id` INT NOT NULL,
  `guest_name` VARCHAR(255) NOT NULL,
  `guest_email` VARCHAR(255),
  `guest_phone` VARCHAR(20),
  `will_attend` ENUM('yes', 'no', 'maybe') DEFAULT 'yes',
  `number_of_guests` INT DEFAULT 1,
  `dietary_restrictions` TEXT,
  `message` TEXT,
  `ip_address` VARCHAR(45),
  `confirmed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`wedding_id`) REFERENCES `weddings`(`id`) ON DELETE CASCADE,
  INDEX `idx_wedding` (`wedding_id`),
  INDEX `idx_will_attend` (`will_attend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 16. ACTIVITY_LOGS (Auditoria)
-- =============================================

CREATE TABLE `activity_logs` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT,
  `user_id` INT,
  `action` VARCHAR(100) NOT NULL COMMENT 'wedding.published, gift.added, etc',
  `entity_type` VARCHAR(50) COMMENT 'wedding, gift, message, user',
  `entity_id` INT,
  `old_values` JSON,
  `new_values` JSON,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  INDEX `idx_tenant` (`tenant_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_action` (`action`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 17. DISCOUNT_CODES (Cupons de desconto)
-- =============================================

CREATE TABLE `discount_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE NOT NULL,
  `description` VARCHAR(255),
  `discount_type` ENUM('percentage', 'fixed') DEFAULT 'percentage',
  `discount_value` DECIMAL(10,2) NOT NULL,
  `max_uses` INT DEFAULT NULL COMMENT 'NULL = ilimitado',
  `times_used` INT DEFAULT 0,
  `valid_from` DATE,
  `valid_until` DATE,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_by` INT COMMENT 'admin_user_id',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  INDEX `idx_code` (`code`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_valid_dates` (`valid_from`, `valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 18. EMAIL_QUEUE (Fila de emails)
-- =============================================

CREATE TABLE `email_queue` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT,
  `to_email` VARCHAR(255) NOT NULL,
  `to_name` VARCHAR(255),
  `subject` VARCHAR(255) NOT NULL,
  `body_html` TEXT NOT NULL,
  `body_text` TEXT,
  `template_name` VARCHAR(100),
  `template_data` JSON,
  `priority` ENUM('low', 'normal', 'high') DEFAULT 'normal',
  `status` ENUM('pending', 'sending', 'sent', 'failed') DEFAULT 'pending',
  `attempts` INT DEFAULT 0,
  `max_attempts` INT DEFAULT 3,
  `error_message` TEXT,
  `sent_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DADOS INICIAIS
-- =============================================

-- Templates padrão
INSERT INTO `templates` (`name`, `slug`, `description`, `category`, `is_active`, `is_premium`, `sort_order`) VALUES
('Clássico Romântico', 'classic-romantic', 'Template elegante com tons pastéis e fontes clássicas', 'classic', TRUE, FALSE, 1),
('Moderno Minimalista', 'modern-minimal', 'Design clean e contemporâneo com foco na simplicidade', 'modern', TRUE, FALSE, 2),
('Rústico Campestre', 'rustic-countryside', 'Estilo rústico com elementos naturais e aconchegantes', 'rustic', TRUE, FALSE, 3);

-- Super Admin (senha: admin123 - hash precisa ser gerado com password_hash())
-- INSERT INTO `admin_users` (`email`, `password_hash`, `full_name`, `role`, `is_active`) VALUES
-- ('admin@meucasamento.com.br', '$2y$10$...', 'Administrador Master', 'super_admin', TRUE);

COMMIT;

-- =============================================
-- VIEWS ÚTEIS
-- =============================================

-- View de estatísticas por tenant
CREATE VIEW `tenant_stats` AS
SELECT 
  t.id AS tenant_id,
  t.domain,
  t.status AS tenant_status,
  w.id AS wedding_id,
  w.status AS wedding_status,
  w.wedding_date,
  COUNT(DISTINCT p.id) AS total_gifts,
  COUNT(DISTINCT CASE WHEN p.status = 'comprado' THEN p.id END) AS gifts_bought,
  COALESCE(SUM(CASE WHEN pt.status = 'confirmado' THEN pt.amount ELSE 0 END), 0) AS total_received,
  COUNT(DISTINCT r.id) AS total_messages,
  s.expires_at AS subscription_expires
FROM tenants t
LEFT JOIN weddings w ON t.id = w.tenant_id
LEFT JOIN presentes p ON w.id = p.wedding_id AND p.deletado = FALSE
LEFT JOIN pix_transactions pt ON w.id = pt.wedding_id
LEFT JOIN recados r ON w.id = r.wedding_id
LEFT JOIN subscriptions s ON t.id = s.tenant_id AND s.payment_status = 'paid'
GROUP BY t.id, w.id, s.id;

-- View de transações recentes
CREATE VIEW `recent_transactions` AS
SELECT 
  pt.id,
  t.domain,
  w.bride_name,
  w.groom_name,
  pt.gift_name,
  pt.amount,
  pt.donor_name,
  pt.status,
  pt.created_at
FROM pix_transactions pt
JOIN weddings w ON pt.wedding_id = w.id
JOIN tenants t ON w.tenant_id = t.id
ORDER BY pt.created_at DESC
LIMIT 100;


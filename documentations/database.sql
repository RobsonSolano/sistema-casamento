-- =====================================================
-- SISTEMA DE PRESENTES PARA CASAMENTO - BANCO DE DADOS
-- =====================================================
-- Script completo para criação do banco de dados
-- Execute este script no seu MySQL/MariaDB

-- =====================================================
-- 1. TABELA DE PRESENTES
-- =====================================================
CREATE TABLE IF NOT EXISTS `presentes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` tinyint NOT NULL DEFAULT 0 COMMENT '0 = disponível, 1 = comprado',
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deletado` tinyint NOT NULL DEFAULT 0 COMMENT '0 = ativo, 1 = deletado',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_deletado` (`deletado`),
  KEY `idx_data_criacao` (`data_criacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- 2. TABELA DE RECADOS
-- =====================================================
CREATE TABLE IF NOT EXISTS `recados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NOT NULL,
    `mensagem` TEXT NOT NULL,
    `data_envio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Índices para melhor performance
CREATE INDEX `idx_recados_data_envio` ON `recados`(`data_envio`);
CREATE INDEX `idx_recados_nome` ON `recados`(`nome`);

-- =====================================================
-- 3. TABELA DE TRANSAÇÕES PIX
-- =====================================================
CREATE TABLE IF NOT EXISTS `pix_transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `gift_id` INT NOT NULL,
  `gift_name` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `pix_code` TEXT NOT NULL,
  `donor_name` VARCHAR(255) NULL,
  `donor_phone` VARCHAR(20) NULL,
  `status` ENUM('iniciado', 'pre_confirmado', 'confirmado', 'cancelled') DEFAULT 'iniciado',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  INDEX `idx_gift_id` (`gift_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`gift_id`) REFERENCES `presentes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- 4. DADOS DE EXEMPLO - PRESENTES
-- =====================================================
INSERT INTO `presentes` (`titulo`, `valor`, `status`, `deletado`) VALUES
('Jogo de Pratos', 299.90, 0, 0),
('Aspirador de Pó', 450.00, 0, 0),
('Cama King Size', 1200.00, 1, 0),
('Smart TV 55"', 2500.00, 0, 0),
('Viagem para Paris', 8000.00, 0, 0),
('Micro-ondas', 350.00, 0, 0),
('Fogão 4 Bocas', 650.00, 0, 0),
('Geladeira Frost Free', 1800.00, 0, 0),
('Máquina de Lavar', 1200.00, 0, 0),
('Sofá 3 Lugares', 1500.00, 0, 0),
('Mesa de Jantar', 800.00, 0, 0),
('Cadeiras', 400.00, 0, 0),
('Cafeteira', 200.00, 0, 0),
('Liquidificador', 150.00, 0, 0),
('Jogo de Copos', 80.00, 0, 0),
('Jogo de Talheres', 120.00, 0, 0),
('Panela de Pressão', 100.00, 0, 0),
('Jogo de Panelas', 300.00, 0, 0),
('Toalhas de Banho', 60.00, 0, 0),
('Roupas de Cama', 200.00, 0, 0);

-- =====================================================
-- 5. DADOS DE EXEMPLO - RECADOS
-- =====================================================
INSERT INTO `recados` (`nome`, `mensagem`, `ip_address`) VALUES
('Maria Silva', 'Parabéns pelo casamento! Que vocês sejam muito felizes! 💕', '127.0.0.1'),
('João Santos', 'Muito feliz por vocês! Que o amor de vocês seja eterno! ❤️', '127.0.0.1'),
('Ana Costa', 'Que momento especial! Desejamos toda felicidade do mundo! 🌟', '127.0.0.1');

-- =====================================================
-- 6. VERIFICAÇÃO DAS TABELAS
-- =====================================================
SELECT 'Tabela presentes criada com sucesso!' as status;
SELECT 'Tabela recados criada com sucesso!' as status;
SELECT 'Tabela pix_transactions criada com sucesso!' as status;

-- =====================================================
-- 7. CONSULTAS ÚTEIS
-- =====================================================

-- Ver todos os presentes disponíveis
-- SELECT * FROM presentes WHERE status = 0 AND deletado = 0 ORDER BY valor ASC;

-- Ver estatísticas de presentes
-- SELECT 
--     COUNT(*) as total_presentes,
--     SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as disponiveis,
--     SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as comprados,
--     SUM(CASE WHEN status = 0 THEN valor ELSE 0 END) as valor_total_disponivel
-- FROM presentes WHERE deletado = 0;

-- Ver transações PIX por status
-- SELECT status, COUNT(*) as quantidade, SUM(amount) as valor_total 
-- FROM pix_transactions 
-- GROUP BY status;

-- Ver recados mais recentes
-- SELECT nome, mensagem, data_envio 
-- FROM recados 
-- ORDER BY data_envio DESC 
-- LIMIT 10;

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================

-- Script SQL para criar a tabela de presentes
-- Banco: pessoal_casamento_mari_douglas

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

-- Inserir alguns dados de exemplo
INSERT INTO `presentes` (`titulo`, `valor`, `status`, `deletado`) VALUES
('Jogo de Pratos', 299.90, 0, 0),
('Aspirador de Pó', 450.00, 0, 0),
('Cama King Size', 1200.00, 1, 0),
('Smart TV 55"', 2500.00, 0, 0),
('Viagem para Paris', 8000.00, 0, 0);

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 28/10/2025 às 12:50
-- Versão do servidor: 8.4.3
-- Versão do PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Banco de dados: `pessoal_casamento_mari_douglas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `pix_transactions`
--

CREATE TABLE `pix_transactions` (
  `id` int NOT NULL,
  `gift_id` int NOT NULL,
  `gift_name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `pix_code` text NOT NULL,
  `donor_name` varchar(255) DEFAULT NULL,
  `donor_phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `status` enum('iniciado','pre_confirmado','confirmado','cancelled') DEFAULT 'iniciado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `presentes`
--

CREATE TABLE `presentes` (
  `id` int NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `status` tinyint NOT NULL COMMENT '0 = "disponível", 1 = "Comprado"',
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deletado` tinyint NOT NULL COMMENT '0 = "ativo", 1 = "Deletado"'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recados`
--

CREATE TABLE `recados` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `pix_transactions`
--
ALTER TABLE `pix_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gift_id` (`gift_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Índices de tabela `presentes`
--
ALTER TABLE `presentes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_imagem` (`imagem`);

--
-- Índices de tabela `recados`
--
ALTER TABLE `recados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recados_data_envio` (`data_envio`),
  ADD KEY `idx_recados_nome` (`nome`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `pix_transactions`
--
ALTER TABLE `pix_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `presentes`
--
ALTER TABLE `presentes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `recados`
--
ALTER TABLE `recados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

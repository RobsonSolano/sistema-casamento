-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 11/09/2025 às 20:49
-- Versão do servidor: 8.4.3
-- Versão do PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pessoal_casamento_mari_douglas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `presentes`
--

CREATE TABLE `presentes` (
  `id` int NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `status` tinyint NOT NULL COMMENT '0 = "disponível", 1 = "Comprado"',
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deletado` tinyint NOT NULL COMMENT '0 = "ativo", 1 = "Deletado"'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `presentes`
--

INSERT INTO `presentes` (`id`, `titulo`, `valor`, `status`, `data_criacao`, `deletado`) VALUES
(1, 'Jogo de Pratos', 299.90, 0, '2025-09-11 09:53:20', 0),
(2, 'Aspirador de Pó', 450.00, 0, '2025-09-11 09:53:20', 0),
(3, 'Cama King Size', 1200.00, 1, '2025-09-11 09:53:20', 0),
(4, 'Smart TV 55\"', 2500.00, 0, '2025-09-11 09:53:20', 0),
(5, 'Viagem para Paris', 8000.00, 0, '2025-09-11 09:53:20', 0),
(6, 'Teste', 10.00, 0, '2025-09-11 09:58:15', 1),
(7, 'Teste flávio', 25.00, 0, '2025-09-11 10:01:09', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `presentes`
--
ALTER TABLE `presentes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `presentes`
--
ALTER TABLE `presentes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

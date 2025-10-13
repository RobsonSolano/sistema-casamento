-- =====================================================
-- MIGRAÇÃO: Adicionar campo de imagem na tabela presentes
-- =====================================================
-- Execute este script para adicionar suporte a imagens nos presentes

ALTER TABLE `presentes` 
ADD COLUMN `imagem` VARCHAR(255) NULL AFTER `valor`,
ADD COLUMN `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP AFTER `data_criacao`;

-- Criar índice para melhor performance
ALTER TABLE `presentes` ADD INDEX `idx_imagem` (`imagem`);

-- Verificar estrutura atualizada
DESCRIBE `presentes`;

-- =====================================================
-- FIM DA MIGRAÇÃO
-- =====================================================


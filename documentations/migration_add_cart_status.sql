-- =====================================================
-- MIGRAÇÃO: Adicionar status "Compra Iniciada"
-- =====================================================
-- Adiciona um novo status para presentes que estão em processo de compra

-- Atualizar comentário da coluna status para incluir o novo status
ALTER TABLE `presentes` 
MODIFY COLUMN `status` tinyint NOT NULL DEFAULT 0 
COMMENT '0 = disponível, 1 = comprado, 2 = compra iniciada';

-- Adicionar índice para melhor performance com o novo status
CREATE INDEX `idx_status_extended` ON `presentes`(`status`, `deletado`);

-- Verificar se a migração foi aplicada corretamente
SELECT 'Migração aplicada com sucesso! Status disponíveis: 0=disponível, 1=comprado, 2=compra iniciada' as status;

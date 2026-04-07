-- ============================================
-- MIGRAÇÃO: Correções de colunas
-- ============================================

-- Backup da tabela caixa (apenas colunas que existem)
CREATE TABLE IF NOT EXISTS caixa_backup AS SELECT * FROM caixa;

-- Dropar tabela antiga
DROP TABLE IF EXISTS caixa;

-- Criar nova tabela com estrutura completa
CREATE TABLE caixa (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tipo TEXT NOT NULL,
    descricao TEXT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_movimento DATE NOT NULL DEFAULT CURRENT_DATE,
    categoria TEXT,
    forma_pagamento_id INTEGER,
    conta_receber_id INTEGER,
    observacoes TEXT,
    usuario_id INTEGER,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (forma_pagamento_id) REFERENCES formas_pagamento(id),
    FOREIGN KEY (conta_receber_id) REFERENCES contas_receber(id),
    FOREIGN KEY (usuario_id) REFERENCES users(id)
);

-- Inserir dados básicos (sem especificar colunas que podem não existir no backup)
INSERT INTO caixa (tipo, descricao, valor, data_movimento)
SELECT tipo, descricao, valor, DATE('now') FROM caixa_backup;

-- Dropar backup
DROP TABLE caixa_backup;

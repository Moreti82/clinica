-- ============================================
-- TABELAS DE CONVÊNIOS
-- ============================================

-- Tabela: convenios
CREATE TABLE IF NOT EXISTS convenios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    razao_social TEXT,
    cnpj TEXT,
    telefone TEXT,
    email TEXT,
    desconto_padrao DECIMAL(5,2) DEFAULT 0,
    observacoes TEXT,
    ativo INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: procedimentos_convenio (preços por convênio)
CREATE TABLE IF NOT EXISTS procedimentos_convenio (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    convenio_id INTEGER NOT NULL,
    procedimento_id INTEGER NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    observacoes TEXT,
    FOREIGN KEY (convenio_id) REFERENCES convenios(id) ON DELETE CASCADE,
    FOREIGN KEY (procedimento_id) REFERENCES procedimentos(id) ON DELETE CASCADE,
    UNIQUE(convenio_id, procedimento_id)
);

-- Tabela: paciente_convenio (vínculo paciente x convênio)
CREATE TABLE IF NOT EXISTS paciente_convenio (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    convenio_id INTEGER NOT NULL,
    numero_carteirinha TEXT,
    validade_carteirinha DATE,
    titular TEXT,
    ativo INTEGER DEFAULT 1,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (convenio_id) REFERENCES convenios(id) ON DELETE CASCADE
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_convenios_nome ON convenios(nome);
CREATE INDEX IF NOT EXISTS idx_proc_conv_convenio ON procedimentos_convenio(convenio_id);
CREATE INDEX IF NOT EXISTS idx_proc_conv_procedimento ON procedimentos_convenio(procedimento_id);
CREATE INDEX IF NOT EXISTS idx_pac_conv_paciente ON paciente_convenio(paciente_id);

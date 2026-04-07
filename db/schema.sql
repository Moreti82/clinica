-- ============================================
-- SCHEMA COMPLETO - OdontoCare ERP
-- ============================================

-- Tabela: pacientes (já existe, mantendo estrutura)
CREATE TABLE IF NOT EXISTS pacientes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    cpf TEXT UNIQUE,
    telefone TEXT,
    email TEXT,
    data_nascimento DATE,
    endereco TEXT,
    observacoes TEXT,
    ativo INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_cadastro DATE DEFAULT CURRENT_DATE
);

-- Tabela: profissionais (já existe)
CREATE TABLE IF NOT EXISTS profissionais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    cro TEXT,
    especialidade TEXT,
    telefone TEXT,
    ativo INTEGER DEFAULT 1
);

-- Tabela: procedimentos (já existe)
CREATE TABLE IF NOT EXISTS procedimentos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    descricao TEXT NOT NULL,
    valor_padrao DECIMAL(10,2) DEFAULT 0,
    observacoes TEXT
);

-- Tabela: perfis (já existe)
CREATE TABLE IF NOT EXISTS perfis (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    perfil TEXT NOT NULL UNIQUE
);

-- Tabela: users (já existe)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    perfil_id INTEGER,
    ativo INTEGER DEFAULT 1,
    FOREIGN KEY (perfil_id) REFERENCES perfis(id)
);

-- Tabela: agendamentos (já existe)
CREATE TABLE IF NOT EXISTS agendamentos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    profissional_id INTEGER NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    observacoes TEXT,
    status TEXT DEFAULT 'Agendado',
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

-- ============================================
-- NOVAS TABELAS - FASE 2: PRONTUÁRIO
-- ============================================

-- Tabela: anamneses (Ficha de saúde do paciente)
CREATE TABLE IF NOT EXISTS anamneses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL UNIQUE,
    queixa_principal TEXT,
    historico_medico TEXT,
    historico_familiar TEXT,
    medicamentos TEXT,
    alergias TEXT,
    habitos TEXT,
    pressao_arterial TEXT,
    diabete INTEGER DEFAULT 0,
    problema_cardiaco INTEGER DEFAULT 0,
    gravida INTEGER DEFAULT 0,
    observacoes TEXT,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);

-- Tabela: prontuarios (Atendimentos)
CREATE TABLE IF NOT EXISTS prontuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    profissional_id INTEGER NOT NULL,
    agendamento_id INTEGER,
    data_atendimento DATE NOT NULL,
    hora_atendimento TIME,
    queixa TEXT,
    diagnostico TEXT,
    procedimentos_realizados TEXT,
    prescricao TEXT,
    observacoes TEXT,
    data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id),
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id)
);

-- Tabela: documentos_paciente
CREATE TABLE IF NOT EXISTS documentos_paciente (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    tipo TEXT NOT NULL,
    descricao TEXT,
    arquivo_path TEXT NOT NULL,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);

-- Tabela: odontograma
CREATE TABLE IF NOT EXISTS odontograma (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    dente INTEGER NOT NULL,
    face TEXT,
    condicao TEXT NOT NULL DEFAULT 'Saudavel',
    procedimento_id INTEGER,
    cor TEXT DEFAULT '#FFFFFF',
    observacoes TEXT,
    data_registro DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (procedimento_id) REFERENCES procedimentos(id)
);

-- ============================================
-- NOVAS TABELAS - FASE 3: FINANCEIRO
-- ============================================

-- Tabela: formas_pagamento
CREATE TABLE IF NOT EXISTS formas_pagamento (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    descricao TEXT NOT NULL,
    ativo INTEGER DEFAULT 1
);

-- Inserir formas de pagamento padrão
INSERT OR IGNORE INTO formas_pagamento (descricao) VALUES 
('Dinheiro'), ('Pix'), ('Cartão de Débito'), ('Cartão de Crédito'), ('Boleto'), ('Transferência');

-- Tabela: contas_receber
CREATE TABLE IF NOT EXISTS contas_receber (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    descricao TEXT NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    valor_recebido DECIMAL(10,2) DEFAULT 0,
    data_vencimento DATE NOT NULL,
    data_recebimento DATE,
    status TEXT DEFAULT 'Pendente',
    forma_pagamento_id INTEGER,
    observacoes TEXT,
    orcamento_id INTEGER,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (forma_pagamento_id) REFERENCES formas_pagamento(id)
);

-- Tabela: caixa (movimentação)
CREATE TABLE IF NOT EXISTS caixa (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tipo TEXT NOT NULL,
    descricao TEXT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_movimento DATE NOT NULL,
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

-- ============================================
-- NOVAS TABELAS - FASE 4: ORÇAMENTOS
-- ============================================

-- Tabela: orcamentos
CREATE TABLE IF NOT EXISTS orcamentos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    profissional_id INTEGER NOT NULL,
    data_orcamento DATE DEFAULT CURRENT_DATE,
    valor_total DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) DEFAULT 0,
    valor_final DECIMAL(10,2) NOT NULL,
    status TEXT DEFAULT 'Pendente',
    observacoes TEXT,
    validade_dias INTEGER DEFAULT 30,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

-- Tabela: orcamento_itens
CREATE TABLE IF NOT EXISTS orcamento_itens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    orcamento_id INTEGER NOT NULL,
    procedimento_id INTEGER NOT NULL,
    quantidade INTEGER DEFAULT 1,
    valor_unitario DECIMAL(10,2) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    dente TEXT,
    face TEXT,
    FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (procedimento_id) REFERENCES procedimentos(id)
);

-- ============================================
-- NOVAS TABELAS - FASE 6: ESTOQUE
-- ============================================

-- Tabela: produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    codigo TEXT UNIQUE,
    descricao TEXT NOT NULL,
    categoria TEXT,
    unidade TEXT DEFAULT 'un',
    quantidade_minima DECIMAL(10,2) DEFAULT 0,
    quantidade_atual DECIMAL(10,2) DEFAULT 0,
    valor_custo DECIMAL(10,2),
    valor_venda DECIMAL(10,2),
    fornecedor TEXT,
    ativo INTEGER DEFAULT 1
);

-- Tabela: estoque_movimentacao
CREATE TABLE IF NOT EXISTS estoque_movimentacao (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    produto_id INTEGER NOT NULL,
    tipo TEXT NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    motivo TEXT,
    usuario_id INTEGER,
    data_movimento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (usuario_id) REFERENCES users(id)
);

-- Tabela: procedimento_produtos
CREATE TABLE IF NOT EXISTS procedimento_produtos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    procedimento_id INTEGER NOT NULL,
    produto_id INTEGER NOT NULL,
    quantidade_usada DECIMAL(10,2) DEFAULT 1,
    FOREIGN KEY (procedimento_id) REFERENCES procedimentos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- ============================================
-- NOVAS TABELAS - FASE 9: CONFIGURAÇÕES
-- ============================================

-- Tabela: configuracoes
CREATE TABLE IF NOT EXISTS configuracoes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    chave TEXT UNIQUE NOT NULL,
    valor TEXT,
    descricao TEXT
);

-- Inserir configurações padrão
INSERT OR IGNORE INTO configuracoes (chave, valor, descricao) VALUES
('clinica_nome', 'OdontoCare', 'Nome da clínica'),
('clinica_endereco', '', 'Endereço completo'),
('clinica_telefone', '', 'Telefone de contato'),
('clinica_email', '', 'E-mail da clínica'),
('clinica_cnpj', '', 'CNPJ da clínica'),
('horario_inicio', '07:30', 'Horário de início dos atendimentos'),
('horario_fim', '20:00', 'Horário de fim dos atendimentos'),
('intervalo_agenda', '30', 'Intervalo entre agendamentos (minutos)'),
('dias_antecedencia_lembrete', '1', 'Dias de antecedência para lembrete de consulta');

-- ============================================
-- ÍNDICES PARA PERFORMANCE
-- ============================================

CREATE INDEX IF NOT EXISTS idx_agendamentos_data ON agendamentos(data);
CREATE INDEX IF NOT EXISTS idx_agendamentos_paciente ON agendamentos(paciente_id);
CREATE INDEX IF NOT EXISTS idx_agendamentos_profissional ON agendamentos(profissional_id);
CREATE INDEX IF NOT EXISTS idx_prontuarios_paciente ON prontuarios(paciente_id);
CREATE INDEX IF NOT EXISTS idx_contas_receber_paciente ON contas_receber(paciente_id);
CREATE INDEX IF NOT EXISTS idx_contas_receber_vencimento ON contas_receber(data_vencimento);
CREATE INDEX IF NOT EXISTS idx_orcamentos_paciente ON orcamentos(paciente_id);
CREATE INDEX IF NOT EXISTS idx_odontograma_paciente ON odontograma(paciente_id);

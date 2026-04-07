-- ============================================
-- MIGRAÇÃO: Adicionar colunas faltantes em prontuarios
-- ============================================

-- Dropar tabela antiga se existir
DROP TABLE IF EXISTS prontuarios;

-- Criar nova tabela com estrutura completa
CREATE TABLE prontuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    profissional_id INTEGER NOT NULL,
    agendamento_id INTEGER,
    data_atendimento DATE NOT NULL DEFAULT CURRENT_DATE,
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

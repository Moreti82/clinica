<?php
require_once 'controller.php';

$dataSelecionada = $_GET['data'] ?? date('Y-m-d');

$agendamentos = listarAgendamentosPorData($db, $dataSelecionada);

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Agendamentos</title>
        <link rel="stylesheet" href="../assets/css/style-agendamentos.css">
    </head>

    <body id="agendamentos-page">

        <div class="agendamentos-container">

            <h2 class="agendamentos-title">
                Agendamentos do Dia (<?= date('d/m/Y', strtotime($dataSelecionada)) ?>)
            </h2>

            <div class="agendamentos-menu">
                <a href="novo.php?data=<?= $dataSelecionada ?>" class="agendamentos-btn">➕ Novo Agendamento</a>
                <a href="calendario.php" class="agendamentos-btn-sec">⬅ Voltar</a>
            </div>

            <div class="agendamentos-table-wrapper">
                <table class="agendamentos-table">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Profissional</th>
                            <th>Observações</th>
                            <th>Status</th>
                            <th>Ações</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agendamentos as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['hora']) ?></td>
                            <td><?= htmlspecialchars($a['paciente']) ?></td>
                            <td><?= htmlspecialchars($a['profissional']) ?></td>
                            <td><?= htmlspecialchars($a['observacoes']) ?></td>
                            <td><?= htmlspecialchars($a['status']) ?></td>
                            <td>
                                <button 
                                    type="button" 
                                    class="abrir-modal" 
                                    data-id="<?= $a['id'] ?>" 
                                    data-nome="<?= htmlspecialchars($a['paciente']) ?>">
                                    Alterar Status
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>

                <div id="modal-status" class="modal" style="display:none;">
                    <div class="modal-content">
                        <span class="fechar">&times;</span>
                        <h3 id="modal-titulo"></h3>
                        <form id="form-status"  method="post" action="alterar-status.php" onsubmit="fecharModal()">
                            <input type="hidden" name="agendamento_id" id="agendamento-id">
                            <label for="status">Novo Status:</label>
                            <select name="status" id="status">
                                <option value="Agendado">Agendado</option>
                                <option value="Confirmado">Confirmado</option>
                                <option value="Cancelado">Cancelado</option>
                                <option value="Concluído">Concluído</option>
                            </select>
                            <br><br>
                            <button type="submit">Salvar</button>
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </body>

    <script src="../assets/js/agendamentos.js"></script>
</html>
<?php
require_once '../config/conexao.php';


/*
|--------------------------------------------------------------------------
| 1️⃣ Se clicou em SALVAR → redireciona mantendo POST (307)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    header("Location: salvar.php", true, 307);
    exit;
}

/*
|--------------------------------------------------------------------------
| 2️⃣ Data selecionada (via POST ou padrão hoje)
|--------------------------------------------------------------------------
*/
$dataSelecionada = $_POST['data'] ?? $_GET['data'] ?? date('Y-m-d');

/*
|--------------------------------------------------------------------------
| 3️⃣ Buscar pacientes
|--------------------------------------------------------------------------
*/
$pacientes = $db->query("SELECT id, nome FROM pacientes ORDER BY nome")
                ->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| 4️⃣ Buscar profissionais
|--------------------------------------------------------------------------
*/
$profissionais = $db->query("SELECT id, nome FROM profissionais ORDER BY nome")
                    ->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| 5️⃣ Buscar horários ocupados na data
|--------------------------------------------------------------------------
*/

$stmt = $db->prepare("SELECT hora FROM agendamentos WHERE data = ?");
$stmt->execute([$dataSelecionada]);
$horariosOcupados = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Novo Agendamento</title>
        <link rel="stylesheet" href="../assets/css/style-novo-agendamento.css">
    </head>
    <body id="agendamento-novo-page">

        <div class="agendamento-novo-container">

            <h2 class="agendamento-novo-title">Novo Agendamento</h2>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="msg-sucesso mensagem-alerta">
                    ✅ Agendamento salvo com sucesso!
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="msg-erro mensagem-alerta">
                    ❌ Erro ao salvar. Verifique os dados.
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="agendamento-novo-form">

                <!-- PACIENTE -->
                <div class="form-group">
                    <label>Paciente:</label>
                    <select name="paciente_id" required>
                        <option value="">Selecione</option>
                        <?php foreach ($pacientes as $p): ?>
                            <option value="<?= $p['id'] ?>"
                                <?= ($_POST['paciente_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- PROFISSIONAL -->
                <div class="form-group">
                    <label>Profissional:</label>
                    <select name="profissional_id" required>
                        <option value="">Selecione</option>
                        <?php foreach ($profissionais as $prof): ?>
                            <option value="<?= $prof['id'] ?>"
                                <?= ($_POST['profissional_id'] ?? '') == $prof['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prof['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group-row">

                    <!-- DATA -->
                    <div class="form-group">
                        <label>Data:</label>
                        <input type="date"
                        name="data"
                        value="<?= $dataSelecionada ?>"
                        onchange="this.form.submit()"
                        required>
                    </div>

                    <!-- HORA -->
                    <div class="form-group">
                        <label>Hora:</label>
                        <select name="hora" required>
                            <option value="">Selecione</option>

                            <?php
                            $inicio = strtotime("07:30");
                            $fim = strtotime("20:00");

                            for ($hora = $inicio; $hora <= $fim; $hora += 1800) {

                                $horaFormatada = date("H:i", $hora);

                                if (!in_array($horaFormatada, $horariosOcupados)) {

                                    $selected = ($_POST['hora'] ?? '') == $horaFormatada ? 'selected' : '';

                                    echo "<option value='$horaFormatada' $selected>
                                            $horaFormatada
                                        </option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <?php
                        $statusAtual = $_POST['status'] ?? 'Agendado';
                        $statusLista = ['Agendado','Confirmado','Cancelado','Concluído'];

                        foreach ($statusLista as $st) {
                            $selected = ($statusAtual == $st) ? 'selected' : '';
                            echo "<option value='$st' $selected>$st</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- OBSERVAÇÕES -->
                <div class="form-group">
                    <label>Observações:</label>
                    <textarea name="observacoes" rows="4"><?= $_POST['observacoes'] ?? '' ?></textarea>
                </div>

                <!-- AÇÕES -->
                <div class="form-actions">
                    <button type="submit" name="salvar" class="btn-salvar">
                        Salvar
                    </button>
                    <a href="calendario.php" class="btn-voltar">
                        Voltar
                    </a>
                </div>
            </form>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const mensagens = document.querySelectorAll(".mensagem-alerta");

                if (mensagens.length > 0) {

                    setTimeout(() => {

                        mensagens.forEach(function(mensagem) {
                            mensagem.classList.add("sumir");

                            setTimeout(() => {
                                mensagem.remove();
                            }, 500);
                        });

                    }, 3000);
                }

            });
        </script>

    </body>
</html>

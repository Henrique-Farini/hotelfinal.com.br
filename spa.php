<?php
// ---- PROCESSAMENTO DO FORMULÁRIO ----
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cliente = $_POST['cliente'];
    $servico = $_POST['servico'];
    $valor = $_POST['valor'];
    $data = $_POST['data'];

    // Conexão com banco (ajuste para o seu)
    $conn = new mysqli("localhost", "root", "", "hotel");

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    $sql = "INSERT INTO spa_consumos (cliente, servico, valor, data_consumo)
            VALUES ('$cliente', '$servico', '$valor', '$data')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green'>Serviço registrado com sucesso!</p>";
    } else {
        echo "<p style='color:red'>Erro: " . $conn->error . "</p>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Registrar Consumo - SPA</title>
<style>
    body { font-family: Arial; margin: 40px; }
    form { width: 350px; padding: 20px; border: 1px solid #ccc; border-radius: 10px; }
    input, select {
        width: 100%; padding: 10px; margin-top: 10px;
        border-radius: 6px; border: 1px solid #aaa;
    }
    button {
        margin-top: 15px; padding: 10px;
        width: 100%; background: #28a745; color: #fff;
        border: none; border-radius: 6px; cursor: pointer;
    }
    button:hover { background: #218838; }
</style>
</head>
<body>

<h2>Registrar Serviço / Consumo do SPA</h2>

<form action="spa.php" method="POST">

    <label>Nome do Cliente:</label>
    <input type="text" name="cliente" required>

    <label>Serviço do SPA:</label>
    <select name="servico" required>
        <option value="">Selecione...</option>
        <option value="Massagem Relaxante">Massagem Relaxante</option>
        <option value="Massagem Terapêutica">Massagem Terapêutica</option>
        <option value="Hidroterapia">Hidroterapia</option>
        <option value="Sauna">Sauna</option>
        <option value="Limpeza de Pele">Limpeza de Pele</option>
    </select>

    <label>Valor (R$):</label>
    <input type="number" name="valor" step="0.01" required>

    <label>Data do Serviço:</label>
    <input type="date" name="data" required>

    <button type="submit">Registrar Consumo</button>

</form>

</body>
</html>

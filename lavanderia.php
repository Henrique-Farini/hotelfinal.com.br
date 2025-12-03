<?php
// Carrega lista de hóspedes ativos (check-ins)
$checkinsFile = "checkins.json";
$checkins = file_exists($checkinsFile) ? json_decode(file_get_contents($checkinsFile), true) : [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Serviços de Lavanderia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }
        .container {
            max-width: 450px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        label { margin-top: 10px; display: block; font-weight: bold; }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #aaa;
        }
        button {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background: #1a73e8;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background: #1558b0; }
    </style>
</head>
<body>

<div class="container">
    <h2>Registrar Serviço de Lavanderia</h2>

    <form action="salvar_lavanderia.php" method="POST">

        <label>Selecione o Hóspede</label>
        <select name="id_checkin" required>
            <option value="">-- Escolher --</option>
            <?php foreach ($checkins as $c): ?>
                <option value="<?= $c['id'] ?>">
                    <?= $c['nome'] ?> (Quarto <?= $c['quarto'] ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label>Tipo de Serviço</label>
        <select name="servico" required>
            <option value="">-- Escolher --</option>
            <option value="Lavagem">Lavagem</option>
            <option value="Secagem">Secagem</option>
            <option value="Passadoria">Passadoria</option>
            <option value="Lavagem + Secagem">Lavagem + Secagem</option>
        </select>

        <label>Valor (R$)</label>
        <input type="number" step="0.01" name="valor" required>

        <label>Observações (opcional)</label>
        <textarea name="obs" rows="3"></textarea>

        <button type="submit">Registrar Serviço</button>
    </form>
</div>

</body>
</html>

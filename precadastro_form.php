<?php
// precadastro_form.php
// Formulário de pré-cadastro do hóspede
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pré-Cadastro de Hóspede</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }
        .container {
            width: 400px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border-radius: 4px;
            border: 1px solid #aaa;
        }
        button {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            border: none;
            background: #4CAF50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Pré-Cadastro de Hóspede</h2>

    <form action="precadastro_save.php" method="POST">

        <label>Nome do Hóspede:</label>
        <input type="text" name="nome" required>

        <label>Documento (RG/CPF/Passaporte):</label>
        <input type="text" name="documento" required>

        <label>Horário de Chegada:</label>
        <input type="time" name="horario" required>

        <label>Placa do Veículo:</label>
        <input type="text" name="placa" placeholder="ABC-1234">

        <button type="submit">Registrar Chegada</button>
    </form>
</div>

</body>
</html>
<?php
session_start();

// Se quiser proteger para só gerente acessar, mantenha isso aqui.
// Caso não use login ainda, pode remover.
if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] !== 'gerente') {
    // header("Location: ../index.php");
    // exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: #0077ff;
            border: none;
            color: white;
            font-size: 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #005ec4;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Cadastrar Usuário</h2>

    <form action="salvar_usuario.php" method="POST">

        <label>Nome:</label>
        <input type="text" name="nome" required>

        <label>Cargo / Setor:</label>
        <select name="cargo" required>
            <option value="">Selecione...</option>
            <option value="manobrista">Manobrista</option>
            <option value="recepcao">Recepção</option>
            <option value="servicos">Serviços Internos</option>
            <option value="gerente">Gerente</option>
        </select>

        <label>Login:</label>
        <input type="text" name="login" required>

        <label>Senha:</label>
        <input type="password" name="senha" required>

        <button type="submit">Cadastrar</button>
    </form>
</div>

</body>
</html>

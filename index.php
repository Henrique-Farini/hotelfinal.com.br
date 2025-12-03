<?php
session_start();

// Caminho do arquivo de usuários
$usuariosFile = __DIR__ . "/data/usuarios.json";

// Carrega usuários do JSON
$usuarios = [];
if (file_exists($usuariosFile)) {
    $usuarios = json_decode(file_get_contents($usuariosFile), true);
}

// Se o formulário foi enviado
$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["login"] ?? "");
    $senha = trim($_POST["senha"] ?? "");

    // Validação simples
    if ($login === "" || $senha === "") {
        $erro = "Preencha todos os campos!";
    } else {
        $usuarioEncontrado = null;

        // Verifica usuário no JSON
        foreach ($usuarios as $u) {
            if ($u["login"] === $login && password_verify($senha, $u["senha"])) {
                $usuarioEncontrado = $u;
                break;
            }
        }

        if ($usuarioEncontrado) {
            // Guarda os dados do usuário na sessão
            $_SESSION["usuario"] = [
                "id" => $usuarioEncontrado["id_usuario"],
                "nome" => $usuarioEncontrado["nome"],
                "cargo" => strtolower($usuarioEncontrado["cargo"])
            ];

            // Redireciona para o painel (dashboard)
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = "Login ou senha incorretos!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SIGH-CE - Login</title>
    <style>
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .login-box {
            width: 340px;
            margin: 120px auto;
            padding: 25px;
            background: white;
            box-shadow: 0 0 10px #aaa;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #bbb;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .erro {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>SIGH-CE - Login</h2>

    <?php if ($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="login" placeholder="Usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>

        <button type="submit">Entrar</button>
    </form>
</div>

</body>
</html>

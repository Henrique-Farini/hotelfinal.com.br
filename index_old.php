<?php
//beatriz

session_start();


$usuarios = [
    "admin@email.com" => ["senha" => "123", "cargo" => "admin"],
    "gerente@email.com" => ["senha" => "123", "cargo" => "gerente"],
    "func@email.com" => ["senha" => "123", "cargo" => "funcionario"],
];

if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_SESSION["email"])) {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    if (isset($usuarios[$email]) && $usuarios[$email]["senha"] === $senha) {
        $_SESSION["email"] = $email;
        $_SESSION["cargo"] = $usuarios[$email]["cargo"];
        header("Location: index.php");
        exit;
    } else {
        $erro = "Credenciais inválidas!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Hotel</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }

        .top-bar {
            background: #0a1a52;
            padding: 15px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 20px;
            color: white;
        }

        .top-bar button {
            background: #05227e;
            border: none;
            padding: 10px 16px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }

        .top-bar .login-btn {
            background: #0033cc;
            display: flex;
            align-items: center;
            gap: 8px;
        }

    
        .promo {
            background: white;
            padding: 20px;
            border-bottom: 3px solid #cdd7f3;
        }

        .promo h2 {
            margin: 0;
            color: #0a1a52;
        }

        .promo small {
            color: #555;
        }

    
        .search-bar {
            display: flex;
            background: #e9e9e9;
            margin: 15px;
            padding: 15px;
            border-radius: 12px;
            justify-content: space-between;
        }

        .search-item {
            text-align: left;
        }

        .search-item h4 {
            margin: 4px 0;
        }

        .tabs {
            background: #0a1a52;
            color: white;
            display: flex;
            padding: 10px 0;
            justify-content: space-around;
            font-size: 14px;
        }

        .big-box-container {
            margin: 20px;
            display: flex;
            gap: 20px;
        }

        .big-box {
            background: white;
            height: 180px;
            flex: 1;
            border-radius: 6px;
            box-shadow: 0 0 6px #0002;
        }

        .mini-cards {
            display: flex;
            gap: 15px;
            margin: 20px;
        }

        .mini {
            background: white;
            width: 150px;
            height: 120px;
            border-radius: 6px;
            box-shadow: 0 0 6px #0002;
        }

        .login-box {
            background: white;
            padding: 25px;
            width: 300px;
            margin: 100px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px #00000022;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #999;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #0033cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .erro {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .logout {
            margin: 20px;
            color: #0033cc;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>

<?php if (!isset($_SESSION["email"])): ?>

    <!-- TELA DE LOGIN -->
    <div class="login-box">
        <h2>Login</h2>

        <?php if (!empty($erro)) echo "<p class='erro'>$erro</p>"; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>

<?php else: ?>

    <!-- TOPO -->
    <div class="top-bar">
        <button>☰ Menu</button>
        <button class="login-btn">🔍 Fazer login</button>
    </div>

    <!-- PROMO -->
    <div class="promo">
        <h2>Além da Black Friday: economize até 50%</h2>
        <small>A melhor rede de hotéis é aqui</small>
    </div>

    <!-- BARRA DE BUSCA -->
    <div class="search-bar">
        <div class="search-item">
            <small>Destino</small>
            <h4>Pra onde</h4>
        </div>
        <div class="search-item">
            <small>Entrada / saída</small>
            <h4>Selecionar datas</h4>
        </div>
        <div class="search-item">
            <small>Hóspedes e quartos</small>
            <h4>6 hóspedes, 3 quartos</h4>
        </div>
    </div>

    <!-- ABAS -->
    <div class="tabs">
        <span>Pré Cadastro</span>
        <span>Hóspedes ativos</span>
        <span>Histórico</span>
        <span>Check-in</span>
        <span>Check-out</span>
        <span>Relatório</span>
    </div>

    <!-- ÁREA PRINCIPAL -->
    <div class="big-box-container">
        <div class="big-box"></div>
        <div class="big-box"></div>
    </div>

    <!-- MINI CARDS -->
    <div class="mini-cards">
        <div class="mini"></div>
        <div class="mini"></div>
        <div class="mini"></div>
        <div class="mini"></div>
        <div class="mini"></div>
    </div>

    <a class="logout" href="?logout=1">Sair</a>

<?php endif; ?>

</body>
</html>

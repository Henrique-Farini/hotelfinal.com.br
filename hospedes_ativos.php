<?php
session_start();
//Gabriela Giglio 

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Caminho do arquivo JSON de check-ins
$arquivo = "../data/checkins.json";

// Carrega dados do JSON
if (file_exists($arquivo)) {
    $json = file_get_contents($arquivo);
    $hospedes = json_decode($json, true);
} else {
    $hospedes = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Hóspedes Ativos</title>

    <!-- Link para o CSS (colocar arquivo em ../assets/css/hospedes_ativos.css) -->
    <link rel="stylesheet" href="../assets/css/hospedes_ativos.css">

    <!-- Mensagem caso o navegador não carregue CSS -->
    <noscript>
        <style>
            /* Estilo mínimo inline só para quando o CSS externo não carregar */
            body { font-family: Arial, sans-serif; background: #fff; color:#000; padding:12px; }
            .ha-table { width:100%; border-collapse:collapse; }
            .ha-table th, .ha-table td { border:1px solid #ccc; padding:8px; text-align:left; }
            .ha-btn { display:inline-block; padding:6px 10px; background:#333; color:#fff; text-decoration:none; border-radius:4px; }
        </style>
    </noscript>
</head>
<body>

<main id="ha-app" class="ha-container">
    <header class="ha-header">
        <h1 class="ha-title">Hóspedes Ativos</h1>
        <a href="../dashboard.php" class="ha-btn ha-btn-back" aria-label="Voltar ao painel">⬅ Voltar</a>
    </header>

    <section class="ha-content">
        <table class="ha-table" role="table" aria-label="Lista de hóspedes ativos">
            <thead>
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Documento</th>
                    <th scope="col">Quarto</th>
                    <th scope="col">Data de Entrada</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($hospedes)) : ?>
                    <tr>
                        <td colspan="5" class="ha-no-records">Nenhum hóspede ativo no momento.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($hospedes as $hospede) : ?>
                        <?php if (isset($hospede["status"]) && $hospede["status"] === "ativo") : ?>
                            <tr>
                                <td><?= htmlspecialchars($hospede["nome"] ?? '—') ?></td>
                                <td><?= htmlspecialchars($hospede["documento"] ?? '—') ?></td>
                                <td><?= htmlspecialchars($hospede["quarto"] ?? '—') ?></td>
                                <td><?= htmlspecialchars($hospede["data_entrada"] ?? '—') ?></td>
                                <td>
                                    <a class="ha-btn ha-btn-action" href="checkout.php?id=<?= urlencode($hospede["id"] ?? '') ?>">
                                        Finalizar Check-out
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

</body>
</html>

<?php
session_start();

// Verifica permissão (apenas gerente)
if (!isset($_SESSION["cargo"]) || $_SESSION["cargo"] !== "gerencia") {
    echo "<p>Acesso negado. Apenas gerentes podem visualizar este relatório.</p>";
    exit;
}

// Caminhos dos arquivos JSON
$arquivoConsumos = __DIR__ . "/../data/consumos.json";
$arquivoServicos = __DIR__ . "/../data/servicos.json";

// Carrega dados
$consumos = file_exists($arquivoConsumos) ? json_decode(file_get_contents($arquivoConsumos), true) : [];
$servicos = file_exists($arquivoServicos) ? json_decode(file_get_contents($arquivoServicos), true) : [];

// Indexa serviços por ID para facilitar busca
$mapServicos = [];
foreach ($servicos as $s) {
    $mapServicos[$s["id_servico"]] = $s;
}

// Tabela consolidação
$relatorio = [];

// Calcula totais
foreach ($consumos as $c) {

    $id = $c["id_servico"];

    // Ignora caso o serviço não exista
    if (!isset($mapServicos[$id])) continue;

    $serv = $mapServicos[$id];

    $nome = $serv["nome"];
    $categoria = $serv["categoria"];
    $preco = floatval($serv["preco"]);
    $qtd = intval($c["quantidade"]);

    // Inicializa se não existir
    if (!isset($relatorio[$categoria])) {
        $relatorio[$categoria] = [
            "quantidade" => 0,
            "valor" => 0
        ];
    }

    // Soma
    $relatorio[$categoria]["quantidade"] += $qtd;
    $relatorio[$categoria]["valor"] += ($qtd * $preco);
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Serviços</title>
    <style>
        body { font-family: Arial; background:#f6f6f6; padding:20px; }
        h1 { text-align:center; margin-bottom:30px; }
        table { width:80%; margin:0 auto; border-collapse:collapse; background:white; }
        th, td { padding:10px; border:1px solid #ccc; text-align:center; }
        th { background:#333; color:white; }
        .total { font-weight:bold; background:#e8e8e8; }
    </style>
</head>
<body>

<h1>Relatório de Serviços Utilizados</h1>

<table>
    <tr>
        <th>Categoria</th>
        <th>Total de Usos</th>
        <th>Valor Total (R$)</th>
    </tr>

    <?php 
    $somaGeral = 0;

    foreach ($relatorio as $categoria => $dados): 
        $somaGeral += $dados["valor"];
    ?>
        <tr>
            <td><?= ucfirst($categoria) ?></td>
            <td><?= $dados["quantidade"] ?></td>
            <td>R$ <?= number_format($dados["valor"], 2, ",", ".") ?></td>
        </tr>
    <?php endforeach; ?>

    <tr class="total">
        <td colspan="2">TOTAL GERAL</td>
        <td>R$ <?= number_format($somaGeral, 2, ",", ".") ?></td>
    </tr>
</table>

</body>
</html>

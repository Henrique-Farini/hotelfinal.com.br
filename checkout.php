<?php
//Gabi Barreto//
session_start();

// Verifica login
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Arquivos do sistema
$arquivo_checkins   = "../data/checkins.json";
$arquivo_hospedes   = "../data/hospedes.json";
$arquivo_quartos    = "../data/quartos.json";
$arquivo_consumos   = "../data/consumos.json";
$arquivo_servicos   = "../data/servicos.json";

// Carregar JSONs
$checkins  = json_decode(file_get_contents($arquivo_checkins), true);
$hospedes  = json_decode(file_get_contents($arquivo_hospedes), true);
$quartos   = json_decode(file_get_contents($arquivo_quartos), true);
$consumos  = json_decode(file_get_contents($arquivo_consumos), true);
$servicos  = json_decode(file_get_contents($arquivo_servicos), true);

// ID do check-in
$id_checkin = $_GET['id_checkin'] ?? null;
if (!$id_checkin) { die("Check-in inválido."); }

// Buscar check-in
$checkin = null;
foreach ($checkins as $c) {
    if ($c['id_checkin'] == $id_checkin) {
        $checkin = $c;
        break;
    }
}

if (!$checkin) { die("Check-in não encontrado."); }

// Buscar hóspede
$hospede = null;
foreach ($hospedes as $h) {
    if ($h['id_hospede'] == $checkin['id_hospede']) {
        $hospede = $h;
        break;
    }
}

// Buscar quarto
$quarto = null;
foreach ($quartos as $q) {
    if ($q['id_quarto'] == $checkin['id_quarto']) {
        $quarto = $q;
        break;
    }
}

// Calcular diárias
$dataEntrada = new DateTime($checkin['data_hora_entrada']);
$dataSaida   = new DateTime(); // agora
$intervalo   = $dataEntrada->diff($dataSaida);
$dias        = max(1, $intervalo->days);

$valorDiarias = $dias * $quarto['preco_diaria'];

// Buscar consumos do hóspede
$consumosHospede = array_filter($consumos, function ($c) use ($id_checkin) {
    return $c['id_checkin'] == $id_checkin;
});

// Somar consumos
$valorServicos = 0;
foreach ($consumosHospede as $c) {
    // puxar nome e preço do serviço
    $nome_servico = "";
    foreach ($servicos as $s) {
        if ($s['id_servico'] == $c['id_servico']) {
            $nome_servico = $s['nome'];
            $preco_unit = $s['preco'];
            break;
        }
    }

    // adicionar para exibição
    $c['nome_servico'] = $nome_servico;
    $c['preco'] = $preco_unit;

    $valorServicos += $c['quantidade'] * $preco_unit;
}

// Total geral
$totalGeral = $valorDiarias + $valorServicos;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Check-out</title>

    <style>
        /* PALETA DO PROJETO */
        :root {
            --azul-1: #182262;
            --azul-2: #0A5AC2;

            --cinza-1: #D9D9D9;
            --cinza-2: #B4B4B4;
            --cinza-3: #B9B9B9;
        }

        body {
            font-family: Arial;
            background: var(--cinza-1);
            padding: 20px;
        }

        h1 {
            color: var(--azul-1);
            text-align: center;
            margin-bottom: 25px;
        }

        h3 {
            color: var(--azul-2);
            margin-top: 20px;
            border-left: 5px solid var(--azul-1);
            padding-left: 10px;
        }

        .box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--cinza-2);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: var(--azul-1);
            color: white;
            padding: 10px;
        }

        td {
            border: 1px solid var(--cinza-3);
            padding: 10px;
            text-align: center;
        }

        tr:nth-child(even) {
            background: var(--cinza-3);
        }

        .btn {
            background: var(--azul-1);
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
            border: none;
        }

        .btn:hover {
            background: var(--azul-2);
        }

        select {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid var(--cinza-2);
        }
    </style>
</head>

<body>

<h1>Finalizar Check-out</h1>

<div class="box">
    <h3>Informações do Hóspede</h3>
    <p><strong>Nome:</strong> <?= $hospede['nome'] ?></p>
    <p><strong>Quarto:</strong> <?= $quarto['numero'] ?></p>
    <p><strong>Entrada:</strong> <?= $checkin['data_hora_entrada'] ?></p>
</div>

<h3>Consumos Registrados</h3>

<?php if (empty($consumosHospede)): ?>
    <p>Nenhum consumo registrado.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Serviço</th>
            <th>Quantidade</th>
            <th>Valor Unitário</th>
            <th>Total</th>
        </tr>

        <?php 
        foreach ($consumosHospede as $c):
            $serv = null;
            foreach ($servicos as $s) {
                if ($s['id_servico'] == $c['id_servico']) {
                    $serv = $s;
                    break;
                }
            }
        ?>
        <tr>
            <td><?= $serv['nome'] ?></td>
            <td><?= $c['quantidade'] ?></td>
            <td>R$ <?= number_format($serv['preco'], 2, ',', '.') ?></td>
            <td>R$ <?= number_format($c['quantidade'] * $serv['preco'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<div class="box">
    <h3>Resumo da Conta</h3>
    <p><strong>Diárias (<?= $dias ?> dia(s)):</strong> R$ <?= number_format($valorDiarias, 2, ',', '.') ?></p>
    <p><strong>Serviços:</strong> R$ <?= number_format($valorServicos, 2, ',', '.') ?></p>
    <p><strong>Total Geral:</strong> <b>R$ <?= number_format($totalGeral, 2, ',', '.') ?></b></p>
</div>

<form method="post" action="salvar_checkout.php">
    <input type="hidden" name="id_checkin" value="<?= $id_checkin ?>">
    <input type="hidden" name="valor_diarias" value="<?= $valorDiarias ?>">
    <input type="hidden" name="valor_servicos" value="<?= $valorServicos ?>">
    <input type="hidden" name="total_geral" value="<?= $totalGeral ?>">

    <label><strong>Método de Pagamento:</strong></label><br><br>
    <select name="metodo_pagamento" required>
        <option value="credito">Crédito</option>
        <option value="debito">Débito</option>
        <option value="pix">PIX</option>
        <option value="dinheiro">Dinheiro</option>
    </select>

    <br><br>
    <button class="btn" type="submit">Confirmar Check-out</button>
</form>

</body>
</html>

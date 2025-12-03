
<?php
//mariana nicolato

$banco = 'vendas.json';


if (!file_exists($banco)) {
    file_put_contents($banco, json_encode([]));
}


$vendas = json_decode(file_get_contents($banco), true);


function filtrarVendas($vendas, $inicio, $fim) {
    $filtro = [];
    foreach ($vendas as $v) {
        if ($v['data'] >= $inicio && $v['data'] <= $fim) {
            $filtro[] = $v;
        }
    }
    return $filtro;
}

$resultado = [];
$total = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];

    if ($tipo === 'dia') {
        $dia = $_POST['dia'];
        $resultado = filtrarVendas($vendas, $dia, $dia);

    } elseif ($tipo === 'mes') {
        $mes = $_POST['mes']; // formato YYYY-MM
        $inicio = $mes . "-01";
        $fim = $mes . "-31";
        $resultado = filtrarVendas($vendas, $inicio, $fim);

    } elseif ($tipo === 'periodo') {
        $inicio = $_POST['inicio'];
        $fim = $_POST['fim'];
        $resultado = filtrarVendas($vendas, $inicio, $fim);
    }

    foreach ($resultado as $r) {
        $total += $r['valor'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Relatório Financeiro</title>
</head>
<body>
    <h2>Relatório Financeiro</h2>

    <form method="POST">
        <label>Selecione o tipo de relatório:</label><br>

        <input type="radio" name="tipo" value="dia" required> Por Dia<br>
        <input type="date" name="dia"><br><br>

        <input type="radio" name="tipo" value="mes"> Por Mês<br>
        <input type="month" name="mes"><br><br>

        <input type="radio" name="tipo" value="periodo"> Por Período<br>
        Início: <input type="date" name="inicio">
        Fim: <input type="date" name="fim"><br><br>

        <button type="submit">Gerar Relatório</button>
    </form>

    <hr>

    <?php if (!empty($resultado)): ?>
        <h3>Resultados:</h3>
        <table border ="1" cellpadding="5">
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor</th>
            </tr>

            <?php foreach ($resultado as $r): ?>
                <tr>
                    <td><?= $r['data'] ?></td>
                    <td><?= $r['descricao'] ?></td>
                    <td>R$ <?= number_format($r['valor'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total: R$ <?= number_format($total, 2, ',', '.') ?></h3>
    <?php endif; ?>

</body>
</html>
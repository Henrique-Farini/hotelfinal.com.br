<?php
$arquivoConsumos = "../data/consumos.json";

if (!isset($_POST['id_checkin']) || !isset($_POST['id_servico']) || !isset($_POST['quantidade'])) {
    die("Erro: Dados incompletos.");
}

$id_checkin   = $_POST['id_checkin'];
$id_servico   = $_POST['id_servico'];
$quantidade   = intval($_POST['quantidade']);
$observacao   = isset($_POST['observacao']) ? $_POST['observacao'] : "";

// Carregar arquivo JSON existente
if (file_exists($arquivoConsumos)) {
    $jsonData = file_get_contents($arquivoConsumos);
    $consumos = json_decode($jsonData, true);
} else {
    $consumos = [];
}

$novoConsumo = [
    "id_consumo" => count($consumos) + 1,
    "id_checkin" => $id_checkin,
    "id_servico" => $id_servico,
    "quantidade" => $quantidade,
    "data_hora" => date("Y-m-d H:i:s"),
    "observacao" => $observacao,
    "categoria" => "restaurante"
];

$consumos[] = $novoConsumo;

file_put_contents($arquivoConsumos, json_encode($consumos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: restaurante.php?status=ok");
exit;

?>

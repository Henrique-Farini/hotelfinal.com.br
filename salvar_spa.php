<?php
// Caminho do arquivo JSON de consumos
$arquivo = __DIR__ . '/../data/consumos.json';

// Carrega consumos já existentes ou cria array vazio
$consumos = file_exists($arquivo)
    ? json_decode(file_get_contents($arquivo), true)
    : [];

// Segurança: verifica se os campos essenciais chegaram
if (!isset($_POST['id_checkin'], $_POST['id_servico'], $_POST['quantidade'])) {
    die("Erro: Dados incompletos enviados.");
}

// Gera ID automático baseado na lista existente
$novoId = empty($consumos) ? 1 : max(array_column($consumos, 'id_consumo')) + 1;

// Montagem do registro de consumo
$novoConsumo = [
    "id_consumo" => $novoId,
    "id_checkin" => $_POST['id_checkin'],
    "id_servico" => $_POST['id_servico'],
    "quantidade" => (int)$_POST['quantidade'],
    "data_hora" => date("Y-m-d H:i:s"),
    "observacao" => $_POST['observacao'] ?? ""
];

// Adiciona no array
$consumos[] = $novoConsumo;

// Salva de volta no arquivo JSON
file_put_contents($arquivo, json_encode($consumos, JSON_PRETTY_PRINT));

// Redireciona com indicador de sucesso
header("Location: spa.php?sucesso=1");
exit;
?>

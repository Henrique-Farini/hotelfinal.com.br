<?php
header('Content-Type: application/json');

// Caminho do arquivo JSON
$arquivo = 'precadastros.json';

// Verifica se recebeu o ID para remover
if (!isset($_GET['id'])) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "ID não informado."
    ]);
    exit;
}

$id = $_GET['id'];

// Lê o arquivo
if (!file_exists($arquivo)) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Arquivo JSON não encontrado."
    ]);
    exit;
}

$dados = json_decode(file_get_contents($arquivo), true);

// Se o JSON estiver vazio
if (!is_array($dados)) {
    $dados = [];
}

$encontrado = false;

// Remove o pré-cadastro com o ID informado
foreach ($dados as $index => $item) {
    if ($item['id'] == $id) {
        unset($dados[$index]);
        $encontrado = true;
        break;
    }
}

if ($encontrado) {
    // Reindexa o array e salva o JSON
    file_put_contents($arquivo, json_encode(array_values($dados), JSON_PRETTY_PRINT));

    echo json_encode([
        "status" => "sucesso",
        "mensagem" => "Pré-cadastro removido."
    ]);
} else {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Registro não encontrado."
    ]);
}
?>

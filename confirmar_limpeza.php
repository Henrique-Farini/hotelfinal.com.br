<?php
// LEONARDO KRONKA
session_start();
require_once "../utils.php";  // Funções de leitura/escrita JSON

// Segurança: apenas setor de serviços pode acessar
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['cargo'] != 'servicos') {
    header("Location: ../index.php");
    exit;
}

// Verificar se recebeu o ID do quarto por GET
if (!isset($_GET['id_quarto'])) {
    header("Location: limpeza.php?erro=Quarto não informado");
    exit;
}

$id_quarto = $_GET['id_quarto'];

// Caminho do arquivo JSON de quartos
$arquivoQuartos = "../data/quartos.json";

// Carregar os dados existentes
$quartos = lerJSON($arquivoQuartos);

// Encontrar o quarto correspondente
$quartoEncontrado = false;

foreach ($quartos as &$quarto) {
    if ($quarto['id_quarto'] == $id_quarto) {

        // Atualizar status do quarto para disponível novamente
        $quarto['status'] = 'livre';
        $quartoEncontrado = true;
        break;
    }
}

// Se o quarto não existe
if (!$quartoEncontrado) {
    header("Location: limpeza.php?erro=Quarto não encontrado");
    exit;
}

// Salvar de volta no JSON
salvarJSON($arquivoQuartos, $quartos);

// Registrar no histórico (opcional, recomendado pelo RF08)
$arquivoHistorico = "../data/historico.json";
$historico = lerJSON($arquivoHistorico);

$historico[] = [
    "data_hora" => date("Y-m-d H:i:s"),
    "acao" => "Limpeza concluída",
    "detalhes" => "Quarto $id_quarto está pronto para uso novamente.",
    "usuario" => $_SESSION['usuario']['nome']
];

salvarJSON($arquivoHistorico, $historico);

// Redirecionar de volta com sucesso
header("Location: limpeza.php?sucesso=Quarto $id_quarto confirmado como limpo!");
exit;

?>

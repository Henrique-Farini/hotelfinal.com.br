<?php
// Configuração dos caminhos dos arquivos JSON
$basePath = __DIR__ . "/../data/";

$checkinsFile = $basePath . "checkins.json";
$consumosFile = $basePath . "consumos.json";
$servicosFile = $basePath . "servicos.json";
$quartosFile  = $basePath . "quartos.json";
$checkoutFile = $basePath . "checkout.json";
$historicoFile= $basePath . "historico.json"; // Usado para armazenar RF08

// Função auxiliar para ler e decodificar JSON com tratamento de erro
function lerJSON($file) {
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

// Função auxiliar para codificar e salvar JSON
function salvarJSON($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// --- 1. Processar Requisição ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_checkin'])) {
    $idCheckin = filter_input(INPUT_POST, 'id_checkin', FILTER_VALIDATE_INT);
    $metodoPagamento = filter_input(INPUT_POST, 'metodo_pagamento', FILTER_SANITIZE_STRING); // Assumimos que foi selecionado na tela checkout.php
    
    // Simulação de Taxa Fixa e Preço Diário (Na prática, viria do Quarto e de Configurações)
    $TAXA_SERVICO = 15.00; 

    // --- 2. Carregar Dados ---
    $checkins = lerJSON($checkinsFile);
    $consumos = lerJSON($consumosFile);
    $servicos = lerJSON($servicosFile);
    $quartos = lerJSON($quartosFile);
    $checkouts = lerJSON($checkoutFile);
    $historico = lerJSON($historicoFile);

    // Encontra o check-in sendo encerrado
    $checkinToCheckout = null;
    $checkinIndex = -1;
    foreach ($checkins as $index => $item) {
        if (($item['id_checkin'] ?? 0) == $idCheckin) {
            $checkinToCheckout = $item;
            $checkinIndex = $index;
            break;
        }
    }

    if (!$checkinToCheckout) {
        die("Erro: Check-in ID não encontrado.");
    }
    
    // --- 3. Calcular Despesas Totais (RF07) ---
    
    // Calcular total de consumos e serviços
    $totalServicos = 0.0;
    $consumosDaEstadia = array_filter($consumos, function($c) use ($idCheckin) {
        return ($c['id_checkin'] ?? 0) == $idCheckin;
    });

    foreach ($consumosDaEstadia as $c) {
        $servico = array_filter($servicos, function($s) use ($c) {
            return ($s['id_servico'] ?? 0) == ($c['id_servico'] ?? 0);
        });
        $servico = reset($servico); // Pega o primeiro (e único)
        
        if ($servico) {
            // Soma (Preço do Serviço * Quantidade)
            $totalServicos += ($servico['preco'] ?? 0) * ($c['quantidade'] ?? 1);
        }
    }
    
    // Calcular Total de Diárias
    $dataEntrada = new DateTime($checkinToCheckout['data_hora_entrada'] ?? 'now');
    $dataSaida = new DateTime('now'); // Assumimos que a saída é o momento do check-out
    $diferenca = $dataEntrada->diff($dataSaida);
    $diarias = max(1, $diferenca->days); // Mínimo de 1 diária
    
    // Simulação de como obter o preço da diária (na prática viria do Quarto)
    $precoDiaria = 250.00; // Valor fixo de exemplo
    $totalDiarias = $diarias * $precoDiaria;

    // Cálculo Final: (Diárias + Consumos + Serviços + Taxas) 
    $totalGeral = $totalDiarias + $totalServicos + $TAXA_SERVICO;

    // --- 4. Registrar Check-out e Atualizar Status ---
    
    $novoCheckout = [
        'id_checkout' => count($checkouts) + 1,
        'id_checkin' => $idCheckin,
        'data_hora_saida' => $dataSaida->format('Y-m-d H:i:s'),
        'total_diarias' => round($totalDiarias, 2),
        'total_servicos' => round($totalServicos, 2),
        'total_geral' => round($totalGeral, 2),
        'metodo_pagamento' => $metodoPagamento ?: 'dinheiro' // Simulação
    ];
    
    $checkouts[] = $novoCheckout;
    salvarJSON($checkoutFile, $checkouts);

    // Atualiza o status do Check-in para 'encerrado'
    $checkins[$checkinIndex]['status'] = 'encerrado';
    salvarJSON($checkinsFile, $checkins);

    // --- 5. Atualizar Quarto para 'manutenção' (Limpeza) ---
    $idQuarto = $checkinToCheckout['id_quarto'] ?? null;
    if ($idQuarto) {
        foreach ($quartos as $qIndex => $q) {
            // Encontra o quarto pelo ID (ou número, dependendo da estrutura do JSON)
            if (($q['id'] ?? $q['numero'] ?? 0) == $idQuarto) { 
                $quartos[$qIndex]['status'] = 'manutenção'; // Prepara para a limpeza [cite: 343]
                break;
            }
        }
        salvarJSON($quartosFile, $quartos);
    }
    
    // --- 6. Registrar Histórico (RF08) ---
    // Transfere os dados da estadia para o arquivo de histórico
    $historicoEstadia = [
        'id_hospede' => $checkinToCheckout['id_hospede'] ?? null,
        'data_entrada' => $dataEntrada->format('Y-m-d'),
        'data_saida' => $dataSaida->format('Y-m-d'),
        'quarto_numero' => $idQuarto,
        'total_pago' => round($totalGeral, 2),
        'consumos_detalhados' => $consumosDaEstadia,
        'movimentacao_checkin_id' => $idCheckin
    ];

    $historico[] = $historicoEstadia;
    salvarJSON($historicoFile, $historico); // O sistema deve manter o histórico de estadias, reservas, serviços e movimentações. [cite: 28]

    // --- 7. Resposta ao Usuário ---
    header("Location: ../recepcao/checkout_sucesso.php?total=" . round($totalGeral, 2));
    exit;
} else {
    // Redireciona se acessado diretamente ou sem dados
    header("Location: ../recepcao/checkout.php");
    exit;
}
?>
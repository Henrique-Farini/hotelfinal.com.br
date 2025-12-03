<?php
// Define a codificação para garantir a compatibilidade de caracteres
header('Content-Type: text/html; charset=utf-8');

// --- Configuração de Caminhos ---
// Caminhos dos arquivos JSON para persistência local (RF10, RNF01)
$checkinsFile = __DIR__ . "/../data/checkins.json";
$servicosFile = __DIR__ . "/../data/servicos.json";

/**
 * Função para ler, decodificar JSON de forma segura e retornar um array vazio em caso de falha.
 * @param string $file Caminho para o arquivo JSON.
 * @return array O conteúdo do JSON decodificado ou um array vazio.
 */
function lerJSON(string $file): array {
    // Tenta ler o conteúdo, suprimindo erros de arquivo não encontrado ou ilegível
    $content = @file_get_contents($file);
    
    // Usa '[]' como fallback para json_decode() se o conteúdo for falso ou vazio
    return json_decode($content ?: '[]', true) ?? [];
}

// --- Lógica de Negócio (RF06) ---

// Carregar hóspedes ativos
$checkins = lerJSON($checkinsFile);
$hospedesAtivos = array_filter($checkins, function($item) {
    // Filtra apenas check-ins com status 'ativo'
    return ($item['status'] ?? null) === "ativo";
});

// Carregar serviços do restaurante
$servicos = lerJSON($servicosFile);
$menuRestaurante = array_filter($servicos, function($s) {
    // Filtra apenas serviços da categoria 'restaurante'
    return ($s['categoria'] ?? null) === "restaurante";
});

// Verifica se os dados básicos puderam ser carregados para exibir o alerta
$dadosAcessiveis = !empty($checkins) || file_exists($checkinsFile);
$servicosAcessiveis = !empty($servicos) || file_exists($servicosFile);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro de Consumo - Restaurante</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 30px; background-color: #f8f8f8; }
        .container { max-width: 600px; margin: auto; padding: 25px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 25px; }
        .error-message { color: #D8000C; background-color: #FFBABA; border: 1px solid #D8000C; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-weight: 500; }
        label { font-weight: bold; display: block; margin-top: 15px; color: #555; }
        input[type="number"], select, textarea { 
            width: 100%; 
            padding: 10px; 
            margin: 6px 0 15px 0; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box;
            background-color: #fff;
        }
        button { 
            width: 100%; 
            padding: 12px; 
            cursor: pointer; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            font-size: 1em;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        button:hover:not(:disabled) { background-color: #0056b3; }
        button:disabled { background-color: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="container">
    <h2>Registro de Consumo - Restaurante</h2>

    <?php 
    // Exibe mensagem de alerta se os arquivos de dados não puderam ser acessados
    if (!$dadosAcessiveis || !$servicosAcessiveis): ?>
        <div class="error-message">
            **ATENÇÃO:** Não foi possível carregar a totalidade dos dados. Verifique a existência e as permissões de acesso dos arquivos JSON (`checkins.json` e `servicos.json`) no diretório `../data/`.
        </div>
    <?php endif; ?>

    <form action="salvar_restaurante.php" method="POST">

        <label for="id_checkin">Hóspede Ativo (Check-in):</label>
        <select name="id_checkin" required>
            <option value="">Selecione...</option>
            <?php 
            if (empty($hospedesAtivos)): ?>
                <option disabled>Nenhum hóspede ativo registrado (Check-ins vazios).</option>
            <?php 
            else:
                foreach ($hospedesAtivos as $h): ?>
                    <option value="<?= htmlspecialchars($h['id_checkin'] ?? '') ?>">
                        [#<?= htmlspecialchars($h['id_checkin'] ?? 'N/A') ?>] Hóspede ID <?= htmlspecialchars($h['id_hospede'] ?? 'N/A') ?> - Quarto <?= htmlspecialchars($h['id_quarto'] ?? 'N/A') ?>
                    </option>
                <?php 
                endforeach;
            endif; ?>
        </select>

        <label for="id_servico">Item Consumido:</label>
        <select name="id_servico" required>
            <option value="">Selecione...</option>
            <?php 
            if (empty($menuRestaurante)): ?>
                <option disabled>Nenhum item de restaurante cadastrado (Serviços vazios).</option>
            <?php 
            else:
                foreach ($menuRestaurante as $item): 
                    $nomeItem = htmlspecialchars($item['nome'] ?? 'Item sem nome');
                    $precoFormatado = number_format($item['preco'] ?? 0, 2, ',', '.');
                    ?>
                    <option value="<?= htmlspecialchars($item['id_servico'] ?? '') ?>">
                        <?= $nomeItem ?> - R$ <?= $precoFormatado ?>
                    </option>
                <?php 
                endforeach;
            endif; ?>
        </select>

        <label for="quantidade">Quantidade:</label>
        <input type="number" name="quantidade" id="quantidade" min="1" value="1" required>

        <label for="observacao">Observação (Opcional):</label>
        <textarea name="observacao" id="observacao" rows="3" placeholder="Detalhes do pedido, restrições alimentares, etc."></textarea>

        <button type="submit" <?= empty($hospedesAtivos) || empty($menuRestaurante) ? 'disabled' : '' ?>>
            Registrar Consumo
        </button>
    </form>
</div>

</body>
</html>
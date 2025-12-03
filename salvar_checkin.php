<?php
// Define a codificação e desativa a exibição de erros (boa prática em produção)
// error_reporting(0);
// ini_set('display_errors', 0);

// Caminhos dos arquivos JSON para persistência local (RF10, RNF01)
// Os caminhos são relativos ao local onde o script está rodando, assumindo que os dados estão em ../data/
$checkinsFile = __DIR__ . "/../data/checkins.json";
$servicosFile = __DIR__ . "/../data/servicos.json";

// --- Lógica para carregar Hóspedes Ativos ---
// Carrega o conteúdo de checkins.json
$checkins = @json_decode(@file_get_contents($checkinsFile), true) ?: [];

// Filtra todos os check-ins que estão com status 'ativo' (Hóspedes Ativos - RF06)
$hospedesAtivos = array_filter($checkins, function($item) {
    return isset($item['status']) && $item['status'] === "ativo";
});

// --- Lógica para carregar o Menu do Restaurante ---
// Carrega o conteúdo de servicos.json
$servicos = @json_decode(@file_get_contents($servicosFile), true) ?: [];

// Filtra todos os serviços que são da categoria 'restaurante' (RF06)
$menuRestaurante = array_filter($servicos, function($s) {
    return isset($s['categoria']) && $s['categoria'] === "restaurante";
});
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro de Consumo - Restaurante</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-top: 10px; color: #555; }
        input[type="number"], select, textarea { 
            width: 100%; 
            padding: 10px; 
            margin: 6px 0 15px 0; 
            display: inline-block; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
        }
        button { 
            width: 100%; 
            background-color: #4CAF50; 
            color: white; 
            padding: 14px 20px; 
            margin: 8px 0;
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px;
        }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>

<div class="container">
    <h2>Registro de Consumo - Restaurante</h2>

    <form action="salvar_restaurante.php" method="POST">

        <label for="id_checkin">Hóspede Ativo (Check-in):</label>
        <select name="id_checkin" required>
            <option value="">Selecione o Hóspede...</option>
            <?php 
            if (!empty($hospedesAtivos)):
                foreach ($hospedesAtivos as $h): 
                    // Assume que 'id_checkin', 'id_hospede' e 'id_quarto' são campos válidos
                    // 'id_hospede' e 'id_quarto' são usados apenas para identificação na tela
                    ?>
                    <option value="<?= htmlspecialchars($h['id_checkin'] ?? '') ?>">
                        Check-in ID <?= htmlspecialchars($h['id_checkin'] ?? '') ?> | Quarto: <?= htmlspecialchars($h['id_quarto'] ?? 'N/A') ?>
                    </option>
                <?php 
                endforeach;
            else:
                ?>
                <option disabled>Nenhum hóspede ativo no momento.</option>
            <?php endif; ?>
        </select>

        <label for="id_servico">Item Consumido:</label>
        <select name="id_servico" required>
            <option value="">Selecione o Item...</option>
            <?php 
            if (!empty($menuRestaurante)):
                foreach ($menuRestaurante as $item): 
                    // Assume que 'id_servico', 'nome' e 'preco' são campos válidos
                    ?>
                    <option value="<?= htmlspecialchars($item['id_servico'] ?? '') ?>">
                        <?= htmlspecialchars($item['nome'] ?? 'Item sem nome') ?> - R$ <?= number_format($item['preco'] ?? 0, 2, ',', '.') ?>
                    </option>
                <?php 
                endforeach;
            else:
                ?>
                <option disabled>Nenhum item de restaurante cadastrado.</option>
            <?php endif; ?>
        </select>

        <label for="quantidade">Quantidade:</label>
        <input type="number" name="quantidade" id="quantidade" min="1" value="1" required>

        <label for="observacao">Observação (opcional):</label>
        <textarea name="observacao" id="observacao" rows="3" placeholder="Ex: Sem glúten, entregue no quarto..."></textarea>

        <button type="submit">Registrar Consumo</button>
    </form>
</div>

</body>
</html>
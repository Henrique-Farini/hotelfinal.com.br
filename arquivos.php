<?php
// Samuel
// arquivos.php
// Página para visualização / download / exclusão dos arquivos JSON do sistema.
// Baseado na estrutura do projeto "Conforto & Elegância" (arquivos listados no PDF).
// Autor: Gerado por ChatGPT. Ajuste conforme necessário.

session_start();
require_once __DIR__ . '/../utils.php'; // ajuste o caminho conforme sua estrutura

// --- CONFIGURAÇÃO ---
define('DATA_DIR', __DIR__ . '/../data'); // caminho para a pasta de dados (ajuste se necessário)
$allowed_files = [
    'hospedes.json',
    'precadastros.json',
    'quartos.json',
    'checkins.json',
    'checkouts.json',
    'servicos.json',
    'consumos.json',
    'usuarios.json',
    'historico.json'
];

// Função simples para checar se usuário tem permissão de gerente.
// Ajuste conforme sua lógica de sessão (ex: $_SESSION['cargo'] == 'gerencia').
function isGerente() {
    // Exemplo: utils.php pode definir $_SESSION['user'] com 'cargo' ou 'setor'
    if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
        $u = $_SESSION['usuario'];
        if (isset($u['cargo']) && strtolower($u['cargo']) === 'gerencia') return true;
        if (isset($u['setor']) && strtolower($u['setor']) === 'gerencia') return true;
    }
    // Fallback: se utils.php definiu $_SESSION['cargo']
    if (isset($_SESSION['cargo']) && strtolower($_SESSION['cargo']) === 'gerencia') return true;
    return false;
}

// Helpers
function safe_filename($name) {
    // Garante que o nome é apenas basename (sem diretórios) e remove espaços perigosos
    return basename(trim($name));
}

function file_path($filename) {
    return rtrim(DATA_DIR, '/\\') . DIRECTORY_SEPARATOR . $filename;
}

function read_json_file($path) {
    if (!file_exists($path)) return null;
    $fp = fopen($path, 'r');
    if (!$fp) return null;
    // bloquear leitura
    flock($fp, LOCK_SH);
    $contents = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    // tentar decodificar JSON
    $data = json_decode($contents, true);
    // se JSON inválido, retornar raw string também
    return ['raw' => $contents, 'json' => $data];
}

// --- AÇÕES (view / download / delete) ---
$action = isset($_GET['action']) ? $_GET['action'] : '';
$file = isset($_GET['file']) ? safe_filename($_GET['file']) : '';

$message = '';
$error = '';

if ($action === 'download' && $file) {
    if (!in_array($file, $allowed_files)) {
        $error = "Arquivo não permitido.";
    } else {
        $full = file_path($file);
        if (!file_exists($full)) {
            $error = "Arquivo não encontrado: $file";
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($full) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full));
            readfile($full);
            exit;
        }
    }
}

if ($action === 'delete' && $file) {
    if (!isGerente()) {
        $error = "Ação não autorizada. Apenas gerência pode excluir arquivos.";
    } elseif (!in_array($file, $allowed_files)) {
        $error = "Arquivo não permitido.";
    } else {
        $full = file_path($file);
        if (!file_exists($full)) {
            $error = "Arquivo não encontrado.";
        } else {
            // Segurança: fazer um backup antes de excluir (opcional)
            $backupDir = DATA_DIR . DIRECTORY_SEPARATOR . 'backups';
            if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
            $timestamp = date('Ymd_His');
            copy($full, $backupDir . DIRECTORY_SEPARATOR . $file . '.' . $timestamp . '.bak');
            if (@unlink($full)) {
                $message = "Arquivo $file excluído com sucesso. Backup criado em /data/backups.";
            } else {
                $error = "Falha ao excluir o arquivo.";
            }
        }
    }
}

// --- Preparar lista de arquivos existentes ---
$files_present = [];
foreach ($allowed_files as $f) {
    $full = file_path($f);
    $files_present[$f] = [
        'exists' => file_exists($full),
        'size' => file_exists($full) ? filesize($full) : 0,
        'modified' => file_exists($full) ? date('d/m/Y H:i:s', filemtime($full)) : '-'
    ];
}

// --- Página HTML ---
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Arquivos do Sistema — Gerência</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; margin:20px; background:#f6f7fb; color:#222; }
        .card { background:white; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); margin-bottom:16px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:8px 10px; border-bottom:1px solid #eee; text-align:left; }
        .small { font-size:0.9em; color:#666; }
        .btn { display:inline-block; padding:6px 10px; border-radius:6px; text-decoration:none; background:#2d7cf0; color:white; margin-right:6px; }
        .btn-danger { background:#e04b4b; }
        .btn-ghost { background:transparent; color:#2d7cf0; border:1px solid #2d7cf0; }
        pre { background:#0b1020; color:#dfefff; padding:12px; border-radius:6px; overflow:auto; max-height:400px; }
        .msg { padding:10px; border-radius:6px; margin-bottom:12px; }
        .success { background:#e6ffef; color:#056b34; border:1px solid #c7f0d2; }
        .err { background:#ffecec; color:#8b1a1a; border:1px solid #f2c2c2; }
        .meta { font-size:0.9em; color:#555; margin-bottom:8px; }
    </style>
</head>
<body>
    <h2>Gerência — Visualizar arquivos do sistema</h2>
    <p class="small">Diretório de dados: <code><?= htmlspecialchars(DATA_DIR) ?></code></p>

    <?php if ($message): ?>
        <div class="msg success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="msg err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <h3>Arquivos disponíveis</h3>
        <table>
            <thead>
                <tr><th>Arquivo</th><th>Existente</th><th>Tamanho</th><th>Última modificação</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($files_present as $fname => $meta): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($fname) ?></strong></td>
                        <td><?= $meta['exists'] ? 'Sim' : 'Não' ?></td>
                        <td><?= $meta['exists'] ? number_format($meta['size']/1024,2).' KB' : '-' ?></td>
                        <td><?= $meta['modified'] ?></td>
                        <td>
                            <?php if ($meta['exists']): ?>
                                <a class="btn" href="?action=view&file=<?= urlencode($fname) ?>">Visualizar</a>
                                <a class="btn btn-ghost" href="?action=download&file=<?= urlencode($fname) ?>">Baixar</a>
                                <?php if (isGerente()): ?>
                                    <a class="btn btn-danger" href="javascript:if(confirm('Confirma exclusão de <?= addslashes($fname) ?>?')) location.href='?action=delete&file=<?= urlencode($fname) ?>'">Excluir</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="small">Arquivo ausente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="small">Lista baseada nos arquivos previstos pela documentação do sistema. (ver PDF do projeto para nomes e estrutura). :contentReference[oaicite:1]{index=1}</p>
    </div>

<?php
// Vista do arquivo selecionado
if ($action === 'view' && $file) {
    if (!in_array($file, $allowed_files)) {
        echo '<div class="msg err">Arquivo não permitido.</div>';
    } else {
        $full = file_path($file);
        if (!file_exists($full)) {
            echo '<div class="msg err">Arquivo não encontrado.</div>';
        } else {
            $data = read_json_file($full);
            echo '<div class="card">';
            echo '<h3>Visualizando: ' . htmlspecialchars($file) . '</h3>';
            echo '<p class="meta">Tamanho: ' . number_format(filesize($full)/1024,2) . ' KB · Modificado: ' . date('d/m/Y H:i:s', filemtime($full)) . '</p>';

            // Se for JSON decodificado em array de objetos, mostrar tabela
            if ($data !== null && is_array($data['json'])) {
                $arr = $data['json'];
                // Caso seja lista de objetos associativos -> construir tabela
                $firstRow = null;
                foreach ($arr as $r) { if (is_array($r)) { $firstRow = $r; break; } }
                if ($firstRow !== null) {
                    echo '<table><thead><tr>';
                    foreach (array_keys($firstRow) as $col) {
                        echo '<th>' . htmlspecialchars($col) . '</th>';
                    }
                    echo '</tr></thead><tbody>';
                    foreach ($arr as $r) {
                        if (!is_array($r)) {
                            echo '<tr><td colspan="' . count($firstRow) . '">' . htmlspecialchars(json_encode($r)) . '</td></tr>';
                            continue;
                        }
                        echo '<tr>';
                        foreach (array_keys($firstRow) as $col) {
                            $cell = isset($r[$col]) ? $r[$col] : '';
                            if (is_array($cell) || is_object($cell)) $cell = json_encode($cell);
                            echo '<td>' . nl2br(htmlspecialchars((string)$cell)) . '</td>';
                        }
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                } else {
                    // Estrutura não é lista de objetos – mostrar raw JSON formatado
                    echo '<h4>Conteúdo JSON (formatado)</h4>';
                    $pretty = json_encode($data['json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    echo '<pre>' . htmlspecialchars($pretty) . '</pre>';
                }
            } else {
                // arquivo não é JSON válido ou decodificação falhou: mostrar texto bruto
                echo '<h4>Conteúdo bruto</h4>';
                echo '<pre>' . htmlspecialchars($data['raw'] ?? file_get_contents($full)) . '</pre>';
            }
            echo '</div>';
        }
    }
}
?>

    <div style="margin-top:40px; font-size:0.9em; color:#666;">
        <p>Observações:</p>
        <ul>
            <li>Esta página é uma ferramenta administrativa para a gerência visualizar os arquivos locais que o sistema usa (JSON/CSV conforme design). :contentReference[oaicite:2]{index=2}</li>
            <li>Não exponha essa página sem autenticação; verifique <code>utils.php</code> para garantir que somente usuários autorizados possam acessá-la.</li>
            <li>Antes de excluir arquivos, verifique o backup automático criado em <code>/data/backups</code>.</li>
        </ul>
    </div>

</body>
</html>

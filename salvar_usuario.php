<?php
// salvar_usuario.php
session_start();

// Verifica se o usuário está logado e é gerente
if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] != 'gerente') {
    header("Location: ../index.php");
    exit;
}

// Caminho do arquivo de usuários
$arquivo = __DIR__ . "/../data/usuarios.json";

// Recebe os dados do formulário
$nome  = $_POST['nome'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';

// Validação simples
if (empty($nome) || empty($cargo) || empty($login) || empty($senha)) {
    $_SESSION['mensagem'] = "Preencha todos os campos!";
    header("Location: cadastro_usuario.php");
    exit;
}

// Carrega usuários existentes
$usuarios = [];
if (file_exists($arquivo)) {
    $json = file_get_contents($arquivo);
    $usuarios = json_decode($json, true) ?? [];
}

// Verifica se o login já existe
foreach ($usuarios as $user) {
    if ($user['login'] === $login) {
        $_SESSION['mensagem'] = "Erro: Login já cadastrado!";
        header("Location: cadastro_usuario.php");
        exit;
    }
}

// Cria novo ID
$novoId = count($usuarios) > 0 ? end($usuarios)['id_usuario'] + 1 : 1;

// Cria novo usuário
$novoUsuario = [
    "id_usuario" => $novoId,
    "nome" => $nome,
    "cargo" => $cargo,
    "login" => $login,
    "senha" => password_hash($senha, PASSWORD_DEFAULT) // Segurança!
];

// Adiciona novo usuário na lista
$usuarios[] = $novoUsuario;

// Salva no JSON
file_put_contents($arquivo, json_encode($usuarios, JSON_PRETTY_PRINT));

// Mensagem de sucesso
$_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
header("Location: cadastro_usuario.php");
exit;
?>

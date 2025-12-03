<?php
//vitor sabino
session_start();

// remove todas as variáveis da sessão
session_unset();

// destrói a sessão
session_destroy();

// redireciona para a tela de login
header("Location: login.php");
exit;
?>

<?php
session_start();
session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Destroi a sessão

// Redirecionar para a página inicial
header("Location: login.php");
exit();
?>

<?php
session_start();
session_destroy(); // Limpa todas as variáveis de sessão
header("Location: entrar.html"); // Manda de volta para o login
exit();
?>
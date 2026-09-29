<?php
$host = "localhost:3307";
$user = "root"; // Padrão do XAMPP
$pass = "";     // Padrão do XAMPP (vazio)
$dbname = "foodshare_db";

// Criar a conexão
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar se houve erro
if ($conn->connect_error) {
    die("Erro na ligação: " . $conn->connect_error);
}

// Se chegar aqui, a ligação foi um sucesso!
?>
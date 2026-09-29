<?php
session_start();
include_once("../../conexao.php");

// 1. Verificar se o utilizador está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

// 2. Verificar se o ID do produto chegou via URL
if (isset($_GET['id'])) {
    $id_produto = intval($_GET['id']);
    $id_doador = intval($_SESSION['id']);

    // 3. Atualizar o status para 'removido'
    // Importante: filtramos pelo id do produto E pelo id do doador logado
    $sql = "UPDATE doacoes SET status = 'removido' WHERE id = $id_produto AND id_utilizador = $id_doador";

    if ($conn->query($sql) === TRUE) {
        // Sucesso: Volta para o painel atualizado
        header("Location: doador.php");
        exit();
    } else {
        echo "Erro ao remover: " . $conn->error;
    }
} else {
    // Se tentarem aceder ao ficheiro sem ID, volta para o doador
    header("Location: doador.php");
    exit();
}
?>

<?php
session_start(); // 1. Identifica o utilizador que tem a sessão aberta.
include_once("../../conexao.php"); // 2. Liga o site à base de dados.

// Só quem estiver autenticado pode publicar uma doação
if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 3. Guarda nas variáveis o que foi escrito no formulário (com escape contra SQL Injection).
    $id_logado  = intval($_SESSION['id']);
    $produto    = mysqli_real_escape_string($conn, $_POST['produto']);
    $categoria  = mysqli_real_escape_string($conn, $_POST['categoria']);
    $quantidade = mysqli_real_escape_string($conn, $_POST['quantidade']);
    $validade   = mysqli_real_escape_string($conn, $_POST['validade']);
    $descricao  = mysqli_real_escape_string($conn, $_POST['descricao']);

    // 4. Faz o upload da imagem para a pasta do servidor com um nome único (evita conflitos/overwrite).
    $foto_original = basename($_FILES['foto']['name']);
    $extensao = pathinfo($foto_original, PATHINFO_EXTENSION);
    $foto = uniqid('doacao_') . '.' . $extensao;
    $target = "../../images/" . $foto;
    move_uploaded_file($_FILES['foto']['tmp_name'], $target);

    $foto_escaped = mysqli_real_escape_string($conn, $foto);

    // 5. Insere os dados na tabela "doacoes" com o estado "ativo".
    $sql = "INSERT INTO doacoes (id_utilizador, produto, categoria, quantidade, validade, descricao, imagem, status)
            VALUES ('$id_logado', '$produto', '$categoria', '$quantidade', '$validade', '$descricao', '$foto_escaped', 'ativo')";

    // 6. Confirma o sucesso e redireciona o utilizador.
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Publicado com sucesso!'); window.location.href='doador.php';</script>";
    } else {
        echo "Erro ao publicar: " . $conn->error;
    }
}
?>

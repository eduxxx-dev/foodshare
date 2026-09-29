<?php
// Liga o ficheiro à base de dados para poder guardar os dados
include_once("../../conexao.php");

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Confirma que a password e a confirmação coincidem antes de gravar
    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo "<script>
                alert('As passwords não coincidem. Por favor tenta novamente.');
                window.location.href='registarr.html';
              </script>";
        exit();
    }

    // Guarda nas variáveis o que o utilizador escreveu no site (com escape contra SQL Injection)
    $nome  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nif   = mysqli_real_escape_string($conn, $_POST['nif']);
    $tel   = mysqli_real_escape_string($conn, $_POST['phone']);
    $loc   = mysqli_real_escape_string($conn, $_POST['location']);
    $tipo  = mysqli_real_escape_string($conn, $_POST['tipo-conta']);
    $senha = mysqli_real_escape_string($conn, $_POST['password']);

    // Cria o comando SQL para inserir os dados na tabela "utilizador"
    // Todas as contas novas entram como 'pendente' até serem aprovadas pelo Administrador
    $sql = "INSERT INTO utilizador (nome, email, nif, senha, telemovel, localidade, tipo_conta, status)
            VALUES ('$nome', '$email', '$nif', '$senha', '$tel', '$loc', '$tipo', 'pendente')";

    // Executa o comando e verifica se correu bem
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Registo concluído! A tua conta está pendente de aprovação pelo Administrador.');
                window.location.href='../entrar/entrar.html';
              </script>";
    } else {
        echo "Erro ao criar conta: " . $conn->error;
    }
}

// Fecha a ligação à base de dados
$conn->close();
?>

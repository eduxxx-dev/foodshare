<?php
session_start();
include_once("../../conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $senha = mysqli_real_escape_string($conn, trim($_POST['password']));

    $sql = "SELECT id, nome, tipo_conta, status FROM utilizador WHERE email = '$email' AND senha = '$senha'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $utilizador = $resultado->fetch_assoc();

        // Contas de doador/beneficiário/voluntário precisam de aprovação do admin
        if ($utilizador['status'] == 'pendente') {
            header("Location: entrar.html?erro=pendente");
            exit();
        }

        if ($utilizador['status'] == 'reprovado') {
            header("Location: entrar.html?erro=reprovado");
            exit();
        }

        $_SESSION['id'] = $utilizador['id'];
        $_SESSION['nome'] = $utilizador['nome'];
        $_SESSION['tipo_conta'] = $utilizador['tipo_conta'];

        // Redireciona conforme o tipo de conta
        if ($utilizador['tipo_conta'] == 'admin') {
            header("Location: ../admin/admin.php");
        } elseif ($utilizador['tipo_conta'] == 'doador') {
            header("Location: ../doador/doador.php");
        } elseif ($utilizador['tipo_conta'] == 'beneficiario') {
            header("Location: ../beneficiario/benefic.php");
        } elseif ($utilizador['tipo_conta'] == 'voluntario') {
            header("Location: ../voluntario/volunt.php");
        }
        exit();

    } else {
        header("Location: entrar.html?erro=dados_invalidos");
        exit();
    }
} else {
    header("Location: entrar.html");
    exit();
}
?>

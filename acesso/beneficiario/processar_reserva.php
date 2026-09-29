<?php
session_start();
include_once("../../conexao.php");

if (isset($_GET['id']) && isset($_SESSION['id'])) {
    $id_produto = mysqli_real_escape_string($conn, $_GET['id']);
    // Pegamos o ID do beneficiário que está logado na sessão
    $id_beneficiario = mysqli_real_escape_string($conn, $_SESSION['id']);

    // AGORA SIM: Atualiza o estado E guarda quem reservou!
    $sql = "UPDATE doacoes 
            SET status = 'reservado', id_beneficiario = '$id_beneficiario' 
            WHERE id = '$id_produto' AND status = 'ativo'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Reserva efetuada com sucesso!'); 
                window.location.href='benefic.php'; 
              </script>";
    } else {
        echo "Erro ao reservar: " . $conn->error;
    }
} else {
    // Caso alguém aceda ao ficheiro sem ID, volta para a página principal
    header("Location: benefic.php");
}
?>
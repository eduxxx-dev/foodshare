<?php
session_start();
include_once("../../conexao.php");

// 1. Verificar se os dados necessários foram passados via URL
if (isset($_GET['id']) && isset($_GET['acao'])) {
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $acao = $_GET['acao'];
    $novo_status = "";
    $mensagem = "";

    // 2. Lógica de transição de estados (conforme o teu HTML)
    if ($acao == 'aceitar') {
        $novo_status = "em_recolha";
        $mensagem = "Missão aceite! Podes agora ver a rota para a recolha.";
    } 
    elseif ($acao == 'confirmar_recolha') {
        $novo_status = "em_entrega";
        $mensagem = "Recolha confirmada! Alimento pronto para entrega.";
    } 
    elseif ($acao == 'finalizar') {
        $novo_status = "entregue";
        $mensagem = "Parabéns! Missão concluída com sucesso.";
    }

    // 3. Executar a atualização na Base de Dados
    if ($novo_status != "") {
        $sql = "UPDATE doacoes SET status = '$novo_status' WHERE id = '$id'";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('$mensagem'); 
                    window.location.href='volunt.php';
                  </script>";
        } else {
            echo "Erro ao atualizar estado: " . $conn->error;
        }
    }
} else {
    // Se tentarem aceder ao ficheiro sem parâmetros, volta para o painel
    header("Location: volunt.php");
    exit();
}
?>
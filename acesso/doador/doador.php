<?php
session_start();
include_once("../../conexao.php");

// 1. Só entra quem fez login
if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

$id_logado = $_SESSION['id'];
$nome_doador = $_SESSION['nome'] ?? "Doador";

// 2. Consultas para as Estatísticas (Stats)
$res_ativos = $conn->query("SELECT COUNT(*) as total FROM doacoes WHERE id_utilizador = '$id_logado' AND status = 'ativo'");
$cont_ativos = $res_ativos->fetch_assoc()['total'];

$res_total = $conn->query("SELECT COUNT(*) as total FROM doacoes WHERE id_utilizador = '$id_logado'");
$cont_total = $res_total->fetch_assoc()['total'];
?>

<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodShare - Painel do Doador</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/templatemo-kind-heart-charity.css" rel="stylesheet">
    <link href="../../css/template-styles.css" rel="stylesheet">
</head>

<body id="section_1">

    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="../../index.html">
                <img src="../../images/logoo.png" class="logo img-fluid" alt="FoodShare">
                <span>FoodShare <small>Doador</small></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="doador.php">Ver Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfildoad.php">Perfil</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="nav-link custom-btn custom-border-btn btn w-100" href="../entrar/entrar.html">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section class="section-padding">
            <div class="container">
                <div class="app-container">
                    <header class="content-header d-flex justify-content-between align-items-center mb-4">
                        <div class="user-welcome">
                            <h1>Painel de Gestão</h1>
                            <p>Bem-vindo, <strong><?php echo htmlspecialchars($nome_doador); ?></strong>.</p>
                        </div>
                        <a href="produto.html" class="btn btn-primary">Registar Novo Produto</a>
                    </header>

                    <section class="stats-grid d-flex gap-3 mb-5">
                        <div class="stat-box p-4 border rounded shadow-sm bg-white flex-fill">
                            <span class="stat-label d-block text-muted small">Produtos Ativos</span>
                            <span class="stat-value h2 text-success"><?php echo sprintf("%02d", $cont_ativos); ?></span>
                        </div>
                        <div class="stat-box p-4 border rounded shadow-sm bg-white flex-fill">
                            <span class="stat-label d-block text-muted small">Total de Doações</span>
                            <span class="stat-value h2 text-primary"><?php echo sprintf("%02d", $cont_total); ?></span>
                        </div>
                    </section>

                    <section class="data-section">
                        <div class="section-top mb-4">
                            <h2>Produtos Recentes e Histórico</h2>
                        </div>
                        
                        <div class="product-table-wrapper shadow-sm bg-white rounded">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Estado</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // 3. Buscar produtos ativos/histórico
                                    $produtos = $conn->query("SELECT * FROM doacoes WHERE id_utilizador = '$id_logado' AND status != 'removido' ORDER BY id DESC");

                                    if ($produtos && $produtos->num_rows > 0) {
                                        while($row = $produtos->fetch_assoc()) {
                                            
                                            // Simplificação dos Estados usando a estrutura Switch
                                            switch ($row['status']) {
                                                case 'reservado':
                                                    $badge = "bg-warning text-dark"; $nome_status = "Reservado";
                                                    break;
                                                case 'em_recolha':
                                                case 'em_entrega':
                                                    $badge = "bg-info text-dark";    $nome_status = "Em Transporte";
                                                    break;
                                                case 'expirado':
                                                    $badge = "bg-secondary";          $nome_status = "Expirado";
                                                    break;
                                                case 'finalizado':
                                                case 'entregue':
                                                    $badge = "bg-danger";             $nome_status = "Entregue";
                                                    break;
                                                default:
                                                    $badge = "bg-success";            $nome_status = "Disponível";
                                            }
                                    ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($row['produto']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($row['quantidade']); ?></td>
                                                <td><span class="badge <?php echo $badge; ?>"><?php echo $nome_status; ?></span></td>
                                                <td class="text-center">
                                                    <a href="remover_produto.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja remover este produto?')">
                                                        <i class="bi-trash"></i> Remover
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php 
                                        } 
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center py-4'>Nenhum produto encontrado.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </main>

    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
</body>
</html>
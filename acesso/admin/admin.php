<?php
session_start();
include_once("../../conexao.php");

// 1. VERIFICAÇÃO DE SEGURANÇA (Se não houver ID na sessão, vai para o Login)
if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

// 2. CONSULTAS SIMPLES PARA AS ESTATÍSTICAS (Uma por uma)
$res_doadores = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'doador'");
$total_doadores = $res_doadores->fetch_assoc()['total'];

$res_beneficiarios = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'beneficiario'");
$total_beneficiarios = $res_beneficiarios->fetch_assoc()['total'];

$res_voluntarios = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'voluntario'");
$total_voluntarios = $res_voluntarios->fetch_assoc()['total'];

$res_ativos = $conn->query("SELECT COUNT(*) as total FROM doacoes WHERE status = 'ativo'");
$produtos_ativos = $res_ativos->fetch_assoc()['total'];

$res_historico = $conn->query("SELECT COUNT(*) as total FROM doacoes");
$total_doacoes = $res_historico->fetch_assoc()['total'];


// 3. CONSULTAS PARA O SINO DE NOTIFICAÇÕES (Quando alguém se regista ou doa e fica pendente)
$res_p_doadores = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'doador' AND status = 'pendente'");
$pendentes_doadores = $res_p_doadores->fetch_assoc()['total'];

$res_p_beneficiarios = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'beneficiario' AND status = 'pendente'");
$pendentes_beneficiarios = $res_p_beneficiarios->fetch_assoc()['total'];

$res_p_voluntarios = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'voluntario' AND status = 'pendente'");
$pendentes_voluntarios = $res_p_voluntarios->fetch_assoc()['total'];

$res_p_doacoes = $conn->query("SELECT COUNT(*) as total FROM doacoes WHERE status = 'pendente'");
$doacoes_pendentes = $res_p_doacoes->fetch_assoc()['total'];

// Somar tudo para dar o número final do sino
$total_notificacoes = $pendentes_doadores + $pendentes_beneficiarios + $pendentes_voluntarios + $doacoes_pendentes;
?>

<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodShare - Dashboard Administrador</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/templatemo-kind-heart-charity.css" rel="stylesheet">
    <link href="../../css/template-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body id="section_1">

    <!-- MENU DE NAVEGAÇÃO -->
    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <img src="../../images/logoo.png" class="logo img-fluid" alt="FoodShare">
                <span>FoodShare <small>Painel Admin</small></span>
            </a>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="admin.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="gerir_utilizadores.php?tipo=doador">Doadores</a></li>
                    <li class="nav-item"><a class="nav-link" href="gerir_utilizadores.php?tipo=beneficiario">Beneficiários</a></li>
                    <li class="nav-item"><a class="nav-link" href="gerir_utilizadores.php?tipo=voluntario">Voluntários</a></li>
                    <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatórios</a></li>

                    <!-- SINO DE NOTIFICAÇÕES -->
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bell fa-lg text-secondary"></i>
                            <?php if ($total_notificacoes > 0) { ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                                    <?php echo $total_notificacoes; ?>
                                </span>
                            <?php } ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-3" style="min-width: 320px; border-radius: 12px;">
                            <li class="mb-2"><h6 class="dropdown-header p-0 text-dark fw-bold">Aprovações Pendentes</h6></li>
                            <li><hr class="dropdown-divider"></li>

                            <li class="mb-1">
                                <a class="dropdown-item d-flex justify-content-between align-items-center small py-2 w-100" href="gerir_utilizadores.php?tipo=doador&status=pendente">
                                    <span>Doadores Pendentes</span>
                                    <span class="badge bg-warning text-dark ms-2"><?php echo $pendentes_doadores; ?></span>
                                </a>
                            </li>
                            <li class="mb-1">
                                <a class="dropdown-item d-flex justify-content-between align-items-center small py-2 w-100" href="gerir_utilizadores.php?tipo=beneficiario&status=pendente">
                                    <span>Instituições Pendentes</span>
                                    <span class="badge bg-warning text-dark ms-2"><?php echo $pendentes_beneficiarios; ?></span>
                                </a>
                            </li>
                            <li class="mb-1">
                                <a class="dropdown-item d-flex justify-content-between align-items-center small py-2 w-100" href="gerir_utilizadores.php?tipo=voluntario&status=pendente">
                                    <span>Voluntários Pendentes</span>
                                    <span class="badge bg-warning text-dark ms-2"><?php echo $pendentes_voluntarios; ?></span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center small py-2 w-100" href="relatorios.php">
                                    <span>Novas Doações</span>
                                    <span class="badge bg-info ms-2"><?php echo $doacoes_pendentes; ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item ms-lg-3">
                        <a class="nav-link custom-btn custom-border-btn btn py-2" href="../entrar/sair.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CABEÇALHO -->
    <header class="p-5 bg-white shadow-sm mb-4">
        <div class="container">
            <h2>Dashboard de Controlo</h2>
            <p class="text-muted"><i class="fa-solid fa-chart-pie"></i> Visão geral do estado da plataforma FoodShare.</p>
        </div>
    </header>

    <!-- METRICAS EM EXIBIÇÃO -->
    <main class="container">
        <div class="feed-title mb-4"><span class="h4">Estatísticas do Sistema</span></div>

        <div class="row g-4 mb-5">
            <!-- DOADORES -->
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="p-4 border rounded shadow-sm bg-white h-100">
                    <span class="text-muted small text-uppercase d-block fw-bold mb-1">Instituições Doadoras</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h2 text-success mb-0"><?php echo $total_doadores; ?></span>
                        <i class="fa-solid fa-store fa-2x" style="color: #e0e0e0;"></i>
                    </div>
                </div>
            </div>

            <!-- BENEFICIÁRIOS -->
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="p-4 border rounded shadow-sm bg-white h-100">
                    <span class="text-muted small text-uppercase d-block fw-bold mb-1">Instituições Beneficiárias</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h2 text-primary mb-0"><?php echo $total_beneficiarios; ?></span>
                        <i class="fa-solid fa-hand-holding-heart fa-2x" style="color: #e0e0e0;"></i>
                    </div>
                </div>
            </div>

            <!-- VOLUNTÁRIOS -->
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="p-4 border rounded shadow-sm bg-white h-100">
                    <span class="text-muted small text-uppercase d-block fw-bold mb-1">Voluntários Ativos</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h2 text-warning mb-0"><?php echo $total_voluntarios; ?></span>
                        <i class="fa-solid fa-people-carrying-box fa-2x" style="color: #e0e0e0;"></i>
                    </div>
                </div>
            </div>

            <!-- PRODUTOS ATIVOS -->
            <div class="col-12 col-sm-6 col-lg-6">
                <div class="p-4 border rounded shadow-sm bg-white h-100">
                    <span class="text-muted small text-uppercase d-block fw-bold mb-1">Produtos Ativos em Linha</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h2 text-info mb-0"><?php echo $produtos_ativos; ?></span>
                        <i class="fa-solid fa-utensils fa-2x" style="color: #e0e0e0;"></i>
                    </div>
                </div>
            </div>

            <!-- HISTÓRICO -->
            <div class="col-12 col-sm-12 col-lg-6">
                <div class="p-4 border rounded shadow-sm bg-white h-100">
                    <span class="text-muted small text-uppercase d-block fw-bold mb-1">Total Histórico de Doações</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h2 text-secondary mb-0"><?php echo $total_doacoes; ?></span>
                        <i class="fa-solid fa-globe fa-2x" style="color: #e0e0e0;"></i>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

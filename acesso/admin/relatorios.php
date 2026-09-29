<?php
session_start();
include_once("../../conexao.php");

// 1. VERIFICAÇÃO DE SEGURANÇA
if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

$id_admin = $_SESSION['id'];

// 2. CONSULTAS PARA O SINO DE NOTIFICAÇÕES (Uma por uma, simples)
$res_p_doadores = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'doador' AND status = 'pendente'");
$pendentes_doadores = $res_p_doadores->fetch_assoc()['total'];

$res_p_beneficiarios = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'beneficiario' AND status = 'pendente'");
$pendentes_beneficiarios = $res_p_beneficiarios->fetch_assoc()['total'];

$res_p_voluntarios = $conn->query("SELECT COUNT(*) as total FROM utilizador WHERE tipo_conta = 'voluntario' AND status = 'pendente'");
$pendentes_voluntarios = $res_p_voluntarios->fetch_assoc()['total'];

$res_p_doacoes = $conn->query("SELECT COUNT(*) as total FROM doacoes WHERE status = 'pendente'");
$doacoes_pendentes = $res_p_doacoes->fetch_assoc()['total'];

// Soma total das notificações do sino
$total_notificacoes = $pendentes_doadores + $pendentes_beneficiarios + $pendentes_voluntarios + $doacoes_pendentes;


// 3. MÉTRICAS PARA AS CAIXAS DO RELATÓRIO
$res_concluido = $conn->query("SELECT COUNT(*) as total FROM doacoes WHERE status = 'concluido'");
$status_concluido = $res_concluido->fetch_assoc()['total'];

$res_ativo = $conn->query("SELECT COUNT(*) as total FROM doacoes WHERE status = 'ativo'");
$status_ativo = $res_ativo->fetch_assoc()['total'];

// Somar a quantidade total de itens doados
$q_quantidade = $conn->query("SELECT SUM(quantidade) as total FROM doacoes WHERE status = 'concluido'");
$dados_quantidade = $q_quantidade->fetch_assoc();

$total_itens_entregues = 0;
if ($dados_quantidade['total'] != null) {
    $total_itens_entregues = $dados_quantidade['total'];
}


// 4. HISTÓRICO DE DOAÇÕES (Com Join para trazer o nome do Doador)
$sql_historico = "SELECT d.id, d.produto, d.quantidade, d.status as status_doacao, u.nome as doador
                  FROM doacoes d
                  JOIN utilizador u ON d.id_utilizador = u.id
                  ORDER BY d.id DESC LIMIT 15";

$resultado_historico = $conn->query($sql_historico);
?>

<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodShare - Relatórios Globais</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/templatemo-kind-heart-charity.css" rel="stylesheet">
    <link href="../../css/template-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* CSS especial que esconde o menu e botões ao gerar o PDF/Imprimir */
        @media print {
            .navbar, .btn, footer, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                font-size: 12px;
            }
            .container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .shadow-sm, .border {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
</head>

<body id="section_1">

    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <img src="../../images/logoo.png" class="logo img-fluid" alt="FoodShare">
                <span>FoodShare <small>Painel Admin</small></span>
            </a>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="gerir_utilizadores.php?tipo=doador">Doadores</a></li>
                    <li class="nav-item"><a class="nav-link" href="gerir_utilizadores.php?tipo=beneficiario">Beneficiários</a></li>
                    <li class="nav-item"><a class="nav-link" href="gerir_utilizadores.php?tipo=voluntario">Voluntários</a></li>
                    <li class="nav-item"><a class="nav-link active" href="relatorios.php">Relatórios</a></li>

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

    <header class="p-5 bg-white shadow-sm mb-4">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <h2>Relatórios de Impacto</h2>
                <p class="text-muted m-0"><i class="fa-solid fa-file-contract"></i> Dados analíticos e histórico de fluxos da plataforma.</p>
            </div>
            <button onclick="window.print();" class="btn btn-primary no-print fw-bold shadow-sm">
                <i class="fa-solid fa-print"></i> Exportar / Imprimir PDF
            </button>
        </div>
    </header>

    <main class="container mb-5">
        <div class="feed-title mb-4"><span class="h4">Métricas Consolidadas</span></div>

        <div class="row g-4 mb-5">
            <div class="col-12 col-md-4">
                <div class="p-4 border rounded shadow-sm bg-white text-center">
                    <span class="text-muted small text-uppercase fw-bold d-block mb-1">Doações Distribuídas</span>
                    <span class="h2 text-success fw-bold"><?php echo $status_concluido; ?></span>
                    <small class="text-muted d-block mt-1">Processos fechados com sucesso</small>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-4 border rounded shadow-sm bg-white text-center">
                    <span class="text-muted small text-uppercase fw-bold d-block mb-1">Produtos Ativos Disponíveis</span>
                    <span class="h2 text-primary fw-bold"><?php echo $status_ativo; ?></span>
                    <small class="text-muted d-block mt-1">Alimentos em linha para pedido</small>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-4 border rounded shadow-sm bg-white text-center">
                    <span class="text-muted small text-uppercase fw-bold d-block mb-1">Volume Estimado Entregue</span>
                    <span class="h2 text-warning fw-bold"><?php echo $total_itens_entregues; ?> <small style="font-size: 14px;">unidades/kg</small></span>
                    <small class="text-muted d-block mt-1">Soma total de volumes validados</small>
                </div>
            </div>
        </div>

        <div class="feed-title mb-4"><span class="h4">Histórico Geral de Movimentações (Auditoria)</span></div>

        <div class="product-table-wrapper shadow-sm bg-white rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Doação</th>
                        <th>Produto / Alimento</th>
                        <th>Quantidade</th>
                        <th>Id Utilizador (Doador)</th>
                        <th class="text-center">Estado Atual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado_historico->num_rows > 0) { ?>
                        <?php while($row = $resultado_historico->fetch_assoc()) {
                            $status_d = $row['status_doacao'];

                            // Definir a cor do status da doação
                            $badge_class = "bg-warning text-dark";
                            if ($status_d == 'ativo' || $status_d == 'concluido') { $badge_class = "bg-success"; }
                            if ($status_d == 'rejeitado') { $badge_class = "bg-danger"; }
                        ?>
                            <tr>
                                <td><strong>#<?php echo $row['id']; ?></strong></td>
                                <td><span class="fw-bold"><?php echo htmlspecialchars($row['produto']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['quantidade']); ?></td>
                                <td><?php echo htmlspecialchars($row['doador']); ?></td>
                                <td class="text-center">
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars($status_d); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Não foram encontrados registos de doações na base de dados.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

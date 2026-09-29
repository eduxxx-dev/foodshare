<?php
session_start();
include_once("../../conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

$id_voluntario_logado = $_SESSION['id'];

// 1. BUSCA OS DADOS REAIS DO VOLUNTÁRIO LOGADO (Nome e Localidade de Registo)
$sql_voluntario = "SELECT nome, localidade FROM utilizador WHERE id = '$id_voluntario_logado'";
$res_voluntario = $conn->query($sql_voluntario);

if ($res_voluntario && $res_voluntario->num_rows > 0) {
    $dados_voluntario = $res_voluntario->fetch_assoc();
    $nome_voluntario = $dados_voluntario['nome'];
    $morada_voluntario = !empty($dados_voluntario['localidade']) ? $dados_voluntario['localidade'] : "Localidade não especificada";
} else {
    // Fallback de segurança caso falhe a consulta
    $nome_voluntario = $_SESSION['nome'];
    $morada_voluntario = "Localidade não encontrada"; 
}

// 2. CONSULTA DAS MISSÕES (Com JOINs para Doador e Beneficiário usando 'localidade')
$sql = "SELECT d.*, 
                u1.nome AS nome_doador, 
                u1.localidade AS morada_doador,
                u2.nome AS nome_beneficiario, 
                u2.localidade AS morada_beneficiario
        FROM doacoes d
        LEFT JOIN utilizador u1 ON d.id_utilizador = u1.id
        LEFT JOIN utilizador u2 ON d.id_beneficiario = u2.id
        WHERE d.status IN ('reservado', 'em_recolha', 'em_entrega') 
        ORDER BY d.id DESC";

$missoes = $conn->query($sql);
?>

<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodShare - Painel do Voluntário</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/templatemo-kind-heart-charity.css" rel="stylesheet">
    <link href="../../css/template-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .btn-action { 
            margin-top: 10px; 
            width: 100%; 
            font-weight: 600; 
            padding: 12px; 
            border-radius: 10px; 
        }
        .mission-card { transition: 0.3s; border: none; }
        
        @media (min-width: 768px) {
            .actions-wrapper {
                display: flex;
                gap: 15px;
            }
            .btn-action {
                flex: 1;
            }
        }
    </style>
</head>

<body id="section_1">
    
    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="../../index.html">
                <img src="../../images/logoo.png" class="logo img-fluid" alt="FoodShare">
                <span>FoodShare <small>Voluntariado</small></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Ver Missões</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfilvolunt.php">Perfil</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="nav-link custom-btn custom-border-btn btn w-100" href="../entrar/entrar.html">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="p-5 bg-white shadow-sm mb-4">
        <div class="container">
            <h2>Olá, <?php echo htmlspecialchars($nome_voluntario); ?>!</h2>
            <p class="text-muted"><i class="fa-solid fa-location-crosshairs"></i> <?php echo htmlspecialchars($morada_voluntario); ?></p>
        </div>
    </header>

    <main class="container">
        <div class="feed-title mb-4"><span class="h4">Missões Perto de Ti</span></div>

        <?php if ($missoes && $missoes->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $missoes->fetch_assoc()): 
                    $status_atual = $row['status'];
                    $id_p = $row['id'];
                    
                    // Configuração dos dados do DOADOR (Estabelecimento)
                    $doador_nome = !empty($row['nome_doador']) ? $row['nome_doador'] : "Estabelecimento Doador";
                    $morada_coleta = !empty($row['morada_doador']) ? $row['morada_doador'] : $row['localidade'];
                    
                    // Configuração dos dados do BENEFICIÁRIO (Instituição que reservou)
                    $beneficiario_nome = !empty($row['nome_beneficiario']) ? $row['nome_beneficiario'] : "Instituição Beneficiária";
                    $morada_entrega = !empty($row['morada_beneficiario']) ? $row['morada_beneficiario'] : "Localidade do Beneficiário não definida";
                ?>
                    <div class="col-12 col-md-6 mb-4">
                        <div class="card p-4 shadow-sm mission-card h-100">
                            <span class="badge bg-info mb-2 w-auto d-inline-block align-self-start">
                                <i class="fa-solid fa-person-walking"></i> Missão em curso
                            </span>
                            <h3><?php echo htmlspecialchars($row['produto']); ?></h3>
                            
                            <div class="my-3">
                                <p class="mb-2">
                                    <strong><i class="fa-solid fa-arrow-up text-success"></i> Recolher em (Doador):</strong> <br>
                                    <span class="text-primary fw-bold ps-3"><?php echo htmlspecialchars($doador_nome); ?></span><br>
                                    <span class="text-muted small ps-3"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($morada_coleta); ?></span>
                                </p>
                                
                                <p class="mb-0">
                                    <strong><i class="fa-solid fa-arrow-down text-danger"></i> Entregar a (Beneficiário):</strong> <br>
                                    <span class="text-primary fw-bold ps-3"><?php echo htmlspecialchars($beneficiario_nome); ?></span><br>
                                    <span class="text-muted small ps-3"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($morada_entrega); ?></span>
                                </p>
                            </div>

                            <div class="actions-wrapper mt-auto">
                                <?php if ($status_atual == 'reservado'): ?>
                                    <a href="processar_missao.php?id=<?php echo $id_p; ?>&acao=aceitar" class="btn btn-primary btn-action">
                                        Aceitar Missão
                                    </a>

                                <?php elseif ($status_atual == 'em_recolha'): ?>
                                    <button class="btn btn-outline-secondary btn-action" onclick="window.open('http://maps.google.com/?q=<?php echo urlencode($morada_coleta); ?>', '_blank')">
                                        <i class="fa-solid fa-map-location-dot"></i> Abrir GPS Recolha
                                    </button>
                                    <a href="processar_missao.php?id=<?php echo $id_p; ?>&acao=confirmar_recolha" class="btn btn-outline-success btn-action">
                                        <i class="fa-solid fa-check"></i> Já recolhi o alimento
                                    </a>

                                <?php elseif ($status_atual == 'em_entrega'): ?>
                                    <button class="btn btn-outline-secondary btn-action" onclick="window.open('http://maps.google.com/?q=<?php echo urlencode($morada_entrega); ?>', '_blank')">
                                        <i class="fa-solid fa-house-medical"></i> Abrir GPS Entrega
                                    </button>
                                    <a href="processar_missao.php?id=<?php echo $id_p; ?>&acao=finalizar" class="btn btn-success btn-action">
                                        <i class="fa-solid fa-circle-check"></i> Alimento Entregue
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-light text-center shadow-sm">Nenhuma missão pendente de transporte.</div>
        <?php endif; ?>
    </main>

    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
</body>
</html>
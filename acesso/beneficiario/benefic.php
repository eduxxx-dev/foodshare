<?php 
session_start();
include_once("../../conexao.php"); 

if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

$id_beneficiario = $_SESSION['id'];

// Busca os dados do Beneficiário
$res_beneficiario = $conn->query("SELECT nome, localidade FROM utilizador WHERE id = '$id_beneficiario'");
$dados = $res_beneficiario->fetch_assoc();

$nome_beneficiario = $dados['nome'] ?? $_SESSION['nome'] ?? "Beneficiário";
$morada_beneficiario = !empty($dados['localidade']) ? $dados['localidade'] : "Localidade não especificada";

// Procura apenas produtos ativos
$resultado = $conn->query("SELECT * FROM doacoes WHERE status = 'ativo' ORDER BY id DESC");
?>
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodShare - Painel do Beneficiário</title>

    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/templatemo-kind-heart-charity.css" rel="stylesheet">
    <link href="../../css/template-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Mantemos apenas o essencial que o Bootstrap não faz sozinho */
        .food-img { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; }
        .food-category { font-size: 11px; color: #5bc1ac; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
        .text-custom { color: #5bc1ac; }
        .btn-reserve { background-color: #5bc1ac; color: white !important; transition: 0.3s; }
        .btn-reserve:hover { background: #4cae9a; }
    </style>
</head>

<body id="section_1">

    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="../../index.html">
                <img src="../../images/logoo.png" class="logo img-fluid" alt="FoodShare">
                <span>FoodShare <small>Beneficiário</small></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Ver Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfilbenef.php">Perfil</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="nav-link custom-btn custom-border-btn btn w-100" href="../entrar/entrar.html">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="p-5 bg-white shadow-sm mb-4">
        <div class="container">
            <h2>Olá, <?php echo htmlspecialchars($nome_beneficiario); ?>!</h2>
            <p class="text-muted"><i class="fa-solid fa-location-crosshairs text-custom"></i> <?php echo htmlspecialchars($morada_beneficiario); ?></p>
        </div>
    </header>

    <main class="container">
        <h4 class="mb-4">Alimentos Disponíveis</h4>

        <div class="row g-4 mb-5">
            <?php 
            if ($resultado && $resultado->num_rows > 0) {
                while($row = $resultado->fetch_assoc()) { 
            ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 p-4 border-0 shadow-sm justify-content-between">
                            
                            <div class="d-flex align-items-center mb-3">
                                <img src="../../images/<?php echo htmlspecialchars($row['imagem']); ?>" alt="Alimento" class="food-img me-3">
                                <div>
                                    <span class="food-category d-block"><?php echo htmlspecialchars($row['categoria']); ?></span>
                                    <h5 class="fw-bold text-dark my-1"><?php echo htmlspecialchars($row['produto']); ?></h5>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Disponível</span>
                                </div>
                            </div>

                            <div class="small text-muted mb-3">
                                <div class="mb-1"><i class="fa-solid fa-box text-custom me-2" style="width: 15px;"></i><strong>Qtd:</strong> <?php echo htmlspecialchars($row['quantidade']); ?></div>
                                <div class="mb-2"><i class="fa-solid fa-clock text-custom me-2" style="width: 15px;"></i><strong>Validade:</strong> <?php echo date('d/m/Y', strtotime($row['validade'])); ?></div>
                                <p class="mb-0 mt-2 text-secondary"><?php echo htmlspecialchars($row['descricao']); ?></p>
                            </div>

                            <a href="processar_reserva.php?id=<?php echo $row['id']; ?>" class="btn btn-reserve w-100 py-2 rounded-3 fw-bold" onclick="return confirm('Deseja reservar este produto? Ele deixará de estar visível para outros utilizadores.')">
                                <i class="fa-solid fa-check-circle me-1"></i> Solicitar Reserva
                            </a>

                        </div>
                    </div>
            <?php 
                } 
            } else { 
            ?>
                <div class="col-12 text-center py-5 bg-white rounded shadow-sm">
                    <i class="fa-solid fa-utensils fa-3x text-muted mb-3"></i>
                    <p class="lead text-muted mb-0">Não existem produtos disponíveis de momento.</p>
                </div>
            <?php } ?>
        </div>
    </main>

    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
</body>
</html>
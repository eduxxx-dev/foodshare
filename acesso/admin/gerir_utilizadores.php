<?php
session_start();
include_once("../../conexao.php");

// 1. VERIFICAÇÃO DE SEGURANÇA
if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

// 2. IDENTIFICAR O TIPO DE UTILIZADOR (Doador, Beneficiário ou Voluntário)
$tipo_atual = 'doador';
if (isset($_GET['tipo'])) {
    $tipo_atual = mysqli_real_escape_string($conn, $_GET['tipo']);
}

// 3. AÇÕES DO ADMINISTRADOR (Aprovar ou Eliminar)
if (isset($_GET['acao']) && isset($_GET['user_id'])) {
    $u_id = intval($_GET['user_id']);
    $acao = $_GET['acao'];

    if ($acao == 'aprovar') {
        $conn->query("UPDATE utilizador SET status = 'aprovado' WHERE id = $u_id");
    }
    if ($acao == 'eliminar') {
        $conn->query("DELETE FROM utilizador WHERE id = $u_id");
    }

    // Recarregar a página limpando a ação da barra de endereços
    header("Location: gerir_utilizadores.php?tipo=" . urlencode($tipo_atual));
    exit();
}

// 4. FILTROS E BARRA DE PESQUISA
$busca = '';
if (isset($_POST['pesquisa'])) {
    $busca = mysqli_real_escape_string($conn, $_POST['pesquisa']);
}

$filtro_status = 'todos';
if (isset($_GET['status'])) {
    $filtro_status = mysqli_real_escape_string($conn, $_GET['status']);
}

// Construção da Consulta SQL Base
$sql_users = "SELECT * FROM utilizador WHERE tipo_conta = '$tipo_atual'";

// Adicionar filtro de status se for diferente de 'todos'
if ($filtro_status != 'todos') {
    $sql_users = $sql_users . " AND status = '$filtro_status'";
}

// Adicionar filtro de pesquisa por texto se o campo não estiver vazio
if ($busca != '') {
    $sql_users = $sql_users . " AND (nome LIKE '%$busca%' OR email LIKE '%$busca%')";
}

$sql_users = $sql_users . " ORDER BY id DESC";
$resultado_users = $conn->query($sql_users);
?>

<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodShare - Gerir Utilizadores</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/templatemo-kind-heart-charity.css" rel="stylesheet">
    <link href="../../css/template-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatórios</a></li>
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link custom-btn custom-border-btn btn py-2" href="../entrar/sair.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="p-5 bg-white shadow-sm mb-4">
        <div class="container">
            <h2>Controlo de Contas</h2>
            <p class="text-muted"><i class="fa-solid fa-users-gear"></i> Aprove ou remova perfis registados no sistema.</p>
        </div>
    </header>

    <main class="container mb-5">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div class="btn-group shadow-sm">
                <a href="gerir_utilizadores.php?tipo=<?php echo htmlspecialchars($tipo_atual); ?>&status=todos" class="btn btn-outline-secondary">Todos</a>
                <a href="gerir_utilizadores.php?tipo=<?php echo htmlspecialchars($tipo_atual); ?>&status=aprovado" class="btn btn-outline-success">Aprovados</a>
                <a href="gerir_utilizadores.php?tipo=<?php echo htmlspecialchars($tipo_atual); ?>&status=pendente" class="btn btn-outline-warning text-dark">Pendentes</a>
            </div>

            <form action="gerir_utilizadores.php?tipo=<?php echo htmlspecialchars($tipo_atual); ?>&status=<?php echo htmlspecialchars($filtro_status); ?>" method="POST" class="d-flex m-0">
                <div class="input-group">
                    <input type="text" name="pesquisa" class="form-control" placeholder="Pesquise por nome ou email..." value="<?php echo htmlspecialchars($busca); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>

        <div class="product-table-wrapper shadow-sm bg-white rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Status</th>
                        <th class="text-center">Ações / Gerir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado_users->num_rows > 0) { ?>
                        <?php while($row = $resultado_users->fetch_assoc()) {
                            $st = $row['status'];

                            // Definir a cor do badge baseado no status
                            $badge = "bg-warning text-dark"; // Padrão: Pendente
                            if ($st == 'aprovado') { $badge = "bg-success"; }
                            if ($st == 'reprovado') { $badge = "bg-danger"; }
                        ?>
                            <tr>
                                <td><strong>#<?php echo $row['id']; ?></strong></td>
                                <td><span class="fw-bold"><?php echo htmlspecialchars($row['nome']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>

                                <td>
                                    <?php
                                    if (isset($row['telemovel']) && !empty($row['telemovel'])) {
                                        echo htmlspecialchars($row['telemovel']);
                                    } else {
                                        echo "Não indicado";
                                    }
                                    ?>
                                </td>

                                <td><span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($st); ?></span></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">

                                        <?php if($st != 'aprovado') { ?>
                                            <a href="gerir_utilizadores.php?tipo=<?php echo htmlspecialchars($tipo_atual); ?>&user_id=<?php echo $row['id']; ?>&acao=aprovar" class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i></a>
                                        <?php } ?>

                                        <a href="gerir_utilizadores.php?tipo=<?php echo htmlspecialchars($tipo_atual); ?>&user_id=<?php echo $row['id']; ?>&acao=eliminar" class="btn btn-sm btn-danger" onclick="return confirm('Deseja eliminar esta conta?')"><i class="bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nenhum utilizador encontrado.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

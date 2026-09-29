<?php
session_start();
include_once("../../conexao.php");

// 1. Verificação de Segurança: Só entra quem fez login
if (!isset($_SESSION['id'])) {
    header("Location: ../entrar/entrar.html");
    exit();
}

$id_beneficiario_logado = $_SESSION['id'];
$mensagem = "";
$classe_mensagem = "";

// 2. Processar a Atualização do Perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $nova_localidade = mysqli_real_escape_string($conn, $_POST['localidade']);
    $novo_email = mysqli_real_escape_string($conn, $_POST['email']);
    $nova_senha = $_POST['senha'];

    $verificar_email = $conn->query("SELECT id FROM utilizador WHERE email = '$novo_email' AND id != '$id_beneficiario_logado'");

    if ($verificar_email->num_rows > 0) {
        $mensagem = "Este e-mail já está a ser utilizado por outra conta.";
        $classe_mensagem = "alert-danger";
    } else {
        if (!empty($nova_senha)) {
            $sql_update = "UPDATE utilizador SET nome = '$novo_nome', localidade = '$nova_localidade', email = '$novo_email', senha = '$nova_senha' WHERE id = '$id_beneficiario_logado'";
        } else {
            $sql_update = "UPDATE utilizador SET nome = '$novo_nome', localidade = '$nova_localidade', email = '$novo_email' WHERE id = '$id_beneficiario_logado'";
        }

        if ($conn->query($sql_update)) {
            $mensagem = "Perfil atualizado com sucesso!";
            $classe_mensagem = "alert-success";
            $_SESSION['nome'] = $novo_nome;
        } else {
            $mensagem = "Erro ao atualizar o perfil: " . $conn->error;
            $classe_mensagem = "alert-danger";
        }
    }
}

// 3. Buscar os dados atuais do Beneficiário
$sql_user = "SELECT * FROM utilizador WHERE id = '$id_beneficiario_logado'";
$res_user = $conn->query($sql_user);
$dados_user = $res_user->fetch_assoc();
?>

<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodShare - Meu Perfil</title>

    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/templatemo-kind-heart-charity.css" rel="stylesheet">
    <link href="../../css/template-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body id="section_1">

    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="../../index.html">
                <img src="../../images/logoo.png" class="logo img-fluid" alt="FoodShare">
                <span>FoodShare <small>Beneficiário</small></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="benefic.php">Ver Produtos</a></li>
                    <li class="nav-item"><a class="nav-link active" href="perfilbenef.php">Perfil</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="nav-link custom-btn custom-border-btn btn w-100" href="../entrar/entrar.html">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="p-5 bg-white shadow-sm mb-4">
        <div class="container">
            <h2>O Meu Perfil</h2>
            <p class="text-muted"><i class="fa-solid fa-location-crosshairs"></i> Gerir meus dados</p>
        </div>
    </header>

    <main class="container">
        <div class="feed-title mb-4">
            <span class="h4">Informações de Registo</span>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 mb-5">

                <?php if (!empty($mensagem)): ?>
                    <div class="alert <?php echo $classe_mensagem; ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <?php echo $mensagem; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="p-4 border rounded shadow-sm bg-white">
                    <form action="perfilbenef.php" method="POST">

                        <div class="mb-3">
                            <label for="nome" class="form-label text-muted small text-uppercase fw-bold">Nome da Instituição / Beneficiário</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($dados_user['nome']); ?>" required style="border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label text-muted small text-uppercase fw-bold">E-mail de Contacto</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($dados_user['email']); ?>" required style="border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label for="localidade" class="form-label text-muted small text-uppercase fw-bold">Localidade / Distrito</label>
                            <input type="text" class="form-control" id="localidade" name="localidade" value="<?php echo htmlspecialchars($dados_user['localidade']); ?>" required style="border-radius: 8px;">
                        </div>

                        <div class="mb-4">
                            <label for="senha" class="form-label text-muted small text-uppercase fw-bold">Nova Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Deixe em branco para manter a atual" style="border-radius: 8px;">
                            <div class="form-text text-muted small mt-1">Preencha apenas se pretender alterar o acesso atual.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px; font-weight: 600;">
                                <i class="fa-solid fa-floppy-disk"></i> Atualizar Dados
                            </button>
                            <a href="benefic.php" class="text-center text-muted small mt-2 text-decoration-none">
                                <i class="fa-solid fa-arrow-left"></i> Voltar à Lista de Alimentos
                            </a>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </main>

    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
</body>
</html>

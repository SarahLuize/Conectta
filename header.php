<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'dbconexao.php';
$conexao = obterConexao();

$id_logado = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$username_logado = '';

// Se tiver usuário logado, busca o nome_usuario dele no banco de dados
if ($id_logado) {
    $sqlHeader = "SELECT nome_usuario, foto_perfil FROM usuario WHERE id = '$id_logado'";
    $resHeader = mysqli_query($conexao, $sqlHeader);

    if ($resHeader && $rowHeader = mysqli_fetch_assoc($resHeader)) {
        $username_logado = $rowHeader['nome_usuario'];

        if (!empty($rowHeader['foto_perfil'])) {
            $fotoPerfil = $rowHeader['foto_perfil'];
        }
    }
}
?>

<div class="col-md-auto">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-4">
                <a class="nav-link active" href="feed.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z" />
                        <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
                    </svg>
                    Página inicial
                </a>
                <a class="nav-link active" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901" />
                    </svg>
                    Notificações
                </a>
                <a class="nav-link active" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z" />
                    </svg>
                    Mensagens
                </a>
            </div>
            <div class="d-flex justify-content-center">
                <a class="nav-link active" href="index.php" title="Connectta">📞</a>
            </div>
            <div class="d-flex align-content-center gap-2">
                <form class="d-flex" role="search" action="#">
                    <div class="input-group input-group-sm">
                        <input class="form-control me-2 rounded-pill" type="search" name="search" id="search" placeholder="Procure no Conectta">
                        <button class="btn btn-outline-success rounded" type="submit" title="Procurar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                        </button>
                    </div>
                </form>
                <a class="rounded d-flex align-items-center ms-1" href="perfil.php?username=<?php echo urlencode($username_logado); ?>">
                    <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Perfil" title="Ir para seu perfil" class="rounded" style="height: 32px; width:32px; object-fit:cover;">
                </a>
                <button class="btn btn-outline-primary rounded" title="Postar">Postagem</button>
            </div>
        </div>
    </nav>
</div>
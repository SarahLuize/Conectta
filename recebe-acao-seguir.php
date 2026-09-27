<?php
session_start();
require_once 'dbconexao.php';
$conexao = obterConexao();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
$id_perfil_logado = $_SESSION['usuario_id'];
$paginaRedirecionada = $_POST['paginaRedirecionar'] ?? 'feed.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //pega IDs do usuario a ser seguido
    if (isset($_POST['idPerfilVisitado'])) {
        $id_perfil_visitado = $_POST['idPerfilVisitado'];
    } else {
        header("Location: " . $paginaRedirecionada);
        exit;
    }
    $nome_usuario_perfil_visitado = $_POST['nomeUsuarioPerfilVisitado'] ?? '';

    if (!$id_perfil_visitado || $id_perfil_logado == $id_perfil_visitado) {
        header("Location: " . $paginaRedirecionada);
        exit;
    }

    //VERIFICAR SE O USUARIO LOGADO JÁ SEGUE O USUARIO
    $stmt = mysqli_prepare($conexao, "SELECT quem_esta_seguindo, quem_foi_seguido FROM seguidores WHERE quem_esta_seguindo = ? AND quem_foi_seguido = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id_perfil_logado, $id_perfil_visitado);
    mysqli_stmt_execute($stmt);
    $resVerificarSeSegueUsuario = mysqli_stmt_get_result($stmt);
    //Se já seguir 
    $UsuarioLogadoJaSegueUsuario = mysqli_num_rows($resVerificarSeSegueUsuario) > 0;

    if ($UsuarioLogadoJaSegueUsuario) {
        header("Location: " . $paginaRedirecionada);
        exit;
    }

    //ADICIONA NO BANCO DE DADOS TABELA SEGUIDORES
    $stmtAtualizarBancoDeDados = mysqli_prepare($conexao, "INSERT INTO seguidores (quem_esta_seguindo, quem_foi_seguido)
    VALUES (?, ?)");
    mysqli_stmt_bind_param($stmtAtualizarBancoDeDados, "ii", $id_perfil_logado, $id_perfil_visitado);

    if (mysqli_stmt_execute($stmtAtualizarBancoDeDados)) {
        //ADICIONA +1 NA TABELA SEGUINDO DO USUARIO LOGADO
        $stmtSeguindo = mysqli_prepare($conexao, "UPDATE usuario
        SET numero_seguindo = numero_seguindo + 1
        WHERE id = ?");
        mysqli_stmt_bind_param($stmtSeguindo, "i", $id_perfil_logado);
        mysqli_stmt_execute($stmtSeguindo);

        //ADICIONA +1 NA TABELA SEGUIDORES DO USUARIO A SER SEGUIDO
        $stmtSeguidores = mysqli_prepare($conexao, "UPDATE usuario
        SET numero_seguidores = numero_seguidores + 1
        WHERE id = ?");
        mysqli_stmt_bind_param($stmtSeguidores, "i", $id_perfil_visitado);
        mysqli_stmt_execute($stmtSeguidores);

        header("Location: " . $paginaRedirecionada);
        exit;
    } else {
        echo "Erro ao seguir usuário." . mysqli_stmt_error($stmtAtualizarBancoDeDados);
    }
} else {
    header("Location: " . $paginaRedirecionada);
    exit;
}
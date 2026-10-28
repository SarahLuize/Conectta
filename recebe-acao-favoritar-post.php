<?php
session_start();
require_once 'dbconexao.php';
require_once 'funcoes.php';
$conexao = obterConexao();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
$id_perfil_logado = $_SESSION['usuario_id'];
$paginaRedirecionada = $_POST['paginaRedirecionar'] ?? 'feed.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_post = $_POST['idPost'] ?? 0;

    $usuarioJaFavoritou = verificarSeFavoritouPost($conexao, $_SESSION['usuario_id'], $id_post);

    if ($usuarioJaFavoritou) {
        header("Location: " . $paginaRedirecionada);
        exit;
    }

    //salvar no banco de dados
    $stmtFavoritar = mysqli_prepare($conexao, "INSERT INTO favoritos(id_usuario, id_post)
    VALUES(?, ?)");
    if ($stmtFavoritar) {
        mysqli_stmt_bind_param($stmtFavoritar, 'ii', $id_perfil_logado, $id_post);
        if (mysqli_stmt_execute($stmtFavoritar)) {
            header("Location: " . $paginaRedirecionada);
            exit;
        } else {
            echo "Erro ao favoritar postagem." . mysqli_stmt_error($stmtFavoritar);
        }
        mysqli_stmt_close($stmtFavoritar);
    }
} else {
    header("Location: " . $paginaRedirecionada);
    exit;
}
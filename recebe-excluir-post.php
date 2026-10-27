<?php
session_start();
require_once 'dbconexao.php';
require_once 'funcoes.php';
$conexao = obterConexao();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_perfil_logado = $_SESSION['usuario_id'];
    $paginaRedirecionada = $_POST['paginaRedirecionar'] ?? 'feed.php';
    $id_post = $_POST['idPost'] ?? 0;

    $sqlBuscarAnexos = mysqli_prepare($conexao, "SELECT anexo, anexo2 FROM postagem WHERE id = ? AND id_usuario = ?");
    if ($sqlBuscarAnexos) {
        mysqli_stmt_bind_param($sqlBuscarAnexos, "ii", $id_post, $id_perfil_logado);
        mysqli_stmt_execute($sqlBuscarAnexos);
        $respostaAtual = mysqli_stmt_get_result($sqlBuscarAnexos);

        if ($dadosAntigos = mysqli_fetch_assoc($respostaAtual)) {
            $anexo1banco = $dadosAntigos['anexo'] ?? '';
            $anexo2banco = $dadosAntigos['anexo2'] ?? '';

            if (!empty($anexo1banco) && file_exists($anexo1banco)) {
                unlink($anexo1banco);
            }

            if (!empty($anexo2banco) && file_exists($anexo2banco)) {
                unlink($anexo2banco);
            }
        }
        mysqli_stmt_close($sqlBuscarAnexos);
    }

    $stmtApagarPost = mysqli_prepare($conexao, "DELETE FROM postagem
    WHERE id = ? AND id_usuario = ?;");
    if ($stmtApagarPost) {
        mysqli_stmt_bind_param($stmtApagarPost, "ii", $id_post, $id_perfil_logado);
        mysqli_stmt_execute($stmtApagarPost);
        mysqli_stmt_close($stmtApagarPost);
    }
    header("Location: " . $paginaRedirecionada);
    exit;
}
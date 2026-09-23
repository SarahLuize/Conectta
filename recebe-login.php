<?php
session_start();
require_once 'dbconexao.php';
$conexao = obterConexao();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginUsuario = mysqli_real_escape_string($conexao, $_POST['LoginUsuario']);
    $senha = $_POST['LoginSenha'];

    $sql = "SELECT * FROM usuario WHERE (email = '$loginUsuario' OR nome_usuario = '$loginUsuario')";
    $resultado = mysqli_query($conexao, $sql);

    if ($resultado && mysqli_num_rows($resultado)>0) {
        $usuario = mysqli_fetch_assoc($resultado);

        if(password_verify ($senha, $usuario['senha'])){
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            $destino = !empty($_POST['redirect']) ? $_POST['redirect'] : 'feed.php';
            header("Location: feed.php");
            exit;
        } else{
            $_SESSION['erro_login'] = "Senha incorreta!";
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['erro_login'] = "Usuário ou e-mail não encontrado!";
        header("Location: index.php");
        exit;
    }

}

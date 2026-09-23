<?php
session_start();
require_once 'dbconexao.php';
$conexao = obterConexao();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = mysqli_real_escape_string($conexao, $_POST['CadastrarNome']);
    $email = mysqli_real_escape_string($conexao, $_POST['CadastrarEmail']);
    $senha = $_POST['CadastrarSenha'];
    $confirmarSenha = $_POST['CadastrarConfSenha'];
    $nomeUsuario = mysqli_real_escape_string($conexao, $_POST['CadastrarNomeUsuario']);

    if ($senha !== $confirmarSenha) {
        //alterar futuramente para mensagem javascript
        $_SESSION['erro_senhas'] = "As senhas não coincidem. Tente novamente!";
        header("Location: cadastro.php");
        exit;
    }
    //VERIFICA SE O NOME DE USUARIO É VALIDO (letras, números, underline e SEM espaço)
    // Se não tiver letras, números, ou underline no nome de usuário aparece mensagem de erro
    if(!preg_match('/^[A-Za-z0-9_]+$/',$nomeUsuario)){
        $_SESSION['erro_nomeusuario'] = "Nome de usuário pode ter somente letra e números, não pode ter espaços";
        header("Location: cadastro.php");
        exit;
    }

    //TRANSFORMA EM HASH
    $SenhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario(email, senha, nome, nome_usuario) VALUES('$email', '$SenhaHash', '$nome', '$nomeUsuario')";
    $resultado = mysqli_query($conexao, $sql);

    if ($resultado) {
        $_SESSION['sucesso_cadastro'] = "Conta cadastrada com sucesso!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['erro_cadastro'] = "Erro ao cadastrar conta!";
        header("Location: cadastro.php");
        exit;
    }
}

<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'conectta');
define('DB_CHARSET', 'utf8mb4');

$conexao = null;

function conectarBanco() {
    global $conexao;
    
    if ($conexao !== null) {
        return $conexao;
    }
    
    $conexao = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conexao) {
        die('Erro ao conectar ao banco de dados: ' . mysqli_connect_error());
    }
    
    mysqli_set_charset($conexao, DB_CHARSET);
    
    return $conexao;
}

function fecharConexao() {
    global $conexao;
    
    if ($conexao !== null) {
        mysqli_close($conexao);
        $conexao = null;
    }
}

function obterConexao() {
    return conectarBanco();
}
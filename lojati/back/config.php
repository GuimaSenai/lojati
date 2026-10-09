
<?php

$host = "localhost";
$banco = "LojaTI";
$usuario = "root";
$senha = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    http_response_code(500);

    header("Content-Type: application/json; charset=utf-8");

    echo json_encode([
        "erro" => "Erro ao conectar com o banco de dados."
    ]);

    exit;
}
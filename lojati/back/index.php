
<?php

header("Content-Type: application/json; charset=utf-8");

require_once "config.php";

$rota = $_GET["rota"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];

$dados = json_decode(file_get_contents("php://input"), true);

if (!is_array($dados)) {
    $dados = $_POST;
}

try {

    // CADASTRAR CLIENTE
    if ($rota == "clientes/create" && $metodo == "POST") {

        $nome = trim($dados["Nome"] ?? $dados["nome"] ?? "");
        $email = trim($dados["Email"] ?? $dados["email"] ?? "");
        $telefone = trim($dados["Telefone"] ?? $dados["telefone"] ?? "");
        $endereco = trim($dados["Endereco"] ?? $dados["endereco"] ?? "");

        if ($nome == "" || $email == "") {
            http_response_code(400);

            echo json_encode([
                "erro" => "Nome e Email são obrigatórios."
            ]);

            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);

            echo json_encode([
                "erro" => "Digite um e-mail válido."
            ]);

            exit;
        }

        $sql = "INSERT INTO Clientes
                (Nome, Email, Telefone, Endereco)
                VALUES (:nome, :email, :telefone, :endereco)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":nome" => $nome,
            ":email" => $email,
            ":telefone" => $telefone,
            ":endereco" => $endereco
        ]);

        http_response_code(201);

        echo json_encode([
            "mensagem" => "Cliente cadastrado com sucesso!",
            "ClienteID" => $pdo->lastInsertId()
        ]);

        exit;
    }

    // LISTAR CLIENTES
    if ($rota == "clientes" && $metodo == "GET") {

        $sql = "SELECT ClienteID, Nome, Email, Telefone, Endereco
                FROM Clientes";

        $stmt = $pdo->query($sql);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($clientes);

        exit;
    }

    // ROTA NÃO ENCONTRADA
    http_response_code(404);

    echo json_encode([
        "erro" => "Rota não encontrada."
    ]);

} catch (PDOException $e) {

    if ($e->getCode() == "23000") {
        http_response_code(409);

        echo json_encode([
            "erro" => "Este e-mail já está cadastrado."
        ]);
    } else {
        http_response_code(500);

        echo json_encode([
            "erro" => "Erro ao executar a operação no banco de dados."
        ]);
    }
}
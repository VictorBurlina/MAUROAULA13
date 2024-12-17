<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$servidor = 'localhost';
$banco = 'leilao';  
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_item = $_POST['id_item'];
    $valor = $_POST['valor'];
    $id_usuario = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO lances (id_item, id_usuario, valor, data_lance) VALUES (:id_item, :id_usuario, :valor, NOW())");
    $stmt->execute([
        ':id_item' => $id_item,
        ':id_usuario' => $id_usuario,
        ':valor' => $valor
    ]);

    echo "Lance realizado com sucesso! <br>";
    echo '<a href="itens_abertos.php">Voltar para Itens Abertos</a>';
} else {
    echo "Erro ao tentar realizar o lance.";
}
?>

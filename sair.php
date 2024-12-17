<?php
session_start();
$servidor = 'localhost';
$banco = 'leilao';  
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_item = $_POST['id_item'];
        $stmt = $pdo->prepare("UPDATE itens SET estado = 'encerrado', vencedor = :vencedor WHERE id = :id_item");
        $vencedor = $_SESSION['usuario_id'];
        $stmt->bindParam(':vencedor', $vencedor);
        $stmt->bindParam(':id_item', $id_item);
        $stmt->execute();

        header("Location: meus_lances.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

header("Location: login.php");
?>
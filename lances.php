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

$id_item = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM lances WHERE id_item = :id_item");
$stmt->execute([':id_item' => $id_item]);
$lances = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lances</title>
</head>
<body>
    <h1>Lances para o Item</h1>
    <ul>
        <?php foreach ($lances as $lance): ?>
            <li>
                Usuário ID: <?php echo $lance['id_usuario']; ?><br>
                Valor: <?php echo $lance['valor']; ?><br>
                Data: <?php echo $lance['data_lance']; ?><br>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

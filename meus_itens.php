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

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT i.*, l.valor, l.data_lance FROM itens i LEFT JOIN lances l ON i.id = l.id_item WHERE i.usuario_id = :user_id ORDER BY i.estado ASC");
$stmt->execute([':user_id' => $user_id]);
$meus_itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Itens</title>
</head>
<body>
    <h1>Meus Itens</h1>
    <ul>
        <?php foreach ($meus_itens as $item): ?>
            <li>
                <img src="<?php echo $item['imagem']; ?>" alt="<?php echo $item['nome']; ?>" width="100"><br>
                Nome: <?php echo $item['nome']; ?><br>
                Lance Mínimo: <?php echo $item['minimo']; ?><br>
                Estado: <?php echo $item['estado']; ?><br>
                <a href="detalhe_item.php?id=<?php echo $item['id']; ?>">Ver Detalhes</a>
                | <a href="realizar_lance.php?id=<?php echo $item['id']; ?>">Dar Lance</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="sair.php">Encerrar Sessão</a>
</body>
</html>

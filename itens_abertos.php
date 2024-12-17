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
$stmt = $pdo->prepare("SELECT * FROM itens WHERE estado = 'aberto' AND usuario_id != :user_id");
$stmt->execute([':user_id' => $user_id]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Itens Abertos</title>
</head>
<body>
    <h1>Itens Abertos</h1>
    <ul>
        <?php foreach ($itens as $item): ?>
            <li>
                <img src="<?php echo $item['imagem']; ?>" alt="<?php echo $item['nome']; ?>" width="100"><br>
                Nome: <?php echo $item['nome']; ?><br>
                Lance Mínimo: <?php echo $item['minimo']; ?><br>
                <a href="detalhe_item.php?id=<?php echo $item['id']; ?>">Ver detalhes</a> | 
                <a href="meus_itens.php">Meus Itens</a> | 
                <a href="itens_vencidos.php">Itens Vencidos</a>
                <form action="realizar_lance.php" method="POST">
                    <input type="hidden" name="id_item" value="<?php echo $item['id']; ?>">
                    <label for="valor">Novo Lance:</label>
                    <input type="number" name="valor" required>
                    <input type="submit" value="Dar Lance">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="sair.php">Encerrar Sessão</a>
</body>
</html>

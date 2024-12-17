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

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM itens WHERE id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt_lances = $pdo->prepare("SELECT l.*, u.nomeUsuario FROM lances l INNER JOIN usuarios u ON l.id_usuario = u.id WHERE id_item = :id");
$stmt_lances->execute([':id' => $id]);
$lances = $stmt_lances->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Item</title>
</head>
<body>
    <h1>Detalhes do Item</h1>
    <img src="<?php echo $item['imagem']; ?>" alt="<?php echo $item['nome']; ?>" width="200"><br>
    Nome: <?php echo $item['nome']; ?><br>
    Lance Mínimo: <?php echo $item['minimo']; ?><br>
    Estado: <?php echo $item['estado']; ?><br>
    Vencedor: <?php echo $item['vencedor']; ?><br>
    <h2>Lances</h2>
    <ul>
        <?php foreach ($lances as $lance): ?>
            <li>
                Usuário: <?php echo $lance['nomeUsuario']; ?><br>
                Valor: <?php echo $lance['valor']; ?><br>
                Data: <?php echo $lance['data_lance']; ?><br>
            </li>
        <?php endforeach; ?>
    </ul>
    <form action="realizar_lance.php" method="POST">
        <input type="hidden" name="id_item" value="<?php echo $item['id']; ?>">
        <label for="valor">Novo Lance:</label>
        <input type="number" name="valor" required>
        <input type="submit" value="Dar Lance">
    </form>
    <a href="itens_abertos.php">Voltar para Itens Abertos</a>
    <a href="sair.php">Encerrar Sessão</a>
</body>
</html>

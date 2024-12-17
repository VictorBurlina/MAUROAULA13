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
$stmt = $pdo->prepare("SELECT * FROM itens WHERE estado = 'vencido' AND usuario_id != :user_id");
$stmt->execute([':user_id' => $user_id]);
$itens_vencidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Itens Vencidos</title>
</head>
<body>
    <h1>Itens Vencidos</h1>
    <ul>
        <?php foreach ($itens_vencidos as $item): ?>
            <li>
                <img src="<?php echo $item['imagem']; ?>" alt="<?php echo $item['nome']; ?>" width="100"><br>
                Nome: <?php echo $item['nome']; ?><br>
                Lance Mínimo: <?php echo $item['minimo']; ?><br>
                Estado: <?php echo $item['estado']; ?><br>
                Vencedor: <?php echo $item['vencedor']; ?><br>
                <a href="detalhe_item.php?id=<?php echo $item['id']; ?>">Ver Detalhes</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="itens_abertos.php">Voltar para Itens Abertos</a>
    <a href="sair.php">Encerrar Sessão</a>
</body>
</html>

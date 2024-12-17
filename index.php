<?php
session_start();
$servidor = 'localhost';
$banco = 'leilao';  
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM itens WHERE estado = 'aberto'";
    $stmt = $pdo->query($sql);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
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
                <img src="<?php echo $item['imagem']; ?>" alt="<?php echo $item['nome']; ?>" width="100">
                <p><?php echo $item['nome']; ?> - Mínimo: R$ <?php echo $item['minimo']; ?></p>
                <a href="detalhe_item.php?id=<?php echo $item['id']; ?>">Ver detalhes</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

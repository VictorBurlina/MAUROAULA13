<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
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
    exit();
}

$mensagem = '';

// Buscar todos os itens vencidos pelo usuário
$stmt = $pdo->prepare("SELECT * FROM itens WHERE estado = 'encerrado' AND vencedor = :usuario_id");
$stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
$stmt->execute();
$itens_vencidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itens Vencidos</title>
</head>
<body>
    <h1>Itens Vencidos</h1>

    <?php if (!empty($mensagem)): ?>
        <p style="color: red;"><?php echo $mensagem; ?></p>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Vencedor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens_vencidos as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                    <td>Usuário ID: <?php echo htmlspecialchars($item['vencedor']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="itens_abertos.php">Voltar para Itens Abertos</a>
    <a href="login.php">Sair</a>
</body>
</html>
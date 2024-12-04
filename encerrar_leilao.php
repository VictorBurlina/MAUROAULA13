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

// Buscar todos os itens com estado 'aberto' e que o usuário é o dono
$stmt = $pdo->prepare("SELECT * FROM itens WHERE estado = 'aberto' AND usuario_id = :usuario_id");
$stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
$stmt->execute();
$itens_abertos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Encerrar leilão
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_item = $_POST['id_item'];

    $stmt = $pdo->prepare("SELECT * FROM lances WHERE id_item = :id_item ORDER BY valor DESC LIMIT 1");
    $stmt->bindParam(':id_item', $id_item, PDO::PARAM_INT);
    $stmt->execute();
    $maior_lance = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($maior_lance) {
        
        $stmt = $pdo->prepare("UPDATE itens SET vencedor = :vencedor_id, estado = 'encerrado' WHERE id = :id_item");
        $stmt->execute([
            ':vencedor_id' => $maior_lance['id_usuario'],
            ':id_item' => $id_item
        ]);

        $mensagem = "Leilão encerrado. O vencedor foi o usuário com ID " . $maior_lance['id_usuario'];
    } else {
        $mensagem = "Nenhum lance foi feito.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encerrar Leilão</title>
</head>
<body>
    <h1>Encerrar Leilão</h1>

    <?php if (!empty($mensagem)): ?>
        <p style="color: red;"><?php echo $mensagem; ?></p>
    <?php endif; ?>

    <h2>Itens Abertos para Encerrar</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Lance Mínimo</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens_abertos as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                    <td>R$ <?php echo number_format($item['minimo'], 2, ',', '.'); ?></td>
                    <td>
                        <form action="encerrar_leilao.php" method="POST">
                            <input type="hidden" name="id_item" value="<?php echo $item['id']; ?>">
                            <button type="submit">Encerrar Leilão</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="itens_vencidos.php">Ver Itens Vencidos</a>
    <br>
    <a href="login.php">Sair</a>
</body>
</html>
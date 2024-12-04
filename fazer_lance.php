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

if (isset($_GET['id'])) {
    $id_item = $_GET['id'];
    
    // Buscar detalhes do item
    $stmt = $pdo->prepare("SELECT * FROM itens WHERE id = :id_item AND estado = 'aberto'");
    $stmt->bindParam(':id_item', $id_item, PDO::PARAM_INT);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        $mensagem = "Item não encontrado ou o leilão já foi encerrado.";
    }
} else {
    $mensagem = "ID do item não fornecido.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['valor_lance'])) {
        $valor_lance = $_POST['valor_lance'];

        if ($valor_lance <= $item['minimo']) {
            $mensagem = "O valor do lance deve ser maior que o lance mínimo.";
        } else {
            // Registrar o lance na tabela 'lances'
            try {
                $id_usuario = $_SESSION['usuario_id'];

                $stmt = $pdo->prepare("INSERT INTO lances (id_item, id_usuario, valor) VALUES (:id_item, :id_usuario, :valor_lance)");
                $stmt->execute([
                    ':id_item' => $id_item,
                    ':id_usuario' => $id_usuario,
                    ':valor_lance' => $valor_lance
                ]);

                // Atualizar o item com o novo lance (se necessário)
                $stmt = $pdo->prepare("UPDATE itens SET vencedor = :id_usuario WHERE id = :id_item");
                $stmt->execute([
                    ':id_usuario' => $id_usuario,
                    ':id_item' => $id_item
                ]);

                $mensagem = "Lance registrado com sucesso!";
            } catch (PDOException $e) {
                $mensagem = "Erro ao registrar o lance: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fazer Lance</title>
</head>
<body>
    <h1>Fazer Lance no Item</h1>

    <?php if (!empty($mensagem)): ?>
        <p style="color: red;"><?php echo $mensagem; ?></p>
    <?php endif; ?>

    <?php if (isset($item)): ?>
        <h2><?php echo htmlspecialchars($item['nome']); ?></h2>
        <img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="Imagem do Item" style="max-width: 200px;">
        <p>Lance Mínimo: R$ <?php echo number_format($item['minimo'], 2, ',', '.'); ?></p>

        <form action="fazer_lance.php?id=<?php echo $item['id']; ?>" method="POST">
            <label for="valor_lance">Valor do Lance:</label>
            <input type="number" name="valor_lance" step="0.01" required><br><br>
            <button type="submit">Fazer Lance</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="itens_abertos.php">Voltar para Itens Abertos</a>
    <a href="login.php">Sair</a>
</body>
</html>

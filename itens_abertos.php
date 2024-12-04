<?php
session_start();

// Verificar se o usuário está logado
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

// Verificar se foi enviado o formulário de cadastro de item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_item = $_POST['nome_item'];
    $lance_minimo = $_POST['lance_minimo'];
    $imagem = $_FILES['imagem']['name'];
    $usuario_id = $_SESSION['usuario_id'];

    // Movendo a imagem para a pasta do servidor
    $pasta_imagens = 'imagens/';
    $caminho_imagem = $pasta_imagens . basename($imagem);

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_imagem)) {
        try {
            // Inserir o item no banco de dados
            $stmt = $pdo->prepare("INSERT INTO itens (nome, imagem, minimo, estado, usuario_id) 
                                   VALUES (:nome, :imagem, :minimo, 'aberto', :usuario_id)");
            $stmt->bindParam(':nome', $nome_item);
            $stmt->bindParam(':imagem', $imagem);
            $stmt->bindParam(':minimo', $lance_minimo);
            $stmt->bindParam(':usuario_id', $usuario_id);

            if ($stmt->execute()) {
                $mensagem = "Item cadastrado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar o item.";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar o item: " . $e->getMessage();
        }
    } else {
        $mensagem = "Erro ao fazer upload da imagem.";
    }
}

// Consultar os itens do usuário logado
$stmt = $pdo->prepare("SELECT * FROM itens WHERE usuario_id = :usuario_id");
$stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
$stmt->execute();
$itens_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Item</title>
</head>
<body>
    <h1>Cadastrar Item</h1>

    <?php if (!empty($mensagem)): ?>
        <p style="color: red;"><?php echo $mensagem; ?></p>
    <?php endif; ?>

    <form action="cadastro_item.php" method="POST" enctype="multipart/form-data">
        <label for="nome_item">Nome do Item:</label>
        <input type="text" name="nome_item" id="nome_item" required><br><br>

        <label for="lance_minimo">Lance Mínimo:</label>
        <input type="text" name="lance_minimo" id="lance_minimo" required><br><br>

        <label for="imagem">Imagem:</label>
        <input type="file" name="imagem" id="imagem" required><br><br>

        <button type="submit">Cadastrar Item</button>
    </form>

    <h2>Meus Itens</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Imagem</th>
                <th>Lance Mínimo</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens_usuario as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                    <td>
                        <?php
                        $caminho_imagem = "imagens/" . $item['imagem'];
                        if (file_exists($caminho_imagem)): ?>
                            <img src="<?php echo $caminho_imagem; ?>" alt="Imagem do Item" style="max-width: 100px; height: auto;">
                        <?php else: ?>
                            <p>Imagem não encontrada</p>
                        <?php endif; ?>
                    </td>
                    <td>R$ <?php echo number_format($item['minimo'], 2, ',', '.'); ?></td>
                    <td><?php echo ucfirst($item['estado']); ?></td>
                    <td>
                        <?php if ($item['estado'] == 'aberto'): ?>
                            <a href="encerrar_leilao.php?id=<?php echo $item['id']; ?>">Encerrar Leilão</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="itens_abertos.php">Ver Itens Abertos</a>
    <br>
    <a href="logout.php">Sair</a>
</body>
</html>

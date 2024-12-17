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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $minimo = $_POST['minimo'];
    $imagem = $_FILES['imagem'];

    if (!empty($nome) && !empty($minimo) && !empty($imagem)) {
        $diretorio = 'imagens/';
        $arquivo = $diretorio . basename($imagem['name']);
        move_uploaded_file($imagem['tmp_name'], $arquivo);

        $stmt = $pdo->prepare("INSERT INTO itens (nome, imagem, minimo, estado, usuario_id) VALUES (:nome, :imagem, :minimo, 'aberto', :user_id)");
        $stmt->execute([':nome' => $nome, ':imagem' => $arquivo, ':minimo' => $minimo, ':user_id' => $_SESSION['user_id']]);

        header("Location: meus_itens.php");
        exit;
    } else {
        $erro = "Preencha todos os campos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Item</title>
</head>
<body>
    <h1>Cadastrar Item</h1>
    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <label for="nome">Nome do Item:</label>
        <input type="text" id="nome" name="nome" required><br><br>
        <label for="imagem">Imagem:</label>
        <input type="file" id="imagem" name="imagem" required><br><br>
        <label for="minimo">Lance Mínimo:</label>
        <input type="number" id="minimo" name="minimo" required><br><br>
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>

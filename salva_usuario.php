<?php
session_start();

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
    $nomeUsuario = $_POST['nomeUsuario'];
    $senha = hash('sha256', $_POST['senhaUsuario']);

    if (!empty($nomeUsuario) && !empty($senha)) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nomeUsuario, senhaUsuario) VALUES (:nome, :senha)");
        $stmt->execute([':nome' => $nomeUsuario, ':senha' => $senha]);
        header("Location: login.php?cadastrado=true");
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
    <title>Cadastro de Usuário</title>
</head>
<body>
    <h1>Cadastro de Usuário</h1>
    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="nomeUsuario">Nome:</label>
        <input type="text" id="nomeUsuario" name="nomeUsuario" required><br><br>
        <label for="senhaUsuario">Senha:</label>
        <input type="password" id="senhaUsuario" name="senhaUsuario" required><br><br>
        <button type="submit">Cadastrar</button>
    </form>
    <p>Já tem uma conta? <a href="login.php">Login aqui</a></p>
</body>
</html>

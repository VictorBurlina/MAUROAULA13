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

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nomeUsuario = :nome AND senhaUsuario = :senha");
    $stmt->execute([':nome' => $nomeUsuario, ':senha' => $senha]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $_SESSION['user_id'] = $usuario['id'];
        header("Location: cadastrar_item.php");
        exit;
    } else {
        $erro = "Nome de usuário ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="nomeUsuario">Nome:</label>
        <input type="text" id="nomeUsuario" name="nomeUsuario" required><br><br>
        <label for="senhaUsuario">Senha:</label>
        <input type="password" id="senhaUsuario" name="senhaUsuario" required><br><br>
        <button type="submit">Entrar</button>
    </form>
    <p>Não tem uma conta? <a href="salva_usuario.php">Cadastre-se aqui</a></p>
</body>
</html>

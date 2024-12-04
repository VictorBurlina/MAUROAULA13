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

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeUsuario = $_POST['nomeUsuario'];
    $senhaUsuario = hash('sha256', $_POST['senhaUsuario']);  // Criptografa a senha

    // Verifica se o usuário existe no banco de dados
    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nomeUsuario = :nomeUsuario AND senhaUsuario = :senhaUsuario");
        $stmt->execute([
            ':nomeUsuario' => $nomeUsuario,
            ':senhaUsuario' => $senhaUsuario
        ]);

        $usuario = $stmt->fetch();

        if ($usuario) {
            // Se as credenciais estiverem corretas, cria a sessão e redireciona para a página principal
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nomeUsuario'] = $usuario['nomeUsuario'];
            header("Location: cadastro_item.php"); // Redireciona para o cadastro de itens
            exit;
        } else {
            $mensagem = "Usuário ou senha inválidos.";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php if (!empty($mensagem)): ?>
        <p style="color: red;"><?php echo $mensagem; ?></p>
    <?php endif; ?>

    <h1>Login</h1>
    <form action="login.php" method="POST">
        <label>Nome de Usuário:</label>
        <input type="text" name="nomeUsuario" required><br><br>

        <label>Senha:</label>
        <input type="password" name="senhaUsuario" required><br><br>

        <button type="submit">Entrar</button>
    </form>

    <p>Não tem uma conta? <a href="salva_usuario.php">Cadastre-se aqui</a></p>
</body>
</html>

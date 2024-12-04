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
    $senhaUsuario = hash('sha256', $_POST['senhaUsuario']);  // Criptografando a senha

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nomeUsuario, senhaUsuario) VALUES (:nomeUsuario, :senhaUsuario)");
        $resultado = $stmt->execute([
            ':nomeUsuario' => $nomeUsuario,
            ':senhaUsuario' => $senhaUsuario
        ]);

        if ($resultado) {
            $mensagem = "Usuário cadastrado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar o usuário.";
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
    <title>Cadastrar Usuário</title>
</head>
<body>
    <?php if (!empty($mensagem)): ?>
        <p style="color: red;"><?php echo $mensagem; ?></p>
    <?php endif; ?>
    
    <form action="salva_usuario.php" method="POST">
        <label>Nome de Usuário:</label>
        <input type="text" name="nomeUsuario" required><br><br>

        <label>Senha:</label>
        <input type="password" name="senhaUsuario" required><br><br>

        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>

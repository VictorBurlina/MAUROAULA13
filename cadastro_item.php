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
    // Verificar se os campos do formulário estão setados
    if (isset($_POST['nome_item']) && isset($_POST['lance_minimo'])) {
        $nome_item = $_POST['nome_item'];
        $lance_minimo = $_POST['lance_minimo'];

        // Processamento do upload da imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            // Definir o diretório de upload (certifique-se que o diretório 'imagens' existe)
            $uploadDir = 'imagens/';

            // Verificar se o diretório 'imagens' existe, se não, cria-lo
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Cria o diretório se não existir
            }

            $imagem = $_FILES['imagem']['name'];
            $imagem_tmp = $_FILES['imagem']['tmp_name'];

            // Substituir espaços e caracteres especiais no nome do arquivo
            $imagem = preg_replace('/[^a-zA-Z0-9._-]/', '_', $imagem);

            // Caminho relativo para o diretório 'imagens' dentro do diretório htdocs
            $caminho_imagem = $uploadDir . basename($imagem);

            // Tentar mover o arquivo para o diretório de destino
            if (move_uploaded_file($imagem_tmp, $caminho_imagem)) {
                echo "Imagem carregada com sucesso!";
            } else {
                echo "Erro ao fazer upload da imagem.";
            }
        } else {
            echo "Nenhuma imagem foi selecionada ou ocorreu um erro no upload da imagem.";
        }

        // Inserir o item no banco de dados
        try {
            $stmt = $pdo->prepare("INSERT INTO itens (nome, imagem, minimo, estado) VALUES (:nome, :imagem, :minimo, :estado)");
            $resultado = $stmt->execute([
                ':nome' => $nome_item,
                ':imagem' => $caminho_imagem,  // Armazenando o caminho relativo
                ':minimo' => $lance_minimo,
                ':estado' => 'aberto'  // O estado inicial do item é "aberto"
            ]);

            if ($resultado) {
                $mensagem = "Item cadastrado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar item.";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Item</title>
</head>
<body>
    <h1>Cadastrar Item para Leilão</h1>
    
    <?php if (!empty($mensagem)): ?>
        <p style="color: green;"><?php echo $mensagem; ?></p>
    <?php endif; ?>
    
    <form action="cadastro_item.php" method="POST" enctype="multipart/form-data">
        <label for="nome_item">Nome do Item:</label>
        <input type="text" name="nome_item" required><br><br>

        <label for="imagem">Imagem do Item:</label>
        <input type="file" name="imagem" accept="image/*" required><br><br>

        <label for="lance_minimo">Lance Mínimo:</label>
        <input type="number" name="lance_minimo" step="0.01" required><br><br>

        <button type="submit">Cadastrar Item</button>
    </form>

    <br>
    <a href="itens_abertos.php">Itens Abertos</a>
    <a href="login.php">Sair</a>
</body>
</html>

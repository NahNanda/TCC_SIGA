<?php
// ============================================================
// ARQUIVO: cadastrar_produto.php
// Função: Formulário + processamento para cadastrar produtos
// ============================================================

require_once 'conexao.php';

$mensagem = '';
$tipo_msg = '';

// ── Processamento do formulário (quando o aluno clicar em Cadastrar) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Receber e limpar os dados do formulário
    $nome      = trim($_POST['nome'] ?? '');
    $preco     = $_POST['preco'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');

    // 2. Validação básica
    if (empty($nome) || empty($preco)) {
        $mensagem = 'Preencha pelo menos o nome e o preço do produto.';
        $tipo_msg = 'erro';
    } else {

        // 3. Processar o upload da imagem
        $nome_arquivo = null; // começa sem imagem

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {

            $arquivo_tmp   = $_FILES['imagem']['tmp_name'];
            $arquivo_nome  = basename($_FILES['imagem']['name']);
            $extensao      = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION));

            // Tipos de imagem permitidos
            $tipos_permitidos = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!in_array($extensao, $tipos_permitidos)) {
                $mensagem = 'Formato de imagem não permitido. Use JPG, PNG ou WEBP.';
                $tipo_msg = 'erro';
            } else {
                // Gera um nome único para evitar conflito de arquivos
                $nome_arquivo = uniqid('prod_', true) . '.' . $extensao;
                $destino      = 'uploads/' . $nome_arquivo;

                if (!move_uploaded_file($arquivo_tmp, $destino)) {
                    $mensagem = 'Erro ao salvar a imagem. Verifique as permissões da pasta /uploads.';
                    $tipo_msg = 'erro';
                    $nome_arquivo = null;
                }
            }
        }

        // 4. Salvar no banco de dados (se não houve erro de imagem)
        if ($tipo_msg !== 'erro') {

            // Usar Prepared Statement para evitar SQL Injection
            $stmt = $conexao->prepare(
                'INSERT INTO produtos (nome, preco, descricao, imagem) VALUES (?, ?, ?, ?)'
            );
            $stmt->bind_param('sdss', $nome, $preco, $descricao, $nome_arquivo);

            if ($stmt->execute()) {
                $mensagem = 'Produto "' . htmlspecialchars($nome) . '" cadastrado com sucesso!';
                $tipo_msg = 'sucesso';
            } else {
                $mensagem = 'Erro ao salvar no banco: ' . $conexao->error;
                $tipo_msg = 'erro';
            }
            $stmt->close();
        }
    }
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <link rel="stylesheet" href="assets/estilo.css">
</head>
<body>

<header class="cabecalho">
    <h1>🛍️ Loja Online</h1>
    <nav>
        <a href="index.php">Ver Catálogo</a>
        <a href="cadastrar_produto.php" class="ativo">Cadastrar Produto</a>
    </nav>
</header>

<main class="container">
    <h2>Cadastrar Novo Produto</h2>

    <?php if (!empty($mensagem)): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <form class="formulario" action="cadastrar_produto.php" method="POST" enctype="multipart/form-data">

        <div class="campo">
            <label for="nome">Nome do Produto *</label>
            <input type="text" id="nome" name="nome" required
                   placeholder="Ex: Camiseta Esportiva Nike">
        </div>

        <div class="campo">
            <label for="preco">Preço (R$) *</label>
            <input type="number" id="preco" name="preco" step="0.01" min="0" required
                   placeholder="Ex: 129.90">
        </div>

        <div class="campo">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="4"
                      placeholder="Descreva o produto, tamanho, material..."></textarea>
        </div>

        <div class="campo">
            <label for="imagem">Imagem do Produto</label>
            <input type="file" id="imagem" name="imagem" accept="image/*">
            <small>Formatos aceitos: JPG, PNG, WEBP (opcional)</small>
        </div>

        <button type="submit" class="btn-cadastrar">Cadastrar Produto</button>

    </form>
</main>

</body>
</html>
<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser;

// Verifica se o arquivo foi enviado
if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    die("Erro: nenhum arquivo enviado ou erro no upload.<br><a href='index.html'>Voltar</a>");
}

$arquivo = $_FILES['arquivo'];
$extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
$caminhoTemp = $arquivo['tmp_name'];
$conteudo = "";

try {
    switch ($extensao) {
        
        // ========== PDF ==========
        case 'pdf':
            $parser = new Parser();
            $pdf = $parser->parseFile($caminhoTemp);
            $conteudo = $pdf->getText();
            break;

        // ========== EXCEL ==========
        case 'xlsx':
        case 'xls':
            $spreadsheet = IOFactory::load($caminhoTemp);
            $sheet = $spreadsheet->getActiveSheet();
            $linhas = $sheet->toArray();
            
            foreach ($linhas as $linha) {
                $linhaLimpa = array_filter($linha, function($valor) {
                    return $valor !== null && $valor !== '';
                });
                if (!empty($linhaLimpa)) {
                    $conteudo .= implode(" | ", $linhaLimpa) . "\n";
                }
            }
            break;

        // ========== FORMATO INVÁLIDO ==========
        default:
            die("Erro: formato não suportado. Use PDF, XLSX ou XLS.<br><a href='index.html'>Voltar</a>");
    }

    // Mostra o resultado
    echo "<h2>Conteúdo extraído de: " . htmlspecialchars($arquivo['name']) . "</h2>";
    echo "<hr>";
    echo "<pre>" . htmlspecialchars($conteudo) . "</pre>";
    echo "<hr>";
    echo "<a href='index.html'>Extrair outro arquivo</a>";

} catch (Exception $e) {
    echo "Erro ao processar: " . htmlspecialchars($e->getMessage());
    echo "<br><a href='index.html'>Voltar</a>";
}
?>
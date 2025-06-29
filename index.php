<?php
$pdfDir = 'pdfs/';
$outputDir = 'img/';
$ghostscript = '"C:\\Program Files\\gs\\gs10.05.1\\bin\\gswin64c.exe"';
$pngquant = 'pngquant.exe --force --ext .png  --quality=70 ';
$pdfFiles = glob($pdfDir . "*.pdf");
if (empty($pdfFiles)) {
    echo "❌ Nenhum arquivo PDF encontrado na pasta.<br>";
    exit;
}
foreach ($pdfFiles as $pdfFile) {
    $pdfFileName = pathinfo($pdfFile, PATHINFO_FILENAME);
    $inputPath = '"' . $pdfFile . '"';
    $outputPath = '"' . $outputDir . $pdfFileName . '_%d.png"';
    $cmd = "$ghostscript -dNOSAFER -dBATCH -dNOPAUSE -sDEVICE=png16m -r180 -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dUseCIEColor -sOutputFile=$outputPath $inputPath";
    echo "🔄 Processando: $pdfFileName.pdf<br>";
    exec($cmd . ' 2>&1', $saida, $codigoRetorno);
    echo "✅ Código de Retorno: $codigoRetorno<br>";
    $generatedFiles = glob($outputDir . $pdfFileName . '_*.png');
    foreach ($generatedFiles as $imageFile) {
        $cmdCompress = "$pngquant " . escapeshellarg($imageFile);
        exec($cmdCompress . ' 2>&1', $saidaCompress, $retCompress);
        if ($retCompress === 0) {
            echo "🟢 Otimizado: " . basename($imageFile) . "<br>";
        } else {
            echo "⚠️ Falha ao otimizar: " . basename($imageFile) . "<br>";
        }
    }
    // Renomeia a primeira página para [nome].png
    $firstPage = $outputDir . $pdfFileName . '_1.png';
    $targetName = $outputDir . $pdfFileName . '.png';
    if (file_exists($firstPage)) {
        rename($firstPage, $targetName);
        echo "📄 Página principal renomeada para: " . basename($targetName) . "<br><br>";
    }
}
?>

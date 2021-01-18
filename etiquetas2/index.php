<?php
        
header("Content-type: text/html; charset=utf-8");
require_once("fpdf/fpdf.php");
include("conecta.php"); 

$query = "select * from pessoa";
mysqli_set_charset($conn,"utf8");
$busca = mysqli_query($conn, $query);
// "SELECT 'TITULO','NOME','ENDERECO_1','BAIRRO_1',
//'CIDADE_1','ESTADO_1','CEP_1' FROM 'pessoa'"
mysqli_close($conn);

// Variaveis de Tamanho

$mesq = "4"; // Margem Esquerda (mm)
$mdir = "4"; // Margem Direita (mm)
$msup = "25.0"; // Margem Superior (mm)
$leti = "107.3"; // Largura da Etiqueta (mm)
$aeti = "33.9"; // Altura da Etiqueta (mm)
$ehet = "3.59"; // Espaço horizontal entre as Etiquetas (mm)
$pdf=new tFPDF('P','mm','Letter', ); // Cria um arquivo novo tipo carta, na vertical.


$pdf->AddPage(); // adiciona a primeira pagina
$pdf->SetMargins('5','12,7', '5'); // Define as margens do documento
$pdf->SetAuthor(""); // Define o autor
$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
$pdf->SetFont('DejaVu','', 8); // Define a fonte
$pdf->SetDisplayMode('fullpage');//Adicinei uma fullpage




$coluna = 0;
$linha = 0;
//MONTA A ARRAY PARA ETIQUETAS ,`ENDERECO_1`,`BAIRRO_1`,`CIDADE_1`,`ESTADO_1`,`CEP_1`
while($dados = mysqli_fetch_array($busca)) {
$nome = $dados["nome"];
$info = $dados["infoLegalOne"];
$cid = $dados["cidade"];
$esta = $dados["estado"];
$cep = $dados["cep"];
$local = $info . " – " . $cid . " – " . $esta;





if($linha == "10") {
$pdf->AddPage();

$linha = 0;
}

if($coluna == "2") { // Se for a segunda coluna
$coluna = 0; // $coluna volta para o valor inicial
$linha = $linha + 1; // $linha é igual ela mesma +1
}

if($linha == "7") { // Se for a última linha da página
$pdf->AddPage(); // Adiciona uma nova página
$linha = 0; // $linha volta ao seu valor inicial
}

$posicaoV = $linha*$aeti;
$posicaoH = $coluna*$leti;

if($coluna == "0") { // Se a coluna for 0
$somaH = $mesq; // Soma Horizontal é apenas a margem da esquerda inicial
} else { // Senão
$somaH = $mesq+$posicaoH; // Soma Horizontal é a margem inicial mais a posiçãoH
}

if($linha == "0") { // Se a linha for 0
$somaV = $msup; // Soma Vertical é apenas a margem superior inicial
} else { // Senão
$somaV = $msup+$posicaoV; // Soma Vertical é a margem superior inicial mais a posiçãoV
}

$pdf->Text($somaH,$somaV,$nome); // Imprime o nome da pessoa de acordo com as coordenadas
$pdf->Text($somaH,$somaV+5,$local); // Imprime a localidade da pessoa de acordo com as coordenadas

// Imprime o cep da pessoa de acordo com as coordenadas

$coluna = $coluna+1;

}

$pdf->Output("etiquetas.pdf","I");





?>
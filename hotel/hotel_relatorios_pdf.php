<?php

$conteudo_html = ob_get_clean();
include('matriz.php');

function subDayIntoDate($date, $days) {
    $thisday = substr($date, 0, 2); 
    $thismonth = substr($date, 3, 8);
    $thisyear = substr($date, 7, 10);
    $nextdate = mktime(0, 0, 0, $thismonth, $thisday - $days, $thisyear);
    return strftime("%d/%m/%Y", $nextdate);
}

$xqualsegmento = $_POST['xqualsegmento'];
$xqualclifor = $_POST['xqualclifor'];
$xqualusuario = $_POST['xqualusuario'];
$xqualunidadenegocio = $_POST['xqualunidadenegocio'];
$xnome = $_POST['nome'];
$xnomeusuario = $_POST['xnomeusuario'];
$xnomeunidadenegocio = $_POST['xnomeunidadenegocio'];
$xqualdocfinanceiro = $_POST['xqualdocfinanceiro'];

$xcidade = $_POST[cidade];


if (empty($xqualsegmento)) {
    $xqualsegmento = $_GET['xqualsegmento'];
}
if (empty($xqualclifor)) {
    $xqualclifor = $_GET['xqualclifor'];
}
if (empty($xqualusuario)) {
    $xqualusuario = $_GET['xqualusuario'];
}
if (empty($xqualdocfinanceiro)) {
    $xqualdocfinanceiro = $_GET['xqualdocfinanceiro'];
}
$filtro_link = "&xqualsegmento=$xqualsegmento&xqualclifor=$xqualclifor&xqualusuario=$xqualusuario&xqualdocfinanceiro=$xqualdocfinanceiro";



$filtro_opcoes_inputmenus_form_name = 'flancamento';



// qual ordem de index caso houver
$filtro_ordenar_por = "";
$id_order = $_POST[ordenarpor];
if (!(empty($id_order))) {
    $filtro_ordenar_por = " order by $id_order ";
}



$marqem_esquerda = 0;
// PROCURA DEFINICOES DE TITULOS PARA CONTAS EM SEGMENTOS
$seltitulo = mysql_query("SELECT * FROM $TSEGMENTOS WHERE cnpjcpf = '$info_cnpj_segmento'", $CONTSEGMENTOS);
if (mysql_num_rows($seltitulo)) {
    $infotitulo = mysql_fetch_assoc($seltitulo);
} else {
    BASICO::atencao('Empresa  n�o Cadastrada!');
    exit;
    $HIDE_FORM = true;
}


//LOGO QUE SER? COLOCADO NO RELAT?RIO
$imagem = "../logomarca/$logomarca_licenciada";
$imagem = "$logomarca_licenciada_thumb";
//ENDERE?O DA BIBLIOTECA FPDF
$end_fpdf = ""; //"c:/pagina/biblioteca/fpdf";
//NUMERO DE RESULTADOS POR P?GINA
$por_pagina = 40;    //defaul
//F-> SALVA NO ENDERE?O ESPECIFICADO NA VAR END_FINAL
$tipo_pdf = "I";
// landscape Ou protable
$land_port = 'P';



$id_pagini = $_POST[id_pagini];
$paginainicial = $id_pagini;
if (strlen($paginainicial) == 0) {
    $paginainicial = 1;
} else {
    $paginainicial = $paginainicial - 1;
}


$pagint1 = $_POST[pagint1];
if (strlen($pagint1) == 0) {
    $pagint1 = 1;
}
$pagint2 = $_POST[pagint2];
if (strlen($pagint2) == 0) {
    $pagint2 = 999999;
}

$id_grupoproduto = $_SESSION['RELAT']['grupoproduto'];
$id_periodo1 = $_POST['lanperiodo1'];
$id_periodo2 = $_POST['lanperiodo2'];

$id_periodo1 = _myfunc_dtos_0hs($id_periodo1);
$id_periodo2 = _myfunc_dtos($id_periodo2);


$mostraperiodo = _myfuncoes_texto_periodo();
$id_pdf_php = $_POST['id_qual_relatorio'];
if ($_GET['id_qual_relatorio']) {
    $id_pdf_php = $_GET['id_qual_relatorio'];
}



//ENDERE?O ONDE SER? GERADO O PDF
$end_final = $id_pdf_php . ".pdf";
$titulo = $nomerel;
$pdf = new FPDF();
$pdf->SetTitle($sistema_nome . ' Ver. ' . $sistema_versao);
$pdf->SetAuthor('W.S.S');
$pdf->SetSubject($_SERVER["REQUEST_URI"]);
$datahora = _myfuncoes_horamysql();
$linha = 0;
$linha_impressa = 0;
$pagina = 1;
if ($paginainicial > 1) {
    $pagina = $paginainicial;
}
$filtro_relatorio = $_SESSION['ADMIN']['filtro_relatorio'];

//exit(var_dump($_SESSION['ADMIN']['nome_user']));
//CALCULA QUANTAS P?GINAS V?O SER NECESS?RIAS
// qual id_pdf_php

switch ($id_pdf_php) {

    case 'hotel_rel_fechamento':
        $id_hotel_local = filter_input(INPUT_GET, 'id_hotel_local');
        $dono = filter_input(INPUT_GET, 'idb');

        $imagem = _myfuncoes_logomarca();
        $pdf->SetAuthor('Online Sistemas - Dasio Lacerda');
        $pdf->AddPage();
        //$pdf->Image("$imagem"); //Logomarca EMBRATUR
        $pdf->Image("$imagem", 10, 5, 25, 20, 'JPG'); //Logomarca EMBRATUR
        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(40, 14, $infotitulo['fantasia']);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(40, 21, $infotitulo['fantasia']);
        
        $sql_lancamentos = "SELECT * FROM $TLANCAMENTOS_TMP WHERE dono = '$dono'";
        $get_lancamentos = mysqli_query($conn_a, $sql_lancamentos);
        $info_lancamentos = mysqli_fetch_assoc($get_lancamentos);
        $id_cnpjcpf = $info_lancamentos['cnpjcpf'];

        $dados_cliente = _myfun_dados_cnpjcpf($id_cnpjcpf);

        $existe_id_hotel_local = _myfunc_existe_id_hotel_local($id_hotel_local);

        $pdf->Text(180, 21, 'Data: '.date('d/m/Y'));
        
        $pdf->Text(10, 31, utf8_decode('Hóspede: '.$dados_cliente['razao']));
        $pdf->Text(100, 31, utf8_decode('Endereço: '.$dados_cliente['endereco'].', '.$dados_cliente['num'] ));
        $pdf->Text(10, 35, 'Cidade: '.$dados_cliente['cidade']);
        $lini = 5;
        $pdf->Rect(10, 35 + $lini, 190, 8);
        $pdf->Text(11, 38 + $lini, 'Apartamento: '.$id_hotel_local);
        
        $sql_nfdocs = "SELECT * FROM $TNFDOCUMENTOS_TMP WHERE dono = '$dono' ORDER BY datacad DESC LIMIT 1";
        $get_nfdocs = mysqli_query($conn_a, $sql_nfdocs);
        $nfdocs = mysqli_fetch_array($get_nfdocs);
        
        $pdf->Text(50, 38 + $lini, 'Entrada: '.date('d/m/Y H:i:s', $nfdocs['datacad']));
        $pdf->Text(120, 38 + $lini, 'Check-Out: PD00037914 CTRL: 613161588-60-');
        $pdf->Text(50, 41 + $lini, utf8_decode('Previsão: '.date('d/m/Y', $nfdocs['datasaida'])));
        $pdf->Text(120, 41 + $lini, utf8_decode('Saída: '.date('d/m/Y'). '  Hora: '.date('H:i:s') .'AG: AD' ));
        
        $pdf->Rect(10, 44 + $lini, 190, 5);
        $pdf->Text(11, 47 + $lini, 'Qtde');
        $pdf->Text(25, 47 + $lini, 'Local');
        $pdf->Text(40, 47 + $lini, utf8_decode('Descrição'));
        $pdf->Text(125, 47 + $lini, utf8_decode('Dt Lança'));
        $pdf->Text(145, 47 + $lini, 'Un');
        $pdf->Text(160, 47 + $lini, 'Vr Unit.');
        $pdf->Text(180, 47 + $lini, 'Total');
        
        $sql_item = "SELECT * FROM $TITEM_FLUXO_TMP WHERE dono = '$dono' ORDER BY datacad";
        $get_item = mysqli_query($conn_a, $sql_item);
        $qtdeItem = mysqli_num_rows($get_item);
        if ($qtdeItem) {
            $pdf->Rect(10, 50 + $lini, 190, (3.5*$qtdeItem));
            $totalHosp = $totalLav = $totalTel = $totalCom = $desconto = 0;
            $pdf->Ln(46);
            while ($item = mysqli_fetch_array($get_item)) {
                
                $desconto += $item['vdesc'];
                if (empty($item['localoperacao'])) {
                    $sql_produto = "SELECT grupoproduto FROM $TPRODUTOS WHERE conta = '".$item['cprod']."'";
                    //exit($sql_produto);
                    $get_produto = mysqli_query($conn_a, $sql_produto);
                    $qtProd = mysqli_num_rows($get_produto);
                    if ($qtProd) {
                        $res = mysqli_fetch_array($get_produto);
                    } else {
                        $sql_servico = "SELECT grupoproduto FROM $TSERVICOS WHERE conta = '".$item['cprod']."'";
                        //exit($sql_servico);
                        $get_servico = mysqli_query($conn_a, $sql_servico);
                        $res = mysqli_fetch_array($get_servico);
                    }
                    
                    switch ($res['grupoproduto']) {
                        case '090.001':
                            $totalHosp += $item['vprod'];
                            break;
                        case '091.001':
                            $totalLav += $item['vprod'];
                            break;
                        case '092.001':
                            $totalTel += $item['vprod'];
                            break;
                        default:
                            $totalCom += $item['vprod'];
                            break;
                    }
                } else {
                    switch ($item['localoperacao']) {
                        case '01': 
                            $local = 'APTO'; 
                            $totalHosp += $item['vprod'];
                            $diaria = $item['vuncom'];
                            break;
                        case '02': 
                            $local = 'FRI'; 
                            $totalCom += $item['vprod'];
                            break;
                        case '03': 
                            $local = 'FRR'; 
                            $totalCom += $item['vprod'];
                            break;
                        case '04': 
                            $local = 'LAV'; 
                            $totalLav += $item['vprod'];
                            break;
                        case '05': 
                            $local = 'RES'; 
                            $totalCom += $item['vprod'];
                            break;
                        case '06': 
                            $local = 'TEL'; 
                            $totalTel += $item['vprod'];
                            break;
                    }
                }
                
                $pdf->Cell(10, 3, number_format($item['qcom'], 3, ',', ''), 0, 0, 'R');
                
                $pdf->Text(25, 53 + $lini, $local);
                $pdf->Text(40, 53 + $lini, utf8_decode($item['xprod']));
                $pdf->Text(110, 53 + $lini, utf8_decode($item['item_complemento']));
                $pdf->Text(125, 53 + $lini, date('d/m/Y', $item['datacad']));
                $pdf->Text(145, 53 + $lini, $item['ucom']);
                $pdf->Cell(150, 3, number_format($item['vuncom'], 2, ',', ''), 0, 0, 'R');
                $pdf->Cell(20, 3, number_format($item['vprod'], 2, ',', ''), 0, 0, 'R');
                //$pdf->Text(175, 53 + $lini, $item['vprod']);
                
                $lini = $lini + 3;
                $pdf->Ln(3);
            }
        }
        $pdf->Ln(5);
        $pdf->Rect(10, 59 + $lini, 190, (15));
        $pdf->Text(11, 62 + $lini, utf8_decode('Hóspede Pagante:'));
        $pdf->Text(11, 65 + $lini, utf8_decode($dados_cliente['razao']));
        $pdf->Text(11, 72 + $lini, '_____________________________________________________');
        $totalGeral = ($totalHosp + $totalCom + $totalTel + $totalLav);
        
        $pdf->Text(91, 62 + $lini, utf8_decode('Diárias: '));
        $pdf->Ln(3);
        $pdf->Cell(110, 4, number_format($totalHosp, 2, ',', ''), 0, 0, 'R' );
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(20, 4, 'Sub Total: ', 0, 0, 'L');
        $pdf->Cell(10, 4, number_format($totalGeral, 2, ',', ''), 0, 0, 'R' );
        $pdf->Ln(3);
        $pdf->Text(91, 65 + $lini, 'Comandas: ');
        $pdf->Cell(110, 4, number_format($totalCom, 2, ',', ''), 0, 0, 'R' ); 
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(20, 4, 'Desconto: ', 0, 0, 'L');
        $pdf->Cell(10, 4, number_format($desconto, 2, ',', ''), 0, 0, 'R' );
        $pdf->Ln(3);
        $pdf->Text(91, 68 + $lini, utf8_decode('Ligações: '));
        $pdf->Cell(110, 4, number_format($totalTel, 2, ',', ''), 0, 0, 'R' ); 
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(20, 4, '* * * * * * * * * * * * * * * * *', 0, 0, 'L');
        $pdf->Ln(3);
        $pdf->Text(91, 72 + $lini, 'Lavander: ');
        $pdf->Cell(110, 4, number_format($totalLav, 2, ',', ''), 0, 0, 'R' ); 
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(20, 4, 'Total: ', 0, 0, 'L');
        $pdf->Cell(10, 4, number_format($totalGeral - $desconto, 2, ',', ''), 0, 0, 'R' );
        
        $pdf->Ln(15);
        $pdf->Text(10, 80 + $lini, utf8_decode('Obs: '.utf8_encode($nfdocs['notebem'])));
        $pdf->Text(80, 85 + $lini, utf8_decode('***    NAO E VALIDO COMO DOCUMENTO FISCAL   ***'));
        //var_dump($dados_cliente);
        //exit('teste dasio');
        break;


    case 'ficha_hospede_apto' :



        $id_hotel_local = $_GET[id_hotel_local];
        $existe_id_hotel_local = _myfunc_existe_id_hotel_local($id_hotel_local);


        if ($existe_id_hotel_local == '') {
            // ok, n?o cadastrado para outro
        } else {
            $flag_hotel = 'OCUPADO';
        }

        $dono = $_GET[idb];
        include('documentos_situacao_erp.php');
        if ($tabela_tmp_on == 'TMP') {
            $TLANCAMENTOS = $TLANCAMENTOS_TMP;
            $TITEM_FLUXO = $TITEM_FLUXO_TMP;
        }
        $xsql_l = "SELECT * FROM $TLANCAMENTOS WHERE (dono='$dono' )";
        $sel_lancados = mysqli_query($conn_a, "$xsql_l");
        if (mysqli_num_rows($sel_lancados)) {
            $info_lancamentos = mysqli_fetch_assoc($sel_lancados);
        }
        $id_cnpjcpf = $info_lancamentos[cnpjcpf];
        $dados_cliente = _myfun_dados_cnpjcpf($id_cnpjcpf);

        $dados_apartamentos = _myfun_dados_apartamentos_hospedagem($id_hotel_local);

        $land_port = 'P';
        $por_pagina = 40;
        $periodo1 = _myfunc_stod($id_periodo1);
        $periodo2 = _myfunc_stod($id_periodo2);

        $periodo_total = $periodo1 . ' a ' . $periodo2;
        $imagem = _myfuncoes_logomarca();
        // $imagem = "../logomarca/$logomarca_licenciada";
        //$imagem = "$logomarca_licenciada_thumb";
        $razao = $infotitulo[razaosocial];
        $cpnj = $infotitulo[cnpjcpf];
        $dadoscnpjcpf = _myfun_dados_cnpjcpf($cpnj);

        $pdf->AddPage();
        //$pdf->Image("$imagem"); //Logomarca EMBRATUR
        $pdf->Image("$imagem", 10, 5, 25, 20, 'JPG'); //Logomarca EMBRATUR

        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(40, 14, $infotitulo['fantasia']);

        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(40, 21, $infotitulo['endereco'] . ',' . $infotitulo['num']);
        $pdf->Text(40, 24, $infotitulo['cidade'] . '-' . $infotitulo['uf']);


        $pdf->SetFont('Arial', '', 14);
        $pdf->Text(90, 24, 'FICHA DE HOSPEDAGEM ');


        $ddtres1i = _myfunc_stod($dados_apartamentos[dtres1i]);
        $ddtres1f = _myfunc_stod($dados_apartamentos[dtres1f]);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Text(90, 19, "CHECK-IN: $ddtres1i");
        $pdf->Text(130, 19, "CHECK-OUT: $ddtres1f");


        if (trim($dados_cliente[razao]) == '') {
            $id_hotel_local = '';
        }

        $pdf->Rect(170, 11, 30, 10); // posicao x ,posicao y da altura em rela??o a pagina , tamanho do retangulo, altura do retangulo

        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(171, 14, 'APTO'); // primeiro numero e o espa?o entre a margem e o texto, segundo e a altura entre a linha do retangulo e o texto
        $pdf->SetFont('Arial', '', 14);
        $pdf->Text(185, 18, $id_hotel_local);

        $lini = 7;
        // linha 1
        $pdf->Rect(10, 23 + $lini, 138, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(11, 26 + $lini, 'NOME');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(18, 31 + $lini, $dados_cliente['razao']);

        $pdf->Rect(150, 23 + $lini, 50, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(151, 26 + $lini, 'CPF');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(161, 31 + $lini, $dados_cliente['cnpj']);


        //LINHA 2

        $pdf->Rect(10, 35 + $lini, 116, 10);  // +12
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(11, 38 + $lini, utf8_decode('ENDEREÇO'));
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(18, 43 + $lini, utf8_decode($dados_cliente['endereco']));

        $pdf->Rect(128, 35 + $lini, 20, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(129, 38 + $lini, 'NUM');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(136, 43 + $lini, $dados_cliente['num']);

        $pdf->Rect(150, 35 + $lini, 50, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(151, 38 + $lini, 'COMPL.');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(156, 43 + $lini, utf8_decode($dados_cliente['complemento']));

        //LINHA 3

        $pdf->Rect(10, 47 + $lini, 86, 10);  // +12
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(11, 50 + $lini, 'CIDADE');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Text(18, 55 + $lini, utf8_decode($dados_cliente['cidade']) . '-' . $dados_cliente['uf']);

        $pdf->Rect(98, 47 + $lini, 50, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(99, 50 + $lini, 'BAIRRO');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Text(106, 55 + $lini, utf8_decode($dados_cliente['bairro']));

        $pdf->Rect(150, 47 + $lini, 50, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(151, 50 + $lini, 'CEP');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Text(161, 55 + $lini, $dados_cliente['cep']);



        //LINHA 4

        $pdf->Rect(10, 58 + $lini, 86, 10);  // +12
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(11, 61 + $lini, 'TELEFONE');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(18, 66 + $lini, $dados_cliente['tel']);

        $pdf->Rect(98, 58 + $lini, 102, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(99, 61 + $lini, 'EMAIL');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(106, 66 + $lini, $dados_cliente[email]);


        //LINHA 5

        $pdf->Rect(10, 69 + $lini, 86, 10);  // +12
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(11, 72 + $lini, utf8_decode('VEÍCULO'));
        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(18, 77 + $lini, '');

        $pdf->Rect(98, 69 + $lini, 50, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(99, 72 + $lini, 'MARCA');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(106, 77 + $lini, '');

        $pdf->Rect(150, 69 + $lini, 50, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(151, 72 + $lini, 'PLACA');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(161, 77 + $lini, ' ');


        //LINHA 5

        $pdf->Rect(10, 81 + $lini, 190, 67);  // +12
        $pdf->SetFont('Arial', '', 8);
        $pdf->Text(11, 84 + $lini, utf8_decode("RELAÇÃO DE HOSPEDES DO APTO  $id_hotel_local [MAX 4 pessoas] "));

        // relacao de hospedes 
        $sel_hospedes = mysqli_query($conn_a, "SELECT * FROM $TAPARTAMENTOS_HOSPEDES WHERE (dono='$dono')");

        $pdf->Text(12, 89 + $lini, ' NUM');
        $pdf->Text(20, 89 + $lini, ' NOME');
        $pdf->Text(130, 89 + $lini, 'IDADE');



        if (trim($dados_cliente[razao]) <> '') {
            $xh = 1;
            $pdf->Text(12, 90 + $lini + $xh * 6, $xh);

            $pdf->Text(20, 90 + $lini + $xh * 6, $dados_cliente['razao']);

            $idade = '_________';

            $pdf->Text(130, 90 + $lini + $xh * 6, $idade);
        }

        $qtde_hospedes = mysqli_num_rows($sel_hospedes);
        if ($qtde_hospedes > 0) {

            while ($hospedes = mysqli_fetch_assoc($sel_hospedes)) {
                $xh++;
                $pdf->Text(12, 90 + $lini + $xh * 6, $xh);
                $dados_hospedes = explode(',', $hospedes['nome']);
                $pdf->Text(20, 90 + $lini + $xh * 6, $dados_hospedes[0]);
                $idade = trim($dados_hospedes[1]);
                if ($idade == '') {
                    $idade = '_________';
                }
                $pdf->Text(130, 90 + $lini + $xh * 6, $idade);
            }
        }

        while ($xh < 4) {
            $xh++;
            $pdf->Text(12, 90 + $lini + $xh * 6, $xh);

            $pdf->Text(20, 90 + $lini + $xh * 6, '__________________________________________');

            $idade = '_________';

            $pdf->Text(130, 90 + $lini + $xh * 6, $idade);
        }


        //  OBS  NOTEBEM

        $notebem = $info_nfdocumentos[notebem];
        $pdf->Text(12, 130 + $lini, utf8_decode('Observação:'));

        $tam_notebem = strlen($notebem);
        $qtde_linha_notebem = $tam_notebem / 130;

        for ($i = 0; $i <= $qtde_linha_notebem; $i++) {
            $xi = $i * 130;
            $notebem_lin = substr($notebem, $xi, 130);
            $pdf->Text(12, 130 + $lini + ($i + 1) * 5, $notebem_lin);
        }


        //quadro  ATENCAO

        $pdf->Rect(10, 150 + $lini, 190, 30);  // +12
        $pdf->SetFont('Arial', '', 11);
        $pdf->Text(11, 156 + $lini, utf8_decode("ATENÇÃO "));
        $pdf->SetFont('Arial', '', 8);
        $pdf->Text(11, 161 + $lini, utf8_decode("- 01 vaga para veículo por apartamento no estacionamento"));
        $pdf->Text(11, 166 + $lini, utf8_decode("- Não responsabilizamos por veículos , objetos e/ou pertences deixados no apartamento , estacionamento ou área externa."));
        $pdf->Text(11, 171 + $lini, utf8_decode("- Não fornecemos roupa de cama, travesseiros, toalhas, produtos de higiene."));
        $pdf->Text(11, 176 + $lini, utf8_decode("- Não está incluso na diária o café, almoço ou jantar. (Opcional terceirizado)"));


        $pdf->Rect(10, 182 + $lini, 190, 30);  // +12
        $pdf->SetFont('Arial', '', 11);
        $pdf->Text(11, 187 + $lini, "OPCIONAL ");
        $pdf->SetFont('Arial', '', 8);
        $pdf->Text(11, 193 + $lini, utf8_decode("[___] Sim, desejo adquirir o café opcional para ____ pessoa(s)."));
        $pdf->Text(11, 199 + $lini, utf8_decode("[___] Sim, desejo adquirir o almoço opcional para ____ pessoa(s)."));
        #$pdf->Text(11,205 + $lini,"* Estes servi?os opcionais s?o fornecidos por terceiros e os valores e hor?rios dever?o ser combinados com os mesmos ao receber as chaves.");
        #$pdf->Rect(10,217 + $lini,190,20);  // +12
        #$pdf->SetFont('Arial','',11);
        #$pdf->Text(11,222 + $lini,"CONTATO");
        #$pdf->SetFont('Arial','',8);
        #$pdf->Text(35,222 + $lini,"(Favor entrar em contato com anteced?ncia nos telefones abaixo para agendar a entrega das chaves em Cachoeira Dourada-MG)");
        #$pdf->SetFont('Arial','',10);
        #$pdf->Text(11,228 + $lini,"Cinelandia - (34) 9815 0426");
        #$pdf->Text(11,234 + $lini,"Ma?sa      - (34) 9826 0509");

        $pdf->Text(35, 244 + $lini, utf8_decode(trim($infotitulo['cidade'])) . "-" . trim($infotitulo['uf']) . ", _____ DE ___________________ DE_______");
        $pdf->Text(45, 256 + $lini, "____________________________________________");
        $pdf->Text(60, 262 + $lini, utf8_decode($dados_cliente['razao']));
        $pdf->Text(60, 268 + $lini, 'CPF:' . $dados_cliente['cnpj']);

        BREAK;

    case 'boletim_ocupacao_hoteleira': // Boletim de Ocupa??o de Hotelaria (BOH)
        $land_port = 'P';
        $por_pagina = 40;
        $periodo1 = _myfunc_stod($id_periodo1);
        $periodo2 = _myfunc_stod($id_periodo2);

        $periodo_total = $periodo1 . ' a ' . $periodo2;
        $imagem = _myfuncoes_logomarca();
        $razao = $infotitulo['razaosocial'];
        $cpnj = $infotitulo['cnpjcpf'];
        $dadoscnpjcpf = _myfun_dados_cnpjcpf($cpnj);

        //exit($periodo1);
        //exit($periodo2);

        $di = $periodo1;
        $df = $periodo2;
        $finished = false;

        $dia_anterior = subDayIntoDate($di, 1);
        $dia_anterior_query = _myfunc_dtos($dia_anterior);
        //exit($dia_anterior_query);
        $getEntradasOld = "SELECT SUM(hospedes) FROM $TNFDOCUMENTOS_TMP WHERE datacad <= '$dia_anterior_query' AND id_hotel_local <> ''";
        $sqlNqtosAnt   = "SELECT count(distinct(dono)) as total FROM $TNFDOCUMENTOS_TMP WHERE datacad <= '$dia_anterior_query' AND id_hotel_local <> ''";
        //exit($sqlNqtosAnt);
        $getNqtosAnt   = mysqli_query($conn_a, $sqlNqtosAnt);
        $gtosAnt = mysqli_fetch_array($getNqtosAnt);
        //exit($getEntradasOld);
        $sql_entradas_old_tmp = mysqli_query($conn_a, $getEntradasOld);
        $hospedes_mes_anterior = 0;
        list($hospedes_mes_anterior) = mysqli_fetch_row($sql_entradas_old_tmp);
       
        if ($hospedes_mes_anterior <= 0) {
            $hospedes_mes_anterior = 0;
        }

        $pdf->AddPage();
        $pdf->Image('../imagens/logo_embratur.jpg', 11, 10, 40, 10, 'JPG'); //Logomarca EMBRATUR
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(65, 14, 'BOLETIM DE OCUPACAO HOTELEIRA');
        $pdf->Text(90, 18, 'BOH');

        $pdf->Rect(150, 11, 50, 10); // posicao x ,posicao y da altura em rela��o a pagina , tamanho do retangulo, altura do retangulo
        $pdf->SetFont('Arial', '', 8);
        $pdf->Text(150, 14, '00 Reservado'); // primeiro numero e o espa�o entre a margem e o texto, segundo e a altura entre a linha do retangulo e o texto
        $pdf->SetFont('Arial', '', 6);
        $pdf->Rect(10, 23, 70, 10);
        $pdf->Text(10, 26, '01 NUMERO EMBRATUR');
        $pdf->Rect(82, 23, 20, 10);
        $pdf->Text(82, 26, '02 LEITOS');
        $pdf->Rect(104, 23, 20, 10);
        $pdf->Text(104, 26, '03 UHs');
        $pdf->Rect(126, 23, 74, 10);
        $pdf->Text(126, 26, '04 REGISTRO DE MOVIMENTO DIARIO DO PERIODO COMPREENDIDO');
        $pdf->Text(128, 29, substr($periodo1, 0, 2) . ' E ' . substr($periodo2, 0, 2) . ' DE ' . substr($periodo2, 3, 2) . ' DE ' . substr($periodo2, 6, 4));
        //LINHA 2
        $pdf->Rect(10, 35, 190, 10);
        $pdf->Text(10, 38, '05 NOME DO ESTABELECIMENTO');
        $pdf->Text(12, 42, $razao);
        $pdf->Rect(10, 47, 80, 10);
        $pdf->Text(10, 50, '06 DISTRITO OU LOCALIDADE');
        $pdf->Text(12, 53, $dadoscnpjcpf[cidade]);
        $pdf->Rect(90, 47, 100, 10);
        $pdf->Text(90, 50, 'MUNICIPIO');
        $pdf->Text(92, 53, $dadoscnpjcpf[cidade]);
        $pdf->Rect(190, 47, 10, 10);
        $pdf->Text(190, 50, 'UF');
        $pdf->Text(192, 53, $dadoscnpjcpf[uf]);
        //LINHA 3
        $pdf->Rect(10, 59, 25, 18); // 07 CAMP
        $pdf->Text(10, 62, '07 CAMPO 10 DO');
        $pdf->Text(10, 65, utf8_decode('MÊS ANTERIOR'));
        $pdf->Text(10, 68, ' (ULTIMO DIA)');
        $pdf->Text(16, 72, $hospedes_mes_anterior);
        $pdf->Rect(35, 59, 135, 13); //MOVIMENTO HOSP
        $pdf->Text(36, 65, '08');
        $pdf->Text(90, 65, 'MOVIMENTO DE HOSPEDES');
        $pdf->Rect(170, 59, 30, 18); //UHS OCUPADAS
        $pdf->Text(171, 65, '09');
        $pdf->Text(180, 65, 'UHs OCUPADAS');
        $pdf->Rect(35, 72, 15, 5); //DIAS
        $pdf->Text(40, 75, 'DIAS');
        $pdf->Rect(50, 72, 40, 5);
        $pdf->Text(62, 75, 'ENTRADAS');
        $pdf->Rect(90, 72, 40, 5);
        $pdf->Text(105, 75, utf8_decode('SAÍDAS'));
        $pdf->Rect(130, 72, 40, 5); //HOSPEDAGEM
        $pdf->Text(142, 75, 'HOSPEDADOS');
        $pdf->Rect(170, 72, 15, 5); //DIAS 2
        $pdf->Text(175, 75, 'DIAS');
        $pdf->Ln(62);
        
        $hp = $hospedes_mes_anterior;
        while (!$finished):

            $dataI_query = _myfunc_dtos_0hs($di);
            $dataF_query = _myfunc_dtos($di);
            
            $getEntrada1 = "SELECT SUM(hospedes) as entrada FROM $TNFDOCUMENTOS_TMP WHERE datacad >= '$dataI_query' AND datacad <= '$dataF_query' AND id_hotel_local <> ''";
            $sql_entradas1 = mysqli_query($conn_a, $getEntrada1);
            list($entrada_tmp) = mysqli_fetch_row($sql_entradas1);
            
            $sqlNqtos   = "SELECT count(distinct(dono)) as total FROM $TNFDOCUMENTOS_TMP WHERE datacad >= '$dataI_query' AND datacad <= '$dataF_query' AND id_hotel_local <> ''";
            //exit($sqlNqtos);
            $getNqtos   = mysqli_query($conn_a, $sqlNqtos);
            $gtos = mysqli_fetch_array($getNqtos);
            
            $getEntrada2 = "SELECT SUM(hospedes) as entrada FROM $TNFDOCUMENTOS WHERE datacad >= '$dataI_query' AND datacad <= '$dataF_query' AND id_hotel_local <> ''";
            $sql_entradas2 = mysqli_query($conn_a, $getEntrada2);
            list($entrada2) = mysqli_fetch_row($sql_entradas2);
            
//            SELECT dono, from_unixtime( data ) , from_unixtime( datacad )
//FROM `nfdocumentos`
//WHERE dono
//IN (
//'LAN295185089162056', 'LAN29518382626205', 'LAN295196389062010', 'LAN296985244462012', 'LAN296981198862011', 'LAN296973009862044', 'LAN296973963062050', 'LAN295197252962018', 'LAN29613562931045'
//)
            
            $getSaida = "SELECT SUM(hospedes) as saida FROM $TNFDOCUMENTOS WHERE datacad >= '$dataI_query' AND datacad <= '$dataF_query'";
            $sql_saida = mysqli_query($conn_a, $getSaida);
            list($saida) = mysqli_fetch_row($sql_saida);
            
            $entrada_total = $entrada_tmp +  $entrada2; // não dei se onde é essa variavel
            $hospedados = $entrada_total - $saida;
            
            if ($hospedados <= 0) {
                $hospedados = 0;
            }
            
            $conentrada = $conentrada + $entrada_total;
            $consaida = $consaida + $saida;

            $conuhs = $conuhs + $hospedados;

            $pdf->Ln(5);
            $pdf->SetFont("Arial", "", 6);
            $pdf->Cell(25, 5, "", 0, 0, 'C');
            $pdf->Cell(15, 5, substr($di, 0, 2).'', 1, 0, 'C');
            if ($entrada_total == '') {
                $pdf->Cell(40, 5, '0', 1, 0, 'C');
            } else {
                $pdf->Cell(40, 5, $entrada_total, 1, 0, 'C');
            }
            if ($saida == '') {
                $pdf->Cell(40, 5, '0', 1, 0, 'C');
            } else {
                $pdf->Cell(40, 5, $saida, 1, 0, 'C');
            }
            
            $hp += $entrada_total - $saida; 
            $qtosAnt = floatval($gtosAnt['total']);
            $qtosAtu = floatval($gtos['total']);
            //exit($qtosAnt);
            //echo var_dump($gtosAnt).'<br>';
            //echo var_dump($gtos).'<br>';
            $nQtos = $qtosAnt + $qtosAtu;
            $pdf->Cell(40, 5, $hp, 1, 0, 'C');
            $pdf->Cell(15, 5, substr($di, 0, 2), 1, 0, 'C');
            $pdf->Cell(15, 5, $nQtos, 1, 0, 'L');
            
            //exit($nQtos);

            $di = _myfuncoes_addDayIntoDate($di, 1);
            $data_atual = _myfunc_dtos($di);
            if (_myfunc_dtos($di) > _myfunc_dtos($df)) {
                $finished = true;
            }
            //$pdf->Ln(5);
            //$pdf->Cell(0, 5, $getEntrada1, 1, 0, 'L');
            
        endwhile;

        $pdf->Ln(10);
        $pdf->Cell(25, 5, " ", 0, 0, 'L');
        $pdf->Cell(15, 5, "TOTAL", 1, 0, 'L');
        $pdf->Cell(40, 5, $conentrada, 1, 0, 'C');
        $pdf->Cell(40, 5, $consaida, 1, 0, 'C');
        $contador_hospedes = $hp;
        $pdf->Cell(40, 5, $contador_hospedes, 1, 0, 'C');
        $pdf->Cell(15, 5, "TOTAL", 1, 0, 'L');
        //$pdf->Cell(15, 5, $conuhs, 1, 0, 'L');
        $pdf->Cell(15, 5, $gtosAnt['total'] + $qtos['total'], 1, 0, 'L');

        $pdf->Ln(20);
        $pdf->Rect(10, 250, 90, 35);
        $pdf->Text(10, 253, '15 CARIMBO DO ESTABELECIMENTO - DATA E ASSINATURA');
        $pdf->Rect(110, 250, 90, 35);
        $pdf->Text(110, 253, '16 PROTOCOLO DE RECEBIMENTO');

        break;


    case "relatorio_ocupacao":
        $linha = 40;
        $sel_empresa = mysql_query("select * from $TSEGMENTOS where cnpjcpf = '$info_cnpj_segmento' ", $CONTSEGMENTOS);
        $v = mysql_fetch_assoc($sel_empresa);
        $land_port = 'P';
        $por_pagina = 40;
        $nomerel = utf8_decode('RELATÓRIO DE OCUPAÇÃO');

        $query = "select a.id_hotel_local, a.classifica, n.datacad, n.datasaida, c.razao, n.vcontabilnf, l.dono, n.hospedes from $TAPARTAMENTOS a, $TCNPJCPF c, $TNFDOCUMENTOS_TMP n, $TLANCAMENTOS_TMP l where n.dono = l.dono and l.cnpjcpf = c.cnpj and n.id_hotel_local != '' and n.id_hotel_local = a.id_hotel_local group by n.id_hotel_local";
        //exit($query);
        $result = mysql_query($query);

        $ocupacoes = Array();
        while ($ocup = mysql_fetch_assoc($result)) {
            $dono = $ocup['dono'];
            $apto = $ocup['id_hotel_local'];
            #$hosp = mysql_query("SELECT count(dono) as hospedes FROM $TAPARTAMENTOS_HOSPEDES WHERE dono = '$dono'");
            #while($hospedes = mysql_fetch_assoc($hosp)) $ocupacoes[$apto]['pessoas'] = $hospedes['hospedes']+1;
            $ocupacoes[$apto]['pessoas'] = $ocup['hospedes'];
            $ocupacoes[$apto]['classifica'] = $ocup['classifica'];
            $ocupacoes[$apto]['hospede'] = $ocup['razao'];
            $ocupacoes[$apto]['valor'] = $ocup['vcontabilnf'];
            $ocupacoes[$apto]['entrada'] = _myfunc_stod($ocup['datacad']);
            $ocupacoes[$apto]['saida'] = _myfunc_stod($ocup['datasaida']);
        }

        $total = Array();
        foreach ($ocupacoes as $key => $o) {

            if ($linha == $por_pagina) {
                include('../pdf/fpdf_cabecalho_wss.php');
                $linha = 1;
                $pdf->SetFont("Arial", "", 7);
                $pdf->Cell(15, 5, "APTO", 1, 0, 'P');
                $pdf->Cell(35, 5, "CLASSIFICA", 1, 0, 'P');
                $pdf->Cell(20, 5, "ENTRADA", 1, 0, 'P');
                $pdf->Cell(20, 5, "SAIDA", 1, 0, 'P');
                $pdf->Cell(55, 5, "HOPEDE", 1, 0, 'P');
                $pdf->Cell(14, 5, "PESSOAS", 1, 0, 'P');
                $pdf->Cell(18, 5, "VALOR AG", 1, 0, 'P');
            }
            $pdf->Ln(5);
            $pdf->Cell(15, 5, "$key", 1, 0, 'P');
            $pdf->Cell(35, 5, $o['classifica'], 1, 0, 'P');
            $pdf->Cell(20, 5, $o['entrada'], 1, 0, 'P');
            $pdf->Cell(20, 5, $o['saida'], 1, 0, 'P');
            $pdf->Cell(55, 5, $o['hospede'], 1, 0, 'P');
            $pdf->Cell(14, 5, $o['pessoas'], 1, 0, 'R');
            $total['pessoas'] += $o['pessoas'];
            $pdf->Cell(18, 5, number_format($o['valor'], 2, ',', '.'), 1, 0, 'R');
            $total['valor'] += $o['valor'];
            $linha++;
        }
        $pdf->Ln(5);
        $pdf->Cell(177, 5, "TOTAIS: ", 1, 0, 'P');
        $pdf->Ln(5);
        $pdf->Cell(15, 5, "APTOS: " . count($ocupacoes), 1, 0, 'P');
        $pdf->Cell(130, 5, "", 1, 0, 'P');
        $pdf->Cell(14, 5, $total['pessoas'], 1, 0, 'R');
        $pdf->Cell(18, 5, number_format($total['valor'], 2, ',', '.'), 1, 0, 'R');
        //$pdf->Cell(20,5,$o['classifica'], 1, 0, 'P');

        break;


    case "relatorio_reservas":
        $linha = 30;
        $sel_empresa = mysql_query("select * from $TSEGMENTOS where cnpjcpf = '$info_cnpj_segmento' ", $CONTSEGMENTOS);
        $v = mysql_fetch_assoc($sel_empresa);
        $land_port = 'L';
        $por_pagina = 30;
        $nomerel = utf8_decode('RELATÓRIO DE RESERVAS');
        $filtro_periodo = "(r.entrada >= $id_periodo1 AND r.entrada <= $id_periodo2) and r.cnpjcpfcad = c.cnpj";
        $ordem = "ORDER BY ";
        if ($_POST['ordenarpor'] == "hospede")
            $ordem .= "r.razao";
        else
            $ordem .= "r.entrada";

        $texto_filtro = $texto_filtro . utf8_decode("Período : ") . _myfunc_stod($id_periodo1) . " a " . _myfunc_stod($id_periodo2);

        #$query = "select r.*, c.razao as user from apartamentos_reservas r, cnpjcpf c where $filtro_periodo $ordem";
        $query = "select r.*,c.razao as agente from $TAPARTAMENTOS_RESERVAS r, $TCNPJCPF as c where $filtro_periodo $ordem";
        //exit($query);
            
        #echo $query;

        $result = mysql_query($query);
        $reservas = Array();
        $i = 0;
        while ($registroHospedes = mysql_fetch_assoc($result)) {
            $reservas[$i]['apto'] = $registroHospedes['id_hotel_local'];
            $reservas[$i]['classifica'] = $registroHospedes['classifica'];
            $reservas[$i]['nome'] = $registroHospedes['razao'];
            #$reservas[$i]['hospede'] = $registroHospedes['razao'];
            $reservas[$i]['data'] = _myfunc_stod($registroHospedes['data']);
            $reservas[$i]['entrada'] = _myfunc_stod($registroHospedes['entrada']);
            $reservas[$i]['saida'] = _myfunc_stod($registroHospedes['saida']);
            $reservas[$i]['usuario'] = $registroHospedes['agente'];
            $reservas[$i]['obs'] = $registroHospedes['obs'];
            $i++;
        }

        /* echo "<pre>";
          print_r($ocupacoes);
          echo "</pre>"; */
        $total = Array();
        $i = 0;
        foreach ($reservas as $r) {

            if ($linha == $por_pagina) {
                include('../pdf/fpdf_cabecalho_wss.php');
                $linha = 1;
                $pdf->SetFont("Arial", "", 7);
                $pdf->Cell(20, 5, "DATA RESERVA", 1, 0, 'P');
                $pdf->Cell(50, 5, "REQUERENTE", 1, 0, 'P');
                $pdf->Cell(40, 5, "APARTAMENTO", 1, 0, 'P');
                $pdf->Cell(25, 5, utf8_decode("USUÁRIO"), 1, 0, 'P');
                $pdf->Cell(20, 5, "DT ENTRADA", 1, 0, 'P');
                $pdf->Cell(20, 5, "DT SAIDA", 1, 0, 'P');
                $pdf->Cell(105, 5, utf8_decode("OBSERVAÇÃO"), 1, 0, 'P');
            }
            $pdf->Ln(5);
            $pdf->Cell(20, 5, $r['data'], 1, 0, 'P');
            $pdf->Cell(50, 5, $r['nome'], 1, 0, 'P');
            $pdf->Cell(40, 5, $r['apto'] . '-' . $r['classifica'], 1, 0, 'P');
            $nome = substr($r['usuario'], 0, 15);  //split(" ", $r['usuario']);
            $pdf->Cell(25, 5, $nome, 1, 0, 'P');   //$nome[0] 
            $pdf->Cell(20, 5, $r['entrada'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['saida'], 1, 0, 'P');
            $pdf->Cell(105, 5, $r['obs'], 1, 0, 'P');
            $linha++;
            $i++;
        }
        $pdf->Ln(5);
        $pdf->Cell(50, 5, "Total apartamentos reservados: ", 1, 0, 'P');
        $pdf->Cell(20, 5, $i, 1, 0, 'P');
        //$pdf->Cell(20,5,$o['classifica'], 1, 0, 'P');

        break;

    case "relatorio_registro_geral":
        $linha = 30;
        $sel_empresa = mysql_query("select * from $TSEGMENTOS where cnpjcpf = '$info_cnpj_segmento' ", $CONTSEGMENTOS);
        $v = mysql_fetch_assoc($sel_empresa);
        $land_port = 'L';
        $por_pagina = 30;
        $nomerel = utf8_decode('RELATÓRIO DE REGISTRO GERAL DE HOSPEDES');

        $query = "SELECT l.dono, l.cnpjcpf, c.razao, c.nascimento, n.datacad, n.id_hotel_local, l.pagamento, n.cidade_origem, n.cidade_destino FROM $TLANCAMENTOS_TMP l, $TNFDOCUMENTOS_TMP n, $TCNPJCPF c WHERE l.dono = n.dono AND n.id_hotel_local != '' AND l.cnpjcpf = c.cnpj group by n.id_hotel_local";
        $result = mysql_query($query);

        $registroHospedes = Array();
        $i = 0;
        while ($ref = mysql_fetch_assoc($result)) {
            $registroHospedes[$i]['hospede'] = $ref['razao'];
            $registroHospedes[$i]['cnpj'] = $ref['cnpjcpf'];
            $registroHospedes[$i]['dono'] = $ref['dono'];
            $registroHospedes[$i]['nascimento'] = _myfunc_stod($ref['nascimento']);
            $registroHospedes[$i]['entrada'] = _myfunc_stod($ref['datacad']);
            $registroHospedes[$i]['saida'] = _myfunc_stod($ref['pagamento']);
            $registroHospedes[$i]['origem'] = utf8_decode(utf8_decode($ref['cidade_origem']));
            $registroHospedes[$i]['destino'] = utf8_decode(utf8_decode($ref['cidade_destino']));
            $registroHospedes[$i]['apartamento'] = $ref['id_hotel_local'];
            $i++;
        }

        $total = Array();
        $i = 0;
        foreach ($registroHospedes as $r) {

            if ($linha == $por_pagina) {
                include('../pdf/fpdf_cabecalho_wss.php');
                $linha = 1;
                $pdf->SetFont("Arial", "", 7);
                $pdf->Cell(30, 5, utf8_decode("Nº DOCUMENTO"), 1, 0, 'P');
                $pdf->Cell(60, 5, "HOSPEDE", 1, 0, 'P');
                $pdf->Cell(20, 5, "CNPJ/CPF", 1, 0, 'P');
                $pdf->Cell(20, 5, "NASCIMENTO", 1, 0, 'P');
                $pdf->Cell(25, 5, "DATA ENTRADA", 1, 0, 'P');
                $pdf->Cell(20, 5, "APARTAMENTO", 1, 0, 'P');
                $pdf->Cell(20, 5, utf8_decode("PROCEDÊNCIA"), 1, 0, 'P');
                $pdf->Cell(25, 5, utf8_decode("DATA SAÍDA"), 1, 0, 'P');
                $pdf->Cell(20, 5, "DESTINO", 1, 0, 'P');
                $pdf->Cell(25, 5, "N FICHA POLICIAL", 1, 0, 'P');
                $pdf->Cell(20, 5, "OBS", 1, 0, 'P');
            }
            $pdf->Ln(5);
            $pdf->SetFont("Arial", "", 7);
            $pdf->Cell(30, 5, $r['dono'], 1, 0, 'P');
            $pdf->Cell(60, 5, $r['hospede'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['cnpj'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['nascimento'], 1, 0, 'P');
            $pdf->Cell(25, 5, $r['entrada'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['apartamento'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['origem'], 1, 0, 'P');
            $pdf->Cell(25, 5, $r['pagamento'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['destino'], 1, 0, 'P');
            $pdf->Cell(25, 5, "", 1, 0, 'P');
            $pdf->Cell(20, 5, "", 1, 0, 'P');
            $linha++;
            $i++;
        }
        break;

    case "relatorio_refeicao":
        $linha = 40;
        #$sel_empresa=mysql_query("select * from $TSEGMENTOS where cnpjcpf = '$info_cnpj_segmento' ", $CONTSEGMENTOS);
        #$v=mysql_fetch_assoc($sel_empresa);
        #$land_port='P';
        $por_pagina = 40;
        $nomerel = utf8_decode('RELATÓRIO DE HOSPEDES PARA REFEIÇÕES');

        $query = "SELECT n.id_hotel_local, c.razao, l.data, n.dono, n.hospedes FROM $TLANCAMENTOS_TMP l, $TNFDOCUMENTOS_TMP n, $TCNPJCPF c WHERE l.dono = n.dono AND l.cnpjcpf = c.cnpj AND n.id_hotel_local <> '' GROUP BY n.id_hotel_local";
        //exit($query);
        $result = mysql_query($query);

        $registroHospedes = Array();
        $i = 0;
        while ($ref = mysql_fetch_assoc($result)) {
            $dono = $ref['dono'];
            #$hosp = mysql_query("SELECT count(dono) as hospedes FROM $TAPARTAMENTOS_HOSPEDES WHERE dono = '$dono'");
            #while($hospedes = mysql_fetch_assoc($hosp)) $registroHospedes[$i]['pessoas'] = $hospedes['hospedes']+1;
            $registroHospedes[$i]['pessoas'] = $ref['hospedes'];
            $registroHospedes[$i]['hospede'] = $ref['razao'];
            $registroHospedes[$i]['entrada'] = _myfunc_stod($ref['data']);
            $registroHospedes[$i]['apartamento'] = $ref['id_hotel_local'];
            $i++;
        }
        /* echo "<pre>";
          print_r($ocupacoes);
          echo "</pre>"; */
        $total = Array();
        $i = 0;
        $pessoas = 0;
        //exit(var_dump($registroHospedes));
        foreach ($registroHospedes as $r) {

            if ($linha == $por_pagina) {
                include('../pdf/fpdf_cabecalho_wss.php');
                $linha = 1;
                $pdf->SetFont("Arial", "", 7);
                $pdf->Cell(20, 5, "APARTAMENTO", 1, 0, 'P');
                $pdf->Cell(60, 5, "HOSPEDE", 1, 0, 'P');
                $pdf->Cell(25, 5, "DATA ENTRADA", 1, 0, 'P');
                $pdf->Cell(20, 5, "PESSOAS", 1, 0, 'P');
            }
            $pdf->Ln(5);
            $pdf->SetFont("Arial", "", 7);
            $pdf->Cell(20, 5, $r['apartamento'], 1, 0, 'P');
            $pdf->Cell(60, 5, $r['hospede'], 1, 0, 'P');
            $pdf->Cell(25, 5, $r['entrada'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['pessoas'], 1, 0, 'R');
            $pessoas += $r['pessoas'];
            $linha++;
            $i++;
        }
        $pdf->Ln(5);
        $pdf->SetFont("Arial", "", 7);
        $pdf->Cell(105, 5, "TOTAL:", 1, 0, 'P');
        $pdf->Cell(20, 5, $pessoas, 1, 0, 'R');

        break;

    case "relatorio_veiculo":
        $linha = 40;
        #$sel_empresa=mysql_query("select * from $TSEGMENTOS where cnpjcpf = '$info_cnpj_segmento' ", $CONTSEGMENTOS);
        #$v=mysql_fetch_assoc($sel_empresa);
        $land_port = 'P';
        $por_pagina = 40;
        $nomerel = utf8_decode('RELATÓRIO DE VEÍCULOS X HOSPEDES');

        $query = "SELECT n.id_hotel_local, n.placatransp, a.ramal, c.razao, n.datacad FROM $TLANCAMENTOS_TMP l, $TNFDOCUMENTOS_TMP n, $TCNPJCPF c, $TAPARTAMENTOS a WHERE l.dono = n.dono AND l.cnpjcpf = c.cnpj AND a.id_hotel_local = n.id_hotel_local group by n.id_hotel_local";
        
        $result = mysql_query($query);

        $registroHospedes = Array();
        $i = 0;
        while ($ref = mysql_fetch_assoc($result)) {
            $registroHospedes[$i]['hospede'] = $ref['razao'];
            $registroHospedes[$i]['entrada'] = _myfunc_stod($ref['datacad']);
            $registroHospedes[$i]['apartamento'] = $ref['id_hotel_local'];
            $registroHospedes[$i]['placa'] = $ref['placatransp'];
            $registroHospedes[$i]['ramal'] = $ref['ramal'];
            $i++;
        }
        /* echo "<pre>";
          print_r($ocupacoes);
          echo "</pre>"; */
        $total = Array();
        $i = 0;
        foreach ($registroHospedes as $r) {

            if ($linha == $por_pagina) {
                include('../pdf/fpdf_cabecalho_wss.php');
                $linha = 1;
                $pdf->SetFont("Arial", "", 7);
                $pdf->Cell(20, 5, "PLACA", 1, 0, 'P');
                $pdf->Cell(60, 5, "HOSPEDE", 1, 0, 'P');
                $pdf->Cell(25, 5, "APARTAMENTO", 1, 0, 'P');
                $pdf->Cell(20, 5, "RAMAL", 1, 0, 'P');
                $pdf->Cell(25, 5, "DATA ENTRADA", 1, 0, 'P');
            }
            $pdf->Ln(5);
            $pdf->SetFont("Arial", "", 7);
            $pdf->Cell(20, 5, $r['placa'], 1, 0, 'P');
            $pdf->Cell(60, 5, $r['hospede'], 1, 0, 'P');
            $pdf->Cell(25, 5, $r['apartamento'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['ramal'], 1, 0, 'P');
            $pdf->Cell(25, 5, $r['entrada'], 1, 0, 'P');
            $linha++;
            $i++;
        }

        break;

    case "relatorio_geral":
        $linha = 40;
        $sel_empresa = mysql_query("select * from $TSEGMENTOS where cnpjcpf = '$info_cnpj_segmento' ", $CONTSEGMENTOS);
        $v = mysql_fetch_assoc($sel_empresa);
        $land_port = 'P';
        $por_pagina = 40;
        $nomerel = 'RELATORIO GERAL DE HOSPEDAGEM';

        $query = "SELECT n.id_hotel_local, c.razao, n.datacad, a.classifica, n.vcontabilnf, n.produtosnf, n.servicosnf FROM $TLANCAMENTOS_TMP l, $TNFDOCUMENTOS_TMP n, $TCNPJCPF c, $TAPARTAMENTOS a WHERE l.dono = n.dono AND l.cnpjcpf = c.cnpj AND a.id_hotel_local = n.id_hotel_local group by n.id_hotel_local";
        $result = mysql_query($query);

        $registroHospedes = Array();
        $i = 0;
        while ($ref = mysql_fetch_assoc($result)) {
            $registroHospedes[$i]['hospede'] = $ref['razao'];
            $registroHospedes[$i]['entrada'] = _myfunc_stod($ref['datacad']);
            $registroHospedes[$i]['apartamento'] = $ref['id_hotel_local'];
            $registroHospedes[$i]['classifica'] = $ref['classifica'];
            $registroHospedes[$i]['valor'] = $ref['vcontabilnf'];
            $registroHospedes[$i]['produtos'] = $ref['produtosnf'];
            $registroHospedes[$i]['servicos'] = $ref['servicosnf'];
            $i++;
        }
        /* echo "<pre>";
          print_r($registroHospedes);
          echo "</pre>"; */
        $total = Array();
        $i = 0;
        $totalProdutos;
        $totalServicos;
        $total = 0;
        foreach ($registroHospedes as $r) {
            if ($linha == $por_pagina) {
                include('../pdf/fpdf_cabecalho_wss.php');
                $linha = 1;
                $pdf->SetFont("Arial", "", 7);
                $pdf->Cell(15, 5, "APTO", 1, 0, 'P');
                $pdf->Cell(30, 5, "CLASSIFICA", 1, 0, 'P');
                $pdf->Cell(55, 5, "HOSPEDE", 1, 0, 'P');
                $pdf->Cell(20, 5, "DATA ENTRADA", 1, 0, 'P');
                $pdf->Cell(20, 5, "VALOR", 1, 0, 'P');
                $pdf->Cell(20, 5, "PRODUTOS", 1, 0, 'P');
                $pdf->Cell(20, 5, utf8_decode("SERVIÇOS"), 1, 0, 'P');
            }
            $pdf->Ln(5);
            $pdf->SetFont("Arial", "", 7);
            $pdf->Cell(15, 5, $r['apartamento'], 1, 0, 'P');
            $pdf->Cell(30, 5, $r['classifica'], 1, 0, 'P');
            $pdf->Cell(55, 5, $r['hospede'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['entrada'], 1, 0, 'P');
            $pdf->Cell(20, 5, $r['valor'], 1, 0, 'P');
            $total += $r['valor'];
            $pdf->Cell(20, 5, $r['produtos'], 1, 0, 'P');
            $totalProdutos += $r['produtos'];
            $pdf->Cell(20, 5, $r['servicos'], 1, 0, 'P');
            $totalServicos += $r['servicos'];
            $linha++;
            $i++;
        }
        $pdf->Ln(5);
        $pdf->SetFont("Arial", "", 7);
        $pdf->Cell(120, 5, "TOTAL:", 1, 0, 'P');
        $pdf->Cell(20, 5, number_format($total, 2, ",", "."), 1, 0, 'P');
        $pdf->Cell(20, 5, number_format($totalProdutos, 2, ",", "."), 1, 0, 'P');
        $pdf->Cell(20, 5, number_format($totalServicos, 2, ",", "."), 1, 0, 'P');
        break;
        
    case "relatorio_analitico_periodo":
        $linha = 30;
        
        $land_port = 'L';
        $por_pagina = 30;
        $nomerel = utf8_decode('RELATÓRIO ANALÍTICO NO PERÍODO');
        $filtro_periodo = "(n.data >= $id_periodo1 AND n.data <= $id_periodo2) AND n.id_hotel_local <> ''";
        if (isset($_POST['xmodelonf']) && $_POST['xmodelonf'] <> ' ') {
            $filtro_periodo .= " AND n.modelo = '".$_POST['xmodelonf']."'";
        }

        $texto_filtro = $texto_filtro . utf8_decode("Período : ") . _myfunc_stod($id_periodo1) . " a " . _myfunc_stod($id_periodo2);
        //exit(var_dump($_POST));
        $ordem = ' ';
        $cons = " FROM $TNFDOCUMENTOS n WHERE $filtro_periodo";
        if (isset($_POST['ordenarpor']) && !empty($_POST['ordenarpor'])) {
            if ($_POST['ordenarpor'] == 'c.razao') {
                $cons = ", l.cnpjcpf, c.razao FROM $TNFDOCUMENTOS n, $TLANCAMENTOS l, $TCNPJCPF c WHERE $filtro_periodo AND n.dono = l.dono AND l.cnpjcpf = c.cnpj";
            } 
            $ordem .= " GROUP BY n.dono ORDER BY ".$_POST['ordenarpor'].", n.data";
        }
        $query = "SELECT n.dono, n.numero, n.data, n.datacad, n.id_hotel_local, n.vcontabilnf, n.cnpjcpfcad, n.modelo $cons $ordem";

        //exit($query);        
        $result = mysqli_query($conn_a, $query);
        $rel = Array();
        $i = 0;
        while ($registroHospedes = mysqli_fetch_assoc($result)) {
            $dados_agente = _myfun_dados_cnpjcpf($registroHospedes['cnpjcpfcad']);
            //exit(var_dump($dados_agente));
            //exit($registroHospedes['dono']);
            
            $sql_lancamentos = "SELECT * FROM $TLANCAMENTOS WHERE dono = '".$registroHospedes['dono']."' AND contad <> ''";
            $get_lancamentos = mysqli_query($conn_a, $sql_lancamentos);
            $fp = array();
            while ($info_lancamentos = mysqli_fetch_assoc($get_lancamentos)) {
                $fp[$info_lancamentos['tipodocfinanceiro']] = $info_lancamentos['valor'];
                $id_cnpjcpf = $info_lancamentos['cnpjcpf'];
            }
            

            $dados_cliente = _myfun_dados_cnpjcpf($id_cnpjcpf);
            
            $sql_item = "SELECT * FROM $TITEM_FLUXO WHERE dono = '".$registroHospedes['dono']."' ORDER BY datacad";
            //exit($sql_item);
            $get_item = mysqli_query($conn_a, $sql_item);
            $qtdeItem = mysqli_num_rows($get_item);
            if ($qtdeItem) {
                //exit('chegou aqui');
                //$pdf->Rect(10, 50 + $lini, 190, (3.5*$qtdeItem));
                $totalHosp = $totalLav = $totalTel = $totalCom = $totalRes = $totalFri = $totalFrr = $desconto = 0;
                //$pdf->Ln(46);
                while ($item = mysqli_fetch_array($get_item)) {

                    $desconto += $item['vdesc'];
                    if (empty($item['localoperacao'])) {
                        $sql_produto = "SELECT grupoproduto FROM $TPRODUTOS WHERE conta = '".$item['cprod']."'";
                        //exit($sql_produto);
                        $get_produto = mysqli_query($conn_a, $sql_produto);
                        $qtProd = mysqli_num_rows($get_produto);
                        if ($qtProd) {
                            $res = mysqli_fetch_array($get_produto);
                        } else {
                            $sql_servico = "SELECT grupoproduto FROM $TSERVICOS WHERE conta = '".$item['cprod']."'";
                            //exit($sql_servico);
                            $get_servico = mysqli_query($conn_a, $sql_servico);
                            $res = mysqli_fetch_array($get_servico);
                        }

                        switch ($res['grupoproduto']) {
                            case '090.001':
                                $totalHosp += $item['vprod'];
                                break;
                            case '091.001':
                                $totalLav += $item['vprod'];
                                break;
                            case '092.001':
                                $totalTel += $item['vprod'];
                                break;
                            default:
                                $totalCom += $item['vprod'];
                                break;
                        }
                    } else {
                        switch ($item['localoperacao']) {
                            case '01': 
                                $local = 'APTO'; 
                                $totalHosp += $item['vprod'];
                                $diaria = $item['vuncom'];
                                break;
                            case '02': 
                                $local = 'FRI'; 
                                $totalFri += $item['vprod'];
                                $totalCom += $item['vprod'];
                                break;
                            case '03': 
                                $local = 'FRR'; 
                                $totalFrr += $item['vprod'];
                                $totalCom += $item['vprod'];
                                break;
                            case '04': 
                                $local = 'LAV'; 
                                $totalLav += $item['vprod'];
                                break;
                            case '05': 
                                $local = 'RES'; 
                                $totalRes += $item['vprod'];
                                $totalCom += $item['vprod'];
                                break;
                            case '06': 
                                $local = 'TEL'; 
                                $totalTel += $item['vprod'];
                                break;
                        }
                    }
                }
            }
            
            $rel[$i]['apto'] = $registroHospedes['id_hotel_local'];
            $rel[$i]['dono'] = $registroHospedes['dono'];
            $rel[$i]['numero'] = $registroHospedes['numero'];
            $rel[$i]['data'] = _myfunc_stod($registroHospedes['data']);
            $rel[$i]['datacad'] = _myfunc_stod($registroHospedes['datacad']);
            $rel[$i]['valor'] = $registroHospedes['vcontabilnf'];
            $rel[$i]['cliente'] = $dados_cliente['razao'];
            $rel[$i]['uf'] = $dados_cliente['uf'];
            $rel[$i]['frig'] = $totalFri;
            $rel[$i]['rest'] = $totalRes;
            $rel[$i]['fone'] = $totalTel;
            $rel[$i]['bar'] = $totalFrr;
            $rel[$i]['lavan'] = $totalLav;
            $rel[$i]['diarias'] = $totalHosp;
            $rel[$i]['formaPg'] = $fp;
            $rel[$i]['agente'] = $dados_agente['fantasia'];
            $rel[$i]['modelo'] = $registroHospedes['modelo'];
            
            $i++;
        }

        $total = Array('total'=>0, 'frig'=>0, 'rest'=>0, 'fone'=>0, 'bar'=>0, 'diarias'=>0, 'lavan'=>0);
        
        $i = 0;
        $formaPagamento = array('APP'=>0, 'APR'=>0, 'CCC'=>0, 'CCD'=>0, 'CHR'=>0, 'CHV'=>0, 'DHP'=>0, 'DHR'=>0, 'SMF'=>0, 'SMV'=>0);
        foreach ($rel as $r) {

            if ($linha == $por_pagina) {
                include('../pdf/fpdf_cabecalho_wss.php');
                $linha = 1;
                $pdf->SetFont("Arial", "", 7);
                $pdf->Cell(10, 5, "APTO", 1, 0, 'P');
                $pdf->Cell(65, 5, "CLIENTE", 1, 0, 'P');
                $pdf->Cell(5, 5, "UF", 1, 0, 'C');
                $pdf->Cell(20, 5, utf8_decode("EMISSÃO"), 1, 0, 'C');
                $pdf->Cell(20, 5, "DOCUMENTO", 1, 0, 'C');
                $pdf->Cell( 8, 5, "MOD", 1, 0, 'C');
                $pdf->Cell(15, 5, "VALOR", 1, 0, 'C');
                $pdf->Cell(10, 5, "FP", 1, 0, 'C');
                $pdf->Cell(15, 5, "FRIG", 1, 0, 'C');
                $pdf->Cell(15, 5, "RESTA", 1, 0, 'C');
                $pdf->Cell(15, 5, "FONE", 1, 0, 'C');
                $pdf->Cell(15, 5, "FRIGR", 1, 0, 'C');
                $pdf->Cell(15, 5, "DIARIAS", 1, 0, 'C');
                $pdf->Cell(15, 5, "LAVAN", 1, 0, 'C');
                $pdf->Cell(25, 5, "AGENTE", 1, 0, 'C');
            }
            
            $totalFPG = count($r['formaPg']);
            $pg = 1;
            foreach ($r['formaPg'] as $k=>$v) {
                //echo $k.'=>'.$v.'<br>';
                $pdf->Ln(5);
                if ($pg == 1) {
                    $pdf->Cell(10, 5, $r['apto'], 1, 0, 'P');
                    $pdf->Cell(65, 5, utf8_decode($r['cliente']), 1, 0, 'P');
                    $pdf->Cell(5, 5, $r['uf'], 1, 0, 'C');
                    $pdf->Cell(20, 5, $r['data'], 1, 0, 'C');
                    $pdf->Cell(20, 5, $r['numero'], 1, 0, 'C');
                    $pdf->Cell( 8, 5, $r['modelo'], 1, 0, 'C');
                    $pdf->Cell(15, 5, number_format($v, 2, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(10, 5, $k, 1, 0, 'C');
                    $pdf->Cell(15, 5, number_format($r['frig'], 2, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(15, 5, number_format($r['rest'], 2, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(15, 5, number_format($r['fone'], 2, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(15, 5, number_format($r['bar'], 2, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(15, 5, number_format($r['diarias'], 2, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(15, 5, number_format($r['lavan'], 2, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(25, 5, utf8_decode($r['agente']), 1, 0, 'C');

                    $total['total']     += $r['valor'];
                    $total['frig']      += $r['frig'];
                    $total['rest']      += $r['rest'];
                    $total['fone']      += $r['fone'];
                    $total['bar']       += $r['bar'];
                    $total['diarias']   += $r['diarias'];
                    $total['lavan']     += $r['lavan'];
                } else {
                    $pdf->Cell(10, 5, "", 1, 0, 'P');
                    $pdf->Cell(65, 5, "", 1, 0, 'P');
                    $pdf->Cell(5, 5, "", 1, 0, 'C');
                    $pdf->Cell(20, 5, "", 1, 0, 'C');
                    $pdf->Cell(20, 5, "", 1, 0, 'C');
                    $pdf->Cell( 8, 5, "", 1, 0, 'C');
                    $pdf->Cell(15, 5, number_format($v, 2, ',', '.'), 1, 0, 'R');
                    $pdf->Cell(10, 5, $k, 1, 0, 'C');
                    $pdf->Cell(15, 5, "", 1, 0, 'R');
                    $pdf->Cell(15, 5, "", 1, 0, 'R');
                    $pdf->Cell(15, 5, "", 1, 0, 'R');
                    $pdf->Cell(15, 5, "", 1, 0, 'R');
                    $pdf->Cell(15, 5, "", 1, 0, 'R');
                    $pdf->Cell(15, 5, "", 1, 0, 'R');
                    $pdf->Cell(25, 5, utf8_decode($r['agente']), 1, 0, 'C');
                }
                $linha++;
                $i++;
                $pg++;
                $formaPagamento[$k] += $v;
            }
            //exit(var_dump($r['formaPg']));
            
        }
        
        $pdf->Ln(5);
        $pdf->Cell(128, 5, "TOTAL:    ", 1, 0, 'R');
        $pdf->Cell(15, 5, number_format($total['total'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(25, 5, number_format($total['frig'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(15, 5, number_format($total['rest'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(15, 5, number_format($total['fone'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(15, 5, number_format($total['bar'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(15, 5, number_format($total['diarias'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(15, 5, number_format($total['lavan'], 2, ',', '.'), 1, 0, 'R');
        //$pdf->Cell(20,5,$o['classifica'], 1, 0, 'P');
        
        $pdf->Ln(15);
        $pdf->Cell(80, 5, "RESUMO POR DOCUMENTO FINANCEIRO", 1, 0, 'C');
        $pdf->Ln(5);
        $pdf->Cell(25, 5, "DOC FINANCEIRO", 1, 0, 'C');   
        $pdf->Cell(30, 5, utf8_encode("DESCRICAO"), 1, 0, 'C');   
        $pdf->Cell(25, 5, "VALOR", 1, 0, 'C');
        $pdf->Ln(5);
        $totalG = 0;
        //exit(var_dump($formaPagamento));
        foreach ($formaPagamento as $k=>$v) {
            if (!empty($v)) {
                $pdf->Cell(25, 5, $k, 1, 0, 'R');
                switch ($k) {
                    case 'APR': $desc = "A PRAZO"; break;
                    case 'CCC': $desc = "CARTAO CREDITO"; break;
                    case 'CCD': $desc = "CARTAO DEBITO"; break;
                    case 'CHR': $desc = "CHEQUE PRE"; break;
                    case 'DHR': $desc = "DINHEIRO"; break;
                    case 'CHV': $desc = "CHEQUE A VISTA "; break;
                    case 'SMF': $desc = "SEM MOV FINANCEIRO "; break;
                    
                    
                }
                $pdf->Cell(30, 5, $desc, 1, 0, 'L');   
                $pdf->Cell(25, 5, number_format($v, 2, ',', '.'), 1, 0, 'R');
                $pdf->Ln(5);    
                $totalG += $v;
            }
        }
        $pdf->Cell(55, 5, utf8_encode("TOTAL"), 1, 0, 'R');   
        $pdf->Cell(25, 5, number_format($totalG, 2, ',', '.'), 1, 0, 'R');
        
        //$pdf->Ln(15);
        //$pdf->Text(11, 180, $texto_filtro);

        break;
        
    case 'relatorio_espelho':
        //$id_hotel_local = filter_input(INPUT_GET, 'id_hotel_local');
        $dono = filter_input(INPUT_POST, 'dono');

        $imagem = _myfuncoes_logomarca();
        $pdf->SetAuthor('Online Sistemas - Dasio Lacerda');
        $pdf->AddPage();
        //$pdf->Image("$imagem"); //Logomarca EMBRATUR
        $pdf->Image("$imagem", 10, 5, 25, 20, 'JPG'); //Logomarca EMBRATUR
        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(40, 14, $infotitulo['fantasia']);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(40, 21, $infotitulo['fantasia']);
        
        $sql_lancamentos = "SELECT * FROM $TLANCAMENTOS WHERE dono = '$dono'";
        //exit($sql_lancamentos);
        $get_lancamentos = mysqli_query($conn_a, $sql_lancamentos);
        $info_lancamentos = mysqli_fetch_assoc($get_lancamentos);
        $id_cnpjcpf = $info_lancamentos['cnpjcpf'];

        $dados_cliente = _myfun_dados_cnpjcpf($id_cnpjcpf);

        //$existe_id_hotel_local = _myfunc_existe_id_hotel_local($id_hotel_local);

        $pdf->Text(180, 21, 'Data: '.date('d/m/Y'));
        
        $pdf->Text(10, 31, utf8_decode('Hóspede: '.$dados_cliente['razao']));
        $pdf->Text(100, 31, utf8_decode('Endereço: '.$dados_cliente['endereco'].', '.$dados_cliente['num'] ));
        $pdf->Text(10, 35, 'Cidade: '.$dados_cliente['cidade']);
        $lini = 5;
        $pdf->Rect(10, 35 + $lini, 190, 8);
        
        $sql_nfdocs = "SELECT * FROM $TNFDOCUMENTOS WHERE dono = '$dono' ORDER BY datacad DESC LIMIT 1";
        $get_nfdocs = mysqli_query($conn_a, $sql_nfdocs);
        $nfdocs = mysqli_fetch_array($get_nfdocs);
        $pdf->Text(11, 38 + $lini, 'Apartamento: '.$nfdocs['id_hotel_local']);
        
        $pdf->Text(50, 38 + $lini, 'Entrada: '.date('d/m/Y H:i:s', $nfdocs['datacad']));
        $pdf->Text(120, 38 + $lini, 'Check-Out: PD00037914 CTRL: 613161588-60-');
        $pdf->Text(50, 41 + $lini, utf8_decode('Previsão: '.date('d/m/Y', $nfdocs['datasaida'])));
        $pdf->Text(120, 41 + $lini, utf8_decode('Saída: '.date('d/m/Y'). '  Hora: '.date('H:i:s') .'AG: AD' ));
        
        $pdf->Rect(10, 44 + $lini, 190, 5);
        $pdf->Text(11, 47 + $lini, 'Qtde');
        $pdf->Text(25, 47 + $lini, 'Local');
        $pdf->Text(40, 47 + $lini, utf8_decode('Descrição'));
        $pdf->Text(125, 47 + $lini, utf8_decode('Dt Lança'));
        $pdf->Text(145, 47 + $lini, 'Un');
        $pdf->Text(160, 47 + $lini, 'Vr Unit.');
        $pdf->Text(180, 47 + $lini, 'Total');
        
        $sql_item = "SELECT * FROM $TITEM_FLUXO WHERE dono = '$dono' ORDER BY datacad";
        $get_item = mysqli_query($conn_a, $sql_item);
        $qtdeItem = mysqli_num_rows($get_item);
        if ($qtdeItem) {
            $pdf->Rect(10, 50 + $lini, 190, (3.5*$qtdeItem));
            $totalHosp = $totalLav = $totalTel = $totalCom = $desconto = 0;
            $pdf->Ln(46);
            while ($item = mysqli_fetch_array($get_item)) {
                
                $desconto += $item['vdesc'];
                if (empty($item['localoperacao'])) {
                    $sql_produto = "SELECT grupoproduto FROM $TPRODUTOS WHERE conta = '".$item['cprod']."'";
                    //exit($sql_produto);
                    $get_produto = mysqli_query($conn_a, $sql_produto);
                    $qtProd = mysqli_num_rows($get_produto);
                    if ($qtProd) {
                        $res = mysqli_fetch_array($get_produto);
                    } else {
                        $sql_servico = "SELECT grupoproduto FROM $TSERVICOS WHERE conta = '".$item['cprod']."'";
                        //exit($sql_servico);
                        $get_servico = mysqli_query($conn_a, $sql_servico);
                        $res = mysqli_fetch_array($get_servico);
                    }
                    
                    switch ($res['grupoproduto']) {
                        case '090.001':
                            $totalHosp += $item['vprod'];
                            break;
                        case '091.001':
                            $totalLav += $item['vprod'];
                            break;
                        case '092.001':
                            $totalTel += $item['vprod'];
                            break;
                        default:
                            $totalCom += $item['vprod'];
                            break;
                    }
                } else {
                    switch ($item['localoperacao']) {
                        case '01': 
                            $local = 'APTO'; 
                            $totalHosp += $item['vprod'];
                            $diaria = $item['vuncom'];
                            break;
                        case '02': 
                            $local = 'FRI'; 
                            $totalCom += $item['vprod'];
                            break;
                        case '03': 
                            $local = 'FRR'; 
                            $totalCom += $item['vprod'];
                            break;
                        case '04': 
                            $local = 'LAV'; 
                            $totalLav += $item['vprod'];
                            break;
                        case '05': 
                            $local = 'RES'; 
                            $totalCom += $item['vprod'];
                            break;
                        case '06': 
                            $local = 'TEL'; 
                            $totalTel += $item['vprod'];
                            break;
                    }
                }
                
                $pdf->Cell(10, 3, number_format($item['qcom'], 2, ',', ''), 0, 0, 'R');
                
                $pdf->Text(25, 53 + $lini, $local);
                $pdf->Text(40, 53 + $lini, utf8_decode($item['xprod']));
                $pdf->Text(110, 53 + $lini, utf8_decode($item['item_complemento']));
                $pdf->Text(125, 53 + $lini, date('d/m/Y', $item['datacad']));
                $pdf->Text(145, 53 + $lini, $item['ucom']);
                $pdf->Cell(150, 3, number_format($item['vuncom'], 2, ',', ''), 0, 0, 'R');
                $pdf->Cell(20, 3, number_format($item['vprod'], 2, ',', ''), 0, 0, 'R');
                //$pdf->Text(175, 53 + $lini, $item['vprod']);
                
                $lini = $lini + 3;
                $pdf->Ln(3);
            }
        }
        $pdf->Ln(5);
        $pdf->Rect(10, 59 + $lini, 190, (15));
        $pdf->Text(11, 62 + $lini, utf8_decode('Hóspede Pagante:'));
        $pdf->Text(11, 65 + $lini, utf8_decode($dados_cliente['razao']));
        $pdf->Text(11, 72 + $lini, '_____________________________________________________');
        $totalGeral = ($totalHosp + $totalCom + $totalTel + $totalLav);
        
        $pdf->Text(91, 62 + $lini, utf8_decode('Diárias: '));
        $pdf->Ln(3);
        $pdf->Cell(110, 4, number_format($totalHosp, 2, ',', ''), 0, 0, 'R' );
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(20, 4, 'Sub Total: ', 0, 0, 'L');
        $pdf->Cell(10, 4, number_format($totalGeral, 2, ',', ''), 0, 0, 'R' );
        $pdf->Ln(3);
        $pdf->Text(91, 65 + $lini, 'Comandas: ');
        $pdf->Cell(110, 4, number_format($totalCom, 2, ',', ''), 0, 0, 'R' ); 
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(20, 4, 'Desconto: ', 0, 0, 'L');
        $pdf->Cell(10, 4, number_format($desconto, 2, ',', ''), 0, 0, 'R' );
        $pdf->Ln(3);
        $pdf->Text(91, 68 + $lini, utf8_decode('Ligações: '));
        $pdf->Cell(110, 4, number_format($totalTel, 2, ',', ''), 0, 0, 'R' ); 
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(20, 4, '* * * * * * * * * * * * * * * * *', 0, 0, 'L');
        $pdf->Ln(3);
        $pdf->Text(91, 72 + $lini, 'Lavander: ');
        $pdf->Cell(110, 4, number_format($totalLav, 2, ',', ''), 0, 0, 'R' ); 
        $pdf->Cell(20, 4, '', 0, 0, 'L');
        $pdf->Cell(20, 4, 'Total: ', 0, 0, 'L');
        $pdf->Cell(10, 4, number_format($totalGeral - $desconto, 2, ',', ''), 0, 0, 'R' );
        
        $pdf->Ln(15);
        $pdf->Text(10, 80 + $lini, utf8_decode('Obs: '.$nfdocs['notebem']));
        $pdf->Text(80, 85 + $lini, utf8_decode('***    NAO E VALIDO COMO DOCUMENTO FISCAL   ***'));
        //var_dump($dados_cliente);
        //exit('teste dasio');
        break;
    
    case "logs":
        //exit(var_dump($_POST));
        $linha = 30;
        
        $land_port = 'L';
        $por_pagina = 30;
        $nomerel = utf8_decode('RELATÓRIO DE ALTERAÇÕES / REMOÇÕES');
        
        $filtro_periodo = " AND (datacad >= $id_periodo1 AND datacad <= $id_periodo2)";
        
        if (!empty($_POST['xqualclifor'])) {
            $filtro_periodo .= " AND mensagem LIKE '%".$_POST['xqualclifor']."%'";
        }
        
        if (!empty($_POST['xqualusuario'])) {
            $filtro_periodo .= " AND cnpjcpfcad =  '".$_POST['xqualusuario']."'";
        }
        
        if (!empty($_POST['codigo_produto_relat'])) {
            $filtro_periodo .= " AND mensagem LIKE '%".$_POST['codigo_produto_relat']."%'";
        }
        
        if (!empty($_POST['dono'])) {
            $filtro_periodo .= " AND mensagem LIKE '%".$_POST['dono']."%'";
        }
        
        $texto_filtro = $texto_filtro . utf8_decode("Período : ") . _myfunc_stod($id_periodo1) . " a " . _myfunc_stod($id_periodo2);

        $query = "SELECT * FROM $TLOGSMENSAGEM WHERE quescript IN ('REMOVEITEM-LANRECEITAEDITA', 'ATUALIZAITEM-LANRECEITAEDITA', 'REMOVERESERVA-HOTELRECEPCAO', 'TROCOUQUARTO-RECEPCAO', 'REMOVEVENDA-ATENDIMENTO', 'REMOVECONTABIL-LANMOVIMENTOFIEDITA') $filtro_periodo";
        //exit($query);

        function _get_qto($dono, $conn_a, $TNFDOCUMENTOS_TMP) {
            $sqlQto = "SELECT id_hotel_local FROM $TNFDOCUMENTOS_TMP WHERE dono = '$dono'";
            $getQto = mysqli_query($conn_a, $sqlQto);
           
            $qo = mysqli_fetch_assoc($getQto);
            
            return $qo['id_hotel_local'];
        }
        
        
        $result = mysqli_query($conn_a, $query);
        $rel = Array();
        $i = 0;
        while ($logs = mysqli_fetch_assoc($result)) {
            $infoLog = explode(':;', $logs['mensagem']);
            
            $dno = explode('|', $infoLog[0]);
            $cli = explode('|', $infoLog[1]);
            $rti = explode('|', $infoLog[2]);
            
            $dono = array();
            foreach ($dno as $k => $v) {
                $dono[] = $v;
            }
            
            $rotin = array();
            foreach ($rti as $k => $v) {
                $rotin[] = $v;
            }

            
            
            $dados_agente = _myfun_dados_cnpjcpf($logs['cnpjcpfcad']);
            
            $rel[$i]['msg']        = $logs['mensagem'];
            $rel[$i]['datacad']    = $logs['datacad'];
            $rel[$i]['cnpjcpfcad'] = $logs['cnpjcpfcad'];
            $rel[$i]['quescript']  = $logs['quescript'];
            $rel[$i]['ip']         = $logs['ip'];
            $rel[$i]['usuario']    = $dados_agente['razao'];
            $rel[$i]['dono']       = $dono[2];
            $rel[$i]['rotina']     = $rotin[1];
            
            switch ($rotin[1]) {
                case 'REMOVEITEM':
                case 'EDITAITEM':
                case 'TROCOUQUARTO':
                    $cprod  = explode('|', $infoLog[3]);
                    $prod   = explode('|', $infoLog[4]);
                    $qtdeO  = explode('|', $infoLog[5]);
                    $vrO    = explode('|', $infoLog[6]);
                    $qtdeN  = explode('|', $infoLog[7]);
                    $vrN    = explode('|', $infoLog[8]);

                    $rel[$i]['prod']     = $prod[2];
                    $rel[$i]['cprod']    = $cprod[2];
                    $rel[$i]['qtdo']     = $qtdeO[2];
                    $rel[$i]['vro']      = $vrO[2];
                    $rel[$i]['qtdn']     = $qtdeN[2];
                    $rel[$i]['vrn']      = $vrN[2];
                    break;
                case 'REMOVERESERVA':
                case 'REMOVECONTABIL':
                    $reser  = explode('|', $infoLog[2]);
                    $cprod  = explode('|', $infoLog[3]);
                    //exit(var_dump($reser));
                    $rel[$i]['reserv']   = $reser[2];
                    $rel[$i]['Hosped']   = $cprod[2];
                    break;
            }
            
            $i++;
        }
//
        $i = 0;
        foreach ($rel as $r) {

            if ($linha == $por_pagina) {
                include('../pdf/fpdf_cabecalho_wss.php');
                $linha = 1;
                $pdf->SetFont("Arial", "", 7);
                $pdf->Cell(30, 5, "DOCUMENTO", 1, 0, 'C');
                $pdf->Cell(25, 5, "CONTROLE", 1, 0, 'C');
                $pdf->Cell(25, 5, "ROTINA", 1, 0, 'C');
                $pdf->Cell(55, 5, utf8_decode("USUARIO"), 1, 0, 'C');
                $pdf->Cell(30, 5, "DATA", 1, 0, 'C');
                $pdf->Cell(100, 5, "OCORRENCIA", 1, 0, 'C');
            }
            
            $pdf->Ln(5);
            $pdf->Cell(30, 5, $r['dono'], 1, 0, 'P');
            
            
            switch ($r['rotina']) {
                case 'REMOVERESERVA':
                    $msg = "REMOVEU RESERVA DO QUARTO ".$r['reserv'].", ".$r['Hosped'];
                    $infoQto = "Quarto "._get_qto($r['dono'], $conn_a, $TNFDOCUMENTOS_TMP);
                    break;
                case 'REMOVECONTABIL':
                    $msg = "REMOVEU ".$r['reserv'];
                    $infoQto = "";
                    break;
                default :
                    $msg = substr($r['prod'], 0, 20).' ('.$r['cprod'].') Ant.: '.number_format($r['qtdo'], 3, ',', '.').' Atual.: '.number_format($r['qtdn'], 3, ',', '.').' Vr.(a): '.number_format($r['vro'], 2, ',', '.').' Vr.(n): '.number_format($r['vrn'], 2, ',', '.');
                    $infoQto = "Quarto "._get_qto($r['dono'], $conn_a, $TNFDOCUMENTOS_TMP);
                    break;
            }
            
            
            $pdf->Cell(25, 5, $infoQto, 1, 0, 'C');
            $pdf->Cell(25, 5, $r['rotina'], 1, 0, 'C');
            $pdf->Cell(55, 5, $r['usuario'], 1, 0, 'L');
            $pdf->Cell(30, 5, date('d/m/Y H:i:s', $r['datacad']), 1, 0, 'C');
            
            $pdf->Cell(100, 5, $msg, 1, 0, 'P');
            
            //exit('entrou no foreach');
            $linha++;
            $i++;
        }
        
        $pdf->Text(11, 180, $texto_filtro);

        break;
    
    case "relatorio_diario":
        $di = filter_input(INPUT_POST, 'datarel');
        $nomerel = utf8_decode('RELATÓRIO DIÁRIO');

        $pdf = new FPDF('L','mm','A4');
        $imagem = _myfuncoes_logomarca();
        $pdf->SetAuthor('Online Sistemas - Dasio Lacerda');
        $pdf->AddPage();

        $pdf->Image("$imagem", 10, 5, 25, 20, 'JPG');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(40, 14, $infotitulo['fantasia']);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Text(40, 21, $infotitulo['fantasia']);
        $pdf->Text(150, 21, utf8_decode('PERÍODO: '.$di));
        $pdf->Text(40, 26, utf8_encode($_SESSION['ADMIN']['nome_user']).' - '.date('d/m/Y H:i:s'));
        $pdf->Ln(20);
       
        $ps = _myfunc_retorno_parametros_segmento(); // $ps = parametros do sistema

        $hj_I_Query = _myfunc_dtos_0hs($di);
        $hj_F_Query = _myfunc_dtos($di);

        $pdf->SetFont("Arial", "", 5);
        $sqlContasHj = "SELECT id, contad, dono FROM $TLANCAMENTOS WHERE data BETWEEN '$hj_I_Query' AND '$hj_F_Query' ORDER BY contad, contac";
        //exit($sqlContasHj);
        $getContasHj = mysqli_query($conn_a, $sqlContasHj);
        $contasD = $movi = $contasS = $contasE = $contasT = array('docs'=>'Documento', 'hist'=>'Historico');
        while ($ctHJ = mysqli_fetch_assoc($getContasHj)) {
            if (!empty($ctHJ['contad']) && substr($ctHJ['contad'], 0, 1) == '1') {
                if (!in_array($ctHJ['contad'], $contasD) && substr($ctHJ['dono'], 0, 5) <> 'COMPP') {
                    $sqlDescPlanoCT = "SELECT descricao FROM $TPLANOCT WHERE conta = '".$ctHJ['contad']."'";
                    $getDescPlanoCT = mysqli_query($conn_a, $sqlDescPlanoCT);
                    $descPlanoCT = mysqli_fetch_assoc($getDescPlanoCT);
                    
                    $contasD[$ctHJ['contad']] = $descPlanoCT['descricao'];
                    $contasS[$ctHJ['contad']] = 0;
                    $contasE[$ctHJ['contad']] = 0;
                    $contasT[$ctHJ['contad']] = 0;
                    $movi[$ctHJ['contad']] = array();
                }
            }
        }
        
        if (isset($ps['conta']) && array_key_exists(trim($ps['conta']), $contasD)) {
            $saldoDia = _valor_contabalanco($di, $di, trim($ps['conta']));//array('1.1.01'=>'5122.69|82.00|0.00|0.00');//
        } else {
            $saldoDia = _valor_contabalanco($di, $di);//array('1.1.01'=>'5122.69|82.00|0.00|0.00');//
        }

        foreach ($contasD as $k=>$v) {
            $tam = 23;
            if ($k == 'docs') {
                $tam = 20;
            }
            if ($k == 'hist') {
                $tam = 60;
            }
            $pdf->Cell($tam, 5, utf8_encode(strtoupper($v)), 1, 0, 'C');
        }
        $totalContasD = count($contasD);
        
        //if ($totalContasD > 2) {
            $sqlLancamentosHj = "SELECT id, cnpjcpf, dono, donoanterior, data, contad, contac, valor, movimento, tipodocfinanceiro, documento, historico FROM $TLANCAMENTOS WHERE tipodocfinanceiro IS NOT NULL AND data BETWEEN '$hj_I_Query' AND '$hj_F_Query' ORDER BY id";
            $getLancamentosHj = mysqli_query($conn_a, $sqlLancamentosHj);
            $linha = 0;
            
            $movi['docs'] = $movi['hist'] = array();
            while ($lh = mysqli_fetch_assoc($getLancamentosHj)) {
                $histLan = '';
                //echo $lh['dono'].' - '.$lh['donoanterior'].'<br>';
                if (substr($lh['dono'], 0, 3) == 'LAN' || (substr($lh['dono'], 0, 4) == 'COMR' && empty($lh['donoanterior'])  || (substr($lh['dono'], 0, 5) == 'COMPP' && empty($lh['donoanterior']) ) ) ) {
                    if ( (!empty($lh['contad']) && substr($lh['contad'], 0, 1) == '1') || (!empty($lh['contac']) && substr($lh['contac'], 0, 1) == '1') ) {
                        $dados_cliente = _myfun_dados_cnpjcpf($lh['cnpjcpf']);
                        if (!empty($lh['historico'])) {
                            $histLan = $lh['historico'].' - ';
                        }
                        $movi['docs'][$linha] = $lh['documento'];
                        $movi['hist'][$linha] = $histLan.$dados_cliente['razao'];
                        if (!empty($lh['contad']) && substr($lh['contad'], 0, 1) == '1') {
                            $movi[$lh['contad']][$linha]  = $lh['valor'];
                            $contasE[$lh['contad']] += $lh['valor'];
                            $contasT[$lh['contad']] += $lh['valor'];
                        }

                        if (!empty($lh['contac']) && substr($lh['contac'], 0, 1) == '1') {
                            $movi[$lh['contac']][$linha]  = number_format(($lh['valor'] * -1), 2, '.', '');
                            if (array_key_exists($lh['contac'], $contasD)) {
                                $contasS[$lh['contac']] += $lh['valor'];
                                $contasT[$lh['contac']] += ($lh['valor'] * -1);
                            }
                        }

                        $linha++;
                    }
                }
    //                if (isset($ps['diario']) && trim($ps['diario']) == 'Dinheiro') {
    //                    //echo ' Entrou em existe diario e diario = dinheiro - ';
    //                    if ($lh['tipodocfinanceiro'] == 'DHR') {
    //                        //echo ' entrou - dinehrio';
    //                        $recebH += $lh['valor'];
    //                    }
    //                } else {
    //                    //echo ' não entrou em existe diario e diario = Dinheiro -';
    //                    $recebH += $lh['valor'];
    //                }
            }

            for ($a = 0;$a < $linha;$a++) { // pra cada linha existente 
                $pdf->Ln(5); // volta a linha 
                foreach ($contasD as $k4=>$v4) { // pega a chave do array
                    $tam = 23;
                    $valor = '';
                    $alin  = 'C';
                    if (!empty($movi[$k4][$a])) {
                        $valor = number_format($movi[$k4][$a], 2, ',', '.');
                    }

                    if ($k4 == 'docs') {
                        $tam = 20;
                        $valor = $movi[$k4][$a];
                        $alin  = 'L';
                    }

                    if ($k4 == 'hist') {
                        $tam = 60;
                        $valor = $movi[$k4][$a];
                        $alin  = 'L';
                    }
                    //$pdf->MultiCell($tam, 10, $valor, 0, $alin);
                    $pdf->Cell($tam, 5, $valor, 1, 0, $alin); //escreve o valor e escreve
                }

            }

            $pdf->Ln(5);
            foreach ($contasD as $k=>$v) {
                $tam = 23;
                if ($k == 'docs') {
                    $tam = 20;
                    $v = '';
                }
                if ($k == 'hist') {
                    $tam = 60;
                    $v = '';
                }
                $pdf->Cell($tam, 5, utf8_encode(strtoupper($v)), 1, 0, 'C');
            }

            $pdf->Ln(5);
            foreach ($contasE as $k=>$v) {
                $tam = 23;
                $valor = '';
                $alin = 'C';
                if ($k == 'docs') {
                    $tam = 20;
                    $v = '';
                }
                if ($k == 'hist') {
                    $tam = 60;
                    $v = '';
                    $valor = 'ENTRADAS: ';
                    $alin = 'R';
                }

                if (!empty($v)) {
                    $valor = number_format($v, 2, ',', '.');
                }

                $pdf->Cell($tam, 5, $valor, 1, 0, $alin);
            }

            $pdf->Ln(5);
            foreach ($contasS as $k=>$v) {
                $tam = 23;
                $valor = '';
                $alin = 'C';
                if ($k == 'docs') {
                    $tam = 20;
                    $v = '';
                }
                if ($k == 'hist') {
                    $tam = 60;
                    $v = '';
                    $valor = utf8_decode('SAÍDAS: ');
                    $alin = 'R';
                }

                if (!empty($v)) {
                    $valor = number_format(($v * -1), 2, ',', '.');
                }

                $pdf->Cell($tam, 5, $valor, 1, 0, $alin);
            }

            $pdf->Ln(5);
            foreach ($contasT as $k=>$v) {
                $tam = 23;
                $valor = '';
                $alin = 'C';
                if ($k == 'docs') {
                    $tam = 20;
                    $v = '';
                }
                if ($k == 'hist') {
                    $tam = 60;
                    $v = '';
                    $valor = utf8_decode('TOTAL: ');
                    $alin = 'R';
                }

                if (!empty($v)) {
                    $valor = number_format($v, 2, ',', '.');
                }

                $pdf->Cell($tam, 5, $valor, 1, 0, $alin);
            }

            $pdf->Ln(15);
            foreach ($saldoDia as $k=>$v) {
                if (array_key_exists($k, $contasD)) {
                    $pdf->Cell(35, 5, strtoupper($contasD[$k]), 1, 0, 'C');
                }
            }

            $pdf->Ln(5);
            foreach ($saldoDia as $k=>$v) {
                if (array_key_exists($k, $contasD)) {
                    $saldoA = explode('|', $saldoDia[$k]);
                    $pdf->Cell(20, 5, "SALDO INICIAL", 1, 0, 'C');
                    $pdf->Cell(15, 5, number_format($saldoA[0], 2,',','.'), 1, 0, 'C');
                }
            }

            $pdf->Ln(5);
            foreach ($saldoDia as $k=>$v) {
                if (array_key_exists($k, $contasE)) {
                    $pdf->Cell(20, 5, "+ ENTRADAS", 1, 0, 'C');
                    $pdf->Cell(15, 5, number_format($contasE[$k], 2,',','.'), 1, 0, 'C');
                }
            }

            $pdf->Ln(5);
            foreach ($saldoDia as $k=>$v) {
                if (array_key_exists($k, $contasS)) {
                    $pdf->Cell(20, 5, "- SAIDAS", 1, 0, 'C');
                    $pdf->Cell(15, 5, number_format($contasS[$k], 2,',','.'), 1, 0, 'C');
                }
            }

            $pdf->Ln(5);
            foreach ($saldoDia as $k=>$v) {
                if (array_key_exists($k, $contasD)) {
                    $saldoB = explode('|', $saldoDia[$k]);
                    $pdf->Cell(20, 5, "SALDO FINAL", 1, 0, 'C');
                    $pdf->Cell(15, 5, number_format(($saldoA[0] + $contasE[$k]) - $contasS[$k], 2,',','.'), 1, 0, 'C');
                }
            }
        
        break;
}

$pdf->Output("$end_final", "$tipo_pdf");
echo "PDF Criado: $end_final";
unset($_SESSION['RELAT']);
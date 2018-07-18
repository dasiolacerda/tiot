<?php
    //exit('sfiuashfdsiauhfasiufdsa');

    session_start();      
    $cnpjcpf_segmento = $info_cnpj_segmento = $_SESSION['ADMIN']['cnpj_segmento']; 
    $info_segmento =  $_SESSION['ADMIN']['cnpj_segmento']; 
    //exit(var_dump($_SESSION['ADMIN']));
    $tipo = filter_input(INPUT_GET, 'tipo');  
    $mesa = filter_input(INPUT_GET, 'param');  
        
    require_once('../includes/myfuncoes.php');
    require_once('../_conecta.php');
    require_once('../_tabelas.php');  
    require_once('../_metodos/basico.php');
    
    switch ($tipo) {
        case 'bar_taxaservico':
            
            $sqlVoutro = "SELECT voutronf from $TNFDOCUMENTOS_TMP WHERE id_hotel_local = '$mesa'";
            $getVoutro = mysqli_query($conn_a, $sqlVoutro);
            $taxa = mysqli_fetch_assoc($getVoutro);
            if ($taxa['voutronf'] == '0.00') {
                $sqlNfdocsTmp = "SELECT produtosnf FROM $TNFDOCUMENTOS_TMP WHERE id_hotel_local = '$mesa'";
                $getNfdocsTmp = mysqli_query($conn_a, $sqlNfdocsTmp);
                $produtosnf = mysqli_fetch_assoc($getNfdocsTmp);
                $taxa = $produtosnf['produtosnf']*0.1;
            } else {
                $taxa = '0.00';
            }
            
            $sqlUpdateNfdocsTmp = "UPDATE $TNFDOCUMENTOS_TMP SET voutronf = '".$taxa."' WHERE id_hotel_local = '$mesa'";
            $updateNfdocsTmp = mysqli_query($conn_a, $sqlUpdateNfdocsTmp);
            if ($updateNfdocsTmp) {
                echo "Taxa de Servi&ccedil;o atualizada com sucesso! Valor da taxa R$: ".number_format($taxa, 2, ',', '.');
            } else {
                echo "Falha ao atualizar taxa de servi&ccedil;o! Valor da taxa R$: ".number_format($taxa, 2, ',', '.');
            }
            
            break;
        case 'transferenciaQuarto':
            
            $produtos    = filter_input(INPUT_POST, 'prods');
            $nItens      = filter_input(INPUT_POST, 'qtItens');
            $p = substr($produtos, 0, -1);
            $arProd = explode(',', $p);
            $qtdProd = count($arProd);
            
            $quartoAtual = filter_input(INPUT_POST, 'qtoAtual');
            $qtoAntigo   = filter_input(INPUT_POST, 'qtoAntigo');
            $qtoNovo     = filter_input(INPUT_POST, 'qtoNovo');
            if ($nItens == $qtdProd) {
                $sql_UpdateNfDocs =  "UPDATE $TNFDOCUMENTOS_TMP SET id_hotel_local='$qtoNovo' WHERE dono = '$qtoAntigo'";
                if (mysqli_query($conn_a, $sql_UpdateNfDocs)) {
                    //mysqli_query($conn_a, "UPDATE $TAPARTAMENTOS SET dataatu='$dtos', cnpjcpfatu='$info_cnpj', status='L' WHERE id_hotel_local = '$quartoAtual'");
                    echo "true#logado.php?ac=bar_recepcao";
                } else {
                    echo "false#Problemas ao transferir quarto, por favor entre em contato com o suporte!";
                } 
            } else {
                
                $prod = '';
                if (!empty($produtos)) {
                    $prod = " AND id IN (".$p.")";
                }
                
                $sqlDono = "SELECT dono FROM $TNFDOCUMENTOS_TMP WHERE id_hotel_local = '$quartoAtual'";
                $getDono = mysqli_query($conn_a, $sqlDono);
                $dno = mysqli_fetch_assoc($getDono);
                $novoDono = BASICO::_numero_aleatorio('LAN');
            
                $sqlInsertNfdocsTmp = "INSERT INTO $TNFDOCUMENTOS_TMP (dono, data, datacad, cnpjcpfcad, cnpjcpfseg, modfrete, id_hotel_local, inscricao_e) VALUES ('$novoDono', '".time()."', '".time()."', '".$_SESSION['ADMIN']['cnpj']."', '$cnpjcpf_segmento', '9', '$qtoNovo', 'INSENTO')";
                $insertNfdocsTmp = mysqli_query($conn_a, $sqlInsertNfdocsTmp);
                $sql_itens = "UPDATE $TITEM_FLUXO_TMP SET dono='".$novoDono."' WHERE dono = '".$dno['dono']."' $prod";
                if (mysqli_query($conn_a, $sql_itens)) {
                    
                    $chklinknovo = _myfuncoes_chklink('ID', $novoDono . 'RECEITAS');

                    echo "true#logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink=$chklinknovo&codnat=HOTE&dono=$novoDono";
                } else {
                    exit('false#Falha ao mudar cliente de mesa!'.mysqli_error($conn_a));
                }
                
                //exit("Numero de produtos <> do numero de itens da mesa! novo lan" .BASICO::_numero_aleatorio('LAN'));
            }    
            
            break;
    }
    
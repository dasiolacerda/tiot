 

<script src="../code/jquery.js"></script>
<script src="../code/jquery-1.12.4.js"></script> 
<script src="../code/jquery.min.js"></script> 
<link href="../napi3/BD_API3.css" rel="stylesheet">  

<?php

    $ps = _myfunc_retorno_parametros_segmento(); // $ps = parametros do sistema
    $pu = _myfunc_retorno_parametros_usuario(); // $pu = parametros do usuario
    
    //echo var_dump($pu);
    
    if (isset($_SESSION['ItemRemov']['itemFlux']) && !empty($_SESSION['ItemRemov']['itemFlux'])) {
        $getItem = mysqli_query($conn_a, "SELECT id FROM $TITEM_FLUXO_TMP WHERE id = ".$_SESSION['ItemRemov']['itemFlux']);
        if (mysqli_num_rows($getItem) == 0) {
            if (mysqli_query($conn_a, $_SESSION['ItemRemov']['sql'])) {
                unset($_SESSION['ItemRemov']);
            }
        }
    }
    
    if (isset($_SESSION['ContabilRemov']['Lcontabil']) && !empty($_SESSION['ContabilRemov']['Lcontabil'])) {
        $getLanca = mysqli_query($conn_a, "SELECT id FROM $TLANCAMENTOS WHERE id = ".$_SESSION['ContabilRemov']['Lcontabil']);
        if (mysqli_num_rows($getLanca) == 0) {
            if (mysqli_query($conn_a, $_SESSION['ContabilRemov']['sql'])) {
                unset($_SESSION['ContabilRemov']);
            }
        }
    }
    
    if (isset($ps['hotel']) && trim($ps['hotel']) == 'S') {
        if (isset($_SESSION['donReser']) && !empty($_SESSION['donReser'])) {
            $sqlNoteBem = "SELECT notebem, dono FROM $TNFDOCUMENTOS_TMP WHERE dono = '".$_SESSION['donReser']."'";
            $getNoteBem = mysqli_query($conn_a, $sqlNoteBem);
            $note = mysqli_fetch_array($getNoteBem);
            if (empty($note['notebem'])) {
                $sqlUpdate = "UPDATE $TNFDOCUMENTOS_TMP SET notebem = '".$_SESSION['obsReser']."' WHERE dono = '".$_SESSION['donReser']."'";
                $updateNfDocs = mysqli_query($conn_a, $sqlUpdate) or exit(mysqli_error($conn_a));
            } 
        }

        unset($_SESSION['obsReser']);
        unset($_SESSION['donReser']);

        mysqli_query($conn_a, "UPDATE $TNFDOCUMENTOS_TMP SET id_hotel_local = '" . $_SESSION['qtoInfo'] . "' WHERE dono = '".$_SESSION['qtoDono']."'");
        unset($_SESSION['qtoDono']);
        unset($_SESSION['qtoInfo']);
    }
    
switch ($inc) {
    
    case '../hotel/hotel_recepcao.php':
        //exit('chegou aqui');
        
        if (isset($ps['bar']) && trim($ps['bar']) == 'S') {
            echo '<script>window.location = "logado.php?ac=bar_recepcao";</script>';
        }
        
        break;
        
    case 'lan_receitas_edita.php':
        session_start();
        $dono_operacao = $_GET['dono'];

        echo '<div>';
        
        if (isset($ps['hotel']) && trim($ps['hotel']) == 'S') {
            
            $sqlQtoHotel = "SELECT id_hotel_local as qto FROM $TNFDOCUMENTOS_TMP WHERE dono = '" . $dono_operacao . "'";
            //echo $sqlQtoHotel;
            $getQtoHotel = mysqli_query($conn_a, $sqlQtoHotel);
            $qto = mysqli_fetch_array($getQtoHotel);
            if ($qto['qto']) {
                $_SESSION['qtoInfo'] = $qto['qto'];
                $_SESSION['qtoDono'] = $dono_operacao;
            }
            
            //echo var_dump($_SESSION);
            if (isset($_GET['res']) && !empty($_GET['res'])) {
                $_SESSION['obsReser'] = $_GET['obs'];
                $_SESSION['ApagaREs'] = $_GET['res'];
            }
            
            if (isset($_POST) && !empty($_POST)) {
                if (isset($_SESSION['obsReser']) && !empty($_SESSION['obsReser'])) {
                    $_SESSION['donReser'] = $dono_operacao;
                } 
                
                $deleteReserv = mysqli_query($conn_a, "DELETE FROM $TAPARTAMENTOS_RESERVAS WHERE id = '".$_SESSION['ApagaREs']."'") or exit(mysqli_error($conn_a));
                unset($_SESSION['ApagaREs']);
            }
        }
        
        if (isset($_POST['del_chk'])) {
            foreach ($_POST['del_chk'] as $k=>$v) {
                $sqlItemFluxo = "SELECT cprod, xprod, qcom, vuncom FROM $TITEM_FLUXO_TMP WHERE id = '".$v."' ORDER BY id DESC";
                $getItemFluxo = mysqli_query($conn_a, $sqlItemFluxo);
                $itemsR = mysqli_fetch_array($getItemFluxo);
                
                $msg = "|REMOVEITEM|".$v."|:;|CPROD|".$itemsR['cprod']."|:;|XPROD|".$itemsR['xprod']."|:;|QTDO|".$itemsR['qcom']."|:;|VRO|".$itemsR['vuncom']."|:;|QTDN|0|:;|VRN|0.00|:;";
                $sqlItemLog = "INSERT INTO $TLOGSMENSAGEM (mensagem, datacad, cnpjcpfcad, cnpjcpfseg, quescript, dataatu, ip) VALUES ('|DONO|".$_POST['dono']."|:;|CNPJCPF|".$_POST['cnpjcpf']."|:;".$msg."', '".time()."', '".$_SESSION['ADMIN']['cnpj']."', '".$_SESSION['ADMIN']['cnpj_segmento']."', 'REMOVEITEM-LANRECEITAEDITA', '".time()."', '".$_SERVER["REMOTE_ADDR"]."');";
                                
                $_SESSION['ItemRemov']['itemFlux'] = $v;
                $_SESSION['ItemRemov']['sql'] = $sqlItemLog;
            }
        }
        
        // atualizar itens
        if (isset($_POST['btacoes']) && $_POST['btacoes'] == ' (=) Atualizar ') {
            echo var_dump($_POST).'<BR><BR>';
            //echo $_SESSION['PERMSHotel'];
            if ($_SESSION['ItemEdit']['itemFlux'] == $_POST['num_id_item']) {
                
                $sqlItemFluxo = "SELECT cprod, xprod, qcom, conta_plano FROM $TITEM_FLUXO_TMP WHERE id = '".$_POST['num_id_item']."' ORDER BY id DESC";
                $getItemFluxo = mysqli_query($conn_a, $sqlItemFluxo);
                $itemsR = mysqli_fetch_array($getItemFluxo);
                
                $sqlProduto = "SELECT valor FROM $TPRODUTOS WHERE conta = '".$itemsR['cprod']."'";
                $getProduto = mysqli_query($conn_a, $sqlProduto);
                $pr = mysqli_fetch_array($getProduto);

                if ($_POST['valor_item'] > $pr['valor']) {
                    $sqlNatureOpIt = "SELECT codnat FROM $TNATUREZAOPERACAO WHERE contaprodutos = '".$itemsR['conta_plano']."'";
                    //echo $sqlNatureOpIt.'<br><br>';
                    $getNatureOpIt = mysqli_query($conn_a, $sqlNatureOpIt);
                    $naturezaIt = mysqli_fetch_assoc($getNatureOpIt);
                    if ($naturezaIt['codnat'] <> '' && !eregi(':500.105', $_SESSION['PERMSHotel'])) {
                        ?>
                            <script type="text/javascript"> 
                                alert("Erro ao tentar editar o valor dos produtos!");
                                location.href = '<?php echo $_SERVER['HTTP_REFERER']?>';
                            </script>       
                        <?php
                        exit;
                    }
                }
                
                if ($teste == 'dasio') {
                    //500.105 // servicos
                    //500.106 // produtos
                }
                
                if ($_POST['qtde_item'] < $itemsR['qcom'] || $_POST['qtde_item'] > $itemsR['qcom']) { // verifica se a qtde enviada pra editar é menor que a qtde no banco
                    //|alterarServicos,S
                    
                    $sqlNatureOpIt = "SELECT codnat FROM $TNATUREZAOPERACAO WHERE contaservicos = '".$itemsR['conta_plano']."'";
                    //echo $sqlNatureOpIt.'<br><br>';
                    $getNatureOpIt = mysqli_query($conn_a, $sqlNatureOpIt);
                    $naturezaIt = mysqli_fetch_assoc($getNatureOpIt);
                    //exit;
                    if ($naturezaIt['codnat'] <> '') {
                        //echo "tem Natureza de serviço!.<br><br>";
                        if (!isset($pu['alterarServicos']) || trim($pu['alterarServicos']) != 'S') {
                            ?>
                                <script type="text/javascript"> 
                                    alert("Erro ao tentar editar o numero de diarias!");
                                    location.href = '<?php echo $_SERVER['HTTP_REFERER']?>';
                                </script>       
                            <?php
                            exit;
                        }
                    }
                } 
                
                $msg = "|EDITAITEM|".$_POST['num_id_item']."|:;|CPROD|".$itemsR['cprod']."|:;|XPROD|".$itemsR['xprod']."|:;|QTDO|".$_SESSION['ItemEdit']['qcom']."|:;|VRO|".$_SESSION['ItemEdit']['vuncom']."|:;|QTDN|".$_POST['qtde_item']."|:;|VRN|";
                $val = $_POST['valor_item']."|:;";
                
                if ($_POST['valor_item'] < $pr['valor'] && !eregi(':500.104', $_SESSION['PERMSHotel'])) {
                    $val = $pr['valor']."|:;";
                }
                
                $sqlEditaItemLog = "INSERT INTO $TLOGSMENSAGEM (mensagem, datacad, cnpjcpfcad, cnpjcpfseg, quescript, dataatu, ip) VALUES ('|DONO|".$_POST['dono']."|:;|CNPJCPF|".$_POST['cnpjcpf']."|:;".$msg.$val."', '".time()."', '".$_SESSION['ADMIN']['cnpj']."', '".$_SESSION['ADMIN']['cnpj_segmento']."', 'ATUALIZAITEM-LANRECEITAEDITA', '".time()."', '".$_SERVER["REMOTE_ADDR"]."');";
                mysqli_query($conn_a, $sqlEditaItemLog);
                unset($_SESSION['ItemEdit']);
            }
            //echo var_dump($_SESSION).'<BR><BR>';
            
        }
        
        if (isset($_GET['upitem'])) {
            $sqlItemFluxo = "SELECT qcom, vuncom, vprod FROM $TITEM_FLUXO_TMP WHERE id = '".$_GET['upitem']."' ORDER BY id DESC";
            //echo '<br><br>'.$sqlItemFluxo;
            $getItemFluxo = mysqli_query($conn_a, $sqlItemFluxo);
            $itemsR = mysqli_fetch_array($getItemFluxo);
            
            if (!isset($_SESSION['ItemEdit'])) {
                $_SESSION['ItemEdit'] = array();
            }
            
            $_SESSION['ItemEdit']['itemFlux'] = $_GET['upitem'];
            $_SESSION['ItemEdit']['qcom'] = $itemsR['qcom'];
            $_SESSION['ItemEdit']['vuncom'] = $itemsR['vuncom'];
            $_SESSION['ItemEdit']['vprod'] = $itemsR['vprod'];
        }
        

        if (isset($ps['logs']) && trim($ps['logs']) == 'S') {
            echo '<button title="Logs de Acesso" class="button" id="logsAcesso">Logs de Acesso</button>&nbsp;';
        }
        if (isset($ps['localOperacao']) && trim($ps['localOperacao']) == 'S') {
            echo '<button title="Alterar Local de Opera&ccedil;&atilde;o" class="button" onclick="_local_operacao_modal(\''.$dono_operacao.'\')">Local de Opera&ccedil;&atilde;o</button>&nbsp;';
        }
        if (isset($ps['redocs']) && trim($ps['redocs']) == 'S') {
            echo '<button title="REDOCS" class="button" id="REDOCS" abbr="'.$_GET['dono'].'">RE-DOCS</button>&nbsp;';
        }            
            
        echo '</div>

        <div id="id_div_local_operacao" > 
        </div>

        <div id="id_div_local_operacao_modal" class="modal" style="" left: 50px; top: 0px;">
            <div id="id_div_local_operacao_modal_dados">

            </div>

        </div>';
        break;
        
    case 'lan_movimento_financeiro_edita.php':
        if (isset($_POST['del_chk_contabil'])) {
            $remover = array_unique($_POST['del_chk_contabil']);
            $tip = explode(' - ', $_POST['tiponatureza']);            
            
            //echo $tip[1].'<br>';
            //echo(var_dump($_POST));
            
            foreach ($remover as $k=>$v) {
                $sqlLancamentos = "SELECT * FROM $TLANCAMENTOS WHERE id = '".$v."' ORDER BY id DESC";
                $getLancamentos = mysqli_query($conn_a, $sqlLancamentos);
                $Lanca = mysqli_fetch_array($getLancamentos);
                
                $msg = "|REMOVECONTABIL|".trim($tip[1])." ".$_POST['nome'].", ". number_format($Lanca['valor'], 2, ',', '.')."|:;";
                $sqlRemovContanil = "INSERT INTO $TLOGSMENSAGEM (mensagem, datacad, cnpjcpfcad, cnpjcpfseg, quescript, dataatu, ip) VALUES ('|DONO|".$Lanca['dono']."|:;|CNPJCPF||:;".$msg."', '".time()."', '".$_SESSION['ADMIN']['cnpj']."', '".$_SESSION['ADMIN']['cnpj_segmento']."', 'REMOVECONTABIL-LANMOVIMENTOFIEDITA', '".time()."', '".$_SERVER["REMOTE_ADDR"]."');";
                                
                $_SESSION['ContabilRemov']['Lcontabil'] = $v;
                $_SESSION['ContabilRemov']['sql'] = $sqlRemovContanil;
            }
            //echo 'teste<br><br>';
            //exit(var_dump($_SESSION));
            
            //echo 'teste<br><br>';
        }
        
        break;

    case 'lan_movimento_financeiro_browse.php':
        ?>
        Data:  
        <input id="id_datalancamento" value="<?= $dthoje; ?>" type="text"  onblur="formataData(this);">
        <br>
        Valor <input value='10.00' type=text id='id_valor'>
        <br>
        C. Debito <input type=text id='id_cdebito'>
        <br>
        C. Credito <input type=text id='id_ccredito'>

        <br>
        Historico <input type=text value='obs historico' id='id_historico'>


        <button title='Exemplo lançamento D/C' class='button' onclick="_lancamento_dc()";>D/C</button>

        <div id='id_div_lancamento_dc'>

        </div>
        <?php
        break;
    
    case 'cnpjcpf_segmento.php':
        ?>
            <div>
                <!-- button title='Informar local de operação' class='button' onclick="_local_operacao('<?= $dono_operacao; ?>')">L.O</button --> 
                <button title='Logs de Acesso' class='button' id="logsAcesso">Logs de Acesso</button>

            </div>

            <div id='id_div_logs' > 
            </div>

            <div id='id_div_logs_modal' class='modal' style=' left: 50px; top: 150px;'>
                <div id='id_div_logs_modal_dados'>

                </div>

            </div>
        <?php
        break;

    case '../api3/api3_saida_browse_posVenda.php':
        echo '
            <div id="id_posVenda" > 
            
            </div>

            <div id="id_posVenda_modal" class="modal" style="" left: 50px; top: 0px;">
                <div id="id_posVenda_modal_dados">
                
                </div>
            </div>
        ';
        break;
    
    
    // aki vou inserir o modal ja pronto sem informações, pra quando chamar o webservice ele colocar as info e mostar modal,
    case '../api3/api3_saida_browse_mdevolucoes.php':
    case 'produtos_cadastro.php':
    case 'produtos_relatorios.php':
    case 'jobs_browse.php':
        //echo(var_dump($_POST));
        $btao = '';
        if ($inc == 'produtos_cadastro.php' && trim($ps['calcados']) == 'S' && !isset($_GET['idb'])) {
            $m_grupoproduto = MATRIZ::matriz_grupoproduto('');
            $chk_grupoproduto[$xgrupoproduto] = 'selected';
            $option = '<option style="width: 150px;" value=""></option>';
            foreach ($m_grupoproduto as $k => $v) {
                $option .= '<option value="'.$k.'" '.$chk_grupoproduto[$k].'>'.($v).'</option>';
            }
            $comb = '<select name="grupoproduto" id="grupoproduto" class="form-control campo8">'.$option.'</select>';

            if ($infotitulo['crt'] == '1') {  // OPTANTE PELO SIMPLES
                $m_cst_csosn = MATRIZ::m_csosn();
                $desc_cst_csosn = 'CSOSN:*';
            } else {
                $m_cst_csosn = MATRIZ::m_cst_icms();
                $desc_cst_csosn = 'CST:*';
            }

            $m_cst_csosn[""] = " ";
            $chk_cst_csosn[''] = 'selected';

            $opt2 = '<option value=""></option>';
            foreach ($m_cst_csosn as $k => $v) {
                $opt2 .= '<option value="'.$k.'" '.$chk_cst_csosn[$k].'>'.$k.'  '.($v).'</option>';
            }

            $comb2 = '<select name="xcst_csosn" id="xcst_csosn" class="form-control campo7">'.$opt2.'</select>';

            $m_matriz_unidade = MATRIZ::matriz_unidade_fator();
            $opt3 = '';
            foreach ($m_matriz_unidade as $k => $v) {
                $opt3 .= '<option value="'.$k.'" >'. $v.'</option>';
            }
            $comb3 = '<select name="unidade" id="unidade" class="form-control campo12">'.$opt3.'</select>';

            $nums = '';
            for ($a = 33;$a < 45; $a++) {
                $nums .= '<div style="width: 40px; float: left; margin-left: 5px;"><label for="num'.$a.'" style="background-color: #00FFFF">'.$a.'</label><br><input type="checkbox" name="numeroCalcado[]" id="num'.$a.'" value="'.$a.'" class="numbs"></div>';
            }

            $btao = '
                <div style=" left: 50px; top: 0px; width: 600px;">
                    <fieldset>
                        <legend>Adicionar Cal&ccedil;ados</legend>
                        <div style="margin-left: 10px; margin-right: 10px;">

                            <div style="width: 268px; float: left">
                                <label for="conta" style="font-size: 14px;">C&oacute;digo:</label>
                                <input type="text" name="conta" id="conta" readonly class="form-control campo1">
                            </div>
                            <div style="width: 268px; float: left; margin-left: 10px;">
                                <label for="referencia"  style="font-size: 14px;">Refer&ecirc;ncia:</label>
                                <input type="text" name="referencia" id="referencia" class="form-control campo2">
                            </div>
                            <div style="clear: both"></div>
                            <div style="width: 548px; float: left;">
                                <label for="descricao" style="font-size: 14px;">Descri&ccedil;&atilde;o</label>
                                <input type="text" name="descricao" id="descricao" class="form-control campo3">
                            </div>
                            <div style="width: 548px; float: left;">
                                <label for="fabricante" style="font-size: 14px;">Fabricante:</label>
                                <input type="text" name="fabricante" id="fabricante" class="form-control campo4">
                            </div>
                            <form id="caldados" >
                            <div>
                                <label for="ncm" style="font-size: 14px;"><a title="Click e veja a lista de NCM" href="logado.php?ac=ncm_consulta&campo=ncm&campo2=ncmdescricao&site=@ CONSULTA NCM" onclick="NewWindow(this.href, \'name\', \'450\', \'550\', \'yes\');return false;">NCM: </a><span>*</span>: </label>
                                <div style="margin-botton: 10px;">
                                    <input id="ncm" name="ncm" style="width: 80px; font: normal 8x verdana; float: left;" type="text" READONLY=YES  maxlength="8" class="form-control campo5">
                                    <input id="ncmdescricao" name="ncmdescricao" style="width: 458px; float: left; margin-left: 10px;" type="text" READONLY=YES maxlength="40" class="form-control campo6">
                                </div>
                            </div>
                            <div>
                                <label for="cest" style="font-size: 14px;"><a title="Click e veja a lista de CEST" href="logado.php?ac=cest_consulta&amp;campo=cest&amp;campo2=cestdescricao&amp;site=@ CONSULTA CEST" onclick="NewWindow(this.href,\'name\',\'450\',\'550\',\'yes\');return false;"><span>CEST*</span>:</a></label>
                                <div style="margin-botton: 10px;">
                                    <input id="cest" name="cest" style="width: 80px; font: normal 8x verdana; float: left;" type="text" READONLY=YES  maxlength="8" class="form-control campo10">
                                    <input id="cestdescricao" name="cestdescricao" style="width: 458px; float: left; margin-left: 10px;" type="text" READONLY=YES maxlength="90" class="form-control campo11">
                                </div>
                            </div>
                            </form>
                            <div style="width: 268px; float: left;">
                                <label for="xcst_csosn" style="font-size: 14px;">'.$desc_cst_csosn.':</label>
                                '.$comb2.'
                            </div>
                            <div style="width: 268px; float: left; margin-left: 10px;">
                                <label for="grupoproduto" style="font-size: 14px;">Grupo:</label>
                                '.$comb.'
                            </div>
                            <div style="clear: both"></div>
                            <div style="width: 268px; float: left;">
                                <label for="unidade" style="font-size: 14px;">Unidade:</label>
                                '.$comb3.'
                            </div>
                            <div style="width: 268px; float: left; margin-left: 10px;">
                                <label for="preco" style="font-size: 14px;">Pre&ccedil;o Venda:</label>
                                <input type="text" name="preco" id="preco" class="form-control campo9" value="" onblur="javascript:document.all.valor.value = _myfunc_valor_brasil_to_usa(this.value);return false;"  onkeyup="javascript:this.value = mascara_global(\'[###.]###,##\', this.value)"> 
                            </div>
                            <div style="clear: both"></div>
                            <div style="font-weight: bold; padding-top: 10px;">
                                '.$nums.'
                            </div>
                            <div style="clear: both"></div>
                            <br>
                            <div>
                                <label for="" style="font-size: 14px;">&nbsp;</label>
                                <button type="submit" id="salvarModal" class="form-control btn btn-default salvarModal" abbr-actSave="salvarCalcados" style="width: 150px; float: right; cursor: pointer;">Salvar</button>
                            </div>
                            <br>
                        </div>
                    </fieldset>
                </div>
            ';
            //$btao = '
            //    <button title="Adicionar Cal&ccedil;ados" class="button ModalUnique" abbr-Action="formCalcados">Adicionar Cal&ccedil;ados</button><br />
           //     
                
           // '.$form;            
        }
        
        if ($inc == 'produtos_relatorios.php' && trim($ps['calcados']) == 'S') {
            //$btao = '<button title="Gerar Etiquetas" class="button ModalUnique" abbr-Action="formEtiquetas">Etiquetas 3 colunas</button>';
        }
        
        if ($inc == 'jobs_browse.php' && isset($ps['clientesAtualizar']) && trim($ps['clientesAtualizar']) == 'S') {
            $btao = '<button title="Clientes a Atualizar" class="button ModalUnique" abbr-Action="ListClientes">Clientes a Atualizar</button>';
        }
        echo '
            <div id="id_alguaCoisa" > 
                '.$btao.'
            </div>

            <div id="id_modal" class="modal" style=" left: 50px; top: 0px;">
                <div id="id_modal_dados">
                    <link rel="stylesheet" type="text/css" href="../api3/css/modalOnlineSistemas.css" rel="stylesheet">
                    <div id="myModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title">TitulosModal</h4>
                                    <div style="float: right"><a id="fecharModalOn" style="cursor: pointer; color: #337ab7; font-weight: normal">Fechar</a></div>
                                    <div style="clear: both"> </div>
                                </div>
                                <!-- Modal body -->
                                <div class="modal-body" style="overflow: auto; height: 450px;">
                                    CorpoModal
                                </div>
                                <!-- Modal footer -->
                                <div class="modal-footer text-right">
                                    <div style="float: right"><a id="fecharModalOn" style="cursor: pointer; color: #337ab7; font-weight: normal">Fechar</a></div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
        break;
        
    //case '../api3/teste_session.php':
      //  echo '<div id="SESSION"> </div>';
     //   break;
}


if (in_array($inc, array('lan_receitas_edita.php', 'lan_fluxo_browse.php', 'lan_compras_browse.php'))) {    
    if (isset($ps['limitNF']) && !empty($ps['limitNF'])) {
        $mesCorrente = date('m');
        $anoCorrente = date('Y');
        $mesI = strtotime("$anoCorrente/$mesCorrente/1 00:00:01");
        $mesF = strtotime("$anoCorrente/$mesCorrente/".date('t')." 23:59:59");

        $sqlTNf = "SELECT count(dono) as total FROM $TNFDOCUMENTOS WHERE data < '$mesF' AND data > '$mesI' ORDER BY data";
        $getTNf = mysqli_query($conn_a, $sqlTNf);
        $tNf = mysqli_fetch_assoc($getTNf);
        //echo '"'.$tNf['total'].'" --- "'.$ps['limitNF'].'"<br>';
        //echo var_dump($_SERVER).'<BR>';
        //echo 'dasio<BR>';
        //echo var_dump($_POST).'<BR>';
        if ($tNf['total'] >= trim($ps['limitNF'])) {
            if (isset($_POST['btnova']) && $_POST['btnova'] == 'Novo atendimento') {
                echo ('<script>alert("'.$ps['msgLimitNF'].'");location.href = "'.$_SERVER['HTTP_REFERER'].'"</script>');
            }
            
            if (isset($_GET['opc']) && $_GET['opc'] == 'gerarnfe') {
                echo ('<script>alert("'.$ps['msgLimitNF'].'");location.href = "'.$_SERVER['HTTP_REFERER'].'"</script>');
                exit ();
            }   
        }
    }
    
    //echo(var_dump($_REQUEST));
    
    if (isset($_GET['moddc']) && $_GET['moddc'] == '2D') {
        $aliquota = 18;
        $dono = filter_input(INPUT_GET, 'dono');
        $updateItemFluxoTmp = "UPDATE $TITEM_FLUXO_TMP SET pcredsn='$aliquota',vcredicmssn=((vprod-vdesc)*($aliquota/100)) WHERE dono='$dono' and (csosn='101' or csosn='102') and movimento='receitas'";
        //exit($updateItemFluxoTmp);
        if (!mysqli_query($conn_a, $updateItemFluxoTmp)) {
            //exit('Falha ao tentar fazer update!');
        } else {
            //exit('Sucesso ao tentar fazer update!');
        }
    }
}
    
//echo "Expira em: ". var_dump($_SESSION);

//ini_set("session.gc_maxlifetime", 300);
//echo var_dump($_GET);
?>
<link rel="stylesheet" type="text/css" href="../_css/themes/office/ui.datepicker.css" />     
<script>
var matched, browser;

jQuery.uaMatch = function( ua ) {
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
        /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
        /(msie) ([\w.]+)/.exec( ua ) ||
        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
        [];

    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
};

matched = jQuery.uaMatch( navigator.userAgent );
browser = {};

if ( matched.browser ) {
    browser[ matched.browser ] = true;
    browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if ( browser.chrome ) {
    browser.webkit = true;
} else if ( browser.webkit ) {
    browser.safari = true;
}

jQuery.browser = browser;
</script>
<script src="../_javascripts/jquery-ui-1.10.3/ui/jquery-ui.js"></script>
<script src="../_javascripts/bootstrap.js"></script>
<script src='../_javascripts/ui/ui.datepicker.min.js'></script>
<script type="text/javascript">

    <?php
        if (isset($ps['timeOut']) && !empty($ps['timeOut']) && $inc == '../api3/teste_session.php'/**/) {
    ?>
        function start_session_timeout(){
        var time_out    = <?php echo $ps['timeOut']?> * 60 * 1000; 
            sessao = setTimeout(function() {
                //alert("Sua sess�o expirou!\nEfetue login.");
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: 'BdAction=RestartSession', 
                    success: function (dados) {
                        //$("#SESSION").append("<div>"+dados+"</div>");
                        //console.log(dados);
                        restart_session_timeout();
                    }
                });
            }, (time_out - 5000));

        }

    // reinicia a contagem de tempo de sess�o quando um arquivo chamado via ajax recarrega a sess�o
    function restart_session_timeout(){
        clearInterval(sessao);
        start_session_timeout();
    }

    // inicia a contagem no load da p�gina
    start_session_timeout();
    <?php
        }
    ?>

    function somenteNumeros(num) {
        var er = /[^0-9.]/;
        er.lastIndex = 0;
        var campo = num;
        if (er.test(campo.value)) {
          campo.value = "";
        }
    }
 
    function _local_operacao(dono) {
        // document.getElementById("id_div_local_operacao").style='display:visible;';
        document.getElementById("id_div_local_operacao").style.display = "inline";

        var BdAction = 'BdAction_local_operacao';
        var nDiv = 'id_div_local_operacao';
        $.ajax({
            url: "../napi3/BD_API3_webservice.php",
            type: "POST",
            data: {
                dono: dono,
                nDiv: nDiv,
                BdAction: BdAction
            }, success: function (dados) {
                document.getElementById("id_div_local_operacao").innerHTML = dados;
            }, error: function (dados) {
                alert("Status: " + JSON.stringify(dados));
            }
        });
    }



    function _local_operacao_modal(dono) {
        document.getElementById("id_div_local_operacao_modal").style.display = "inline";
        var BdAction = 'BdAction_local_operacao',
            nDiv = 'id_div_local_operacao_modal';
        $.ajax({
            url: "../napi3/BD_API3_webservice.php",
            type: "POST",
            data: {
                dono: dono,
                nDiv: nDiv,
                BdAction: BdAction
            }, success: function (dados) {
                document.getElementById("id_div_local_operacao_modal_dados").innerHTML = dados;
            }, error: function (dados) {
                alert("Status: " + JSON.stringify(dados));
            }
        });
    }

    function _update_local_operacao(id, cLocalOperacao, dono) {
        var BdAction = 'BdAction_update_local_operacao';
        $.ajax({
            url: "../napi3/BD_API3_webservice.php",
            type: "POST",
            data: {
                dono: dono,
                id: id,
                cLocalOperacao: cLocalOperacao,
                BdAction: BdAction
            }, success: function (dados) {
                //  _local_operacao(dono);
            }, error: function (dados) {
                alert("Status: " + JSON.stringify(dados));
            }
        });
        return;
    }


    function fechar(qTela) {
        document.getElementById(qTela).style.display = 'none';
        return;
    }

    function _lancamento_dc() {

        document.getElementById("id_div_lancamento_dc").style.display = "inline";
        var ldata = document.getElementById("id_datalancamento").value;
        var valor = document.getElementById("id_valor").value;
        var cdebito = document.getElementById("id_cdebito").value;
        var ccredito = document.getElementById("id_ccredito").value;
        var historico = document.getElementById("id_historico").value;
        var BdAction = 'BdAction_lancamento_dc';
        var nDiv = 'id_div_lancamento_dc';

        $.ajax({
            url: "../napi3/BD_API3_webservice.php",
            type: "POST",
            data: {
                ldata: ldata,
                valor: valor,
                cdebito: cdebito,
                ccredito: ccredito,
                historico: historico,
                nDiv: nDiv,
                BdAction: BdAction
            }, success: function (dados) {
                document.getElementById('id_div_lancamento_dc').innerHTML = "<br>" + dados;
            }, error: function (dados) {
                alert("Status: " + JSON.stringify(dados));
            }
        });
    }
        $(document).ready(function () {
            $(document).on('click', "#fecharModalOn", function() {
                $("#id_div_local_operacao_modal").hide();
                $("#id_posVenda_modal").hide();
                $("#id_modal").hide();
                
                $("#id_modal_dados").find('.modal-title').html('TitulosModal');
                $("#id_modal_dados").find('.modal-body').html('CorpoModal');
                
            });
            
            $(document).on('click', '#REDOCS', function() {
                //alert('Clicou em Redocs');
                var dono = $(this).attr('abbr');
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: 'Redocs',
                        dono: dono
                    }, success: function (dados) {
                        alert(dados);
                        //$('#id_div_local_operacao_modal_dados').html(dados);
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
            });
            
            $(document).on('click', '#logsAcesso', function() {
                //var alvo = $(this).parent().next().find().attr('id');
//                //alert('clicou no btn ' + alvo); 
                $('#id_div_local_operacao_modal').show();
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: 'getLogs'
                    }, success: function (dados) {
                       // alert(dados);
                        $('#id_div_local_operacao_modal_dados').html(dados);
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
            });
            
            $(document).on('change', '#campo', function(){
                var val = $(this).val();
                if (val === 'data') {
                    $(this).parent().next().html('<input type="text" class="data" name="dt1" id="dt1" placeholder="Data Inicial" onkeyup="this.value = mascara_global(\'##/##/####\', this.value);"/> &nbsp;&nbsp;<input type="text" class="data" name="dt2" id="dt2" placeholder="Data Final" onkeyup="this.value = mascara_global(\'##/##/####\', this.value);" />&nbsp;&nbsp;&nbsp;<button id="filtrarLogs" title="Filtrar Log" class="button">Filtrar</button> <script>$(document).ready(function () {  $(".data").datepicker(); })<\/script>');
                } else if(val === 'lancnpjcpf') {
                    $(this).parent().next().html('<input type="text" name="filtro" id="filtro" placeholder="CNPJ / CPF" onkeyup="this.value = mascara_global(\'###.###.###-##\', this.value);" /> &nbsp;&nbsp;&nbsp;<button id="filtrarLogs" title="Filtrar Log" class="button">Filtrar</button>');
                } else if(val === 'cliente') {
                    $(this).parent().next().html('<input type="text" name="filtro" id="filtro" placeholder="Nome do Cliente"  /> &nbsp;&nbsp;&nbsp;<button id="filtrarLogs" title="Filtrar Log" class="button">Filtrar</button>');
                } else if(val === 'dono') {
                    $(this).parent().next().html('<input type="text" name="filtro" id="filtro" placeholder="Dono do Documento (LAN)"  /> &nbsp;&nbsp;&nbsp;<button id="filtrarLogs" title="Filtrar Log" class="button">Filtrar</button>');
                } else {
                    $(this).parent().next().html('<input type="text" name="filtro" id="filtro" placeholder="IP" onkeyup="this.value = mascara_global(\'###.###.#.#\', this.value);" /> &nbsp;&nbsp;&nbsp;<button id="filtrarLogs" title="Filtrar Log" class="button">Filtrar</button>');
                }    
            });
            
            $(document).on('click', '#filtrarLogs', function() {
                var camp = $('#campo').val(),
                    filt = $('#filtro').val(),
                    d1 = $('#dt1').val(),
                    d2 = $('#dt2').val(),
                    erro = '';
                    
                if (camp === '') {
                    erro += 'Campos do filtro obrigatorios!';
                    alert(erro);
                    return;
                } 
                
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: 'filtroLogs',
                        campo: camp,
                        filtro: filt,
                        dt1: d1,
                        dt2: d2
                    }, success: function (Dados) {
                        var msg = Dados.substr(0,5);
                        if (msg === 'FALSE') {
                            alert(Dados.split('#')[1]);
                        } else {
                            $('#resultadoLogs').html(Dados);
                        }
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
                
            });
            
            $(document).on('click', '#salvarPosVenda', function() {
                var btn     = $(this),
                    dono    = btn.parent().parent().find('#dono').val(),
                    assunto = btn.parent().parent().find('#assuntoA').val(),
                    respost = btn.parent().parent().find('#respO').val(),
                    agenda  = btn.parent().parent().find('#agend').val();
                
                //alert('Clicou em salvar pos venda! + ' + btn.attr('id') + ' --- ' + assunto );
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: 'posVendasSalvar',
                        dono: dono,
                        assunto: assunto,
                        resposta: respost,
                        agenda: agenda
                    }, success: function (dados) {
                       // alert(dados);
                        $('#id_posVenda_modal_dados').html(dados);
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
            });
            
            $(document).on('click', '.docs', function() {
                //var alvo = $(this).parent().next().find().attr('id');
                //alert($(this).attr('id'));
    
//                //alert('clicou no btn ' + alvo); 
                $('#id_posVenda_modal').show();
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: 'posVendas',
                        dono: $(this).attr('id'),
                        tipoDoc: 'Confirmado'
                    }, success: function (dados) {
                       // alert(dados);
                        $('#id_posVenda_modal_dados').html(dados);
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
            });  
            
            $(document).on('click', '#AgendamentosPosVenda', function(){
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: 'posVendasAgendamentos'
                    }, success: function (dados) {
                       // alert(dados);
                        $('#id_posVenda_modal_dados').html(dados);
                        $('#id_posVenda_modal').show();
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
            });
            
            $(document).on('click', '#filtrarAgendaPosVenda', function() {
                var d1 = $('#dt1').val(),
                    d2 = $('#dt2').val();

                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: 'filtroAgendmantoPosVenda',
                        dt1: d1,
                        dt2: d2
                    }, success: function (Dados) {
                        var msg = Dados.substr(0,5);
                        if (msg === 'FALSE') {
                            alert(Dados.split('#')[1]);
                        } else {
                            $('#respoAgendamentoPosVenda').html(Dados);
                        }
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
                
            });
            
            $(document).on('click', '.AbreModalUnico', function() {
                var action = 'mDevolucoes',
                    dono = $(this).attr('id');
                               
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: action,
                        dono: dono
                    }, success: function (retorno) {
                        //alert(retorno);
                        var valor = retorno.split('||');
                        $('#id_modal_dados').find('.modal-title').html(valor[0]);
                        $('#id_modal_dados').find('.modal-body').html(valor[1] );
                        $('#id_modal').show();
                        if (action === 'mDevolucoes') {
                            $('#id_modal').find('#myModal').children().removeClass('modal-dialog');
                            $('#id_modal').find('#myModal').children().addClass('modal-dialogL');
                        }   
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
            });              
            
            
            $(document).on('click', '.itemm', function() {
                var item = $(this).attr('id').split('_')[1],
                    alvo = $(this).parent().parent().parent(),
                    title = $(this).parent().parent().parent().parent().find('.modal-title');
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: 'getItem',
                        dono: item
                    }, success: function (retorno) {
                        //alert(retorno);
                        var val = retorno.split('||');
                        title.html(val[0]);
                        alvo.html(val[1]);
                        alvo.parent().parent().removeClass('modal-dialogL');
                        alvo.parent().parent().addClass('modal-dialog');
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
            });
            
            $(document).on('click', '#salvarItemMDev', function() {
                //alert('clicou no btn');
                var alvo    = $(this).parent().parent().parent(),
                    item    = alvo.find('#id').val(),
                    dono    = alvo.find('#dono').val(),
                    vprod   = alvo.find('#vprod').val(),
                    vdesc   = alvo.find('#vdesc').val(),
                    voutro  = alvo.find('#voutro').val(),
                    vicmsst = alvo.find('#vicmsst').val(),
                    vbcst   = alvo.find('#vbcst').val(),
                    vbcipi  = alvo.find('#vbcipi').val(),
                    pipi    = alvo.find('#pipi').val(),
                    picms   = alvo.find('#picms').val(),
                    vipi    = alvo.find('#vipi').val(),
                    vicms   = alvo.find('#vicms').val(),
                    vbc     = alvo.find('#vbc').val(),
                    ncm     = alvo.find('#ncm').val(),
                    title   = alvo.parent().find('.modal-title'),
                    action  = 'mDevolucoes',
                    dados   = 'BdAction='+action+'&item='+item+'&dono='+dono+'&vprod='+vprod+'&vdesc='+vdesc+'&voutro='+voutro+'&vicmsst='+vicmsst+'&vbcst='+vbcst+'&vbcipi='+vbcipi+'&pipi='+pipi+'&picms='+picms+'&vipi='+vipi+'&vbc='+vbc+'&vicms='+vicms+'&ncm='+ncm;
                    
                    //alert(dados);
                    $.ajax({
                        url: "../napi3/BD_API3_webservice.php",
                        type: "POST",
                        data: dados, 
                        success: function (retorno) {
                            //alert(retorno);
                            var val = retorno.split('||');
                            title.html(val[0]);
                            alvo.html(val[1]);
                            if (action === 'mDevolucoes') {
                                alvo.parent().parent().removeClass('modal-dialog');
                                alvo.parent().parent().addClass('modal-dialogL');
                            } 
                        }, error: function (dados) {
                            alert("Status: " + JSON.stringify(dados));
                        }
                    });
                
            });
            
            $(document).on('click', '.atualizaNfDocTmp', function() {
                //alert('clicou no btn');
                var alvo    = $(this).parent().parent().parent().parent().parent().parent().parent(),
                    dono    = $(this).attr('abbr'),
                    action  = 'atualizaNfDocTmp',
                    dados   = 'BdAction='+action+'&dono='+dono;
                    
                //alert(dados);
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: dados, 
                    success: function (retorno) {
                        //alert(retorno);
                        alert("Dados confirmados!");
                        alvo.hide();
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
                
            });
            
            $(document).on('click', '.ModalUnique', function() { 
                var action = $(this).attr('abbr-Action'), 
                    title = $(this).attr('title'),
                    dono = '';
                    
                if (action === 'formCalcados') {
                    var formFlancamento = $(this).parent().parent().find("[name*='flancamento']");
                    
                    formFlancamento.removeAttr("name");
                }
                
                $.ajax({
                    url: "../napi3/BD_API3_webservice.php",
                    type: "POST",
                    data: {
                        BdAction: action,
                        dono: dono,
                        info: <?php echo $infotitulo['crt']?>
                    }, success: function (retorno) {
                        //alert(retorno);
                        //var valor = retorno.split('||');
                        $('#id_modal_dados').find('.modal-title').html(title);
                        $('#id_modal_dados').find('.modal-body').html(retorno);
                        $('#id_modal').show();
                        if (action === 'mDevolucoes' || action === 'ListClientes') {
                            $('#id_modal').find('#myModal').children().removeClass('modal-dialog');
                            $('#id_modal').find('#myModal').children().addClass('modal-dialogL');
                        } 
                    }, error: function (dados) {
                        alert("Status: " + JSON.stringify(dados));
                    }
                });
            });
            
            $(document).on('click', '.salvarModal', function() {
                //alert('clicou no btn salvarModal');
                var alvo    = $(this).parent().parent().parent(),
                    campo1  = alvo.find('.campo1').val(),
                    campo2  = alvo.find('.campo2').val(),
                    campo3  = alvo.find('.campo3').val(),
                    campo4  = alvo.find('.campo4').val(),
                    campo5  = alvo.find('.campo5').val(),
                    campo6  = alvo.find('.campo6').val(),
                    campo7  = alvo.find('.campo7').val(),
                    campo8  = alvo.find('.campo8').val(),
                    campo9  = alvo.find('.campo9').val(),
                    campo10  = alvo.find('.campo10').val(),
                    campo11  = alvo.find('.campo11').val(),
                    campo12  = alvo.find('.campo12').val(),
                    campo13  = alvo.find('.campo13').val(),
                    campo14  = alvo.find('.campo14').val(),
                    campo15  = alvo.find('.campo15').val(),
                    campo16  = alvo.find('.campo16').val(),
                    campo17  = alvo.find('.campo17').val(),
                    campo18  = alvo.find('.campo18').val(),
                    campo19  = alvo.find('.campo19').val(),
                    campo20  = alvo.find('.campo20').val(),
                    campo21  = alvo.find('.campo21').val(),
                    campo22  = alvo.find('.campo22').val(),
                    title    = alvo.parent().find('.modal-title'),
                    action   = $(this).attr('abbr-actSave'),
                    numeros  = '',
                    dados    = 'BdAction='+action+'&campo1='+campo1+'&campo2='+campo2+'&campo3='+campo3+'&campo4='+campo4+'&campo5='+campo5+'&campo6='+campo6+'&campo7='+campo7+'&campo8='+campo8+'&campo9='+campo9+'&campo10='+campo10+'&campo11='+campo11+'&campo12='+campo12+'&campo13='+campo13+'&campo14='+campo14+'&campo15='+campo15+'&campo16='+campo16+'&campo17='+campo17+'&campo18='+campo18+'&campo19='+campo19+'&campo20='+campo20+'&campo21='+campo21+'&campo22='+campo22+'&num=';
                    
                    if (action === 'salvarCalcados') {
                        $("input[name='numeroCalcado[]']:checked:enabled").each(function() {
                            numeros=$(this).val()+","+numeros;
                        });
                    }
                    
                    $.ajax({
                        url: "../napi3/BD_API3_webservice.php",
                        type: "POST",
                        data: dados+numeros, 
                        success: function (retorno) {
                            var val = retorno.split('||');
                            alert(val[1]);
                            if (action === 'salvarCalcados') {
                                alvo.find('.campo1').val('');
                                alvo.find('.campo2').val('');
                                alvo.find('.campo3').val('');
                                alvo.find('.campo4').val('');
                                alvo.find('.campo5').val('');
                                alvo.find('.campo6').val('');
                                alvo.find('.campo7').val('');
                                alvo.find('.campo8').val('');
                                alvo.find('.campo9').val('');
                                alvo.find('.campo10').val('');
                                alvo.find('.campo11').val('');
                                alvo.find('.campo12').val('');
                                alvo.find('.campo13').val('');
                                alvo.find('.campo14').val('');
                                alvo.find('.campo15').val('');
                                alvo.find('.campo16').val('');
                                alvo.find('.campo17').val('');
                                alvo.find('.numbs').attr('checked', false);
                                
                            } else {
                                if (val[0] === 'TRUE') {
                                    alvo.parent().parent().parent().parent().parent().hide();
                                }
                            }
                        }, error: function (dados) {
                            alert("Status: " + JSON.stringify(dados));
                        }
                    });
                
            });
            
            $(document).on('click', '.SalvarInfoAtualizaCliente', function() { 
                var clie   = $(this).parent().parent(),
                    cnpj   = clie.attr('id').split('_')[1],
                    dataP  = clie.find('#data_pre_'+cnpj).val(),
                    dataA  = clie.find('#data_atu_'+cnpj).val(),
                    Obs    = clie.find('#OBS_'+cnpj).val();
                //alert(cnpj+' -- '+dataP+' -- '+dataA+' -- '+Obs);
                
                if (dataP !== '' || dataA !== '' || Obs !== '') {
                        //alvo = $(this).parent().parent().parent().parent().parent(),
                        //title = $(this).parent().parent().parent().parent().parent().parent().find('.modal-title');
                    $.ajax({
                        url: "../napi3/BD_API3_webservice.php",
                        type: "POST",
                        data: 'BdAction=SalvarInfoCliente&dono='+cnpj+'&data_pre='+dataP+'&data_atu='+dataA+'&obs='+Obs,
                        success: function (retorno) {
                            alert(retorno);
                        }, error: function (dados) {
                            alert("Status: " + JSON.stringify(dados));
                        }
                    });
                }
            });
            <?php
                if ($inc == 'produtos_cadastro.php' && trim($ps['calcados']) == 'S') {
                    echo '
                        $(document).on("focus", "#descricao", function() {
                            var formFlancamento = $(this).parent().parent().parent().parent().parent().parent().parent().find("[name*=\'flancamento\']");
                            formFlancamento.removeAttr("name");
                            $("#caldados").attr("name", "flancamento");
                        });
                    ';
                }
            ?>
            
            
            $.datepicker.setDefaults({
                monthNames: ['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                dayNames: ['Domingo', 'Segunda', 'Ter&ccedil;a', 'Quarta', 'Quinta', 'Sexta', 'S&aacute;bado'],
                dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'],
                dateFormat: "dd/mm/yy"
            });

            $(".data").datepicker();
        });
    </script>


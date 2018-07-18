<?php
include('../napi3/BD_API3HeadAjax.php');
//require_once '../myfuncoes.php';
$BdAction = BASICO::trata_var($_POST['BdAction'], string);
$dono = BASICO::trata_var($_POST['dono'], string);
$nDiv = BASICO::trata_var($_POST['nDiv'], string);

$camp1  = filter_input(INPUT_POST, 'campo1');
$camp2  = filter_input(INPUT_POST, 'campo2');
$camp3  = filter_input(INPUT_POST, 'campo3');
$camp4  = filter_input(INPUT_POST, 'campo4');
$camp5  = filter_input(INPUT_POST, 'campo5');
$camp6  = filter_input(INPUT_POST, 'campo6');
$camp7  = filter_input(INPUT_POST, 'campo7');
$camp8  = filter_input(INPUT_POST, 'campo8');
$camp9  = filter_input(INPUT_POST, 'campo9');
$camp10 = filter_input(INPUT_POST, 'campo10');
$camp11 = filter_input(INPUT_POST, 'campo11');
$camp12 = filter_input(INPUT_POST, 'campo12');
$camp13 = filter_input(INPUT_POST, 'campo13');
$camp14 = filter_input(INPUT_POST, 'campo14');
$camp15 = filter_input(INPUT_POST, 'campo15');
$camp16 = filter_input(INPUT_POST, 'campo16');
$camp17 = filter_input(INPUT_POST, 'campo17');
$camp18 = filter_input(INPUT_POST, 'campo18');
$camp19 = filter_input(INPUT_POST, 'campo19');
$camp20 = filter_input(INPUT_POST, 'campo20');
$camp21 = filter_input(INPUT_POST, 'campo21');
$camp22 = filter_input(INPUT_POST, 'campo22');


function _getLogs($conn_a, $where = '') {
    $sql_logs = "SELECT * FROM logs $where ORDER BY id DESC LIMIT 0, 50";
    #exit("FALSE#".$sql_logs);
    $get_logs = mysqli_query($conn_a, $sql_logs);
    
    echo '
        <table class="table table-hover" border="1">
            <tr class="cel_subtit">
                <td align="center">Data</td>
                <td align="center">IP</td>
                <td align="center">USU&Aacute;RIO</td>
            </tr>
    '; 
    
    while ($log = mysqli_fetch_assoc($get_logs)) {
        $sql_razao = "SELECT razao FROM cnpjcpf WHERE cnpj = '".$log['lancnpjcpf']."'";
        $get_razao = mysqli_query($conn_a, $sql_razao);
        $n = mysqli_fetch_array($get_razao);
        echo '
            <tr>
                <td>'.date('d/m/Y H:i:s', $log['data']).'</td>
                <td>'.$log['ip'].'</td>
                <td>'.$n['razao'].'</td>
            </tr>
        ';
    }
    echo '</table>';
}

function _getPosVendas($conn_a, $where) {
    $pos =  '
        <br><table class="table table-hover" border="1">
            <tr class="cel_subtit">
                <td align="center">Data Atendimento</td>
                <td align="center">Assunto</td>
                <td align="center">Resposta</td>
                <td align="center">Agendamento</td>
            </tr>
    '; 
    $sqlPosVendas = "SELECT * FROM pos_venda $where";
    $getPosVendas = mysqli_query($conn_a, $sqlPosVendas);
    while ($p = mysqli_fetch_assoc($getPosVendas)) {
        if ($p['agendamento'] <> 0) {
            $dtAgendamento = substr(date('d/m/Y H:i:s', $p['agendamento']), 0, 10);
        } else {
            $dtAgendamento = '';
        }
        $pos .= '
            <tr>
                <td>'.date('d/m/Y H:i:s', $p['data_pos']).'</td>
                <td>'.$p['assunto'].'</td>
                <td>'.$p['resposta_operador'].'</td>
                <td>'.$dtAgendamento.'</td>
            </tr>
        ';
    }
    
    $pos .= '</table>';
    
    return $pos;
}

function _getAgendamentosPosVendas($conn_a, $dt1, $dt2) {
    $pos =  '
        <br><table class="table table-hover" border="1">
            <tr class="cel_subtit">
                <td align="center">Data Atendimento</td>
                <td align="center">Assunto</td>
                <td align="center">Resposta</td>
                <td align="center">Agendamento</td>
            </tr>
    '; 
    $sqlPosVendas = "SELECT * FROM pos_venda WHERE dono = '$dono'";
    $getPosVendas = mysqli_query($conn_a, $sqlPosVendas);
    while ($p = mysqli_fetch_assoc($getPosVendas)) {
        $pos .= '
            <tr>
                <td>'.date('d/m/Y H:i:s', $p['data_pos']).'</td>
                <td>'.$p['assunto'].'</td>
                <td>'.$p['resposta_operador'].'</td>
                <td>'.substr(date('d/m/Y H:i:s', $p['agendamento']), 0, 10).'</td>
            </tr>
        ';
    }
    
    $pos .= '</table>';
    
    return $pos;
}

/**
* @param var $conn variavel da conexao com o banco de dados
* @param string $tabela tabela que vai ser feita a consulta
* @param string  $param = codigo da naturaze de operação que esta registrado na tabela
* @return string conta de serviço
*/
function getNatureza($param, $conn, $tabela ) {
   // pega a naturza de operacao verificando a conta de servico
   $sqlNatureOpIt = "SELECT contaservicos FROM $tabela WHERE contaservicos <> '' AND codnat = '".$param."'";
   $getNatureOpIt = mysqli_query($conn, $sqlNatureOpIt);
   $naturezaIt = mysqli_fetch_assoc($getNatureOpIt);
   return $naturezaIt['contaservicos'];
}

switch ($BdAction) {


    case 'BdAction_update_local_operacao':

        $id = BASICO::trata_var($_POST['id'], string);
        $cLocalOperacao = BASICO::trata_var($_POST['cLocalOperacao'], string);
        $TITEM_FLUXO_TMP;
        $sql_q = "UPDATE $TITEM_FLUXO_TMP SET localoperacao = '$cLocalOperacao' WHERE id='$id' AND dono='$dono'";
        $querySQL = mysqli_query($conn_a, $sql_q);

        break;

    case 'BdAction_local_operacao':

        $matriz_local_operacao = Array();
        $matriz_local_operacao[''] = '';
        $matriz_local_operacao['01'] = 'APARTAMENTO';
        $matriz_local_operacao['02'] = 'FRIGOBAR';
        $matriz_local_operacao['03'] = 'FRIGOBAR RECEPÇÃO';
        $matriz_local_operacao['04'] = 'LAVANDERIA';
        $matriz_local_operacao['05'] = 'RESTAURANTE';
        $matriz_local_operacao['06'] = 'TELEFONE';
        
        $TITEM_FLUXO_TMP;
        $sql_q = "SELECT * FROM $TITEM_FLUXO_TMP WHERE dono='$dono'";
        $querySQL = mysqli_query($conn_a, $sql_q);

        $acha_qtde = (mysqli_affected_rows($conn_a));

        
        echo '
            <link rel="stylesheet" type="text/css" href="../api3/css/modalOnlineSistemas.css" rel="stylesheet">
            <div id="myModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">ALTERANDO LOCAL DE OPERA&Ccedil;&Atilde;O</h4>
                            <div style="float: right"><a id="fecharModalOn" style="cursor: pointer; color: #337ab7; font-weight: normal">Fechar</a></div>
                            <div style="clear: both"> </div>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                            <table class="table table-hover">
                                <tr class="cel_subtit">
                                    <td align="center">C&Oacute;DIGO</td>
                                    <td align="center">DESCRI&Ccedil;&Atilde;O</td>
                                    <td align="center">LOCAL OPERA&Ccedil;&Atilde;O</td>
                                </tr>'; 
                            while ($exibe = mysqli_fetch_assoc($querySQL)) {
                                $chk_localoperacao[$exibe['localoperacao']] = 'selected';
                                ?>
                                    <tr>
                                        <td><?php echo $exibe['cprod']?></td>
                                        <td><?php echo $exibe['xprod']?></td>
                                        <td>
                                            <select name="local_operacao" id="local_operacao" class="form-control" onchange="_update_local_operacao('<?= $exibe['id']; ?>', this.value, '<?= $dono; ?>')"  >
                                                <?php
                                                    foreach ($matriz_local_operacao as $k => $v) {
                                                        echo '<option value="'.$k.'" '.$chk_localoperacao[$k].'>'.$v.'</option>';
                                                    }
                                                    $chk_localoperacao[$exibe['localoperacao']] = '';
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php
                            }
                        echo '</table>
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer text-right">
                            
                        </div>

                    </div>
                </div>
            </div
            
        ';
        ?>
        <!-- span class='close' onclick=getElementById('<?= $nDiv; ?>').style.display='none';>X</span -->

        <?php

        break;



    case 'BdAction_lancamento_dc':


        $data = BASICO::trata_var($_POST['ldata'], string);
        $valor = BASICO::trata_var($_POST['valor'], string);
        $conta_loted = BASICO::trata_var($_POST['cdebito'], string);
        $conta_lotec = BASICO::trata_var($_POST['ccredito'], string);

        $codhis = '';
        $historico = BASICO::trata_var($_POST['historico'], string);

        $retorno_lancamento = gerar_lancamentos_dc($data, $conta_loted, $conta_lotec, $valor, $codhis, $historico);
        IF (count($retorno_lancamento) > 0) {
            echo "Erro de lançamento!<br>";
            print_r($retorno_lancamento);
        } else {
            echo "true";
        }
        break;
        
    case 'getLogs':
        echo '
            <link rel="stylesheet" type="text/css" href="../api3/css/modalOnlineSistemas.css" rel="stylesheet">
            <div id="myModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">LOG DE ACESSO</h4>
                            <div style="float: right"><a id="fecharModalOn" style="cursor: pointer; color: #337ab7; font-weight: normal">Fechar</a></div>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                            <div>Filtrar por <select name="campo" id="campo"><option></option><option value="lancnpjcpf">CNPJ / CPF</option><option value="data">Data</option><option value="ip">IP</option></select></div>
                            <div class="inputs"><input type="text" name="filtro" id="filtro" value="" /> &nbsp;&nbsp;&nbsp;<button id="filtrarLogs" title="Filtrar Log" class="button">Filtrar</button></div>
                            <div id="resultadoLogs" style="overflow: auto; height: 400px">';
                            _getLogs($conn_a);
                            echo '</div>
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer text-right">
                        </div>

                    </div>
                </div>
            </div
            
        ';
        break;
        
    case 'filtroLogs':
        $campo  = filter_input(INPUT_POST, 'campo');
        $filtro = filter_input(INPUT_POST, 'filtro');
        $dat1   = filter_input(INPUT_POST, 'dt1');
        $dat2   = filter_input(INPUT_POST, 'dt2');
        
        //exit(var_dump($_POST));
        switch($campo) {
            case 'data':
               // exit('FALSE#'.$dat1);
                list($d1, $m1, $a1) = preg_split('/[-.\/ ]/', $dat1);
                //exit('FALSE#'.$dat1.'<br>'.$m1.' -- '.$d1.' -- '.$a1);
                if (!checkdate($m1, $d1, $a1)) {
                    exit('FALSE#Data inicial invalida!');
                }
                
                list($d2, $m2, $a2) = preg_split('/[-.\/ ]/', $dat2);
                if (!checkdate($m2, $d2, $a2)) {
                    exit('FALSE#Data final invalida!');
                }
                
                $dataI = mktime(00, 00, 00, $m1, $d1, $a1);
                $dataF = mktime(23, 59, 59, $m2, $d2, $a2);
                
                $where = " WHERE  $campo >= '$dataI' AND $campo <= '$dataF'";
                break;
            case 'ip':
            case 'lancnpjcpf':
                if ($campo == 'lancnpjcpf') {
                    $filtro = trim(str_replace(array('.', ',', '-', '(', ')', ' '), '', $filtro));
                }
                
                $where = " WHERE $campo = '$filtro'";
                break;
            
            default:
                $where = '';
                //exit('FALSE#Valor do campo a filtar invalido!');
                break;
        }
        
        _getLogs($conn_a, $where);
        break;
        
    case 'posVendas':
        
        
        $tipo   = filter_input(INPUT_POST, 'tipoDoc');
        $tmp    = ($tipo == 'Aberto') ? "_tmp" : "";
        $where = " WHERE dono = '$dono'";

        echo '
            <link rel="stylesheet" type="text/css" href="../api3/css/modalOnlineSistemas.css" rel="stylesheet">
            <div id="myModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Relatorio Pos Venda</h4>
                            <div style="float: right"><a id="fecharModalOn" style="cursor: pointer; color: #337ab7; font-weight: normal">Fechar</a></div>
                            <div style="clear: both"> </div>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                            <div>
                                <label for="assuntoA" style="font-size: 14px;">Assunto Abordado:</label>
                                <input type="text" name="assuntoA" id="assuntoA" class="form-control" value="">
                            </div><br>
                            <div>
                                <label for="respO"  style="font-size: 14px;">Resposta operador:</label>
                                <input type="text" name="respO" id="respO" class="form-control" value="">
                            </div><br>
                            <div>
                                <label for="agend" style="font-size: 14px;">Agendamento de retorno:</label>
                                <input type="hidden" name="dono" id="dono" value="'.$dono.'">
                                <input type="text" name="agend" id="agend" class="form-control" value="'.date('d/m/Y').'">
                            </div><br>
                            <div>
                                <label for="" style="font-size: 14px;">&nbsp;</label>
                                <button type="submit" id="salvarPosVenda" class="form-control btn btn-default" style="width: 150px; float: right; cursor: pointer;">Salvar</button>
                            </div>
                            <br>
                            <div id="POSVE">'._getPosVendas($conn_a, $where).'
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer text-right">
                            <div style="float: right"><a id="fecharModalOn" style="cursor: pointer; color: #337ab7; font-weight: normal">Fechar</a></div>                                    
                        </div>
                    </div>
                </div>
            </div>
        ';
        break;
    
    case 'posVendasSalvar':
        $assunto  = filter_input(INPUT_POST, 'assunto');
        $resposta = filter_input(INPUT_POST, 'resposta');
        $dataAgen = filter_input(INPUT_POST, 'agenda');
        
        if (!empty($dataAgen)) {
            $agndamento = "'"._myfunc_dtos($dataAgen)."'";
        } else {
            $agndamento = 'NULL';
        }
        
        $sqlInsertPosVeda = "INSERT INTO $TPOS_VENDAS (dono, data_pos, assunto, resposta_operador, retornado, agendamento, cnpjcad) VALUES ('$dono', '".time()."', '$assunto', '$resposta', 'N', $agndamento, '".$_SESSION['ADMIN']['cnpj']."');";
        //exit($sqlInsertPosVeda);
        mysqli_query($conn_a, $sqlInsertPosVeda);
        //exit(var_dump($_POST));
        break;
    
    case 'posVendasAgendamentos':
        echo '
            <link rel="stylesheet" type="text/css" href="../api3/css/modalOnlineSistemas.css" rel="stylesheet">
            <div id="myModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Relatorio Pos Agendamentos Venda</h4>
                            <div style="float: right"><a id="fecharModalOn" style="cursor: pointer; color: #337ab7; font-weight: normal">Fechar</a></div>
                            <div style="clear: both"> </div>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                            <input class="data" type="text" name="dt1" id="dt1" placeholder="Data Inicial" /> &nbsp;&nbsp;<input class="data" type="text" name="dt2" id="dt2" placeholder="Data Final" /><button id="filtrarAgendaPosVenda" title="Filtrar Agendamentos pos venda" class="button">Filtrar</button>
                            <div id="respoAgendamentoPosVenda"></div>
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer text-right">
                            <div style="float: right"><a id="fecharModalOn" style="cursor: pointer; color: #337ab7; font-weight: normal">Fechar</a></div>                                    
                        </div>
                    </div>
                </div>
            </div>
            <script>$(document).ready(function () {  $(".data").datepicker(); })</script>
        ';
        break;
    
    case 'filtroAgendmantoPosVenda':
        $dat1   = filter_input(INPUT_POST, 'dt1');
        $dat2   = filter_input(INPUT_POST, 'dt2');
        
        // exit('FALSE#'.$dat1);
         list($d1, $m1, $a1) = preg_split('/[-.\/ ]/', $dat1);
         //exit('FALSE#'.$dat1.'<br>'.$m1.' -- '.$d1.' -- '.$a1);
         if (!checkdate($m1, $d1, $a1)) {
             exit('FALSE#Data inicial invalida!');
         }

         list($d2, $m2, $a2) = preg_split('/[-.\/ ]/', $dat2);
         if (!checkdate($m2, $d2, $a2)) {
             exit('FALSE#Data final invalida!');
         }

         $dataI = mktime(00, 00, 00, $m1, $d1, $a1);
         $dataF = mktime(23, 59, 59, $m2, $d2, $a2);

         $where = " WHERE agendamento >= '$dataI' AND agendamento <= '$dataF'";
        
        echo _getPosVendas($conn_a, $where);
        break;
        
    case 'mDevolucoes':
        
        $item    = filter_input(INPUT_POST, 'item');
        $vprod   = filter_input(INPUT_POST, 'vprod');
        $vdesc   = filter_input(INPUT_POST, 'vdesc');
        $voutro  = filter_input(INPUT_POST, 'voutro');
        $vicmsst = filter_input(INPUT_POST, 'vicmsst');
        $vbcst   = filter_input(INPUT_POST, 'vbcst');
        $vbcipi  = filter_input(INPUT_POST, 'vbcipi');
        $pipi    = filter_input(INPUT_POST, 'pipi');
        $picms   = filter_input(INPUT_POST, 'picms');
        $vipi    = filter_input(INPUT_POST, 'vipi');
        $vicms   = filter_input(INPUT_POST, 'vicms');
        $vbc     = filter_input(INPUT_POST, 'vbc');
        $ncm     = filter_input(INPUT_POST, 'ncm');   
        
        //
        if (!empty($item)) {
            //exit(var_dump($_POST));
            $sqlUpdateItemFluxo = "UPDATE $TITEM_FLUXO_TMP SET vprod = '$vprod', vdesc = '$vdesc', voutro = '$voutro', vicmsst = '$vicmsst', vbcst = '$vbcst', vbcipi = '$vbcipi', pipi = '$pipi', picms = '$picms', vipi = '$vipi', vicms = '$vicms', vbc = '$vbc', ncm = '$ncm' WHERE id = $item AND dono = '$dono'";
            //exit($sqlUpdateItemFluxo);
            mysqli_query($conn_a, $sqlUpdateItemFluxo);
        }
        
        $sqlItemFluxo = "SELECT * FROM $TITEM_FLUXO_TMP WHERE dono = '$dono'";
        $getItemFluxo = mysqli_query($conn_a, $sqlItemFluxo);
        $var = '';
        $a = 0;
        while ($iten = mysqli_fetch_assoc($getItemFluxo)) {
            $class = (($a % 2) == 0) ? "cel_par" : "cel_impar"; 
            $var .= '
                <tr id="item_'.$iten['id'].'" class="'.$class.' itemm">
                    <td style="text-align: center;">'.$iten['cprod'].'</td>
                    <td style="text-align: center;">'.$iten['xprod'].'</td>
                    <td style="text-align: center;">'.$iten['cfop'].'</td>
                    <td style="text-align: center;">'.$iten['ucom'].'</td>
                    <td style="text-align: center;">'.$iten['qcom'].'</td>
                    <td style="text-align: center;">'.$iten['vuncom'].'</td>
                    <td style="text-align: center;">'.$iten['vprod'].'</td>
                    <td style="text-align: center;">'.$iten['vbc'].'</td>
                    <td style="text-align: center;">'.$iten['vicms'].'</td>
                    <td style="text-align: center;">'.$iten['vipi'].'</td>
                    <td style="text-align: center;">'.$iten['picms'].'</td>
                    <td style="text-align: center;">'.$iten['pipi'].'</td>
                </tr>
            ';
            $a++;
        }
        
        $form = '
            
            <div>
                <label for="" style="font-size: 14px;">&nbsp;</label>
                <button type="submit" abbr="'.$dono.'" class="form-control btn btn-default atualizaNfDocTmp" style="width: 200px; float: right; cursor: pointer;">Atualizar Documento</button>
            </div>
            <br><br>
            <table cellspacing="0" cellpadding="0" class="tabela" style="width: 97%;">
                <thead>
                    <tr style="background-color: #AFB3B7; font-weight: bold ">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Produto</th>
                        <th style="text-align: center;">CFOP</th>
                        <th style="text-align: center;">UN</th>
                        <th style="text-align: center;">QUANT</th>
                        <th style="text-align: center;">Valor Unitario</th>
                        <th style="text-align: center;">Valor Total</th>
                        <th style="text-align: center;">B. Calculc. ICMS</th>
                        <th style="text-align: center;">Valor ICMS</th>
                        <th style="text-align: center;">Valolr IPI</th>
                        <th style="text-align: center;">Aliq ICMS</th>
                        <th style="text-align: center;">Aliq IPI</th>
                    </tr>
                </thead>
                <tbody>
                    '.$var.'
                </tbody>
            </table>
            <br>
            <div>
                <label for="" style="font-size: 14px;">&nbsp;</label>
                <button type="submit" abbr="'.$dono.'" class="form-control btn btn-default atualizaNfDocTmp" style="width: 200px; float: right; cursor: pointer;">Atualizar Documento</button>
            </div>
            <br>
        ';
        
        
        
        echo 'Manuten&ccedil;&atilde;o de Devolu&ccedil;&otilde;es||'.$form;
        // sempre vai paritr o que esta sendo envaido em duas partes o que vai antes e o que vai depois de ||
        // o que vai antes vai no titulo do modal
        // o que vai depois vai no corpro, pode ser um formulario ou um relatorio
        // o que importa é que deve veser escrito e não dar um return.
        break;
        
    case 'getItem':
        $sqlItemFluxo = "SELECT id, xprod, dono, vbc, vicms, vipi, picms, pipi, vbcipi, vbcst, vicmsst, voutro, vdesc, vprod, ncm FROM $TITEM_FLUXO_TMP WHERE id = '$dono'";
        $getItemFluxo = mysqli_query($conn_a, $sqlItemFluxo);
        $item = mysqli_fetch_assoc($getItemFluxo);
        
        $form = '
            <div style="margin-left: 45px;">
            
                <div style="width: 150px; float: left">
                    <label for="vbc" style="font-size: 14px;">B. Calculo ICMS:</label>
                    <input type="text" name="vbc" id="vbc" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['vbc'].'">
                </div>
                <div style="width: 150px; float: left; margin-left: 10px;">
                    <label for="vicms"  style="font-size: 14px;">Valor ICMS:</label>
                    <input type="text" name="vicms" id="vicms" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['vicms'].'">
                </div>
                <div style="width: 150px; float: left; margin-left: 10px;">
                    <label for="picms" style="font-size: 14px;">Aliq. ICMS:</label>
                    <input type="text" name="picms" id="picms" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['picms'].'">
                </div>
                
                <div style="clear: both"></div>
                <div style="width: 150px; float: left; ">
                    <label for="vbcipi" style="font-size: 14px;">Base IPI:</label>
                    <input type="text" name="vbcipi" id="vbcipi" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['vbcipi'].'">
                </div>
                <div style="width: 150px; float: left; margin-left: 10px;">
                    <label for="vipi" style="font-size: 14px;">Valor IPI:</label>
                    <input type="text" name="vipi" id="vipi" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['vipi'].'">
                </div>
                <div style="width: 150px; float: left; margin-left: 10px;">
                    <label for="pipi" style="font-size: 14px;">Aliq. IPI:</label>
                    <input type="text" name="pipi" id="pipi" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['pipi'].'">
                </div>
                
                <div style="clear: both"></div>
                <div style="width: 150px; float: left;">
                    <label for="vbcst" style="font-size: 14px;">Base ST:</label>
                    <input type="text" name="vbcst" id="vbcst" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['vbcst'].'">
                </div>
                <div style="width: 150px; float: left; margin-left: 10px;">
                    <label for="vicmsst" style="font-size: 14px;">Valor ST:</label>
                    <input type="text" name="vicmsst" id="vicmsst" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['vicmsst'].'">
                </div>
                
                <div style="clear: both"></div>
                <div style="width: 150px; float: left;">
                    <label for="voutro" style="font-size: 14px;">Outras Despesas:</label>
                    <input type="text" name="voutro" id="voutro" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['voutro'].'">
                </div>
                <div style="width: 150px; float: left; margin-left: 10px;">
                    <label for="vdesc" style="font-size: 14px;">Desconto:</label>
                    <input type="text" name="vdesc" id="vdesc" class="form-control" onkeyup="somenteNumeros(this);" value="'.$item['vdesc'].'">
                </div>
                 <div style="width: 150px; float: left; margin-left: 10px;">
                    <label for="vprod" style="font-size: 14px;">Valor Item:</label>
                    <input type="hidden" name="id" id="id" value="'.$item['id'].'">
                    <input type="hidden" name="dono" id="dono" value="'.$item['dono'].'">
                    <input type="text" name="vprod" id="vprod" class="form-control" value="'.$item['vprod'].'">
                </div>
                <div style="clear: both"></div>
                <form name="flancamento">
                <div>
                    <label for="ncm" style="font-size: 14px;"><a title="Click e veja a lista de NCM" href="logado.php?ac=ncm_consulta&campo=ncm&campo2=ncmdescricao&site=@ CONSULTA NCM" onclick="NewWindow(this.href, \'name\', \'450\', \'550\', \'yes\');return false;">NCM: </a><span>*</span>: </label>
                    <div style="margin-botton: 10px;">
                        <input id="ncm" name="ncm" style="width: 100px; font: normal 8x verdana; float: left;" type="text" READONLY=YES  maxlength="8" class="form-control" value="'.$item['ncm'].'">
                        <input id="ncmdescricao" name="ncmdescricao" style="width: 360px; float: left; margin-left: 10px;" type="text" READONLY=YES maxlength="40" class="form-control">
                    </div>
                </div>
                </form>
                <br>
                <div>
                    <label for="" style="font-size: 14px;">&nbsp;</label>
                    <button type="submit" id="salvarItemMDev" abbr="ItemMDevelocoes" class="form-control btn btn-default" style="width: 150px; float: right; cursor: pointer;">Salvar</button>
                </div>
                <br>
            </div>
        ';
        echo $item['xprod'].'||'.$form;
        break;
    
    case 'atualizaNfDocTmp':
        $sqlItemFluxo = "SELECT sum(voutro) as total FROM $TITEM_FLUXO_TMP WHERE dono = '$dono'";
        $getItemFluxo = mysqli_query($conn_a, $sqlItemFluxo);
        $Tvoutro = mysqli_fetch_assoc($getItemFluxo);
        
        mysqli_query($conn_a, "UPDATE $TNFDOCUMENTOS_TMP SET voutronf = '".$Tvoutro['total']."' WHERE dono = '$dono'");
        
        break;
    
    case 'formCalcados':
        
        $m_grupoproduto = MATRIZ::matriz_grupoproduto('');
        $chk_grupoproduto[$xgrupoproduto] = 'selected';
        $option = '<option style="width: 150px;" value=""></option>';
        foreach ($m_grupoproduto as $k => $v) {
            $option .= '<option value="'.$k.'" '.$chk_grupoproduto[$k].'>'.utf8_encode($v).'</option>';
        }
        $comb = '<select name="grupoproduto" id="grupoproduto" class="form-control campo8">'.$option.'</select>';
        
        $inf = filter_input(INPUT_POST, 'info');
        if ($inf == '1') {  // OPTANTE PELO SIMPLES
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
            $opt2 .= '<option value="'.$k.'" '.$chk_cst_csosn[$k].'>'.$k.'  '.utf8_encode($v).'</option>';
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
            $nums .= '<div style="width: 40px; float: left; margin-left: 5px;"><label for="num'.$a.'" style="background-color: #00FFFF">'.$a.'</label><br><input type="checkbox" name="numeroCalcado[]" id="num'.$a.'" value="'.$a.'"></div>';
        }
        
        $form = '
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
                <div>
                    <label for="descricao" style="font-size: 14px;">Descri&ccedil;&atilde;o</label>
                    <input type="text" name="descricao" id="descricao" class="form-control campo3">
                </div>
                <div>
                    <label for="fabricante" style="font-size: 14px;">Fabricante:</label>
                    <input type="text" name="fabricante" id="fabricante" class="form-control campo4">
                </div>
                <form name="flancamento">
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
        ';
        echo $form;
        break;
        
    case 'salvarCalcados':
        //echo var_dump($_POST);
        
        $getNconta = mysqli_query($conn_a, "SELECT conta, descricao, cast(conta as decimal) as nconta FROM $TPRODUTOS ORDER BY nconta DESC LIMIT 1");
        if (mysqli_num_rows($getNconta) > 0) {
            $ultimoNconta = mysqli_fetch_assoc($getNconta);
            
            $add1 = substr($ultimoNconta['nconta'], 0, 6) + 1;    // Tamanho de 6 caracteres como padr�o
            $mais1 = _myfunc_zero_a_esquerda($add1, 6);
        }
        
        $descricao = utf8_decode(BASICO::trata_var($camp3, string));
//echo 'Variavel Descricao: '.$descricao;
//echo 'Variavel campo3: '.$camp3;
 //       exit();
        $valor = str_replace(',', '.', str_replace('.', '', $camp9));
        $fabricante = utf8_decode(BASICO::trata_var($campo4, string));
        $unidade = (!empty($camp12)) ? $camp12 : 'UN';
        
        $cnpjcpfcad = '';
        $cst_pis = '49';
        $cst_cofins = '49';
        $cst_origem = '0';
        $icms_letra = 'C';        // Correspondente a 18%
        $icms_out_letra = 'C';      // Correspondente a 18%
        $cst_icms = $camp7; 
        $cst_icms_out = $camp7;
        $csosn = $camp7;
        $csosn_out = $csosn;
        $tipoitem = '00';        // Mercadoria de revenda
        $grupoproduto = $camp8;  // C�digo do grupo de produtos
        $ncm = $camp5;  
        
        if (empty($camp5)) {
            exit("FALSE||Campos NCM não pode estar vazio!");
        }
        
        if (empty($camp10)) {
            exit("FALSE||Campos CEST não pode estar vazio!");
        }
        
        if (empty($camp7)) {
            exit("FALSE||Campo CST ou CSOSN não pode estar vazio!");
        }
        
        $numeros = filter_input(INPUT_POST, 'num');
        $nu  = substr($numeros, 0, -1);
       
        if (!empty($numeros)) { 
            $qtos = explode(',', $nu);
//exit('dentro do if');
            foreach ($qtos as $k=>$v) {
                if (!empty($v)) {
                    $sql = "
                        INSERT INTO $TPRODUTOS
                            (conta, descricao, valor, fabricante, unidade, cnpjcpfcad, cst_pis, cst_cofins, cst_origem, icms_letra, icms_out_letra, cst_icms, cst_icms_out, csosn, csosn_out, tipoitem, grupoproduto, ncm, datacad, conta_cod_similar, cest) 
                        VALUES
                            ('$mais1-$v', '$descricao-$v', '$valor', '$fabricante', '$unidade', '".$_SESSION['ADMIN']['cnpj']."', '$cst_pis', '$cst_cofins', '$cst_origem', '$icms_letra', '$icms_out_letra', '$cst_icms', '$cst_icms_out', '$csosn', '$csosn_out', '$tipoitem', '$grupoproduto', '$ncm', '".time()."', '$camp2', '$camp10')
                    ";
                    //echo 'FALSE||'.$sql;
                    if(!mysqli_query($conn_a, $sql)) {
                        exit("FALSE||Falha ao inserir calçados entre em contato com o suporte!");
                    }
                }
            }
        }
        echo "TRUE||Calçados inseridos com sucesso!";
        break;
        
    case 'ListClientes':
        $sqlClientes = "SELECT cnpj, razao, fantasia, cidade, contato, box_flag, obs FROM $TCNPJCPF WHERE box_flag = 1 ORDER BY fantasia, box_flag";
        $getClientes = mysqli_query($conn_a, $sqlClientes);
        $qtde1 = mysqli_num_rows($getClientes);
        $sqlClientes2 = "SELECT cnpj, razao, fantasia, cidade, contato, box_flag, obs FROM $TCNPJCPF WHERE box_flag = 2 ORDER BY fantasia, box_flag";
        $getClientes2 = mysqli_query($conn_a, $sqlClientes2);
        $qtde2 = mysqli_num_rows($getClientes2);
        
        $var = '';
        $b = $c = $d = $e = 0;
        while ($iten = mysqli_fetch_assoc($getClientes)) {
            $class = (($c % 2) == 0) ? "cel_par" : "cel_impar";
            $sqlAnota = "SELECT * FROM $TCRM_PESQUISA_RESPOSTAS WHERE dono = '".$iten['cnpj']."'";
            $getAnota = mysqli_query($conn_a, $sqlAnota);
            $a = mysqli_fetch_assoc($getAnota);
            if (mysqli_num_rows($getAnota) && !empty($a['dataatu'])) {
                $d++;
            }
            
            $dtP = _myfunc_stod($a['data']);
            $dtA = _myfunc_stod($a['dataatu']);
            
            $var .= '
                <tr id="cliente_'.$iten['cnpj'].'" class="'.$class.'">
                    <td style="text-align: center;"></td>
                    <td style="text-align: left;">'.utf8_encode($iten['fantasia']).'</td>
                    <td style="text-align: left;">'.utf8_encode($iten['cidade']).'</td>
                    <td style="text-align: left;"><input type="text" name="data_pre" id="data_pre_'.$iten['cnpj'].'" value="'.$dtP.'" onfocus="displayCalendar(data_pre,\'dd/mm/yyyy\',this);this.value=\'\';" onkeyup="this.value = mascara_global(\'##/##/####\', this.value);"></td>
                    <td style="text-align: left;"><input type="text" name="data_atu" id="data_atu_'.$iten['cnpj'].'" value="'.$dtA.'" onfocus="displayCalendar(data_atu,\'dd/mm/yyyy\',this);this.value=\'\';" onkeyup="this.value = mascara_global(\'##/##/####\', this.value);"></td>
                    <td style="text-align: left;"><input type="text" name="OBS" id="OBS_'.$iten['cnpj'].'" value="'.$a['obs'].'"></td>
                    <td style="text-align: center;"><button title="Salvar" class="button SalvarInfoAtualizaCliente">Salvar</button></td>
                </tr>
            ';
            $c++;
        } 
        
        $var .= '
            <tr>
                <td style="text-align: center;" colspan="7">INICIDO DA LISTA 2</td>
            </tr>
        ';
        while ($iten = mysqli_fetch_assoc($getClientes2)) {
            $class = (($b % 2) == 0) ? "cel_par" : "cel_impar";
            $sqlAnota = "SELECT * FROM $TCRM_PESQUISA_RESPOSTAS WHERE dono = '".$iten['cnpj']."'";
            $getAnota = mysqli_query($conn_a, $sqlAnota);
            $a = mysqli_fetch_assoc($getAnota);
            if (mysqli_num_rows($getAnota) && !empty($a['dataatu'])) {
                $e++;
            }
            $dtP = _myfunc_stod($a['data']);
            $dtA = _myfunc_stod($a['dataatu']);
            
            $var .= '
                <tr id="cliente_'.$iten['cnpj'].'" class="'.$class.'">
                    <td style="text-align: center;"></td>
                    <td style="text-align: left;">'.utf8_encode($iten['fantasia']).'</td>
                    <td style="text-align: left;">'.utf8_encode($iten['cidade']).'</td>
                    <td style="text-align: left;"><input type="text" name="data_pre" id="data_pre_'.$iten['cnpj'].'" value="'.$dtP.'" onfocus="displayCalendar(data_pre,\'dd/mm/yyyy\',this);this.value=\'\';" onkeyup="this.value = mascara_global(\'##/##/####\', this.value);"></td>
                    <td style="text-align: left;"><input type="text" name="data_atu" id="data_atu_'.$iten['cnpj'].'" value="'.$dtA.'" onfocus="displayCalendar(data_atu,\'dd/mm/yyyy\',this);this.value=\'\';" onkeyup="this.value = mascara_global(\'##/##/####\', this.value);"></td>
                    <td style="text-align: left;"><input type="text" name="OBS" id="OBS_'.$iten['cnpj'].'" value="'.$a['obs'].'"></td>
                    <td style="text-align: center;"><button title="Salvar" class="button SalvarInfoAtualizaCliente">Salvar</button></td>
                </tr>
            ';
            $b++;
        }
        
        $form = '
            <br>
            <table cellspacing="0" cellpadding="0" class="tabela" style="width: 97%;">
                <thead>
                    <tr><td colspan="7">Qtd Total Flag 1: '.$qtde1.' &nbsp;&nbsp;Atualizadas Flag 1: '.$d.'&nbsp;&nbsp;&nbsp;&nbsp;Qtd Total Flag 2: '.$qtde2.'&nbsp;&nbsp;Atualizadas Flag 2: '.$e.'</td></tr>
                    <tr style="background-color: #AFB3B7; font-weight: bold ">
                        <td style="text-align: center;">#</td>
                        <td style="text-align: center;">FANTASIA</td>
                        <td style="text-align: center;">CIDADE</td>
                        <td style="text-align: center;">DATA PRE</td>
                        <td style="text-align: center;">DATA ATU</td>
                        <td style="text-align: center;">OBS</td>
                        <td style="text-align: center;"></td>
                    </tr>
                </thead>
                <tbody>
                    '.$var.'
                </tbody>
            </table>
            <br>
        ';
        echo $form;
        break;
        
    case 'SalvarInfoCliente':
        
        $dtPre = filter_input(INPUT_POST, 'data_pre');
        $dtAtu = filter_input(INPUT_POST, 'data_atu');
        $OBs = filter_input(INPUT_POST, 'obs');
        
        $sqlInfo = "SELECT * FROM $TCRM_PESQUISA_RESPOSTAS WHERE dono = '$dono'";
        $getInfo = mysqli_query($conn_a, $sqlInfo);
        
        $up = "dono = '$dono'";
        $cam = $val = '';
        if (!empty($dtPre)) {
            $dataPre = _myfunc_dtos($dtPre);
            $up  .= ", data = '$dataPre'";
            $cam .= ', data';
            $val .= ", '".$dataPre."'";
        }

        if (!empty($dtAtu)) {
            $dataAtu = _myfunc_dtos($dtAtu);
            $up  .= ", dataatu = '$dataAtu'";
            $cam .= ', dataatu';
            $val .= ", '".$dataAtu."'";
        }

        if (!empty($OBs)) {
            $up  .= " , obs = '$OBs'";
            $cam .= ', obs';
            $val .= ", '".$OBs."'";
        }
        
        if (mysqli_num_rows($getInfo)) {
            if (mysqli_query($conn_a, "UPDATE $TCRM_PESQUISA_RESPOSTAS SET $up WHERE dono = '$dono'") ) {
                echo "Cliente atualizado com sucesso!";
            } else {
                echo "Falha ao atualizar cliente!";
            }
        } else {
            if (mysqli_query($conn_a, "INSERT INTO $TCRM_PESQUISA_RESPOSTAS (dono, cnpjcpfatu, cnpjcpfseg$cam) VALUES ('$dono', '".$_SESSION['ADMIN']['cnpj']."', '$dono' $val)")  ) {
                echo "Cliente atualizado com sucesso!";
            } else {
                echo "Falha ao atualizar cliente!";
            }
        }
        break;
        
    case 'RestartSession':
        $consultarDb = mysqli_query($conn_a, "SELECT now() as agora");
        $re = mysqli_fetch_array($consultarDb);
        if (!empty($re['agora'])) {
            $dt = new DateTime($re['agora']);
            echo $dt->format('d/m/Y h:i:s');
        } else {
            echo "Problema na Consulta!";
        }
        break;
        
    case 'Redocs':
        //echo var_dump($_POST);
        $novoLan = BASICO::_numero_aleatorio('LAN');
        
        $docs = $lancam = $itens = $camposNfDocs = $camposLanc = $camposItemF = $valoresNfDocs = $valoresLanc = $valoresItemF = array();
        
        $sqlNfdocs = "SELECT * FROM $TNFDOCUMENTOS_TMP WHERE dono = '$dono'";
        $getNfdocs = mysqli_query($conn_a, $sqlNfdocs);
        while ($Nfdocs = mysqli_fetch_assoc($getNfdocs)) {
            foreach ($Nfdocs as $k=>$v) {
                if ($k <> 'id_hotel_local') { // não copia o campo id_hotel_local
                    $camposNfDocs[] = $k;
                    if ($k == 'dono') {
                        $valoresNfDocs[] = "'".$novoLan."'";
                    } else {
                        $valoresNfDocs[] = "'".$v."'";
                    }
                }
            } 
            mysqli_query($conn_a, "INSERT INTO $TNFDOCUMENTOS_TMP (". implode(', ', $camposNfDocs).") values (".implode(', ', $valoresNfDocs).")");
        }
        
        $sqlLanc = "SELECT * FROM $TLANCAMENTOS_TMP WHERE dono = '$dono' ORDER BY id ASC";
        //echo $sqlLanc.'<br><br>';
        $getLanc = mysqli_query($conn_a, $sqlLanc);
        while ($Lanca = mysqli_fetch_assoc($getLanc)) {
            // pega a naturza de operacao verificando a conta de servico
            $natL = getNatureza($Lanca['codnat'], $conn_a, $TNATUREZAOPERACAO);
            
            if (empty($natL)) {
                mysqli_query($conn_a, "DELETE FROM $TNFDOCUMENTOS_TMP WHERE dono = '$novoLan'");
                exit("Falha ao tentar executar RE-DOCS inconsistencia na Natureza da Operacao - Lancamento!");
            } 
                
            // se a conta da natureza igual a contac do lancamento, é serviço e não preciso duplicar
            if ($natL <> $Lanca['contac']) {
                if ($Lanca['contad'] == '') { // não deixa pegar quando é recebimento de alguma maneira o campo contad esta vazio ai "duplica"
                    foreach ($Lanca as $k=>$v) {
                        if ($k <> 'id') { // não copia o campo id pois é auto_increment
                            $camposLanc[] = $k;
                            if ($k == 'dono') {
                                $valoresLanc[] = "'".$novoLan."'";
                            } else {
                                if ($k == 'donoanterior') { // aki esta preenchendo o dono anterior com o lan antigo, conforme solicitado
                                    $valoresLanc[] = "'".$dono."'";    
                                } else {
                                    $valoresLanc[] = "'".$v."'";
                                }
                            }
                        }
                    }

                    $novoLancamento = "INSERT INTO $TLANCAMENTOS_TMP (". implode(', ', $camposLanc).") values (".implode(', ', $valoresLanc).")";
                    $camposLanc = $valoresLanc = array();
                    mysqli_query($conn_a, $novoLancamento);
                }
            } else {
                // remove os lancamentos de produtos do dono anterior
                $deleteLancamentos = "DELETE FROM $TLANCAMENTOS_TMP where dono = '$dono' AND contac <> '$natL' AND contad = ''";
                //echo $deleteLancamentos.'<br><br>';
                mysqli_query($conn_a, $deleteLancamentos);
            }
        }
        
        $sqlItemF = "SELECT * FROM $TITEM_FLUXO_TMP WHERE dono = '$dono'";

        $getItemF = mysqli_query($conn_a, $sqlItemF);
        while ($ItemF = mysqli_fetch_assoc($getItemF)) {
            // pega a naturza de operacao verificando a conta de servico
            $natI = getNatureza($ItemF['cod_nat'], $conn_a, $TNATUREZAOPERACAO);
            
            // se a conta da natureza igual a conta_plano do item_fluxo, é serviço e não preciso duplicar
            if ($natI <> $ItemF['conta_plano']) {
                foreach ($ItemF as $k=>$v) {
                    if ($k <> 'id') {// não copia o campo id pois é auto_increment
                        $camposItemF[] = $k;
                        if ($k == 'dono') { // if pra colocar o novo dono ao duplicar
                            $valoresItemF[] = "'".$novoLan."'";
                        } else {
                            if ($k == 'donoanterior') { // aki esta preenchendo o dono anterior com o lan antigo, conforme solicitado
                                $valoresItemF[] = "'".$dono."'";    
                            } else {
                                $valoresItemF[] = "'".$v."'";
                            }
                        }
                    }
                }
                mysqli_query($conn_a, "INSERT INTO $TITEM_FLUXO_TMP (". implode(', ', $camposItemF).") values (".implode(', ', $valoresItemF).")");
                $camposItemF = $valoresItemF = array();
            } else {
                // remove os produtos do dono anterior
                mysqli_query($conn_a, "DELETE FROM $TITEM_FLUXO_TMP where dono = '$dono' AND conta_plano <> '$natI' ");
            }
        }
        
        echo $novoLan;
        
//        
        break;
}

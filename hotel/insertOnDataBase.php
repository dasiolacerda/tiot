<?php

    session_start();      
    $cnpjcpf_segmento = $info_cnpj_segmento = $_SESSION['ADMIN']['cnpj_segmento']; 
    $info_segmento =  $_SESSION['ADMIN']['cnpj_segmento']; 
    //exit(var_dump($_SESSION['ADMIN']));
    $tipo = filter_input(INPUT_POST, 'tipo');   
        
    require_once('../includes/myfuncoes.php');
    require_once('../_conecta.php');
    require_once('../_tabelas.php');  
    require_once('../_metodos/basico.php');
        //exit(var_dump($info_cnpj_segmento));
    switch ($tipo) {
        case 'infoHospedes':
            $placa      = filter_input(INPUT_POST, 'placa');
            $pla        = (!empty($placa)) ? "placatransp='$placa'" : "";
            $origem     = utf8_encode(filter_input(INPUT_POST, 'origem'));
            $destino    = utf8_encode(filter_input(INPUT_POST, 'destino'));
            $saida      = filter_input(INPUT_POST, 'dataS');
            $dtSaida    = _myfunc_dtos($saida);
            $hospedes   = filter_input(INPUT_POST, 'hospedes');
            $dono       = filter_input(INPUT_POST, 'dno');
            $link       = filter_input(INPUT_POST, 'chklink');
            
            $contasr    = filter_input(INPUT_POST, 'conta');
            $descricaor = filter_input(INPUT_POST, 'descr');
            $unidadesr  = filter_input(INPUT_POST, 'uni');
            $valorsr    = filter_input(INPUT_POST, 'vlr');
            $clistserv  = filter_input(INPUT_POST, 'cli');
            $cst        = filter_input(INPUT_POST, 'cst');
            $csosn      = filter_input(INPUT_POST, 'cso');
            //exit(var_dump($_POST));
            if($hospedes < 1) {
                exit('false#Numero de Hospedes invalido!');
            }

            //atualiza informa��es do cliente
            $query = "UPDATE $TNFDOCUMENTOS_TMP SET placatransp='$placa', cidade_origem='$origem', cidade_destino='$destino', datasaida='$dtSaida', hospedes='$hospedes', datacad = '".time()."' WHERE id_hotel_local='$contasr'";
            //exit($query);
            if (mysqli_query($conn_a, $query)) {
                
                // pega natureza de opera��o do servi�o de hotelaria
                $sqlnat = "SELECT codnat, contaservicos, contaprodutos FROM $TNATUREZAOPERACAO WHERE codnat='HOTE'";
                $rnat = mysqli_query($conn_a, $sqlnat);
                $infonat = mysqli_fetch_assoc($rnat);

                $conta_plano = (!empty($infonat['contaservicos'])) ? $infonat['contaservicos'] : "";
                
                $cpfUsuarioLogado = $_SESSION['ADMIN']['cnpj'];
                $tipo_lancamento = 'SERVICOS';
                $modbc = '3';
                $movimento = 'RECEITAS';
                $flag_mult = '-1';
                $qcom = '1.0000';
                $cnpjcpfcli = ''; // Deve ser analisado posteriormente
                # Insert na tabela $TITEM_FLUXO_TMP
                $campos_tabela = "data, datacad, dataatu, cnpjcpf, cnpjcpfcad, cnpjcpfatu, cnpjcpfseg, cnpjcpfvendedor, dono, movimento, cprod, xprod, ucom, qcom, vuncom, vuncom_normal, vprod, utrib, qtrib, vuntrib, cst, csosn, tipo_lancamento, modbc, clistserv, conta_plano, flag_mult, ip";
                $campos_insert = "'".time()."', '".time()."', '".time()."', '$cnpjcpfcli', '$cpfUsuarioLogado', '$cpfUsuarioLogado', '$info_cnpj_segmento', '$cpfUsuarioLogado', '$dono', '$movimento', '$contasr', '$descricaor', '$unidadesr', '$qcom', '$valorsr', '$valorsr', '$valorsr', '$unidadesr', '$qcom', '$valorsr', '$cst', '$csosn', '$tipo_lancamento', '$modbc', '$clistserv', '$conta_plano', '$flag_mult', '".$_SERVER["REMOTE_ADDR"]."'";
                $sql = "INSERT INTO $TITEM_FLUXO_TMP ($campos_tabela) VALUES ($campos_insert)";
            
                if (mysqli_query($conn_a, $sql)) {
                    //exit('false#teste de msg '.$updtLancamentos);
                    echo "deucerto#logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink=$link&codnat=HOTE&dono=$dono";
                } else {
                    echo "false#Falha ao inserir item de fluxo!";
                }

            } else {
                echo 'Erro ao tentar atualizar informa��es';
            }
            
            //exit(var_dump($_POST));
            break;
            
        case 'transferenciaQuarto':
            //exit(var_dump($_POST));
            $quartoAtual = filter_input(INPUT_POST, 'qtoAtual');
            $qtoAntigo   = filter_input(INPUT_POST, 'qtoAntigo');
            $qtoNovo     = filter_input(INPUT_POST, 'qtoNovo');
            
            $sql_UpdateNfDocs = "UPDATE $TNFDOCUMENTOS_TMP SET id_hotel_local='$qtoNovo' WHERE dono='$qtoAntigo'";
            $sqlServicoAtual  = "SELECT cprod, xprod, qcom, vuncom FROM $TITEM_FLUXO_TMP WHERE dono = '$qtoAntigo' AND cprod = '$quartoAtual'";
            $sqlServicoNovo   = "SELECT conta, descricao, valor FROM $TSERVICOS WHERE conta = '$qtoNovo'";
            
            //exit($sql_UpdateNfDocs); 
            if (mysqli_query($conn_a, $sql_UpdateNfDocs)) {
                mysqli_query($conn_a, "UPDATE $TAPARTAMENTOS SET dataatu='$dtos', cnpjcpfatu='$info_cnpj', status='L' WHERE id_hotel_local = '$quartoAtual'");
                $getServicoAtual = mysqli_query($conn_a, $sqlServicoAtual);
                $getServicoNovo  = mysqli_query($conn_a, $sqlServicoNovo);
                $sA = mysqli_fetch_assoc($getServicoAtual);
                $sN = mysqli_fetch_assoc($getServicoNovo);
                
                $updateItemFluxo = mysqli_query($conn_a, "UPDATE $TITEM_FLUXO_TMP SET cprod = '".$sN['conta']."', xprod = '".$sN['descricao']."', vuncom = '".$sN['valor']."', vuntrib = '".$sN['valor']."' WHERE dono = '$qtoAntigo' AND cprod = '$quartoAtual'");
                if ($updateItemFluxo) {
                    $msg = "|TROCOUQUARTO|".$v."|:;|CPROD|".$sA['cprod']."|:;|XPROD|".$sA['xprod']."|:;|QTDO|".$sA['cprod']."|:;|VRO|".$sA['vuncom']."|:;|QTDN|".$sN['conta']."|:;|VRN|".$sN['valor']."|:;";
                    $sqlItemLog = "INSERT INTO $TLOGSMENSAGEM (mensagem, datacad, cnpjcpfcad, cnpjcpfseg, quescript, dataatu, ip) VALUES ('|DONO|".$qtoAntigo."|:;|CNPJCPF|".$_POST['cnpjcpf']."|:;".$msg."', '".time()."', '".$_SESSION['ADMIN']['cnpj']."', '".$_SESSION['ADMIN']['cnpj_segmento']."', 'TROCOUQUARTO-RECEPCAO', '".time()."', '".$_SERVER["REMOTE_ADDR"]."');";
                    mysqli_query($conn_a, $sqlItemLog);
                }
                
                echo "true#logado.php?ac=hotel_recepcao";
            } else {
                echo "false#Problemas ao transferir quarto, por favor entre em contato com o suporte!";
            }            

            break;
            
        case 'aptoReserva':

            $dataInicial = filter_input(INPUT_POST, 'data_in');
            $dataFinal  = filter_input(INPUT_POST, 'dataS');
            $data_inicial = _myfunc_dtos($dataInicial)+1;
            $data_final = _myfunc_dtos($dataFinal)-1;
            
            $filtro_periodo="('$data_inicial' >= entrada && '$data_inicial' <= saida) || ('$data_final' >= entrada && '$data_final' <= saida) || (entrada >= '$data_inicial' && saida <= '$data_final')";
            // nessa linha abaixo esta com o nome da tabela digitado e não usando variavel pois não esta no arquivo de tabelas informado na ultima versão a proxima linha esta com a variavel correta
            $sqlReservas = "SELECT id_hotel_local, classifica, status FROM $TAPARTAMENTOS WHERE id_hotel_local NOT IN(SELECT id_hotel_local FROM apartamentos_reservas WHERE $filtro_periodo GROUP BY id_hotel_local)";
            //$sqlReservas = "SELECT id_hotel_local, classifica, status FROM $TAPARTAMENTOS WHERE id_hotel_local NOT IN(SELECT id_hotel_local FROM $TAPARTAMENTOS_RESERVAS WHERE $filtro_periodo GROUP BY id_hotel_local)";
//            exit($sqlReservas);
            $aptos = mysqli_query($conn_a, $sqlReservas);
            $statu = array('M', 'D');
            while($vagos = mysqli_fetch_assoc($aptos)) {
                if (!in_array($vagos['status'], $statu)) {
                    //echo $vagos['status'];
                    echo "<option value='".$vagos['id_hotel_local']."-".$vagos['classifica']."'>".$vagos['id_hotel_local']."-".$vagos['classifica']."</option>"; 
                }
            }
            break;
            
        case 'insereReserva':
            //exit(var_dump($_POST));
            
            $hospede = utf8_decode(filter_input(INPUT_POST, 'hospede'));
            $quarto = filter_input(INPUT_POST, 'qto'); 
            $dtE = filter_input(INPUT_POST, 'dataI');
            $dtS = filter_input(INPUT_POST, 'dataS');
            $dataE = _myfunc_dtos($dtE);
            $dataS = _myfunc_dtos($dtS);
            $obs = utf8_decode(filter_input(INPUT_POST, 'obs'));
            $apto = explode('-', $quarto);
            //$rclassifica = substr(strrchr($classifica, "-"), 1);
            //$rapto = strstr($classifica, '-', true);
            //$obs = $_POST['obs'];

            $mens_data = '';
            IF ($dataE < $dtos and $dtE <> '') {
                $mens_data = $mens_data . "$dtE - Data reserva incoreta!";
                $dataE = '';
                $dataS = '';
            }
            IF ($dataS < $dtos and $dtS <> '') {
                $mens_data = $mens_data . "$dtS - Data reserva incorreta";
                $dataE = '';
                $dataS = '';
            }
            IF ($dataE > $dataS) {
                $mens_data = $mens_data . "$dtE - Datas invertidas";
                $dataE = '';
                $dataS = '';
            }

            if ($mens_data <> '') {
                exit($mens_data);
            }
            
            $filtro_periodo="('$dataE' >= entrada && '$dataE' <= saida) || ('$dataS' >= entrada && '$dataS' <= saida) || (entrada >= '$dataE' && saida <= '$dataS')";
            // nessa linha abaixo esta com o nome da tabela digitado e não usando variavel pois não esta no arquivo de tabelas informado na ultima versão a proxima linha esta com a variavel correta
            $sqlReservas = "SELECT id_hotel_local FROM $TAPARTAMENTOS WHERE id_hotel_local NOT IN(SELECT id_hotel_local FROM apartamentos_reservas WHERE $filtro_periodo GROUP BY id_hotel_local)";
            //$sqlReservas = "SELECT id_hotel_local FROM $TAPARTAMENTOS WHERE id_hotel_local NOT IN(SELECT id_hotel_local FROM $TAPARTAMENTOS_RESERVAS WHERE $filtro_periodo GROUP BY id_hotel_local)";
            $getReservas = mysqli_query($conn_a, $sqlReservas);
            $qtoDisp = mysqli_fetch_assoc($getReservas);
            
            if (in_array($apto[1], $qtoDisp)) {
                // nessa linha abaixo esta com o nome da tabela digitado e não usando variavel pois não esta no arquivo de tabelas informado na ultima versão a proxima linha esta com a variavel correta
                $query = "INSERT INTO apartamentos_reservas VALUES('".$apto[1]."', '$dataE', '$dataS', '$hospede', '$obs', '".$_SESSION['ADMIN']['cnpj']."', '$dtos', '','','".$apto[0]."')";
                //$query = "INSERT INTO $TAPARTAMENTOS_RESERVAS VALUES('".$apto[1]."', '$dataE', '$dataS', '$hospede', '$obs', '".$_SESSION['ADMIN']['cnpj']."', '$dtos', '','','".$apto[0]."')";
                if (mysqli_query($conn_a, $query)) {
                    echo 'TRUE';
                } else {
                    echo 'Falha ao reserver quarto, por favor entre em contato com suporte! '.mysqli_error($conn_a);
                }
            } else {
                echo 'Falha ao reserver quarto, por favor escolha um quarto disponivel! ';
            }
            
            
            
            
            break;
        
        case 'removeReserva':
            
            $reservas = filter_input(INPUT_POST, 'reservas');
            $r = substr($reservas, 0, -1);
            
            if (!empty($reservas)) {
                $qtos = explode(',', $r);
                
                foreach ($qtos as $k=>$v) {
                    if (!empty($v)) {
                        // nessa linha abaixo esta com o nome da tabela digitado e não usando variavel pois não esta no arquivo de tabelas informado na ultima versão a proxima linha esta com a variavel correta
                        $sqlQtoReserva = "SELECT id_hotel_local FROM apartamentos_reservas WHERE id =$v";
                        //$sqlQtoReserva = "SELECT id_hotel_local FROM $TAPARTAMENTOS_RESERVAS WHERE id =$v";
                        $getQtoReserva = mysqli_query($conn_a, $sqlQtoReserva);
                        $qto = mysqli_fetch_assoc($getQtoReserva);
                        
                        // nessa linha abaixo esta com o nome da tabela digitado e não usando variavel pois não esta no arquivo de tabelas informado na ultima versão a proxima linha esta com a variavel correta
                        $sqlDeletReservas = "DELETE FROM apartamentos_reservas WHERE id = $v";
                        //$sqlDeletReservas = "DELETE FROM $TAPARTAMENTOS_RESERVAS WHERE id = $v";
                        if (mysqli_query($conn_a, $sqlDeletReservas)) {
                            $msg = "|REMOVERESERVA|".$qto['id_hotel_local']."|:;";
                            $sqlItemLog = "INSERT INTO $TLOGSMENSAGEM (mensagem, datacad, cnpjcpfcad, cnpjcpfseg, quescript, dataatu, ip) VALUES ('|DONO|".$qto['id_hotel_local']."|:;|CNPJCPF|"."|:;".$msg."', '".time()."', '".$_SESSION['ADMIN']['cnpj']."', '".$_SESSION['ADMIN']['cnpj_segmento']."', 'REMOVERESERVA-HOTELRECEPCAO', '".time()."', '".$_SERVER["REMOTE_ADDR"]."');";
                            mysqli_query($conn_a, $sqlItemLog);
                        } else {
                            echo "FALSE";
                        }
                    }
                }
                
                echo "TRUE";
                
            }
            
            break;
        
        case 'listaReservas':
            ?>
                <table class='table table-hover'>
                    <tr class='cel_subtit'><td colspan="6" class="text-center">Reservas</td></tr>
                    <tr class='cel_subtit'>
                        <td><input type="checkbox" name="selecionarTodas" id="selecionarTodas"></td>
                        <td>PER&Iacute;ODO</td>
                        <td>APTO</td>
                        <td>CLASSIFICA&Ccedil;&Atilde;O</td>
                        <td>NOME</td>
                        <td>OBS</td>
                    </tr>
                    <tbody>
                        <?php
                            // nessa linha abaixo esta com o nome da tabela digitado e não usando variavel pois não esta no arquivo de tabelas informado na ultima versão a proxima linha esta com a variavel correta
                            $sqlReservas = "SELECT * FROM apartamentos_reservas ORDER BY entrada";
                            //$sqlReservas = "SELECT * FROM $TAPARTAMENTOS_RESERVAS ORDER BY entrada";
                            $getReservas = mysqli_query($conn_a, $sqlReservas);

                            while($reservas = mysqli_fetch_assoc($getReservas)) {
                                echo "
                                    <tr class='linha' id='linha_".$reservas['id']."'>
                                        <td><input id='id_".$reservas['id']."' class='chk' name='chk[]' value='".$reservas['id']."' type='checkbox'></td>
                                        <td class='selE'>"._myfunc_stod($reservas['entrada'])." - "._myfunc_stod($reservas['saida'])."</td>
                                        <td class='selE'>".$reservas['id_hotel_local']."</td>
                                        <td class='selE'>".$reservas['classifica']."</td>
                                        <td class='selE'>".utf8_encode($reservas['razao'])."</td>
                                        <td class='selE'>".utf8_encode($reservas['obs'])."</td>
                                    </tr>
                                ";
                            }
                        ?>
                    </tbody>
                </table>
            <?php
            break;
        
        case 'hospede':
            
            //exit(var_dump($_POST));
            $id = filter_input(INPUT_POST, 'param');
            $horaFechamento = filter_input(INPUT_POST, 'hrfechamento');
            
            if (empty($horaFechamento)) {
                $horaFechamento = '12:00:00';
            }
            
            $sel="SELECT n.datacad, c.razao, a.ramal, n.placatransp, n.datasaida, n.dono FROM $TNFDOCUMENTOS_TMP n, $TLANCAMENTOS_TMP l, $TAPARTAMENTOS a, $TCNPJCPF c WHERE n.id_hotel_local = '$id' AND n.dono = l.dono AND a.id_hotel_local = n.id_hotel_local AND c.cnpj = l.cnpjcpf AND l.contac <> '' group by n.id_hotel_local";
            //exit($sel);
            $query = mysqli_query($conn_a, $sel);
            $hospede = mysqli_fetch_assoc($query);
            
            $dono = $nome = $ramal = $data = $datasaida = $datacad = $placa = $df_dias = '';       
            if (!empty($hospede)) {
                //exit($hospede['datacad']);
                $dono = $hospede['dono']; // Para atualização do serviço = hospedagem
                $nome = substr($hospede['razao'], 0, 20);
                $ramal = $hospede['ramal'];
                $data = date('d/m/Y H:i:s', $hospede['datacad']);	
                $datasaida = _myfunc_stod($hospede['datasaida']);	
                $datacad= $hospede['datacad']; // Para calculo da diferen�a entre as datas
                $placa = $hospede['placatransp'];
                //$df_dias= _calcula_atualiza_diarias_apto($dtos, $param, $dono, $datacad); 
                //echo $hospede['datacad'].'<br> - ';
                $df_dias = _myfuncoes_diaria_hotel($hospede['datacad'], $TITEM_FLUXO_TMP, $id, $dono, $conn_a, $horaFechamento);
                
                
            } 
            $html  = "<font size='2'><b>Hospede</b><br></font><font size='2'> ".$nome." </font>";
            $html .= "<br><font size='2'><b>Ramal</b><br></font><font size='2'> ".$ramal." </font>";
            $html .= "<br><font size='2'><b>Data Entrada</b><br></font><font size='2'> ".$data." </font>";
            if (!empty($hospede['datasaida'])) {
                $html .= "<br><font size='2'><b>Previs&atilde;o Sa&iacute;da</b><br></font><font size='2'> ".$datasaida." </font>";
            }
            $html .= "<br><font size='2'><b>Dias hospedagem:</b><br></font><font size='2'> ".$df_dias." </font>";
            $html .= "<br><font size='2'><b>Placa:</b><br></font><font size='2'> ".$placa." </font>";

            echo $html;
            break;
            
        case 'cadastrarQaurtos':
            // Tratando Vari�veis
            //exit(var_dump($_POST));
            
            $id_hotel_local = filter_input(INPUT_POST, 'quarto');
            $descricao = filter_input(INPUT_POST, 'descricao');
            $classifica = filter_input(INPUT_POST, 'class');
            $status = filter_input(INPUT_POST, 'status');
            $ramal = filter_input(INPUT_POST, 'ramal');
            $ocupacao_maxima = filter_input(INPUT_POST, 'ocupacaoM');
            $obs = filter_input(INPUT_POST, 'obs');
            $editar = filter_input(INPUT_POST, 'editar');

            if ($classifica == '(Vinculado ao tarifário)') {
                exit("Classificacao invalida!");
            }
            
            if ($status == '(Status do quarto)') {
                exit("Status invalido!");
            }
            $dtres1i = filter_input(INPUT_POST, 'dtE1');
            $dtres1f = filter_input(INPUT_POST, 'dtF1');

            $dtres2i = filter_input(INPUT_POST, 'dtE2');
            $dtres2f = filter_input(INPUT_POST, 'dtF2');


            $sdtres1i = _myfunc_dtos($dtres1i);
            $sdtres1f = _myfunc_dtos($dtres1f);
            $sdtres2i = _myfunc_dtos($dtres2i);
            $sdtres2f = _myfunc_dtos($dtres2f);
            
            $mens_data = '';
            IF ($sdtres1i < $dtos and $dtres1i <> '') {
                $mens_data = $mens_data . "$dtres1i - Data reserva incorreta!";
                $sdtres1i = '';
                $sdtres1f = '';
            }
            IF ($sdtres1f < $dtos and $dtres1f <> '') {
                $mens_data = $mens_data . "$dtres1f - Data reserva incorreta!";
                $sdtres1i = '';
                $sdtres1f = '';
            }
            IF ($sdtres1i > $sdtres1f) {
                $mens_data = $mens_data . "$dtres1i - Datas invertidas!";
                $sdtres1i = '';
                $sdtres1f = '';
            }

            IF ($sdtres2i < $dtos and $dtres2i <> '') {
                $mens_data = $mens_data . "<$dtres2i - Data reserva incorreta!";
                $sdtres2i = '';
                $sdtres2f = '';
            }
            IF ($sdtres2f < $dtos and $dtres2f <> '') {
                $mens_data = $mens_data . "$dtres2f - Data reserva incorreta!";
                $sdtres2i = '';
                $sdtres2f = '';
            }
            IF ($sdtres2i > $sdtres2f) {
                $mens_data = $mens_data . "$dtres2i - Datas invertidas!";
                $sdtres21i = '';
                $sdtres2f = '';
            }

            if ($mens_data <> '') {
                exit($mens_data);
            }

            //exit(var_dump($_POST));
            if (empty($editar)) {
                $sql = "INSERT INTO $TAPARTAMENTOS (id_hotel_local,descricao,datacad,dataatu,cnpjcpfcad,cnpjcpfatu,classifica,status,ocupacao_maxima,ramal,obs, dtres1i, dtres1f, dtres2i, dtres2f, dtres3i, dtres3f ) VALUES ('$id_hotel_local','$descricao','$dtos','$dtos','$info_cnpj','$info_cnpj','$classifica','$staus','$ocupacao_maxima','$ramal','$obs', '$sdtres1i', '$sdtres1f', '$sdtres2i', '$sdtres2f', '1', '1')";
                //exit($sql);
                if (mysqli_query($conn_a, $sql)) {
                    echo "TRUE";
                } else {
                    echo "Falha ao inserir quarto, entre em contato com o suporte! ".mysqli_error($conn_a);
                }
                
            } else {
                $sql = "UPDATE $TAPARTAMENTOS SET dataatu='$dtos', cnpjcpfatu='$info_cnpj', descricao='$descricao', classifica='$classifica', status='$status', ocupacao_maxima='$ocupacao_maxima', ramal='$ramal', obs='$obs', dtres1i='$sdtres1i', dtres1f='$sdtres1f', dtres2i='$sdtres2i', dtres2f='$sdtres2f' WHERE id_hotel_local = '$editar'";
                //exit($sql);
                if (mysqli_query($conn_a, $sql)) {
                    echo "TRUE";
                } else {
                    echo "Falha ao alterar quarto, entre em contato com o suporte! ".mysqli_error($conn_a);;
                }
            }

            break;
            
        case 'removeQuarto':
            
            $quartos = filter_input(INPUT_POST, 'quartos');
            $q = substr($quartos, 0, -1);
            //exit(var_dump($_POST));
            if (!empty($quartos)) {
                $quarts = explode(',', $q);
                foreach ($quarts as $qtos) {
                    $getDono = mysqli_query($conn_a, "SELECT id_hotel_local,dono FROM $TNFDOCUMENTOS_TMP WHERE id_hotel_local = '$qtos'");
                    //echo "SELECT id_hotel_local,dono FROM $TNFDOCUMENTOS_TMP WHERE id_hotel_local = '$qtos'<br>";
                    if(!mysqli_num_rows($getDono)) {
                        $sqlDeletQuartos = "DELETE FROM $TAPARTAMENTOS WHERE id_hotel_local = '$qtos' ";
                        //echo ($sqlDeletQuartos.'<br>');
                        if (mysqli_query($conn_a, $sqlDeletQuartos)) {
                            $apagou = true;
                        } else {
                            $apagou = false;
                        }
                    }
                }
                
                if ($apagou) {
                    echo "TRUE";
                } else {
                    echo "FALSE". mysqli_error($conn_a);
                }
            }
            
            break;    
            
        case 'listaQuartos':
            //exit("");
            ?>
                <table class='table table-hover'>
                    <tr class='cel_subtit'><td colspan="7" class="text-center">Quartos</td></tr>
                    <tr class='cel_subtit'>
                        <td><input type="checkbox" name="selecionarTodas" id="selecionarTodas"></td>
                        <td>ID</td>
                        <td>DESCRICAO</td>
                        <td>CLASSIFICA&Ccedil;&Atilde;O</td>
                        <td>STATUS</td>
                        <td>RAMAL</td>
                        <td>RESERVAS</td>
                    </tr>
                    <tbody>
                        <?php
                            $sqlQuartos = "SELECT * FROM $TAPARTAMENTOS ORDER BY id_hotel_local";
                            $getQuartos = mysqli_query($conn_a, $sqlQuartos);

                            while($qto = mysqli_fetch_assoc($getQuartos)) {
                                echo "
                                    <tr class='linha' id='linha_".$qto['id_hotel_local']."'>
                                        <td><input id='id_".$qto['id_hotel_local']."' class='chk' name='chk[]' value='".$qto['id_hotel_local']."' type='checkbox'></td>
                                        <td class='selE'>".$qto['id_hotel_local']."</td>
                                        <td class='selE'>".$qto['descricao']."</td>
                                        <td class='selE'>".$qto['classifica']."</td>
                                        <td class='selE'>".$qto['status']."</td>
                                        <td class='selE'>".$qto['ramal']."</td>
                                        <td>"."</td>
                                    </tr>
                                ";
                            }
                        ?>
                    </tbody>
                </table>
            <?php
            break;
            
        case 'insereClassi':
            //exit(var_dump($_POST));
            $clas = filter_input(INPUT_POST, 'classifica');
            
            $query = "INSERT INTO $TAPARTAMENTOS_CLASSIFICA 
                    (datacad, dataatu, cnpjcpfcad, cnpjcpfatu, classifica, vr_padrao_percentual_0, vr_padrao_percentual_1, vr_padrao_percentual_2, vr_padrao_percentual_3, vr_padrao_percentual_4, vr_padrao_percentual_5, vr_padrao_percentual_6) 
                    VALUES 
                    ('$dtos', '$dtos', '$cpfUsuario', '$cpfUsuario', '$clas', '0', '0', '0', '0', '0', '0', '0')
            ";
            //exit($query);
            if (mysqli_query($conn_a, $query)) {
                echo 'TRUE';
            } else {
                echo 'Falha ao inserir classificacao, por favor entre em contato com suporte! '.mysqli_error($conn_a);;
            }
            break;
        
        case 'listaClassi':
            ?>
                <table class='table table-hover'>
                    <tr class='cel_subtit'><td colspan="4" class="text-center">Classifica&ccedil;&atilde;o</td></tr>
                    <tr class='cel_subtit'>
                        <td><input type="checkbox" name="selecionarTodas" id="selecionarTodas"></td>
                        <td>ID</td>
                        <td>CLASSIFICA&Ccedil;&Atilde;O</td>
                    <tbody>
                        <?php
                            $sqlClass = "SELECT id, classifica FROM $TAPARTAMENTOS_CLASSIFICA ORDER BY classifica";
                            $getClass = mysqli_query($conn_a, $sqlClass);

                            while($class = mysqli_fetch_assoc($getClass)) {
                                echo "
                                    <tr class='linha' id='linha_".$class['id']."'>
                                        <td><input id='id_".$class['id']."' class='chk' name='chk[]' value='".$class['id']."' type='checkbox'></td>
                                        <td class='selE'>".$class['id']."</td>
                                        <td class='selE'>".$class['classifica']."</td>
                                    </tr>
                                ";
                            }
                        ?>
                    </tbody>
                </table>
            <?php
            break;
            
        case 'removeClassifica':
            //exit(var_dump($_POST));
            $class = filter_input(INPUT_POST, 'classifica');
            $r = substr($class, 0, -1);
            
            if (!empty($class)) {
                $sqlDeletClass = "DELETE FROM $TAPARTAMENTOS_CLASSIFICA WHERE id IN ($r)";
                //exit($sqlDeletClass);
                if (mysqli_query($conn_a, $sqlDeletClass)) {
                    echo "TRUE";
                } else {
                    echo "FALSE";
                }
            }
            
            break;  
            
        case 'statusQuarto':
            //exit(var_dump($_POST));
            $qto = filter_input(INPUT_POST, 'quarto');
            $sta = filter_input(INPUT_POST, 'status');
            
            
            $sql = "UPDATE $TAPARTAMENTOS SET dataatu='$dtos', cnpjcpfatu='".$_SESSION['ADMIN']['cnpj']."', status='$sta' WHERE id_hotel_local = '$qto'";
                //exit($sql);
                if (mysqli_query($conn_a, $sql)) {
                    echo "TRUE";
                } else {
                    echo "Falha ao alterar quarto, entre em contato com o suporte! ".mysqli_error($conn_a);;
                }
            
            break;
            
        default :
            
            break;
        
    }
    
    function _get_mk_time($data, $fecha = ''){
        $h = date('H', $data);
        $m = date('i', $data);
        $s = date('s', $data);
        if(!empty($fecha)) {
            $hora = explode(':', $fecha);
            $h = $hora[0];
            $m = $hora[1];
            $s = $hora[2];
        }

        return mktime($h, $m, $s, date('m', $data), date('d', $data), date('Y', $data)) ;
    }
    
    function _myfuncoes_diaria_hotel($dataEntrada, $TITEM_FLUXO_TMP, $param, $dono, $conn_a, $fecha) {
        
        $fechamento = substr($fecha, 0, 2);
        $horaEntrada = date('H', $dataEntrada);

        $now = time();
        $ent = _get_mk_time($dataEntrada, '00:00:00');
        $dia = _get_mk_time($now, '00:00:00');
        $dataE = date('d-m-Y', $dataEntrada);
        $dataH = date('d-m-Y', $now);
        $horaAtual = date('H', $now);
        
        if (($dataE == $dataH) && ($horaEntrada >= $fechamento)) {
            $n_dias = 1;
        } else if( ($dataE == $dataH) && ($horaEntrada < $fechamento) && ($horaAtual < $fechamento)) {
            $n_dias = 1;
        } else {
            $n_dias = 2;
        }
        
        if (($dataE <> $dataH) && ($horaEntrada < $fechamento) && ($horaAtual < $fechamento)) {
            $n_dias = ($dia - $ent) / 86400;
            $n_dias++;
        } else if(($dataE <> $dataH) && ($horaEntrada < $fechamento) && ($horaAtual >= $fechamento)) {
            $n_dias = (($dia - $ent) / 86400 ) + 2;
        } else if(($dataE <> $dataH) && ($horaEntrada >= $fechamento) && ($horaAtual < $fechamento)){
            $n_dias = (($dia - $ent) / 86400 );
        } else if(($dataE <> $dataH) && ($horaEntrada >= $fechamento) && ($horaAtual >= $fechamento)) {
            $n_dias = (($dia - $ent) / 86400 ) + 1;
        } 
        
        
        if ($n_dias) {
            $sel_Dias = "SELECT cprod, vuncom FROM $TITEM_FLUXO_TMP WHERE cprod = '$param' AND dono='$dono' limit 1";
            $get_Dias = mysqli_query($conn_a, $sel_Dias) or exit(mysqli_error($conn_a));
            $d = mysqli_fetch_array($get_Dias);

            $vprod=($d['vuncom']*$n_dias);
            mysqli_query($conn_a, "UPDATE $TITEM_FLUXO_TMP SET qcom='$n_dias', qtrib='$n_dias', vprod='$vprod', dataatu='".time()."' WHERE cprod='$param' AND dono='$dono'");
        }
        
        return $n_dias;
    }
 

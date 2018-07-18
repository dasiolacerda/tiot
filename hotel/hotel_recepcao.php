<link rel='stylesheet' type='text/css' href="../_css/font-awesome.css" rel="stylesheet">
<link rel='stylesheet' type='text/css' href="../_css/bootstrap.3.3.5-24.css" rel="stylesheet">

<link rel='stylesheet' type='text/css' href='../_css/themes/office/ui.datepicker.css' />
<link rel='stylesheet' type='text/css' href='../_css/jquery.autocomplete.css' />
            


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
<script src='../_javascripts/jquery.autocomplete.min.js'></script> 
<?php $ps = _myfunc_retorno_parametros_segmento(); ?>
<script type="text/javascript">

    $(function () {
        var aux = new Object();
        var mouseQuarto = false;
        var mouseLink = false;

        $(".quarto").each(function (e) {
            var id = $(this).attr("id").split("_")[1];
            aux[id] = $(this).html();
        })

        $(".room").mousemove(function (event) {
            var flag = $(this).attr("id").split("_")[2];
            if (flag == "OCUPADO")
            {
                $("#infoQuarto").css("display", "block");
                $("#infoQuarto").css("height", "230px");
                $("#infoQuarto").css("left", event.pageX + 30);
                $("#infoQuarto").css("top", event.pageY - 210);
            }
            if (flag != "LIVRE" && flag != "OCUPADO")
            {
                $("#infoQuarto").css("display", "block");
                $("#infoQuarto").css("height", "50px");
                $("#infoQuarto").css("left", event.pageX + 30);
                $("#infoQuarto").css("top", event.pageY - 60);
                var html = "<font size='2'><b>Estado Quarto</b><br></font><font size='3'>" + flag + "</font>";
                $("#infoQuarto").html(html);

            }
        });
        $("#infoQuarto").mouseenter(function () {
            $(this).css("display", "none");
        });
        $(".room").mouseenter(function () {
            //console.log("entrou");
            if (mouseQuarto == false)
            {
                $("#infoQuarto").css("display", "none");
                mouseQuarto = true;
                var id_quarto = $(this).attr("id").split("_"),
                        id = id_quarto[1],
                        flag = id_quarto[2];

                if (flag == "OCUPADO")
                {
                    //var dados = "tipo=hospede&param="+id;
                    var html = "<font size='3'>CARREGANDO...<br></font><br><br>";
                    html += "<img src='../imagens/trabalhando.png' width='40px'>";
                    $("#infoQuarto").html(html);
                    $.ajax({
                        url: "../hotel/insertOnDataBase.php",
                        type: "post",
                        data: 'tipo=hospede&param='+id+'&hrfechamento=<?php echo $ps['hrfechamento']?>',
                        success: function (retorno) {
                            //console.log("x: " + event.pageX + ", y: " + event.pageY + " -> " + retorno);
                            //console.log(retorno);
                            $("#infoQuarto").html(retorno);
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            html = "<font size='3'>ERRO<br></font>N�o Foi poss�vel carregar as informa��ess do h�spede";
                            html += "<br><font size='2'><b>Tipo: </b>" + id + "</font>" + XMLHttpRequest + " teste" + textStatus + " outro teste" + errorThrown;
                            $("#infoQuarto").html(html);
                        }
                    });

                }
                //var html = $("#link_" + id).html();

                //$(this).html(html + "<div style='position: absolute; bottom:0; width: 100%' class='icoTitulo'></div>");

            }
        });
        $(".room").mouseleave(function () {
            $("#infoQuarto").css("display", "none");
            mouseQuarto = false;
        });
        
        $.datepicker.setDefaults({
            monthNames: ['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            dayNames: ['Domingo', 'Segunda', 'Ter&ccedil;a', 'Quarta', 'Quinta', 'Sexta', 'S&aacute;bado'],
            dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'],
            dateFormat: "dd/mm/yy"
        });
        
        $(".data").datepicker();
        
        $(".qtoReservar").change(function() {
            
            var apto = $(this).attr('id').split('_')[1],
                alvo = $(this).parent().parent().find('#resV_'+apto),
                campos = $(this).val().split('|');
                
            alvo.attr('href', 'logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink='+campos[0]+'&codnat=HOTE&dono='+campos[1]+'&id_hotel_local='+campos[2]+'&obs='+campos[3]+'&res='+campos[4]); 
            
        });
        
        $(document).on('click', 'div table.table tr.linha td.selE', function(){
            var linha = $(this).parent().attr('id').split("_"),
                input = $(this).parent().parent().find('#id_'+linha[1]);
                //alert(linha[0]+'_'+linha[1]);
                //alert(input.attr('class'));
            if (input.is(':checked')) {
                input.click();
                return;
            } else {
                input.click();
                return;
            }
        });
        
        $(document).on("click", "#selecionarTodas", function(){
            $(this).parent().parent().parent().parent().find('.chk').click();
        });
        
        $(".modal-footer").click(function(){
            location.reload();
        }); 
    });
</script>

    <?php
    
    //exit('chegou aqui');
    session_start();
    if (!((eregi(':052.000', $contasacesso_user)) or ( eregi(':052.010', $contasacesso_user)))) {
        myfuncoes_usuario_sem_permissao_acesso();
        exit;
    }
    $_SESSION['PERMSHotel'] = $contasacesso_user;
    //echo var_dump($_SESSION);

    
    $sel = mysqli_query($conn_a, "SELECT * FROM $TAPARTAMENTOS ORDER BY id_hotel_local");
    echo "<div id='infoQuarto' style='width: 200px; height: 150px;background-color: white; position: absolute; top: 0; left:0; z-index: 1000'></div>";
    
    $legenda = '
        <div class="col-md-24 col-sm-24 col-xs-24 text-center">
            <div class="col-md-offset-4 col-sm-offset-11 col-xs-offset-4">
                <div style="float: left"><div style="background-color: #C9F880; width: 15px; height: 15px;float: left"></div>&nbsp; Livre&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="float: left"><div style="background-color: #f38989; width: 15px; height: 15px;float: left"></div>&nbsp; Ocupado&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="float: left"><div style="background-color: #9bceea; width: 15px; height: 15px;float: left"></div>&nbsp; Ocupado (saida hoje)&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="float: left"><div style="background-color: #bfaedb; width: 15px; height: 15px;float: left"></div>&nbsp; Ocupado (saida expirada)&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="float: left"><div style="background-color: #fbfb51; width: 15px; height: 15px;float: left"></div>&nbsp; Desativado / Manuten&ccedil;&atilde;o&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="float: left"><div style="background-color: #e9e0e0; width: 15px; height: 15px;float: left"></div>&nbsp; Limpeza&nbsp;&nbsp;&nbsp;&nbsp;</div>
            </div>
            
        </div>
    ';
    ?>
    <!--a href="#" class="modalAbre">outro teste</a >
    data: <input type="text" name="text_data" class="data" value="" style="width: 125px;" /-->
    <div class="well">
        <div class="container">
            <?php
            echo $legenda;
            //for ($abc = 0; $abc < 6;$abc++) { 
            $reserDono = array();
            $reserChli = array();
            $reseOcupa = array();
            $qtoLivre  = array();
            while ($v = mysqli_fetch_assoc($sel)) {
                $ico_apto = '';
                $flag_hotel = 'LIVRE';
                $bg1 = '';
                $existe_id_hotel_local = _myfunc_existe_id_hotel_local($v['id_hotel_local']);

                if ($existe_id_hotel_local == '') {
                    // ok, nãso cadastrado para outro
                } else {
                    $flag_hotel = 'OCUPADO';
                }

                $dados_apartamentos = _myfun_dados_apartamentos_hospedagem($v['id_hotel_local']);
                IF ($dados_apartamentos['status'] == 'D') {  //desativado
                    $flag_hotel = 'DESATIVADO';
                }
                IF ($dados_apartamentos['status'] == 'L') {  //Limpeza
                    $flag_hotel = 'LIMPEZA';
                }
                IF ($dados_apartamentos['status'] == 'M') {  //Manuten��o
                    $flag_hotel = 'MANUTEN&Ccedil;&Atilde;O';
                }

                $ddtres1i = _myfunc_stod($v['dtres1i']);
                $ddtres1f = _myfunc_stod($v['dtres1f']);
                $ddtres2i = _myfunc_stod($v['dtres2i']);
                $ddtres2f = _myfunc_stod($v['dtres2f']);
                $ddtres3i = _myfunc_stod($v['dtres3i']);
                $ddtres3f = _myfunc_stod($v['dtres3f']);
                $reserva = '';
                $reserva_link = 'Clique para efetuar reserva do APTO';
                $dias_1 = abs(_myfuncoes_data_diferenca_dias($dthoje, $ddtres1i));
                $dias_2 = abs(_myfuncoes_data_diferenca_dias($dthoje, $ddtres2i));

                if ($dias_1 < $dias_2) {
                    if ($v['dtres1i'] <> 0 and $v['dtres1i'] > $dtos) {
                        $dias_x = _myfuncoes_data_diferenca_dias($dthoje, $ddtres1i);
                        $reserva = "<font size='1'>***RESERVA***</font><br>";
                        $reserva_link = "ATEN&Ccedil;&Atilde;O:  $dias_x  dia(s) da reserva.";
                    }
                }
                if ($dias_2 < $dias_1) {
                    if ($v['dtres2i'] <> 0 and $v['dtres2i'] > $dtos) {
                        $dias_x = _myfuncoes_data_diferenca_dias($dthoje, $ddtres2i);
                        $reserva = "<font size='1'>***RESERVA***</font><BR>";
                        $reserva_link = "ATEN&Ccedil;&Atilde;O:  $dias_x  dia(s) da reserva.";
                    }
                }
                
                switch ($flag_hotel) {
                    case 'LIVRE':
                        $xdono = BASICO::_numero_aleatorio('LAN');
                        $chklinknovo = _myfuncoes_chklink('ID', $xdono . 'RECEITAS');
                        $bg1 = "#C9F880"; //"#ADFF2F"; // verde
                        $reserDono[$v['id_hotel_local']] = $xdono;
                        $reserChli[$v['id_hotel_local']] = $chklinknovo;
                        $qtoLivre[$v['id_hotel_local']] = $v['id_hotel_local'];
                        $ico_apto = '
                            <span id="link_'.$v['id_hotel_local'].'">
                                <a class="link" href="logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink='.$chklinknovo.'&codnat=HOTE&dono='.$xdono.'&id_hotel_local='.$v['id_hotel_local'].'"><i class="fa fa-arrow-circle-o-down fa-3x pointer" id="entrada" title="Adiconar Hospede" abbr=""></i></a>
                                <a class="modalAbre pointer" abbr="hotel_cadastro.php?idb='.$v['id_hotel_local'].'" abbr-title="Mudar Quarto" > <i class="fa fa-bed fa-3x" title="Mudar Estado Quarto" id="estado"></i></a>
                                <a class="modalAbre pointer" abbr="reservas.php?idb='.$v['classifica'].'" abbr-title="Reservas"> <i class="fa fa-edit fa-3x pointer" id="reservar" title="Reservar Quarto"></i></a>
                            </span>
                        ';
                        break;

                    case 'OCUPADO':
                        $bg1 = "#f38989"; // vermelho #FF0000
                        $xdono = $existe_id_hotel_local;
                        $chklinknovo = _myfuncoes_chklink('ID', $xdono . 'RECEITAS');
                        $reserDono[$v['id_hotel_local']] = $xdono;
                        $reserChli[$v['id_hotel_local']] = $chklinknovo;
                        $reseOcupa[$v['id_hotel_local']] = $v['id_hotel_local'];
                        
                        $selInfor="
                            SELECT 
                                n.datasaida
                            FROM 
                                $TNFDOCUMENTOS_TMP n,
                                $TLANCAMENTOS_TMP l, 
                                $TAPARTAMENTOS a, 
                                $TCNPJCPF c 
                            WHERE 
                                n.id_hotel_local = '".$v['id_hotel_local']."' 
                                AND n.dono = l.dono 
                                AND a.id_hotel_local = n.id_hotel_local 
                                AND c.cnpj = l.cnpjcpf 
                                AND l.contac <> '' 
                            GROUP BY 
                                n.id_hotel_local
                         ";
                        //exit($selInfor);
                        $queryInfor = mysqli_query($conn_a, $selInfor);
                        $VrInfor = mysqli_fetch_assoc($queryInfor);
                        if (empty($VrInfor)) { 
                            $ico_apto = '
                                <span id="link_'.$v['id_hotel_local'].'">
                                    <a class="modalAbre pointer" abbr="hotel_ocupacao.php?dono='.$xdono.'&chklink='.$chklinknovo.'&quarto='.$v['id_hotel_local'].'" abbr-title="Informa&ccedil;&otilde;es Hospedes"><i class="fa fa-plus-circle fa-3x" id="maisinfo" title="Informa&ccedil;&otilde;es"></i></a>
                                </span>
                            ';
                        } else {
                            
                            $dt = new DateTime( date('Y-m-d', $VrInfor['datasaida']));
                            $hj = new DateTime (date('Y-m-d'));
                            
                            
                            if ($dt->getTimestamp() ==  $hj->getTimestamp()) {
                                $bg1 = "#9bceea"; // azul
                            } 
                            
                            if ($dt->getTimestamp() < $hj->getTimestamp()) {
                                $bg1 = "#bfaedb"; // roxo
                                //exit('roxo');
                            }
                            //echo $VrInfor['datasaida'];
                            $ico_apto = '
                                <span id="link_'.$v['id_hotel_local'].'">
                                    <a class="link" href="logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink='.$chklinknovo.'&codnat=HOTE&dono='.$xdono.'" ><i class="fa fa-credit-card fa-3x" title="Conta Hospede" id="conta"></i></a>
                                    <a class="modalAbre pointer" abbr="hotel_transferencia.php?quarto='.$v['id_hotel_local'].'" abbr-title="Tranferir Quarto" ><i class="fa fa-share-square-o fa-3x" id="transferir" class="TrQuarto" title="Transferir Quarto"></i></a>
                                    <a class="modalAbre pointer" abbr="hotel_ocupacao.php?dono='.$xdono.'&chklink='.$chklinknovo.'&quarto='.$v['id_hotel_local'].'" abbr-title="Informa&ccedil;&otilde;es Hospedes"><i class="fa fa-plus-circle fa-3x" id="maisinfo" title="Informa&ccedil;&otilde;es"></i></a>
                                    <a href="logado.php?ac=hotel_relatorios_pdf&site=i&id_qual_relatorio=hotel_rel_fechamento&idb='.$xdono.'&id_hotel_local='.$v['id_hotel_local'].'" abbr-title="Informa&ccedil;&otilde;es Hospedes"><i class="fa fa-file-o fa-3x" id="relGerencial" title="Relat&aacute;rio"></i></a>
                                </span>
                            ';
                        }

                        break;

                    case 'DESATIVADO':
                        $bg1 = "#fbfb51"; // amarelo
                        $reserva .= '<br>'.$flag_hotel;
                        $reseOcupa[$v['id_hotel_local']] = $v['id_hotel_local'];
                        $ico_apto = '
                            <br /><span id="link_'.$v['id_hotel_local'].'">
                                <a class="modalAbre pointer" abbr="hotel_cadastro.php?idb='.$v['id_hotel_local'].'" abbr-title="Mudar Quarto" > <i class="fa fa-bed fa-3x" title="Mudar Estado Quarto" id="estado"></i></a>
                            </span>
                        ';
                        break;

                    case 'LIMPEZA':
                        $bg1 = "#e9e0e0"; // cinza
                        $reserva .= '<br>'.$flag_hotel;
                        $reseOcupa[$v['id_hotel_local']] = $v['id_hotel_local'];
                        $ico_apto = '
                            <br /><span id="link_'.$v['id_hotel_local'].'">
                                <a class="modalAbre pointer" abbr="hotel_cadastro.php?idb='.$v['id_hotel_local'].'" abbr-title="Mudar Quarto" > <i class="fa fa-bed fa-3x" title="Mudar Estado Quarto" id="estado"></i></a>
                            </span>
                        ';
                        break;

                    case 'MANUTENCAO':
                        $bg1 = "#fbfb51"; // amarelo
                        $reserva .= '<br>'."MANUTEN&Ccedil;&Atilde;O";
                        $reseOcupa[$v['id_hotel_local']] = $v['id_hotel_local'];
                        $ico_apto = '
                            <br /><span id="link_'.$v['id_hotel_local'].'">
                                <a class="modalAbre pointer" abbr="hotel_cadastro.php?idb='.$v['id_hotel_local'].'" abbr-title="Mudar Quarto" > <i class="fa fa-bed fa-3x" title="Mudar Estado Quarto" id="estado"></i></a>
                            </span>
                        ';
                        break;
                }

                if (!(eregi(':052.010', $contasacesso_user))) {
                    $ico_apto = '';
                }

                //$id_quarto='id_'.$v[id_hotel_local].'_'.$flag_hotel;
                //echo "<td id='id_$v[id_hotel_local]_$flag_hotel' class='quarto' align='center' $bg1> $reserva <b><font size='3'><span style='color:blue'>$ico_apto <br></span></font>";
                //echo " $v[id_hotel_local] </font> <br><br> $v[descricao] </b>";
                //}<i class=fa fa-times-circle-o fa-3x pointer' title='Quarto Interditado(Limpeza)'></i>
                echo '
                        <div id="id_' . $v['id_hotel_local'] . '_' . $flag_hotel . '" class="col-md-4 col-sm-4 col-xs-12 room" style="background-color: ' . $bg1 . ';">
                            ' . $reserva . $ico_apto . '<br />' . $v['id_hotel_local'] . '<br /><br />' . $v['descricao'] . '
                        </div>
                    ';
                ?>

            <?php } ?>
            <div class="col-md-4 col-sm-4 col-xs-12" style="background-color: #FFF; height: 180px; border: solid 1px #fff; padding-top: 20px; border-radius: 10px;">
                <?php 
                    if (eregi(':052.001', $contasacesso_user)) { 
                        echo '
                            <a class="modalAbre pointer" abbr="hotel_cadastro.php" abbr-title="Adicionar Quarto" > <i class="fa fa-bed fa-3x" title="Adicionar Quarto" id="estado"></i></a>
                            <a class="modalAbre pointer" abbr="classificacao.php" abbr-title="Classifica&ccedil;&atilde;o"> <i class="fa fa-star-o fa-3x pointer" id="reservar" title="Classifica&ccedil;&atilde;o"></i></a>
                        ';
                    }
                    
                    if (eregi(':052.015', $contasacesso_user) && !eregi(':052.001', $contasacesso_user)) {
                        
                        echo '<a class="modalAbre pointer" abbr="classificacao.php" abbr-title="Classifica&ccedil;&atilde;o"> <i class="fa fa-star-o fa-3x pointer" id="reservar" title="Classifica&ccedil;&atilde;o"></i></a>';
                    }
                ?>
                
                <a class="modalAbre pointer" abbr="reservas.php" abbr-title="Reservas"> <i class="fa fa-edit fa-3x pointer" id="reservar" title="Reservar Quarto"></i></a>
                
                <a href="logado.php?ac=hotel_relatorios" abbr-title="Relat&oacute;rios e Gr&aacute;ficos"><i class="fa fa-file-o fa-3x" id="relGerencial" title="Relat&oacute;rios e Gr&aacute;ficos"></i></a>

            </div>
            <?php echo $legenda;?>
        </div>
    </div>

<!-- /fieldset -->
<?php
//echo _myfuncoes_user_menu_bott('HOTEL');

$xsql = "SELECT a.dono,a.id_hotel_local,b.cnpjcpf  FROM $TNFDOCUMENTOS_TMP as a,$TLANCAMENTOS_TMP as b WHERE a.id_hotel_local<>'' and a.dono = b.dono group by b.cnpjcpf, a.id_hotel_local ORDER BY id_hotel_local";
$sel = mysqli_query($conn_a, $xsql);

?>
<br>

<div class="col-md-24">
    <div class="col-lg-12"> 
        <table class='table table-hover' >
            <tr class='cel_subtit'><td align=left> APTO </td>
                <td align=left> HOSPEDE(s) </td>
                <td align=left> RESERVADO</td>
                <td align=right> RAMAL</td>
                <td align=right> PLACA</td>
                <td align=left> CIDADE - UF </td>
                <td align=right> OCUPA&Ccedil;&Atilde;O</td>
                <td align=right> FICHA</td>
            </tr>
            <?php

                $xa = 0;

                while ($v = mysqli_fetch_assoc($sel)) {
                    $dados_cli = _myfun_dados_cnpjcpf($v['cnpjcpf']);
                    //exit(var_dump($dados_cli));
                    
                    $dados_apartamentos = _myfun_dados_apartamentos_hospedagem($v['id_hotel_local']);
                    $dados_dono = $v[dono];
                    echo "<tr><td>";
                    $razao = substr($dados_cli[razao], 0, 25);
                    echo $v['id_hotel_local']."</td><td>$razao";
                    $sel_hospedes = mysqli_query($conn_a, "SELECT * FROM $TAPARTAMENTOS_HOSPEDES WHERE (dono='".$v['dono']."')", $CONTITEM_FLUXO_TMP);
                    $qtde_hospedes = mysqli_num_rows($sel_hospedes);
                    if ($qtde_hospedes > 0) {
                        $ddtres1i = _myfunc_stod($dados_apartamentos['dtres1i']);
                        $ddtres1f = _myfunc_stod($dados_apartamentos['dtres1f']);

                        while ($hospedes = mysqli_fetch_assoc($sel_hospedes)) {
                            echo "<br> - ".$hospedes['nome'];
                        }
                    }
                    
                    $sqlNfdocsTmp = "SELECT placatransp FROM $TNFDOCUMENTOS_TMP WHERE dono = '$dados_dono'";
                    $getNfdocsTmp = mysqli_query($conn_a, $sqlNfdocsTmp);
                    $placa = mysqli_fetch_assoc($getNfdocsTmp);

                    echo "</td><td><font size=1> $ddtres1i  a $ddtres1f </font></td><td align=right>".$dados_apartamentos['ramal']."</td><td>".$placa['placatransp']."</td><td>".$dados_cli['cidade']." - ".$dados_cli['uf']."</td>";

                    echo "<td>";
                    $chklinknovo = _myfuncoes_chklink('ID', $dados_dono . 'RECEITAS');
                    echo "<a href='logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink=$chklinknovo&codnat=HOTE&dono=$dados_dono'>Ocupa&ccedil;&atilde;o";
                    echo "</td>";
                    echo "<td>";
                    echo "<a href=logado.php?ac=hotel_relatorios_pdf&site=i&id_qual_relatorio=ficha_hospede_apto&idb=$dados_dono&id_hotel_local=$v[id_hotel_local]>Ficha";
                    echo "</td></tr>";
                }
            ?>
        </table>
    </div>
    <div class="col-lg-12">
        <table class='table table-hover'>
            <tr class='cel_subtit'><td colspan='6' align='center'>RESERVAS</td></tr>
            <tr class='cel_subtit'>
                <td>PER&Iacute;ODO</td>
                <td>CLASSIFICA&Ccedil;&Atilde;O</td>
                <td>NOME</td>
                <td>OBS</td>
                <td>QTO</td>
                <td></td>
            </tr>
            <?php
                $query = "SELECT * FROM $TAPARTAMENTOS_RESERVAS order by entrada";
                $result = mysqli_query($conn_a, $query);
                
                
                
                while ($reservas = mysqli_fetch_assoc($result)) {
                    $id = $reservas['id'];
                    #$dthoje=date('d/m/Y');
                    #$dtos=_myfunc_dtos(date($dthoje));
                    if ($reservas['entrada'] < $dtos) {
                        mysqli_query($conn_a, "DELETE FROM $TAPARTAMENTOS_RESERVAS WHERE id='$id'");
                    } else {
                        $entrada = _myfunc_stod($reservas['entrada']);
                        $saida = _myfunc_stod($reservas['saida']);
                        $classifica = $reservas['classifica'];
                        $apto = $reservas['id_hotel_local'];
                        
                        
                        $comboQtoLivre = '<select name="qtoReservar" id="mudarQto_'.$apto.'" class="qtoReservar"><option></option>';
                        foreach ($qtoLivre as $k=>$v) {
                            $select = ($v == $apto) ? 'selected="selected"' : "";
                            $comboQtoLivre .= '<option value="'.$reserChli[$v].'|'.$reserDono[$v].'|'.$v.'|'.$reservas['obs'].'|'.$id.'" '.$select.'>'.$v.'</option>';
                        }
                        $comboQtoLivre .= '</select>';
                        
                        if (!array_key_exists($apto, $reseOcupa)) {
                            $linkOcupaReserva = '<a id="resV_'.$apto.'" class="link" href="logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink='.$reserChli[$apto].'&codnat=HOTE&dono='.$reserDono[$apto].'&id_hotel_local='.$apto.'&obs='.$reservas['obs'].'&res='.$reservas['id'].'"><i class="fa fa-arrow-circle-o-down fa-3x pointer" id="entrada" title="Adiconar Hospede" abbr=""></i></a>';
                        } else {
                            $linkOcupaReserva = '<a id="resV_'.$apto.'" class="link" ><i class="fa fa-arrow-circle-o-down fa-3x pointer" id="entrada" title="Adiconar Hospede" abbr=""></i></a>';
                        }
                                               
                        echo '
                            <tr>
                                <td>' . $entrada . ' - ' . $saida . '</td>
                                <td>' . $apto . '-' . $classifica . '</td>
                                <td>' . $reservas['razao'] . '</td>
                                <td>'. $reservas['obs'].'</td>
                                <td>'.$comboQtoLivre.'</td>
                                <td>'.$linkOcupaReserva.'</td>
                            </tr>
                        ';
                    }
                }
                
                $transQto = '';
                foreach ($qtoLivre as $k=>$v) {
                    $transQto .= $v.'|';
                }
            ?>
        </table>
    </div>
</div>


<div class="modal fade modal-dialog-centered" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Titulo do modal</h4>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                corpo do modal
            </div>
            <!-- Modal footer -->
            <div class="modal-footer text-right">
                <button type="button" class="btn btn-link" data-dismiss="modal">Fechar</button>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">

    $(function () {
        
        $('.modalAbre').click(function() {
            var titulo = $(this).attr('abbr-title'),
                arquivo = '../hotel/'+$(this).attr('abbr'),
                erro = '',
                dados = '';
                //alert('Arquivo: ' + arquivo);
                //alert('Titulo: "'+titulo+'"');
                $(".modal-title").html(titulo);
                
                if (titulo === 'Tranferir Quarto') {
                    dados = 'qLivres=<?php echo $transQto;?>';
                }
                //setInterval(function() {
//                    // corpo da funcao
//                }, 3000); 

            $.ajax({
                url: arquivo,
                type: 'POST',
                data: dados,
                success: function (retorno) {
                    //console.log("x: " + event.pageX + ", y: " + event.pageY + " -> " + retorno);
                    $(".modal-body").html(retorno);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    erro = "Erro ao tentar abrir modal! ";
                    $(".modal-title").html(erro + errorThrown);
                    $(".modal-body").html('<h5>'+erro+'</h5>');
                }
            });
            $('#myModal').modal('show');
        });
    });
    
</script>
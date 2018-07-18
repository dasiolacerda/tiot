<link rel='stylesheet' type='text/css' href="../_css/font-awesome.css" rel="stylesheet">
<link rel='stylesheet' type='text/css' href="../_css/bootstrap.3.3.5-24.css" rel="stylesheet">

<link rel='stylesheet' type='text/css' href='../_css/themes/office/ui.datepicker.css' />
<link rel='stylesheet' type='text/css' href='../_css/jquery.autocomplete.css' />	
<script src="../_javascripts/jquery-ui-1.10.3/ui/jquery-ui.js"></script>
<script src="../_javascripts/bootstrap.js"></script>
<script src='../_javascripts/ui/ui.datepicker.min.js'></script>
<script src='../_javascripts/jquery.autocomplete.min.js'></script> 
<div class="well">
    <div class="container">
        <?php
        
            $ps = _myfunc_retorno_parametros_segmento();
        
            $legenda = '
                <div class="col-md-24 col-sm-24 col-xs-24 text-center">
                    <div class="col-md-offset-11 col-sm-offset-11 col-xs-offset-11">
                        <div style="float: left"><div style="background-color: #C9F880; width: 15px; height: 15px;float: left"></div>&nbsp; Livre&nbsp;&nbsp;&nbsp;&nbsp;</div>
                        <div style="float: left"><div style="background-color: #f38989; width: 15px; height: 15px;float: left"></div>&nbsp; Ocupado&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    </div>

                </div>
            ';
            
            echo $legenda;
            
            
            $sel = mysqli_query($conn_a, "SELECT * FROM $TAPARTAMENTOS ORDER BY id_hotel_local");
            $qtoLivre = array();
            while ($v = mysqli_fetch_assoc($sel)) {
                $reserva = '';
                $reserva_link = '';
                $ico_apto = '';
                $flag_mesa = 'LIVRE';
                $bg1 = '';
                $existe_id_mesa_local = _myfunc_existe_id_hotel_local($v['id_hotel_local']);

                // Verifica se existe um id setado para a mesa
                if ($existe_id_mesa_local == '') {
                    $reserva_link = 'Ocupar Mesa';
                } else {
                    $flag_mesa = 'OCUPADO';
                    $reserva_link = 'Acessar Mesa';
                }
                
                switch ($flag_mesa) {

                    case 'LIVRE':
                        $xdono = BASICO::_numero_aleatorio('LAN'); // Gera numero do dono
                        $chklinknovo = _myfuncoes_chklink('ID', $xdono . 'RECEITAS');
                        $bg1 = "#C9F880"; //"#ADFF2F"; // verde
                        $qtoLivre[$v['id_hotel_local']] = $v['id_hotel_local'];
                        $ico_apto = '
                            <span id="link_'.$v['id_hotel_local'].'">
                                <a href="logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink='.$chklinknovo.'&codnat=HOTE&dono='.$xdono.'&id_hotel_local='.$v['id_hotel_local'].'" title="'.$reserva_link.'"> <i class="fa fa-sign-in fa-3x" title="Ocupar mesa"></i></a>
                            </span>
                        ';

                        break;

                    case 'OCUPADO':
                        $bg1 = "#f38989"; // vermelho #FF0000
                        $xdono = $existe_id_mesa_local;
                        $chklinknovo = _myfuncoes_chklink('ID', $xdono . 'RECEITAS');
                        $ico_apto = '
                            <span id="link_'.$v['id_hotel_local'].'">
                                <a class="modalAbre pointer" abbr="banco.php?tipo=bar_taxaservico&param='.$v['id_hotel_local'].'" abbr-title="Taxa de servi&ccedil;o"><i class="fa fa-money fa-3x" title="Taxa de servi&ccedil;o"></i></a>
                                <a href="logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink='.$chklinknovo.'&codnat=HOTE&dono='.$xdono.'"> <i class="fa fa-expand fa-3x" title="Acessar Mesa"></i></a>
                                <a class="modalAbre pointer"  abbr="bar_transferencia.php?mesa='.$v['id_hotel_local'].'" abbr-title="Trocar mesa"><i class="fa fa-refresh fa-3x" title="Trocar mesa"></i></a>
                                <a href="logado.php?ac=api3_menu1_pdf&site=i&id_qual_relatorio=lista_previa&qualdono='.$xdono.'"><i class="fa fa-file-o fa-3x" title="Relat&aacute;rio Pr&eacute;vio"></i></a>
                            </span>
                        ';
                        break;
                }
                
                if (!(eregi(':052.010', $contasacesso_user))) {
                    $ico_apto = '';
                }

                echo '
                    <div id="id_' . $v['id_hotel_local'] . '_' . $flag_mesa . '" class="col-md-4 col-sm-12 col-xs-12 room" style="background-color: ' . $bg1 . ';">
                        ' . $reserva . $ico_apto . '<br /><br /><br /><span style="font-size:  18px; font-weight: bold;">MESA ' . $v['id_hotel_local'] . '</span><br /><br />' . '
                    </div>
                ';
            }
            
            $transQto = '';
            foreach ($qtoLivre as $k=>$v) {
                $transQto .= $v.'|';
            }
            
            echo $legenda;
        ?>
    </div>
</div>
<br />
<div class="col-md-24">
    <div class="col-lg-4"></div>
    <div class="col-lg-16"> 
        <table class='table table-hover'>
            <thead>
                <tr class='cel_subtit'>   
                    <th>MESA</th>
                    <th style="text-align: center">CLIENTE(s)</th>
                    <th style="text-align: center">VLR TOTAL</th>
                    <th style="text-align: center">VLR PRODUTOS</th>
                    <th style="text-align: center">VLR SERVI&Ccedil;OS</th>
                    <th style="text-align: center">TAXAS DE SERVI&Ccedil;OS</th>
                    <th>DATA</th>
                    <th>INFORM&Ccedil;&Otilde;ES </th>
                </tr>
            </thead>
            <tbody>
            <?php
                //echo _myfuncoes_user_menu_bott('HOTEL');

                $xsql = "SELECT a.dono,a.id_hotel_local,a.vcontabilnf,a.produtosnf,a.servicosnf,b.data,b.cnpjcpf,a.voutronf,a.vfretenf,b.valor FROM $TNFDOCUMENTOS_TMP as a,$TLANCAMENTOS_TMP as b WHERE a.id_hotel_local<>'' and a.dono=b.dono group by b.cnpjcpf,a.id_hotel_local ORDER BY id_hotel_local";
                $sel = mysqli_query($conn_a, "$xsql");

                while ($v = mysqli_fetch_assoc($sel)) {
                    $dados_cli = _myfun_dados_cnpjcpf($v[cnpjcpf]);
                    //$dados_apartamentos=_myfun_dados_apartamentos_hospedagem($v[id_hotel_local]);
                    $chklinknovo = _myfuncoes_chklink('ID', $v[dono] . 'RECEITAS');

                    echo '
                        <tr>
                            <td style="text-align: center">'.$v['id_hotel_local'].'</td>
                            <td align=lefth>'.$dados_cli['razao'].'</td>
                            <td style="text-align: center"> R$ '.number_format(($v['produtosnf'] + $v['servicosnf'] + $v['voutronf'] ), 2, ',', '.').'</td>
                            <td style="text-align: center"> R$ '.number_format($v['produtosnf'], 2, ',', '.').'</td>
                            <td style="text-align: center"> R$ '.number_format($v['servicosnf'], 2, ',', '.').'</td>
                            <td style="text-align: center"> R$ '.number_format($v['voutronf'], 2, ',', '.').'</td>
                            <td style="text-align: center">'._myfunc_stod($v['data']).'</td>
                            <td style="text-align: center"><a href="logado.php?ac=lan_receitas_edita&movimento=RECEITAS&chklink='.$chklinknovo.'&codnat=HOTE&dono='.$v['dono'].'"><span class="glyphicon glyphicon-eye-open"></span> + INFO</a></td>
                        </tr>
                    ';
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div><a href="#" class="volta_topo" style="font-family: Open Sans; color: rgb(0,115,255);"><img src='../api3/imagens/arrow-up.png'>&nbsp; Ir para o topo</a></div>  

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
                arquivo = '../bar/'+$(this).attr('abbr'),
                erro = '',
                dados = '';
                //alert('Arquivo: ' + arquivo);
                //alert('Titulo: "'+titulo+'"');
                $(".modal-title").html(titulo);
                
                if (titulo === 'Trocar mesa') {
                    dados = 'qLivres=<?php echo $transQto;?>';
                }
                
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

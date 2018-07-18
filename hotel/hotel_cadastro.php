<?php

    session_start();
    //$cnpjcpf_segmento = filter_input(INPUT_POST, 'cnpj');    
    $cnpjcpf_segmento = $_SESSION['ADMIN']['cnpj_segmento'];
    
    require_once('../includes/myfuncoes.php');
    require_once('../_conecta.php');
    require_once('../_tabelas.php');  
    require_once('../_metodos/basico.php');
    require_once('../includes/matriz.php');
    
    $id = filter_input(INPUT_GET, 'idb');
    if (!empty($id)) {
        $sqlQuarto = "SELECT * FROM $TAPARTAMENTOS WHERE id_hotel_local = '$id'";
        //exit($sqlQuarto);
        $getQuarto = mysqli_query($conn_a, $sqlQuarto);
        $qtoEdit = mysqli_fetch_array($getQuarto) ;
    } else {
        $qtoEdit['id_hotal_local'] = $qtoEdit['ocupacao_maxima'] = $qtoEdit['descricao'] = $qtoEdit['classifica'] = $qtoEdit['ramal'] = $qtoEdit['status'] = $qtoEdit['obs'] = $qtoEdit['dtres1i'] = $qtoEdit['dtres1f'] = $qtoEdit['dtres2i'] = $qtoEdit['dtres2f'] = $qtoEdit['dtres3i'] = $qtoEdit['dtres3f'] = '';
    }
    //exit(var_dump($_GET));
    //exit(var_dump($_SESSION));
    
    if (eregi(':052.001', $_SESSION['PERMSHotel'])) {
        //myfuncoes_usuario_sem_permissao_acesso();
        //exit;
     
?>
<div class="row">
    <div class="col-md-24">
        <form id="res">
            <div class="form-group">
                <div class="col-md-12 text-left">
                    <label for="id_hotel_local" class="control-label">Quarto:</label>
                    <input type="text" name="id_hotel_local" id="id_hotel_local" class="form-control" placeholder="(N&uacute;mero apto, su&iacute;te, etc)" value="<?php echo $qtoEdit['id_hotel_local']?>">
                    <input type="hidden" name="editar" id="editar" value="<?php echo $id?>">
                </div>
                <div class="col-md-12 text-left">
                    <label for="ocupacaoM" class="control-label">Ocupa&ccedil;&atilde;o Maxima</label>
                    <input name="ocupacaoM" id="ocupacaoM" class="form-control" placeholder="(N&uacute;mero m&aacute;ximo de hospedes)" value="<?php echo $qtoEdit['ocupacao_maxima']?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-24 text-left">
                    <label for="descricao" class="control-label">Descri&ccedil;&atilde;o:</label>
                    <input type="text" name="descricao" id="descricao" class="form-control" placeholder="Ex: Qaurto 07" value="<?php echo $qtoEdit['descricao']?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 text-left">
                    <label for="classifica" class="control-label">Classifica&ccedil;&atilde;o Quarto:</label>
                    <select name="classifica" id="classifica" class="form-control">
                        <option>(Vinculado ao tarif&aacute;rio)</option>
                        <?php
                            $m_matriz_apartamentos_classifica = MATRIZ::matriz_apartamentos_classifica();
                            $chk_classifica[$qtoEdit['classifica']] = 'selected';
                            foreach ($m_matriz_apartamentos_classifica as $k => $v) {
                                echo '<option value="' . $k . '" ' . $chk_classifica[$k] . '>' . $v . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-12 text-left">
                    <label for="ramal" class="control-label">Ramal:</label>
                    <input type="text" name="ramal" id="ramal" class="form-control" placeholder="Ex: 704" value="<?php echo $qtoEdit['ramal']?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-24 text-left">
                    <label for="status" class="control-label">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option>(Status do quarto)</option>
                        <?php
                            $m_matriz_apartamentos_status = MATRIZ::matriz_apartamentos_status();
                            $chk_status[$qtoEdit['status']] = 'selected';
                            foreach ($m_matriz_apartamentos_status as $k => $v) {
                                echo '<option value="'.$k.'"  '.$chk_status[$k].'>'. $v.'</option>';
                            }
                        ?>
                        <option value="L" <?php if ($qtoEdit['status'] == 'L') echo 'selected';?>>LIMPEZA</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 text-left">
                    <label for="dataEntra1" class="control-label">Per&iacute;odo de Reservas 1:</label>
                    <input type="text" name="dataEntra1" id="dataEntra1" class="form-control data" placeholder="Data de Entrada" value="<?php echo $dtaE1?>">
                </div>
                <div class="col-md-12 text-left">
                    <label class="control-label">&nbsp;</label>
                    <input type="text" name="dataSai1" id="dataSai1" class="form-control data" placeholder="Data de Saida" value="<?php echo $dtaF1?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 text-left">
                    <label for="dataEntra2" class="control-label">Per&iacute;odo de Reservas 2:</label>
                    <input type="text" name="dataEntra2" id="dataEntra2" class="form-control data" placeholder="Data de Entrada" value="<?php echo $dtaE2?>">
                </div>
                <div class="col-md-12 text-left">
                    <label class="control-label">&nbsp;</label>
                    <input type="text" name="dataSai2" id="dataSai2" class="form-control data" placeholder="Data de Saida" value="<?php echo $dtaF1?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-24 text-left">
                    <label for="obs" class="control-label">Observa&ccedil;&otilde;es:</label>
                    <textarea name="obs" id="obs" class="form-control" placeholder="Observa&ccedil;&atilde;o do hospede"><?php echo $qtoEdit['obs']?></textarea>
                </div>
            </div>
        </form>
        <div class="form-group">
            <div class="col-md-12 text-left col-md-offset-12">
                <label class="control-label">&nbsp;</label>
                <button type="submit" id="Quartos" class="form-control btn btn-default">Salvar</button>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-24" style="padding-top: 10px;">
                <div id="respostaQuartos"></div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 text-left col-md-offset-12">
                <label class="control-label">&nbsp;</label>
                <button type="submit" id="RemoverQaurto" class="form-control btn btn-default">Remover Quarto</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
            
        getQuartos();
        
        // datepicker do modal
        $(".data").datepicker();
      
        $("#Quartos").click(function() {
            var nome = $("#id_hotel_local").val(),
                ocupacao = $("#ocupacaoM").val(),
                desc = $("#descricao").val(),
                classi = $("#classifica").val(),
                ramal  = $("#ramal").val(),
                status = $("#status").val(),
                obs = $("#obs").val(),
                dataE1 = $("#dataEntra1").val(),
                dataF1 = $("#dataSai1").val(),
                dataE2 = $("#dataEntra2").val(),
                dataF2 = $("#dataSai2").val(),
                editQto = $("#editar").val(),
                dados = 'tipo=cadastrarQaurtos&quarto='+nome+'&ocupacaoM='+ocupacao+'&descricao='+desc+'&class='+classi+'&ramal='+ramal+'&status='+status+'&obs='+obs;
                
            if(editQto !== '') {
                dados += '&editar='+editQto;
            }
                
            if (dataE1 !== '') {
                dados += '&dtE1='+dataE1;
            }

            if (dataF1 !== '') {
                dados += '&dtF1='+dataF1;
            }

            if (dataE2 !== '') {
                dados += '&dtE2='+dataE2;
            }

            if (dataF1 !== '') {
                dados += '&dtF2='+dataF2;
            }
            $.ajax({
                type: "POST",
                data: dados,
                url: '../hotel/insertOnDataBase.php',
                success: function(m) {
                    if (m === 'TRUE'){
                        location.reload();
                    } else {
                        alert(m);
                    }
                },
                error: function(data, status, shr) {
                    //erro ao carregar o ajax, mostra a msg com o erro pra melhor analise
                    alert('Ocorreu um problema e n\u00E3o foi poss\u00EDvel remover os dados!\n\nTente novamente mais tarde!'+data +' <br> '+ status +' <br> '+ shr);
                        //console.log('Status: '+status+' - '+shr);
                }
            });
        });
        
        $("#RemoverQaurto").click(function() {
            var quartos =  ''; //$("input[name='id[]']").serialize();
            $("input[name='chk[]']:checked:enabled").each(function() {
                quartos=$(this).val()+","+quartos;
            });
            
            //alert('clicou aqki');
            $.ajax({
                type: "POST",
                data: 'tipo=removeQuarto&quartos='+quartos,
                url: '../hotel/insertOnDataBase.php',
                success: function(m) {
                    if (m === 'TRUE'){
                        getQuartos();
                    } else {
                        alert("Falha ao remover reserva, por favor entre em contato com o suporte! " + m);
                    }
                },
                error: function(data, status, shr) {
                    //erro ao carregar o ajax, mostra a msg com o erro pra melhor analise
                    alert('Ocorreu um problema e n\u00E3o foi poss\u00EDvel remover os dados!\n\nTente novamente mais tarde!'+data +' <br> '+ status +' <br> '+ shr);
                        //console.log('Status: '+status+' - '+shr);
                }
            });
        });
    });
    
    function getQuartos() {
        $.ajax({
            type: "POST",
            data: 'tipo=listaQuartos',
            url: '../hotel/insertOnDataBase.php',
            success: function(m) {
                //alert(m);
                $("#respostaQuartos").html(m);
                return;
            },
            error: function(data, status, shr) {
                //erro ao carregar o ajax, mostra a msg com o erro pra melhor analise
                alert('Ocorreu um problema e n\u00E3o foi poss\u00EDvel remover os dados!\n\nTente novamente mais tarde!'+data +' <br> '+ status +' <br> '+ shr);
                    //console.log('Status: '+status+' - '+shr);
            }
        });
    }
</script>

<?php 
    } else if (eregi(':052.015', $_SESSION['PERMSHotel'])) {
?>
<div class="row">
    <div class="col-md-24">
        <form id="res">
            <div class="form-group">
                <div class="col-md-24 text-left">
                    <label for="status" class="control-label">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option>(Status do quarto)</option>
                        <?php
                            $m_matriz_apartamentos_status = MATRIZ::matriz_apartamentos_status();
                            $chk_status[$qtoEdit['status']] = 'selected';
                            foreach ($m_matriz_apartamentos_status as $k => $v) {
                                echo '<option value="'.$k.'"  '.$chk_status[$k].'>'. $v.'</option>';
                            }
                        ?>
                        <option value="L" <?php if ($qtoEdit['status'] == 'L') echo 'selected';?>>LIMPEZA</option>
                    </select>
                </div>
            </div>
            
        </form>
        <div class="form-group">
            <div class="col-md-12 text-left col-md-offset-12">
                <label class="control-label">&nbsp;</label>
                <button type="submit" id="Quartos" class="form-control btn btn-default">Salvar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
            
        $("#Quartos").click(function() {
            var status = $("#status").val(),
                dados = 'tipo=statusQuarto&quarto=<?php echo $qtoEdit['id_hotel_local']?>&status='+status;
                
            $.ajax({
                type: "POST",
                data: dados,
                url: '../hotel/insertOnDataBase.php',
                success: function(m) {
                    //alert(m);
                    if (m === 'TRUE'){
                        location.reload();
                    } else {
                        alert(m);
                    }
                },
                error: function(data, status, shr) {
                    //erro ao carregar o ajax, mostra a msg com o erro pra melhor analise
                    alert('Ocorreu um problema e n\u00E3o foi poss\u00EDvel remover os dados!\n\nTente novamente mais tarde!'+data +' <br> '+ status +' <br> '+ shr);
                        //console.log('Status: '+status+' - '+shr);
                }
            });
        });
    });

</script>
<?php
    } else {
?>
<div class="row">
    <div class="col-md-24">
        <form id="res">
            <div class="form-group">
                <div class="col-md-24 text-center">
                    Usu&aacute;rio sem permiss&atilde;o!
                </div>
            </div>
            
        </form>
    </div>
</div>
<?php

    }
?>
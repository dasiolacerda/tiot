<?php

    session_start();
    //$cnpjcpf_segmento = filter_input(INPUT_POST, 'cnpj');    
    $cnpjcpf_segmento = $_SESSION['ADMIN']['cnpj_segmento'];
    
    require_once('../includes/myfuncoes.php');
    require_once('../_conecta.php');
    require_once('../_tabelas.php');  
    require_once('../_metodos/basico.php');
?>
<div class="row">
    <div class="col-md-24">
        <div class="form-group">
            <div class="col-md-12 text-left">
                <label for="dataEntra" class="control-label">Periodo:</label>
                <input type="text" name="dataEntra" id="dataEntra" class="form-control data" placeholder="Data de Entrada">
            </div>
            <div class="col-md-12 text-left">
                <label class="control-label">&nbsp;</label>
                <input name="dataSai" id="dataSai" class="form-control data" placeholder="Data de Saida">
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-24 text-left">
                <label for="hospede" class="control-label">Hospede:</label>
                <input type="text" name="hospede" id="hospede" class="form-control" placeholder="Hospede" >
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 text-left">
                <label for="quartoLivre" class="control-label">Quarto Disponivel:</label>
                <select name="quartoLivre" id="quartoLivre" class="form-control">
                    <option>Lista de Quartos Livre</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-24 text-left">
                <label for="obs" class="control-label">Observa&ccedil;&otilde;es:</label>
                <textarea name="obs" id="obs" class="form-control" placeholder="Observa&ccedil;&atilde;o do hospede"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 text-left col-md-offset-12">
                <label class="control-label">&nbsp;</label>
                <button type="submit" id="InserirReservas" class="form-control btn btn-default">Reservar</button>
            </div>
        </div>
        <?php if (eregi(':003.000', $_SESSION['PERMSHotel'])) { // essa permissão esta vinculada com atendimento cliente geral ?>
        <div class="form-group">
            <div class="col-md-24" style="padding-top: 10px;">
                <div id="respostaReservas"></div>
                
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 text-left col-md-offset-12">
                <label class="control-label">&nbsp;</label>
                <button type="submit" id="RemoverReserva" class="form-control btn btn-default">Remover Reservas</button>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
            
        getReservas();
        
        var carregou = false;
        // datepicker do modal de informa��o do hospede
        $(".data").datepicker();
        
        $("#ui-datepicker-div").click(function() {
            $("#quartoLivre").trigger('click');
            carregou = false;
        });
        
        $("#quartoLivre").click(function() {
            var dataE = $("#dataEntra").val(),
                dataF = $("#dataSai").val();
            
            if (dataE !== '' && dataF !== '' && carregou === false) {
                $.ajax({
                    type: "POST",
                    data: 'tipo=aptoReserva&data_in='+dataE+'&dataS='+dataF,
                    url: '../hotel/insertOnDataBase.php',
                    success: function(m) {
                        //alert(m);
                        $("#quartoLivre").html(m);
                        carregou = true;
                        return;
                    },
                    error: function(data, status, shr) {
                        //erro ao carregar o ajax, mostra a msg com o erro pra melhor analise
                        alert('Ocorreu um problema e n\u00E3o foi poss\u00EDvel remover os dados!\n\nTente novamente mais tarde!'+data +' <br> '+ status +' <br> '+ shr);
                            //console.log('Status: '+status+' - '+shr);
                    }
                }); 
            } 
        });
        
        $("#InserirReservas").click(function() {
            var hospede = $("#hospede").val(),
                quartoLivre = $("#quartoLivre").val(),
                obs = $("#obs").val(),
                dataE = $("#dataEntra").val(),
                dataF = $("#dataSai").val();
                
            if (dataE !== '' && dataF !== '' && quartoLivre !== '' && hospede !== '') {
                $.ajax({
                    type: "POST",
                    data: 'tipo=insereReserva&dataI='+dataE+'&dataS='+dataF+'&hospede='+hospede+'&qto='+quartoLivre+'&obs='+obs,
                    url: '../hotel/insertOnDataBase.php',
                    success: function(m) {
                        if (m === 'TRUE'){
                            getReservas();
                            $("#hospede").val('');
                            $("#quartoLivre").val('');
                            $("#obs").val('');
                            $("#dataEntra").val('');
                            $("#dataSai").val('');
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
            }
        });
        
        $("#RemoverReserva").click(function() {
            var reservas =  ''; //$("input[name='id[]']").serialize();
            $("input[name='chk[]']:checked:enabled").each(function() {
                reservas=$(this).val()+","+reservas;
            });
            
            $.ajax({
                type: "POST",
                data: 'tipo=removeReserva&reservas='+reservas,
                url: '../hotel/insertOnDataBase.php',
                success: function(m) {
                    if (m === 'TRUE'){
                        getReservas();
                    } else {
                        alert("Falha ao remover reserva, por favor entre em contato com o suporte!");
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
    
    function getReservas() {
        $.ajax({
            type: "POST",
            data: 'tipo=listaReservas',
            url: '../hotel/insertOnDataBase.php',
            success: function(m) {
                //alert(m);
                $("#respostaReservas").html(m);
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

<?php // include("hotel_reserva_browse.php"); ?>
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
            <div class="col-md-24 text-left">
                <label for="classifica" class="control-label">Classifica&ccedil;&atilde;o:</label>
                <input type="text" name="classifica" id="classifica" class="form-control" placeholder="Ex: Luxo" >
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 text-left col-md-offset-12">
                <label class="control-label">&nbsp;</label>
                <button type="submit" id="InserirClassi" class="form-control btn btn-default">Salvar</button>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-24" style="padding-top: 10px;">
                <div id="respostaClassifica">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 text-left col-md-offset-12">
                <label class="control-label">&nbsp;</label>
                <button type="submit" id="RemoverClassifica" class="form-control btn btn-default">Remover Classifica&ccedil;&atilde;o</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
            
        getClassifica();
        
        $("#InserirClassi").click(function() {
            var classi = $("#classifica").val();
            
            $.ajax({
                type: "POST",
                data: 'tipo=insereClassi&classifica='+classi,
                url: '../hotel/insertOnDataBase.php',
                success: function(m) {
                    if (m === 'TRUE'){
                    getClassifica();
                    $("#classifica").val('');
                    } else {
                        alert(m);
                    }
                },
                error: function(data, status, shr) {
                    //erro ao carregar o ajax, mostra a msg com o erro pra melhor analise
                    alert('Ocorreu um problema e n\u00E3o foi poss\u00EDvel carregar o arquivo!\n\nTente novamente mais tarde!'+data +' <br> '+ status +' <br> '+ shr);
                        //console.log('Status: '+status+' - '+shr);
                }
            });
        });
        
        $("#RemoverClassifica").click(function() {
            var input =  ''; //$("input[name='id[]']").serialize();
            $("input[name='chk[]']:checked:enabled").each(function() {
                input=$(this).val()+","+input;
            });
            
            $.ajax({
                type: "POST",
                data: 'tipo=removeClassifica&classifica='+input,
                url: '../hotel/insertOnDataBase.php',
                success: function(m) {
                    if (m === 'TRUE'){
                        getClassifica();
                    } else {
                        //alert(m);
                        alert("Falha ao remover Classificacao, por favor entre em contato com o suporte!");
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
    
    function getClassifica() {
        $.ajax({
            type: "POST",
            data: 'tipo=listaClassi',
            url: '../hotel/insertOnDataBase.php',
            success: function(m) {
                //alert(m);
                $("#respostaClassifica").html(m);
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
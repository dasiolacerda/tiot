<?php
    session_start();      
    $cnpjcpf_segmento = $_SESSION['ADMIN']['cnpj_segmento']; 

    require_once('../includes/myfuncoes.php');
    require_once('../_conecta.php');
    require_once('../_tabelas.php');  
    require_once('../_metodos/basico.php');  
    
    $ocupar = $_GET['ocupar'];
    $qualapto = $_GET['quarto'];
    $chklink = $_GET['chklink'];
    $dados_apartamentos = _myfun_dados_apartamentos_hospedagem($qualapto);

    # Busca dados da ocupa��o caso j� esteja cadastrado
    $dono = $_GET['dono'];
    $query = "SELECT placatransp, cidade_origem, cidade_destino, datasaida, hospedes FROM $TNFDOCUMENTOS_TMP WHERE dono='$dono'";
    //echo $query;
    $result = mysqli_query($conn_a, $query);
    $info = mysqli_fetch_assoc($result);
    $placa = $info['placatransp'];
    $origem = $info['cidade_origem'];
    $destino = $info['cidade_destino'];
    $dataSaida = _myfunc_stod($info['datasaida']);
    $hospedes = $info[hospedes];  // Para n�mero hospedes informados

    $bt_registrar = ''; // Bot�o habilitado, aceita edi��o da informa��o
    if (!empty($origem)) {
        $bt_registrar = 'disabled'; // N�o permite editar
    }

    # Usa FUN��O _myfun_dados_servicos para Buscar APTO no cadastro de servi�o para precifica��o e lan�amento no documento
    $dados_servicos = _myfun_dados_servicos($qualapto);
    $contasr = $dados_servicos['conta'];
    $descricaosr = $dados_servicos['descricao'];
    $unidadesr = $dados_servicos['unidade'];
    $valorsr = $dados_servicos['valor'];
    $clistserv = $dados_servicos['cod_lst'];
    $cst = $dados_servicos['cst_iss'];
    $csosn = $dados_servicos['csosn'];

    if (empty($hospedes))
        $hospedes = $dados_apartamentos['ocupacao_maxima'];  // Para n�mero maximo de  hospedes do APTO

    $xdono = $dono;
    $chklinknovo = _myfuncoes_chklink('ID', $xdono . 'RECEITAS');
    ?>
    <div class="row">
        <div class='col-md-24'>
            <!-- form action="logado.php?ac=hotel_ocupacao&ocupar=S&quarto=<?echo $qualapto;?>&id_hotel_local=<?echo $qualapto;?>&dono=<?echo $dono;?>" -->
            <div class="form-group">
                <div class="col-md-24 text-left">
                    <label for="txtDono" class="control-label">Controle:</label>
                    <input type="text" name="dono" id="txtDono" class="form-control" readonly="YES" value="<?php echo $dono?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 text-left">
                    <label for="txtQuarto" class="control-label">Apartamento:</label>
                    <input type="text" name="quarto" id="txtQuarto" class="form-control" readonly="YES" value="<?php echo $qualapto?>">
                </div>
                <div class="col-md-12 text-left">
                    <label for="txtconta" class="control-label">Servi&ccedil;o:</label>
                    <input type="text" name="contasr" id="txtconta" class="form-control" readonly="YES" value="<?php echo $contasr?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-18 text-left">
                    <label for="txtservico" class="control-label">Descri&ccedil;&atilde;o:</label>
                    <input type="text" name="descricaosr" id="txtservico" class="form-control" readonly="YES" value="<?php echo $descricaosr?>">
                </div>
                <div class="col-md-6 text-left">
                    <label for="txtvalor" class="control-label">Valor:</label>
                    <input type="text" name="valorsr" id="txtvalor" class="form-control" readonly="YES" value="<?php echo $valorsr?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6 text-left">
                    <label for="hospedes" class="control-label">Hospedes:</label>
                    <input type="number" name="hospedes" id="hospedes" class="form-control" value="<?php echo $hospedes?>" <?php echo $bt_registrar?>>
                </div>
                <div class="col-md-18 text-left">
                    <label for="placa" class="control-label">Placa do Ve&iacute;culo:</label>
                    <input type="text" name="placa" id="placa" class="form-control" value="<?php echo $placa?>" <?php echo $bt_registrar?> placeholder="Placa do veiculo">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 text-left">
                    <label for="cidade" class="control-label">Origem:</label>
                    <input type="text" name="cidade" id="cidade" class="form-control" autocomplete="off" value="<?php echo utf8_decode($origem)?>" <?php echo $bt_registrar?> placeholder="Cidade de origem">
                    <input type="hidden" name="idCidade" id="idCidade" />
                </div>
                
                <div class="col-md-12 text-left">
                    <label for="cidade1" class="control-label">Destino:</label>
                    <input type="text" name="city" id="city" class="form-control" value="<?php echo utf8_decode($destino)?>" <?php echo $bt_registrar?> placeholder="Cidade de destino">
                    <input type="hidden" name="idCity" id="idCity" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 text-left">
                    <label for="lanperiodo1" class="control-label">Data Saida:</label>
                    <input type="text" name="dataF" id="dataF" class="form-control data" value="<?php echo $dataSaida?>" <?php echo $bt_registrar?> placeholder="Previs&atilde;o de sa&iacute;da">
                </div>
                <div class="col-md-12 text-left">
                    <label class="control-label">&nbsp;</label>
                    <button type="submit" id="RegistrarReserva" class="form-control btn btn-default" <?php echo $bt_registrar?>>Registrar</button>
                </div>
            </div>
        </div>
    </div>
    
    

    <script type="text/javascript">

        $(function () {
            // ao digitar 3 letras da cidade de origem inicia uma busca no banco pra completar automaticamente
            $("#cidade").autocomplete("../hotel/consultCity.php?cpf=<?php echo $cnpjcpf_segmento?>", {
                width: 120,
                max: 20,        
                minChars: 3,
                selectFirst: false
            });
            $("#cidade").result(function(event, data, formatted) {
                if (data) {
                    $(this).parent().next().find("#cidade").val(data[1]);
                }
                $('#idCidade').val(data[2]);
            });
            
            // ao digitar 3 letras da cidade de destino inicia uma busca no banco pra completar automaticamente
            $("#city").autocomplete("../hotel/consultCity.php?cpf=<?php echo $cnpjcpf_segmento?>", {
                width: 120,
                max: 20,        
                minChars: 3,
                selectFirst: false
            });
            
            $("#city").result(function(event, data, formatted) {
                if (data) {
                    $(this).parent().next().find("#city").val(data[1]);
                }
                $('#idCity').val(data[2]);
            });
            
            // datepicker do modal de informa��o do hospede
            $(".data").datepicker();
            
            // ao clicar no btn de registar informa��es do hospede
            $('#RegistrarReserva').click(function(){
                var origem  = $("#cidade").val(),
                    destino = $("#city").val(),
                    dataSai = $("#dataF").val(),
                    placa   = $("#placa").val(),
                    hospede = $("#hospedes").val(),
                    contasR = '<?php echo $contasr;?>', 
                    descriR = '<?php echo $descricaosr;?>', 
                    unidR   = '<?php echo $unidadesr;?>', 
                    valorR  = '<?php echo $valorsr;?>', 
                    clieR   = '<?php echo $clistserv;?>',
                    cstR    = '<?php echo $cst;?>', 
                    csosnR  = '<?php echo $csosn;?>';
            
                if (origem === '') {
                    alert("Preencha o campo origem!");
                    $("#cidade").focus();
                } else if (destino === '') {
                    alert("Preencha o campo destino!");
                    $("#city").focus();
                } else if (dataSai === '') {
                    alert("Preencha o campo de data saida!");
                    $("#dataF").focus();
                } else {
                    $.ajax({
                        type: "POST",
                        data: 'tipo=infoHospedes&conta='+contasR+'&descr='+descriR+'&uni='+unidR+'&vlr='+valorR+'&cli='+clieR+'&cst='+cstR+'&cso='+csosnR+'&dno=<?php echo $dono;?>&chklink=<?php echo $chklink?>&qto='+<?php echo $qualapto?>+'&origem=' + origem + '&destino=' + destino + '&dataS=' + dataSai + '&placa='+placa+'&hospedes='+hospede,
                        url: '../hotel/insertOnDataBase.php',
                        success: function(m) {
                            //alert(m); 
                            // pega o retorno do ajax e quebra no # pra verificar se � false
                            var retorno = m.split("#");
                            if (retorno[0] === 'false') {
                                // se for false da esse alert aqui em baixo
                                alert(retorno[1]);
                            } else {
                                // se for verdadeiro da location no link
                                window.location = retorno[1];
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
        });
    </script>
<?php

    session_start();
    //$cnpjcpf_segmento = filter_input(INPUT_POST, 'cnpj');    
    $cnpjcpf_segmento = $_SESSION['ADMIN']['cnpj_segmento'];  

    $qtosL = filter_input(INPUT_POST, 'qLivres');
    
    $livres = explode('|', $qtosL);
    
    require_once('../includes/myfuncoes.php');
    require_once('../_conecta.php');
    require_once('../_tabelas.php');  
    require_once('../_metodos/basico.php');  

    $quarto = filter_input(INPUT_GET, 'quarto');
    
    $queryDono = "SELECT dono FROM $TNFDOCUMENTOS_TMP WHERE id_hotel_local = '$quarto' limit 1";
    //echo '<br>'.$queryDono.'<br>';
    $resultDono = mysqli_query($conn_a, $queryDono);
    if (mysqli_num_rows($resultDono)) {
        $dono = mysqli_fetch_assoc($resultDono);
    } else {
        exit('false#Falha ao consultar o dono do quarto.<br>'.var_dump($resultDono));
    }
    //var_dump($item);
    
    $query2 = "SELECT n.*, l.cnpjcpf, l.cnpjcpfvendedor, c.razao, c.endereco, c.cidade FROM $TNFDOCUMENTOS_TMP as n, $TCNPJCPF as c, $TLANCAMENTOS_TMP as l WHERE l.dono = n.dono AND l.cnpjcpf = c.cnpj AND  n.dono = '".$dono['dono']."' limit 1";
    //exit($query2);
    $result2 = mysqli_query($conn_a, $query2);
    if (mysqli_num_rows($result2)) {
        while ($infos = mysqli_fetch_assoc($result2)) {
            $cliente = $infos['razao'];
            $cnpjcpfCliente = $infos['cnpjcpf'];
            $quarto = $infos['id_hotel_local'];

            # Para chegar qtde de itens e qtde transferida
            $sql_itens = "SELECT * FROM $TITEM_FLUXO_TMP WHERE dono = '".$dono['dono']."'";
            //exit($sql_itens);
            $resultItens = mysqli_query($conn_a, $sql_itens);
            $numProdutos = mysqli_num_rows($resultItens);
            $cont = 0;
            while ($itens = mysqli_fetch_assoc($resultItens)) {
                $id = $itens['id'];
                $itensNF[$id]['habilitado'] = true;
                $itensNF[$id]['id'] = $itens['id'];
                $itensNF[$id]['cod'] = $itens['cprod'];
                $itensNF[$id]['qtidade'] = $itens['qcom'];
                $itensNF[$id]['unidade'] = $itens['ucom'];
                $itensNF[$id]['produto'] = $itens['xprod'];
                $itensNF[$id]['valor'] = $itens['vprod'];
                $itensNF[$id]['tipo'] = $itens['tipo_lancamento'];
                $itensNF[$id]['cont'] = $cont;

                $cont++;
            }
        }
        //echo "<form id='formConfirmar' action='logado.php?ac=hotel_transferencia' method='post' name='flancamento' onSubmit='return validate(this);'>";
        //echo "</form>";
        //echo "<button id='btnConfirmar' onClick='enviar($quarto,$idquartonovo)'>CONFIRMAR TRANSFER�NCIA</button>";
    }
?>

<div class="row">
    <div class="col-md-24">
        <div>
            <h4>O APTO/QUARTO para transfer&ecirc;ncia j&aacute; deve estar aberto/ocupado</h4>
        </div>
        <div class="form-group">
            <div class="col-md-12 text-left">
                <label for="quartoAtual" class="control-label">Quarto Atual:</label>
                <input type="text" name="quartoAtual" id="quartoAtual" class="form-control" readonly="YES" value="<?php echo $quarto?>">
            </div>
            <div class="col-md-12 text-left">
                <label for="quartoNovo" class="control-label">Quarto Novo:</label>
                <select name="quartoNovo" id="quartoNovo" class="form-control">
                    <option>Escolha o quarto</option>
                    <?php
                        foreach ($livres as $k=>$v) {
                            if (!empty($v)) {
                                echo '<option value="'.$v.'">'.$v.'</option>';
                            }
                            
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-24" style="padding-top: 10px;">
                <table class='table table-hover'>
                    <tr><td colspan="6" class="cel_subtit text-center">PRODUTOS</td></tr>
                    <tr class="cel_subtit">
                        <td>COD - PRODUTO</td>
                        <td>UN</td>
                        <td>QTDE</td>
                        <td>VALOR</td>
                        <td>TIPO</td>
                    </tr>
                    <?php 
                        $nItens = 0;
                        foreach ($itensNF as $itens) {
                            $nItens++;
                            echo "
                                <tr id='linha_".$itens['id']."'>
                                    <td class='selE'>" . $itens['cod'] . " - " . $itens['produto'] . "</td>
                                    <td class='selE'>" . $itens['unidade'] . "</td>
                                    <td class='selE'>" . $itens['qtidade'] . "</td>
                                    <td class='selE'>" . $itens['valor'] . "</td>
                                    <td class='selE'>" . $itens['tipo'] . "<input type='hidden' id='id_".$itens['id']."' checked='checked' name='id[]' value=" . $itens['id'] . "></td>
                                </tr>
                            ";
                        }
                    ?>
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 text-left col-md-offset-12">
                <label class="control-label">&nbsp;</label>
                <button type="submit" id="RealizarTransferencia" class="form-control btn btn-default">Tranferir</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        // ao clicar no btn de tranferencia de quarto 
        $('#RealizarTransferencia').click(function(){
            var quartoNovo = $('#quartoNovo').val();            
            
            if (quartoNovo === '') {
                alert('Escolha o quarto novo!');
                $('#quartoNovo').focus();
                return;
            } else {
                $.ajax({
                    type: "POST",
                    data: 'tipo=transferenciaQuarto&qtoAtual=<?php echo $quarto;?>&qtoAntigo=<?php echo $dono['dono']?>&qtoNovo='+quartoNovo,
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
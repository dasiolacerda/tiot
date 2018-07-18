<?php
$form_tam_label = 200;

$form_width = '900px;';

include('layoutform.php');


include('periodo.php');

if (empty($lanperiodo1)) {
    $lanperiodo1 = $dthoje;
}

if (empty($lanperiodo2)) {
    $lanperiodo2 = $dthoje;
}
$datarel = $dthoje;
?>


<div id="container">

    <div style="margin:20 auto; text-align:center">


        <div class="gradient">
            <fieldset>
                <legend style='FONT-SIZE: 16px'>&nbsp;&nbsp;
                    <img src='../imagens/relat_graficos.gif' border=0>&nbsp;RELAT&Oacute;RIOS E GR&Aacute;FICOS

                </legend>						
                <?php
                if ($_GET[id]) {
                    $qual_relatorio = $_GET['id'];
                    $inputs_menu = '';
                    switch ($qual_relatorio) {

                        case "boletim_ocupacao_hoteleira";
                            $nomerel = "Boletim de ocupa&ccedil;&atilde;o - BOH";
                            $inputs_menu = 'periodo';
                            break;
                        case "relatorio_ocupacao":
                            $nomerel = "Relat&oacute;rio de ocupa&ccedil;&atilde;o";
                            $inputs_menu = 'periodo';
                            break;

                        case "relatorio_reservas":
                            $nomerel = "Relat&oacute;rio de Reservas";
                            $inputs_menu = 'periodo';
                            $ordem_indice = "hospede,HOSPEDE:data,DATA ENTRADA";
                            break;
                        case "relatorio_registro_geral":
                            $nomerel = "Relat&oacute;rio de Registro Geral de h&oacute;spedes";
                            break;
                        case "relatorio_refeicao":
                            $nomerel = "Relat&oacute;rio de H&oacute;spedes pra Refei&ccedil;&atilde;o";
                            break;
                        case "relatorio_veiculo":
                            $nomerel = "Relat&oacute;rio de Ve&iacute;culos X H&oacute;spedes";
                            break;
                        case "relatorio_geral":
                            $nomerel = "Relat&oacute;rio de Geral de Hospedagem";
                            break;
                        case 'relatorio_analitico_periodo':
                            $nomerel = "Relta&oacute;rio Anal&iacute;tico no per&iacute;odo";
                            $inputs_menu = 'periodo,modelo_documento';
                            $ordem_indice = "c.razao,Nome:n.id_hotel_local,APTO:n.data,Data Saida:n.datacad,Data Entrada";
                            //$ordem_indice = "data,DATA DOC;n.id_hotel_local,APTO";
                            break;
                        case 'relatorio_espelho':
                            $nomerel = "Relta&oacute;rio Espelho";
                            $inputs_menu = 'dono';
                            break;
                        case 'logs':
                            $nomerel = "Logs";
                            $inputs_menu = 'periodo,usuario,dono, cnpjcpf,produto';
                            break;
                        case 'relatorio_diario':
                            $nomerel = "Relat&oacute;rio Di&aacute;rio";
                            $inputs_menu = 'datarel';
                            break;
                    }

                    $pagini = 1;
                    $pagint1 = 1;
                    $pagint2 = 999999;
                    ?>

                    <form action="logado.php?ac=hotel_relatorios_pdf&site=i" method="post" name="flancamento" onSubmit="return validate(this);">

                        <input type="hidden" name="id_qual_relatorio" value="<?= $qual_relatorio; ?>">

                        <?php
                        if (isset($_GET['tipo']))
                            echo "<input type='hidden' name='tipo' value='grafico'>";

                        echo"<font size=2><i><U>$nomerel</u></i></font><br>";

                        $xqualsegmento = $_POST['xqualsegmento'];
                        $xqualclifor = $_POST['xqualclifor'];
                        $xqualusuario = $_POST['xqualusuario'];
                        $xqualunidadenegocio = $_POST['xqualunidadenegocio'];
                        $xnome = $_POST['nome'];
                        $xnomeusuario = $_POST['xnomeusuario'];
                        $xnomeunidadenegocio = $_POST['xnomeunidadenegocio'];
                        $xqualdocfinanceiro = $_POST['xqualdocfinanceiro'];

                        if (empty($xqualsegmento)) {
                            $xqualsegmento = $_GET['xqualsegmento'];
                        }
                        if (empty($xqualclifor)) {
                            $xqualclifor = $_GET['xqualclifor'];
                        }
                        if (empty($xqualusuario)) {
                            $xqualusuario = $_GET['xqualusuario'];
                        }
                        if (empty($xqualdocfinanceiro)) {
                            $xqualdocfinanceiro = $_GET['xqualdocfinanceiro'];
                        }

                        $ordenar_por = $_POST['ordenarpor'];

                        $filtro_link = "&qualsegmento=$xqualsegmento&xqualclifor=$xqualclifor&xqualusuario=$xqualusuario&xqualdocfinanceiro=$xqualdocfinanceiro";

                        $filtro_opcoes_inputmenus_form_name = 'flancamento';

                        echo " <center>";
                        include('filtro_opcoes_inputmenus.php'); 
                        echo " </center>";
                        ?>

                    </form>
                    <?php
                }

                // relatorios e graficos

                $m_acessos_user = MATRIZ::m_user_acessos();

                //echo $contasacesso_user;

                if ((eregi(':013.000', $contasacesso_user))) { // RAZAO
                    echo "<br> ";
                    echo "<table cellspacing='2' cellpadding='4'>";

                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=boletim_ocupacao_hoteleira title='Boletim de Ocupa&ccedil;&atilde;o'><img src='../imagens/visto_vermelho.png' border=0 >BOLETIM DE OCUPA&Ccedil;&Atilde;O HOTELEIRA BOH</a>";
                    echo _espacos(3);
                    echo " <font size=1> BOLETIM DE OCUPA&Ccedil;&Atilde;O HOTELEIRA BOH";
                    echo "</td></tr>";

                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_ocupacao title='Relat&oacute;rio de Ocupa&ccedil;&atilde;o'><img src='../imagens/visto_vermelho.png' border=0 >RELAT&Oacute;RIO DE OCUPA&Ccedil;&Atilde;O</a>";
                    echo _espacos(3);
                    echo " <font size=1> (Apto, Classifica&ccedil;&atilde;o, Data Entrada, Data Sa&iacute;da, Nro Hospedes, Valor)";
                    echo "</td></tr>";

                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_reservas title='Relat&oacute;rio de Reservas'><img src='../imagens/visto_vermelho.png' border=0 >RELAT&Oacute;RIO DE RESERVAS</a>";
                    echo _espacos(3);
                    echo " <font size=1> (Data Reserva, Requerente, Apto, Data Entrada, Data Sa&iacute;da)";
                    echo "</td></tr>";

                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_registro_geral title='Relat&oacute;rio de Registro geral de h&oacute;spedes'><img src='../imagens/visto_vermelho.png' border=0 >REGISTRO GERAL DE HOSPEDES</a>";
                    echo _espacos(3);
                    echo " <font size=1> (Hospede, Cpf, Nascimento, Data Entrada, Apto, Proced&ecirc;ncia, Destino)";
                    echo "</td></tr>";

                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_refeicao title='Relat&oacute;rio de hospedes para refei&ccedil;ões'><img src='../imagens/visto_vermelho.png' border=0 >RELAT&Oacute;RIO DE HOSPEDES PARA REFEI&Ccedil;&Otilde;ES</a>";
                    echo _espacos(3);
                    echo " <font size=1> (Apto, Hospede , n&uacute;mero de hospedes)";
                    echo "</td></tr>";

                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_veiculo title='Relat&oacute;rio de Veículos X H&oacute;spedes'><img src='../imagens/visto_vermelho.png' border=0 >RELAT&Oacute;RIO DE VE&Iacute;CULOS x HOSPEDES</a>";
                    echo _espacos(3);
                    echo " <font size=1> (Placa, Hospede, Apto, Ramal, Data)";
                    echo "</td></tr>";

                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_geral title='Relat&oacute;rio Geral de Hospedagem'><img src='../imagens/visto_vermelho.png' border=0 >RELAT&Oacute;RIO GERAL DE HOSPEDAGEM</a>";
                    echo _espacos(3);
                    echo " <font size=1> (Apto, Classifica&ccedil;&atilde;o, Hospede, Data, Vlr Total, Vlr Produtos, Vlr Servi&ccedil;os)";
                    echo "</td></tr>";

                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_analitico_periodo title='Relat&oacute;rio Anal&iacute;tico no Per&iacute;odo'><img src='../imagens/visto_vermelho.png' border=0 >RELAT&Oacute;RIO ANAL&Iacute;TICO NO PER&Iacute;ODO</a>";
                    echo _espacos(3);
                    echo " <font size=1> (Apto, H&oacute;spede, UF, Emiss&atilde;o, Documento)";
                    echo "</td></tr>";
                    
                    echo "<tr><td>";
                    echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_espelho title='Relat&oacute;rio Espelho'><img src='../imagens/visto_vermelho.png' border=0 >RELAT&Oacute;RIO ESPELHO</a>";
                    echo _espacos(3);
                    echo " <font size=1> (Apto, H&oacute;spede, UF, Emiss&atilde;o, Documento)";
                    echo "</td></tr>";
                    
                    if (eregi(':000.000', $contasacesso_user)) {
                        echo "<tr><td>";
                        echo "<a href=logado.php?ac=hotel_relatorios&id=logs title='Logs'><img src='../imagens/visto_vermelho.png' border=0 >Logs</a>";
                        echo _espacos(3);
                        echo " <font size=1> (Usuario, quarto, acao, produto)";
                        echo "</td></tr>";
                        
                        echo "<tr><td>";
                        echo "<a href=logado.php?ac=hotel_relatorios&id=relatorio_diario title='Relat&oacute;rio Di&aacute;rio'><img src='../imagens/visto_vermelho.png' border=0 >Relat&oacute;rio Di&aacute;rio</a>";
                        echo _espacos(3);
                        echo " <font size=1> (Usuario, quarto, acao, produto)";
                        echo "</td></tr>";
                    }

                    echo "</table>";
                }
                ?>
            </fieldset>
        </div>
    </div>

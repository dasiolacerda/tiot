<?php

namespace Posprint;

/**
 * Esta classe foi colocada aqui apneas para facilitar o desenvolvimento, seu local correto
 * é no repositório sped-da
 *
 * em caso de contingência criar duas vias consumidor e estabelecimento

  Classe usada pra montar uma impressão usando as classes existentes

 */
use Posprint\Printers\PrinterInterface;
use InvalidArgumentException;

class DanfcePosEspelho {
    
    public $PATH = '/var/www/html/pdc20180717/';

    /**
     * NFCe
     * @var SimpleXMLElement
     */
    protected $nfce = '';

    /**
     * protNFe
     * @var SimpleXMLElement
     */
    protected $protNFe = '';

    /**
     * Printer
     * @var PrinterInterface
     */
    protected $printer;

    /**
     * Documento montado
     * @var array
     */
    protected $da = array();

    /**
     * Total de itens da NFCe
     * @var integer
     */
    protected $totItens = 0;

    
    /**
     * Carrega a impressora a ser usada
     * a mesma deverá já ter sido pré definida inclusive seu
     * conector
     *
     * @param PrinterInterface $this->printer
     */
    public function __construct(PrinterInterface $printer) {
        $this->printer = $printer;
    }


    /**
     * Monta a DANFCE para uso de impressoras POS
     */
    public function monta($conn_a, $TPLANOCT, $TCNPJCPF, $suprimir, $dadosPrestador = array()) {
        
        
        $this->razaosocial  = $dadosPrestador['razaosocial'];
        $this->cnpjcpfRazao = $dadosPrestador['cnpjcpf'];
        $this->tel = (empty($dadosPrestador['tel'])) ? "" : _myfunc_mascara_string("(##)####-####", $dadosPrestador['tel']);
        $this->endereco = $dadosPrestador['endereco'];
        $this->numero = $dadosPrestador['num'];
        $this->cidade = $dadosPrestador['cidade'];
        $this->bairro = $dadosPrestador['bairro'];
        $this->estado = $dadosPrestador['uf'];
        $this->totItens = count($dadosPrestador['itens']);
        
        $this->printer->setPrintMode('ESCBEMA');
        
        $this->printer->setCodePage('CP860');
        $this->printer->setSpacing(1, 15);
        $this->printer->setCharSpacing(0);
        
        $this->printer->setCondensed(); // Entra Condensado
        
        $this->parteI();        //parte 1
        $this->parteIII($dadosPrestador['itens'], $suprimir);      //parte 3
        $this->parteV( $conn_a, $TPLANOCT, $dadosPrestador['lancamentos']);        //parte 5
        $this->parteII($conn_a, $TCNPJCPF);       //parte 2
        
        $this->printer->setCondensed(); // Sai Condensado
        //$this->printer->Cut();
    }

    /**
     * Manda os dados para a impressora ou
     * retorna os comandos em ordem e legiveis
     * para a tela
     */
    public function printDanfe() {
        $resp = $this->printer->send();
        if (!empty($resp)) {
            echo str_replace("\n", "<br>", $resp);
        }
    }

    /**
     * Recupera a sequiencia de comandos para envio
     * posterior para a impressora por outro
     * meio como o QZ.io (tray)
     *
     * @return string
     */
    public function getCommands() {
        $aCmds = $this->printer->getBuffer('binA');
        return implode("\n", $aCmds);
    }

    /**
     * Parte I - Emitente
     */
    protected function parteI() {
        
        $this->printer->setAlign('C');
        $this->printer->text($this->razaosocial);
        $this->printer->lineFeed(1);
        $this->printer->setBold(); // Entra Negrito
        $this->printer->text($this->tel);
        $this->printer->setBold(); // Sai do Negrito
        $this->printer->lineFeed(1);
        
        $this->printer->text($this->endereco . ', ' . $this->numero);
        
        $this->printer->text(' '.$this->bairro . ' ' . $this->cidade . ' ' . $this->estado);
        $this->printer->lineFeed(1);
        $this->printer->text(str_pad('-', 66, '-'));
        $this->printer->lineFeed(1);
    }

    /**
     * Parte II - Tomador da compra
     */
    protected function parteII($conn_a, $TCNPJCPF) {
        $getTomador = mysqli_query($conn_a, "SELECT * FROM $TCNPJCPF WHERE cnpj = '".$this->cnpjCliente."'");
        $dadosTomador = mysqli_fetch_assoc($getTomador);
        
        $telCliente = empty($dadosTomador['tel']) ? "" : _myfunc_mascara_string("(##)####-####", $dadosTomador['tel']);
        
        $this->printer->setAlign('L');
        $this->printer->text('Nome: '.$dadosTomador['razao']);
        $this->printer->SetBold(); //Entra do negrito
        $this->printer->text(' CNPJ: '.$dadosTomador['cnpj']);
        $this->printer->SetBold(); //Sai do negrito
        $this->printer->lineFeed(1);
        $this->printer->text('End: '.$dadosTomador['endereco'] . ', ' . $dadosTomador['num']);
        $this->printer->text(' Tel: '.$telCliente);
        $this->printer->lineFeed(1);
        //$this->printer->text('Bairro: '.$dadosTomador['bairro'].' '.$dadosTomador['cnpj'].' - '.$dadosTomador['uf']);
        //$this->printer->lineFeed(1);
        $this->printer->text(str_pad('-', 66, '-'));
        $this->printer->lineFeed(1);
        $this->printer->setAlign('C');
        $this->printer->text('NAO E VALIDO COMO DOCUMENTO FISCAL');
    }

    /**
     * Parte III - Itens da venda
     */
    protected function parteIII($dadosItens, $suprimir) {
        $this->printer->setAlign('L');
    
        $this->printer->text('ITEM CÓDIGO  DESCRIÇÃO  UN  QTDE    V.UNIT     V.TOTAL');
        $nItem = 1;
        $vtotal = 0;
        foreach ($dadosItens as $kIten=>$vItem) {
            $this->printer->lineFeed(1);
            $this->printer->text(str_pad($nItem, 5) . '' . str_pad(substr($vItem['cprod'], 0, 13), 10) . '' . substr($vItem['xprod'], 0, 50));
            $this->printer->lineFeed(1);
            if ($suprimir <> 'S') {
                $this->printer->text(str_pad(' ', 24, ' ') . '' . $vItem['ucom'] . '   ' . number_format($vItem['qcom'], 2, '.', '') . '    ' . number_format($vItem['vuncom'], 2, '.', '') . '   ' . number_format($vItem['vprod'], 2, '.', ''));
            } else {
                $this->printer->text(str_pad(' ', 24, ' ') . '' . $vItem['ucom'] . '   ' . number_format($vItem['qcom'], 2, '.', '') );
            }
            $nItem++;
            $vtotal += $vItem['vprod'];
        }
        $this->printer->lineFeed(1);
        $this->printer->text(str_pad('-', 66, '-'));
        $this->vtotal = $vtotal;
    }

    /**
     * Parte IV - Forma de pagamento
     */
    protected function parteV($conn_a, $TPLANOCT, $dadosLancamentos) {
        $this->printer->lineFeed(1);
        $this->printer->setAlign('L');
        $this->printer->text(str_pad('QTDE. TOTAL DE ITENS', 60));
        $this->printer->setAlign('R');
        $this->printer->text($this->totItens);
        $this->printer->lineFeed(1);
        $this->printer->setAlign('L');
        $this->printer->text(str_pad('VALOR TOTAL', 55) . 'R$ '); 
        $this->printer->setAlign('R');
        $this->printer->text(number_format($this->vtotal, 2, '.', ''));
        $this->printer->lineFeed(1); // pula uma linha
        $this->printer->SetBold(); // Entra Negrito
        $this->printer->setAlign('L'); // alinha a esquerda
        $this->printer->text(str_pad('FORMA PAGAMENTO', 55) . 'VALOR PAGO'); // escreve o texto
        $this->printer->SetBold(); //Sai do negrito
        
        foreach ($dadosLancamentos as $kLanca=>$vLanca) {
            if (!empty($vLanca['contad']) && substr($vLanca['contad'], 0, 1) == '1') {
                $sqlDescPlanoCT = "SELECT descricao FROM $TPLANOCT WHERE conta = '".$vLanca['contad']."'";
                $getDescPlanoCT = mysqli_query($conn_a, $sqlDescPlanoCT);
                $descPlanoCT = mysqli_fetch_assoc($getDescPlanoCT);
                
                $this->printer->lineFeed(1);
                $this->printer->setAlign('L');
                $this->printer->text(str_pad($descPlanoCT['descricao'], 55));
                $this->printer->setAlign('R');
                $this->printer->text('R$ ' . number_format($vLanca['valor'], 2, '.', '')); 
                $this->cnpjCliente = $vLanca['cnpjcpf'];
            }
        }
        $this->printer->lineFeed(1);
        $this->printer->text(str_pad('-', 66, '-'));
        $this->printer->lineFeed(1);
    }
}

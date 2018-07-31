<?php
//echo var_dump($_REQUEST);
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
	include_once('bootstrap.php');
	use Posprint\DanfcePosEspelho;
	use Posprint\Printers\Bematech;
	#use Posprint\Connectors\Serial;
	use Posprint\Connectors\File;
	
        $dono = filter_input(INPUT_GET, 'dono');
        $lancamentos = $itens = array();

        $sqlPrestador = "SELECT * FROM $TSEGMENTOS WHERE cnpjcpf = '$cnpjcpf_segmento'";
        $getPrestador = mysqli_query($conn_a, $sqlPrestador);
        $dadosPrestador = mysqli_fetch_assoc($getPrestador);

        $sqlLanc = "SELECT * FROM $TLANCAMENTOS_TMP WHERE dono = '$dono'";
        $getLanc = mysqli_query($conn_a, $sqlLanc);
        while ($lanc = mysqli_fetch_assoc($getLanc)) {
            $lancamentos[] = $lanc;
        }

        $getItem = mysqli_query($conn_a, "SELECT * FROM $TITEM_FLUXO_TMP WHERE dono = '".$dono."'");
        while ($item = mysqli_fetch_assoc($getItem)) {
            $itens[] = $item;
        }

        $dadosPrestador['itens'] = $itens;
        $dadosPrestador['lancamentos'] = $lancamentos;
	
        $suprimir = filter_input(INPUT_GET, 'suprimir');
        
	// Gerando arquivo do buffer
	$connector = null;
        $filename = '../nfefiles/'.$cnpjcpf_segmento.'/spedtxt/'.$dono.'.prn';
        
	$connector = new Posprint\Connectors\File($filename);
	$printer = new Posprint\Printers\Bematech($connector);
	$danfe = new DanfcePosEspelho($printer);

	$danfe->monta($conn_a, $TPLANOCT, $TCNPJCPF, $suprimir, $dadosPrestador);
	$danfe->printDanfe();
        
        $dir = explode('/', $_SERVER['HTTP_REFERER']);
        //echo var_dump($dir);
        $file = $dir[0].'//'.$dir[2].'/'.$dir[3].'/nfephp50/'.$filename;
        
        //exit(var_dump($file));
        $host  = $_SERVER["REMOTE_ADDR"];
        //echo 'Servidor: '.$host.'<br>';
        $ip = filter_input(INPUT_GET, 'ip');
        $final_ip= substr(strrchr($ip, "."), 1) ;
        $porta=strval($final_ip)+5000;
        //echo 'Porta: '.$porta.'<br>';        
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        // connect to server
        $result = socket_connect($socket, $host, $porta) or die("Could not connect to server\n");  
        // send string to server
        socket_write($socket, $file, strlen($file)) or die("Could not send data to server\n");
        // get server response
        $result1 = socket_read ($socket, 1024) or die("Could not read server response\n");
        // close socket
        socket_close($socket);
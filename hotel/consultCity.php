<?php

    $cnpjcpf_segmento = filter_input(INPUT_GET, 'cpf'); 
    
    require_once('../includes/myfuncoes.php');
    require_once('../_conecta.php');
    require_once('../_tabelas.php');  
    require_once('../_metodos/basico.php'); 
    
    $query = filter_input(INPUT_GET, 'q');

    $q = strtolower(utf8_decode($query));
    
    $sqlCity = mysqli_query($conn_a, "SELECT id, nome, uf, iduf FROM $TTAB_MUNICIPIOS where nome like '%$q%'");
    
    while ($c = mysqli_fetch_object($sqlCity)) {
        echo utf8_encode($c->nome)."|".utf8_encode($c->nome)."|".$c->id."|".$c->iduf."|".$c->uf."\n";
    }
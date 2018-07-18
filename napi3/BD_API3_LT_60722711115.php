<?php

    $ps = _myfunc_retorno_parametros_segmento(); // $ps = parametros do sistema
    //exit(var_dump($ps));
    //exit($dono);

    if (isset($ps['hotel']) && !empty($ps['hotel'])) {
        $sqlQtoHotel = "SELECT id_hotel_local as qto FROM $TNFDOCUMENTOS WHERE dono = '" . $dono . "'";
        //echo $sqlQtoHotel;
        $getQtoHotel = mysqli_query($conn_a, $sqlQtoHotel);
        $qto = mysqli_fetch_array($getQtoHotel);
        if ($qto['qto']) {
            mysqli_query($conn_a, "UPDATE $TAPARTAMENTOS SET dataatu='$dtos', cnpjcpfatu='$info_cnpj', status='L' WHERE id_hotel_local = '" . $qto['qto'] . "'");
        }
        //echo "UPDATE $TAPARTAMENTOS SET dataatu='$dtos', cnpjcpfatu='$info_cnpj', status='L' WHERE id_hotel_local = '".$qto['qto']."';<br>";
    }


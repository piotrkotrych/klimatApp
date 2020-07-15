<?php

$link = mysqli_connect('10.47.8.236', 'AOITHT', 'eltwin123', 'elt') or die(mysqli_connect_errno());
mysqli_set_charset($link, "utf8");
header('Content-Type: application/json');

switch ($_GET['type']) {
    case 'getSensors':
        $sql = mysqli_query($link, "select distinct(miejsce) as sensor from klimat");
        $sensors = array();
        while ($r = mysqli_fetch_assoc($sql)) {
            

            $sensor = $r['sensor'];

            $sql1 = mysqli_query($link, "SELECT created, napiecie_baterii as bat, (SELECT procent FROM `klimat_bat` WHERE napiecie_baterii between vFrom and vTo) as procent, round(avg(temperatura),1) as temp, round(AVG(wilgotnosc),1) as hum FROM `klimat` WHERE miejsce = '$sensor' GROUP by created, bat order by created desc limit 1");
            if($res = mysqli_fetch_assoc($sql1)){
                $r['data'] = $res['created'];
                $r['temp'] = $res['temp'];
                $r['hum'] = $res['hum'];
                $r['bat'] = $res['procent'];
            }

            array_push($sensors, $r);

        }
        
        echo json_encode($sensors);
        break;
    
    default:
        # code...
        break;
}

?>
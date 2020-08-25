<?php

$link = mysqli_connect('10.47.8.236', 'AOITHT', 'eltwin123', 'elt');
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
mysqli_set_charset($link, "utf8");
header('Content-Type: application/json');

switch ($_GET['type']) {
    case 'getSensors':
        $sql = mysqli_query($link, "select distinct(miejsce) as sensor from klimat");
        $sensors = array();
        while ($r = mysqli_fetch_assoc($sql)) {
            

            $sensor = $r['sensor'];

            $sql1 = mysqli_query($link, "SELECT created, napiecie_baterii as bat, (SELECT procent FROM `klimat_bat` WHERE napiecie_baterii between vFrom and vTo) as procent, round(avg(temperatura),1) as temp, round(AVG(wilgotnosc),1) as hum, lokacja, lokx, loky FROM `klimat` left join klimat_sensor on klimat.miejsce = klimat_sensor.miejsce WHERE klimat.miejsce = '$sensor' GROUP by created, bat, lokacja, lokx, loky order by created desc limit 1");
            if($res = mysqli_fetch_assoc($sql1)){
                $r['data'] = $res['created'];
                $r['temp'] = $res['temp'];
                $r['hum'] = $res['hum'];
                $r['bat'] = $res['procent'];
                $r['loc'] = $res['lokacja'];
                $r['locx'] = $res['lokx'];
                $r['locy'] = $res['loky'];
            }

            array_push($sensors, $r);

        }
        
        echo json_encode($sensors);
        break;

    case 'updateSensor':

        $id = $_POST['id'];
        $loc = $_POST['loc'];
        $locx = $_POST['locx'];
        $locy = $_POST['locy'];

        if(mysqli_query($link, "update klimat_sensor set lokacja = '$loc', lokx = '$locx', loky = '$locy' where miejsce = '$id'")){
            echo "updejt sucesful";
        }

        break;
    
    default:
        # code...
        break;
}

?>
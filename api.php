<?php

$link = mysqli_connect('localhost', 'so718_klimat', 'eltwin123', 'so718_klimat');
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
mysqli_set_charset($link, "utf8");
header('Content-Type: application/json');

switch ($_GET['type']) {
    case 'getSensors':
        $sql = mysqli_query($link, "select distinct(klimat.miejsce) as sensor from klimat left join klimat_sensor on klimat.miejsce = klimat_sensor.miejsce where klimat_sensor.active = 1 order by sensor asc");
        $sensors = array();
        while ($r = mysqli_fetch_assoc($sql)) {
            

            $sensor = $r['sensor'];

            $sql1 = mysqli_query($link, "SELECT created, napiecie_baterii as bat, (SELECT procent FROM `klimat_bat` WHERE napiecie_baterii between vFrom and vTo) as procent, round(avg(temperatura),1) as temp, round(AVG(wilgotnosc),1) as hum, lokacja, lokx, loky FROM `klimat` left join klimat_sensor on klimat.miejsce = klimat_sensor.miejsce WHERE klimat.miejsce = '$sensor' and klimat_sensor.active = 1 GROUP by created, bat, lokacja, lokx, loky order by created desc limit 1");
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

    case 'getAlerts':

        $sql = mysqli_query($link, "select klimat.created, klimat.temperatura, klimat.wilgotnosc, concat(klimat.miejsce, ' - ', klimat_sensor.lokacja) as sensor from klimat left join klimat_sensor on klimat.miejsce = klimat_sensor.miejsce where alert = '1' order by created desc limit 50");

        $alerts = array();
        
        while($r = mysqli_fetch_assoc($sql)){
            array_push($alerts, $r);
        }

        echo json_encode($alerts);
        
            
        break;


    case 'getDatesRange':

        $id = $_GET['id'];

        $sql = mysqli_query($link, "select min(date(created)) as startDate, max(date(created)) as endDate from klimat where miejsce = '$id'");

        $dates = array();

        while($r = mysqli_fetch_assoc($sql)){
            array_push($dates, $r);
        }

        echo json_encode($dates[0]);

    break;

    case 'getDataOneDay':

        $id = $_GET['id'];
        $date = $_GET['date'];

        $sql = mysqli_query($link, "select time(created) as data, temperatura as temp, wilgotnosc as hum from klimat where miejsce = '$id' and date(created) = '$date' order by created asc");

        $data = array();

        while($r = mysqli_fetch_assoc($sql)){
            array_push($data, $r);
        }

        echo json_encode($data);

    break;

    case 'getDataRange':

        $id = $_GET['id'];
        $start = $_GET['start'];
        $end = $_GET['end'];

        $sql = mysqli_query($link, "select date(created) as data, round(avg(temperatura),2) as temp, round(avg(wilgotnosc),2) as hum from klimat where miejsce = '$id' and date(created) between '$start' and '$end' group by data order by data asc");

        $data = array();

        while($r = mysqli_fetch_assoc($sql)){
            array_push($data, $r);
        }

        echo json_encode($data);

    break;
    
    default:
        # code...
        break;
}

?>
<?php

$host="localhost";
$username="root";
$password="";
$db_name="test";


$con = mysqli_connect($host, $username, $password, $db_name);

if (!$con)
{
    die("Connection failed:" . mysqli_connect_error());
}
?>

<?php
$time = date("Y/m/d", strtotime('last monday'));
echo $time;

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>skp</title>
    <script
            type="text/javascript"
            src="https://www.gstatic.com/charts/loader.js"
    ></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var datamon = google.visualization.arrayToDataTable([
                ['Tid', 'Temperatur °C', {role: 'annotation'}, 'Luftfugtighed %', {role: 'annotation'}],

                <?php

                $sql_tabel = "SELECT * FROM ( SELECT * FROM climateSensor WHERE zone = 5 ORDER BY id DESC LIMIT 3 ) AS r ORDER BY id";
                $resultat = mysqli_query($con, $sql_tabel);

                while ($row = mysqli_fetch_assoc($resultat)){
                $Tid = $row['updated'];
                $Temperature = $row['temperature'];
                $Humidity = $row['humidity'];
                ?>
                ['<?php echo $Tid;?>',<?php echo $Temperature;?>,<?php echo $Temperature;?>,<?php echo $Humidity;?>, <?php echo $Humidity;?>],
                <?php
                }
                ?>
            ]);


            var optionsMon = {
                colors: ['orange','blue'],
                title: 'Man',
                vAxis: {minValue: 0},
                series: {
                    0: {
                        areaOpacity: 0.5,
                        annotations: {
                            stem: {
                                length: -30
                            },
                            textStyle: {
                                auraColor: '#000000',
                                bold: true,
                                fontSize: 20,
                            }
                        },
                    },
                    1: {
                        areaOpacity: 0.1,
                        annotations: {
                            stem: {
                                length: 10
                            },
                            textStyle: {
                                auraColor: '#ffffff',
                                bold: true,
                                fontSize: 20,
                            }
                        },
                    },
                }


            }
            var chartMon = new google.visualization.AreaChart(document.getElementById('chart_Mon'));

            chartMon.draw(datamon, optionsMon);

        }
    </script>
</head>
<body>
<div id="chart_Mon" style="width: 100%; height: 70%"></div>
</body>
</html>

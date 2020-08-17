<?php

$host="localhost";
$username="infotavle.itd-sk";
$password="OVSZY0Jt";
$db_name="infotavle_itd_skp_sde_dk";


$con = mysqli_connect($host, $username, $password, $db_name);

if (!$con)
{
    die("Connection failed:" . mysqli_connect_error());
}

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
                ['Time', 'Temp°C', 'Fugtighed%'],
                ['08.00', 26, 48],
                ['09.30', 27, 49],
                ['11.30', 25, 47],
                ['13.30', 12, 50],
                ['15.30', 24, 46]
            ]);
            var datatue = google.visualization.arrayToDataTable([
                ['Time', 'Temp°C', 'Fugtighed%'],
                ['08.00', 40, 60],
                ['09.30', 27, 49],
                ['11.30', 34, 47],
                ['13.30', 26, 50],
                ['15.30', 24, 46]
            ]);
            var datawed = google.visualization.arrayToDataTable([
                ['Time', 'Temp°C', 'Fugtighed%'],
                ['08.00', 26, 48],
                ['09.30', 27, 49],
                ['11.30', 25, 47],
                ['13.30', 16, 50],
                ['15.30', 24, 46]
            ]);
            var datathu = google.visualization.arrayToDataTable([
                ['Time', 'Temp°C', 'Fugtighed%'],
                ['08.00', 26, 48],
                ['09.30', 30, 49],
                ['11.30', 30, 47],
                ['13.30', 26, 50],
                ['15.30', 24, 46]
            ]);
            var datafri = google.visualization.arrayToDataTable([
                ['Time', 'Temp°C', 'Fugtighed%'],
                ['08.00', 18, 48],
                ['09.30', 20, 49],
                ['11.30', 30, 47],
                ['13.30', 26, 50],
                ['15.30', 24, 46]
            ]);


            //function makeOptions(optionsdag, navn) {
            //var optionsdag = {
            //colors: ['orange','blue'],
            //title: navn,
            //series: {
            //0: { areaOpacity: 0.5},
            //1: { areaOpacity: 0.1}
            //}
            //}
            //}
            //makeOptions(optionsMon, "Man")



            var optionsMon = {
                colors: ['orange','blue'],
                title: 'Man',
                series: {
                    0: { areaOpacity: 0.5},
                    1: { areaOpacity: 0.1}
                }

            }

            var optionsTue = {
                colors: ['orange','blue'],
                title: 'Tir',
                series: {
                    0: { areaOpacity: 0.5},
                    1: { areaOpacity: 0.1}
                }
            };
            var optionsWed = {
                colors: ['orange','blue'],
                title: 'Ons',
                series: {
                    0: { areaOpacity: 0.5},
                    1: { areaOpacity: 0.1}
                }
            };
            var optionsThu = {
                colors: ['orange','blue'],
                title: 'Tor',
                series: {
                    0: { areaOpacity: 0.5},
                    1: { areaOpacity: 0.1}
                }
            };
            var optionsFri = {
                colors: ['orange','blue'],
                title: 'Fri',
                series: {
                    0: { areaOpacity: 0.5},
                    1: { areaOpacity: 0.1}
                }
            };

            var chartMon = new google.visualization.AreaChart(document.getElementById('chart_Mon'));
            var charTue = new google.visualization.AreaChart(document.getElementById('chart_Tue'));
            var chartWed = new google.visualization.AreaChart(document.getElementById('chart_Wed'));
            var chartThu = new google.visualization.AreaChart(document.getElementById('chart_Thu'));
            var chartFri = new google.visualization.AreaChart(document.getElementById('chart_Fri'));
            chartMon.draw(datamon, optionsMon);
            charTue.draw(datatue, optionsTue);
            chartWed.draw(datawed, optionsWed);
            chartThu.draw(datathu, optionsThu);
            chartFri.draw(datafri, optionsFri);
        }
    </script>
</head>
<body>
<div id="chart_Mon" style="width: 100%; height: 20%"></div>
<div id="chart_Tue" style="width: 100%; height: 20%"></div>
<div id="chart_Wed" style="width: 100%; height: 20%"></div>
<div id="chart_Thu" style="width: 100%; height: 20%"></div>
<div id="chart_Fri" style="width: 100%; height: 20%"></div>
<?php

$sql_tabel = "SELECT * FROM climateSensor";
$resultat = mysqli_query($con, $sql_tabel);

while ($row = mysqli_fetch_assoc($resultat)){

    echo $row['zone'];

}
?>
</body>
</html>

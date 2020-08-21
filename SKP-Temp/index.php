<link href="wp/week-picker.css" rel="stylesheet">
<link rel="stylesheet" href="style.css"/>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
      integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1" name="viewport">
<link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="wp/week-picker.js"></script>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>skp</title>

    <div class="container-fluid">
        <div class="dropdown first">
            <button onclick="myFunction()" class="dropbtn">Zoner</button>
            <div id="myDropdown" class="dropdown-content">
                <a href="#contact">Zone5</a>
                <a href="#contact">Zone6</a>
                <a href="#contact">Zone8</a>
                <a href="#contact">Zone9</a>
                <a href="#contact">Zone100</a>
                <a href="#contact">Zone102</a>
            </div>
        </div>
        <div class="week-picker second" data-mode="single"></div>
    </div>

    <div style="text-align: center; margin-top:10px !important;">
        <h1>Zone 5</h1>
    </div>

    <script>
        function myFunction() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        window.onclick = function (event) {
            if (!event.target.matches('.dropbtn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                var i;
                for (i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var datamon = google.visualization.arrayToDataTable([
                ['Tid', 'Temperatur °C', {role: 'annotation'}, 'Luftfugtighed %', {role: 'annotation'}],

                <?php
                $conn = mysqli_connect("localhost", "root", "", "test");
                $result = mysqli_query($conn, "SELECT humidity, zone, temperature, updated FROM climatesensor WHERE zone = 8 and ((updated > '2020-08-12 08:00:00' and updated < '2020-08-12 08:05:00') or (updated > '2020-08-12 12:00:00' and updated < '2020-08-12 12:05:00') or (updated > '2020-08-12 15:00:00' AND updated < '2020-08-12 15:05:00'))");


                while ($row = mysqli_fetch_assoc($result)){
                $zone = $row['zone'];
                $dateTime = $row['updated'];
                $dateTime = date_create($dateTime);
                $Date = date_format($dateTime, "D d-m-Y");
                $Tid = date_format($dateTime, "H:i");
                $Temperature = $row['temperature'];
                $Humidity = $row['humidity'];
                ?>
                ['<?php echo $Tid;?>',<?php echo $Temperature;?>,<?php echo $Temperature;?>,<?php echo $Humidity;?>, <?php echo $Humidity;?>],
                <?php
                }
                ?>
            ]);


            var optionsMon = {
                colors: ['orange', 'blue'],
                title: "<?php echo($Date) ?>",
                titleTextStyle: {fontSize: 24},
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
<div id="chart_Mon" style="width: 100% !important; height: 70% !important;"></div>
</body>
</html>
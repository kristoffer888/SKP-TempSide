<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="#c9ecff" name="theme-color"/>
    <meta content="SDE - Temperatur og luftfugtighedsmåler" name="description">
    <title>SKP - Klima</title>

    <link rel="icon" href="assets/img/sde-logo.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/week-picker.css">
    <link rel="stylesheet" href="assets/css/style.css"/>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="assets/js/week-picker.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="dropdown first">
        <button onclick="pickBuilding(this)" class="dropbtn">MU7</button>
        <div id="btn1" class="dropdown-content">
            <a onclick='pickZone(this)'>Zone 3</a>
            <a onclick='pickZone(this)'>Zone 5</a>
            <a onclick='pickZone(this)'>Zone 6</a>
            <a onclick='pickZone(this)'>Zone 8</a>
            <a onclick='pickZone(this)'>Zone 9</a>
        </div>
    </div>
    <div class="dropdown first">
        <button onclick="pickBuilding(this)" class="dropbtn margin">MU1a</button>
        <div id="btn2" class="dropdown-content margin">
            <a onclick='pickZone(this)'>Zone 7</a>
        </div>
    </div>
    <div class="week-picker second" data-mode="single"></div>
</div>


<div style="text-align: center; padding-bottom:25px !important;">
    <h1 id="title">MU7 Zone 5</h1>
</div>

<div id="chartContainer"></div>

<script>
    var firstDateOfWeek = getMonday(new Date().toISOString().split('T')[0])
    var zoneNumber = 5, weekList = getWeek(firstDateOfWeek)

    $(document).ready(function () {
        $.ajax({
            url: "assets/php/data.php",
            data: {week: weekList, zone: zoneNumber},
            type: "GET",
            dataType: "JSON",

            success: function (data) {
                dateSorter(data)
            }, error: function (error) {
                console.log(error)
            }
        });
    });

    function startCall() {
        console.clear()
        weekList = getWeek(firstDateOfWeek)
        $.ajax({
            url: "assets/php/data.php",
            data: {week: weekList, zone: zoneNumber},
            type: "GET",
            dataType: "JSON",

            success: function (data) {
                dateSorter(data)
            }, error: function (error) {
                console.log(error)
            }
        });
    }

    // Henter mandag fra den nuværende uge
    function getMonday(d) {
        d = new Date(d);
        var day = d.getDay(),
            diff = d.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(d.setDate(diff));
    }

    function dateSorter(dataArray) {
        timeList = [[], [], [], [], []];
        for (let i = 0; i < weekList.length; i++) {
            for (let x = 0; x < dataArray.length; x++) {
                if (new Date(dataArray[x].updated).toISOString().split('T')[0] === weekList[i]) {
                    timeList[i].push(dataArray[x]);
                }
            }
        }

        // Tilføjer data hvis der mangler
        let feed = {humidity: "", temperature: "", updated: " -  Intet data fra denne dag"}
        let feed1 = {humidity: "", temperature: "", updated: ""}
        for (let q = 0; q < timeList.length; q++) {
            if (timeList[q].length === 0) {
                timeList[q].push(feed);
                timeList[q].push(feed);
                timeList[q].push(feed);
            } else if (timeList[q].length === 2) {
                let a = (timeList[q][0].updated.split(" "))[1].split(":")[0]
                let b = (timeList[q][1].updated.split(" "))[1].split(":")[0]
                if (a === "08" && b === "12") {
                    timeList[q].push(feed1);
                } else if (a === "08" && b === "15") {
                    timeList[q].push(feed1);
                } else if (a === "12" && b === "15") {
                    timeList[q].unshift(feed1);
                }
                timeList[q][0].updated = " -  Manglende data fra et eller flere tidspunkter"
            } else if (timeList[q].length === 1) {
                let a = (timeList[q][0].updated.split(" "))[1].split(":")[0]
                if (a === "08") {
                    timeList[q].push(feed1);
                    timeList[q].push(feed1);
                } else if (a === "12") {
                    timeList[q].unshift(feed1)
                    timeList[q].push(feed1);
                } else if (a === "15") {
                    timeList[q].unshift(feed1)
                    timeList[q].unshift(feed1)
                }
                timeList[q][0].updated = " -  Manglende data fra et eller flere tidspunkter"
            }
        }
        // Sletter canvas, padding-div og appender et nyt canvas og padding-div til "chartContainer"
        for (var i = 0; i < 5; i++) {
            $('#chart' + i).remove();
            $('#padding' + i).remove();
            $('#chartContainer').append('<canvas id=' + "chart" + i + ' height="27%" width="100%"></canvas> <div id=' + "padding" + i + ' style="padding-bottom: 50px"></div>');
        }
        // creatChart() genererer alle charts
        createChart(0, "Mandag  -  ")
        createChart(1, "Tirsdag  -  ")
        createChart(2, "Onsdag  -  ")
        createChart(3, "Torsdag  -  ")
        createChart(4, "Fredag  -  ")

        console.log(dataArray)
        console.log(timeList)
    }

    // Skriver i titlen hvis der mangler data
    function makeChartTitle(index) {
        if (timeList[index][0].updated.match("e")) {
            return timeList[index][0].updated
        } return ""
    }

    // Den får datoen for mandagen i den valgte uge og returner en liste med de næste fire dage (mandag-fredag , yyyy-mm-dd)
    function getWeek(monday) {
        weekList = []
        for (let i = 0; i < 5; i++) {
            weekList[i] = moment(moment(monday, "YYYY-MM-DD").add(i, "days")).format("YYYY-MM-DD")
        } return weekList
    }

    //Åbner dropdown menuen
    function pickBuilding(obj) {
        var building = $(obj).text();
        if (building == "MU7") {
            document.getElementById("btn1").classList.toggle("show");
            document.getElementById("btn2").classList.remove("show");
        } else {
            document.getElementById("btn2").classList.toggle("show");
            document.getElementById("btn1").classList.remove("show");
        }
    }

    // Lukker dropdown menuerne hvis man klikker uden for dem
    window.onclick = function (event) {
        if (!event.target.matches('.dropbtn')) {
            let dropdowns = document.getElementsByClassName("dropdown-content");
            for (let i = 0; i < dropdowns.length; i++) {
                let openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    // Henter teksten fra knappen som man trykkede på og ændrer "zoneNumber" og titlen
    function pickZone(obj) {
        zoneNumber = $(obj).text().split(' ');
        zoneNumber = zoneNumber[zoneNumber.length - 1]
        if (zoneNumber == 7) {
            document.getElementById("title").innerHTML = "MU1a Zone " + zoneNumber;
        } else {
            document.getElementById("title").innerHTML = "MU7 Zone " + zoneNumber;
        }
        startCall();
    }

    // createChart() er en function der genererer chart ud fra et template
    function createChart(index, dag) {
        // Tilføjer mellemrum mellem chart og legend.
        Chart.Legend.prototype.afterFit = function () {
            this.height = this.height + 30;
        };
        new Chart(document.getElementById("chart" + index).getContext('2d'), {
            type: 'line',
            // The data for our dataset
            data: {
                labels: ['08:00', '12:00', '15:00'],
                datasets: [{
                    label: 'Temperatur',
                    backgroundColor: 'rgba(239,154,18,0.7)',
                    borderColor: 'rgb(239,154,18)',
                    data: [timeList[index][0].temperature, timeList[index][1].temperature, timeList[index][2].temperature],
                    fill: false,
                }, {
                    label: 'Luftfugtighed',
                    backgroundColor: 'rgba(31,84,208,0.1)',
                    borderColor: 'rgb(31,84,208)',
                    data: [timeList[index][0].humidity, timeList[index][1].humidity, timeList[index][2].humidity],
                    fill: false,
                }]
            },

            // Configuration options go here
            options: {
                plugins: {
                    datalabels: {
                        display: true,
                        offset: 6,
                        font: {
                            size: 20,
                            weight: 'bold',
                        },
                        // Aligner datalabels så de ikke overlapper eller går ud over grafens flade.
                        align: function(context) {
                            var index = context.dataIndex;
                            var datasets = context.chart.data.datasets;
                            var v0 = datasets[0].data[index];
                            var v1 = datasets[1].data[index];
                            var invert = v0 - v1 > 0;

                            if (index == 0){
                                return context.datasetIndex === 0 ?
                                    invert ? -45 : 45 :
                                    invert ? 45 : -45 ;
                            }
                            else if (index == 1){
                                return context.datasetIndex === 0 ?
                                    invert ? 'end' : 'start' :
                                    invert ? 'start' : 'end';
                            }
                            else if (index == 2){
                                return context.datasetIndex === 0 ?
                                    invert ? 240 : -240 :
                                    invert ? -240 : 240 ;
                            }

                        },
                        // Giver labels rigtige farver.
                        color: function (context) {
                            var labelColor = context.dataset.label;
                            if (labelColor === 'Temperatur') {
                                return 'rgb(239,154,18)';
                            } else {
                                return 'rgb(31,84,208)';
                            }
                        },
                        // Tilføjer % og °C til labels.
                        formatter: function(value, context) {
                            var index = context.dataIndex;
                            var datasets = context.chart.data.datasets;
                            var temperatur = datasets[0].data[index];
                            var luftfugtighed = datasets[1].data[index];

                            var labelName = context.dataset.label;

                            if (temperatur == 0 && luftfugtighed == 0){
                                return null;
                            }
                            else if (labelName === 'Temperatur') {
                                return temperatur + ' °C';
                            } else {
                                return luftfugtighed + ' %';
                            }
                        }

                    }
                },
                legend: {
                    position: "top",
                    offset: 10,
                    onHover: function (e) {
                        e.target.style.cursor = 'pointer';
                    },
                    labels: {
                        fontSize: 15,
                    }
                },
                hover: {
                    onHover: function (e) {
                        var point = this.getElementAtEvent(e);
                        if (point.length) e.target.style.cursor = 'pointer';
                        else e.target.style.cursor = 'default';
                    }
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 20,
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 50,
                            fontSize: 15
                        }
                    }]
                },
                responsive: true,
                title: {
                    display: true,
                    text: dag + weekList[index].split('-').reverse().join("-") + " " + makeChartTitle(index),
                    fontSize: 20
                },
            }
        });
    }
</script>
</body>
</html>
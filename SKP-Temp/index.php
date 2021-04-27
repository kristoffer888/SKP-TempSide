<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="assets/img/sde-logo.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="assets/css/week-picker.css">
    <link rel="stylesheet" href="http://www.jqueryscript.net/css/jquerysctipttop.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css"/>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="assets/js/week-picker.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <title>SKP - Klima</title>
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
            <a onclick='pickZone(this)'>Zone 100</a>
            <a onclick='pickZone(this)'>Zone 102</a>
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
    var zoneNumber = 5, weekList = ['2021-04-26', '2021-04-27', '2021-04-28', '2021-04-29', '2021-04-30'],
        timeList = [[], [], [], [], []]
    var firstDateOfWeek = getMonday(new Date().toISOString().split('T')[0])

    $(document).ready(function () {
        $.ajax({
            url: "assets/php/data.php",
            data: {week: weekList, zone: zoneNumber},
            type: "GET",
            dataType: "JSON",

            success: function (data) {
                console.log(data)
                dateSorter(data)
            }, error: function (error) {
                console.log(error)
            }
        });
    });

    function startCall() {
        weekList = getWeek(firstDateOfWeek)
        console.clear()
        console.log(zoneNumber)
        console.log(weekList)
        $.ajax({
            url: "assets/php/data.php",
            data: {week: weekList, zone: zoneNumber},
            type: "GET",
            dataType: "JSON",

            success: function (data) {
                console.log(data)
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
        weekList = getWeek(firstDateOfWeek)
        timeList = [[], [], [], [], []];
        for (var i = 0; i < weekList.length; i++) {
            for (var x = 0; x < dataArray.length; x++) {
                var date = new Date(dataArray[x].updated);
                var year = date.getFullYear();
                var month = ('0' + (date.getMonth() + 1)).slice(-2);
                var day = ('0' + date.getDate()).slice(-2);
                if ((year + "-" + month + "-" + day) === weekList[i] && dataArray[x].zone === zoneNumber.toString()) {
                    timeList[i].push(dataArray[x]);
                }
            }
        }
        // Tilføjer data hvis der mangler
        var feed = {humidity: "", temperature: "", updated: "\xa0-\xa0\xa0Intet\xa0data\xa0fra\xa0denne\xa0dag"}
        var feed1 = {humidity: "0", temperature: "0", updated: ""}
        var a;
        var b;
        for (var q = 0; q < timeList.length; q++) {
            if (timeList[q].length === 0) {
                timeList[q].push(feed);
                timeList[q].push(feed);
                timeList[q].push(feed);
            } else if (timeList[q].length === 2) {
                a = (timeList[q][0].updated.split(" "))[1].split(":")[0]
                b = (timeList[q][1].updated.split(" "))[1].split(":")[0]
                if (a === "08" && b === "12") {
                    timeList[q].push(feed1);
                } else if (a === "08" && b === "15") {
                    timeList[q].push(feed1);
                } else if (a === "12" && b === "15") {
                    timeList[q].unshift(feed1);
                }
                timeList[q][0].updated += " \xa0-\xa0\xa0Manglende\xa0data\xa0fra\xa0et\xa0eller\xa0flere\xa0tidspunkter"
            } else if (timeList[q].length === 1) {
                a = (timeList[q][0].updated.split(" "))[1].split(":")[0]
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
                timeList[q][0].updated += " \xa0-\xa0\xa0Manglende\xa0data\xa0fra\xa0et\xa0eller\xa0flere\xa0tidspunkter"
            } else {
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

    }

    // Skriver en fejlbesked hvis der mangler data
    function makeChartTitle(index) {
        var a = timeList[index][0].updated

        if (timeList[index][0].updated.split(' ')[1] === " -  Manglende data fra et eller flere tidspunkter") {
            a = timeList[index][0].updated.split(' ')[1]
        } else if (timeList[index][0].updated.split(' ')[2] === " -  Manglende data fra et eller flere tidspunkter") {
            a = timeList[index][0].updated.split(' ')[2]
        } else if (timeList[index][0].updated.split(' ').length === 2) {
            a = " "
        }

        return a
    }

    // Den får datoen for mandagen i den valgte uge og returner en liste med de næste fire dage (mandag-fredag , yyyy-mm-dd)
    function getWeek(monday) {
        weekList = []
        for (let i = 0; i < 5; i++) {
            weekList[i] = moment(moment(monday, "YYYY-MM-DD").add(i, "days")).format("YYYY-MM-DD")
        }
        return weekList
    }

    //Åbner dropdown menuen
    function pickBuilding(obj) {
        const building = $(obj).text();
        if (building === "MU7") {
            document.getElementById("btn1").classList.toggle("show");
            document.getElementById("btn2").classList.remove("show");

        } else {
            document.getElementById("btn2").classList.toggle("show");
            document.getElementById("btn1").classList.remove("show");

        }
    }

    // Lukker dropdown menuen hvis man klikker uden for den
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
    function createChart(indeks, dag) {
        new Chart(document.getElementById("chart" + indeks).getContext('2d'), {
            type: 'line',

            // The data for our dataset
            data: {
                labels: ['08:00', '12:00', '15:00'],
                datasets: [{
                    label: 'Temperatur',
                    backgroundColor: 'rgba(239,154,18,0.7)',
                    borderColor: 'rgb(239,154,18)',
                    data: [timeList[indeks][0].temperature, timeList[indeks][1].temperature, timeList[indeks][2].temperature],
                    fill: true,
                }, {
                    label: 'Luftfugtighed',
                    backgroundColor: 'rgba(31,84,208,0.1)',
                    borderColor: 'rgb(31,84,208)',
                    data: [timeList[indeks][0].humidity, timeList[indeks][1].humidity, timeList[indeks][2].humidity],
                    fill: true,
                }]
            },

            // Configuration options go here
            options: {
                plugins: {
                    datalabels: {
                        display: true,
                        anchor: 'center',
                        align: 'top',
                        offset: 10,
                        color: function (context) {
                            var valueOrange = context.dataset.label;
                            if (valueOrange === 'Temperatur') {
                                return 'rgb(239,154,18)';
                            } else {
                                return 'rgb(31,84,208)';
                            }
                        },
                        font: {
                            size: 20,
                            weight: 'bold'
                        },
                    }
                },
                legend: {
                    onHover: function (e) {
                        e.target.style.cursor = 'pointer';
                    },
                    labels: {
                        fontSize: 15
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
                            fontSize: 15
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 90,
                            fontSize: 15
                        }
                    }]
                },
                responsive: true,
                title: {
                    display: true,
                    text: dag + weekList[indeks].split('-').reverse().join("-") + " " + makeChartTitle(indeks),
                    fontSize: 20
                },
            }
        });
    }
</script>
</body>
</html>
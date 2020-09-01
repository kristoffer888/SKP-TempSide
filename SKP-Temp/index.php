<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="assets/css/week-picker.css">
    <link rel="stylesheet" href="assets/css/style.css"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="http://www.jqueryscript.net/css/jquerysctipttop.css" SameSite=None; Secure rel="stylesheet"
          type="text/css">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="assets/js/week-picker.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <meta charset="UTF-8">
    <title>SKP Temperatur</title>

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
    <script>

        var zoneNumber = "5";

        function pickZone5() {
            zoneNumber = "5";
            document.getElementById("title").innerHTML = "Zone 5";
            listCall();
        }

        function pickZone6() {
            zoneNumber = "6";
            document.getElementById("title").innerHTML = "Zone 6";
            listCall();
        }

        function pickZone8() {
            zoneNumber = "8";
            document.getElementById("title").innerHTML = "Zone 8";
            listCall();
        }

        function pickZone9() {
            zoneNumber = "9";
            document.getElementById("title").innerHTML = "Zone 9";
            listCall();
        }

        function pickZone100() {
            zoneNumber = "100";
            document.getElementById("title").innerHTML = "Zone 100";
            listCall();
        }

        function pickZone102() {
            zoneNumber = "102";
            document.getElementById("title").innerHTML = "Zone 102";
            listCall();
        }

    </script>

    <div class="container-fluid">
        <div class="dropdown first">
            <button onclick="myFunction()" class="dropbtn">Zoner</button>
            <div id="myDropdown" class="dropdown-content">
                <a href="javascript:pickZone5()">Zone 5</a>
                <a href="javascript:pickZone6()">Zone 6</a>
                <a href="javascript:pickZone8()">Zone 8</a>
                <a href="javascript:pickZone9()">Zone 9</a>
                <a href="javascript:pickZone100()">Zone 100</a>
                <a href="javascript:pickZone102()">Zone 102</a>
            </div>
        </div>
        <div class="week-picker second" data-mode="single"></div>
    </div>

    <div style="text-align: center; padding-bottom:25px !important;">
        <h1 id="title">Zone 5</h1>
    </div>

    <script>

        $(document).ready(function () {
            var jsondata = $.ajax({
                url: "assets/php/data.php",
                dataType: "JSON",

                success: function (data) {
                    appendList(data)
                }, error: function (error) {
                    console.log(error)
                }
            });
        });

        function listCall() {
            $(document).ready(function () {
                var jsondata = $.ajax({
                    url: "assets/php/data.php",
                    dataType: "JSON",

                    success: function (data) {
                        appendList(data)
                    }, error: function (error) {
                        console.log(error)
                    }
                });
            });
        }

        var weekList = [];
        var firstDateOfWeek = getMonday(new Date());
        var year2 = firstDateOfWeek.getFullYear();
        var month2 = ('0' + (firstDateOfWeek.getMonth() + 1)).slice(-2);
        var day2 = ('0' + firstDateOfWeek.getDate()).slice(-2);
        firstDateOfWeek = year2 + '-' + month2 + '-' + day2;
        var dateSplit = firstDateOfWeek.split('-');
        var listList = [[], [], [], [], []];


        function getMonday(d) {
            d = new Date(d);
            var day = d.getDay(),
                diff = d.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is sunday
            return new Date(d.setDate(diff));
        }

        function getWeek() {
            weekList = []
            var firstDateOfWeekPreSplit = firstDateOfWeek
            dateSplit = firstDateOfWeek.split('-');
            firstDateOfWeek = new Date(dateSplit[0] + ',' + dateSplit[1] + ',' + dateSplit[2]);

            for (i = 0; i < 5; i++) {
                var nextDate = new Date(firstDateOfWeek);
                nextDate.setDate(firstDateOfWeek.getDate() + i);

                var year1 = nextDate.getFullYear();
                var month1 = ('0' + (nextDate.getMonth() + 1)).slice(-2);
                var day1 = ('0' + nextDate.getDate()).slice(-2);

                weekList.push(year1 + '-' + month1 + '-' + day1);
            }
            firstDateOfWeek = firstDateOfWeekPreSplit

        }

        function appendList(dataArray) {
            //if (firstDateOfWeek == "2020-08-10") {
            getWeek()
            //}

            listList = [[], [], [], [], []];
            for (var i = 0; i < weekList.length; i++) {
                for (var x = 0; x < dataArray.length; x++) {
                    var date = new Date(dataArray[x].updated);
                    var year = date.getFullYear();
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var day = ('0' + date.getDate()).slice(-2);
                    if ((year + "-" + month + "-" + day) == weekList[i] && dataArray[x].zone == zoneNumber) {
                        listList[i].push(dataArray[x]);
                    }
                }
            }
            for (var q = 0; q < listList.length; q++) {
                if (listList[q].length == 0) {
                    var feed = {humidity: "0", temperature: "0", updated: "\xa0Missing\xa0Data"}
                    listList[q].push(feed);
                    listList[q].push(feed);
                    listList[q].push(feed);
                } else if (listList[q].length == 2) {
                    listList[q].push(listList[q][1]);
                } else if (listList[q].length == 1) {
                    listList[q].push(listList[q][0]);
                    listList[q].push(listList[q][0]);
                } else {
                    console.log("LMAO")
                }
            }
            drawChart()
        }


    </script>
    <script>
        function drawChart() {
            $('#chart0').remove();
            $('#chart1').remove();
            $('#chart2').remove();
            $('#chart3').remove();
            $('#chart4').remove();
            $('#padding0').remove();
            $('#padding1').remove();
            $('#padding2').remove();
            $('#padding3').remove();
            $('#padding4').remove();
            $('#chartContainer').append('<canvas id="chart0" height="27%" width="100%"></canvas> <div id="padding0" style="padding-bottom: 50px"></div>');
            $('#chartContainer').append('<canvas id="chart1" height="27%" width="100%"></canvas> <div id="padding1" style="padding-bottom: 50px"></div>');
            $('#chartContainer').append('<canvas id="chart2" height="27%" width="100%"></canvas> <div id="padding2" style="padding-bottom: 50px"></div>');
            $('#chartContainer').append('<canvas id="chart3" height="27%" width="100%"></canvas> <div id="padding3" style="padding-bottom: 50px"></div>');
            $('#chartContainer').append('<canvas id="chart4" height="27%" width="100%"></canvas> <div id="padding4" style="padding-bottom: 50px"></div>');

            var ctx = document.getElementById('chart0').getContext('2d');
            var chart0 = new Chart(ctx, {
                type: 'line',

                // The data for our dataset
                data: {
                    labels: ['08:00', '12:00', '15:00'],
                    datasets: [{
                        label: 'Temperatur',
                        backgroundColor: 'rgba(239,154,18,0.7)',
                        borderColor: 'rgb(239,154,18)',
                        data: [listList[0][0].temperature, listList[0][1].temperature, listList[0][2].temperature],
                        fill: true,
                    }, {
                        label: 'Luftfugtighed',
                        backgroundColor: 'rgba(31,84,208,0.1)',
                        borderColor: 'rgb(31,84,208)',
                        data: [listList[0][0].humidity, listList[0][1].humidity, listList[0][2].humidity],
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
                                var index = context.dataIndex;
                                var value = context.dataset.data[index];
                                var valueOrange = context.dataset.label;

                                if (valueOrange === 'Temperatur') {
                                    return value = 'rgb(239,154,18)';
                                } else {
                                    return value = 'rgb(31,84,208)';
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
                                suggestedMax: 70,
                                fontSize: 15
                            }
                        }]
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: "Mandag  -  " + listList[0][0].updated.split(' ')[0],
                        fontSize: 20
                    },
                }
            });
            var ctx = document.getElementById('chart1').getContext('2d');
            var chart1 = new Chart(ctx, {
                type: 'line',

                // The data for our dataset
                data: {
                    labels: ['08:00', '12:00', '15:00'],
                    datasets: [{
                        label: 'Temperatur',
                        backgroundColor: 'rgba(239,154,18,0.7)',
                        borderColor: 'rgb(239,154,18)',
                        data: [listList[1][0].temperature, listList[1][1].temperature, listList[1][2].temperature],
                        fill: true,
                    }, {
                        label: 'Luftfugtighed',
                        backgroundColor: 'rgba(31,84,208,0.1)',
                        borderColor: 'rgb(31,84,208)',
                        data: [listList[1][0].humidity, listList[1][1].humidity, listList[1][2].humidity],
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
                            color: function (context) {
                                var index = context.dataIndex;
                                var value = context.dataset.data[index];
                                var valueOrange = context.dataset.label;

                                if (valueOrange === 'Temperatur') {
                                    return value = 'rgb(239,154,18)';
                                } else {
                                    return value = 'rgb(31,84,208)';
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
                                suggestedMax: 70,
                                fontSize: 15
                            }
                        }]
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: "Tirsdag  -  " + listList[1][0].updated.split(' ')[0],
                        fontSize: 20
                    },
                }
            });
            var ctx = document.getElementById('chart2').getContext('2d');
            var chart2 = new Chart(ctx, {
                type: 'line',

                // The data for our dataset
                data: {
                    labels: ['08:00', '12:00', '15:00'],
                    datasets: [{
                        label: 'Temperatur',
                        backgroundColor: 'rgba(239,154,18,0.7)',
                        borderColor: 'rgb(239,154,18)',
                        data: [listList[2][0].temperature, listList[2][1].temperature, listList[2][2].temperature],
                        fill: true,
                    }, {
                        label: 'Luftfugtighed',
                        backgroundColor: 'rgba(31,84,208,0.1)',
                        borderColor: 'rgb(31,84,208)',
                        data: [listList[2][0].humidity, listList[2][1].humidity, listList[2][2].humidity],
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
                            color: function (context) {
                                var index = context.dataIndex;
                                var value = context.dataset.data[index];
                                var valueOrange = context.dataset.label;

                                if (valueOrange === 'Temperatur') {
                                    return value = 'rgb(239,154,18)';
                                } else {
                                    return value = 'rgb(31,84,208)';
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
                                suggestedMax: 70,
                                fontSize: 15
                            }
                        }]
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: "Onsdag  -  " + listList[2][0].updated.split(' ')[0],
                        fontSize: 20
                    },
                }
            });
            var ctx = document.getElementById('chart3').getContext('2d');
            var chart3 = new Chart(ctx, {
                type: 'line',

                // The data for our dataset
                data: {
                    labels: ['08:00', '12:00', '15:00'],
                    datasets: [{
                        label: 'Temperatur',
                        backgroundColor: 'rgba(239,154,18,0.7)',
                        borderColor: 'rgb(239,154,18)',
                        data: [listList[3][0].temperature, listList[3][1].temperature, listList[3][2].temperature],
                        fill: true,
                    }, {
                        label: 'Luftfugtighed',
                        backgroundColor: 'rgba(31,84,208,0.1)',
                        borderColor: 'rgb(31,84,208)',
                        data: [listList[3][0].humidity, listList[3][1].humidity, listList[3][2].humidity],
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
                            color: function (context) {
                                var index = context.dataIndex;
                                var value = context.dataset.data[index];
                                var valueOrange = context.dataset.label;

                                if (valueOrange === 'Temperatur') {
                                    return value = 'rgb(239,154,18)';
                                } else {
                                    return value = 'rgb(31,84,208)';
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
                                suggestedMax: 70,
                                fontSize: 15
                            }
                        }]
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: "Torsdag  -  " + listList[3][0].updated.split(' ')[0],
                        fontSize: 20
                    },
                }
            });
            var ctx = document.getElementById('chart4').getContext('2d');
            var chart4 = new Chart(ctx, {
                type: 'line',

                // The data for our dataset
                data: {
                    labels: ['08:00', '12:00', '15:00'],
                    datasets: [{
                        label: 'Temperatur',
                        backgroundColor: 'rgba(239,154,18,0.7)',
                        borderColor: 'rgb(239,154,18)',
                        data: [listList[4][0].temperature, listList[4][1].temperature, listList[4][2].temperature],
                        fill: true,
                    }, {
                        label: 'Luftfugtighed',
                        backgroundColor: 'rgba(31,84,208,0.1)',
                        borderColor: 'rgb(31,84,208)',
                        data: [listList[4][0].humidity, listList[4][1].humidity, listList[4][2].humidity],
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
                            color: function (context) {
                                var index = context.dataIndex;
                                var value = context.dataset.data[index];
                                var valueOrange = context.dataset.label;

                                if (valueOrange === 'Temperatur') {
                                    return value = 'rgb(239,154,18)';
                                } else {
                                    return value = 'rgb(31,84,208)';
                                }
                            },
                            borderColor: 'green',
                            font: {
                                size: 20,
                                weight: 'bold',
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
                                suggestedMax: 70,
                                fontSize: 15
                            }
                        }]
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: "Fredag  -  " + listList[4][0].updated.split(' ')[0],
                        fontSize: 20
                    },
                }
            });
        }
    </script>

</head>
<body>
<div id="chartContainer">
</div>
</body>
</html>
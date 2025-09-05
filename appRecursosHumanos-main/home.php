<?php
session_start();
// echo "Bienvenido ".$_SESSION['usuario'];

if(isset($_SESSION['usuario'])== null){
    Header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/dashboards/dashboards.js"></script>
    <script src="https://code.highcharts.com/dashboards/modules/layout.js"></script>
    <title>Panel Contentido</title>
    
     <style>
@import url("https://code.highcharts.com/css/highcharts.css");
@import url("https://code.highcharts.com/dashboards/css/datagrid.css");
@import url("https://code.highcharts.com/dashboards/css/dashboards.css");

* {
    font-family:
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Roboto,
        Helvetica,
        Arial,
        "Apple Color Emoji",
        "Segoe UI Emoji",
        "Segoe UI Symbol",
        sans-serif;
}

h2,
h3 {
    text-align: center;
}

#csv {
    display: none;
}

/* LARGE */
@media (max-width: 1200px) {
    #top-left,
    #top-right {
        flex: 1 1 50%;
    }
}

/* MEDIUM */
@media (max-width: 992px) {
    #top-left,
    #top-right {
        flex: 1 1 50%;
    }
}

/* SMALL */
@media (max-width: 576px) {
    #top-left,
    #top-right {
        flex: 1 1 100%;
    }
}

.highcharts-description {
    margin: 0.3rem 10px;
}


@media (prefers-color-scheme: dark) {
    body {
        background-color: #141414;
        color: #ffffff;
    }
}
     </style>
</head>
<body>
<?php
include_once('./nav.php');
?>
<div id="container"></div>
<pre id="csv">
    Vegetable,Amount
Broccoli,44
Carrots,51
Corn,38
Cucumbers,45
Onions,57
Potatos,62
Spinach,35
Tomatos,61
</pre>
</body>
</html>
<script src="graf.js"></script>

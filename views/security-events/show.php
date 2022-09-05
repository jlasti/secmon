<?php
/**
 * Created by PhpStorm.
 * User: mkovac
 * Date: 25.2.2019
 * Time: 20:24
 */
use \yii\helpers\Html;
?>
<head>
    <!-- Resources -->
    <script src="https://www.amcharts.com/lib/4/core.js"></script>
    <script src="https://www.amcharts.com/lib/4/maps.js"></script>
    <script src="https://www.amcharts.com/lib/4/geodata/worldLow.js"></script>
    <script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
</head>

<body>
<!-- HTML -->
<div class="main-actions centered-horizontal">
    <a class="btn-floating waves-effect waves-light btn-large blue" id="allHeat" title="All analyzed events in heat map"><i class='material-icons'>select_all</i>></a>
    <a class="btn-floating waves-effect waves-light btn-large blue" id="allPoints" title="All analyzed events in point map"><i class='material-icons'>control_point</i>></a>
    <a class="btn-floating waves-effect waves-light btn-large blue" id="clear" title="Reset"><i class='material-icons'>clear</i>></a>
</div>
<div id="chartdiv"></div>
</body>

<!-- Chart code -->
<script>
    $(document).ready(function() {

        var id = getQueryVariable("id");

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create map instance
        var chart = am4core.create("chartdiv", am4maps.MapChart);

        // Set map definition
        chart.geodata = am4geodata_worldLow;

        // Set projection
        chart.projection = new am4maps.projections.Mercator();

        // Add zoom control
        chart.zoomControl = new am4maps.ZoomControl();

        // Set initial zoom
        chart.homeZoomLevel = 3;
        chart.homeGeoPoint = {latitude: 51, longitude: -23};

        // Create map polygon series
        var polygonSeries = chart.series.push(new am4maps.MapPolygonSeries());
        polygonSeries.exclude = ["AQ"];
        polygonSeries.useGeodata = true;

        var obj = [];
        var i = 0;

        ajaxHeat = function (url) {
            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function (data) {
                    obj = JSON.parse(data);
                    for (i = 0; i < Object.keys(obj).length; i++)
                        if (polygonSeries.data[obj[i].id].value > 0)
                            polygonSeries.data[obj[i].id].value += obj[i].value;
                        else
                            polygonSeries.data[obj[i].id].value = obj[i].value;
                    polygonSeries.validateData();
                },
                error: function(data){
                    console.log("ERROR PATH" + data);
                }
            });
        };

        ajaxPoint = function (url) {
            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function (data) {
                    obj = JSON.parse(data);
                    for (i = 0; i < Object.keys(obj).length; i++) {
                        imageSeries.data.push(obj[i]);
                        lineSeries.data.push(obj[i].multiGeoLine);
                    }
                    imageSeries.validateData();
                    lineSeries.validateData();
                },
                error: function(data){
                    console.log("ERROR PATH" + data);
                }
            });
        };

        // test data
        polygonSeries.data.push({
            "id": "FR",
            "name": "France",
        });

        polygonSeries.heatRules.push({
            "property": "fill",
            "target": polygonSeries.mapPolygons.template,
            "min": am4core.color("#b2b8ff"),
            "max": am4core.color("#0076b9")
        });

        var polygonTemplate = polygonSeries.mapPolygons.template;
        polygonTemplate.tooltipText = "{name}: {value}";

        var heatLegend = chart.createChild(am4maps.HeatLegend);
        heatLegend.series = polygonSeries;
        heatLegend.width = am4core.percent(99.5);

        $("#allHeat").click(function () {
            ajaxHeat('all?id='+id);
        });

        $("#allPoints").click(function () {
            ajaxPoint('point?id='+id);
        });

        $("#clear").click(function () {
            polygonSeries.data = [];
            imageSeries.data = [];
            lineSeries.data = [];
            polygonSeries.data.push({
                "id": "FR",
                "name": "France",
            });
            lineSeries.validateData();
            imageSeries.validateData();
            polygonSeries.validateData();


        });

        // Add images
        var imageSeries = chart.series.push(new am4maps.MapImageSeries());
        var imageTemplate = imageSeries.mapImages.template;
        imageTemplate.tooltipText = "{title}: {value}";
        imageTemplate.propertyFields.latitude = "latitude";
        imageTemplate.propertyFields.longitude = "longitude";

        var circle = imageTemplate.createChild(am4core.Circle);
        circle.fillOpacity = 0.7;
        circle.propertyFields.fill = "color";
        circle.tooltipText = "{title}: [bold]{value}[/]";

        imageSeries.heatRules.push({
            "target": circle,
            "property": "radius",
            "min": 1.5,
            "max": 5.5,
            "dataField": "value"
        });

        // Add lines
        var lineSeries = chart.series.push(new am4maps.MapLineSeries());
        lineSeries.dataFields.multiGeoLine = "multiGeoLine";
        var lineTemplate = lineSeries.mapLines.template;
        lineTemplate.nonScalingStroke = true;
        lineTemplate.arrow.nonScaling = true;
        lineTemplate.arrow.width = 5;
        lineTemplate.arrow.height = 7;
        lineTemplate.line.strokeOpacity = 0.5;

        // ability to refresh grid with actual data every x time
        /*setInterval( function() {
        }, 5000 );*/
    });
</script>
<script>
    function getQueryVariable(variable)
    {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            if(pair[0] === variable){
                return pair[1];
            }
        }
        return(false);
    }
</script>
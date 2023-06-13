<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 10/22/2019
 * Time: 10:13 AM
 */

?>

<!--CONTENT START------------------------------------------------------------------------------------------------------>
<!--CHART https://canvasjs.com/-->
<script src="<?=base_url()?>/plugin/canvasjs/js/canvasjs.min.js"></script>

<!--DATE PICKER https://bootstrap-datepicker.readthedocs.io/en/latest/-->
<script src="<?=base_url()?>/plugin/bootstrap-datepicker-1.9.0-dist/js/bootstrap-datepicker.min.js"></script>
<!--SCRIPT------------------------------------------------------------------------------------------------------------->

<form id="_form" action="<?=base_url()?>/statistic" method="get">
    <input type="hidden" id="_date" name="_date" value="<?=$dtCurrent?>">
</form>

<!--Date picker-->
<div class="row"><div class="col-md-6">
        <div class="col-md-12"><H3>Choose date</H3></div>
        <div class="input-group date">
            <input type="text" class="form-control"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
        </div>
</div></div>

<!--Active Time-->
<br/><br/>
<div class="row">
    <div class="col-md-6">
        <div id="chartActiveTimeDaily" style="height: 300px; width: 100%;"></div>
    </div>
    <div class="col-md-6">
        <div id="chartActiveTimeMonthly" style="height: 300px; width: 100%;"></div>
    </div>
</div>

<!--Yearly-->
<br/><br/>
<div class="row">
    <div class="col-md-12">
        <div id="chartActiveTimeYearly" style="height: 300px; width: 100%;"></div>
    </div>
</div>

<!-- Fav channel -->
<br/><br/>
<div id="chartMostWatched" style="height: 300px; width: 100%;"></div>

<script>
    var chart;

    console.log(window.location.href);

    $('.input-group.date').datepicker({
        format: "d MM yyyy",
        autoclose: true,
        todayHighlight: true
    });

    $('.input-group.date').datepicker()
        .on('changeDate', function(e) {
            console.log('changeDate');
            console.log(e.date);
            console.log(_date.value);

            var dateEntry = e.date.getFullYear()+'-'+(e.date.getMonth()+1)+'-'+e.date.getDate();

            _date.value = dateEntry;
            _form.submit();

        });

    $('.input-group.date').datepicker('update', '<?=$date?>');

    chart = new CanvasJS.Chart("chartActiveTimeDaily", {
        backgroundColor: null,
        title:{
            text: "Active time <?=$dayOfMonth?> <?=$monthName?>"
        },
        axisX:{
            title: "Time",
            labelFontColor: "#909090",
            lineColor: "#A0A0A0",
            tickColor: "#A0A0A0",
            tickLength: 5,
            tickThickness: 1,
            gridColor: "#D0D0D0" ,
            gridThickness: 0,
            gridDashType: "solid",
            interval: 1
        },
        axisY:{
            title: "Subscriber",
            labelFontColor: "#909090",
            lineColor: "#A0A0A0",
            tickColor: "#A0A0A0" ,
            tickLength: 5,
            tickThickness: 1,
            gridColor: "#D0D0D0" ,
            gridThickness:1,
            gridDashType: "solid"
        },
        data: [
            {
                // Change type to "doughnut", "line", "splineArea", etc.
                type: "column",
                color: 'rgba(255, 99, 132, 1)',
                dataPoints: <?=$dataActiveTimeDaily?>
            }
        ]
    });
    chart.render();

    chart = new CanvasJS.Chart("chartActiveTimeMonthly", {
        backgroundColor: null,
        title:{
            text: "Monthly <?=$monthName?>"
        },
        axisX:{
            title: "Day",
            labelFontColor: "#909090",
            lineColor: "#A0A0A0",
            tickColor: "#A0A0A0",
            tickLength: 5,
            tickThickness: 1,
            gridColor: "#D0D0D0" ,
            gridThickness: 0,
            gridDashType: "solid",
            interval: 1
        },
        axisY:{
            title: "Subscriber",
            labelFontColor: "#909090",
            lineColor: "#A0A0A0",
            tickColor: "#A0A0A0" ,
            tickLength: 5,
            tickThickness: 1,
            gridColor: "#D0D0D0" ,
            gridThickness:1,
            gridDashType: "solid"
        },
        data: [
            {
                // Change type to "doughnut", "line", "splineArea", etc.
                type: "column",
                color: 'rgba(255, 99, 132, 1)',
                dataPoints: <?=$dataActiveTimeMonthly?>
            }
        ]
    });
    chart.render();

    chart = new CanvasJS.Chart("chartActiveTimeYearly", {
        backgroundColor: null,
        title:{
            text: "Yearly <?=$year?>"
        },
        axisX:{
            title: "Month",
            labelFontColor: "#909090",
            lineColor: "#A0A0A0",
            tickColor: "#A0A0A0",
            tickLength: 5,
            tickThickness: 1,
            gridColor: "#D0D0D0" ,
            gridThickness: 0,
            gridDashType: "solid",
            interval: 1
        },
        axisY:{
            title: "Subscriber",
            labelFontColor: "#909090",
            lineColor: "#A0A0A0",
            tickColor: "#A0A0A0" ,
            tickLength: 5,
            tickThickness: 1,
            gridColor: "#D0D0D0" ,
            gridThickness:1,
            gridDashType: "solid"
        },
        data: [
            {
                // Change type to "doughnut", "line", "splineArea", etc.
                type: "column",
                color: 'rgba(255, 99, 132, 1)',
                dataPoints: <?=$dataActiveTimeYearly?>
            }
        ]
    });
    chart.render();

    chart = new CanvasJS.Chart("chartMostWatched", {
        backgroundColor: null,
        title:{
            text: "Favorite Channel <?=$year?>"
        },
        axisX:{
            title: "Channel",
            labelFontColor: "#909090",
            lineColor: "#A0A0A0",
            tickColor: "#A0A0A0",
            tickLength: 5,
            tickThickness: 1,
            gridColor: "#D0D0D0" ,
            gridThickness: 0,
            gridDashType: "solid",
            interval: 1
        },
        axisY:{
            title: "Duration (hour)",
            labelFontColor: "#909090",
            lineColor: "#A0A0A0",
            tickColor: "#A0A0A0" ,
            tickLength: 5,
            tickThickness: 1,
            gridColor: "#D0D0D0" ,
            gridThickness:1,
            gridDashType: "solid"
        },
        data: [
            {
                // Change type to "doughnut", "line", "splineArea", etc.
                type: "column",
                color: 'rgba(255, 99, 132, 1)',
                dataPoints: <?=$dataMostWatched?>
            }
        ]
    });
    chart.render();
</script>
<!--CONTENT END-------------------------------------------------------------------------------------------------------->

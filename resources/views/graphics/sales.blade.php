@extends('layouts.app')

@section('content')
<div id="sales-chart" style="width: 100%; height: 400px;"></div>

<!-- Highchats -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>


<script>
    Highcharts.chart('sales-chart', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Sales per day report - All Time'
        },
        subtitle: {
            text: 'Sales chart subtitle, can countain a link to sales'
        },
        accessibility: {
            announceNewData: {
                enabled: true
            }
        },
        xAxis: {
            type: 'category',
            title: {
                text: 'Dates'
            }
        },
        yAxis: {
            title: {
                text: 'Total sale amount'
            }

        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '$ {point.y:.1f}'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +
                '<b>$ {point.y:.2f}</b> of total<br/>'
        },

        series: [{
            name: 'Total Sales',
            colorByPoint: true,
            data: <?= $data ?>
        }, ],
    });
</script>
@endsection
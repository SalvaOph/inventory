@extends('layouts.app')

@section('content')
<div id="inventories-chart" style="width: 100%; height: 500px;"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>

<script>
Highcharts.chart('inventories-chart', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Total Inventory by Product'
    },
    subtitle: {
        text: 'Click the columns to view inventory by warehouse.'
    },

    accessibility: {
        announceNewData: {
            enabled: true
        },
        point: {
            valueSuffix: '%'
        }
    },

    plotOptions: {
        series: {
            borderRadius: 5,
            dataLabels: [{
                enabled: true,
                distance: 15,
                format: '{point.name}'
            }, {
                enabled: true,
                distance: '-30%',
                filter: {
                    property: 'percentage',
                    operator: '>',
                    value: 5
                },
                format: '{point.y:.0f}',
                style: {
                    fontSize: '0.9em',
                    textOutline: 'none'
                }
            }]
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +
            '<b>{point.y:.0f}</b> of total<br/>'
    },

    series: [
        {
            name: 'Inventory',
            colorByPoint: true,
            data: <?= $data ?>
        },
    ],
    drilldown: {
      series: []  
    }
});

Highcharts.addEvent(Highcharts.Series, 'click', function (e) {
    var chart = this.chart,
        drilldownSeries = chart.options.drilldown.series;

    if (!drilldownSeries.length) {
        $.ajax({
            url: '/getInventoryDrilldownData/' + e.point.options.id,
            method: 'GET',
            success: function (data) {
                var drilldownData = {
                    name: e.point.name,
                    id: e.point.options.id,
                    data: data.map(function (item) {
                        return [item.name, item.y];
                    })
                };

                chart.addSeriesAsDrilldown(e.point, drilldownData);
            }
        });
    }
});
</script>
@endsection
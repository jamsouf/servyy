/**
 * Make things on page load
 */
$(function () {
    run();
});

/**
 * Start the app
 */
function run() {
    initScrollbars();
    chartCpuLoadAverage();
    chartCpuLoadUsing();
    chartMemUsage();
    chartSwapUsage();
    chartDiskUsage();
    chartTasksCount();
}

/**
 * Create good looking scrollbars
 */
function initScrollbars() {
    $(".box .wrap").mCustomScrollbar({
        scrollInertia: 400
    });
}

function cpuUsingLabel(key) {
    var label;
    switch (key) {
        case 'us': label = 'user processes'; break;
        case 'sy': label = 'system processes'; break;
        case 'ni': label = 'processes priority upgraded'; break;
        case 'id': label = 'not used (idle)'; break;
        case 'wa': label = 'waiting for I/O operations'; break;
        case 'hi': label = 'serving hardware interrupts'; break;
        case 'si': label = 'serving software interrupts'; break;
        case 'st': label = 'stolen by the hypervisor'; break;
        default: label = key;
    }
    return label;
}

/**
 * Chart for the cpu load average
 */
function chartCpuLoadAverage() {
    $('#cpu-load-average-chart').highcharts({
        chart: {
            backgroundColor: 'none',
            spacing: [0,10,0,10]
        },
        title: {
            text: null,
        },
        plotOptions: {
            series: {
                showInLegend: false
            }
        },
        xAxis: {
            lineColor: 'transparent',
            minorTickLength: 0,
            tickLength: 0,
            labels: {
                enabled: false
            },
        },
        yAxis: {
            title: {
                text: null
            },
            gridLineWidth: 0,
            minorGridLineWidth: 0,
            labels: {
                enabled: false
            },
        },
        tooltip: {
            formatter: function() {
                return 'load avg<br><b>' + this.point.y + '</b>';
            }
        },
        series: [{
            data: [4.06, 3.63, 3.46]
        }],
        credits: {
            enabled: false
        }
    });
}

/**
 * Chart for the cpu usage
 */
function chartCpuLoadUsing() {
    $('#cpu-load-using-chart').highcharts({
        chart: {
            type: 'column',
            backgroundColor: 'none',
            spacing: [0,0,0,0]
        },
        title: {
            text: null
        },
        plotOptions: {
            series: {
                showInLegend: false
            },
            column: {
                borderWidth: 0,
                stacking: 'percent',
            }
        },
        xAxis: {
            categories: ['us', 'sy', 'ni', 'id', 'wa', 'hi', 'si', 'st'],
            lineColor: 'transparent',
            minorTickLength: 0,
            tickLength: 0,
        },
        yAxis: {
            title: {
                text: null
            },
            gridLineWidth: 0,
            minorGridLineWidth: 0,
            labels: {
                enabled: false
            }
        },
        tooltip: {
            shared: true,
            formatter: function () {
                var s = '<span style="font-size:11px">' + cpuUsingLabel(this.x) + '</span>';
                $.each(this.points, function () {
                    if (this.series.name == 'used') {
                        s += '<br><b>' + this.y + '%</b>';
                    }
                });
                return s;
            }
        },
        series: [{
            name: 'not used',
            data: [92.7, 88.5, 100, 20.1, 99, 100, 99.8, 100],
            color: '#343432'
        }, {
            name: 'used',
            data: [7.3, 11.5, 0.0, 79.9, 1.0, 0.0, 0.2, 0.0],
            color: '#F2A31B'
        }],
        credits: {
            enabled: false
        }
    });
}

/**
 *Chart for the memory usage
 */
function chartMemUsage() {
    $('#mem-usage-chart').highcharts({
        chart: {
            backgroundColor: 'none',
            spacing: [0,0,0,0]
        },
        title: {
            text: null
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: false,
                borderColor: 'transparent'
            }
        },
        tooltip: {
            formatter: function() {
                return this.series.name + '<br>' +
                       this.point.name + ': <b>' + this.point.percentage.toFixed(1) + '%</b> ('+this.point.y+'G)';
            }
        },
        series: [{
            type: 'pie',
            name: 'Memory',
            data: [
                ['Used', 46],
                ['Free', 12]
            ]
        }],
        colors: ['#F89144', '#333333'],
        credits: {
            enabled: false
        }
    });
}

/**
 *Chart for the swap usage
 */
function chartSwapUsage() {
    $('#swap-usage-chart').highcharts({
        chart: {
            backgroundColor: 'none',
            spacing: [0,0,0,0]
        },
        title: {
            text: null
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: false
                },
                borderColor: 'transparent',
            }
        },
        tooltip: {
            formatter: function() {
                return this.series.name + '<br>' +
                       this.point.name + ': <b>' + this.point.percentage.toFixed(1) + '%</b> ('+this.point.y+'G)';
            }
        },
        series: [{
            type: 'pie',
            name: 'Swap',
            data: [
                ['Used', 8.8],
                ['Free', 26]
            ]
        }],
        colors: ['#F89144', '#333333'],
        credits: {
            enabled: false
        }
    });
}

/**
 *Chart for the disk usage
 */
function chartDiskUsage() {
    $('#disk-usage-chart').highcharts({
        chart: {
            type: 'bar',
            backgroundColor: 'none',
            spacing: [0,0,0,0]
        },
        title: {
            text: null
        },
        plotOptions: {
            series: {
                showInLegend: false
            },
            bar: {
                borderWidth: 0,
                stacking: 'percent',
            }
        },
        xAxis: {
            categories: ['/', '/dev', '/dev/shm', '/nix', '/proc/kcore'],
            lineColor: 'transparent',
            minorTickLength: 0,
            tickLength: 0,
        },
        yAxis: {
            title: {
                text: null
            },
            gridLineWidth: 0,
            minorGridLineWidth: 0,
            labels: {
                enabled: false
            }
        },
        tooltip: {
            shared: true,
            formatter: function () {
                var s = '<b>' + this.x + '</b><br><b>Size:</b> ' + this.points[0].total + 'G';
                $.each(this.points, function () {
                    s += '<br><b>' + this.series.name + ':</b> ' + this.y + 'G';
                });
                return s;
            },
        },
        series: [{
            name: 'Avail',
            data: [1.5, 30, 0.064, 308, 30],
            color: '#343432'
        }, {
            name: 'Used',
            data: [0.038, 0, 0, 534, 0],
            color: '#93C784'
        }],
        credits: {
            enabled: false
        }
    });
}

/**
 *Chart for the tasks count
 */
function chartTasksCount() {
    $('#tasks-count-chart').highcharts({
        chart: {
            backgroundColor: 'none',
            spacing: [0,0,0,0]
        },
        title: {
            text: null
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: false,
                borderColor: 'transparent'
            }
        },
        tooltip: {
            formatter: function() {
                return this.series.name + '<br>' +
                       this.point.name + ': <b>' + this.point.y + 'x</b>';
            }
        },
        series: [{
            type: 'pie',
            name: 'Process',
            data: [
                ['apache2', 8],
                ['mysqld', 2],
                ['tmux', 2],
                ['bash', 2],
                ['micro-inetd', 1],
                ['dropbear', 1]
            ]
        }],
        colors: ['#BBCC99', '#95A674', '#717F53', '#4D5936', '#3D4E1D', '#303D19'],
        credits: {
            enabled: false
        }
    });
}
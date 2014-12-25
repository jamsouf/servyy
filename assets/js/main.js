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
    chartTasksCount();
    chartDiskUsage();
}

/**
 * Create good looking scrollbars
 */
function initScrollbars() {
    $(".box .wrap").mCustomScrollbar({
        scrollInertia: 400
    });
}

/**
 * Convert the byte value into better readable format
 */
function formatBytes(bytes, precision) {
    precision = typeof precision !== 'undefined' ? precision : 2;
    var suffixes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB'];
    if (bytes === 0) return 0 + ' ' + suffixes[0];
    var base = Math.log(bytes) / Math.log(1024);
    
    return Math.pow(1024, base - Math.floor(base)).toFixed(precision) + ' ' + suffixes[Math.floor(base)];
}

/**
 * Convert the cpu load identifier into readable text
 */
function cpuUsingLabel(key) {
    var label;
    switch (key) {
        case 'us':
        case 'be': label = 'user processes'; break;
        case 'sy': label = 'system processes'; break;
        case 'ni': label = 'processes priority upgraded'; break;
        case 'id':
        case 'un': label = 'not used (idle)'; break;
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
            spacing: [10,10,10,10]
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
            data: Data.cpuLoadAvg
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
            categories: Data.cpuLoadCategories,
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
            data: Data.cpuLoadNotUsed,
            color: '#343432'
        }, {
            name: 'used',
            data: Data.cpuLoadUsed,
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
                       this.point.name + ': <b>' + this.point.percentage.toFixed(0) + '%</b> ('+formatBytes(this.point.y,0)+')';
            }
        },
        series: [{
            type: 'pie',
            name: 'Memory',
            data: [
                ['Used', Data.memoryUsed],
                ['Free', Data.memoryFree]
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
                       this.point.name + ': <b>' + this.point.percentage.toFixed(0) + '%</b> ('+formatBytes(this.point.y,0)+')';
            }
        },
        series: [{
            type: 'pie',
            name: 'Swap',
            data: [
                ['Used', Data.swapUsed],
                ['Free', Data.swapFree]
            ]
        }],
        colors: ['#F89144', '#333333'],
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
            data: Data.tasksCount
        }],
        colors: ['#BBCC99', '#95A674', '#717F53', '#4D5936', '#3D4E1D', '#303D19'],
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
            categories: Data.diskCategories,
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
                var s = '<b>' + this.x + '</b><br><b>Size:</b> ' + formatBytes(this.points[0].total,0);
                $.each(this.points, function () {
                    s += '<br><b>' + this.series.name + ':</b> ' + formatBytes(this.y,0);
                });
                return s;
            },
        },
        series: [{
            name: 'Avail',
            data: Data.diskAvail,
            color: '#343432'
        }, {
            name: 'Used',
            data: Data.diskUsed,
            color: '#93C784'
        }],
        credits: {
            enabled: false
        }
    });
}
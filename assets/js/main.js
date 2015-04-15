/**
 * Global variable to controle
 * displaying hidden files or not 
 */
var showHiddenFiles = false;

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
    initNavi();
    initScrollbars();
    initDashboard();
    initBrowser();
}

/**
 * Create the dashboard
 */
function initDashboard() {
    chartCpuLoadAverage();
    chartCpuLoadUsing();
    chartMemUsage();
    chartSwapUsage();
    chartTasksCount();
    chartDiskUsage();
    $('#c-dashboard').show();
}

/**
 * Create the browser
 */
function initBrowser() {
    initBrowserList();
    initFilterField();
    initHiddenFilesLink();
    $('#c-browser').hide();
    $('#file-close').click(function(){$('#c-file').hide();});
}

/**
 * Add the actions to the navi menu
 */
function initNavi() {
    $('#navi-dashboard').click(function(){
        $('#c-dashboard').show();
        $('#c-browser').hide();
        $('#c-file').hide();
    });
    $('#navi-browser').click(function(){
        $('#c-dashboard').hide();
        $('#c-browser').show();
    });
}

/**
 * Create good looking scrollbars
 */
function initScrollbars() {
    $(".box .wrap").mCustomScrollbar({
        scrollInertia: 400
    });
    $("#file-content-wrap").mCustomScrollbar({
        scrollInertia: 400,
        axis: "x",
        advanced: {
            autoExpandHorizontalScroll: true
        }
    });
}

/**
 * Create the filterable browser list
 */
function initBrowserList() {
    var options = {
        valueNames: ['name','size','lastmod','owner'],
        indexAsync: true
    };
    
    var browserList = new List('browser-list', options);
    
    $("a.resource").click(function(){
        loadBrowserList($(this).data('url'), $(this).data('file'));
    });
    
    $("a.file").click(function(){
        loadFileContent($(this).data('url'), $(this).data('file'));
    });
}

/**
 * Create listener for the filter field
 */
function initFilterField() {
    var $filter = $("#filterField"),
        value, path;
        
    $filter.keyup(function (e) {
        value = $filter.val();
        if (e.keyCode == 13 && value !== '' && value !== '.' && value !== '\\') {
            if (value.indexOf('..') > -1) {
                path = atob($(this).data('file'));
                path = path.split('/');
                path = path.filter(function(n){ return n !== '' });
                path.pop();
                path = (path.length === 0) ? '/' : '/' + path.join('/') + '/';
            }
            else if (value.charAt(0) == '/') {
                path = value;
            }
            else {
                path = atob($(this).data('file')) + value;
            }
            if (path.slice(-1) != '/') {
                path = path + '/';
            }
            loadBrowserList($(this).data('url'), btoa(path));
        }
    });
}

/**
 * Create listener for the show hidden files link
 */
function initHiddenFilesLink() {
    $("a.hiddenfi").click(function(){
        showHiddenFiles = !showHiddenFiles;
        var $hiddenicon = $("#hiddenicon");
        if (showHiddenFiles === true) {
            $hiddenicon.removeClass("fa-toggle-off");
            $hiddenicon.addClass("fa-toggle-on");
        }
        else {
            $hiddenicon.removeClass("fa-toggle-on");
            $hiddenicon.addClass("fa-toggle-off");
        }
        loadBrowserList($(this).data('url'), $(this).data('file'));
    });
}

/**
 * Make a ajax call to load the browser list
 */
function loadBrowserList(url, path) {
    $.ajax({
        url: url,
        data: {
            type: 'browser',
            file: path,
            showHiddenFiles: showHiddenFiles
        },
        type: 'GET',
        dataType: 'json',
        success: function(result) {
            if (result['status'] == 'success') {
                $("#filterField").val('');
                $("#filterField").data('file', result['pathForDataAttr']);
                $("a.hiddenfi").data('file', result['pathForDataAttr']);
                $("#breadcrumb").html(result['breadcrumb']);
                $("#listing tbody").html(result['list']);
                $('#c-file').hide();
                initBrowserList();
            }
        }
    });
}

/**
 * Make a ajax call to load the file content
 */
function loadFileContent(url, path) {
    $.ajax({
        url: url,
        data: {
            type: 'file',
            file: path
        },
        type: 'GET',
        dataType: 'json',
        success: function(result) {
            if (result['status'] == 'success') {
                $("#file-content").removeClass();
                var language = getLanguageFromExtension(result['fileExtension']);
                if (language != null) {
                    $("#file-content").addClass(language);
                }
                
                $("#file-name").text(result['fileName']);
                $("#file-loc").text(result['fileLoc']);
                $("#file-path").text(result['filePath']);
                $("#file-content").text(result['fileContent']);
                $("#c-file").show();
                $("#file-content").each(function(i, block) {
                    hljs.highlightBlock(block);
                });
            }
        }
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
 * Get the code language from the file extension
 */
function getLanguageFromExtension(ext) {
    var map = {
        cpp: ['cpp'],
        css: ['css'],
        html: ['html'],
        ini: ['ini'],
        java: ['java'],
        javascript: ['js'],
        json: ['json'],
        markdown: ['md', 'mdown', 'markdown', 'mdtext'],
        perl: ['pl'],
        php: ['php', 'php5'],
        python: ['py'],
        ruby: ['rb'],
        sql: ['sql'],
        xml: ['xml']
    };
    
    for (var language in map) {
        var index = $.inArray(ext, map[language]);
        if (index != -1) {
            return language;
        }
    }
    
    return null;
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
                       this.point.name + ': <b>' + this.point.percentage.toFixed(0) + '%</b> ('+formatBytes(this.point.y,1)+')';
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
                       this.point.name + ': <b>' + this.point.percentage.toFixed(0) + '%</b> ('+formatBytes(this.point.y,1)+')';
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
                var s = '<b>' + this.x + '</b><br><b>Size:</b> ' + formatBytes(this.points[0].total,1);
                $.each(this.points, function () {
                    s += '<br><b>' + this.series.name + ':</b> ' + formatBytes(this.y,1);
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
<?php ini_set('display_errors', 1); $servyy = new Servyy() ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Server dashboard [jamsouf1-digitalocean] &ndash; Servyy</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://jamsouf.github.io/servyy/assets/css/jquery.mCustomScrollbar.min.css" rel="stylesheet">
        <link href="https://jamsouf.github.io/servyy/assets/css/style.css" rel="stylesheet">
    </head>
    <body>
        <div id="head">
            
            <div class="logo">
                <a href="https://github.com/jamsouf/servyy"><i class="fa fa-tasks"></i><br>Servyy</a>
            </div>
            <div class="left">
                172.26.85.1<br>
                jamsouf.digital.ocean
            </div>
            <div class="right">
                Wed, 17.12.2014<br>
                18:38:53 UTC
            </div>
            <div class="clear"></div>
            
        </div>
            
        <div id="main">
            
            <div id="content">
                
                <div class="box float wp33">
                    <div class="wrap hf150">
                        <div id="info-os" class="inner">
                            <span class="head-title">Ubuntu 14.04.1 LTS</span>
                            <span class="sub-title">jamsouf-servyy-1162049</span>
                            <div class="content">
                                <span class="bold">IPv4 address:</span> 172.17.189.36<br>
                                <span class="bold">IPv6 address:</span> fe80::e4b0:d6ff:feaa:6c48<br>
                                <span class="bold">MAC address:</span> e6:b0:d6:aa:6c:48
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp33">
                    <div class="wrap hf150">
                        <div id="info-cpu" class="inner">
                            <span class="head-title">Intel(R) Xeon(R) CPU @ 2.60GHz</span>
                            <span class="sub-title">16 CPU(s), 64-bit, 2 MB cache</span>
                            <span class="load-avg">4.06, 3.63, 3.46</span>
                            <span class="up-since"> &nbsp; | &nbsp; up since 37 days</span>
                            <div id="cpu-load-average-chart"></div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp33 nmright">
                    <div class="wrap hf150">
                        <div class="inner">
                            <div id="cpu-load-using-chart"></div>
                            <div id="cpu-total-load">
                                <span class="value">20%</span><br>
                                <span class="ident">cpu load</span>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp33">
                    <div class="wrap hf235">
                        <div class="inner">
                            <div id="mem-usage">
                                <div id="mem-usage-chart"></div>
                                <b>Memory:</b> 59G &nbsp;&nbsp; <b>Used:</b> 46G &nbsp;&nbsp; <b>Free:</b> 13G
                            </div>
                            <div id="swap-usage">
                                <div id="swap-usage-chart"></div>
                                <b>Swap:</b> 34G &nbsp;&nbsp; <b>Used:</b> 8.8G &nbsp;&nbsp; <b>Free:</b> 25.2G
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp66 nmright">
                    <div class="wrap hf235">
                        <div class="inner">
                            <div id="tasks-total" class="float">
                                <div class="tasks-total-box float">
                                    <span class="value-top">17</span><br>
                                    <span class="ident">Tasks</span>
                                </div>
                                <div class="clear"></div>
                                <div class="tasks-total-box float">
                                    <span class="value">1</span><br>
                                    <span class="ident">Running</span>
                                </div>
                                <div class="tasks-total-box float">
                                    <span class="value">16</span><br>
                                    <span class="ident">Sleeping</span>
                                </div>
                                <div class="clear"></div>
                                <div class="tasks-total-box float">
                                    <span class="value">0</span><br>
                                    <span class="ident">Stopped</span>
                                </div>
                                <div class="tasks-total-box float">
                                    <span class="value">0</span><br>
                                    <span class="ident">Zombie</span>
                                </div>
                            </div>
                            
                            <div id="tasks-count" class="float">
                                <div id="tasks-count-chart"></div>
                                <span class="ident">Number of process<br>instances:</span>
                                apache2 (8)<br>mysqld (2)<br>tmux (2)
                            </div>
                            
                            <div id="tasks-resources" class="float">
                                <table>
                                    <tr><th>%cpu</th><th>%mem</th><th>command</th></tr>
                                    <tr><td>9.5</td><td>0.8</td><td>mysqld</td></tr>
                                    <tr><td>2.0</td><td>0.4</td><td>apache2</td></tr>
                                    <tr><td>1.6</td><td>0.3</td><td>apache2</td></tr>
                                    <tr><td>1.4</td><td>0.1</td><td>apache2</td></tr>
                                    <tr><td>1.3</td><td>0.0</td><td>apache2</td></tr>
                                    <tr><td>1.2</td><td>0.0</td><td>init</td></tr>
                                    <tr><td>0.9</td><td>0.0</td><td>cron</td></tr>
                                    <tr><td>0.8</td><td>0.0</td><td>syslogd</td></tr>
                                    <tr><td>0.9</td><td>0.0</td><td>exim4</td></tr>
                                    <tr><td>0.8</td><td>0.0</td><td>xinetd</td></tr>
                                </table>
                            </div>
                            
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp50">
                    <div class="wrap hf150">
                        <div class="inner">
                            <div id="disk-usage-chart"></div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp50 nmright">
                    <div class="wrap hf150">
                        <div class="inner">
                            <div class="package">
                                <div class="ident">PHP</div>
                                <div class="value">5.5.9-1ubuntu4.5</div>
                            </div>
                            <div class="package">
                                <div class="ident">nginx</div>
                                <div class="value">nginx/1.4.6 (Ubuntu)</div>
                            </div>
                            <div class="package">
                                <div class="ident">Apache</div>
                                <div class="value">Apache/2.4.7 (Ubuntu)</div>
                            </div>
                            <div class="package">
                                <div class="ident">MySQL</div>
                                <div class="value">14.14 Distrib 5.5.40</div>
                            </div>
                            <div class="package">
                                <div class="ident">Perl</div>
                                <div class="value">5.18.2</div>
                            </div>
                            <div class="package">
                                <div class="ident">Java</div>
                                <div class="value">1.7.0_65</div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                
                <div class="clear"></div>
                
            </div>
            
        </div>
        
        <script src="https://jamsouf.github.io/servyy/assets/js/jquery-2.1.1.min.js"></script>
        <script src="https://jamsouf.github.io/servyy/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script src="https://jamsouf.github.io/servyy/assets/js/highcharts-4.0.4.js"></script>
        <script src="https://jamsouf.github.io/servyy/assets/js/main.js"></script>
    </body>
</html>

<?php

class Servyy {
    
    public function printAll() {
        echo '<pre>';

        #echo '<h2>Linux version:</h2>';
        #passthru('cat /etc/issue');
        #echo "\n------------\n";
        
        #echo '<h2>Hostname:</h2>';
        #passthru('hostname');
        #echo "\n------------\n";
        
        #echo '<h2>IP config:</h2>';
        #passthru('/sbin/ifconfig');
        #echo "\n------------\n";
        
        #echo '<h2>Architektur:</h2>';
        #passthru('uname -m');
        #echo "\n------------\n";
        
        #echo '<h2>CPU info:</h2>';
        #passthru('cat /proc/cpuinfo');
        #echo "\n------------\n";
        
        #echo '<h2>CPU usage:</h2>';
        #passthru('uptime');
        #echo "\n------------\n";
        
        #echo '<h2>RAM:</h2>';
        #passthru('free -h');
        #echo "\n------------\n";
        
        #echo '<h2>Disk space:</h2>';
        #passthru('df -h');
        #echo "\n------------\n";
        
        #echo '<h2>Snapshot of top:</h2>';
        #passthru('top -n 1 -b');
        #echo "\n------------\n";
        
        #echo '<h2>Apache version:</h2>';
        #passthru('apache2 -v');
        #echo "\n------------\n";
        
        echo '<h2>nginx version:</h2>';
        passthru('nginx -v');
        echo "\n------------\n";
        
        echo '<h2>PHP version:</h2>';
        passthru('php -v');
        echo "\n------------\n";
        
        echo '<h2>MySQL version:</h2>';
        passthru('mysql --version');
        echo "\n------------\n";
        
        #echo '<h2>Server time:</h2>';
        #passthru('date');
        #echo "\n------------\n";
        
        echo '</pre>';
    }
}

?>
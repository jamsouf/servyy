<?php ini_set('display_errors', 1); $servyy = new Servyy() ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Servyy</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href='https://jamsouf.github.io/servyy/assets/css/style.css' rel='stylesheet' type='text/css'>
    </head>
    <body>
        <div id="main">
            
            <div id="content">
                
                <div class="box float wp50">
                    <div class="inner">
                        Ubuntu 14.04.01 LTS<br>
                        Apache Stack<br>
                        PHP 5.3 / nginx 1.6
                    </div>
                </div>
                
                <div class="box float wp50 nmright">
                    <div class="inner">
                        Ubuntu 14.04.01 LTS<br>
                        Apache Stack<br>
                        PHP 5.3 / nginx 1.6
                    </div>
                </div>
                
                <div class="box float wp33">
                    <div class="inner">
                        Ubuntu 14.04.01 LTS<br>
                        Apache Stack<br>
                        PHP 5.3 / nginx 1.6
                    </div>
                </div>
                
                <div class="box float wp33">
                    <div class="inner">
                        Ubuntu 14.04.01 LTS<br>
                        Apache Stack<br>
                        PHP 5.3 / nginx 1.6
                    </div>
                </div>
                
                <div class="box float wp33 nmright">
                    <div class="inner">
                        Ubuntu 14.04.01 LTS<br>
                        Apache Stack<br>
                        PHP 5.3 / nginx 1.6
                    </div>
                </div>
                
                <div class="clear"></div>
                
            </div>
            
        </div>
    </body>
</html>

<?php

class Servyy {
    
    public function printAll() {
        echo '<pre>';

        echo '<h2>Linux version:</h2>';
        passthru('cat /etc/issue');
        echo "\n------------\n";
        
        echo '<h2>Hostname:</h2>';
        passthru('hostname');
        echo "\n------------\n";
        
        echo '<h2>IP config:</h2>';
        passthru('/sbin/ifconfig');
        echo "\n------------\n";
        
        echo '<h2>CPU info:</h2>';
        passthru('cat /proc/cpuinfo');
        echo "\n------------\n";
        
        echo '<h2>CPU usage:</h2>';
        passthru('uptime');
        echo "\n------------\n";
        
        echo '<h2>Snapshot of top:</h2>';
        passthru('top -n 1 -b');
        echo "\n------------\n";
        
        echo '<h2>Process tree:</h2>';
        passthru('pstree');
        echo "\n------------\n";
        
        echo '<h2>RAM:</h2>';
        passthru('free -h');
        echo "\n------------\n";
        
        echo '<h2>Disk space:</h2>';
        passthru('df -h');
        echo "\n------------\n";
        
        echo '<h2>Webserver version:</h2>';
        passthru('nginx -v');
        echo "\n------------\n";
        
        echo '<h2>PHP version:</h2>';
        passthru('php -v');
        echo "\n------------\n";
        
        echo '<h2>MySQL version:</h2>';
        passthru('mysql --version');
        echo "\n------------\n";
        
        echo '<h2>Server time:</h2>';
        passthru('date');
        echo "\n------------\n";
        
        echo '</pre>';
    }
}

?>
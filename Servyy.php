<?php

/**
 * Servyy - single page server dashboard
 * @author Jamil Soufan
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/jamsouf/servyy
 */
class Servyy
{
    /**
     * Holds the determined server data
     * @var array
     */
    private $data = array();
    
    /**
     * Start the application
     * @param array $params
     */
    public function run($params)
    {
        if (empty($params)) {
            $this->load('all');
        } elseif ($params['type'] == 'overview' && $params['action'] == 'refresh') {
            $this->load('refresh');
        }
    }
    
    /**
     * Load all server data
     * @param string $scope
     */
    private function load($scope)
    {
        if ($scope == 'all') {
            $this->data['os'] = $this->execute('os');
            $this->data['hostname'] = $this->execute('hostname');
            $this->data['address'] = $this->execute('address');
            $this->data['architecture'] = $this->execute('architecture');
            $this->data['cpuinfo'] = $this->execute('cpuinfo');
        }
        
        $this->data['date'] = $this->execute('date');
        $this->data['uptime'] = $this->execute('uptime');
        $this->data['top'] = $this->execute('top');
        $this->data['toptable'] = $this->createTopTable($this->data['top']);
        $this->data['memory'] = $this->execute('memory');
        $this->data['disk'] = $this->execute('disk');
    }
    
    /**
     * Get the console command
     * @param string $type
     * @return string
     */
    private function getCommand($type)
    {
        switch ($type) {
            case 'date': $command = 'date +%a,%d.%m.%Y-%H:%M:%S,%Z'; break;
            case 'os': $command = 'lsb_release -a'; break;
            case 'hostname': $command = 'hostname'; break;
            case 'address': $command = 'ifconfig eth0'; break;
            case 'architecture': $command = 'uname -m'; break;
            case 'cpuinfo': $command = 'cat /proc/cpuinfo'; break;
            case 'uptime': $command = 'uptime'; break;
            case 'top': $command = 'top -n 1 -b'; break;
            case 'memory': $command = 'free -b'; break;
            case 'disk': $command = 'df -k'; break;
            case 'linux': $command = 'uname -r'; break;
            case 'apache': $command = 'apache2 -v'; break;
            case 'nginx': $command = 'nginx -v'; break;
            case 'mysql': $command = 'mysql --version'; break;
            case 'java': $command = 'java -version'; break;
            case 'php': $command = 'php -v'; break;
            case 'perl': $command = 'perl -v'; break;
            case 'python': $command = 'python --version'; break;
            case 'ruby': $command = 'ruby --version'; break;
        }
        
        return $command;
    }
    
    /**
     * Execute the console command for the given type
     * @param string $type
     * @return string
     */
    private function execute($type)
    {
        return shell_exec($this->getCommand($type));
    }
    
    /**
     * Transform the top output into an array
     * @param string $top
     * @return array
     */
    private function createTopTable($top)
    {
        $topValue = substr($top, strpos($top, 'PID'));
        $topArr = explode("\n", trim($topValue));
        
        foreach ($topArr as $line) {
            if (substr($line, 0, 3) == 'PID') continue;
            $line = preg_replace("/\s+/", "{-}", trim($line));
            $tmp = explode("{-}", $line);
            $arr[] = array(
                'pid' => trim($tmp[0]),
                'user' => trim($tmp[1]),
                'pr' => trim($tmp[2]),
                'ni' => trim($tmp[3]),
                'virt' => trim($tmp[4]),
                'res' => trim($tmp[5]),
                'shr' => trim($tmp[6]),
                's' => trim($tmp[7]),
                'cpu' => trim($tmp[8]),
                'mem' => trim($tmp[9]),
                'time' => trim($tmp[10]),
                'command' => trim($tmp[11]),
            );
        }
        
        return $arr;
    }
    
    /**
     * Get the IP or MAC address
     * @param string $type
     * @return string
     */
    public function getAddress($type)
    {
        switch ($type) {
            case 'ipv4':
                $result = $this->extractStr($this->data['address'], "(inet addr|inet Adresse):", " ", 2);
                break;
            case 'ipv6':
                $result = $this->extractStr($this->data['address'], "(inet6 addr|inet6-Adresse):", "\/", 2);
                break;
            case 'mac':
                $result = $this->extractStr($this->data['address'], "(HWaddr|Hardware Adresse)", "\n", 2);
                break;
        }
        
        return $result;
    }
    
    /**
     * Get the hostname
     * @return string
     */
    public function getHostname()
    {
        return trim($this->data['hostname']);
    }
    
    /**
     * Get the date or time
     * @param string $type
     * @return string
     */
    public function getDateTime($type)
    {
        $arr = explode('-', $this->data['date']);
        switch ($type) {
            case 'date':
                $result = str_replace(',', ', ', $arr[0]);
                break;
            case 'time':
                $result = str_replace(',', ' ', $arr[1]);
                break;
        }
        
        return $result;
    }
    
    /**
     * Get the operating system
     * @return string
     */
    public function getOperatingSystem()
    {
        return $this->extractStr($this->data['os'], "Description:", "\n");
    }
    
    /**
     * Get the CPU model name
     * @return string
     */
    public function getCpuName()
    {
        return $this->extractStr($this->data['cpuinfo'], "model name[ |\t]*:", "\n");
    }
    
    /**
     * Get the number of CPUs
     * @return string
     */
    public function getNumberOfCpus()
    {
        return $this->extractStr($this->data['cpuinfo'], "cpu cores[ |\t]*:", "\n");
    }
    
    /**
     * Get the architecture 32-bit or 64-bit
     * @return string
     */
    public function getBitSize()
    {
        $arr = array(
            'x86_64' => 64,
            'x86-64' => 64,
            'i386' => 32,
            'i686' => 32
        );
        
        return $arr[trim($this->data['architecture'])];
    }
    
    /**
     * Get the size of the cache
     * @return string
     */
    public function getCacheSize()
    {
        $cacheValue = $this->extractStr($this->data['cpuinfo'], "cache size[ |\t]*:", "\n");
        $size = preg_replace("/[^0-9]/", "", $cacheValue);
        
        if (strpos($cacheValue, 'KB') !== false) {
            $size *= 1024;
        }
        
        return $this->formatBytes($size, 0);
    }
    
    /**
     * Get the load average values
     * @param integer $type
     * @return float
     */
    public function getLoadAverage($type)
    {
        $avgValue = $this->extractStr($this->data['uptime'], "load average:", "\n");
        $avgArr = explode(", ", $avgValue);
        $arr = array(
             1 => trim($this->rcp($avgArr[0])),
             5 => trim($this->rcp($avgArr[1])),
            15 => trim($this->rcp($avgArr[2]))
        );
        
        return $arr[$type];
    }
    
    /**
     * Get the time since the server is up
     * @return string
     */
    public function getUpSince()
    {
        return $this->extractStr($this->data['uptime'], "up", ",");
    }
    
    /**
     * Get the load of the CPU
     * @param string $type
     * @param integer $number (1-8)
     * @return string
     */
    public function getCpuLoad($type, $number = null)
    {
        $loadValue = $this->extractStr($this->data['top'], "Cpu\(s\):", "\n");
        $loadArr = explode(", ", $loadValue);
        
        foreach ($loadArr as $str) {
            $tmp = explode(" ", trim($str));
            $used = trim($this->rcp($tmp[0]));
            $arr[] = array(
                'ident' => trim($tmp[1]),
                'used' => $used,
                'nused' => 100 - $used
            );
        }
        
        switch ($type) {
            case 'total':
                $result = round(100 - $arr[3]['used']);
                break;
            case 'categories':
                $result = "'".$arr[0]['ident']."', '".$arr[1]['ident']."', '".$arr[2]['ident']."', '".$arr[3]['ident']."', '".$arr[4]['ident']."', '".$arr[5]['ident']."', '".$arr[6]['ident']."', '".$arr[7]['ident']."'";
                break;
            case 'used':
                $result = ''.$arr[0]['used'].', '.$arr[1]['used'].', '.$arr[2]['used'].', '.$arr[3]['used'].', '.$arr[4]['used'].', '.$arr[5]['used'].', '.$arr[6]['used'].', '.$arr[7]['used'].'';
                break;
            case 'notused':
                $result = ''.$arr[0]['nused'].', '.$arr[1]['nused'].', '.$arr[2]['nused'].', '.$arr[3]['nused'].', '.$arr[4]['nused'].', '.$arr[5]['nused'].', '.$arr[6]['nused'].', '.$arr[7]['nused'].'';
                break;
        }
        
        return $result;
    }
    
    /**
     * Get the memory capacity
     * @param string $type
     * @return float
     */
    public function getMemory($type)
    {
        $memValue = $this->extractStr($this->data['memory'], "(Mem|Speicher):", "\n", 2);
        $memValue = preg_replace("/\s+/", "-", $memValue);
        $memArr = explode("-", $memValue);
        
        $arr['total'] = $memArr[0];
        $arr['used'] = $memArr[1];
        $arr['free'] = $memArr[2];
        
        return $arr[$type];
    }
    
    /**
     * Get the swap capacity
     * @param string $type
     * @return float
     */
    public function getSwap($type)
    {
        $swapValue = $this->extractStr($this->data['memory'], "(Swap|Auslagerungsdatei):", "\n", 2);
        $swapValue = preg_replace("/\s+/", "-", $swapValue);
        $swapArr = explode("-", $swapValue);
        
        $arr['total'] = $swapArr[0];
        $arr['used'] = $swapArr[1];
        $arr['free'] = $swapArr[2];
        
        return $arr[$type];
    }
    
    /**
     * Get the number of tasks
     * @param string $type
     * @return integer
     */
    public function getTasks($type)
    {
        $tasksValue = $this->extractStr($this->data['top'], "(Tasks|Aufgaben):", "\n", 2);
        $tasksArr = explode(",", $tasksValue);
        
        foreach ($tasksArr as $str) {
            $tmp = explode(" ", trim($str));
            $arr[] = array(
                'ident' => trim($tmp[1]),
                'value' => trim($tmp[0])
            );
        }
        
        $arr['total'] = $arr[0]['value'];
        $arr['running'] = $arr[1]['value'];
        $arr['sleeping'] = $arr[2]['value'];
        $arr['stopped'] = $arr[3]['value'];
        $arr['zombie'] = $arr[4]['value'];
        
        return $arr[$type];
    }
    
    /**
     * Get name and number of instances
     * @param string $type
     * @param integer $number
     * @return string
     */
    public function getTopCountInstances($type, $number = null)
    {
        $tmp = array();
        foreach ($this->data['toptable'] as $process) {
            if (array_key_exists($process['command'], $tmp)) {
                $tmp[$process['command']]++;
            } else {
                $tmp[$process['command']] = 1;
            }
        }
        
        arsort($tmp);
        foreach ($tmp as $command => $count) {
            $topCountInstances[] = array('command' => $command, 'count' => $count);
        }
        
        if ($type == 'chart') {
            $i = 0;
            foreach ($topCountInstances as $instance) {
                if ($i >= 6) continue;
                $arr[] = "['".$instance['command']."',".$instance['count']."]";
                $i++;
            }
            $result = implode($arr, ",");
        } else {
            $result = $topCountInstances[$number][$type];
        }
        
        return $result;
    }
    
    /**
     * Get top commands with the most load
     * @param string $type
     * @param integer $number
     * @return string
     */
    public function getTop($type, $number = null)
    {
        foreach ($this->data['toptable'] as $process) {
            $top[] = array('cpu' => $process['cpu'], 'mem' => $process['mem'], 'command' => $process['command']);
        }
        
        array_multisort($top, SORT_DESC);
        $result = $top[$number][$type];
        
        return $result;
    }
    
    /**
     * Get the disk capacity
     * @param string $type
     * @return string
     */
    public function getDiskUsage($type)
    {
        $diskLines = explode("\n", trim($this->data['disk']));
        foreach ($diskLines as $line) {
            $line = preg_replace("/\s+/", "{-}", $line);
            $tmp = explode("{-}", $line);
            
            if (is_numeric($tmp[1]) && $tmp[0] != 'none') {
                $categories[] = "'".trim($tmp[0])."'";
                $used[] = trim($tmp[2]) * 1024;
                $avail[] = trim($tmp[3]) * 1024;
            }
        }
        
        $arr['categories'] = implode(",", $categories);
        $arr['used'] = implode(",", $used);
        $arr['avail'] = implode(",", $avail);
        
        return $arr[$type];
    }
    
    /**
     * Get the version of a software package
     * @param string $type
     * @return string
     */
    public function getVersion($type)
    {
        $na = 'n/a';
        $output = $this->execute($type);
        if (empty($output)) return $na;
        
        switch ($type) {
            case 'linux':
                $result = $output;
                break;
            case 'apache':
                $result = $this->extractStr($output, "version:", "\n");
                break;
            case 'nginx':
                $result = $this->extractStr($output, "version:", "\n");
                break;
            case 'mysql':
                $result = $this->extractStr($output, "Ver", ",");
                break;
            case 'java':
                $result = $this->extractStr($output, "\"", "\"");
                break;
            case 'php':
                $result = $this->extractStr($output, "PHP", "\(");
                break;
            case 'perl':
                $result = $this->extractStr($output, "\(v", "\)");
                break;
            case 'python':
                $result = $this->extractStr($output, "Python", "\Z");
                break;
            case 'ruby':
                $result = $this->extractStr($output, "ruby", "\(");
                break;
        }
        
        if (empty($result)) $result = $na;
        
        return $result;
    }
    
    /**
     * Get a part of a string between 2 identifiers
     * @param string $string
     * @param string $start
     * @param string $end
     * @param integer $match
     * @return string
     */
    public function extractStr($string, $start, $end, $match = 1)
    {
        preg_match("/$start(.*?)$end/", $string, $matches);
        
        if (array_key_exists($match, $matches)) {
            $result = trim($matches[$match]);
        } else {
            $result = null;
        }
        
        return $result;
    }
    
    /**
     * Get human readable representation
     * @param float $bytes
     * @param integer $precision
     * @return string
     */
    public function formatBytes($bytes, $precision = 2) {
        $suffixes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB');
        if ($bytes == 0) return 0 . ' ' . $suffixes[0];
        $base = log($bytes, 1024);
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
    
    /**
     * Replace commas with points
     * @param mixed $value
     * @return mixed
     */
    public function rcp($value)
    {
        return str_replace(",", ".", $value);
    }
}

$s = new Servyy();
$s->run($_GET);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Server dashboard [<?=$s->getHostname()?>] &ndash; Servyy</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://jamsouf.github.io/servyy/assets/css/jquery.mCustomScrollbar.min.css" rel="stylesheet">
        <link href="https://jamsouf.github.io/servyy/assets/css/style.css" rel="stylesheet">
        <script type="text/javascript">
            var Data = {
                cpuLoadAvg: [<?=$s->getLoadAverage(1)?>, <?=$s->getLoadAverage(5)?>, <?=$s->getLoadAverage(15)?>],
                cpuLoadCategories: [<?=$s->getCpuLoad('categories')?>],
                cpuLoadUsed: [<?=$s->getCpuLoad('used')?>],
                cpuLoadNotUsed: [<?=$s->getCpuLoad('notused')?>],
                memoryUsed: <?=$s->getMemory('used')?>,
                memoryFree: <?=$s->getMemory('free')?>,
                swapUsed: <?=$s->getSwap('used')?>,
                swapFree: <?=$s->getSwap('free')?>,
                tasksCount: [<?=$s->getTopCountInstances('chart')?>],
                diskCategories: [<?=$s->getDiskUsage('categories')?>],
                diskUsed: [<?=$s->getDiskUsage('used')?>],
                diskAvail: [<?=$s->getDiskUsage('avail')?>]
            };
        </script>
    </head>
    <body>
        <div id="head">
            
            <div class="logo">
                <a href="https://github.com/jamsouf/servyy"><i class="fa fa-tasks"></i><br>Servyy</a>
            </div>
            <div class="left">
                <?=$s->getAddress('ipv4')?><br>
                <?=$s->getHostname()?>
            </div>
            <div class="right">
                <?=$s->getDateTime('date')?><br>
                <?=$s->getDateTime('time')?>
            </div>
            <div class="clear"></div>
            
        </div>
            
        <div id="main">
            
            <div id="content">
                
                <div class="box float wp33">
                    <div class="wrap hf150">
                        <div id="info-os" class="inner">
                            <span class="head-title"><?=$s->getOperatingSystem()?></span>
                            <span class="sub-title"><?=$s->getHostname()?></span>
                            <div class="content">
                                <span class="bold">IPv4 address:</span> <?=$s->getAddress('ipv4')?><br>
                                <span class="bold">IPv6 address:</span> <?=$s->getAddress('ipv6')?><br>
                                <span class="bold">MAC address:</span> <?=$s->getAddress('mac')?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp33">
                    <div class="wrap hf150">
                        <div id="info-cpu" class="inner">
                            <span class="head-title"><?=$s->getCpuName()?></span>
                            <span class="sub-title"><?=$s->getNumberOfCpus()?> CPU(s), <?=$s->getBitSize()?>-bit, <?=$s->getCacheSize()?> cache</span>
                            <span class="load-avg"><?=$s->getLoadAverage(1)?>, <?=$s->getLoadAverage(5)?>, <?=$s->getLoadAverage(15)?></span>
                            <span class="up-since"> &nbsp; | &nbsp; up since <?=$s->getUpSince()?></span>
                            <div id="cpu-load-average-chart"></div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp33 nmright">
                    <div class="wrap hf150">
                        <div class="inner">
                            <div id="cpu-load-using-chart"></div>
                            <div id="cpu-total-load">
                                <span class="value"><?=$s->getCpuLoad('total')?>%</span><br>
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
                                <b>Memory:</b> <?=$s->formatBytes($s->getMemory('total'),1)?> &nbsp;&nbsp; <b>Used:</b> <?=$s->formatBytes($s->getMemory('used'),1)?> &nbsp;&nbsp; <b>Free:</b> <?=$s->formatBytes($s->getMemory('free'),1)?>
                            </div>
                            <div id="swap-usage">
                                <div id="swap-usage-chart"></div>
                                <b>Swap:</b> <?=$s->formatBytes($s->getSwap('total'),1)?> &nbsp;&nbsp; <b>Used:</b> <?=$s->formatBytes($s->getSwap('used'),1)?> &nbsp;&nbsp; <b>Free:</b> <?=$s->formatBytes($s->getSwap('free'),1)?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp33">
                    <div class="wrap hf235">
                        <div class="inner">
                            <div id="tasks-total">
                                <table>
                                    <tr>
                                        <td><span class="value"><?=$s->getTasks('total')?></span><br><span class="ident">Tasks</span></td>
                                        <td><span class="value"><?=$s->getTasks('running')?></span><br><span class="ident">Running</span></td>
                                        <td><span class="value"><?=$s->getTasks('sleeping')?></span><br><span class="ident">Sleeping</span></td>
                                        <td><span class="value"><?=$s->getTasks('stopped')?></span><br><span class="ident">Stopped</span></td>
                                        <td><span class="value"><?=$s->getTasks('zombie')?></span><br><span class="ident">Zombie</span></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div id="tasks-count">
                                <div id="tasks-count-chart"></div>
                                <span class="ident">Number of process instances:</span><br>
                                <?=$s->getTopCountInstances('command',0)?> (<?=$s->getTopCountInstances('count',0)?>), 
                                <?=$s->getTopCountInstances('command',1)?> (<?=$s->getTopCountInstances('count',1)?>), 
                                <?=$s->getTopCountInstances('command',2)?> (<?=$s->getTopCountInstances('count',2)?>)
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box float wp33 nmright">
                    <div class="wrap hf235">
                        <div class="inner">
                            <div id="tasks-resources">
                                <table>
                                    <tr><th>%cpu</th><th>%mem</th><th>command</th></tr>
                                    <tr><td><?=$s->getTop('cpu',0)?></td><td><?=$s->getTop('mem',0)?></td><td><?=$s->getTop('command',0)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',1)?></td><td><?=$s->getTop('mem',1)?></td><td><?=$s->getTop('command',1)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',2)?></td><td><?=$s->getTop('mem',2)?></td><td><?=$s->getTop('command',2)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',3)?></td><td><?=$s->getTop('mem',3)?></td><td><?=$s->getTop('command',3)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',4)?></td><td><?=$s->getTop('mem',4)?></td><td><?=$s->getTop('command',4)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',5)?></td><td><?=$s->getTop('mem',5)?></td><td><?=$s->getTop('command',5)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',6)?></td><td><?=$s->getTop('mem',6)?></td><td><?=$s->getTop('command',6)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',7)?></td><td><?=$s->getTop('mem',7)?></td><td><?=$s->getTop('command',7)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',8)?></td><td><?=$s->getTop('mem',8)?></td><td><?=$s->getTop('command',8)?></td></tr>
                                    <tr><td><?=$s->getTop('cpu',9)?></td><td><?=$s->getTop('mem',9)?></td><td><?=$s->getTop('command',9)?></td></tr>
                                </table>
                            </div>
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
                                <div class="ident">Linux</div>
                                <div class="value"><?=$s->getVersion('linux')?></div>
                            </div>
                            <div class="package">
                                <div class="ident">Apache</div>
                                <div class="value"><?=$s->getVersion('apache')?></div>
                            </div>
                            <div class="package">
                                <div class="ident">nginx</div>
                                <div class="value"><?=$s->getVersion('nginx')?></div>
                            </div>
                            <div class="package">
                                <div class="ident">MySQL</div>
                                <div class="value"><?=$s->getVersion('mysql')?></div>
                            </div>
                            <div class="package">
                                <div class="ident">Java</div>
                                <div class="value"><?=$s->getVersion('java')?></div>
                            </div>
                            <div class="package">
                                <div class="ident">PHP</div>
                                <div class="value"><?=$s->getVersion('php')?></div>
                            </div>
                            <div class="package">
                                <div class="ident">Perl</div>
                                <div class="value"><?=$s->getVersion('perl')?></div>
                            </div>
                            <div class="package">
                                <div class="ident">Python</div>
                                <div class="value"><?=$s->getVersion('python')?></div>
                            </div>
                            <div class="package">
                                <div class="ident">Ruby</div>
                                <div class="value"><?=$s->getVersion('ruby')?></div>
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
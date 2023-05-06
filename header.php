<?php
function my_xhprof_enable() {
    switch ($_GET['_profile']) {
        case 'simple':
            define('DEBUG_MICROTIME_START', microtime(1));
            break;
    
        case 'all':
            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
            break;
    
        case 'cpu':
            xhprof_enable(XHPROF_FLAGS_CPU);
            break;
    
        case 'memory':
            xhprof_enable(XHPROF_FLAGS_MEMORY);
            break;
    
        case 'time':
        default:
            xhprof_enable();
    }
}
 
function my_xhprof_disable() {
    if ($_GET['_profile'] == 'simple') {
        $time = number_format(microtime(1) - DEBUG_MICROTIME_START, 4);
        $cur  = number_format(memory_get_usage() / 1024, 3);
        $peak = number_format(memory_get_peak_usage() / 1024, 3);
        print "\ntime = {$time} seconds
            \nmemory_get_usage = {$cur} kb
            \nmemory_get_peak_usage = {$peak} kb";
    } elseif (extension_loaded('xhprof')) {
        $XHPROF_ROOT = "/var";
        include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
        include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";
        $profiler_namespace = $_SERVER['HOSTNAME'] ?  $_SERVER['HOSTNAME'] : $_SERVER['SERVER_NAME'];
        $xhprof_data = xhprof_disable();
        $xhprof_runs = new XHProfRuns_Default();
        $output_dir = ini_get('xhprof.output_dir');
        if (!is_dir($output_dir)) {
            mkdir($output_dir, 0777, true);
        }
        $xhprof_runs->save_run($xhprof_data, $profiler_namespace);
  }
}
 
if (extension_loaded('xhprof') && isset($_GET['_profile'])) {
    my_xhprof_enable();
    register_shutdown_function('my_xhprof_disable');
}

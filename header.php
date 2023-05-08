<?php
function my_xhprof_enable() {
    if (PHP_SAPI == 'cli') {
        if (isset($_SERVER['xhprof'])) {
            switch ($_SERVER['xhprof']) {            
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
    } else {
        switch ($_GET['_profile']) {        
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
}
 
function my_xhprof_disable() {
    if (isset($_GET['_profile']) || isset($_SERVER['xhprof'])) {
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
 
if (extension_loaded('xhprof') && (PHP_SAPI == 'cli' || isset($_GET['_profile']))) {
    my_xhprof_enable();
    register_shutdown_function('my_xhprof_disable');
}

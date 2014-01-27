<?php
define('ROOT_DIR', dirname(__DIR__));
define('EVANEOS_ROOT_DIR', '/home/www.evaneos.com.dev/web/');

class CustomLoader {
    private $loader;

    public function __construct($loader) {
        $this->loader = $loader;
    }

    public function loadClass($class) {
        $result = $this->loader->loadClass($class);
        if (!$result) {
            $result = $this->loadLegacyClass($class);
        }

        return $result;
    }

    protected function loadLegacyClass($className) {
        // Berthe framework
        if(strstr($className, 'Evaneos_Berthe_'))  {
            $sFilePath = str_replace('_', '/', $className);
            if (file_exists(EVANEOS_ROOT_DIR . '/lib/' . $sFilePath . '.php')) {
                require_once(EVANEOS_ROOT_DIR . '/lib/' . $sFilePath . '.php');
                return true;
            }
        }
        // Business class using Berthe
        elseif(strstr($className, 'Berthe_'))  {
            $sFilePath = str_replace('_', '/', $className);
            $_fp = explode("/", $sFilePath);
            $count = count($_fp);
            $sFilePath = '';
            for ($i=0; $i<$count-1; $i++) {
                $sFilePath .= $_fp[$i] . '/';
            }
            $sFilePath .= $_fp[$count-2] . $_fp[$count-1];
            if (file_exists(EVANEOS_ROOT_DIR . '/app/' . $sFilePath . '.php')) {
                require_once EVANEOS_ROOT_DIR . '/app/' . $sFilePath . '.php';
                return true;
            }
        }
        return false;
    }
}

$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$loader->unregister();
spl_autoload_register(array(new CustomLoader($loader), 'loadClass'));



date_default_timezone_set('Europe/Paris');

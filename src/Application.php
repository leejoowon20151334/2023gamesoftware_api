<?php

namespace src;

use src\Core\API;

class Application
{
    private $handler = '/main';
    private $route = 200;

    public function __construct()
    {
        $this->_DefaultSetting();
        $this->_LoadClasses();
        $this->_Route();
    }

    public function _DefaultSetting()
    {
        date_default_timezone_set('Asia/Seoul');
	session_start();
	header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
    }

    public function _LoadClasses()
    {
        spl_autoload_register(function ($class_name) {
            if (!class_exists($class_name)) {
                if($class_name == 'Crypt_Hash' || $class_name == 'Math_BigInteger'){
                    $str_explode_res = explode('_',$class_name);
                    $class_name = $str_explode_res[1];
                    $path = $str_explode_res[0];
                    $class = 'phpseclib1.0.19/' . $path . '/' . $class_name;
                } else{
                    $class = str_replace('\\', '/', $class_name);
                }
                require_once ROOT_DIR . '/' . $class . '.php';
            }
        });
    }

    public function _Route()
    {
        $uri = $_REQUEST['handler'] ?? $this->handler;
        $this->handler = ltrim(parse_url($uri)['path'], '/');

        $dir = $this->FindDir("src/API/{$this->handler}.php");

        if ($this->route === 200 && $dir === false) {
            $api = new API();
            $api->Error404();
        }

        $class = preg_replace('/\\/{1,}/', '\\', $dir);
        $class = preg_replace('/.php$/', '', $class);
        $classname = []; preg_match('/\\\(\w*)$/', $class, $classname);

        if (!class_exists($class)) {
            $api = new API();
            $api->Error($classname[1] . " class is not exists. Please check namespace or class. ", 601);
        } else {
            $api = new $class();
        }

        $api->Call();
    }

    public function FindDir($full_dir)
    {
        $full_dir = "./{$full_dir}";
        $dir = preg_replace('/\\/{2,}/', '/', $full_dir);
        $dir = explode('/', $dir);

        if (count($dir) > 1) {
            $parent = $dir[0];
            $count = count($dir);

            for ($i = 1; $i < $count; $i++) {
                $find = $dir[$i];

                if ($this->FindFile($parent, $find) === false) {
                    return false;
                }

                $parent = $this->FindFile($parent, $find);

                if (strtolower($parent) == strtolower($full_dir)) {
                    return substr($parent, 2);
                }
            }
        }

        return false;
    }

    public function FindFile($parent_dir, $search_str)
    {
        if (file_exists($parent_dir)) {
            $d = scandir($parent_dir);
            $lower_d = '';

            foreach ($d as $dir) {
                if (strtolower($search_str) === strtolower($dir)) {
                    $lower_d = $dir;

                    break;
                }
            }

            return "{$parent_dir}/{$lower_d}";
        }

        return false;
    }
}

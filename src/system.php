<?php

session_start();
define('MY_DIR', dirname(__FILE__));


/**
 * This system is loosely inspired by Kohana. Their framework as too bulky for a game despite being a great system.
 *
 * @version 1.0
 */
class system
{
    const VERSION = '0.0.2';

    public function __construct($configFile, $name = 'default')
    {
        require_once MY_DIR.'/config.php';
        require_once MY_DIR.'/view.php';
        require_once MY_DIR.'/morph.php';
        $config = Config::init($configFile, $name);
        $this->path = $config->get('path');
        if ($config->get('database')) {
            require_once MY_DIR.'/database.php';
            if ($config->get('autoLoadDB')) {
                Database::Init();
            }
        }
        unset($_SESSION['globals']);
    }

    public static function getValue($name, $isInt = false)
    {
        if ($_SESSION['globals']) {
            $val = $_SESSION['globals'][$name];
        } else {
            $tmp = array_merge($_POST, $_REQUEST);
            $val = $tmp[$name];
            $_SESSION['globals'] = $tmp;
        }
        if ($isInt) {
            settype($val, 'int');
        } elseif (!is_array($val)) {
            $val = self::clean($val);
        }

        return $val;
    }

    public static function setValue($name, $value)
    {
        $_SESSION['globals'][$name] = $value;
    }

    public static function clearValue($name)
    {
        unset($_SESSION['globals'][$name]);
    }

    public function load($system = null, $action = null)
    {
        if (!$system) {
            $system = Config::get('default_system');
        }
        $file = Config::get('path').'/'.$system.'.php';
        $system[0] = strtoupper($system[0]);
        if (!is_file($file)) {
            throw new Exception("No class {$system} found");
        }
        include_once $file;
        $class = new $system();
        if ($action) {
            $action = 'action_'.$action;
            if (method_exists($class, $action)) {
                $class->$action();
            } else {
                throw new Exception("No method {$action} found");
            }
        } else {
            $class->action_index();
        }

        return true;
    }

    public static function send_email($from, $email, $subject, $message)
    {
        $header = "From: $from\nMIME-Version: 1.0\nContent-type: text/html charset=iso-8859-1\n";
        if (mail($email, $subject, $message, $header)) {
            return true;
        } else {
            return false;
        }
    }

    public static function clean($data)
    {
        // Fix &entity\n;
        $data = str_replace(['&amp;', '&lt;', '&gt;'], ['&amp;amp;', '&amp;lt;', '&amp;gt;'], $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }

    public function loadModule($name)
    {
        if (is_file($name)) {
            require_once $name;
        } else {
            throw new Exception("Couldn't find the module");
        }
    }
}

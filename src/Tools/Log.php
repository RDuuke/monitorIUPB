<?php
namespace App\Tools;

class Log implements InterfaceLog
{

    static function i (string $message, String $type) : bool
    {
        $log = "INFO[".date('d-m-Y h:i:s')."]: [" . $type  . "] " . $message . " " . "\n";
        return self::write($log);
    }

    static function e (string $message, String $type) : bool
    {
        $log = "ERROR[".date('d-m-Y h:i:s')."]: [" . $type  . "] " . $message . " " . "\n";
        return self::write($log);
    }
    static function a (string $message, String $type) : bool
    {
        $log = "ALERTA[".date('d-m-Y h:i:s')."]: [" . $type  . "] " . $message . " " . "\n";
        return self::write($log);
    }

    static function write(string $message) : bool
    {
        $directory = BASE_DIR . 'logs' . DS . 'logs.txt';

        if (is_writable($directory)) {

            if ($archive = fopen($directory, 'a+')) {

                if (fwrite($archive, $message)) {
                    return true;
                }
            }

        }

        return false;

    }
}
<?php
namespace App\Tools;

interface InterfaceLog
{
    static function e (string $message, String $type ) : bool;

    static function i (string $message, String $type ) : bool;

    static function a (string $message, String $type ) : bool;

    static function write (string $message) : bool;
}
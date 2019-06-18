<?php
header('Content-Type: text/html; charset=ISO-8859-1');
date_default_timezone_set("America/Bogota");

ini_set('display_errors', '1');
ini_set('memory_limit', '-1');

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .  ".." . DIRECTORY_SEPARATOR  . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
use App\Tools\cUrl;


$settings = include_once dirname(__DIR__) . DIRECTORY_SEPARATOR .  ".." . DIRECTORY_SEPARATOR . "bootstrap" . DIRECTORY_SEPARATOR . "database.php";

$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($settings['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$monitores = \App\Models\Monitor::all();
$dat = new DateTime();
$now =  $dat->format("Y-m-d H:i:s");
$date = new DateTime($now);
echo '<pre>';
foreach ($monitores as $monitor) {
    $first = $monitor->registers()->latest()->first();
    if ($monitor->status == 1) {
        if (is_null($first)) {
            $curl = new cUrl($monitor->url);
            $response = $curl->initialize()->execute();
            $info = $response->getInfo();
            $error = $response->getError();
            if (!$error) {
                $data = [
                    "monitor_id" => $monitor->id,
                    "response" => $info['http_code'],
                    "time" => str_replace(".", ",", $info["total_time"])
                ];
            } else {
                $data = [
                    "monitor_id" => $monitor->id,
                    "response" => $error,
                    "time" => str_replace(".", ",", $info["total_time"])
                ];
            }
            $curl->close();
            \App\Models\MonitorRegistro::create($data);
            continue;
        }
        $min = new DateTime($first->created_at);
        $diff = $date->diff($min);
        if ($monitor->type == "min") {
            if ($monitor->interval <= $diff->i) {
                $curl = new cUrl($monitor->url);
                $response = $curl->initialize()->execute();
                $error = $response->getError();
                $info = $response->getInfo();
                if (!$error) {
                    $data = [
                        "monitor_id" => $monitor->id,
                        "response" => $info['http_code'],
                        "time" => str_replace(".", ",", $info["total_time"])
                    ];
                } else {
                    $data = [
                        "monitor_id" => $monitor->id,
                        "response" => $error,
                        "time" => str_replace(".", ",", $info["total_time"])
                    ];
                }
                $curl->close();
                \App\Models\MonitorRegistro::create($data);
            }
        } else if ($monitor->type == "hour") {
            if ($monitor->interval <= $diff->h) {
                $curl = new cUrl($monitor->url);
                $response = $curl->initialize()->execute();
                $info = $response->getInfo();
                $error = $response->getError();
                if (!$error) {
                    $data = [
                        "monitor_id" => $monitor->id,
                        "response" => $info['http_code'],
                        "time" => str_replace(".", ",", $info["total_time"])
                    ];
                } else {
                    $data = [
                        "monitor_id" => $monitor->id,
                        "response" => $error,
                        "time" => str_replace(".", ",", $info["total_time"])
                    ];
                }
                $curl->close();
                \App\Models\MonitorRegistro::create($data);
            }
        } else if ($monitor->type == "day") {
            if ($monitor->interval <= $diff->days ) {
                $curl = new cUrl($monitor->url);
                $response = $curl->initialize()->execute();
                $info = $response->getInfo();
                $error = $response->getError();
                if (!$error) {
                    $data = [
                        "monitor_id" => $monitor->id,
                        "response" => $info['http_code'],
                        "time" => str_replace(".", ",", $info["total_time"])
                    ];
                } else {
                    $data = [
                        "monitor_id" => $monitor->id,
                        "response" => $error,
                        "time" => str_replace(".", ",", $info["total_time"])
                    ];
                }
                $curl->close();
                \App\Models\MonitorRegistro::create($data);
            }
        }
    }
}

//$ch = new cUrl("http://10.0.4.27/campus/mainsite/login/index.php");
//$ch = $ch->initialize()->execute();


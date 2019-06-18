<?php

namespace App\Controllers;


use App\Models\Monitor;
use App\Models\MonitorRegistro;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Slim\Http\Request;
use Slim\Http\Response;

class MonitorController extends Controller
{

    function store(Request $request, Response $response){
        Monitor::create($request->getParams());
        return $response->withRedirect($this->router->pathFor("admon.monitoreo"));
    }

    function all(Request $request, Response $response)
    {
        $monitores = Monitor::all();
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse->withJson($monitores, 200);
    }

    function delete(Request $request, Response $response, $args)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        if (Monitor::find($args["id"])->delete()) {
            return $newResponse->withJson(
                [
                    "codigo" => 1,
                    "message" => "Monitor eliminado correctamente"
                ],
                200);
        }

        return $newResponse->withJson(
        [
            "codigo" => 0,
            "message" => "Errror al eliminar el monitor"
        ],
        200);
    }

    function show(Request $request, Response $response, $args)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        $monitor = Monitor::find($args["id"]);
        if ($monitor instanceof Monitor) {
            return $newResponse->withJson(
                [
                    "codigo" => 1,
                    "message" => "Monitor eliminado correctamente",
                    "data" => $monitor
                ],
                200);
        }

        return $newResponse->withJson(
            [
                "codigo" => 0,
                "message" => "Errror al eliminar el monitor",
                "data" => []
            ],
            200);
    }

    function update(Request $request, Response $response, $args){
        $monitor = Monitor::find($args['id']);
        $monitor->name = $request->getParam("name");
        $monitor->url = $request->getParam("url");
        $monitor->interval = $request->getParam("interval");
        $monitor->type = $request->getParam("type");
        $monitor->save();
        return $response->withRedirect($this->router->pathFor("admon.monitoreo"));
    }

    function PausePlay(Request $request, Response $response, $args)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        $monitor = Monitor::find($args['id']);
        $monitor->status = ($monitor->status == 1) ? 0 : 1;
        if ($monitor->save()) {
            return $newResponse->withJson(
                [
                    "codigo" => 1,
                    "message" => "Acción ejecutada correctamente",
                ],
                200);
        }
        return $newResponse->withJson(
            [
                "codigo" => 1,
                "message" => "Error al ejecutar la acción",
            ],
            200);
    }
    function reset(Request $request, Response $response, $args)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        if (MonitorRegistro::where("monitor_id", $args['id'])->delete()) {
            return $newResponse->withJson(
                [
                    "codigo" => 1,
                    "message" => "Acción ejecutada correctamente",
                ],
                200);
        }
        return $newResponse->withJson(
            [
                "codigo" => 1,
                "message" => "Error al ejecutar la acción",
            ],
            200);
    }


    function detailsData(Request $request, Response $response, $args)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');

        $labels = MonitorRegistro::select(Manager::raw("DISTINCT DATE_FORMAT(created_at, '%H:%i:%m') AS labels"))->where("monitor_id", "=", $args['id'])
            ->whereBetween("created_at", [Carbon::today()->subDay(1), Carbon::today()->addDay()])->get();

        $monitores = MonitorRegistro::select("time")->where("monitor_id", "=", $args['id'])
            ->whereBetween("created_at", [Carbon::today()->subDay(1), Carbon::today()->addDay()])->get();

        return $newResponse->withJson([
            "labels" => $labels,
            "dataset" => $monitores
        ], 200);
    }

}
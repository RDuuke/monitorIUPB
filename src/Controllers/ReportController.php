<?php

namespace App\Controllers;


use App\Models\Institution;
use App\Models\Register;
use App\Models\Student;
use App\Tools\Tools;
use Illuminate\Database\Capsule\Manager;
use Slim\Http\Response;
use Slim\Http\Request;

class ReportController extends Controller
{

    function filterForMonth(Request $request, Response $response, $args)
    {
        $dataTable = Tools::getDataGeneralForMonth(1, $args['incial'] . ' 00:00:00', $args['final'] . ' 23:59:59');
        $institutions = Institution::all(["nombre", "codigo"])->sortBy("nombre");
        return $this->view->render($response, "_partials/report.twig", ["data_table" => $dataTable, "institutions" =>$institutions]);

    }

    function studentForMonth (Request $request, Response $response)
    {
        $firstDate = date("Y") . "-" . date("m") . "-" . "1";
        $lastDate = date("Y") . "-" . date("m") . "-" . "31";
        $data = Tools::studentData(1, $firstDate, $lastDate);
        $newResponse = $response->withHeader('Content-type', 'application/json');

        return $newResponse->withJson($data, 200);
    }

    function consolidated(Request $request, Response $response)
    {
        $data = Tools::studentData();

        $newResponse = $response->withHeader('Content-type', 'application/json');

        return $newResponse->withJson($data, 200);
    }

    function statsRegister(Request $request, Response $response)
    {
        $periodo = explode(".", $request->getParam("semester"));
        $registers = Tools::getRegisterForPeriod($periodo[0] . " 00:00:00", $periodo[1] . " 23:59:59");
        return $this->view->render($response, "_partials/stats_register.twig", ["registers" => $registers]);
    }
    

}
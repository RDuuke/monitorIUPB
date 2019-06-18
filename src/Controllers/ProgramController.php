<?php

namespace App\Controllers;

use Illuminate\Database\Capsule\Manager;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Program;
use App\Tools\Log;
use App\Tools\Tools;

class ProgramController extends Controller
{
    function all(Request $request, Response $response)
    {
            if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
                $programs = Program::where('codigo_institucion', $this->auth->user()->id_institucion)
                    ->where("estado", 1)->get();

            } else {
                $programs = Program::all();
            }
            
            $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse->withJson($programs, 200);
    }

    function store(Request $request, Response $response)
    {

        $data = ['message' => 0];
        $newResponse = $response->withHeader('Content-type', 'application/json');
        try {
            if (Program::checkCodigo(trim($request->getParam('codigo')))) {
                $program = Program::create(array_map('trim', $request->getParams()));
                $program->fecha = date('Y-m-d h:i:s');
                Log::i(Tools::getMessageCreaterRegisterModule(Tools::codigoProgramas, $this->auth->user()->usuario, $request->getParam('nombre')), Tools::getTypeCreatorAction());
                if ($request->isXhr()) {
                    $data = ['message' => 1, 'program' => $program];
                    return $newResponse->withJson($data, 200);
                }
                $this->flash->addMessage("creators","Programa creado correctamente");
                return $response->withRedirect($this->router->pathFor("admin.program.add"));
            }else {
                if ($request->isXhr()) {
                    return $response->withStatus(500)->write("El código ingresado ya esta en uso");
                } else {
                    $this->flash->addMessage("errors","El código ingresado ya esta en uso");
                    return $response->withRedirect($this->router->pathFor('admin.program.add'));
                }
            }
        } catch (\Exception $e) {
            Log::e(Tools::getMessageCreaterRegisterModule(Tools::codigoProgramas, $this->auth->user()->usuario, $request->getParam('nombre')), Tools::getTypeCreatorAction());
            if ($request->isXhr()) {
                return $response->withStatus(500)->write($e->getMessage());
            } else {
                $this->flash->addMessage("errors", $e->getMessage());
                return $response->withRedirect($this->router->pathFor('admin.program.add'));
            }        }
    }

    function show(Request $request, Response $response)
    {
        $router = $request->getAttribute('route');
        $instance = Program::find($router->getArguments()['id'])->toArray();
        try {
            $newResponse = $response->withHeader('Content-type', 'application/json');
            return $newResponse->withJson($instance, 200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->write($e->getMessage());
        }
    }

    function delete(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        $programs= Program::find($router->getArguments()['id']);
        try {
            if ($programs->course->count() < 1) {

                if ($programs->delete()) {
                    Log::i(Tools::getMessageDeleteRegisterModule(Tools::codigoProgramas, $this->auth->user()->usuario, $programs->nombre), Tools::getTypeDeleteAction());
                    return $response->withStatus(200)->write('1');
                }
            }
            return $response->withStatus(500)->write($programs->course->count());
        } catch(\Exception $e) {
            Log::e(Tools::getMessageDeleteRegisterModule(Tools::codigoProgramas, $this->auth->user()->usuario, $programs->nombre), Tools::getTypeDeleteAction());
            return $response->withStatus(500)->write($e->getMessage());
        }
    }

    function update(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        try {
            $program = Program::updateOrCreate(['id' => $router->getArguments()['id']], $request->getParams());
            $data_array = ["message" => 2, "program" => $program];
            Log::i(Tools::getMessageUpdateRegisterModule(Tools::codigoProgramas, $this->auth->user()->usuario, $program->nombre), Tools::getTypeUpdateAction());
            $newResponse = $response->withHeader('Content-type', 'application/json');
            return $newResponse->withJson($data_array, 200);

        } catch (\Exception $e) {
            Log::e(Tools::getMessageUpdateRegisterModule(Tools::codigoProgramas, $this->auth->user()->usuario, $program->nombre), Tools::getTypeUpdateAction());
            return $response->withStatus(500)->write('0');
        }
    }

    function search(Request $request, Response $response)
    {
        $router = $request->getAttribute('route');
        $param = $router->getArguments()['params']. "%";
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
            $programs = Program::where("nombre","LIKE", $param)->orWhere("codigo", "LIKE", $param)->where("codigo_institucion", $this->auth->user()->id_institucion)->get();
        } else {
            $programs = Program::where("nombre","LIKE", $param)->orWhere("codigo", "LIKE", $param)->get();
        }
        try {
            return $this->view->render($response, "_partials/search_program.twig", ['programs' => $programs]);
        } catch (\Exception $e) {
            return $response->withStatus(500)->write($e->getMessage());
        }
    }

    function getTotalOfRegisterForDate(Request $request, Response $response)
    {
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin() && $this->auth->user()->id_institucion != Tools::codigoSapiencia()) {
            $registers = Program::with(["course" => function ($query) use ($request)
            {
               $query->with(["registers" => function ($query) use ($request) {
                   $query->whereBetween("fecha", [ $request->getParam('fechainicial') .' 00:00:00', $request->getParam('fechafinal') . ' 23:59:59']);
               }]);
            }])->where("codigo_institucion", $this->auth->user()->id_institucion)->get();
            return $this->view->render($response, "_partials/stats_programs.twig", ["programs" => $registers]);
        }
        $registers = Program::with(["course" => function ($query) use ($request)
        {
            $query->with(["registers" => function ($query) use ($request) {
                $query->whereBetween("fecha", [ $request->getParam('fechainicial') .' 00:00:00', $request->getParam('fechafinal') . ' 23:59:59']);
            }]);
        }])->get();
        return $this->view->render($response, "_partials/stats_programs.twig", ["programs" => $registers]);



    }
}
<?php
namespace App\Controllers;

use App\Tools\Log;
use App\Tools\Tools;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Institution;


class InstitutionController extends Controller
{
    function all (Request $request, Response $response)
    {
        $institutions = Institution::all();
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse->withJson($institutions, 200);
    }

    function store(Request $request, Response $response)
    {

        $data = ['message' => 0];
        $newResponse = $response->withHeader('Content-type', 'application/json');
        try {

            if (Institution::checkCodigo(trim($request->getParam('codigo')))) {
                $institution = Institution::create(array_map('trim', $request->getParams()));
                Log::i(Tools::getMessageCreaterRegisterModule(Tools::codigoInstituciones, $this->auth->user()->usuario, $request->getParam('nombre')), Tools::getTypeCreatorAction());
                if ($request->isXhr()) {
                    $data = ['message' => 1, 'institution' => $institution];
                    return $newResponse->withJson($data, 200);
                }
                $this->flash->addMessage("creators", "Institución creada correctamente");
                return $response->withRedirect($this->router->pathFor("admin.institution.add"));
            } else {
                if ($request->isXhr()) {
                    return $response->withStatus(500)->write("El código ingresado ya esta en uso");
                } else {
                    $this->flash->addMessage("errors","El código ingresado ya esta en uso");
                    return $response->withRedirect($this->router->pathFor('admin.{.add'));
                }
            }
            return $newResponse->withJson($data, 200);
        } catch (\Exception $e) {
            Log::e(Tools::getMessageCreaterRegisterModule(Tools::codigoInstituciones, $this->auth->user()->usuario, $request->getParam('nombre')), Tools::getTypeCreatorAction());
            if ($request->isXhr()) {
                $data['text'] = $e->getMessage();
                return $newResponse->withJson($data, 200);
            } else {
                $this->flash->addMessage("errors", $e->getMessage());
                return $response->withRedirect($this->router->pathFor('admin.institution.add'));
            }
        }
    }

    function show(Request $request, Response $response)
    {
        $router = $request->getAttribute('route');
        $institution = Institution::find($router->getArguments()['id'])->toArray();
        try {
            $newResponse = $response->withHeader('Content-type', 'application/json');
            return $newResponse->withJson($institution, 200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->write('0');
        }
    }

    function delete(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        $institution = Institution::find($router->getArguments()['id']);
        try {
            if ($institution->programs->count() < 1) {

                if ($institution->delete()) {
                    Log::i(Tools::getMessageDeleteRegisterModule(Tools::codigoInstituciones, $this->auth->user()->usuario, $institution->nombre), Tools::getTypeDeleteAction());
                    return $response->withStatus(200)->write('1');
                }
            }
            return $response->withStatus(500)->write('0');
        } catch(\Exception $e) {
            Log::e(Tools::getMessageDeleteRegisterModule(Tools::codigoInstituciones, $this->auth->user()->usuario, $institution->nombre), Tools::getTypeDeleteAction());
            return $response->withStatus(500)->write('0');
        }
    }

    function update(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        $institution = Institution::updateOrCreate(['id' => $router->getArguments()['id']], $request->getParams());
        try {

            $data_array = ["message" => 2, "institution" => $institution];
            Log::i(Tools::getMessageUpdateRegisterModule(Tools::codigoInstituciones, $this->auth->user()->usuario, $institution->nombre), Tools::getTypeUpdateAction());
            $newResponse = $response->withHeader('Content-type', 'application/json');
            return $newResponse->withJson($data_array, 200);

        } catch (\Exception $e) {
            Log::e(Tools::getMessageUpdateRegisterModule(Tools::codigoInstituciones, $this->auth->user()->usuario, $institution->nombre), Tools::getTypeUpdateAction());
            return $response->withStatus(500)->write('0');
        }
    }

    function getTotalOfRegisterForDate(Request $request, Response $response)
    {
        $registers = Institution::with(["registers" => function($query) use ($request) {
            $query->whereBetween("fecha", [ $request->getParam('fechainicial') .' 00:00:00', $request->getParam('fechafinal') . ' 23:59:59']);
        }])->get();

        return $this->view->render($response, "_partials/stats_institutions.twig", ["institutions" => $registers]);
    }
}
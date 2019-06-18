<?php
namespace App\Controllers;

use App\Models\FirstSingIn;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;
use App\Models\Student;
use App\Tools\Log;
use App\Tools\Tools;


class UserController extends Controller
{
    public function all(Request $request, Response $response)
    {
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
            $users = User::where('id_institucion', $this->auth->user()->id_institucion)->get();
        } else {
            $users = User::all();
        }
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse->withJson($users, 200);
    }

    public function store(Request $request, Response $response)
    {

        $data = ['message' => 0];
        $newResponse = $response->withHeader('Content-type', 'application/json');
        try {
            $data = $request->getParams();
            $data['clave'] = password_hash($data['documento'], PASSWORD_DEFAULT);
            $user = User::create(array_map('trim', $data));
            FirstSingIn::create(['usuario' => $user->usuario, "singin" => 0]);
            Log::i(Tools::getMessageCreaterRegisterModule(Tools::codigoUsuarioPlataforma, $this->auth->user()->usuario, $request->getParam('usuario')), Tools::getTypeCreatorAction());
            if ($request->isXhr()) {
                $data = ['message' => 1, 'user' => $user];
                return $newResponse->withJson($data, 200);
            }
            $this->flash->addMessage("creators", "Usuario @monitor creado correctamente");
            return $response->withRedirect($this->router->pathFor('admin.user.add'));
        } catch ( \PDOException $e ) {
            if ($request->isXhr()) {
                $data["message"] = $e->getMessage();
                Log::e(Tools::getMessageCreaterRegisterModule(Tools::codigoUsuarioPlataforma, $this->auth->user()->usuario, $request->getParam('usuario')), Tools::getTypeCreatorAction());
                return $newResponse->withJson($data, 200);
            }

            $this->flash->addMessage("errors", str_replace(":correo",  $request->getParam('usuario'), Tools::$CodePDO[$e->getCode()]));
            return $response->withRedirect($this->router->pathFor('admin.user.add'));

        }
    }

    public function show(Request $request, Response $response)
    {
        $router = $request->getAttribute('route');
        $user = User::find($router->getArguments()['id'])->toArray();
        try {
            $newResponse = $response->withHeader('Content-type', 'application/json');
            return $newResponse->withJson($user, 200);
        } catch(\Exception $e) {
            return $response->withStatus(500)->write('0');
        }
    }

    function update(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        $student = User::updateOrCreate(['id' => $router->getArguments()['id']],$request->getParams());
        try{
            if ($student != false) {
                $data_array = ["message" => 2, "user" => $student];
                Log::i(Tools::getMessageUpdateRegisterModule(Tools::codigoUsuarioPlataforma, $this->auth->user()->usuario, $student->usuario), Tools::getTypeUpdateAction());
                $newResponse = $response->withHeader('Content-type', 'application/json');
                return $newResponse->withJson($data_array, 200);
            }
        }catch(\Exception $e) {
            Log::e(Tools::getMessageUpdateRegisterModule(Tools::codigoUsuarioPlataforma, $this->auth->user()->usuario, $student->usuario), Tools::getTypeUpdateAction());
            return $response->withStatus(500)->write('0');
        }
    }


    function delete(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        $usuario= User::find($router->getArguments()['id']);
        try {
            if ($usuario->delete()) {
                Log::i(Tools::getMessageDeleteRegisterModule(Tools::codigoUsuarioPlataforma, $this->auth->user()->usuario, $usuario->usuario), Tools::getTypeDeleteAction());
                return $response->withStatus(200)->write('1');
            }
        } catch(\Exception $e) {
            Log::i(Tools::getMessageDeleteRegisterModule(Tools::codigoUsuarioPlataforma, $this->auth->user()->usuario, $usuario->usuario), Tools::getTypeDeleteAction());
            return $response->withStatus(500)->write('0');
        }
    }

    function reset(Request $request, Response $response)
    {
        $router = $request->getAttribute('route');
        $usuario = User::find($router->getArguments()['id']);
        $usuario->clave =  password_hash($usuario->documento, PASSWORD_DEFAULT);
        if ($usuario->save()) {
            FirstSingIn::where('usuario', $usuario->usuario)->update(['singin' => 0]);
            $newResponse = $response->withHeader('Content-type', 'application/json');
            return $newResponse->withJson(['message' => 1], 200);
        }
        return $response->withStatus(500)->write('0');
    }
}
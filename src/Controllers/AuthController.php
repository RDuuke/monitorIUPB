<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;


class AuthController extends Controller
{

    public function signin(Request $request,Response $response)
    {
        $auth = $this->auth->attempt(
            $request->getParam('usuario'),
            $request->getParam('clave')
        );

        if (! $auth) {
            $this->flash->addMessage("errors", "Usuario o contraseña incorrectos");
            return $response->withRedirect($this->router->pathFor('home'));
        }
        if (! $this->auth->firstSingIn($request->getParam('usuario'))) {
            return $response->withRedirect($this->router->pathFor('firstsingin'));
        }
        return $response->withRedirect($this->router->pathFor('admin.home'));
    }
    public function signout(Request $request,Response $response)
    {
        $this->auth->logout();
        return $response->withRedirect($this->router->pathFor('home'));
    }
}
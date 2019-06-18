<?php

namespace App\Controllers;

use App\Models\Permission;
use App\Models\Register;
use App\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Student;


class ApiController extends Controller
{

    function alluser(Request $request, Response $response)
    {
        $user = Student::all()->toJson();

        $newResponse = $response->withHeader('Content-type', 'application/json');

        return $newResponse->withJson(["codigo" => 1, "message" => "All student", "students" => $user], 200);

    }

    function user(Request $request, Response $response)
    {
        $user = (object) User::where('usuario', '=', 'juuanduuke@gmail.com')->first()->toArray();
        echo '<pre>';
        foreach(Permission::where('user_id', '=', $user->id)->get()->toArray() as $k => $v){
            foreach ($v as $k2 => $v2) {
               $_SESSION['p']['modulo'][$v2] = $v;
               break;
            }

        }
        print_r($_SESSION['p']);
        return false;
    }
}
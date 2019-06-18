<?php
namespace App\Auth;
use App\Models\FirstSingIn;
use App\Models\Permission;
use App\Models\User;
use App\Tools\Tools;

class Auth
{
    public function attempt($email, $password)
    {
        $user = User::where('usuario', '=', $email)->first();
        if (!$user) {
            return false;
        }
        $user = (object) $user->toArray();
        if (password_verify($password, $user->clave)) {
            $_SESSION['user'] = $user;
            foreach(Permission::where('user_id', '=', $user->id)->get()->toArray() as $k => $v){
                foreach ($v as $k2 => $v2) {
                    $_SESSION['permission']['modules'][$v2] = $v;
                    break;
                }

            }
            return true;
        }
        return false;
    }
    public function check()
    {
        return isset($_SESSION['user']);
    }
    public function user()
    {
        return $_SESSION['user'];
    }
    public function logout()
    {
        session_destroy();
    }


    public function getInstitution()
    {
        if ( $this->user()->institution->codigo == Tools::codigoMedellin()) {
              return "%";
        }
        return $this->user()->institution->nombre;
    }

    public function getCodigoInstitution()
    {
        return $this->user()->id_institucion;
    }

    public function firstSingIn(String $user) : bool
    {
        $user = FirstSingIn::find($user);
        if ( $user->singin == 0 ) {
            return false;
        }
        return true;
    }

    public function getPermissionForModule($module)
    {
        return $_SESSION['permission']['modules'][$module]['permiso'];
    }
}
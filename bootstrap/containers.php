<?php

use App\Models\Program;
use App\Tools\Tools;
use Illuminate\Database\Capsule\Manager;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use App\Controllers\AppController;
use App\Controllers\AuthController;
use App\Controllers\StudentController;
use App\Controllers\RegisterController;
use App\Controllers\UserController;
use App\Controllers\InstanceController;
use App\Controllers\ProgramController;
use App\Controllers\InstitutionController;
use App\Controllers\CourseController;
use App\Controllers\ApiController;
use App\Auth\Auth;
use Slim\Flash\Messages;


$container = $app->getContainer();
$capsule = new Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->addConnection($container['settings']['db_moodle'], "db_moodle");
/*
$capsule->addConnection($container['settings']['db_postgrado'], "db_postgrado");
$capsule->addConnection($container['settings']['db_itm'], "db_itm");
$capsule->addConnection($container['settings']['db_colmayor'], "db_colmayor");
$capsule->addConnection($container['settings']['db_pascual'], "db_pascual");
*/
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};
$container['flash'] = function ($container) {
    return new Messages;
};
$container['auth'] = function ($container) {
    return new Auth($container);
};
$container['tmp'] = function($container) {
    return dirname(__DIR__) . DS . "resource" . DS . "tmp" . DS;
};
$container['files'] = function($container) {
    return dirname(__DIR__) . DS . "resource" . DS . "files" . DS;
};
$container['view'] = function ($container) {
    $view = new Twig(__DIR__ . "/../views",[
        'cache' => false,
    ]);
    $view->addExtension(new TwigExtension(
        $container->router,
        $container->request->getUri()
    ));
    $view->addExtension(new Knlv\Slim\Views\TwigMessages(
        new Slim\Flash\Messages()
    ));
    $view->getEnvironment()->addGlobal('modulo_plataforma', Tools::codigoUsuarioPlataforma);
    $view->getEnvironment()->addGlobal('modulo_campus', Tools::codigoUsuarioCampus);
    $view->getEnvironment()->addGlobal('modulo_instancias', Tools::codigoInstancias);
    $view->getEnvironment()->addGlobal('modulo_instituciones', Tools::codigoInstituciones);
    $view->getEnvironment()->addGlobal('modulo_programas', Tools::codigoProgramas);
    $view->getEnvironment()->addGlobal('modulo_cursos', Tools::codigoCursos);
    $view->getEnvironment()->addGlobal('modulo_matriculas', Tools::codigoMatriculas);
    $view->getEnvironment()->addGlobal('modulo_busqueda', Tools::codigoBusqueda);
    $view->getEnvironment()->addGlobal('modulo_estadistica', Tools::codigoEstadistica);
    $view->getEnvironment()->addGlobal('modulo_reporte', Tools::codigoReporte);
    $view->getEnvironment()->addGlobal('codigo_arroba_medellin', Tools::codigoMedellin());
    $view->getEnvironment()->addGlobal('codigo_sapiencia', Tools::codigoSapiencia());
    $view->getEnvironment()->addGlobal('name_iupb', Tools::getInstitutionForCodigo(Tools::codigoPascualBravo()));
    $view->getEnvironment()->addGlobal('codigo_iupb', Tools::codigoPascualBravo());
    $view->getEnvironment()->addGlobal('instance_iupb', Tools::IUPBinstance);
    $view->getEnvironment()->addGlobal('lectura', Tools::Lectura);
    $view->getEnvironment()->addGlobal('lectura_escritura', Tools::LecturaEscritura);
    $view->getEnvironment()->addGlobal('session', $_SESSION);

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    $view->getEnvironment()->addGlobal('tools', Tools::$Modules);

    $view->getEnvironment()->addGlobal('base_url', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']. "");


    $function = new Twig_SimpleFunction('getPermission', function ($module, $user) {
        $permission = \App\Models\Permission::where("modulo_id", $module)->where("user_id", $user)->first();
        if ($permission->permiso != null) {
            return $permission->permiso;
        }
        return 0;
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('getInstance', function ($codigo) {
        $i = substr($codigo,0, 1);
        $nombre = \App\Models\Instance::where("codigo", $i)->first();
        return $nombre->nombre;
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('getInstitution', function ($codigo, $type = 0) {
        if ($type == 0) {
            $nombre = \App\Models\Institution::where("codigo", $codigo)->first();
            return $nombre->nombre;
        } else if ($type == 1) {
            $program = \App\Models\Program::where("codigo", $codigo)->first();
            return $program->codigo_institucion;
        }
        $curso = \App\Models\Course::where("codigo", $codigo)->first();
        return $curso->programs->codigo_institucion;
    });


    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('getPrograma', function ($codigo) {
        $program = Program::where("codigo", $codigo)->first();
        return $program->nombre;
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('getMes', function ($mes) {
        $meses = [
            '01' => "enero", '02' => "febrero", '03', "marzo", '04' => "abril", '05' => "mayo", '06',
            "junio", '07' => "julio", "08" => "agosto", "09" => "septiembre", '10' => "octubre", '11' => "noviembre", '12' => "diciembre"
        ];

        return $meses[$mes];
    });

    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('getP', function ($modulo) {

        return Auth::getPermissionForModule($modulo);
    });
    $view->getEnvironment()->addFunction($function);


    $function = new Twig_SimpleFunction('getCodigoInstance', function ($codigo) {
        return \App\Models\Instance::where("institucion_id", $codigo)->first()->codigo;
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('getNameInstance', function ($codigo) {
        return \App\Models\Instance::where("institucion_id", $codigo)->first()->nombre;
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('limitDate', function ($fecha) {
        $fechaActual = strtotime(date("Y-m-d"));
        $f = strtotime($fecha);
        if ($fechaActual > $f) {
            return 1;
        }
        return 0;
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('countStudent', function ($type, $codigo) {
        $total = 0;
        switch ($type) {
            case 1 :
                $total = \App\Models\Register::where("institucion_id", $codigo)->count();
                break;
            case 2 :
                $total = \App\Models\Register::where("curso", "like", $codigo . "%")->count();
                break;
        }
        return $total;
    });
    $view->getEnvironment()->addFunction($function);


    $function = new Twig_simpleFunction("getLastEntry", function ($codigo, $username){
        $sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(la.timeaccess),'%d %b %Y') AS ultimoCur  FROM mdl_user u, mdl_role_assignments ra, mdl_context c, mdl_course co,
                                                        mdl_user_lastaccess la 
                                                        WHERE u.id=ra.userid AND ra.contextid = c.id AND c.instanceid=co.id AND u.id=la.userid AND co.id=la.courseid AND u.username='$username'
                                                        AND co.idnumber=$codigo";

        if (substr($codigo, 0, 1) == 1) {
            $data = Manager::connection("db_moodle")->select($sql);
        } else {
            return "no registro";
        }
        return $data[0]->ultimoCur != "" ? $data[0]->ultimoCur : 'Nunca';
    });

    $view->getEnvironment()->addFunction($function);

    return $view;
};
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container['view']
            ->render($container['response'], "errors/404.twig");
    };
};
/*
$container['phpErrorHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container['view']
            ->render($container['response'], "errors/500.twig");
    };
};
*/
$container['AppController'] = function ($container)
{
    return new AppController($container);
};

$container['AuthController'] = function ($container)
{
    return new AuthController($container);
};

$container['StudentController'] = function($container)
{
    return new StudentController($container);
};

$container['RegisterController'] = function($container)
{
    return new RegisterController($container);
};

$container['UserController'] = function($container)
{
    return new UserController($container);
};

$container['InstanceController'] = function($container)
{
    return new InstanceController($container);
};

$container['ProgramController'] = function($container)
{
    return new ProgramController($container);
};

$container['InstitutionController'] = function($container)
{
    return new InstitutionController($container);
};

$container['CourseController'] = function($container)
{
    return new CourseController($container);
};
$container['ApiController'] = function($container)
{
    return new ApiController($container);
};
$container['ReportController'] = function($container)
{
    return new \App\Controllers\ReportController($container);
};
$container["MonitorController"] = function ($container)
{
    return new \App\Controllers\MonitorController($container);
};

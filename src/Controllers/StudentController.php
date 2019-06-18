<?php
namespace App\Controllers;

use App\Models\Institution;
use App\Models\StudentArchive;
use App\Models\User;
use App\Tools\Log;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Student;
use App\Tools\Tools;
use App\Models\Permission;
use ForceUTF8\Encoding;



class StudentController extends Controller
{
    protected $errors = [];
    protected $creators = [];
    protected $alerts = [];


    function store(Request $request, Response $response) : Response
    {
        try{
            $student = Student::create(array_map('trim', $request->getParams()));
            $student->clave = trim($request->getParam('documento'));
            $student->correo = trim($request->getParam('usuario'));
            if ($student->save()) {
                $student->fecha = date('Y-m-d h:i:s');
                Log::i(Tools::getMessageCreaterRegisterModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario, $request->getParam('usuario')), Tools::getTypeCreatorAction());
                if($request->isXhr()) {

                    $data_array = ["message" => 1, "user" => $student];
                    $newResponse = $response->withHeader('Content-type', 'application/json');
                    return $newResponse->withJson($data_array, 200);
                }
                $this->flash->addMessage("creators", "Usuario campus creado correctamente");
                return $response->withRedirect($this->router->pathFor('admin.student.add'));
            }
        }catch(\PDOException $e) {

            Log::e(Tools::getMessageCreaterRegisterModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario, $request->getParam('usuario')) . " INFO:" . $e->getMessage(), Tools::getTypeCreatorAction());
            if ($request->isXhr()) {
                return $response->withStatus(500)->write(str_replace(":correo",  $request->getParam('usuario'), Tools::$CodePDO[$e->getCode()]));
            } else {
                $this->flash->addMessage("errors", str_replace(":correo",  $request->getParam('usuario'), Tools::$CodePDO[$e->getCode()]));
                return $response->withRedirect($this->router->pathFor('admin.student.add'));
            }
        }
    }

    function delete(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        $student= Student::find($router->getArguments()['id']);

        try {
            if ($student->registers->count() == 0) {
                if ($student->delete()) {
                    Log::i(Tools::getMessageDeleteRegisterModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario, $student->usuario), Tools::getTypeDeleteAction());
                    return $response->withStatus(200)->write('1');
                }
                return $response->withStatus(500)->write('Error en la operación');
            }
            return $response->withStatus(500)->write('El usuario tiene matriculas activas');
        } catch(\Exception $e) {
            Log::e(Tools::getMessageDeleteRegisterModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario, $student->usuario) . " INFO:" . $e->getMessage(), Tools::getTypeDeleteAction());
            return $response->withStatus(500)->write($e->getMessage());
        }
    }

    function show(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        $student= Student::find($router->getArguments()['id'])->toArray();
        try {
            $newResponse = $response->withHeader('Content-type', 'application/json');
            return $newResponse->withJson($student, 200);
        } catch(\Exception $e) {
            return $response->withStatus(500)->write('0');
        }
    }

    function update(Request $request, Response $response) : Response
    {
        $router = $request->getAttribute('route');
        $student = Student::updateOrCreate(['id' => $router->getArguments()['id']],array_map('trim',$request->getParams()));
        try{
            if ($student != false) {
                $data_array = ["message" => 2, "user" => $student];
                $newResponse = $response->withHeader('Content-type', 'application/json');
                Log::i(Tools::getMessageUpdateRegisterModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario, $student->usuario), Tools::getTypeUpdateAction());
                return $newResponse->withJson($data_array, 200);
            }
        }catch(\Exception $e) {
            Log::e(Tools::getMessageUpdateRegisterModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario, $student->usuario) . " INFO:" . $e->getMessage(), Tools::getTypeUpdateAction());
            return $response->withStatus(500)->write('0');
        }
    }
    function all(Request $request, Response $response) : Response
    {
            if($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
                $students = Student::where('institucion_id', $this->auth->user()->id_institucion)->get();
            } else {
                $students = Student::all();
            }
            $newResponse = $response->withHeader('Content-type', 'application/json');
            return $newResponse->withJson($students, 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    function upload(Request $request, Response $response)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $archive = $uploadedFiles['archive'];
        if ($archive->getError() == UPLOAD_ERR_OK) {
            $filename = Tools::moveUploadedFile($archive, $this->tmp);
            if ($filename != false) {
                try {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($this->tmp . DS . $filename);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = Tools::getHighestDataRow($worksheet);
                    for ($row=2; $row <= $highestRow; $row++) {
                        $data = [
                            "usuario" => trim($worksheet->getCell('A'. $row)->getvalue()),
                            "clave" => trim($worksheet->getCell('E'. $row)->getvalue()),
                            "nombres" => trim($worksheet->getCell('C'. $row)->getvalue()),
                            "apellidos" => trim($worksheet->getCell('D'. $row)->getvalue()),
                            "correo" => trim($worksheet->getCell('A'. $row)->getvalue()),
                            "documento" => trim($worksheet->getCell('E'. $row)->getvalue()),
                            "institucion" => trim($worksheet->getCell("F". $row)->getValue()),
                            "genero" => trim($worksheet->getCell('G'. $row)->getvalue()),
                            "ciudad" => trim($worksheet->getCell('H'. $row)->getvalue()),
                            "departamento" => trim($worksheet->getCell('I'. $row)->getvalue()),
                            "pais" => trim($worksheet->getCell('J'. $row)->getvalue()),
                            "telefono" => trim($worksheet->getCell('K'. $row)->getvalue()),
                            "celular" => trim($worksheet->getCell('L'. $row)->getvalue()),
                            "direccion" => trim($worksheet->getCell('M'. $row)->getvalue())
                        ];

                        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
                            $data["institucion"] = !empty($data["institucion"]) ? $data["institucion"] : Institution::getNameInstitutionForCodigo($this->auth->user()->id_institucion);
                            $data["institucion_id"] = $this->auth->user()->id_institucion;
                        } else {
                            $data["institucion"] = !empty($data["institucion"]) ? $data["institucion"] :  Institution::getNameInstitutionForCodigo($request->getParam('codigo_institucion'));
                            $data["institucion_id"] = $request->getParam('codigo_institucion');
                        }
                        $data = array_map("ucwords", array_map("strtolower",$data));
                        $data['usuario'] = strtolower($data['usuario']);
                        $data['correo'] = strtolower($data['correo']);
                        $data['usuario'] = preg_replace('/[^(\x20-\x7F)]*/', "", $data['usuario']);
                        $data['correo'] = preg_replace('/[^(\x20-\x7F)]*/', "", $data['correo']);
                        if (!filter_var(trim($data['usuario']), FILTER_VALIDATE_EMAIL)) {
                            $data['message'] = Tools::getMessageUser(5);
                            $data['codigo'] = Tools::getCodigoUser(5);
                            array_push($this->errors, $data);
                            unset($data);
                            continue;
                        }
                        if (empty($data["documento"])) {
                            $data['message'] = Tools::getMessageUser(6);
                            $data['codigo'] = Tools::getCodigoUser(6);
                            array_push($this->errors, $data);
                            unset($data);
                            continue;
                        }
                        $student_document = Student::where('documento', $data['documento'])->get();

                        if ($student_document->count() == 1){
                            $filter = $student_document->where('usuario', $data['usuario']);
                            if ($filter->count() == 0){
                                $data['message'] = str_replace(':usuario', $student_document[0]->usuario, Tools::getMessageUser(4));
                                $data['codigo'] = Tools::getCodigoUser(4);
                                array_push($this->alerts, $data);
                                unset($data);
                                continue;
                            }
                        }
                        $student = Student::where('usuario', '=', $data['usuario'])->get();
                        if ($student->count() == 0) {
                            $data['message'] = Tools::getMessageUser(0);
                                $data['codigo'] = Tools::getCodigoUser(0);
                                array_push($this->creators, $data);
                                unset($data);
                                continue;
                        } else {
                            $filter = $student->where('documento',$data['documento']);

                            if($filter->count() == 0) {
                                $data['message'] = str_replace(":documento", $student[0]->documento, Tools::getMessageUser(1));
                                $data['codigo'] = Tools::getCodigoUser(1);
                                array_push($this->alerts, $data);
                            }else {
                                $data['message'] = Tools::getMessageUser(3);
                                $data['codigo'] = Tools::getCodigoUser(3);
                                array_push($this->errors, $data);
                            }
                            unset($data);
                            continue;
                        }
                    }
                    $responseData = ['message' => 1, 'creators' => $this->creators, 'errors' => $this->errors, 'alerts' => $this->alerts,'totalr' => ($highestRow), 'totalc' => count($this->creators), 'totale' => count($this->errors), 'totala' => count($this->alerts)];
                    $newResponse = $response->withHeader('Content-type', 'application/json');
                    Log::i(Tools::getMessageImportModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario), Tools::getTypeAction(5));
                    return $newResponse->withJson($responseData, 200);
                } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    Log::e(Tools::getMessageImportModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario) . " INFO:" . $e->getMessage(), Tools::getTypeAction(5));
                    die('Error loading file: '.$e->getMessage());
                }

            }
        }
        echo $archive->getError();
        return false;
    }

    function checkEmailStudents(Request $request, Response $response)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        if(Student::where('usuario', '=', $request->getParam('usuario'))->count() > 0 ) {
            return $newResponse->withJson(['message' => 1], 200);
        }
        return $newResponse->withJson(['message' => 0], 200);
    }

    function getDataForEmailStudents(Request $request, Response $response)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        $data = Student::where('usuario', '=', $request->getParam('usuario'))->get()->first();
        if($data != false) {
            return $newResponse->withJson($data, 200);
        }
        return $newResponse->withJson(['message' => 0, 'student' => null], 200);
    }

    function search(Request $request, Response $response)
    {

        $router = $request->getAttribute('route');
        $param = $router->getArguments()['params']. "%";
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
            $students = Student::where("usuario", "LIKE", $param)
                ->where("institucion_id", $this->auth->user()->id_institucion)
                ->orWhere("documento", "LIKE", $param)
                ->get()->toArray();
        } else {
            $students = Student::where("usuario","LIKE", $param)
                ->orWhere("documento", "LIKE", $param)
                ->get()->toArray();
        }
        try {
            return $this->view->render($response, "_partials/search_student.twig", ["students" => $students]);
        } catch (\Exception $e) {
            return $response->withStatus(500)->write('0');
        }
    }

    function proccess(Request $request, Response $response)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        try {
            $dataOK = $request->getParam('data');
            for($i = 0; $i < count($dataOK); $i++){

                if (Student::updateOrCreate(['usuario' => $dataOK[$i]['usuario']], $dataOK[$i]) instanceof Student) {
                    $dataOK[$i]["codigo"] = "C0";
                    $dataOK[$i]["message"] = "Creado correctamente";
                    unset($dataOK[$i]["institucion_id"]);
                    array_push($this->creators, $dataOK[$i]);
                } else {
                    $dataOK[$i]["codigo"] = "E0";
                    $dataOK[$i]["message"] = "Error al crear el usuario";
                    unset($dataOK[$i]["institucion_id"]);
                    array_push($this->errors, $dataOK[$i]);
                }
            }

            return $newResponse->withJson([
                "status" => 0,
                "creators" => [
                    "data" => $this->creators,
                    "total" => count($this->creators)
                ],
                "errors" => [
                    "data" => $this->errors,
                    "total" => count($this->errors)
                ],
                "message" => "Se completo el proceso de creación de usuarios.",

            ], 200);
        } catch (\ErrorException $e ) {
            return $newResponse->withJson([
                "status" => 1,
                "creators" => [
                    "data" => $this->creators,
                    "total" => count($this->creators)
                ],
                "errors" => [
                    "data" => $this->errors,
                    "total" => count($this->errors)
                ],
                "message" => "Se interrumpio el proceso de creación de usuarios.",
                "messageRaw" => $e->getMessage()

            ], 200);
        }
    }

    function permissionAll(Request $request, Response $response){
        $router = $request->getAttribute('route');
        $permissions = Permission::where('user_id',"=",$router->getArguments()['id'])->get();
        $usuario = User::find($router->getArguments()['id'])->first();
        return $this->view->render($response, "_partials/permission.twig", ['permissions' => $permissions, "usuario" => $usuario]);
    }
    function permission(Request $request, Response $response)
    {
        $data = [];

        $dataOK = $request->getParams();
        $router = $request->getAttribute('route');
        $id = $router->getArguments()['id'];
        $writing  = isset($dataOK['writing']) ? $dataOK['writing'] : 0;
        $reading  = isset($dataOK['reading']) ? $dataOK['reading'] : 0;
        $data = ['permiso' => ($writing + $reading), 'modulo_id' => $dataOK['modules'], 'user_id' => $id];
        $permission = Permission::updateOrCreate(['modulo_id' => $dataOK['modules'], 'user_id' => $id], $data);
        Tools::refreshPermission($id);
        $newResponse = $response->withHeader('Content-type', 'application/json');
        if ($permission != false){
            $message = ["status" => 1, "module" => $permission->module->nombre, "writing" => $writing, "reading" => $reading, "id" => $permission->id];
            return $newResponse->withJson($message, 200);
        }
        return "no";
    }

    function remove(Request $request, Response $response)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        $router = $request->getAttribute('route');
        $permission = Permission::find($router->getArguments()['id']);
        Tools::refreshPermission($permission->user_id);
        if ($permission->delete()){
            $message = ["status" => 1];
            return $newResponse->withJson($message, 200);
        }

    }

    function reset(Request $request, Response $response)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        $router = $request->getAttribute('route');
        try
        {
            $s = Student::find($router->getArguments('id'));
            $s[0]->clave = $s[0]->documento;
            $s[0]->save();
            return $newResponse->withJson(["response" => 1, "message" => "Contraseña restablecida"], 200);
        } catch ( \ErrorException $e) {
            return $newResponse->withJson(['response' => 0, "message" => $e->getMessage()], 500);
        }
    }

    function archive(Request $request, Response $response)
    {
        $newResponse = $response->withHeader('Content-type', 'application/json');
        $router = $request->getAttribute('route');
        try {
            $s = Student::find($router->getArguments('id'));
            if ( $s[0]->registers->count() == 0){
                $data = $s[0]->toArray();
                $s[0]->delete();
                StudentArchive::create($data);
                return $newResponse->withJson(["response" => 1, "message" => "Usuario archivado"], 200);
            }
            return $newResponse->withJson(['response' => 2, "message" => "El usuario tiene matrícula  activas"], 200);
        } catch (\Exception $e) {
            return $newResponse->withJson(['response' => 0, "message" => $e->getMessage()], 500);
        }
    }

    function uploadArchive(Request $request, Response $response)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $archive = $uploadedFiles['archive'];
        $newResponse = $response->withHeader('Content-type', 'application/json');
        if ($archive->getError() == UPLOAD_ERR_OK) {
            $filename = Tools::moveUploadedFile($archive, $this->tmp);
            if ($filename != false) {
                try {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($this->tmp . DS . $filename);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = Tools::getHighestDataRow($worksheet);
                    for ($row=2; $row <= $highestRow; $row++) {
                        $data = [
                            "usuario" => trim($worksheet->getCell('A'. $row)->getvalue())
                        ];
                        if (!filter_var(trim($data['usuario']), FILTER_VALIDATE_EMAIL)) {
                            $data['message'] = "E01";
                            $data['codigo'] = "El usuario no tiene una estructra de un correo electronico";
                            array_push($this->errors, $data);
                            unset($data);
                            continue;
                        };

                        $student = Student::where("usuario", $data['usuario'])->first();

                        if ($student instanceof Student) {
                            $data = $student->toArray();
                            if ($student->registers->count() !== 0) {
                                $data['codigo'] = "A01";
                                $data['message'] = "El usuario tiene matriculas activas";
                                array_push($this->alerts, $data);
                                unset($data);
                                continue;
                            }
                            if ($student->registershistoricos->count() !== 0) {
                                $data['codigo'] = "E02";
                                $data['message'] = "El usuario tiene matriculas archivadas";
                                array_push($this->alerts, $data);
                                unset($data);
                                continue;
                            };
                            $data['codigo'] = "C01";
                            $data['message'] = "El usuario puede ser archivado correctamente";
                            array_push($this->creators, $data);
                            unset($data);
                            continue;
                        } else {
                            $data['codigo'] = "E03";
                            $data['message'] = "El usuario no existe";
                            array_push($this->errors, $data);
                            unset($data);
                            continue;
                        }

                    }
                    $responseData = ['message' => 1, 'creators' => $this->creators, 'errors' => $this->errors, 'alerts' => $this->alerts,'totalr' => ($highestRow-1), 'totalc' => count($this->creators), 'totale' => count($this->errors), 'totala' => count($this->alerts)];
                    Log::i(Tools::getMessageImportModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario), Tools::getTypeAction(5));
                    return $newResponse->withJson($responseData, 200);
                } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    Log::e(Tools::getMessageImportModule(Tools::codigoUsuarioCampus, $this->auth->user()->usuario), Tools::getTypeAction(5));
                    return $newResponse->withJson(['message' => 0, "info" => $e->getMessage()], 500);
                }
            }
        }

    }

    function proccess_archive(Request $request, Response $response)
    {
        $dataOK = $request->getParam('data');
        $data = [
            "status" => 1,
            "creators" => 0,
            "errors" => 0
        ];
        $c = 0;
        $e = 0;

        for($i = 0; $i < count($dataOK); $i++){
            $student = Student::where("usuario", $dataOK[$i]['usuario'])->delete();
            if($student) {
                if (StudentArchive::create($dataOK[$i]) instanceof StudentArchive) {
                    $c++;
                    continue;
                }
            }
            $e++;
        }
        $data["creators"] = $c;
        $data["errors"] = $e;
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse->withJson($data, 200);
    }



}
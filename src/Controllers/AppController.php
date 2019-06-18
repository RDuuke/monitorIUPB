<?php
namespace App\Controllers;

use App\Models\FirstSingIn;
use App\Models\Monitor;
use App\Models\User;
use App\Tools\Log;
use App\Tools\Tools;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Collection;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Student;
use App\Models\Institution;
use App\Models\Rol;
use App\Models\Instance;
use App\Models\Program;
use App\Models\Course;
use App\Models\Register;
use App\Models\RegisterArchive;
use App\Models\Module;
use Slim\Http\Stream;

class AppController extends Controller
{

    public function index(Request $request,Response $response)
    {
        return $this->view->render($response, "index.twig");
    }

    public function home(Request $request,Response $response)
    {

        return $this->view->render($response, "home.twig");
    }

    public function students(Request $request,Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoUsuarioCampus)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoUsuarioCampus, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        $institutions =Institution::all();
        Log::i(Tools::getEnterModuleMessage(Tools::codigoUsuarioCampus, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "student.twig", ['module_name' => Tools::$Modules[Tools::codigoUsuarioCampus], "menu_active" => Tools::$MenuActive[0], "instituciones" => $institutions]);
    }

    public function users(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoUsuarioPlataforma)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoUsuarioPlataforma, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        //$rols = Rol::all();
        $institutions =Institution::all();
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
            $modules = Module::public();
        } else {
            $modules = Module::all();
        }
        Log::i(Tools::getEnterModuleMessage(Tools::codigoUsuarioPlataforma, $this->auth->user()->usuario), Tools::getTypeAction(3));

        return $this->view->render($response, "user.twig", ["instituciones" => $institutions, "modules" => $modules, 'module_name' => Tools::$Modules[Tools::codigoUsuarioPlataforma], "menu_active" => Tools::$MenuActive[0]]);
    }

    public function firstIn(Request $request, Response $response)
    {
        return $this->view->render($response, "change_password.twig");
    }
    public function registers(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoMatriculas)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoMatriculas, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        Log::i(Tools::getEnterModuleMessage(Tools::codigoMatriculas, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "register.twig", ['module_name' => Tools::$Modules[Tools::codigoMatriculas], "menu_active" => Tools::$MenuActive[0], "institutions" => Institution::all(), "roles" => Tools::$Roles]);
    }

    public function instance(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoInstancias)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoInstancias, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        Log::i(Tools::getEnterModuleMessage(Tools::codigoInstancias, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "instance.twig", ['module_name' => Tools::$Modules[Tools::codigoInstancias], "menu_active" => Tools::$MenuActive[0]]);
    }

    public function program(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoProgramas)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoProgramas, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
            $instances = Manager::table('instancia')->whereIn('codigo', [1, 2])->orWhere('institucion_id', $this->auth->user()->id_institucion)->get();
        }else {
            $instances = Instance::all();
        }
        $institutions =Institution::all();
        Log::i(Tools::getEnterModuleMessage(Tools::codigoProgramas, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "program.twig", ["instituciones" => $institutions, 'instances' => $instances, 'module_name' => Tools::$Modules[Tools::codigoProgramas], "menu_active" => Tools::$MenuActive[0]]);
    }

    public function institution(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoInstituciones)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoInstituciones, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        Log::i(Tools::getEnterModuleMessage(Tools::codigoInstituciones, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "institution.twig", ['module_name' => Tools::$Modules[Tools::codigoInstituciones], "menu_active" => Tools::$MenuActive[0]]);
    }

    public function courses(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoCursos)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoCursos, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
            $programs = Program::where('codigo_institucion', $this->auth->user()->id_institucion)
                ->where("estado", 1)->get();
        } else {
            $programs = Program::all();
        }
        $institutions = Institution::all();
        Log::i(Tools::getEnterModuleMessage(Tools::codigoCursos, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "courses.twig", ["programs" => $programs, "module_name" => Tools::$Modules[Tools::codigoCursos], "menu_active" => Tools::$MenuActive[0], "institutions" => $institutions]);
    }

    public function report(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response, Tools::codigoReporte)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoReporte, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin() and $this->auth->user()->id_institucion != Tools::codigoSapiencia()) {
            $institutions = Institution::where("codigo", $this->auth->user()->id_institucion)->get();
            $programs = Program::where("codigo_institucion", $this->auth->user()->id_institucion)->get();
            $courses = Course::where("institucion_id", $this->auth->user()->id_institucion)->get();
        } else {
            $institutions = Institution::all();
            $programs = Program::all();
            $courses = Course::all();
        }
        Log::i(Tools::getEnterModuleMessage(Tools::codigoReporte, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "stats.twig", ["module_name" => Tools::$Modules[Tools::codigoReporte], "menu_active" => Tools::$MenuActive[0], "institutions" => $institutions, "programs" => $programs, "courses" => $courses]);
    }

    public function statsRegister(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response, Tools::codigoReporte)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoReporte, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        Log::i(Tools::getEnterModuleMessage(Tools::codigoEstadistica, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "stats_register.twig", ["module_name" => Tools::$Modules[Tools::codigoReporte], "menu_active" => Tools::$MenuActive[0], "anos" => Tools::getAnosEvaluate()]);

    }

    public function search(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoBusqueda)) {
            Log::a(Tools::getTryEnterModuleMessage(Tools::codigoBusqueda, $this->auth->user()->usuario), Tools::getTypeAction(3));
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        Log::i(Tools::getEnterModuleMessage(Tools::codigoBusqueda, $this->auth->user()->usuario), Tools::getTypeAction(3));
        return $this->view->render($response, "search.twig", ['module_name' => Tools::$Modules[Tools::codigoBusqueda], "menu_active" => Tools::$MenuActive[0]]);
    }

    public function searchStudent(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoBusqueda)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        return $this->view->render($response, "search_student.twig", ["module_name" => ["Búsqueda#admin.search", "Búsqueda usuario s"], "menu_active" => Tools::$MenuActive[1]]);
    }

    public function searchCourse(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoBusqueda)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        return $this->view->render($response, "search_course.twig", ["module_name" => ["Búsqueda#admin.search", "Búsqueda Curso"], "menu_active" => Tools::$MenuActive[1]]);
    }

    public function searchProgram(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoBusqueda)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        return $this->view->render($response, "search_program.twig", ["module_name" => ["Búsqueda#admin.search", "Búsqueda Programa"], "menu_active" => Tools::$MenuActive[1]]);
    }

    public function searchReport(Request $request, Response $response)
    {
        $firstDate = date("Y") . "-" . date("m") . "-" . "1";
        $lastDate = date("Y") . "-" . date("m") . "-" . "31";

        $dataTable = Tools::getDataGeneralForMonth(1, $firstDate, $lastDate);
        $total = Tools::getTotalStudent();
        $institutions = Institution::all(["nombre", "codigo"])->sortBy("nombre");
        return $this->view->render($response, "report.twig", ["module_name" => "Estadisticas", "menu_active" => Tools::$MenuActive[1], "data_table" => $dataTable, "total" => $total, "institutions" => $institutions]);
    }
    public function userAdd(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoBusqueda)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        $institutions =Institution::all();
        return $this->view->render($response, "content_form_add.twig", ["form" => "user.twig", "instituciones" => $institutions, "module_name" => ["Usuarios @Monitor#admin.users","Agregar Usuario @Monitor"], "menu_active" => Tools::$MenuActive[0]]);
    }
    public function studentAdd(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoUsuarioCampus)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        $institutions =Institution::all();
        return $this->view->render($response, "content_form_add.twig", ["form" => "student.twig", "instituciones" => $institutions, "module_name" => ["Usuarios Campus#admin.students", "Agregar Usuario Campus"], "menu_active" => Tools::$MenuActive[0]]);
    }

    public function programAdd(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoProgramas)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
            $instances = Manager::table('instancia')->whereIn('codigo', [1, 2])->orWhere('institucion_id', $this->auth->user()->id_institucion)->get();
        }else {
            $instances = Instance::all();
        }
        $institutions =Institution::all();
        return $this->view->render($response, "content_form_add.twig", ["form" => "program.twig", "instances" => $instances, "instituciones" => $institutions, "module_name" => ["Programas#admin.program", "Agregar Programas"], "menu_active" => Tools::$MenuActive[0]]);
    }

    public function courseAdd(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoCursos)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        if ($this->auth->user()->id_institucion != Tools::codigoMedellin()) {
            $programs = Program::where('codigo_institucion', $this->auth->user()->id_institucion)
                ->where("estado", 1)->get();
        } else {

            $programs = Program::all();
        }
        $institutions = Institution::all();
        return $this->view->render($response, "content_form_add.twig", ["form" => "course.twig", "module_name" => ["Cursos#admin.courses", "Agregar curso"], "programs" => $programs, "menu_active" => Tools::$MenuActive[0],  "institutions" => $institutions]);
    }

    public function instanceAdd(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoInstancias)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        return $this->renderForm("instance.twig", ["Instancias#admin.instance", "agregar instancia"], $response);
    }


    public function institutionAdd(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoInstituciones)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        return $this->renderForm("institution.twig", ["Instituciones#admin.institution", "agregar institución"], $response);
    }

    public function registerAdd(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoMatriculas)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        return $this->view->render($response, "content_form_add.twig", ["form" =>"register.twig", "module_name" => ["Matriculas#admin.register","agregar matricula"], "institutions" => Institution::all(), "menu_active" => Tools::$MenuActive[0], "roles" => Tools::$Roles]);

    }
    public function monitoreo(Request $request, Response $response)
    {
        $monitores = Monitor::all();
        return $this->view->render($response, "monitor.twig", ["module_name" => ["Estadisticas#admin.search.report", "monitores"], "monitores"=> $monitores]);
    }

    function details(Request $request, Response $response, $args){
        $monitor = Monitor::find($args['id']);
        return $this->view->render($response, "details_monitor.twig", ["monitor" => $monitor, "module_name" =>["Monitores#admon.monitoreo", $monitor->name]]);
    }

    public function changePassword(Request $request, Response $response)
    {
        if ($request->getParam('password_confirmacion') == $request->getParam('password')) {
            $user = User::find($request->getParam('id'));
            $user->clave = password_hash($request->getParam("password"), PASSWORD_DEFAULT);
            $user->save();
            $first = FirstSingIn::find($user->usuario);
            $first->singin = 1;
            $first->save();
            return $response->withRedirect($this->router->pathFor('signout'));
        }
        return $response->withRedirect($this->router->pathFor('firstsingin'));
    }
    public function searchStudentsForCourse(Request $request, Response $response)
    {

        $router = $request->getAttribute('route');
        $course = Course::where('codigo', $router->getArguments()['id'])->first();
        $students = Register::where('curso', $router->getArguments()['id'])->get();
        return $this->view->render($response, 'result_student_for_course.twig', ["students"=>$students, "course" => $course, "module_name" => ["Búsqueda#admin.search", "Usuarios Matriculados"], "menu_active" => Tools::$MenuActive[1]]);
    }

    public function searhDataForStudent(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoBusqueda)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $router = $request->getAttribute('route');
        $student = Student::find($router->getArguments()['id']);
        $student_data = Register::where('usuario', $student->usuario)->get();
        if (! $request->isXhr()) {
            return $this->view->render($response, 'result_data_for_student.twig', ["student_data"=>$student_data, "student" => $student, "module_name" => ["Búsqueda#admin.search", "Cursos del usuario"], "menu_active" => Tools::$MenuActive[1]]);
        }
        return $this->view->render($response, '_partials/register_student.twig', ["student_data"=>$student_data, "total" => $student_data->count()]);
    }

    public function searchCoursesForPogram(Request $request, Response $response)
    {
        if (!$this->accessModuleRead($response,Tools::codigoBusqueda)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $router = $request->getAttribute('route');
        if(! is_numeric($router->getArguments()['id'])) {
            $program = Program::where('nombre','=', $router->getArguments()['id'])->first();
        } else {
            $program = Program::where('codigo', $router->getArguments()['id'])->first();
        }
        $curses = Course::where('id_programa', $program->codigo)->get();

        return $this->view->render($response, 'result_curses_for_program.twig', ["curses"=> $curses, "program" => $program, "module_name" => ["Búsqueda#admin.search",  "Cursos por programa"], "menu_active" => Tools::$MenuActive[1]]);
    }

    public function registerArchive(Request $request,Response $response)
    {
        $router = $request->getAttribute('route');
        $register = Register::find($router->getArguments()['id']);
        try {
            if(RegisterArchive::updateOrCreate(['usuario' => $register->usuario, 'curso' => $register->curso], $register->toArray())) {
                if ($register->delete()) {
                    $newResponse = $response->withHeader('Content-type', 'application/json');
                    return $newResponse->withJson(['message' => 1], 200);
                }

            }
        } catch(\Exception $e) {
            return $response->withStatus(500)->write($e->getMessage());
        }
    }
    function upload_students(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoUsuarioCampus)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $institutions = Institution::all();
        return $this->view->render($response, "uploadusers.twig", ["module_name" => ["Usuarios Plataforma#admin.students", "Creación Masivamente"], "institutions" => $institutions, "menu_active" => Tools::$MenuActive[2]]);
    }

    function upload_registers(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoMatriculas)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        $institutions = Institution::all();
        return $this->view->render($response, "uploadregister.twig", ["module_name" => ["Matriculas#admin.register", "Creación Masivamente"], "menu_active" => Tools::$MenuActive[2], "institutions" => $institutions]);
    }
    function upload_courses(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoCursos)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        $institutions = Institution::all();
        return $this->view->render($response, "uploadcourse.twig", ["module_name" => ["Cursos#admin.courses", "Creación Masivamente"], "menu_active" => Tools::$MenuActive[2], "institutions" => $institutions]);
    }

    function upload_students_archive(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoUsuarioCampus)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        return $this->view->render($response, "uploadarchive.twig", ["module_name" => ["Usuario Plataforma#admin.students", "Archivar Masivamente"], "menu_active" => Tools::$MenuActive[2]]);
    }

    function upload_register_de_enroll(Request $request, Response $response)
    {
        if (!$this->accessModuleReadAndWrite($response,Tools::codigoMatriculas)) {
            return $response->withRedirect($this->router->pathFor('admin.home'));
        }
        return $this->view->render($response, "uploaddeenroll.twig", ["module_name" => ["Matricula#admin.register", "Desmatricular Masivamente"], "menu_active" => Tools::$MenuActive[2]]);
    }



    /**
        Helpers!!!
     */

    function downloadArchive(Request $request, Response $response)
    {
        return $this->download('Anexo2.xlsx', $response);

    }

    function downloadStudent(Request $request, Response $response)
    {
        return $this->download('Anexo1.xlsx', $response);
    }

    function downloadCourse(Request $request, Response $response)
    {
        return $this->download('Anexo3.xlsx', $response);
    }

    function downloadStudentArchive(Request $request, Response $response)
    {
        return $this->download('Anexo10.xlsx', $response);
    }
    function downloadRegisterArchive(Request $request, Response $response)
    {
        return $this->download('Anexo11.xlsx', $response);
    }

    protected function download(String $filename, Response $response) : Response
    {
        $fh = fopen($this->files . $filename, "rb");
        $stream = new Stream($fh);

        return $response->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Type', 'application/download')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withBody($stream);
    }

    protected function renderForm(String $partial, $module_name, Response $response) {
        return $this->view->render($response, "content_form_add.twig", ['form' => $partial, 'module_name' => $module_name]);
    }

    protected function accessModuleRead(Response $response, Int $module) : bool
    {
        if (! $this->auth->getPermissionForModule($module) || $this->auth->getPermissionForModule($module) == 0) {
            return false;
        }
        return true;
    }
    protected function accessModuleReadAndWrite(Response $response, Int $module) : bool
    {
        if (! $this->auth->getPermissionForModule($module) == Tools::LecturaEscritura) {
            return false;
        }
        return true;
    }
}
<?php

$app->get("/", "AppController:index")->setName("home")->add( new \App\Middleware\GuestMiddleware($container));
$app->post("/signin", "AuthController:signin")->setName("signin");
$app->get("/signout", "AuthController:signout")->setName("signout");
$app->get("/api/test", "ApiController:user");
$app->get("/api/v1/student", "ApiController:alluser");

$app->group("/panel", function (){
    /** Controller for view */
    $this->get("", "AppController:home")->setName("admin.home");
    $this->get("/monitoreo", "AppController:monitoreo")->setName("admin.monitoreo");
    $this->get("/students", "AppController:students")->setName("admin.students");
    $this->get("/students/add", "AppController:studentAdd")->setName('admin.student.add');
    $this->get("/students/upload", "AppController:upload_students")->setName('admin.upload.students');
    $this->get("/user/add", "AppController:userAdd")->setName('admin.user.add');
    $this->get("/register/add", "AppController:registerAdd")->setName('admin.register.add');
    $this->get("/institution/add", "AppController:institutionAdd")->setName('admin.institution.add');
    $this->get("/course/add", "AppController:courseAdd")->setName('admin.course.add');
    $this->get("/program/add", "AppController:programAdd")->setName('admin.program.add');
    $this->get("/instance/add", "AppController:instanceAdd")->setName('admin.instance.add');
    $this->get("/student/upload", "AppController:upload_students")->setName("admin.view.students.upload");
    $this->get("/students/uploadarchive", "AppController:upload_students_archive")->setName("admin.view.student.upload.archive");
    $this->get("/register/uploaddeenroll", "AppController:upload_register_de_enroll")->setName("admin.view.student.upload.deenroll");
    $this->get("/register/upload", "AppController:upload_registers")->setName("admin.upload.register");
    $this->get("/courses/upload", "AppController:upload_courses")->setName("admin.upload.courses");
    $this->get("/register", "AppController:registers")->setName("admin.register");
    $this->get("/users", "AppController:users")->setName("admin.users");
    $this->get("/instances", "AppController:instance")->setName("admin.instance");
    $this->get("/programs", "AppController:program")->setName("admin.program");
    $this->get("/institutions", "AppController:institution")->setName("admin.institution");
    $this->get("/courses", "AppController:courses")->setName("admin.courses");
    $this->get("/search", "AppController:search")->setName("admin.search");
    $this->get("/search/student", "AppController:searchStudent")->setName("admin.search.student");
    $this->get("/search/course", "AppController:searchCourse")->setName("admin.search.course");
    $this->get("/search/program", "AppController:searchProgram")->setName("admin.search.program");
    $this->get("/search/report", "AppController:searchReport")->setName("admin.search.report");
    $this->get("/stats", "AppController:report")->setName("admin.stats");
    $this->get("/stats/register", "AppController:statsRegister")->setName("admin.stats.register");
    $this->get("/first", "AppController:firstIn")->setName("firstsingin");
    $this->post("/first", "AppController:changePassword")->setName("firstsingin");

    /** Controller actions estudiante */
    $this->post("/students/upload", "StudentController:upload")->setName("admin.upload.students");
    $this->get("/students/delete/{id}", "StudentController:delete")->setName("admin.students.delete");
    $this->get("/students/show/{id}", "StudentController:show")->setName("admin.students.show");
    $this->post("/students/update/{id}", "StudentController:update")->setName("admin.students.update");
    $this->get("/students/all", "StudentController:all")->setName("admin.students.all");
    $this->post("/students", "StudentController:store")->setName("admin.students.store");
    $this->get("/students/reset/{id}", "StudentController:reset")->setName("admin.students.reset");
    $this->get("/students/archive/{id}", "StudentController:archive")->setName("admin.students.archive");
    $this->post("/students/uploadarchive", "StudentController:uploadArchive")->setName("admin.view.student.upload.archive");

    /** Controller actions matricula */
    $this->post("/register/upload", "RegisterController:upload")->setName("admin.upload.register");
    $this->post("/register/uploaddeenroll", "RegisterController:uploadDeEnRoll")->setName("admin.view.student.upload.deenroll");
    $this->get("/register/delete/{id}", "RegisterController:delete")->setName("admin.delete.register");
    $this->get("/register/show/{id}", "RegisterController:show")->setName("admin.show.register");
    $this->post("/register/update/{id}", "RegisterController:update")->setName("admin.update.register");
    $this->get("/register/all", "RegisterController:all")->setName("admin.all.register");
    $this->post("/register", "RegisterController:store")->setName("admin.store.register");

    /** Controller actions user */
    $this->post('/users', 'UserController:store')->setName('admin.users.store');
    $this->get('/users/all', 'UserController:all')->setName('admin.users.all');
    $this->get('/users/delete/{id}', 'UserController:delete')->setName('admin.users.delete');
    $this->get('/users/show/{id}', 'UserController:show')->setName('admin.users.show');
    $this->post('/users/update/{id}', 'UserController:update')->setName('admin.users.update');
    $this->get('/users/reset/{id}', 'UserController:reset')->setName('admin.users.reset');

    /** Controller actions instance */
    $this->get("/instances/all", "InstanceController:all")->setName('admin.instance.all');
    $this->post("/instances", "InstanceController:store")->setName('admin.instance.store');
    $this->get("/instances/show/{id}", "InstanceController:show")->setName('admin.instance.show');
    $this->post("/instances/update/{id}", "InstanceController:update")->setName('admin.instance.update');
    $this->get("/instances/delete/{id}", "InstanceController:delete")->setName('admin.instance.delete');

    /** Controller actions programs */
    $this->get("/programs/all", "ProgramController:all")->setName('admin.program.all');
    $this->post("/programs", "ProgramController:store")->setName('admin.program.store');
    $this->get("/programs/delete/{id}", "ProgramController:delete")->setName('admin.program.delete');
    $this->post("/programs/update/{id}", "ProgramController:update")->setName('admin.program.update');
    $this->get("/programs/show/{id}", "ProgramController:show")->setName('admin.program.show');


    /** Controller actions institution */
    $this->get("/institutions/all", "InstitutionController:all")->setName('admin.institution.all');
    $this->post("/institutions", "InstitutionController:store")->setName('admin.institution.store');
    $this->get("/institutions/delete/{id}", "InstitutionController:delete")->setName('admin.institution.delete');
    $this->get("/institutions/show/{id}", "InstitutionController:show")->setName('admin.institution.show');
    $this->post("/institutions/update/{id}", "InstitutionController:update")->setName('admin.institution.update');

    /** Controller actions courses */

    $this->get("/courses/all", "CourseController:all")->setName('admin.courses.all');
    $this->post("/courses", "CourseController:store")->setName('admin.courses.store');
    $this->get("/courses/delete/{id}", "CourseController:delete")->setName('admin.courses.delete');
    $this->get("/courses/show/{id}", "CourseController:show")->setName('admin.courses.show');
    $this->post("/courses/update/{id}", "CourseController:update")->setName('admin.courses.update');
    $this->post("/courses/upload", "CourseController:upload")->setName("admin.upload.courses");

    /** Controller actions monitores */
    $this->post("/monitoreo", "MonitorController:store")->setName("admon.monitoreo");
    $this->get("/monitoreo/all", "MonitorController:all")->setName("admin.monitoreo.all");
    $this->get("/monitoreo/show/{id}", "MonitorController:show")->setName("admin.monitoreo.show");
    $this->post("/monitoreo/update/{id}", "MonitorController:update")->setName("admin.monitoreo.update");
    $this->get("/monitoreo/delete/{id}", "MonitorController:delete")->setName("admin.monitoreo.delete");
    $this->get("/monitoreo/pause-play/{id}", "MonitorController:PausePlay")->setName("admin.monitoreo.pause-play");
    $this->get("/monitoreo/reset/{id}", "MonitorController:reset")->setName("admin.monitoreo.pause-play");
    $this->get("/monitoreo/details/{id}", "AppController:details")->setName("admin.monitoreo.details");
    $this->get("/monitoreo/data/{id}", "MonitorController:detailsData")->setName("admin.monitoreo.details.data");

    /** Controller search general */

    $this->get("/search/program/courses/{id}", "AppController:searchCoursesForPogram")->setName('admin.search.program.course');
    $this->get("/search/program/courses/usuarios/{id}", "AppController:searchStudentsForCourse")->setName('admin.search.program.course.student');
    $this->get("/search/program/courses/usuarios/info/{id}", "AppController:searhDataForStudent")->setName('admin.search.program.course.student.data');

    /** Controller archive */

    $this->get("/register/archive/{id}", "AppController:registerArchive")->setName("admin.register.archive");

    /** Controller helpers */
    $this->get("/students/check", "StudentController:checkEmailStudents")->setName('admin.check.students');
    $this->get("/students/email", "StudentController:getDataForEmailStudents")->setName('admin.data.email.students');
    $this->get("/register/courses", "RegisterController:getCourses")->setName('admin.register.courses');
    $this->get("/courses/search[/{params}]", "CourseController:search")->setName('admin.courses.search');
    $this->get("/program/search[/{params}]", "ProgramController:search")->setName('admin.program.search');
    $this->get("/students/search[/{params}]", "StudentController:search")->setName('admin.student.search');
    $this->post("/students/upload/proccess", "StudentController:proccess")->setName('admin.student.proccess');
    $this->post("/students/upload_archive/proccess", "StudentController:proccess_archive")->setName("admin.student.archive.proccess");
    $this->post("/register/upload_register/proccess", "RegisterController:proccess_register_de_en_roll")->setName("admin.register.archive.proccess");
    $this->post("/register/upload/proccess", "RegisterController:proccess")->setName('admin.register.proccess');
    $this->post("/courses/upload/proccess", "CourseController:proccess")->setName('admin.course.proccess');
    $this->post("/users/permission[/{id}]", "StudentController:permission")->setName('admin.student.permission');
    $this->get("/users/permission/view/{id}", "StudentController:permissionAll")->setName('admin.student.permissionAll');
    $this->get("/users/permission/remove/{id}", "StudentController:remove")->setName('admin.student.remove');
    $this->get("/student/download/archive", "AppController:downloadStudent")->setName('admin.student.anexo');
    $this->get("/student/download/student_archive", "AppController:downloadStudentArchive")->setName('admin.student.archive.anexo');
    $this->get("/register/download/register_enroll", "AppController:downloadRegisterArchive")->setName('admin.register.archive.anexo');
    $this->get("/register/download/archive", "AppController:downloadArchive")->setName('admin.archive.anexo');
    $this->get("/course/download/archive", "AppController:downloadCourse")->setName('admin.course.anexo');
    $this->get("/search/report/student/consolidated", "ReportController:consolidated")->setName("admin.report.student.consolidated");
    $this->get("/search/report/student/moth", "ReportController:studentForMonth")->setName("admin.report.student.month");
    $this->get("/search/report/filter[/{incial}[/{final}]]", "ReportController:filterForMonth")->setName("admin.report.filter.month");
    $this->post("/stats/register", "ReportController:statsRegister")->setName("admin.stats.period.register");
    $this->post("/stats/date/course", "CourseController:getTotalOfRegisterForDate")->setName("admin.date.report.course");
    $this->post("/stats/date/program", "ProgramController:getTotalOfRegisterForDate")->setName("admin.date.report.program");
    $this->post("/stats/date/institution", "InstitutionController:getTotalOfRegisterForDate")->setName("admin.date.report.institution");

 })->add(new \App\Middleware\AuthMiddleware($container));


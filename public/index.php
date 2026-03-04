<?php
/**
 * Front controller - routing
 */
$baseDir = dirname(__DIR__);
require $baseDir . '/config/app.php';

$url = trim($_GET['url'] ?? 'jobs', '/');
$url = $url === '' ? 'jobs' : $url;
$parts = array_filter(explode('/', $url));
$key = implode('/', $parts);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$routes = [
    'GET' => [
        'auth/login' => [AuthController::class, 'login'],
        'auth/register' => [AuthController::class, 'register'],
        'auth/logout' => [AuthController::class, 'logout'],
        'jobs' => [JobController::class, 'index'],
        'jobs/show' => [JobController::class, 'show'],
        'applications' => [ApplicationController::class, 'index'],
        'user/settings' => [UserController::class, 'profile'],
        'user/settings/edit' => [UserController::class, 'profileEdit'],
        'hr/jobs' => [HrJobController::class, 'index'],
        'hr/jobs/create' => [HrJobController::class, 'create'],
        'hr/jobs/edit' => [HrJobController::class, 'edit'],
        'hr/jobs/applicants' => [HrApplicationController::class, 'index'],
        'hr/applications/accepted' => [HrApplicationController::class, 'accepted'],
        'download/cv' => [DownloadController::class, 'cv'],
    ],
    'POST' => [
        'auth/login' => [AuthController::class, 'login'],
        'auth/register' => [AuthController::class, 'register'],
        'jobs/apply' => [ApplicationController::class, 'apply'],
        'user/settings/edit' => [UserController::class, 'profileEdit'],
        'hr/jobs/create' => [HrJobController::class, 'create'],
        'hr/jobs/store' => [HrJobController::class, 'create'],
        'hr/jobs/edit' => [HrJobController::class, 'edit'],
        'hr/jobs/delete' => [HrJobController::class, 'delete'],
        'hr/applications/update-status' => [HrApplicationController::class, 'updateStatus'],
    ],
];

$routeList = $routes[$method] ?? [];

if (isset($routeList[$key])) {
    [$controllerClass, $action] = $routeList[$key];
    $controller = new $controllerClass();
    $controller->$action();
    exit;
}

http_response_code(404);
echo '<h1>404 Not Found</h1><p><a href="' . BASE_URL . '/jobs">Ke beranda</a></p>';

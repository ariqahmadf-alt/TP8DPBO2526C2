<?php
/**
 * Front controller: seluruh request masuk lewat file ini.
 * Pola URL: index.php?page=<halaman>&action=<aksi>
 */
session_start();

require_once __DIR__ . '/controllers/LecturerController.php';
require_once __DIR__ . '/controllers/DepartmentController.php';
require_once __DIR__ . '/controllers/CourseController.php';

$routes = [
    'lecturers'   => LecturerController::class,
    'departments' => DepartmentController::class,
    'courses'     => CourseController::class,
];
$actions = ['index', 'create', 'store', 'edit', 'update', 'delete'];

$page   = $_GET['page'] ?? 'lecturers';
$action = $_GET['action'] ?? 'index';

if (!isset($routes[$page]) || !in_array($action, $actions, true)) {
    http_response_code(404);
    echo 'Halaman tidak ditemukan.';
    exit;
}

$controller = new $routes[$page]();
$controller->$action();

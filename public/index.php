<?php
require_once __DIR__ . '/../config/app.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

Auth::start();

require_once BASE_PATH . '/routes/Router.php';

$router = new Router();
require_once BASE_PATH . '/routes/web.php';

$uri       = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base      = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($base !== '') {
    $uri = substr($uri, strlen($base));
}
$uri = '/' . ltrim($uri, '/');

$router->dispatch($uri, $_SERVER['REQUEST_METHOD']);

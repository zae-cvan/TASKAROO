<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Http\Request;

$controller = new App\Http\Controllers\SpeechController();
$request = Request::create('/speech/translate', 'POST', ['text' => 'Hello world', 'to' => 'tl']);
$response = $controller->translate($request);
if ($response instanceof \Illuminate\Http\JsonResponse) {
    echo $response->getContent();
} else {
    var_dump($response);
}

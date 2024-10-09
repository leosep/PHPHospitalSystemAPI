<?php
// Enable error reporting
error_reporting(E_ALL); // Report all PHP errors
ini_set('display_errors', 1); // Display errors on the screen

require 'vendor/autoload.php';
require 'config/config.php';
require 'repositories/AuthRepository.php';
require 'repositories/PatientRepository.php';
require 'services/AuthService.php';
require 'controllers/AuthController.php';
require 'controllers/PatientController.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

$app = AppFactory::create();

// Dependency Injection
$authRepository = new AuthRepository($pdo);
$patientRepository = new PatientRepository($pdo);
$authService = new AuthService($authRepository);
$authController = new AuthController($authService);
$patientController = new PatientController($patientRepository);

// Middlewares
$jwtMiddleware = function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($authService): ResponseInterface {
    $authHeader = $request->getHeader('Authorization');
    
    if (!$authHeader) {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => 'Authorization header missing']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    // Extract token from header
    $token = str_replace('Bearer ', '', $authHeader[0]);

    // Validate token
    $decoded = $authService->validateToken($token);

    if (!$decoded) {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => 'Invalid or expired token']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    // Token is valid, proceed to the next middleware or route
    return $handler->handle($request);
};
 
// Routes
// Define a basic route for the homepage (example)
$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write("Hello, welcome to the API!");
    return $response;
});

$app->post('/login', function (ServerRequestInterface $request, ResponseInterface $response) use ($authController) {
    $response->getBody()->write($authController->login($request));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/patients', function (ServerRequestInterface $request, ResponseInterface $response) use ($patientController) {
    $response->getBody()->write($patientController->getPatients($request));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

$app->get('/patients/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($patientController) {
    $response->getBody()->write($patientController->getPatient($request, $args['id']));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

$app->post('/patients', function (ServerRequestInterface $request, ResponseInterface $response) use ($patientController) {
    $response->getBody()->write($patientController->createPatient($request));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

$app->put('/patients/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($patientController) {
    $response->getBody()->write($patientController->updatePatient($request, $args['id']));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

$app->delete('/patients/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($patientController) {
    $response->getBody()->write($patientController->deletePatient($request, $args['id']));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

// Run the application
$app->run();

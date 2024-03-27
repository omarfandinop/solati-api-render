<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controllers\HomeController;
use Slim\Factory\AppFactory;
use App\Controllers\UserController;
use App\MiddleWares\ContentTypeJson;
use App\MiddleWares\CORS;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create App
$app = AppFactory::create();

// Create Twig (Template System)
$twig = Twig::create('app/views', ['cache' => false]);

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));

// Parse json, form data and XML
$app->addBodyParsingMiddleware();

// CORS added for external requests
$app->add(new CORS());

// Group for API routes
$app->group("/api", function ($app) {
	// API routes
	$app->get("/users", UserController::class . ":index");
	$app->post("/users", UserController::class . ":store");
	$app->get("/users/{id}", UserController::class . ":show");
	$app->post("/users/{id}", UserController::class . ":update");
	$app->post("/users/{id}/delete", UserController::class . ":destroy");
})->add(new ContentTypeJson());

// Group for WEB routes
$app->group("/usuarios", function ($app) {
	// WEB routes
	$app->get("", HomeController::class . ":index")->setName('usuarios.index');
	$app->get("/{id}", HomeController::class . ":show")->setName('usuarios.show');
});

// Define Custom Error Handler, especially for 404 Not Found
$customErrorHandler = function () use ($app, $twig) {
	$response = $app->getResponseFactory()->createResponse();

	return $twig->render($response, 'not-found.html');
};

// Define Custom Error Handler
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// Run app
$app->run();

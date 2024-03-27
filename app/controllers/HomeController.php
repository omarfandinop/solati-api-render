<?php

namespace App\Controllers;

use App\Helpers\ApiHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

/**
 * Controller for handling requests related to the HOme Page.
 */
class HomeController
{
	// ApiHelper as Trait to extend some helper functions
	use ApiHelper;

	/**
     * Handles GET requests to the home page.
     * 
     * @param Request $request The received HTTP request.
     * @param Response $response The HTTP response to be sent.
     * @return Response The rendered HTTP response as HTML File
     */
	public function index(Request $request, Response $response)
	{
		$view = Twig::fromRequest($request);

		$api = $this->baseUrl();
		$content = file_get_contents("$api/users");
		$users = json_decode($content);
		$solatiSession = $_SESSION['user'] ?? false;

		return $view->render($response, 'tabla.html', compact('users', 'solatiSession'));
	}
}

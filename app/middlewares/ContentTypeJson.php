<?php

namespace App\MiddleWares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Middleware for setting the Content-Type header to application/json.
 */
class ContentTypeJson
{
	/**
	 * Example middleware invokable class
	 *
	 * @param Request $request PSR-7 request
	 * @param RequestHandler $handler PSR-15 request handler
	 *
	 * @return Response Content-Type header to application/json
	 */
	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		// Execute the request handler to obtain the response
		$response = $handler->handle($request);

		// Add the Content-Type header if it's not already set
		if (!$response->hasHeader('Content-Type')) {
			$response = $response->withHeader('Content-Type', 'application/json');
		}

		return $response;
	}
}

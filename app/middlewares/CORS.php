<?php

namespace App\MiddleWares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Middleware for adding CORS headers to the response.
 */
class CORS
{
	/**
	 * Example middleware invokable class
	 *
	 * @param Request $request PSR-7 request
	 * @param RequestHandler $handler PSR-15 request handler
	 *
	 * @return Response CORS headers to the response
	 */
	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = $handler->handle($request);
		
		// Add CORS headers to the response
		return $response
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
			->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');

		return $response;
	}
}

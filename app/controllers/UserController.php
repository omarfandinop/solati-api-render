<?php

namespace App\Controllers;

use App\Helpers\ApiHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use Valitron\Validator;

/**
 * Controller for handling requests related to user management.
 */
class UserController
{

	// ApiHelper as Trait to extend some helper functions
	use ApiHelper;

	/**
	 * Handles GET requests to fetch all users.
	 * 
	 * @param Request $request The received HTTP request.
	 * @param Response $response The HTTP response to be sent.
	 * @return Response The HTTP response with JSON data of all users.
	 */
	public function index(Request $request, Response $response)
	{
		$model = User::getInstance();
		$users = $model->all();

		$users = json_encode($users);
		$response->getBody()->write($users);

		return $response
			->withStatus(200);
	}

	/**
	 * Handles POST requests to create a new user.
	 * 
	 * @param Request $request The received HTTP request.
	 * @param Response $response The HTTP response to be sent.
	 * @return Response The HTTP response with JSON data of the created user or error messages.
	 */
	public function store(Request $request, Response $response)
	{
		// Obtener los parámetros del cuerpo de la solicitud
		$postData = $request->getParsedBody();
		$postData = $this->emptyItemRemover($postData);
		$fieldName = 'name';
		$fieldEmail = 'email';

		$v = new Validator($postData);
		$v->rule('required', array($fieldName, $fieldEmail))->message('{field} es requerido');
		$v->rule('email', $fieldEmail)->message('{field} no es válido');
		$v->labels(array(
			$fieldName => 'Nombre',
			$fieldEmail => 'Correo electrónico'
		));

		// Check if the data is valid
		if (!$v->validate()) {
			$errors = json_encode($v->errors());
			$response->getBody()->write($errors);

			return $response
				->withStatus(400);
		}

		$model = User::getInstance();
		$foundUser = $model->where('email', $postData[$fieldEmail]);

		if ($foundUser) {
			$errors = json_encode(["error" => "El correo electrónico ya se encuentra registrado"]);
			$response->getBody()->write($errors);

			return $response
				->withStatus(400);
		}

		$user = $model->create([
			$fieldName => $postData[$fieldName],
			$fieldEmail => $postData[$fieldEmail],
		]);

		$user = json_encode($user);
		$response->getBody()->write($user);

		return $response
			->withStatus(201);
	}

	/**
	 * Handles GET requests to fetch details of a specific user.
	 * 
	 * @param Request $request The received HTTP request.
	 * @param Response $response The HTTP response to be sent.
	 * @param array $args The route parameters (user ID).
	 * @return Response The HTTP response with JSON data of the requested user.
	 */
	public function show(Request $request, Response $response, $args)
	{
		$id = $args["id"];

		$model = User::getInstance();
		$user = $model->find($id);

		$user = json_encode($user);
		$response->getBody()->write($user);

		return $response
			->withStatus(200);
	}

	/**
	 * Handles POST requests to update details of a specific user.
	 * 
	 * @param Request $request The received HTTP request.
	 * @param Response $response The HTTP response to be sent.
	 * @param array $args The route parameters (user ID).
	 * @return Response The HTTP response with JSON data of the updated user or error messages.
	 */
	public function update(Request $request, Response $response, $args)
	{
		$id = $args["id"];
		$postData = $request->getParsedBody();
		$postData = $this->emptyItemRemover($postData);
		$fieldName = 'name';
		$fieldEmail = 'email';

		// Check if the data is empty
		if (!count($postData)) {
			$errors = json_encode(["error" => "Sin datos para actualizar"]);
			$response->getBody()->write($errors);

			return $response
				->withStatus(400);
		}

		$v = new Validator($postData);
		$v->rule('optional', array($fieldName, $fieldEmail));
		$v->rule('email', $fieldEmail)->message('{field} no es válido');
		$v->labels(array(
			$fieldName => 'Nombre',
			$fieldEmail => 'Correo electrónico'
		));

		// Check if the data is valid
		if (!$v->validate()) {
			$errors = json_encode($v->errors());
			$response->getBody()->write($errors);

			return $response
				->withStatus(400);
		}

		$model = User::getInstance();

		if ($postData[$fieldEmail]) {
			$foundUser = $model->where('email', $postData[$fieldEmail]);

			if ($foundUser) {
				$errors = json_encode(["error" => "El correo electrónico ya se encuentra registrado"]);
				$response->getBody()->write($errors);

				return $response
					->withStatus(400);
			}
		}

		$user = $model->update($id, $postData);

		$user = json_encode($user);

		$response->getBody()->write($user);

		return $response
			->withStatus(200);
	}

	/**
	 * Handles POST requests to delete a specific user.
	 * 
	 * @param Request $request The received HTTP request.
	 * @param Response $response The HTTP response to be sent.
	 * @param array $args The route parameters (user ID).
	 * @return Response The HTTP response with JSON data of the deleted user or error messages.
	 */
	public function destroy(Request $request, Response $response, $args)
	{
		$id = $args["id"];

		$model = User::getInstance();
		$user = $model->delete($id);

		$user = json_encode($user);

		$response->getBody()->write($user);

		return $response
			->withStatus(200);
	}
}

<?php

namespace App\Models;

/**
 * Class User
 * Represents a model for interacting with the 'users' table in the database.
 */
class User extends Model
{
	/** @var User|null The singleton instance of the User class. */
	private static $instance = null;

	/** @var string The name of the database table associated with this model. */
	protected $table = 'users';

	/**
     * Retrieve the singleton instance of the User class.
     *
     * @return User The singleton instance of the User class.
     */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
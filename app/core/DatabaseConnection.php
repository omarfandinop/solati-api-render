<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Class DatabaseConnection
 * 
 * Singleton class for establishing a connection to the database using PDO.
 */
class DatabaseConnection
{
	/** @var DatabaseConnection|null The singleton instance of the class */
	private static $instance;

	/** @var PDO The PDO database connection */
	private $connection;

	/**
     * Private constructor to prevent creating instances of the class directly.
     */
	private function __construct()
	{
		// Retrieve database connection parameters from environment variables (.env)
		$server = $_ENV['DB_SERVER'];
		$host = $_ENV['DB_HOST'];
		$port = $_ENV['DB_PORT'];
		$database = $_ENV['DB_DATABASE'];
		$username = $_ENV['DB_USERNAME'];
		$password = $_ENV['DB_PASSWORD'];
		$dsn = "$server:host=$host;port=$port;dbname=$database";
	
		try {
			// Establish a connection to the database using PDO
			$this->connection = new PDO($dsn, $username, $password);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			// In case of connection failure, output the error message and exit
			echo "Connection failed: " . $e->getMessage();
			exit;
		}
	}

	/**
     * Get the singleton instance of the DatabaseConnection class.
     *
     * @return DatabaseConnection The singleton instance
     */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
     * Get the PDO database connection.
     *
     * @return PDO The PDO database connection
     */
	public function getConnection()
	{
		return $this->connection;
	}
}

<?php

namespace App\Models;

use App\Core\DatabaseConnection;
use PDO;

/**
 * Base model class for interacting with the database.
 */
class Model
{
	private $connection;
	protected $table;
	protected $query;

	/**
     * Constructor to initialize the database connection.
     */
	public function __construct()
	{
		$this->connection = DatabaseConnection::getInstance()->getConnection();
	}

	/**
     * Execute a SQL query.
     *
     * @param string $sql The SQL query
     * @param array $data An array of parameter values (optional)
     *
     * @return Model The model instance
     */
	public function query($sql, $data = [])
	{
		$sqlPrep = $this->connection->prepare($sql);

		if (count($data)) {

			$i = 1;
			foreach ($data as $param) {
				$value = $param;
				$type = PDO::PARAM_STR;

				if (is_array($param)) {
					list($value, $type) = $param;
				}

				$sqlPrep->bindValue($i, $value, $type);
				$i++;
			}
		}

		$sqlPrep->execute();
		$this->query = $sqlPrep;

		return $this;
	}

	/**
     * Fetch the first result from the query.
     *
     * @return array|null The first result as an associative array, or null if no result
     */
	public function first()
	{
		return $this->query->fetch(PDO::FETCH_ASSOC);
	}

	/**
     * Fetch all results from the query.
     *
     * @return array The results as an array of associative arrays
     */
	public function get()
	{
		return $this->query->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
     * Retrieve all records from the database table.
     *
     * @return array An array of associative arrays representing the records
     */
	public function all()
	{
		// Construct SQL query to select all records from the table
		$sql = "SELECT * FROM {$this->table}";
		return $this->query($sql)->get();
	}

	/**
     * Find a record by its ID.
     *
     * @param int $id The ID of the record to find
     * @return array|null An associative array representing the record, or null if not found
     */
	public function find($id)
	{
		// Construct SQL query to select a record by ID
		$sql = "SELECT * FROM {$this->table} WHERE id = ?";
		return $this->query($sql, [[$id, PDO::PARAM_INT]])->first();
	}

	/**
     * Retrieve records from the database table based on a condition.
     *
     * @param string $column The column name to filter on
     * @param string $operator The comparison operator
     * @param mixed $value The value to compare against
     * @return array|null An associative array representing the record, or null if not found
     */
	public function where($column, $operator, $value = '')
	{
		// If only two parameters are provided, assume equality comparison
		if (!$value) {
			$value = $operator;
			$operator = '=';
		}

		// Construct SQL query to select records based on condition
		$sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?";
		return $this->query($sql, [[$value, PDO::PARAM_INT]])->first();
	}

	/**
     * Create a new record in the database table.
     *
     * @param array $data An associative array of column names and values for the new record
     * @return array An associative array representing the newly created record
     */
	public function create($data)
	{
		// Extract column names and values from the data array
		$columns = implode(', ', array_keys($data));
		$values = str_repeat('?, ', count($data) - 1) . "?";

		// Construct SQL query to insert a new record into the table
		$sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";

		// Execute the query with the values as parameters
		$this->query($sql, array_values($data));

		// Retrieve the ID of the newly inserted record
		$lastId = $this->connection->lastInsertId();
		return $this->find($lastId);
	}

	/**
     * Update an existing record in the database table.
     *
     * @param int $id The ID of the record to update
     * @param array $data An associative array of column names and new values for the record
     * @return array|null An associative array representing the updated record, or null if not found
     */
	public function update($id, $data)
	{
		// Find the record to update based on its ID
		$foundItem = $this->find($id);

		// If the record does not exist, return null
		if (!$foundItem) {
			return null;
		}

		// Construct SQL data string for the update query
		$sqlData = array_map(function ($column) {
			return "{$column} = ?";
		}, array_keys($data));
		$sqlData = implode(', ', $sqlData);

		// Construct SQL query to update the record
		$sql = "UPDATE {$this->table} SET {$sqlData} WHERE id = ?";

		// Execute the query with the new values and the record's ID as parameters
		$this->query($sql, [...array_values($data), [$foundItem['id'], PDO::PARAM_INT]]);

		// Return the updated record by finding it again using its ID
		return $this->find($foundItem['id']);
	}

	/**
     * Delete a record from the database table based on its ID.
     *
     * @param int $id The ID of the record to delete
     * @return array|null An associative array representing the deleted record, or null if not found
     */
	public function delete($id)
	{
		// Find the record to delete based on its ID
		$foundItem = $this->find($id);

		if (!$foundItem) {
			return null;
		}

		// Construct SQL query to delete the record
		$sql = "DELETE FROM {$this->table} WHERE id = ?";

		// Execute the query with the record's ID as a parameter
		$this->query($sql, [[$id, PDO::PARAM_INT]]);

		// Return the deleted record
		return $foundItem;
	}
}

<?php

namespace App\Helpers;

/**
 * Trait ApiHelper
 * 
 * Helper methods for API-related tasks.
 */
trait ApiHelper
{
	/**
     * Remove empty items (null, empty strings, etc.) from an array.
     *
     * @param array $array The array to be filtered
     * @return array The filtered array
     */
	public function emptyItemRemover($array)
	{
		return array_filter($array, function ($value) {
			$value = trim($value);
			return !empty($value) || $value === 0 || $value === '0';
		});
	}

	/**
     * Get the base URL of the API.
     *
     * @return string The base URL of the API
     */
	public function baseUrl()
	{
		$protocol = $_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
		$host = $_SERVER['HTTP_HOST'];
		return "$protocol://$host/api";
	}
}

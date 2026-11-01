<?php

namespace Studio\Util;

use mysqli_stmt;

class BindParam {

	private $values = [];
	private $types = '';

	/**
	 * Adds a parameter for binding.
	 *
	 * @param string $type
	 * @param mixed $value
	 * @return void
	 */
	public function add($type, $value) {
		$this->values[] = $value;
		$this->types .= $type;
	}

	/**
	 * Compiles the argument array for passing into `bind_param`.
	 *
	 * @return mixed[]
	 */
	public function get() {
		$values = array_merge(array($this->types), $this->values);
		$refs = [];

		foreach($values as $key => $value) {
            $refs[$key] = &$values[$key];
		}

		return $refs;
	}

	/**
	 * Binds the parameters to the given statement.
	 *
	 * @param mysqli_stmt $stmt
	 * @return bool
	 */
	public function apply(\mysqli_stmt $stmt) {
		return call_user_func_array(array($stmt, 'bind_param'), $this->get());
	}

}

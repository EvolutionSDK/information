<?php

namespace Bundles\Information;
use Bundles\SQL\SQLBundle;
use Exception;
use e;

/**
 * Information Bundle
 * Attaches custom data to any model
 * @author Nate Ferrero
 */
class Bundle extends SQLBundle {
	
	public function __callBundle($model) {
		if(is_object($model))
			$model = $model->__map();
		list($table, $id) = explode(':', $model);
		return new Accessor($table, (int) $id);
	}

	public function core($id = 1) {
		return new Accessor('core.information', $id);
	}

	public function core64($id = 1) {
		$a64 = new Accessor('core.information', $id);
		$a64->use_base64 = true;
		return $a64;
	}

}

/**
 * Information Accessor Bundle
 * Set / get actual data on the record
 * @author Nate Ferrero
 */
class Accessor {
	
	private $table;
	private $id;
	public $use_base64 = false;

	public function __construct($table, $id) {
		if(!is_numeric($id) || $id < 1)
			throw new Exception("ID `$id` is invalid, must be a positive integer");
		$this->table = $table;
		$this->id = $id;
	}

	public function getRecord($field) {
		$field = mysql_escape_string($field);
		return e::$sql->query("SELECT `field`, `value` FROM `information.record` WHERE `model` = '$this->table' AND `id` = '$this->id' AND `field` = '$field'")->row();
	}

	public function listRecords() {
		return e::$sql->query("SELECT `field`, `value`, `updated_timestamp`, `created_timestamp` FROM `information.record` WHERE `model` = '$this->table' AND `id` = '$this->id'")->all();
	}

	public function delRecord($field) {
		$field = mysql_escape_string($field);
		return e::$sql->query("DELETE FROM `information.record` WHERE `model` = '$this->table' AND `id` = '$this->id' AND `field` = '$field'");
	}

	public function updateRecord($field, $value) {
		$field = mysql_escape_string($field);
		$value = mysql_escape_string($value);
		return e::$sql->query("UPDATE `information.record` SET `value` = '$value' WHERE `model` = '$this->table' AND `id` = '$this->id' AND `field` = '$field'");
	}

	public function createRecord($field, $value) {
		$field = mysql_escape_string($field);
		$value = mysql_escape_string($value);
		return e::$sql->query("INSERT INTO `information.record` (`model`, `id`, `field`, `value`) VALUES ('$this->table', $this->id, '$field', '$value')");
	}

	public function __call($method, $args) {
		return null;
	}

	public function __isset($field) {
		return true;
	}

	public function __get($field) {
		$row = $this->getRecord($field);
		if(!$row) return null;
		if($this->use_base64)
			return base64_encode($row['value']);
		return $row['value'];
	}

	public function __set($field, $value) {
		$row = $this->getRecord($field);
		if(!$row) return $this->createRecord($field, $value);
		else {
			if(strlen($value) < 1) return $this->delRecord($field);
			return $this->updateRecord($field, $value);	
		}
	}

}
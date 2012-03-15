<?php

namespace Bundles\Information;
use Bundles\SQL\SQLBundle;
use Bundles\SQL\Model;
use Exception;
use e;

/**
 * Information Bundle
 * Attaches custom data to any model
 * @author Nate Ferrero
 */
class Bundle extends SQLBundle {
	
	public function __callBundle($group = false) {
		if(!$group) return $this;
		else return new Accessor('information.record', $group);
	}

	public function __getBundle($method = true) {
		return new Accessor('information.record', '/');
	}

	public function accessor(Model $model) {
		$tableName = "\$information ".$model->__getTable();
		return new Accessor($tableName, $model->id);
	}

}

/**
 * Information Accessor Bundle
 * Set / get actual data on the record
 * @author Nate Ferrero
 */
class Accessor {
	
	private $base64 = false;
	private $table;
	private $id;

	public function __construct($table, $id) {
		$this->table = $table;
		$this->id = $id;
	}

	public function b64() {
		$this->base64 = true;
		return $this;
	}

	public function getRecord($field) {
		$field = mysql_escape_string($field);
		$owner = $this->table !== 'information.record' ? "AND `owner` = '$this->id'" : "AND `category` = '$this->id'";
		if(!is_numeric($this->id) && $this->id == '*') return false;
		return e::$sql->query("SELECT `field`, `value` FROM `$this->table` WHERE `field` = '$field' $owner")->row();
	}

	public function listRecords() {
		$owner = $this->table !== 'information.record' ? "WHERE `owner` = '$this->id'" : "WHERE `category` = '$this->id'";
		if(!is_numeric($this->id) && $this->id == '*') $owner = '';
		return e::$sql->query("SELECT `field`, `value`, `updated_timestamp` FROM `$this->table` $owner")->all();
	}

	public function delRecord($field) {
		$field = mysql_escape_string($field);
		$owner = $this->table !== 'information.record' ? "AND `owner` = '$this->id'" : "AND `category` = '$this->id'";
		if(!is_numeric($this->id) && $this->id == '*') return false;
		return e::$sql->query("DELETE FROM `$this->table` WHERE `field` = '$field' $owner");
	}

	public function updateRecord($field, $value) {
		$field = mysql_escape_string($field);
		$value = mysql_escape_string($value);
		$owner = $this->table !== 'information.record' ? "AND `owner` = '$this->id'" : "AND `category` = '$this->id'";
		if(!is_numeric($this->id) && $this->id == '*') return false;
		return e::$sql->query("UPDATE `$this->table` SET `value` = '$value' WHERE `field` = '$field' $owner");
	}

	public function createRecord($field, $value) {
		$field = mysql_escape_string($field);
		$value = mysql_escape_string($value);
		$owner = $this->table !== 'information.record' ? ", `owner` = '$this->id'" : ", `category` = '$this->id'";
		if(!is_numeric($this->id) && $this->id == '*') return false;
		return e::$sql->query("INSERT INTO `$this->table` SET `field` = '$field', `value` = '$value' $owner");
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
		if($this->base64)
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
<?php

namespace Bundles\SQL\Extensions;
use Bundles\SQL\ListObj;
use Bundles\SQL\Model;
use Exception;
use e;

class Information {

	public function _buildTable($tableName) {
		e::$sql->architect("\$information $tableName", array(
			'fields' => array(
				"id" => '_suppress',
				"created_timestamp" => '_suppress',
				"owner" => 'number',
				"updated_timestamp" => array(
					'Type' => 'timestamp',
					'Null' => 'YES',
					'Key' => '',
					'Default' => 'CURRENT_TIMESTAMP',
					'Extra' => 'on update CURRENT_TIMESTAMP'
				),
				"field" => 'string',
				"value" => 'string'
			)
		));
	}

	public function modelSet(Model $model, $key, $val) {
		e::information()->accessor($model)->$key = $val;
	}

	public function modelGet(Model $model, $key) {
		return e::information()->accessor($model)->$key;
	}

	public function modelGet64(Model $model, $key) {
		return e::information()->accessor($model)->b64()->$key;
	}

	public function modelList(Model $model) {
		return e::information()->accessor($model)->listRecords();
	}

}
<?php

namespace Bundles\SQL\Extensions;
use Bundles\SQL\ListObj;
use Bundles\SQL\Model;
use Exception;
use e;

class Information {

	private $type = 'user';

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
				"value" => 'string',
				"type" => array(
					"Type" => "enum('user','system')",
					'Default' => 'user'
				)
			)
		));
	}

	public function modelSet(Model $model, $key, $val) {

		if($key == '__type')
			return $this->type = $val;

		foreach(e::$events->informationSet($model, $key, $val) as $bundle => $result) {
			if(is_null($result)) continue;
			if($result === false) return;
			
			$val = $result;
		}
		
		e::information()->accessor($model, $this->type)->$key = $val;
	}

	public function modelIsset(Model $model, $key) {
		if(strlen(e::information()->accessor($model, $this->type)->$key) > 0)
			return true;
		else return null;
	}

	public function modelGet(Model $model, $key) {
		return e::information()->accessor($model, $this->type)->$key;
	}

	public function modelGet64(Model $model, $key) {
		return e::information()->accessor($model, $this->type)->b64()->$key;
	}

	public function modelList(Model $model) {
		static $cache = array();

		if(!isset($cache[$model->__map()]))
			$cache[$model->__map()] = e::information()->accessor($model, $this->type)->listRecords();
		return $cache[$model->__map()];
	}

}
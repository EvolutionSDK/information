<?php

namespace Bundles\SQL\Extensions;
use Bundles\SQL\ListObj;
use Bundles\SQL\Model;
use Exception;
use e;

class Information {

	public function modelSet(Model $model, $key, $val) {
		e::information($model)->$key = $val;
	}

	public function modelGet(Model $model, $key) {
		return e::information($model)->$key;
	}

	public function modelList(Model $model) {
		return e::information($model)->listRecords();	
	}

}
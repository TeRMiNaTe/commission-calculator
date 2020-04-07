<?php

namespace App\Managers;

class BaseManager
{
	protected $app;

	public function __construct($app)
	{
		$this->app = $app;
	}
}

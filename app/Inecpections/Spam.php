<?php

namespace App\Inecpections;

class Spam
{
	/**
	 * All registered inspections
	 * 
	 * @var array
	 */
	protected $inecpections = [
		InvalidKeywords::class,
		KeyHeldDown::class,
	];

	public function detect($body)
	{
		foreach ($this->inecpections as $inspection) {
			app($inspection)->detect($body);
		}
		return false;
	}
}

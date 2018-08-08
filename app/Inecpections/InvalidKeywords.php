<?php 

namespace App\Inecpections;

use Exception;
class InvalidKeywords
{
	protected $keywords = [
		'yahoo custom support', 
	];
	public function detect($body)
	{
		foreach ($this->keywords as $keyword) {
			if(stripos($body, $keyword) !== false) {
				throw new Exception('Your reply contains Spam.');
			}
		}
	}
}

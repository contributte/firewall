<?php declare(strict_types = 1);

namespace Tests\Toolkit\Helpers;

use Nette\DI\Config\Adapters\NeonAdapter;
use Nette\Neon\Neon;

class GeneralHelper
{

	public static function parseNeon(string $str): array
	{
		return (new NeonAdapter())->process((array) Neon::decode($str));
	}

}

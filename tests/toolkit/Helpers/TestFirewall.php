<?php declare(strict_types = 1);

namespace Tests\Toolkit\Helpers;

use Contributte\Firewall\Authentication\BaseFirewall;
use Nette\Security\SimpleIdentity;

class TestFirewall extends BaseFirewall
{

	public function authenticate(int $id): void
	{
		$this->login(new SimpleIdentity($id));
	}

}

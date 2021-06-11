<?php declare(strict_types = 1);

namespace Contributte\Firewall\Tests\Helpers;

use Nette\Security\SimpleIdentity;

class TestFirewall extends \Contributte\Firewall\Authentication\BaseFirewall
{

	public function authenticate(int $id): void{
		$this->login(new SimpleIdentity($id));
	}
}
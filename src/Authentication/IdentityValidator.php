<?php declare(strict_types = 1);

namespace Contributte\Firewall\Authentication;

use Nette\Security\IIdentity;

interface IdentityValidator
{

	public function validate(IIdentity $identity): IIdentity;

}

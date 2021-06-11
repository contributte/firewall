<?php declare(strict_types = 1);

namespace Contributte\Firewall\Authentication;

interface UserStorage extends \Nette\Security\UserStorage
{

	public function setNamespace(string $namespace): void;

}

<?php declare(strict_types = 1);

namespace Contributte\Firewall\Authorization;

use Nette\Security\Resource;

interface Authorizator
{

	/**
	 * @param Resource|string $resource
	 */
	public function isAllowed($resource, string $privilege): bool;

}

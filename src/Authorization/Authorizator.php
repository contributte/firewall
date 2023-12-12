<?php declare(strict_types = 1);

namespace Contributte\Firewall\Authorization;

use Nette\Security\Resource;

interface Authorizator
{

	public function isAllowed(Resource|string $resource, string $privilege): bool;

}

<?php declare(strict_types = 1);

namespace Contributte\Firewall\Authorization;

use Contributte\Firewall\Authentication\Firewall;
use Nette\Security\Resource;

class PermissionAuthorizator implements Authorizator
{

	public const AUTHENTICATED_ROLE = '__authenticated';

	public const GUEST_ROLE = '__guest';

	private Firewall $firewall;

	private Permission $permission;

	public function __construct(Firewall $firewall, Permission $permission)
	{
		$this->firewall = $firewall;
		$this->permission = $permission;
		$this->permission->addRole(self::AUTHENTICATED_ROLE);
		$this->permission->addRole(self::GUEST_ROLE);
	}

	public function isAllowed(Resource|string $resource, string $privilege): bool
	{
		$identity = $this->firewall->getIdentity();
		$this->permission->setIdentity($identity);
		if ($identity !== null) {
			$roles = $identity->getRoles();
			// Add a role to make sure even identities without any roles will invoke permission.
			$roles[] = self::AUTHENTICATED_ROLE;
		} else {
			$roles = [self::GUEST_ROLE];
		}

		foreach ($roles as $role) {
			if ($this->permission->isAllowed($role, $resource, $privilege)) {
				return true;
			}
		}

		return false;
	}

}

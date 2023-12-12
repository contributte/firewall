<?php declare(strict_types = 1);

namespace Contributte\Firewall\Authorization;

use Nette\Security\IIdentity;
use Nette\Security\Permission as BasePermission;

class Permission extends BasePermission
{

	private ?IIdentity $identity = null;

	public function setIdentity(?IIdentity $identity = null): void
	{
		$this->identity = $identity;
	}

	/**
	 * Allows one or more Roles access to [certain $privileges upon] the specified Resource(s).
	 * If $assertion is provided, then it must return TRUE in order for rule to apply.
	 *
	 * @return static
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
	 */
	public function allow($roles = self::ALL, $resources = self::ALL, $privileges = self::ALL, ?callable $assertion = null): self
	{
		if ($assertion !== null) {
			$assertion = fn () => $assertion($this->identity, $this->getQueriedResource(), $this->getQueriedRole());
		}

		parent::allow($roles, $resources, $privileges, $assertion);

		return $this;
	}

	/**
	 * Denies one or more Roles access to [certain $privileges upon] the specified Resource(s).
	 * If $assertion is provided, then it must return TRUE in order for rule to apply.
	 *
	 * @return static
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
	 */
	public function deny($roles = self::ALL, $resources = self::ALL, $privileges = self::ALL, ?callable $assertion = null): self
	{
		if ($assertion !== null) {
			$assertion = fn () => $assertion($this->identity, $this->getQueriedResource(), $this->getQueriedRole());
		}

		parent::deny($roles, $resources, $privileges, $assertion);

		return $this;
	}

}

<?php declare(strict_types = 1);

namespace Tests\Toolkit\Helpers;

use Contributte\Firewall\Authentication\UserStorage;
use Nette\Security\IIdentity;

class TestStorage implements UserStorage
{

	/**
	 * @inheritDoc
	 */
	public function saveAuthentication(IIdentity $identity): void
	{
		// TODO: Implement saveAuthentication() method.
	}

	/**
	 * @inheritDoc
	 */
	public function clearAuthentication(bool $clearIdentity): void
	{
		// TODO: Implement clearAuthentication() method.
	}

	/**
	 * @inheritDoc
	 */
	public function getState(): array
	{
		// TODO: Implement getState() method.
	}

	/**
	 * @inheritDoc
	 */
	public function setExpiration(?string $expire, bool $clearIdentity): void
	{
		// TODO: Implement setExpiration() method.
	}

	public function setNamespace(string $namespace): void
	{
		// TODO: Implement setNamespace() method.
	}

}

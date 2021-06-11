<?php
declare(strict_types = 1);

namespace Contributte\Firewall\Tests\Helpers;

use Nette\Security\IIdentity;

class TestStorage implements \Contributte\Firewall\Authentication\UserStorage
{

	/**
	 * @inheritDoc
	 */
	function saveAuthentication(IIdentity $identity): void
	{
		// TODO: Implement saveAuthentication() method.
	}

	/**
	 * @inheritDoc
	 */
	function clearAuthentication(bool $clearIdentity): void
	{
		// TODO: Implement clearAuthentication() method.
	}

	/**
	 * @inheritDoc
	 */
	function getState(): array
	{
		// TODO: Implement getState() method.
	}

	/**
	 * @inheritDoc
	 */
	function setExpiration(?string $expire, bool $clearIdentity): void
	{
		// TODO: Implement setExpiration() method.
	}

	public function setNamespace(string $namespace): void
	{
		// TODO: Implement setNamespace() method.
	}

}
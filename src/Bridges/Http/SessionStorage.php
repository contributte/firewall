<?php declare(strict_types = 1);

namespace Contributte\Firewall\Bridges\Http;

use Contributte\Firewall\Authentication\UserStorage;
use Nette\Http\Session;
use Nette\Security\IIdentity;

class SessionStorage implements UserStorage
{

	private \Nette\Bridges\SecurityHttp\SessionStorage $storage;

	public function __construct(Session $session)
	{
		$this->storage = new \Nette\Bridges\SecurityHttp\SessionStorage($session);
	}

	public function saveAuthentication(IIdentity $identity): void
	{
		$this->storage->saveAuthentication($identity);
	}

	public function clearAuthentication(bool $clearIdentity): void
	{
		$this->storage->clearAuthentication($clearIdentity);
	}

	public function getState(): array
	{
		return $this->storage->getState();
	}

	public function setExpiration(?string $expire, bool $clearIdentity = false): void
	{
		$this->storage->setExpiration($expire, $clearIdentity);
	}

	public function setNamespace(string $namespace): void
	{
		$this->storage->setNamespace($namespace);
	}

}

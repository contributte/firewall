<?php declare(strict_types = 1);

namespace Contributte\Firewall\Bridges\Http;

use Contributte\Firewall\Authentication\UserStorage;
use Nette\Bridges\SecurityHttp\SessionStorage as NetteSessionStorage;
use Nette\Http\Session;
use Nette\Security\IIdentity;

class SessionStorage implements UserStorage
{

	private NetteSessionStorage $storage;

	public function __construct(Session $session)
	{
		$this->storage = new NetteSessionStorage($session);
	}

	public function saveAuthentication(IIdentity $identity): void
	{
		$this->storage->saveAuthentication($identity);
	}

	public function clearAuthentication(bool $clearIdentity): void
	{
		$this->storage->clearAuthentication($clearIdentity);
	}

	/**
	 * @return array{bool, IIdentity|null, int|null}
	 */
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

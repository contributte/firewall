<?php declare(strict_types = 1);

namespace Contributte\Firewall\Authentication;

use Nette\Security\IdentityHandler;
use Nette\Security\IIdentity;
use Nette\Utils\Arrays;

abstract class BaseFirewall implements Firewall
{

	/** @var callable[]  function (IIdentity $identity): void; Occurs when the user is successfully logged in */
	public array $onLoggedIn = [];

	/** @var callable[]  function (IIdentity $identity): void; Occurs when the user is logged out */
	public array $onLoggedOut = [];

	private UserStorage $storage;

	private ?IdentityValidator $identityValidator;

	public function __construct(UserStorage $storage, ?IdentityValidator $identityValidator = null)
	{
		$this->storage = $storage;
		$this->identityValidator = $identityValidator;
	}

	public function login(IIdentity $identity): void
	{
		$id = $this->storage instanceof IdentityHandler
			? $this->storage->sleepIdentity($identity)
			: $identity;

		$this->storage->saveAuthentication($id);
		Arrays::invoke($this->onLoggedIn, $identity);
	}

	public function logout(): void
	{
		$identity = $this->getIdentity();
		if ($identity !== null) {
			$this->storage->clearAuthentication(false);
			Arrays::invoke($this->onLoggedOut, $identity);
		}
	}

	public function getIdentity(): ?IIdentity
	{
		$identity = null;
		$authenticated = false;
		$logoutReason = null;

		(static function (bool $state, ?IIdentity $id, ?int $reason) use (&$identity, &$authenticated, &$logoutReason): void {
			$identity = $id;
			$authenticated = $state;
			$logoutReason = $reason;
		})(...$this->storage->getState());

		if (!$authenticated || $identity === null) {
			return null;
		}

		$identity = $this->storage instanceof IdentityHandler
			? $this->storage->wakeupIdentity($identity)
			: $identity;

		return $this->identityValidator !== null && $identity !== null
			? $this->identityValidator->validate($identity)
			: $identity;
	}

	public function getExpiredIdentity(): ?IIdentity
	{
		/** @var IIdentity|null $identity */
		$identity = $this->storage->getState()[1];

		if ($identity === null) {
			return null;
		}

		$identity = $this->storage instanceof IdentityHandler
			? $this->storage->wakeupIdentity($identity)
			: $identity;

		return $this->identityValidator !== null && $identity !== null
			? $this->identityValidator->validate($identity)
			: $identity;
	}

	public function getLogoutReason(): ?int
	{
		/** @var int|null $reason */
		$reason = $this->storage->getState()[2];
		return $reason;
	}

	public function setExpiration(?string $expire, bool $clearIdentity = false): void
	{
		$this->storage->setExpiration($expire, $clearIdentity);
	}

}

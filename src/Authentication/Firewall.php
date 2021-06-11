<?php declare(strict_types = 1);

namespace Contributte\Firewall\Authentication;

use Nette\Security\IIdentity;

interface Firewall
{

	public function login(IIdentity $identity): void;

	public function logout(): void;

	public function getIdentity(): ?IIdentity;

	public function getExpiredIdentity(): ?IIdentity;

	public function getLogoutReason(): ?int;

	public function setExpiration(?string $expire, bool $clearIdentity = false): void;

}

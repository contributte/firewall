<?php declare(strict_types = 1);

namespace Contributte\Firewall\Bridges\Tracy\SecurityPanel;

use Contributte\Firewall\Authentication\Firewall;
use Latte\Engine;
use Nette\SmartObject;
use Tracy\IBarPanel;

class SecurityPanel implements IBarPanel
{

	use SmartObject;

	/** @var Firewall[] */
	private array $firewalls;

	private Engine $engine;

	public function __construct(Firewall ...$firewalls)
	{
		$this->firewalls = $firewalls;
		$this->engine = new Engine();
	}

	public function getTab(): string
	{
		$logged = false;
		foreach ($this->firewalls as $firewall) {
			if ($firewall->getIdentity() !== null) {
				$logged = true;
				break;
			}
		}

		return $this->engine->renderToString(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'UserPanel.tab.latte', ['loggedIn' => $logged]);
	}

	public function getPanel(): string
	{
		return $this->engine->renderToString(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'UserPanel.panel.latte', ['firewalls' => $this->firewalls]);
	}

}

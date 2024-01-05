<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\Firewall\Authentication\Firewall;
use Contributte\Firewall\Bridges\Tracy\SecurityPanel\SecurityPanel;
use Contributte\Firewall\DI\FirewallExtension;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tester\Utils\Neonkit;
use Nette\DI\Compiler;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\TestFirewall;

require __DIR__ . '/../../bootstrap.php';

class FirewallExtensionTest extends TestCase
{

	public function testDefault(): void
	{
		$container = ContainerBuilder::of()
			->withCompiler(function (Compiler $compiler): void {
				$compiler->addExtension('firewall', new FirewallExtension());
				$compiler->addConfig(Neonkit::load('
				firewall:
					namespaces:
						test:
							firewall: Tests\Fixtures\TestFirewall
							storage: Tests\Fixtures\TestStorage
			'));
				$compiler->addDependencies([__FILE__]);
			})->build();

		Assert::type(TestFirewall::class, $container->getByType(Firewall::class));
		Assert::type(SecurityPanel::class, $container->getByName('firewall.panel'));
	}

}

(new FirewallExtensionTest())->run();

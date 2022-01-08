<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\Firewall\Authentication\Firewall;
use Contributte\Firewall\DI\FirewallExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\TestCase;
use Tests\Toolkit\Helpers\GeneralHelper;
use Tests\Toolkit\Helpers\TestFirewall;

require __DIR__ . '/../../bootstrap.php';

class FirewallExtensionTest extends TestCase
{

	public function testDefault(): void
	{
		$loader = new ContainerLoader(TMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler): void {
			$compiler->addExtension('firewall', new FirewallExtension());
			$compiler->addConfig(GeneralHelper::parseNeon('
				firewall:
					namespaces:
						test:
							firewall: Tests\Toolkit\Helpers\TestFirewall
							storage: Tests\Toolkit\Helpers\TestStorage
			'));
			$compiler->addDependencies([__FILE__]);
		}, __METHOD__);

		/** @var Container $container */
		$container = new $class();

		Assert::type(TestFirewall::class, $container->getByType(Firewall::class));
	}

}

(new FirewallExtensionTest())->run();

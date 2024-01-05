<?php declare(strict_types = 1);

namespace Contributte\Firewall\DI;

use Contributte\Firewall\Bridges\Http\SessionStorage;
use Contributte\Firewall\Bridges\Tracy\SecurityPanel\SecurityPanel;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @method stdClass getConfig()
 */
class FirewallExtension extends CompilerExtension
{

	public string $defaultStorage = SessionStorage::class;

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'storage' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->default($this->defaultStorage),
			'namespaces' => Expect::arrayOf(Expect::anyOf(
				Expect::string(),
				Expect::structure([
					'firewall' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->required(true),
					'storage' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->required(false),
					'validator' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->required(false),
					'authorizator' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->required(false),
				]),
			))->required(true),
			'panel' => Expect::bool(true),
		]);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$firewalls = [];
		foreach ($config->namespaces as $namespace => $securityConfig) {
			if (is_string($securityConfig)) {
				$firewall = $builder->addDefinition($this->prefix($namespace . '.firewall'))
					->setFactory($securityConfig)
					->setArgument('storage', $this->prefix('@' . $namespace . '.storage'));

				$builder->addDefinition($this->prefix($namespace . '.storage'))
					->setFactory($config->storage)
					->addSetup('setNamespace', ['namespace' => $namespace])
					->setAutowired(false);

				$firewalls[] = $firewall;
			}

			if (!is_string($securityConfig)) {
				$firewall = $builder->addDefinition($this->prefix($namespace . '.firewall'))
					->setFactory($securityConfig->firewall)
					->setArgument('storage', $this->prefix('@' . $namespace . '.storage'));

				if (isset($securityConfig->validator)) {
					$firewall->setArgument('identityValidator', $securityConfig->validator);
				}

				$builder->addDefinition($this->prefix($namespace . '.storage'))
					->setFactory($securityConfig->storage ?? $config->storage)
					->addSetup('setNamespace', ['namespace' => $namespace])
					->setAutowired(false);

				if (isset($securityConfig->authorizator)) {
					$builder->addDefinition($this->prefix($namespace . '.authorizatorFactory'))
						->setFactory($securityConfig->authorizator, ['firewall' => $firewall]);
				}

				$firewalls[] = $firewall;
			}
		}

		if ($config->panel) {
			$builder->addDefinition($this->prefix('panel'))
				->setFactory(SecurityPanel::class, ['firewalls' => $firewalls])
				->setAutowired(false);
		}
	}

	public function afterCompile(ClassType $class): void
	{
		$config = $this->getConfig();

		if ($config->panel) {
			$initialize = $class->getMethod('initialize');
			$initialize->addBody(
				'$this->getService(?)->addPanel($this->getService(?));',
				['tracy.bar', $this->prefix('panel')]
			);
		}
	}

}

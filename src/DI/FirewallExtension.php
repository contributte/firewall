<?php declare(strict_types = 1);

namespace Contributte\Firewall\DI;

use Contributte\DI\Helper\ExtensionDefinitionsHelper;
use Contributte\Firewall\Bridges\Http\SessionStorage;
use Contributte\Firewall\Bridges\Tracy\SecurityPanel\SecurityPanel;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Utils\ArrayHash;

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
		$defHelper = new ExtensionDefinitionsHelper($this->compiler);
		/** @var ArrayHash $config */
		$config = $this->config;
		$firewalls = [];
		foreach ($config->namespaces as $namespace => $securityConfig) {
			if (is_string($securityConfig)) {
				/** @var ServiceDefinition $firewall */
				$firewall = $defHelper->getDefinitionFromConfig($securityConfig, $this->prefix($namespace . '.firewall'));
				/** @var ServiceDefinition $storage */
				$storage = $defHelper->getDefinitionFromConfig($config->storage, $namespace . '.storage');
				$storage->addSetup('setNamespace', ['namespace' => $namespace]);
				$storage->setAutowired(false);
				$firewall->setArgument('storage', $storage);
				$firewalls[] = $firewall;
				continue;
			}

			/** @var ServiceDefinition $firewall */
			$firewall = $defHelper->getDefinitionFromConfig($securityConfig->firewall, $this->prefix($namespace . '.firewall'));
			/** @var ServiceDefinition $storage */
			$storage = $securityConfig->storage
				? $defHelper->getDefinitionFromConfig($securityConfig->storage, $namespace . '.storage')
				: $defHelper->getDefinitionFromConfig($config->storage, $namespace . '.storage');
			$storage->addSetup('setNamespace', ['namespace' => $namespace]);
			$storage->setAutowired(false);
			$firewall->setArgument('storage', $storage);

			if (isset($securityConfig->validator)) {
				$firewall->setArgument('identityValidator', $defHelper->getDefinitionFromConfig($securityConfig->validator, $namespace . '.validator'));
			}

			if (isset($securityConfig->authorizator)) {
				$builder->addDefinition($this->prefix($namespace . '.authorizatorFactory'))
					->setFactory($securityConfig->authorizator, ['firewall' => $firewall]);
			}

			$firewalls[] = $firewall;
		}

		if ($config->panel) {
			$builder->addDefinition($this->prefix('panel'))
				->setFactory(SecurityPanel::class, $firewalls)
				->setAutowired(false);
		}
	}

	public function afterCompile(ClassType $class): void
	{
		/** @var ArrayHash $config */
		$config = $this->config;
		if ($config->panel) {
			$initialize = $class->getMethod('initialize');
			$initialize->addBody(
				'$this->getService(?)->addPanel($this->getService(?));',
				['tracy.bar', $this->prefix('panel')]
			);
		}
	}

}

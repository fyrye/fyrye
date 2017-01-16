<?php
/**
 * AddPhysicalQuantityPass.php
 * @author fyrye <admin@fyrye.com>
 * @version 2017.01.15
 */
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\DependencyInjection\Compiler;

use PhpUnitsOfMeasure\AbstractPhysicalQuantity;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;

/**
 * {@inheritDoc}
 */
class AddPhysicalQuantityPass implements CompilerPassInterface
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * You can modify the container here before it is dumped to PHP code.
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (
            !$container->hasDefinition('php_units_of_measure.registry_manager') ||
            !class_exists(AbstractPhysicalQuantity::class) ||
            !in_array($container->getParameter('php_units_of_measure.auto'), ['ALL', 'BUNDLES'], true)
        ) {
            return;
        }
        if (!class_exists(Finder::class)) {
            throw new \RuntimeException(
                'You need the symfony/finder component to register PhysicalQuantity objects from bundles.'
            );
        }
        $this->container = $container;
        /** @var array|null $bundles */
        if ($bundles = $container->getParameter('php_units_of_measure.bundles')) {
            $services = $this->getBundleServices($bundles);
            $this->registerServices($services);
        }
    }

    /**
     * @param array $bundles
     * @return array
     */
    private function getBundleServices(array $bundles)
    {
        $bundleMetaData = $this->container->getParameter('kernel.bundles_metadata');
        foreach ($bundles as $bundle) {
            if (
                isset($bundleMetaData[$bundle]['path']) &&
                is_dir($path = $bundleMetaData[$bundle]['path'] . '/PhysicalQuantity')
            ) {
                $this->scanDirectory($bundleMetaData[$bundle]['namespace'], $path);
            }
        }

        return $this->services;
    }

    /**
     * @param array $services
     */
    private function registerServices(array $services)
    {
        foreach ($services as $serviceName) {
            $this->container
                ->getDefinition('php_units_of_measure.registry_manager')
                ->addMethodCall('registerDefinition', [new Reference($serviceName)]);
        }
    }

    /**
     * @param string $namespace
     * @param string $path
     */
    private function scanDirectory($namespace, $path)
    {
        /** @var Finder $files */
        $files = Finder::create()->files()->name('*.php')->in($path);
        foreach ($files as $file) {
            $quantityName = $file->getBasename('.php');
            $class = $namespace . '\\PhysicalQuantity\\' . $quantityName;
            $serviceName = 'php_units_of_measure.quantity.' . $this->normalizeName($quantityName);
            $r = new \ReflectionClass($class);
            if ($r->isSubclassOf(AbstractPhysicalQuantity::class) && !$r->isAbstract()) {
                if ($this->container->has($serviceName)) {
                    $this->container->removeDefinition($serviceName);
                }
                $service = $this->container->register($serviceName, '%php_units_of_measure.quantity_definition.class%');
                $service->setArguments([$quantityName, $class]);
                $this->services[] = $serviceName;
            }
        }
    }

    /**
     * @param string $name
     * @return string
     */
    private function normalizeName($name)
    {
        return preg_replace('[^a-z0-9_]', '', strtolower($name));
    }
}

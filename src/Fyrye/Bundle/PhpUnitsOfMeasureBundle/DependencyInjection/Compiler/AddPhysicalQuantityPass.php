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
        /** @var array|null $bundles */
        if ($bundles = $container->getParameter('php_units_of_measure.bundles')) {
            $bundleMetaData = $container->getParameter('kernel.bundles_metadata');
            $finder = new Finder();
            foreach ($bundles as $bundle) {
                if (!isset($bundleMetaData[$bundle]['path'])) {
                    continue;
                }
                $bundlePath = $bundleMetaData[$bundle]['path'];
                $physicalQuantityPath = $bundlePath . '/PhysicalQuantity';
                if (!is_dir($physicalQuantityPath)) {
                    continue;
                }
                $finder->files()->name('*.php')->in($physicalQuantityPath);
                $this->scanBundle($container, $finder, $bundleMetaData[$bundle]['namespace']);
            }
            foreach ($this->services as $serviceName) {
                $container
                    ->getDefinition('php_units_of_measure.registry_manager')
                    ->addMethodCall('registerDefinition', [new Reference($serviceName)]);
            }
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\Finder\Finder $finder
     * @param $namespace
     */
    private function scanBundle(ContainerBuilder $container, Finder $finder, $namespace)
    {
        foreach ($finder as $file) {
            $quantityName = $file->getBasename('.php');
            $class = $namespace . '\\PhysicalQuantity\\' . $quantityName;
            $serviceName = 'php_units_of_measure.quantity.' . $this->normalizeName($quantityName);
            $r = new \ReflectionClass($class);
            if ($r->isSubclassOf(AbstractPhysicalQuantity::class) && !$r->isAbstract()) {
                if ($container->has($serviceName)) {
                    $container->removeDefinition($serviceName);
                }
                $service = $container->register($serviceName, '%php_units_of_measure.quantity_definition.class%');
                $service->setArguments([$quantityName, $class,]);
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

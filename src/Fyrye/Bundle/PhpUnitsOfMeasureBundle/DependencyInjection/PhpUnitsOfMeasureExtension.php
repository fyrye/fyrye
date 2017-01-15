<?php
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\DependencyInjection;

use Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\ConfiguredPhysicalQuantity;
use Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\QuantityDefinition;
use PhpUnitsOfMeasure\AbstractPhysicalQuantity;
use PhpUnitsOfMeasure\Exception\UnknownUnitOfMeasure;
use PhpUnitsOfMeasure\UnitOfMeasure;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PhpUnitsOfMeasureExtension extends Extension
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        if (true !== $config['enabled']) {
            return;
        }
        if (!class_exists(UnitOfMeasure::class)) {
            throw new \LogicException(
                'PhpUnitsOfMeasureBundle support can not be enabled as the PhpUnitsOfMeasure component is not installed.'
            );
        }
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        foreach ($config['units'] as $quantity => $units) {
            $this->defineQuantity($quantity, $units);
        }
        $container->setParameter('php_units_of_measure.bundles', $config['bundles']);
        $this->container
            ->getDefinition('php_units_of_measure.registry_manager')
            ->addArgument($config['options']);
    }

    /**
     * @param string $name
     * @param array|boolean $units
     */
    public function defineQuantity($name, $units)
    {
        $serviceName = 'php_units_of_measure.quantity.' . $this->normalizeName($name);
        if ($this->container->hasDefinition($serviceName)) {
            //use existing quantity
            throw new \RuntimeException('Physical Quantity: "' . $name . '" is already defined.');
        }
        $service = $this->container->register($serviceName, '%php_units_of_measure.quantity_definition.class%');
        $namespace = $this->container->getParameter('php_units_of_measure.library.namespace');
        /** @var AbstractPhysicalQuantity $physicalQuantity */
        $physicalQuantity = $namespace . '\\' . ucfirst($name);
        $proxy = !class_exists($physicalQuantity);
        $service->setArguments(
            [
                $name,
                $proxy ? $this->container->getParameter(
                    'php_units_of_measure.physical_quantity.class'
                ) : $physicalQuantity,
            ]
        );
        foreach ($units as $unitName => $unit) {
            $type = $proxy ? $unit['type'] : 'linear';
            switch ($type) {
                case 'native':
                    $factor = 1;
                    break;
                case 'linear':
                    $factor = $unit['factor'];
                    break;
                default:
                    throw new \LogicException(
                        'Invalid unit type: "' . $type . '". Expected one of: "native", "linear".'
                    );
            }
            $service->addMethodCall('defineUnit', [$this->normalizeName($unitName), (float)$factor, $unit['aliases']]);
        }
        $this->container
            ->getDefinition('php_units_of_measure.registry_manager')
            ->addMethodCall('registerDefinition', [new Reference($serviceName)]);
    }

    /**
     * @param string $name
     * @return string
     */
    private function normalizeName($name)
    {
        return preg_replace('/[^a-z0-9_]/', '', strtolower($name));
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        $this->container = $container;
        $bundles = $container->getParameter('kernel.bundles');

        return new Configuration(array_keys($bundles));
    }
}

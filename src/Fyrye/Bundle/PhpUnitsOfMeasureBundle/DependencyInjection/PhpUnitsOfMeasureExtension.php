<?php
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\DependencyInjection;

use PhpUnitsOfMeasure\AbstractPhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PhpUnitsOfMeasureExtension extends Extension
{

    const NONE = 1;
    const INTEGRATED = 2;
    const BUNDLES = 4;
    const ALL = self::NONE | self::INTEGRATED | self::BUNDLES;
    /**
     * @var integer
     */
    private $auto = self::ALL;

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
        if (false === $config['twig']) {
            $container->removeDefinition('php_units_of_measure.twig_extension');
        }
        $container->setParameter('php_units_of_measure.auto', $config['auto']);
        $container->setParameter('php_units_of_measure.bundles', $config['bundles']);
        $this->auto = constant('self::' . $config['auto']);
        if ($this->auto === self::BUNDLES) {
            //only bundles was specified
            return;
        }
        if ($this->auto & self::INTEGRATED) {
            //register integrated quantities prior to custom
            $this->registerIntegratedQuantities();
        }
        if ($this->auto & self::NONE) {
            //register config defined units
            foreach ($config['units'] as $quantity => $units) {
                $this->defineQuantity($quantity, $units);
            }
        }
    }

    /**
     * @param string $name
     * @param array $units
     */
    public function defineQuantity($name, $units)
    {
        $serviceName = 'php_units_of_measure.quantity.' . $this->normalizeName($name);
        $namespace = $this->container->getParameter('php_units_of_measure.library.namespace');
        /** @var AbstractPhysicalQuantity $physicalQuantity */
        $physicalQuantity = $namespace . '\\' . ucfirst($name);
        $proxy = !class_exists($physicalQuantity);
        if ($this->container->hasDefinition($serviceName)) {
            //use the defined physical quantity service and add additional units
            $service = $this->container->getDefinition($serviceName);
        } else {
            $service = $this->container->register($serviceName, '%php_units_of_measure.quantity_definition.class%');
            $service->setArguments(
                [
                    $name,
                    $proxy ? $this->container->getParameter(
                        'php_units_of_measure.physical_quantity.class'
                    ) : $physicalQuantity,
                ]
            );
        }
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
     * finds and registers the integrated quantities
     */
    private function registerIntegratedQuantities()
    {
        if (!class_exists(Finder::class)) {
            throw new \RuntimeException(
                'You need the symfony/finder component to register PhysicalQuantity objects from integrated.'
            );
        }
        $r = new \ReflectionClass(AbstractPhysicalQuantity::class);
        $integratedPath = dirname($r->getFileName()) . '/PhysicalQuantity';
        if (is_dir($integratedPath)) {
            $finder = new Finder();
            $finder->files()->name('*.php')->in($integratedPath);
            foreach ($finder as $file) {
                $this->defineQuantity(basename($file, '.php'), []);
            }
        }
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

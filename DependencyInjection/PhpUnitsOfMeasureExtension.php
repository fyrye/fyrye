<?php

namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\DependencyInjection;

use Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\ConfiguredPhysicalQuantity;
use Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\Manager;
use Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\QuantityDefinition;
use PhpUnitsOfMeasure\AbstractPhysicalQuantity;
use PhpUnitsOfMeasure\PhysicalQuantityInterface;
use PhpUnitsOfMeasure\UnitOfMeasure;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class PhpUnitsOfMeasureExtension
 * @package Fyrye\Bundle\PhpUnitsOfMeasureBundle\DependencyInjection
 */
class PhpUnitsOfMeasureExtension extends Extension
{

    use UnitNamingTrait;

    const MANUAL = 1;
    const INTEGRATED = 2;
    const ALL = self::MANUAL | self::INTEGRATED;
    const AUTO = [
        'ALL' => self::ALL,
        'INTEGRATED' => self::INTEGRATED,
        'MANUAL' => self::MANUAL,
    ];

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
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;
        $configuration = new Configuration();
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
        if (false === $config['twig'] ) {
            //symfony tag wil check for twig, so no need to check for twig
            $container->removeDefinition(TwigExtension::class);
        }
        $container->registerForAutoconfiguration(PhysicalQuantityInterface::class)
            ->addTag('php_units_of_measure.extension');
        $this->auto = self::AUTO[$config['auto']];
        $managerDefinition = $this->container->getDefinition(Manager::class);
        if ($this->auto & self::INTEGRATED) {
            //register integrated quantities prior to custom
            $this->registerIntegratedQuantities($managerDefinition);
        }
        if ($this->auto & self::MANUAL) {
            //register config defined units
            foreach ($config['units'] as $quantity => $units) {
                $this->registerQuantityService($managerDefinition, $quantity, $units);
            }
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $managerDefinition
     * @param string $name
     * @param array $units
     */
    public function registerQuantityService(Definition $managerDefinition, $name, $units)
    {
        $serviceName = 'php_units_of_measure.quantity.' . $this->normalizeName($name);
        /** @var AbstractPhysicalQuantity $physicalQuantity */
        $physicalQuantity = 'PhpUnitsOfMeasure\\PhysicalQuantity\\' . ucfirst($name);
        $proxy = !class_exists($physicalQuantity);
        $service = $this->defineService($name, $serviceName, $physicalQuantity, $proxy);
        $this->defineUnits($service, $units, $proxy);
        $managerDefinition->addMethodCall('registerDefinition', [new Reference($physicalQuantity)]);
    }

    /**
     * @param string $name
     * @param string $serviceName
     * @param string $physicalQuantity
     * @param bool $proxy
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function defineService($name, $serviceName, $physicalQuantity, $proxy)
    {
        if ($this->container->hasDefinition($physicalQuantity)) {
            //use the defined physical quantity service and add additional units
            $service = $this->container->getDefinition($physicalQuantity);
        } else {
            $service = $this->container->register($physicalQuantity, QuantityDefinition::class)
                ->setPublic(true);
            $this->container->setAlias($serviceName, $physicalQuantity)
                ->setPublic(true);
            $service->setArguments([
                $name,
                $proxy ? ConfiguredPhysicalQuantity::class : $physicalQuantity,
            ]);
        }

        return $service;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $service
     * @param $units
     * @param bool $proxy
     */
    private function defineUnits(Definition $service, $units, $proxy = false)
    {
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
                        sprintf('Invalid unit type: "%s". Expected one of: "native", "linear".', $type)
                    );
            }
            $service->addMethodCall('defineUnit', [
                $this->normalizeName($unitName),
                (float)$factor,
                $unit['aliases'],
            ]);
        }
    }

    /**
     * finds and registers the integrated quantities
     * @param \Symfony\Component\DependencyInjection\Definition $managerDefinition
     * @throws \ReflectionException
     */
    private function registerIntegratedQuantities(Definition $managerDefinition)
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
                $this->registerQuantityService($managerDefinition, basename($file, '.php'), []);
            }
        }
    }
}

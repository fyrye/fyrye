<?php
/**
 * QuantityDefinition
 * @author fyrye <admin@fyrye.com>
 * @version 2017.01.15
 */
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry;

use PhpUnitsOfMeasure\AbstractPhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

/**
 * Class QuantityDefinition
 * @package Fyrye\Bundle\PhpUnitsOfMeasureBundle
 */
class QuantityDefinition implements QuantityDefinitionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * QuantityDefinition constructor.
     * @param string $name
     * @param string $class
     */
    public function __construct($name, $class)
    {
        $this->name = $name;
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function defineUnit($name, $factor, array $aliases)
    {
        /** @var AbstractPhysicalQuantity $physicalQuantity */
        $physicalQuantity = $this->class;
        if (!$this->hasDefinition($physicalQuantity, $name)) {
            $unitOfMeasure = UnitOfMeasure::linearUnitFactory($name, $factor);
            foreach ($aliases as $alias) {
                $unitOfMeasure->addAlias($alias);
            }
            $physicalQuantity::addUnit($unitOfMeasure);
        }

        return $this;
    }

    /**
     * @param AbstractPhysicalQuantity|string $physicalQuantity
     * @param string $name
     * @return bool
     */
    public function hasDefinition($physicalQuantity, $name)
    {
        /** @var array|UnitOfMeasure[] $definitions */
        $definitions = $physicalQuantity::getUnitDefinitions();
        foreach ($definitions as $unitOfMeasure) {
            if ($unitOfMeasure->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param float $value
     * @param string $name
     * @return \PhpUnitsOfMeasure\AbstractPhysicalQuantity
     */
    public function getUnit($value, $name)
    {
        /** @var AbstractPhysicalQuantity $physicalQuantity */
        $physicalQuantity = new $this->class($value, $name);
        if ($physicalQuantity instanceof ConfiguredPhysicalQuantity) {
            $physicalQuantity->setName($name);
        }

        return $physicalQuantity;
    }

    /**
     * @param Manager $manager
     * @return $this
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }
}

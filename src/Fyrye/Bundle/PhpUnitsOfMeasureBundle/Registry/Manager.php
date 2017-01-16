<?php
/**
 * Registry
 * @author fyrye <admin@fyrye.com>
 * @version 2017.01.15
 */
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry;

/**
 * Class Manager
 * @package Fyrye\Bundle\PhpUnitsOfMeasureBundle
 */
class Manager
{
    /**
     * @var array
     */
    private static $registry = [];

    /**
     * @var static
     */
    private static $instance;

    /**
     * @param array $options
     * @return static
     */
    public static function getInstance(array $options = [])
    {
        if (!self::$instance) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * add a quantity to the registry
     * @param QuantityDefinition $definition
     * @param null|string $name
     * @return $this
     */
    public function registerDefinition(QuantityDefinition $definition, $name = null)
    {
        if (null === $name) {
            $name = strtolower($definition->getName());
        }
        if (!$name) {
            throw new \InvalidArgumentException('Empty quantity definition names are not permitted.');
        }
        $definition->setManager($this);
        self::$registry[$name] = $definition;

        return $this;
    }

    /**
     * retrieve the definition for the specified Physical Quantity
     * @param string $name
     * @return QuantityDefinition
     */
    public function getDefinition($name)
    {
        $definitionName = strtolower($name);
        if (!$this->hasDefinition($definitionName)) {
            throw new \InvalidArgumentException('Unknown Physical Quantity: "' . $name . '"');
        }

        return self::$registry[$definitionName];
    }

    /**
     * determines if a definition exists
     * @param string $name
     * @return bool
     */
    public function hasDefinition($name)
    {
        return isset(self::$registry[$name]);
    }

    /**
     * retrieves the collection of specified physical quantities
     * @return array
     */
    public function getDefinitions()
    {
        return self::$registry;
    }

    /**
     * create a unit for the specified physical quantity
     * @param string $quantity
     * @param float $value
     * @param string $unit
     * @return \PhpUnitsOfMeasure\AbstractPhysicalQuantity
     */
    public function getUnit($quantity, $value, $unit)
    {
        return $this->getDefinition($quantity)->getUnit($value, $unit);
    }
}

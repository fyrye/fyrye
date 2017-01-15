<?php
/**
 * QuantityDefinitionInterface
 * @author fyrye <admin@fyrye.com>
 * @version 2017.01.15
 */
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry;

/**
 * Interface QuantityDefinitionInterface
 * @package Fyrye\Bundle\PhpUnitsOfMeasureBundle
 */
interface QuantityDefinitionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @param string $name
     * @param float $factor
     * @param array $aliases
     * @return $this
     */
    public function defineUnit($name, $factor, array $aliases);

    /**
     * @param float $value
     * @param string $name
     * @return \PhpUnitsOfMeasure\AbstractPhysicalQuantity
     */
    public function getUnit($value, $name);

    /**
     * @param Manager $manager
     * @return $this
     */
    public function setManager(Manager $manager);

    /**
     * @return Manager
     */
    public function getManager();
}

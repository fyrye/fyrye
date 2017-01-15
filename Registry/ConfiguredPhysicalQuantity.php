<?php
/**
 * ConfiguredPhysicalQuantity.php
 * @author fyrye <admin@fyrye.com>
 * @version 2017.01.15
 */
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry;

use PhpUnitsOfMeasure\AbstractPhysicalQuantity;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

/**
 * {@inheritDoc}
 */
class ConfiguredPhysicalQuantity extends AbstractPhysicalQuantity
{
    use HasSIUnitsTrait;

    /**
     * @var array
     */
    protected static $unitDefinitions;

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

}

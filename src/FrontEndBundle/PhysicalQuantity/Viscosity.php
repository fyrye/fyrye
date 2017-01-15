<?php
/**
 * Viscosity.php
 * @author fyrye <admin@fyrye.com>
 * @version 2017.01.15
 */
namespace FrontEndBundle\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractPhysicalQuantity;
use PhpUnitsOfMeasure\HasSIUnitsTrait;
use PhpUnitsOfMeasure\UnitOfMeasure;

/**
 * {@inheritDoc}
 */
class Viscosity extends AbstractPhysicalQuantity
{

    protected static $unitDefinitions;

    use HasSIUnitsTrait;

    /**
     * {@inheritDoc}
     */
    protected static function initialize()
    {
        $cst = UnitOfMeasure::nativeUnitFactory('cst');
        $cst->addAlias('centistokes');
        static::addUnit($cst);
    }
}

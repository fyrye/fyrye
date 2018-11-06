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

    use HasSIUnitsTrait;

    protected static $unitDefinitions;

    /**
     * {@inheritDoc}
     */
    protected static function initialize()
    {
        $cst = UnitOfMeasure::nativeUnitFactory('centistoke');
        $cst->addAlias('cst');
        static::addUnit($cst);
        static::addMissingSIPrefixedUnits(
            $cst,
            1,
            '%pcst',
            [
                '%Pcentistoke',
            ]
        );
        $stoke = UnitOfMeasure::linearUnitFactory('stoke', 0.01);
        $stoke->addAlias('s');
        static::addUnit($stoke);
    }
}

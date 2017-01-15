<?php
/**
 * TwigExtension.php
 * @author fyrye <admin@fyrye.com>
 * @version 2017.01.14
 */
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\Twig;

use Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\Manager;
use Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\QuantityDefinition;
use PhpUnitsOfMeasure\PhysicalQuantityInterface;

class TwigExtension extends \Twig_Extension
{
    /**
     * @var array|\Twig_SimpleFilter[]
     */
    private $filters = [];

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param array|QuantityDefinition[] $definitions
     */
    protected function setDefinitions($definitions)
    {
        foreach ($definitions as $name => $definition) {
            $quantity = $name;
            $this->filters[] = new \Twig_SimpleFilter(
                'uom_' . $quantity,
                function ($value, $from, $to) use ($definition) {
                    return $definition->getUnit($value, $from)->toUnit($to);
                }
            );
        }
    }

    /**
     * @param Manager $manager
     * @return \Twig_SimpleFilter
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
        $this->setDefinitions($manager->getDefinitions());
    }

    /**
     * @param null|string $quantity
     * @param null|float $value
     * @param null|string $unit
     * @return Manager|PhysicalQuantityInterface
     */
    public function getManager($quantity = null, $value = null, $unit = null)
    {
        if (null === $quantity) {
            return $this->manager;
        }

        return $this->manager->getUnit($quantity, $value, $unit);
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('uom', [$this, 'getManager']),
        ];
    }

}

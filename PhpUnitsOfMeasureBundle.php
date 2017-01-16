<?php
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle;

use Fyrye\Bundle\PhpUnitsOfMeasureBundle\DependencyInjection\Compiler\AddPhysicalQuantityPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * {@inheritDoc}
 */
class PhpUnitsOfMeasureBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddPhysicalQuantityPass());
    }
}

<?php
namespace Fyrye\Bundle\PhpUnitsOfMeasureBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    private $bundles;

    /**
     * Constructor
     *
     * @param array $bundles An array of bundle names
     */
    public function __construct(array $bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('php_units_of_measure');
        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->info('Determines whether or not to use the service')
                    ->defaultTrue()
                ->end()
                ->enumNode('auto')
                    ->info('Allow for the manager to automatically register undefined Physical Quantities by bundles, integrated, all or none')
                    ->defaultValue('all')
                    ->values(['ALL', 'BUNDLES', 'INTEGRATED', 'NONE'])
                    ->treatNullLike('all')
                    ->beforeNormalization()
                        ->always(function($v){ return strtoupper($v); })
                    ->end()
                ->end()
                ->booleanNode('twig')
                    ->info('Control the use of the twig extension')
                    ->defaultTrue()
                    ->treatNullLike(true)
                ->end()
                ->arrayNode('bundles')
                    ->info('Listing of Bundles to search for custom Physical Quantities.')
                    ->defaultValue($this->bundles)
                    ->treatNullLike($this->bundles)
                    ->prototype('scalar')
                        ->validate()
                            ->ifNotInArray($this->bundles)
                            ->thenInvalid('%s is not a valid bundle.')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('units')
                    ->info('Extend the integrated units with additional units, Like: Time, Length. If a physical quantity does not exist it will be created.')
                    ->prototype('array')
                        ->info('The physical quantity name to configure.')
                        ->prototype('array')
                            ->info('The unit configuration to apply to the specified physical quantity.')
                            ->children()
                                ->enumNode('type')
                                    ->info('Only native and linear units can be defined here. Native means factor=1, while linear would be any factor!=1')
                                    ->defaultValue('linear')
                                    ->values(['native', 'linear'])
                                ->end()
                                ->scalarNode('factor')
                                    ->validate()
                                        ->ifTrue(function($v){ return !is_numeric($v); })
                                        ->thenInvalid('%s must be a numeric value.')
                                    ->end()
                                    ->info('This factor has to be numeric and will be used to convert this unit to the native one.')
                                    ->defaultValue(1)
                                ->end()
                                ->arrayNode('aliases')
                                    ->beforeNormalization()
                                        ->ifString()->then(function($v){ return [$v]; })
                                    ->end()
                                    ->info('Define a list of possible aliases here, like "meter" could have [m, metre, metere]')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

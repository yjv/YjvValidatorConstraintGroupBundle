<?php
namespace Yjv\Bundle\ValidatorConstraintGroupBundle;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Yjv\Bundle\ValidatorConstraintGroupBundle\DependencyInjection\YjvValidatorConstraintGroupExtension;
use Yjv\Bundle\ValidatorConstraintGroupBundle\Tests\Fixtures\Bundle1\Bundle as Bundle1;
use Yjv\Bundle\ValidatorConstraintGroupBundle\Tests\Fixtures\Bundle2\Bundle as Bundle2;


/**
 * Created by PhpStorm.
 * User: yosefderay
 * Date: 2/16/14
 * Time: 11:12 PM
 */
class YjvValidatorConstraintGroupExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    public function testLoad()
    {
        $extension = new YjvValidatorConstraintGroupExtension();
        $container = new ContainerBuilder();
        $container->setParameter('kernel.bundles', array(
            'bundle1' => new Bundle1(),
            'bundle2' => new Bundle2()
        ));

        $extension->load(array(), $container);
        $this->assertContains(
            new FileResource(__DIR__.'/../Resources/config/services.yml'),
            $container->getResources(),
            '',
            false,
            false
        );
        $this->assertContains(
            new FileResource(__DIR__.'/Fixtures/Bundle1/Resources/config/validator_constraint_groups.yml'),
            $container->getResources(),
            '',
            false,
            false
        );
        $this->assertContains(
            array('addLoader', array(new Reference('yjv.validator_constraint_group.group_factory.loader.bundle1'))),
            $container->getDefinition('yjv.validator_constraint_group.group_factory')->getMethodCalls(),
            '',
            false,
            false
        );
        $loader = new Definition();
        $loader
            ->setClass($container->getParameter('yjv.validator_constraint_group.yaml_group_loader.class'))
            ->setArguments(array(realpath(__DIR__.'/Fixtures/Bundle1/Resources/config/validator_constraint_groups.yml')))
        ;
        $this->assertEquals($loader, $container->getDefinition('yjv.validator_constraint_group.group_factory.loader.bundle1'));
        $this->assertFalse($container->hasDefinition('yjv.validator_constraint_group.group_factory.loader.bundle2'));
    }
} 
<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Tools\Di\Code\Generator;

class InterceptionConfigurationBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Di\Code\Generator\InterceptionConfigurationBuilder
     */
    protected $model;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $interceptionConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pluginList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeReader;

    protected function setUp()
    {
        $this->interceptionConfig = $this->getMock(
            'Magento\Framework\Interception\Config\Config',
            ['hasPlugins'],
            [],
            '',
            false
        );
        $this->pluginList = $this->getMock(
            'Magento\Tools\Di\Code\Generator\PluginList',
            ['setInterceptedClasses', 'setScopePriorityScheme', 'getPluginsConfig'],
            [],
            '',
            false
        );
        $this->typeReader = $this->getMock('Magento\Tools\Di\Code\Reader\Type', ['isConcrete'], [], '', false);
        $this->model = new \Magento\Tools\Di\Code\Generator\InterceptionConfigurationBuilder(
            $this->interceptionConfig,
            $this->pluginList,
            $this->typeReader
        );
    }

    /**
     * @dataProvider getInterceptionConfigurationDataProvider
     */
    public function testGetInterceptionConfiguration($plugins)
    {
        $definedClasses = ['Class1'];
        $this->interceptionConfig->expects($this->once())
            ->method('hasPlugins')
            ->with('Class1')
            ->willReturn(true);
        $this->typeReader->expects($this->any())
            ->method('isConcrete')
            ->willReturnMap([
                ['Class1', true],
                ['instance', true]
            ]);
        $this->pluginList->expects($this->once())
            ->method('setInterceptedClasses')
            ->with($definedClasses);
        $this->pluginList->expects($this->once())
            ->method('setScopePriorityScheme')
            ->with(['global', 'areaCode']);
        $this->pluginList->expects($this->once())
            ->method('getPluginsConfig')
            ->willReturn(['instance' => $plugins]);

        $this->model->addAreaCode('areaCode');
        $this->model->getInterceptionConfiguration($definedClasses);
    }

    /**
     * @return array
     */
    public function getInterceptionConfigurationDataProvider()
    {
        return [
            [null],
            [['plugin' => ['instance' => 'someinstance']]],
            [['plugin' => ['instance' => 'someinstance'], 'plugin2' => ['instance' => 'someinstance']]]
        ];
    }
}

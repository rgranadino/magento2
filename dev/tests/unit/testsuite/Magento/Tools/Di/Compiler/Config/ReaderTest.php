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
namespace Magento\Tools\Di\Compiler\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Di\Compiler\Config\Reader
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $diContainerConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configLoader;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $argumentsResolverFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $argumentsResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $classReaderDecorator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeReader;

    protected function setUp()
    {
        $this->diContainerConfig = $this->getMock('Magento\Framework\ObjectManager\ConfigInterface', [], [], '', false);
        $this->configLoader = $this->getMock('Magento\Framework\App\ObjectManager\ConfigLoader', [], [], '', false);
        $this->argumentsResolverFactory = $this->getMock(
            'Magento\Tools\Di\Compiler\ArgumentsResolverFactory',
            [],
            [],
            '',
            false
        );
        $this->argumentsResolver= $this->getMock('Magento\Tools\Di\Compiler\ArgumentsResolver', [], [], '', false);
        $this->classReaderDecorator = $this->getMock(
            'Magento\Tools\Di\Code\Reader\ClassReaderDecorator',
            [],
            [],
            '',
            false
        );
        $this->typeReader = $this->getMock('Magento\Tools\Di\Code\Reader\Type', [], [], '', false);

        $this->model = new \Magento\Tools\Di\Compiler\Config\Reader(
            $this->diContainerConfig,
            $this->configLoader,
            $this->argumentsResolverFactory,
            $this->classReaderDecorator,
            $this->typeReader
        );
    }

    /**
     * @dataProvider generateCachePerScopeDataProvider
     */
    public function testGenerateCachePerScope($extendConfig)
    {
        $definitionsCollection = $this->getMock('Magento\Tools\Di\Definition\Collection', [], [], '', false);
        if ($extendConfig) {
            $this->diContainerConfig->expects($this->once())
                ->method('extend')
                ->with([]);
            $this->configLoader->expects($this->once())
                ->method('load')
                ->with('areaCode')
                ->willReturn([]);
        }
        $this->argumentsResolverFactory->expects($this->once())
            ->method('create')
            ->with($this->diContainerConfig)
            ->willReturn($this->argumentsResolver);
        $definitionsCollection->expects($this->exactly(2))
            ->method('getInstancesNamesList')
            ->willReturn(['instanceType1'], ['instanceType2']);
        $definitionsCollection->expects($this->once())
            ->method('getInstanceArguments')
            ->willReturnMap([
                ['instanceType1', null],
                ['instanceType2', ['arg1', 'arg2']]
            ]);
        $this->typeReader->expects($this->exactly(3))
            ->method('isConcrete')
            ->willReturnMap([
                ['instanceType1', true],
                ['instanceType2', false],
                ['originalType1', true],
                ['originalType2', false]
            ]);
        $this->argumentsResolver->expects($this->exactly(2))
            ->method('getResolvedConstructorArguments')
            ->willReturnMap([
                ['instanceType1', 'resolvedConstructor1'],
                ['instanceVirtualType1', 'resolvedConstructor2']
            ]);
        $this->diContainerConfig->expects($this->exactly(2))
            ->method('getVirtualTypes')
            ->willReturn(['instanceVirtualType1' => 1, 'instanceVirtualType2' => 2]);
        $this->diContainerConfig->expects($this->exactly(4))
            ->method('getInstanceType')
            ->willReturnMap([
                ['instanceVirtualType1', 'originalType1'],
                ['instanceVirtualType2', 'originalType2']
            ]);
        $definitionsCollection->expects($this->exactly(2))
            ->method('hasInstance')
            ->willReturn('');
        $this->classReaderDecorator->expects($this->once())
            ->method('getConstructor')
            ->willReturn('constructor');
        $this->diContainerConfig->expects($this->once())
            ->method('isShared')
            ->willReturnMap([
                ['instanceType1', true],
                ['instanceType2', false]
            ]);
        $this->diContainerConfig->expects($this->once())
            ->method('getPreference')
            ->willReturnMap([
                ['instanceType1', 'instanceType1ss'],
                ['instanceType2', 'instanceType2']
            ]);
        $this->model->generateCachePerScope($definitionsCollection, 'areaCode', $extendConfig);
    }

    /**
     * @return array
     */
    public function generateCachePerScopeDataProvider()
    {
        return [
            [true],
            [false]
        ];
    }
}

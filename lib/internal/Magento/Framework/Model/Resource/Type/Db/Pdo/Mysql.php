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
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Framework\Model\Resource\Type\Db\Pdo;

use Magento\Framework\App\Resource\ConnectionAdapterInterface;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Model\Resource\Type\Db;

class Mysql extends Db implements ConnectionAdapterInterface
{
    /**
     * @var \Magento\Framework\Stdlib\String
     */
    protected $string;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var array
     */
    protected $_connectionConfig;

    /**
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param array $config
     */
    public function __construct(
        \Magento\Framework\Stdlib\String $string,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        array $config
    ) {
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->_connectionConfig = $this->getValidConfig($config);

        parent::__construct();
    }

    /**
     * Get connection
     *
     * @param LoggerInterface|null $logger
     * @return \Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    public function getConnection(LoggerInterface $logger)
    {
        if (!$this->_connectionConfig['active']) {
            return null;
        }

        $connection = $this->_getDbAdapterInstance($logger);
        if (!empty($this->_connectionConfig['initStatements']) && $connection) {
            $connection->query($this->_connectionConfig['initStatements']);
        }

        $profiler = $connection->getProfiler();
        if ($profiler instanceof \Magento\Framework\DB\Profiler) {
            $profiler->setType($this->_connectionConfig['type']);
            $profiler->setHost($this->_connectionConfig['host']);
        }

        return $connection;
    }

    /**
     * Create and return DB adapter object instance
     *
     * @param LoggerInterface $logger
     * @return \Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    protected function _getDbAdapterInstance(LoggerInterface $logger)
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className($this->string, $this->dateTime, $logger, $this->_connectionConfig);
        return $adapter;
    }

    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Magento\Framework\DB\Adapter\Pdo\Mysql';
    }

    /**
     * Validates the config and adds default options, if any is missing
     *
     * @param array $config
     * @return array
     */
    private function getValidConfig(array $config)
    {
        $default = ['initStatements' => 'SET NAMES utf8', 'type' => 'pdo_mysql', 'active' => false];
        foreach ($default as $key => $value) {
            if (!isset($config[$key])) {
                $config[$key] = $value;
            }
        }
        $required = ['host'];
        foreach ($required as $name) {
            if (!isset($config[$name])) {
                throw new \InvalidArgumentException("MySQL adapter: Missing required configuration option '$name'");
            }
        }

        $config['active'] = !(
            $config['active'] === 'false'
            || $config['active'] === false
            || $config['active'] === '0'
        );

        return $config;
    }
}

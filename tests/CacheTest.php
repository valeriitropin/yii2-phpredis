<?php

namespace ValeriiTropin\redis\tests;

use ValeriiTropin\redis\Cache;
use ValeriiTropin\redis\Connection;

class CacheTest extends TestCase
{
    private $_cacheInstance = null;

    /**
     * @return Cache
     */
    protected function getCache()
    {
        $params = self::getParam();
        $connection = new Connection($params);
        $this->mockApplication(['components' => ['redis' => $connection]]);
        if ($this->_cacheInstance === null) {
            $this->_cacheInstance = new Cache();
        }

        return $this->_cacheInstance;
    }


    public function testSetAndGet()
    {
        $cache = $this->getCache();
        $key = 'key';
        $value = 'value';
        $this->assertTrue($cache->set($key, $value));
        $this->assertTrue($cache->get($key) == $value);
    }

    public function testDelete()
    {
        $cache = $this->getCache();
        $key = 'key';
        $this->assertTrue($cache->delete($key));
        $this->assertFalse($cache->get($key));
    }

    public function testMultiSetAndMultiGet()
    {
        $cache = $this->getCache();

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $this->assertTrue($cache->multiSet($data, 1));
        $this->assertTrue($data === $cache->multiGet(array_keys($data)));
    }

    public function testFlushValues()
    {
        $cache = $this->getCache();
        $cache->flush();
        $this->assertFalse($cache->get('key1'));
    }
}

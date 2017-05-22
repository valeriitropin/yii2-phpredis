<?php

namespace ValeriiTropin\redis\tests;

use ValeriiTropin\redis\Connection;
use ValeriiTropin\redis\Mutex;

class MutexTest extends TestCase
{
    protected $mutex;

    protected function getMutex()
    {
        $params = self::getParam();
        $connection = new Connection($params);
        $connection->open();
        $this->mockApplication(['components' => ['redis' => $connection]]);
        if ($this->mutex === null) {
            $this->mutex = new Mutex();
        }

        return $this->mutex;
    }

    public function testAcquire()
    {
        $lockName = 'test-lock';
        $mutex = $this->getMutex();
        $this->assertTrue($mutex->acquire($lockName, 1));
        $this->assertFalse($mutex->acquire($lockName, 1));
        $this->assertTrue($mutex->release($lockName));
        $this->assertTrue($mutex->acquire($lockName, 1));
        $this->assertTrue($mutex->release($lockName));
    }
}

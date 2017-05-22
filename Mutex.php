<?php

namespace ValeriiTropin\redis;

use yii\di\Instance;

class Mutex extends \yii\mutex\Mutex
{
    /**
     * @var string|array|Connection
     */
    public $connection = 'redis';

    public function init()
    {
        parent::init();
        $this->connection = Instance::ensure($this->connection, Connection::className());
        $this->connection->open();
    }

    protected function acquireLock($name, $timeout = 0)
    {
        return $this->connection->add($name, true, $timeout);
    }

    protected function releaseLock($name)
    {
        return $this->connection->delete($name);
    }
}

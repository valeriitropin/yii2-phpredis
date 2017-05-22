<?php

namespace ValeriiTropin\redis;

use yii\di\Instance;

class Cache extends \yii\caching\Cache
{
    public $serializer;

    /**
     * @var string|array|Connection
     */
    public $connection = 'redis';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->connection = Instance::ensure($this->connection, Connection::className());
        $this->connection->open();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function getValue($key)
    {
        return $this->connection->get($key);
    }

    /**
     * @inheritdoc
     */
    protected function setValue($key, $value, $duration)
    {
        $options = [];
        if ($duration) {
            $options['nx'] = $duration;
        }
        return $this->connection->set($key, $value, $options);
    }

    /**
     * @inheritdoc
     */
    protected function addValue($key, $value, $duration)
    {
        return $this->connection->add($key, $value, $duration);
    }

    /**
     * @inheritdoc
     */
    protected function deleteValue($key)
    {
        return (bool) $this->connection->delete($key);
    }

    /**
     * @inheritdoc
     */
    protected function flushValues()
    {
        return $this->connection->flushDB();
    }

    /**
     * @inheritdoc
     */
    protected function getValues($keys)
    {
        $response = $this->connection->mget($keys);
        $result = [];
        $i = 0;
        foreach ($keys as $key) {
            $result[$key] = $response[$i++];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function setValues($data, $duration)
    {
        $result = $this->connection->mset($data);
        if ($result && $duration) {
            $duration = (int)($duration * 1000);
            $this->connection->multi();
            foreach ($data as $key => $value) {
                $this->connection->expire($key, $duration);
            }
            $this->connection->exec();
        }

        return $result;
    }

    protected function addValues($data, $duration)
    {
        $result = $this->connection->msetnx($data);
        if ($result && $duration) {
            $duration = (int)($duration * 1000);
            $this->connection->multi();
            foreach ($data as $key => $value) {
                $this->connection->expire($key, $duration);
            }
            $this->connection->exec();
        }

        return $result;
    }
}

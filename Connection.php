<?php

namespace ValeriiTropin\redis;

use Redis;
use yii\base\Component;
use RedisException;
use yii\db\Exception;

/**
 * Class Connection
 * @package ValeriiTropin\redis
 *
 * @link https://redis.io/commands
 * @link https://github.com/phpredis/phpredis
 *
 */
class Connection extends Component
{
    const EVENT_AFTER_OPEN = 'afterOpen';
    const EVENT_AFTER_CLOSE = 'afterClose';

    /**
     * @var string
     */
    public $hostname = 'localhost';

    /**
     * @var int
     */
    public $port = 6379;

    /**
     * @var
     */
    public $unixSocket;

    /**
     * @var string
     */
    public $password;

    /**
     * @var int
     */
    public $database = 0;

    /**
     * @var float
     */
    public $connectionTimeout = 0.0;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var Redis
     */
    protected $connection;

    /**
     * List of php redis methods
     * @var array
     */
    protected $redisCommands = [
        'append' => true,
        'auth' => true,
        'bgSave' => true,
        'bgrewriteaof' => true,
        'bitcount' => true,
        'bitop' => true,
        'bitpos' => true,
        'blPop' => true,
        'brPop' => true,
        'brpoplpush' => true,
        'client' => true,
        'command' => true,
        'config' => true,
        'dbSize' => true,
        'debug' => true,
        'decr' => true,
        'decrBy' => true,
        'del' => true,
        'delete' => true,
        'discard' => true,
        'dump' => true,
        'echo' => true,
        'eval' => true,
        'evalsha' => true,
        'evaluate' => true,
        'evaluateSha' => true,
        'exec' => true,
        'exists' => true,
        'expire' => true,
        'expireAt' => true,
        'flushAll' => true,
        'flushDB' => true,
        'geoadd' => true,
        'geodist' => true,
        'geohash' => true,
        'geopos' => true,
        'georadius' => true,
        'georadiusbymember' => true,
        'get' => true,
        'getAuth' => true,
        'getBit' => true,
        'getDBNum' => true,
        'getHost' => true,
        'getKeys' => true,
        'getLastError' => true,
        'getMode' => true,
        'getMultiple' => true,
        'getOption' => true,
        'getPersistentID' => true,
        'getPort' => true,
        'getRange' => true,
        'getReadTimeout' => true,
        'getSet' => true,
        'getTimeout' => true,
        'hDel' => true,
        'hExists' => true,
        'hGet' => true,
        'hGetAll' => true,
        'hIncrBy' => true,
        'hIncrByFloat' => true,
        'hKeys' => true,
        'hLen' => true,
        'hMget' => true,
        'hMset' => true,
        'hSet' => true,
        'hSetNx' => true,
        'hVals' => true,
        'hscan' => true,
        'incr' => true,
        'incrBy' => true,
        'incrByFloat' => true,
        'info' => true,
        'isConnected' => true,
        'keys' => true,
        'lGet' => true,
        'lGetRange' => true,
        'lInsert' => true,
        'lLen' => true,
        'lPop' => true,
        'lPush' => true,
        'lPushx' => true,
        'lRemove' => true,
        'lSet' => true,
        'lSize' => true,
        'lastSave' => true,
        'lindex' => true,
        'listTrim' => true,
        'lrange' => true,
        'lrem' => true,
        'ltrim' => true,
        'mget' => true,
        'migrate' => true,
        'move' => true,
        'mset' => true,
        'msetnx' => true,
        'multi' => true,
        'object' => true,
        'persist' => true,
        'pexpire' => true,
        'pexpireAt' => true,
        'pfadd' => true,
        'pfcount' => true,
        'pfmerge' => true,
        'ping' => true,
        'pipeline' => true,
        'popen' => true,
        'psetex' => true,
        'psubscribe' => true,
        'pttl' => true,
        'publish' => true,
        'pubsub' => true,
        'punsubscribe' => true,
        'rPop' => true,
        'rPush' => true,
        'rPushx' => true,
        'randomKey' => true,
        'rawcommand' => true,
        'rename' => true,
        'renameKey' => true,
        'renameNx' => true,
        'restore' => true,
        'role' => true,
        'rpoplpush' => true,
        'sAdd' => true,
        'sAddArray' => true,
        'sContains' => true,
        'sDiff' => true,
        'sDiffStore' => true,
        'sGetMembers' => true,
        'sInter' => true,
        'sInterStore' => true,
        'sMembers' => true,
        'sMove' => true,
        'sPop' => true,
        'sRandMember' => true,
        'sRemove' => true,
        'sSize' => true,
        'sUnion' => true,
        'sUnionStore' => true,
        'save' => true,
        'scan' => true,
        'scard' => true,
        'script' => true,
        'select' => true,
        'set' => true,
        'setBit' => true,
        'setOption' => true,
        'setRange' => true,
        'setTimeout' => true,
        'setex' => true,
        'setnx' => true,
        'sismember' => true,
        'slaveof' => true,
        'slowlog' => true,
        'sort' => true,
        'sortAsc' => true,
        'sortAscAlpha' => true,
        'sortDesc' => true,
        'sortDescAlpha' => true,
        'srem' => true,
        'sscan' => true,
        'strlen' => true,
        'subscribe' => true,
        'substr' => true,
        'time' => true,
        'ttl' => true,
        'type' => true,
        'unsubscribe' => true,
        'unwatch' => true,
        'wait' => true,
        'watch' => true,
        'zAdd' => true,
        'zCard' => true,
        'zCount' => true,
        'zDelete' => true,
        'zDeleteRangeByRank' => true,
        'zDeleteRangeByScore' => true,
        'zIncrBy' => true,
        'zInter' => true,
        'zLexCount' => true,
        'zRange' => true,
        'zRangeByLex' => true,
        'zRangeByScore' => true,
        'zRank' => true,
        'zRem' => true,
        'zRemRangeByLex' => true,
        'zRemRangeByRank' => true,
        'zRemRangeByScore' => true,
        'zRemove' => true,
        'zRemoveRangeByScore' => true,
        'zRevRange' => true,
        'zRevRangeByLex' => true,
        'zRevRangeByScore' => true,
        'zRevRank' => true,
        'zReverseRange' => true,
        'zScore' => true,
        'zSize' => true,
        'zUnion' => true,
        'zinterstore' => true,
        'zscan' => true,
        'zunionstore' => true,
    ];

    /**
     * Establishes a DB connection.
     * It does nothing if a DB connection has already been established.
     * @throws RedisException if connection fails
     */
    public function open()
    {
        if ($this->connection) {
            return;
        }

        $this->connection = new Redis();

        try {
            if ($this->connection) {
                if ($this->unixSocket !== null) {
                    $this->connection->connect($this->unixSocket);
                } else {
                    $this->connection->connect($this->hostname, $this->port, $this->connectionTimeout);
                }

                if (!$this->connection->isConnected()) {
                    throw new RedisException();
                }

                if ($this->password !== null) {
                    $this->connection->auth($this->password);
                }

                if ($this->database !== null) {
                    $this->connection->select($this->database);
                }

                foreach ($this->options as $optionName => $option) {
                    $this->connection->setOption($optionName, $option);
                }

                $this->initConnection();
            }
        } catch (RedisException $redisException) {
            throw new Exception(
                $redisException->getMessage(),
                $redisException->getMessage(),
                $redisException->getCode(),
                $redisException
            );
        }
    }

    public function close()
    {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
            $this->trigger(self::EVENT_AFTER_CLOSE);
        }
    }

    protected function initConnection()
    {
        $this->trigger(self::EVENT_AFTER_OPEN);
    }

    public function __call($name, $params)
    {
        $this->open();
        if (isset($this->redisCommands[$name])) {
            return call_user_func_array([$this->connection, $name], $params);
        } else {
            return parent::__call($name, $params);
        }
    }

    public function add($key, $value, $timeout)
    {
        $this->open();
        return $this->connection->set($key, $value, ['nx', 'ex' => $timeout]);
    }

    public function isConnected()
    {
        return $this->connection && $this->connection->isConnected();
    }
}

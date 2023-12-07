<?php

namespace src\Util;

class Memcached
{
    protected $mem;

    protected $prefix;
    protected $host;
    protected $port;

    public function __construct($mem_config)
    {
        $this->Initialize($mem_config);
        $this->mem = new \Memcached();
        $this->mem->addServer($this->host, $this->port) || die('There is no memcached. ');
    }

    public function Initialize($mem_config)
    {
        $this->prefix = $mem_config['prefix'];
        $this->host = $mem_config['host'];
        $this->port = $mem_config['port'];
    }

    public function Set($key, $value, $time = 0)
    {
        $this->mem->set($this->prefix . $key, $value, $time);

        return $value;
    }

    public function Get($key)
    {
        return $this->mem->get($this->prefix . $key);
    }

    public function Delete($key): bool
    {
        return $this->mem->delete($this->prefix . $key);
    }

    public function Enqueue($queue, $item)
    {
        $id = $this->mem->increment($this->prefix . 'HT_' . $queue);

        if ($id === false) {
            if ($this->mem->add($this->prefix . 'HT_' . $queue, 1) === false) {
                $id = $this->mem->increment($this->prefix . 'HT_' . $queue);
                if ($id === false) {
                    return false;
                }
            } else {
                $id = 1;
                $this->mem->add($this->prefix . 'HQ_' . $queue, $id);
            }
        }

        if ($this->mem->add($this->prefix . 'HI_' . $queue . '_' . $id, $item) === false) {
            return false;
        }

        return $id;
    }

    public function Dequeue($queue)
    {
        $tail = $this->mem->get($this->prefix . 'HT_' . $queue);

        if (($id = $this->mem->increment($this->prefix . 'HQ_' . $queue)) === false) {
            return false;
        }

        if (($id - 1) <= $tail) {
            return $this->mem->get($this->prefix . 'HI_' . $queue . '_' . ($id - 1));
        }
        $this->mem->decrement($this->prefix . 'HQ_' . $queue);

        return false;
    }

    public function Stats()
    {
        return $this->mem->getStats();
    }

    public function Flush()
    {
        $this->mem->flush();
    }
}

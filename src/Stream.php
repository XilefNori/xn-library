<?php

namespace XnLibrary;

class Stream
{
    /**
     * @param $fd
     * @param $data
     *
     * @return bool
     * @throws Exception\RuntimeException
     */
    public static function non_block_read($fd, &$data): bool
    {
        $read   = array($fd);
        $write  = array();
        $except = array();
        $result = stream_select($read, $write, $except, 0);

        if ($result === false) {
            throw new Exception\RuntimeException('stream_select failed');
        }

        if ($result === 0) {
            return false;
        }

        $data = fread($fd, 1);

        return true;
    }
}

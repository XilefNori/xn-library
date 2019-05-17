<?php

namespace XnLibrary;

use XnLibrary\Exception\AssertException;

class TmpFile
{
    protected $base_tmp_dir;

    /**
     * @return TmpFile
     */
    public static function getInstance()
    {
        static $instance;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    public function __construct($base_tmp_dir = null)
    {
        $this->base_tmp_dir = $base_tmp_dir;
    }


    public function makeTmpFilename($prefix = null, $extension = 'tmp')
    {
        $tmp_dir      = $this->base_tmp_dir;
        $tempfile     = tempnam($tmp_dir, strtolower($prefix) . '~');
        $tempfile_new = $tempfile . '.' . $extension;
        rename($tempfile, $tempfile_new);

        return $tempfile_new;
    }

    public function makeTmpDirname($prefix = null, $mode = 0777)
    {
        $tempfile = tempnam($this->base_tmp_dir, strtolower($prefix) . '~');
        $dirname  = $tempfile . '.d';
        if (!mkdir($dirname, $mode, true) && !is_dir($dirname)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dirname));
        }

        unlink($tempfile);

        if (!is_dir($dirname)) {
            throw new AssertException('Cannot create dir: ' . $dirname);
        }

        return $dirname;
    }
}

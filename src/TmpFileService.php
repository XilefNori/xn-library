<?php

namespace XnLibrary;

use XnLibrary\Exception\AssertException;

class TmpFileService
{
    protected $base_tmp_dir;
    /** @var int */
    private $file_mode;
    /** @var int */
    private $dir_mode;

    /**
     * @return TmpFileService
     */
    public static function getGlobalInstance($base_tmp_dir, $file_mode = null, $dir_mode = null)
    {
        static $instance;

        if (!$instance) {
            $instance = new self($base_tmp_dir, $file_mode, $dir_mode);
        }

        return $instance;
    }

    public function __construct($base_tmp_dir = null, $file_mode = null, $dir_mode = 0755)
    {
        $this->base_tmp_dir = $base_tmp_dir;
        $this->file_mode    = $file_mode;
        $this->dir_mode     = $dir_mode;
    }


    public function makeTmpFilename($extension = 'tmp', $prefix = null, $mode = null)
    {
        $tmp_dir      = $this->getTempBaseDir();
        $tempfile     = tempnam($tmp_dir, strtolower($prefix) . '~');
        $tempfile_new = $tempfile . '.' . $extension;
        rename($tempfile, $tempfile_new);

        $mode = $mode ?? $this->file_mode;

        if ($mode) {
            chmod($tempfile_new, $this->file_mode);
        }

        return $tempfile_new;
    }

    public function makeTmpDirname($mode = null, $prefix = null)
    {
        $tempfile = tempnam($this->getTempBaseDir(), strtolower($prefix) . '~');
        $dirname  = $tempfile . '.d';
        unlink($tempfile);

        if (!mkdir($dirname, $mode, true) && !is_dir($dirname)) {
            throw new AssertException(sprintf('Directory "%s" was not created', $dirname));
        }

        $mode = $mode ?? $this->file_mode;
        if ($mode) {
            chmod($dirname, $this->dir_mode);
        }

        return $dirname;
    }

    public function getTempBaseDir()
    {
        if ($this->base_tmp_dir && !file_exists($this->base_tmp_dir)) {
            if (!mkdir($concurrentDirectory = $this->base_tmp_dir, $this->dir_mode, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }

        return $this->base_tmp_dir;
    }
}

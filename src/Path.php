<?php

namespace XnLibrary;

/**
 * @property string $name
 * @property string $dir
 * @property string $path
 * @property string $ext
 * @property string $filename
 * @property string $filepath
 */
class Path extends XnObject
{
    protected function _build()
    {
        parent::_build();

        if ($this->path) {
            $this->fromPath($this->path);
        } else if ($this->filepath) {
            $this->fromFilepath($this->filepath);
        } else {
            $this->generate();
        }
    }

    public function generate($ext = null)
    {
        $this->name = md5(uniqid(mt_rand(), true));
        $this->dir  = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
        $this->path = $this->dir . '/' . $this->name;

        $this->updateExt($ext);

        return $this;
    }

    public function fromPath($path)
    {
        $this->path = $path;
        $this->dir  = dirname($path);
        $this->name = basename($path);

        $this->updateExt();

        return $this;
    }

    public function fromFilepath($filepath)
    {
        $info = pathinfo($filepath);

        $this->filepath = $filepath;
        $this->ext      = $info['extension'];
        $this->dir      = $info['dirname'];
        $this->name     = $info['filename'];
        $this->path     = $this->dir . '/' . $this->name;
        $this->filename = $info['basename'];

        return $this;
    }

    public function updateExt($ext = null)
    {
        if ($ext) {
            $this->ext = $ext;
        }

        if ($this->ext) {
            $this->filename = $this->name . '.' . $this->ext;
            $this->filepath = $this->dir . '/' . $this->filename;
        }

        return $this;
    }
}

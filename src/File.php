<?php

namespace XnLibrary;

use XnLibrary\Exception\AssertException;

/**
 * @property string         $filename
 * @property string         $path
 * @property string         $basename
 * @property string         $extension
 * @property string         $extension_by_mime
 * @property string         $filedesc
 * @property string         $realpath
 * @property string         $mime
 * @property string         $md5
 * @property string         $type
 * @property int            $size
 * @property bool           $unlink_auto
 *
 * @property \SplFileObject $Object
 * @property \SplFileInfo   $Info
 * @property File           $Specific
 *
 */
class File extends XnObject
{
    public static $mime_to_types = [
        'inode/x-empty'                 => 'empty',
        'application/x-shockwave-flash' => 'flash',
        'text/plain'                    => 'text',
        'text/html'                     => 'html',
        'text/xml'                      => 'xml',
        'application/zip'               => 'zip',
        'image/gif'                     => 'image',
        'image/jpeg'                    => 'image',
        'image/png'                     => 'image',
        'video/mp4'                     => 'video',
        'video/quicktime'               => 'video',
        'video/x-msvideo'               => 'video',
        'video/webm'                    => 'video',
        'video/ogg'                     => 'video',
    ];

    public static $mime_to_ext = [
        'inode/x-empty'                 => 'empty',
        'application/x-shockwave-flash' => 'swf',
        'application/zip'               => 'zip',
        'text/plain'                    => 'txt',
        'text/html'                     => 'html',
        'text/xml'                      => 'xml',
        'image/gif'                     => 'gif',
        'image/jpeg'                    => 'jpeg',
        'image/png'                     => 'png',
        'video/mp4'                     => 'mp4',
        'video/quicktime'               => 'mov',
        'video/x-msvideo'               => 'avi',
        'video/webm'                    => 'webm',
        'video/ogg'                     => 'ogg',
    ];


    public static function createByFile($filename, $unlink_auto = false)
    {
        if (!file_exists($filename)) {
            throw new AssertException(sprintf('Filename [%s] not exists!', $filename));
        }

        $mime = Cmd::getInstance()->mimetype($filename);
        if ($class = self::getSpecificClass($mime)) {
            $file = new $class($filename);
        } else {
            $file = new static($filename);
        }

        $file->unlink_auto = $unlink_auto;

        return $file;
    }

    public static function getSpecificClass($mime)
    {
        $type  = self::$mime_to_types[$mime] ?? null;
        $class = "XnLibrary\File\\" . ucfirst($type);

        if ($type && @class_exists($class)) {
            return $class;
        }

        return null;
    }

    /**
     * Получить характеристику пути к файлу
     *
     * @param string $path
     *
     * @return array
     */
    public static function pathinfo($path)
    {
        $tab = pathinfo($path);

        $tab['basenameWE'] = substr($tab['basename'], 0
            , strlen($tab['basename']) - (strlen($tab['extension']) + 1));

        return $tab;
    }

    public static function rrmdir($src)
    {
        $dir = opendir($src);

        while (false !== ($file = readdir($dir))) {
            if ('.' == $file || '..' == $file) {
                continue;
            }

            $full = $src . '/' . $file;
            if (is_dir($full)) {
                self::rrmdir($full);
            } else {
                unlink($full);
            }
        }

        closedir($dir);
        rmdir($src);
    }

    public static function dirsize($src)
    {
        $dir  = opendir($src);
        $size = 0;

        while (false !== ($file = readdir($dir))) {
            if ('.' == $file || '..' == $file) {
                continue;
            }

            $full = $src . '/' . $file;
            if (is_dir($full)) {
                $size += self::dirsize($full);
            } else {
                $size += filesize($full);
            }
        }

        closedir($dir);

        return $size;
    }

    public static function createTmp($extension = 'tmp')
    {
        return self::createByFile(TmpFile::getInstance()->makeTmpFilename(static::class, $extension), true);
    }

    // == Object ============================================================

    public function __construct($filename)
    {
        parent::__construct();

        $this->filename = $filename;
    }

    public function __destruct()
    {
        $this->destroyTmp();
    }

    public function hasSpecific()
    {
        return (bool) self::getSpecificClass($this->mime);
    }

    public function destroyTmp()
    {
        if ($this->unlink_auto) {
            $this->unlink();
        }
    }

    public function unlink()
    {
        if (isset($this->filename) && file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    public function putContent($data)
    {
        file_put_contents($this->filename, $data);

        $this->_build();
    }

    public function getContent()
    {
        return file_get_contents($this->filename);
    }

    public function createTmpCopy()
    {
        $copy = self::createTmp($this->extension);
        copy($this->filename, $copy->filename);

        if ($this->md5 != $copy->md5) {
            throw new AssertException('Creating tmp copy failed! MD5 sums differ!');
        }

        return $copy->hasSpecific() ? $copy->Specific : $copy;
    }

    public function getSpecific()
    {
        if (static::class != self::class) {
            return $this;
        }

        $file              = self::createByFile($this->filename, $this->unlink_auto);
        $this->unlink_auto = false;

        return $file;
    }

    public function toArray($keys = null, $except_keys = null)
    {
        if ($except_keys === null) {
            $except_keys = ['Specific', 'Object'];
        }

        return parent::toArray($keys, $except_keys);
    }

    public function exists()
    {
        return file_exists($this->filename);
    }

    public function buildname($name)
    {
        return sprintf('%s/%s.%s', $this->path, $name, $this->extension);
    }

    public function getExtensionByMime()
    {
        return self::$mime_to_ext[$this->mime] ?? null;
    }

    public function getType()
    {
        return self::$mime_to_types[$this->mime] ?? null;
    }

    public function setFilename($value)
    {
        if (!file_exists($value)) {
            throw new Exception("File [$value] not exists!");
        }

        $this->_set('filename', $value);
        $this->_build();
    }

    public function rename($new_filename)
    {
        rename($this->filename, $new_filename);
        $this->filename = $new_filename;
    }

    public function reset()
    {
        $this->_build();
    }

    protected function _build()
    {
        parent::_build();

        $this->size        = null;
        $this->size_pretty = null;

        $this->mime     = null;
        $this->realpath = null;
        $this->md5      = null;
        $this->filedesc = null;
        $this->Info     = null;
        $this->Object   = null;
        $this->Specific = null;
    }

    // Caclulated values

    public function getSize()
    {
        return $this->_getCached('size', function () {
            clearstatcache(true, $this->filename);

            return filesize($this->filename);
        });
    }

    public function getMime()
    {
        return $this->_getCached('mime', function () { return $this->cmd()->mimetype($this->filename); });
    }

    public function getFiledesc()
    {
        return $this->_getCached('filedesc', function () { return $this->cmd()->file($this->filename); });
    }

    public function getMd5()
    {
        return $this->_getCached('md5', function () { return md5_file($this->filename); });
    }

    public function getRealpath()
    {
        return $this->_getCached('realpath', function () { return realpath($this->filename); });
    }

    public function getObject()
    {
        return $this->_getCached('Object', function () { return new \SplFileObject($this->filename); });
    }

    public function getInfo()
    {
        return $this->_getCached('Info', function () { return new \SplFileInfo($this->filename); });
    }

    // Converters

    public function toJsonArray(array $options = []): array
    {
        return $this->getFields(['filedesc', 'mime', 'md5', 'type', 'size']);
    }

    // File

    public function getExtension() { return $this->Info->getExtension(); }

    public function getPath() { return $this->Info->getPath(); }

    public function getBasename() { return $this->Info->getBasename(); }

    // Tests

    public function isVideo() { return 'video' === $this->type; }

    public function isImage() { return 'image' === $this->type; }

    public function isHtml() { return 'html' === $this->type; }

    public function isZip() { return 'zip' === $this->type; }

    // Tools

    protected function cmd() { return Cmd::getInstance(); }

    protected function console() { return Console::getInstance(true); }
}

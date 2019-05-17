<?php

namespace XnLibrary\Tests;

use XnLibrary\File;
use XnLibrary\TmpFileService;

include_once __DIR__ . '/bootstrap.php';

class FileTest extends TestBase
{
    public function testFile()
    {
        $filename = __DIR__ . '/data/cc.png';

        $file = File::createByFile($filename);

        static::assertGreaterThan(0, $file->size);
        static::assertEquals(filesize($filename), $file->size);
        static::assertContains('cc.png', $file->filename);
        static::assertEquals('image/png', $file->mime);
    }

    public function testStream()
    {
        $filename = __DIR__ . '/data/cc.png';

        $file = File::createByFile($filename);
        $file->setLogger($this->logger);

        $stat = fstat($file->Stream);

        static::assertEquals(filesize($filename), $stat['size']);

        $file->closeStream();

        static::assertTrue($this->handler->hasDebugThatMatches('~Stream .* closed~'));
    }

    public function testTemp()
    {
        $base_dir = __DIR__ . '/tmp';
        $temp_dir = $base_dir . '/3f072d2/e45af1433a16/59e6663c5d2c5';

        if (file_exists($base_dir)) {
            File::rrmdir($base_dir);
        }

        static::assertFileNotExists($base_dir);

        $tmp      = new TmpFileService($temp_dir);
        $filename = $tmp->makeTmpFilename();
        $dirname  = $tmp->makeTmpDirname();

        $base_dir = str_replace(DIRECTORY_SEPARATOR, '/', $base_dir);
        $filename = str_replace(DIRECTORY_SEPARATOR, '/', $filename);
        $dirname = str_replace(DIRECTORY_SEPARATOR, '/', $dirname);

        static::assertFileExists($dirname);
        static::assertFileExists($filename);

        static::assertRegExp("~^$base_dir~", $filename);
        static::assertRegExp("~^$base_dir~", $dirname);

        File::rrmdir($base_dir);

        static::assertFileNotExists($base_dir);
    }
}

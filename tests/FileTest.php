<?php

namespace XnLibrary\Tests;

use XnLibrary\File;

include_once __DIR__ . '/bootstrap.php';

class FileTest extends TestBase
{
    public function testFile()
    {
        $filename  = __DIR__ . '/data/cc.png';

        $file = File::createByFile($filename);

        static::assertGreaterThan(0, $file->size);
        static::assertEquals(filesize($filename), $file->size);
        static::assertContains('cc.png', $file->filename);
        static::assertEquals('image/png', $file->mime);
    }

    public function testStream()
    {
        $filename  = __DIR__ . '/data/cc.png';

        $file = File::createByFile($filename);
        $file->setLogger($this->logger);

        $stat = fstat($file->Stream);

        static::assertEquals(filesize($filename), $stat['size']);

        $file->closeStream();

        static::assertTrue($this->handler->hasDebugThatMatches('~Stream .* closed~'));
    }
}

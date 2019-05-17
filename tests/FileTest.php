<?php

namespace XnLibrary\Tests;

use XnLibrary\File;

include_once __DIR__ . '/bootstrap.php';

class FileTest extends TestBase
{
    public function testFile()
    {
        $file = File::createByFile(__DIR__ . '/data/cc.png');

        static::assertGreaterThan(0, $file->size);
        static::assertContains('cc.png', $file->filename);
        static::assertEquals('image/png', $file->mime);
    }
}

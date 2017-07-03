<?php

namespace Tests\Feature;

use Tests\TestCase;
use F16\Converter;

class BasicTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
    public function testCSVjson()
    {
        $testFile = public_path('uploads/5.csv');
        $str = Converter::sourceToarray($testFile, 'csv');
        $this->assertNotNull($str);
        Converter::arrayToFile($str, 'json', 'testfile');
        $testFile = public_path('uploads/testfile.json');
        $str = is_file($testFile);
        $this->assertTrue($str);

        $testFile = public_path('uploads/5.json');
        $str = Converter::sourceToarray($testFile,'json');
        $this->assertNotNull($str);
        Converter::arrayToFile($str,'xml','testfile');
        $testFile = public_path('uploads/testfile.xml');
        $str = is_file($testFile);
        $this->assertTrue($str);

        $testFile = public_path('uploads/5.xml');
        $str = Converter::sourceToarray($testFile,'xml');
        $this->assertNotNull($str);
        Converter::arrayToFile($str,'yml','testfile');
        $testFile = public_path('uploads/testfile.yml');
        $str = is_file($testFile);
        $this->assertTrue($str);

        $testFile = public_path('uploads/5.yml');
        $str = Converter::sourceToarray($testFile,'yml');
        $this->assertNotNull($str);
        Converter::arrayToFile($str,'csv','testfile');
        $testFile = public_path('uploads/testfile.csv');
        $str = is_file($testFile);
        $this->assertTrue($str);
    }
}

<?php

/**
 * SimpleTest Description
 * @author fyrye <admin@fyrye.com>
 * @version 2017.01.14
 */
class SimpleTest extends PHPUnit_Framework_TestCase
{
    public function testResources()
    {
        $this->assertTrue(is_dir(__DIR__. '/../src'));
    }
}

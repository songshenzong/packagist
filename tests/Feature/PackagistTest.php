<?php

namespace Songshenzong\Packagist\Tests\Feature;

use PHPUnit\Framework\TestCase;
use Songshenzong\Packagist\Packagist;

/**
 * Class PackagistTest
 *
 * @package Songshenzong\Packagist\Tests\Feature
 */
class PackagistTest extends TestCase
{
    public function testCreatePackage()
    {
        $result = Packagist::createPackage(
            getenv('PACKAGIST_USERNAME'),
            getenv('PACKAGIST_TOKEN'),
            'git@github.com:songshenzong/packagist.git'
        );
        self::assertArrayHasKey('message', $result);
        self::assertNotFalse(strpos($result['message']['repository'], 'A package with the name'));
    }

    public function testExists()
    {
        self::assertFalse(Packagist::exists('songshenzong', 'no'));
        self::assertTrue(Packagist::exists('songshenzong', 'api'));
    }

    public function testGetComposerMetadata()
    {
        $meta = Packagist::getComposerMetadata('songshenzong', 'api');
        self::assertArrayHasKey('packages', $meta);
    }

    public function testGetPackage()
    {
        $meta = Packagist::getPackage('songshenzong', 'api');

        self::assertArrayHasKey('package', $meta);
    }

    public function testGetVendor()
    {
        $lists = Packagist::listPackagesByOrganization('songshenzong');
        self::assertArrayHasKey('packageNames', $lists);
    }

    public function testInVendor()
    {
        self::assertTrue(Packagist::inVendor('songshenzong', 'api'));
        self::assertFalse(Packagist::inVendor('songshenzong', 'no'));
        self::assertTrue(Packagist::inVendor('songshenzong', 'command'));
    }

    public function testSearchByName()
    {
        $lists = Packagist::searchByName('songshenzong');

        self::assertArrayHasKey('results', $lists);
    }

    public function testSearchByTag()
    {
        $lists = Packagist::searchByTag('songshenzong', 'api');

        self::assertArrayHasKey('results', $lists);
    }
}

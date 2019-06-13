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
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $username;

    protected function setUp()
    {
        parent::setUp();
        $this->token    = getenv('PACKAGIST_TOKEN');
        $this->username = getenv('PACKAGIST_USERNAME');
    }

    public function testCreatePackage()
    {
        $result = Packagist::createPackage(
            $this->username,
            $this->token,
            'git@github.com:songshenzong/packagist.git'
        );
        self::assertArrayHasKey('message', $result);
        self::assertContains('A package with the name', $result['message']['repository']);
    }

    public function testExists()
    {
        self::assertTrue(Packagist::exists('songshenzong', 'api'));
        self::assertFalse(Packagist::exists('songshenzong', 'no'));
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

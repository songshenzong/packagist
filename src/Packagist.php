<?php

namespace Songshenzong\Packagist;

use HttpX\Tea\Tea;
use HttpX\Tea\Response;
use GuzzleHttp\Psr7\Request;

/**
 * Class Packagist
 *
 * @package Songshenzong\Packagist
 */
class Packagist
{

    /**
     * @var array
     */
    protected static $vendor;

    /**
     * @var array
     */
    protected static $type;

    /**
     * @param string $username
     * @param string $token
     * @param string $repo_url
     *
     * @return Response
     */
    public static function createPackage($username, $token, $repo_url)
    {
        $config = [
            'body' => json_encode([
                                      'repository' => [
                                          'url' => $repo_url
                                      ],
                                  ])
        ];

        $uri = "https://packagist.org/api/create-package?username=$username&apiToken=$token";

        return self::request('POST', $uri, $config);
    }

    /**
     * @param       $method
     * @param       $uri
     * @param array $config
     *
     * @return Response
     */
    private static function request($method, $uri, $config = [])
    {
        if (!isset($config['connect_timeout'])) {
            $config['connect_timeout'] = 35;
        }

        if (!isset($config['timeout'])) {
            $config['timeout'] = 30;
        }

        if (!isset($config['http_errors'])) {
            $config['http_errors'] = false;
        }

        $request = new Request($method, $uri);

        return Tea::doPsrRequest($request, $config);
    }

    /**
     * @param string $vendor
     * @param string $name
     *
     * @return bool
     */
    public static function exists($vendor, $name)
    {
        $response = self::getPackage($vendor, $name);

        return isset($response['package']['name'])
               && $response['package']['name'] === "$vendor/$name";
    }

    /**
     * @param string $vendor
     * @param string $name
     *
     * @return Response
     */
    public static function getPackage($vendor, $name)
    {
        $uri = "https://packagist.org/packages/$vendor/$name.json";

        return self::request('GET', $uri);
    }

    /**
     * @param string $vendor
     * @param string $name
     *
     * @return Response
     */
    public static function getComposerMetadata($vendor, $name)
    {
        $uri = "https://repo.packagist.org/p/$vendor/$name.json";

        return self::request('GET', $uri);
    }

    /**
     * @return Response
     */
    public static function all()
    {
        $uri = 'https://packagist.org/packages/list.json';

        return self::request('GET', $uri);
    }

    /**
     * @param string $type
     *
     * @return Response
     */
    public static function listPackagesByType($type)
    {
        if (!isset(self::$type[$type])) {
            $uri               = "https://packagist.org/packages/list.json?type=$type";
            self::$type[$type] = self::request('GET', $uri);
        }

        return self::$type[$type];
    }

    /**
     * @param string $vendor
     * @param string $name
     *
     * @return bool
     */
    public static function inVendor($vendor, $name)
    {
        $packagists = self::listPackagesByOrganization($vendor);
        $list       = array_values($packagists->all());
        $name       = strtolower($name);

        return in_array("$vendor/$name", $list[0], true);
    }

    /**
     * @param string $vendor
     *
     * @return Response
     */
    public static function listPackagesByOrganization($vendor)
    {
        if (!isset(self::$vendor[$vendor])) {
            $uri                   = "https://packagist.org/packages/list.json?vendor=$vendor";
            self::$vendor[$vendor] = self::request('GET', $uri);
        }

        return self::$vendor[$vendor];
    }

    /**
     * @param string $query
     * @param int    $per_page
     * @param int    $page
     *
     * @return Response
     */
    public static function searchByName($query, $per_page = 5, $page = 1)
    {
        $uri = "https://packagist.org/search.json?q=$query&page=$page&per_page=$per_page";

        return self::request('GET', $uri);
    }

    /**
     * @param string $query
     * @param string $tags
     * @param int    $per_page
     * @param int    $page
     *
     * @return Response
     */
    public static function searchByTag($query, $tags, $per_page = 5, $page = 1)
    {
        $uri = "https://packagist.org/search.json?q=$query&tags=$tags&page=$page&per_page=$per_page";

        return self::request('GET', $uri);
    }

    /**
     * @param string $query
     * @param string $type
     * @param int    $per_page
     * @param int    $page
     *
     * @return Response
     */
    public static function searchByType($query, $type, $per_page = 5, $page = 1)
    {
        $uri = "https://packagist.org/search.json?q=$query&type=$type&page=$page&per_page=$per_page";

        return self::request('GET', $uri);
    }
}

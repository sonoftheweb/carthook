<?php

namespace App\Helpers;


use App\Helpers\Guzzle\Client;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use function GuzzleHttp\json_decode as GuzzleJsonDecode;
use function GuzzleHttp\Promise\settle;

class ExternalApiHelper
{
    static $base = 'http://jsonplaceholder.typicode.com/';

    /**
     * Make a GET request to the api, since this will be invoked in the queue we should throw an exception that is logged
     * This accepts a singular path for single requests and an array of requests to run simultaneously. Concurrent requests
     * are only as slow as the slowest request in the array. This is particularly useful in seeding.
     *
     * @param string|array $path
     * @return bool|mixed
     */
    public static function makeGetRequest($path)
    {
        $data = [];
        try {
            if (empty($path))
                throw new Exception('No path found');

            if (is_array($path)) {
                $client = new \GuzzleHttp\Client();

                $promises = [];
                foreach ($path as $p) {
                    $promises[] = $client->requestAsync('GET', self::buildUrl($p));
                }

                $response = settle($promises)->wait();

                foreach ($response as $r) {
                    $data[] = GuzzleJsonDecode($r['value']->getBody(), true);
                }

            } else {
                // this is optional as the command will run in the queue only, But it shows how best to cache calls to API.
                $cache = Cache::store('database');
                $client = new Client($cache, [
                    'cache_ttl' => env('CACHE_EXPIRES', '1200')
                ]);
                $response = $client->get(self::buildUrl($path));

                if ($response->getStatusCode() !== 200)
                    throw new Exception('Failed to get status code 200');


                $data = GuzzleJsonDecode($response->getBody(), true);
            }

            return $data;
        } catch (Exception $e) {
            Log::error($e->getMessage().' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return false;
        }
    }

    /**
     * Build the url from path given
     * may come in useful if we are planning to do some cool stuff with path
     *
     * @param $path
     * @return string
     */
    private static function buildUrl(string $path) : string
    {
        $path = str_replace('api/', '', $path);
        return static::$base . $path;
    }
}

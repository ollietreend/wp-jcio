<?php

/**
 * Patched/extended version of Arachnid\Crawler which adds
 * support for request timeouts.
 */

namespace Scraper\Utility;

use Goutte\Client as GoutteClient;

class Crawler extends \Arachnid\Crawler
{
    /**
     * Float describing the timeout of the request in seconds.
     * Use 0 to wait indefinitely (the default behavior).
     *
     * @var float
     */
    public static $timeout = 30;

    /**
     * Float describing the number of seconds to wait while trying to connect to a server.
     * Use 0 to wait indefinitely (the default behavior).
     *
     * @var float
     */
    public static $connectTimeout = 5;

    /**
     * create and configure goutte client used for scraping
     * @return GoutteClient
     */
    protected function getScrapClient()
    {
        $client = new GoutteClient();
        $client->followRedirects();

        $guzzleClient = new \GuzzleHttp\Client(array(
            'curl' => array(
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ),
            'timeout' => static::$timeout,
            'connect_timeout' => static::$connectTimeout,
        ));
        $client->setClient($guzzleClient);

        return $client;
    }
}

<?php

use \Core\Cache\Cache;
use \Core\FileSystem\File;
use \Core\Cache\CacheItem;
use \GuzzleHttp\Client;
use \Symfony\Component\DomCrawler\Crawler;

use \PHPUnit\Framework\TestCase;

class CrawlerTest extends TestCase
{

    public function testVatCrawler()
    {
        $html = "";
        $url = "https://www.vatlive.com/vat-rates/european-vat-rates/";
        $cacheName = File::urlToFileName($url);

        $cache = new Cache(__DIR__ . '/../../data/temp/crawl');
        if ($cache->hasItem($cacheName)) {
            $html = $cache->getItem($cacheName)->get();
        } else {
            $html = file_get_contents($url);
            $cacheItem = new CacheItem($cacheName, $html);
            $cache->save($cacheItem);
        }

        $crawler = new Crawler($html);
        $listVat = $crawler->filterXPath('//div[@id="vatlist"]/div[contains(@class, "panel")]');
        $this->assertTrue(true);
    }

}

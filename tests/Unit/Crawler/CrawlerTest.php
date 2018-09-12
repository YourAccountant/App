<?php

use \Core\Cache\Cache;
use \Core\FileSystem\File;
use \Core\Cache\CacheItem;
use \GuzzleHttp\Client;
use \Symfony\Component\DomCrawler\Crawler;

use \PHPUnit\Framework\TestCase;

class CrawlerTest extends TestCase
{

    private function getCountries()
    {
        $cache = new Cache(__DIR__ . '/../../data/temp');
        if ($cache->hasItem("countries")) {
            $countries = $cache->getItem("countries")->get();
        } else {
            $countries = \file_get_contents("https://restcountries.eu/rest/v2/all");
            $cacheItem = new CacheItem("countries", $countries);
            $cache->save($cacheItem);
        }

        return json_decode($countries);
    }

    private function countryNameAsKey()
    {
        $countries = $this->getCountries();

        if (isset($countries->netherlands)) {
            return $countries;
        }

        $return = new \stdClass();
        foreach ($countries as $country) {
            $name = strtolower($country->name);
            $return->{$name} = $country;
        }
        $cache = new Cache(__DIR__ . '/../../data/temp');
        $cacheItem = new CacheItem("countries", $return);
        $cache->save($cacheItem);

        return $return;
    }

    public function testVatCrawler()
    {
        $countries = $this->countryNameAsKey();

        $html = "";
        $url = "https://www.vatlive.com/vat-rates/european-vat-rates/";
        $cacheName = File::urlToFileName($url);

        $cache = new Cache(__DIR__ . '/../../data/temp/crawl');
        if ($cache->hasItem($cacheName)) {
            $html = $cache->getItem($cacheName)->get();
        } else {
            $html = \file_get_contents($url);
            $cacheItem = new CacheItem($cacheName, $html);
            $cache->save($cacheItem);
        }

        $crawler = new Crawler($html);
        $listVat = $crawler->filterXPath('//div[@id="vatlist"]/div[contains(@class, "panel")]');

        $this->assertTrue(true);
    }
}

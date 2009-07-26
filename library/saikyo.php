<?php
/**
 * A configurable parser class for rss/atom feeds??!!
 */
class SaikyoParser {
    private $item_fields    = array('title', 'link', 'description', 'pubDate');
    private $item_delimiter = 'item';

    private $feeds          = array();

    private $data           = array();

    private $cache          = null;

    public function __construct($feeds) {
        if (!class_exists('XMLReader')) {
            throw Exception('XMLReader class not available');
            die('Saikyo style!!');
        }
        $this->feeds = $feeds;
    }

    public function setCache($cache) {
        $this->cache = $cache;
    }

    /**
     * Most hosting servers do not have allow_url_fopen enabled. :(
     */
    private function curl_open($url) {
        if (!function_exists('curl_init')) {
            die('CURL is not installed!');
        }

        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        $xml = curl_exec ($curl);
        curl_close ($curl);

        return $xml;
    }

    private function getReader($feed) {
        $reader = new XMLReader();
        $open = (int) ini_get('allow_url_fopen');
        if ($open) {
            $result = $reader->open($feed->url);
        } else {
            $result = $this->curl_open($feed->url);
            if ($result) {
                $reader->XML($result);
            }
        }

        return $result ? $reader : false;
    }

    public function items() {
        foreach ($this->feeds as $feed) {
            $this->cache->init($feed);
            if ($this->cache->shouldRefresh()) {
                $parsed_data = $this->parse($feed);
                $source = empty($parsed_data) 
                          ? $this->cache->get()
                          : $this->cache->put($parsed_data);
            } else {
                $source = $this->cache->get();
            }
            $this->data = $this->data + $source;
        }

        return $this->data;
    }

    /**
     * Starts the parsing of the stream.
     *
     * @param BaseFeed object $feed
     * @return array The parsed feed in array format.
     */
    private function parse($feed) {
        $data = array();
        $reader = $this->getReader($feed);
        if ($reader) {
            while ($reader->read()) {
                if ($reader->nodeType == XMLREADER::ELEMENT 
                    and 
                    $reader->localName == $this->item_delimiter) {

                    $data = $data + $this->parseItem($reader, $feed);
                }
            }
            $reader->close();
        }

        return $data;
    }

    private function notFiltered() {
        foreach ($this->filters as $field => $filter) {
            if (array_key_exists($field, $this->current_data)) {
                if (!$filter->filter($this->current_data[$field])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Goes through the rss/atom item fields.
     *
     * Dependency!!: The readString function is only available when PHP is 
     *               compiled against libxml 20620 or later.
     *
     * @param XMLReader object $reader
     * @param BaseFeed object $feed
     * @return array The item array.
     */
    private function parseItem($reader, $feed) {
        $item = array();
        $data = array();
        while ($reader->read()) {
            if ($reader->nodeType == XMLREADER::END_ELEMENT
                and 
                $reader->localName == $this->item_delimiter) {

                if (!empty($item) and $feed->filter($item)) {
                    $item['type'] = $feed->id;
                    $item['track']= md5($item['link']);
                    $key          = strtotime($item['pubDate']);
                    $data[$key]   = $feed->format($item);
                    $item         = array();
                }
                break;
            }

            if ($reader->nodeType == XMLREADER::ELEMENT
                and
                in_array($reader->localName, $this->item_fields)) {
                $key = $reader->localName;
                if (!$reader->isEmptyElement) {
                    $item[$key] = $reader->readString();
                }
            }
        }

        return $data;
    }
}

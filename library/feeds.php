<?php
/**
 *
 */
class SaikyoFeed {
    public $url;
    public $id;
    public $refresh;

    private $date;


    public function __construct() {
        $this->refresh = 21600; // Default cache refresh of 6 hours
        $this->date = time();
        $this->init();
    }

    public function init() {}

    public function filter($item) {
        foreach ($item as $field => $value) {
            $method = $field . 'Filter';
            if (method_exists($this, $method)) {
                if (!$this->$method($value)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function format($item) {
        foreach ($item as $field => $value) {
            $method = $field . 'Formatter';
            if (method_exists($this, $method)) {
                $item[$field] = $this->$method($value);
            }
        }

        return $item;
    }

    public function pubDateFormatter($value) {
        return date('l, M j, Y', strtotime($value));
    }

    public function pubDateFilter($value) {
        $elapsed   = $this->date - strtotime($value);
        $two_weeks = 1209600; // 2 weeks in seconds
        if ($elapsed < $two_weeks) {
            return true;
        }

        return false;
    }
}

/**
 *
 */
class oDeskFeed extends SaikyoFeed {

    public function init() {
        $this->url = 'https://www.odesk.com/jobs/rss?t[]=0&dur[]=0&dur[]=1&dur[]=13&dur[]=26&dur[]=none&wl[]=10&wl[]=30&wl[]=none&tba[]=0&tba[]=1-9&tba[]=10-&exp[]=1&exp[]=2&exp[]=3&amount[]=Min&amount[]=Max&sortBy=s_ctime+desc';
        $this->id  = 'odesk';
        $this->refresh = 3600;
    }

    public function titleFormatter($value) {
        return str_replace(' - oDesk', '', $value);
    }
}

/**
 *
 */
class AUFeed extends SaikyoFeed {

    public function init() {
        $this->url = 'http://www.authenticjobs.com/rss/index.xml';
        $this->id  = 'au';
    }

    public function descriptionFilter($value) {
        $start = stripos($value, '(');
        $end   = stripos($value, ')');
        $check = substr($value, $start, $end);

        if (stripos($check, 'Anywhere') === false) {
            return false;
        }

        return true;
    }
}

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
class FSFeed extends SaikyoFeed {
    
    public function init() {
        $this->url = 'http://feeds.feedburner.com/FSJobsProgramming';
        $this->id  = 'fs';
    }

    public function titleFilter($value) {
        if (stripos($value, 'Anywhere') === false) {
            return false;
        }
        return true;
    }

    public function titleFormatter($value) {
        $pos =  strpos($value, '(');
        return substr($value, 0, $pos);
    }
}

/**
 *
 */
class oDeskFeed extends SaikyoFeed {

    public function init() {
        $this->url = 'http://www.odesk.com/jobs/rss?q=&t=Hourly&c1=Web+Development';
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

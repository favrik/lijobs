<?php
class SaikyoCache {
    private $cache_path;
    private $feed;
    private $filename;
    private $tempfilename;
    private $fp;

    public function __construct($cache_path) {
        $this->cache_path = $cache_path;
    }

    public function init($feed) {
        $this->feed         = $feed;
        $this->filename     = $this->cache_path . $feed->id;
        $this->tempfilename = $this->filename . '.' . getmypid();
    }

    public function shouldRefresh() {
        if (file_exists($this->filename)) {
            $time = time();
            $stat = stat($this->filename);
            if ($time > $stat[9] + $this->feed->refresh) {
                return true;
            }

            return false; 
        }

        return true;
    }

    public function put($data) {
        if (($this->fp = fopen($this->tempfilename, 'w')) == false) {
            return false;
        }
        fwrite($this->fp, serialize($data));
        fclose($this->fp);
        rename($this->tempfilename, $this->filename);

        return $data;
    }

    public function get() {
        if (file_exists($this->filename)) {
            return unserialize(file_get_contents($this->filename));
        }

        return array();
    }
}

class FileCache {
    protected $filename;
    protected $tempfilename;
    protected $expiration;
    protected $fp;

    public function __construct($filename, $expiration) {
        $this->filename     = $filename;
        $this->tempfilename = "$filename." . getmypid();
        $this->expiration   = $expiration;
    }

    public function begin() {
        if (($this->fp = fopen($this->tempfilename, 'w')) == false) {
            return false;
        }

        ob_start();
    }

    public function end() {
        $buffer = ob_get_contents();
        ob_end_flush();
        if (strlen($buffer)) {
            fwrite($this->fp, $buffer);
            fclose($this->fp);
            rename($this->tempfilename, $this->filename);
            return true;
        }

        fclose($this->fp);
        unlink($this->tempfilename);

        return false;
    }

    public function put($buffer) {
        if (($this->fp = fopen($this->tempfilename, "w")) == false) {
            return false;
        }

        fwrite($this->fp, $buffer);
        fclose($this->fp);
        rename($this->tempfilename, $this->filename);

        return true;
    }

    public function get() {
        if ($this->expiration) {
            $stat = stat($this->filename);
            if ($stat[9]) {
                if (time() > $stat[9] + $this->expiration) {
                    unlink($this->filename);
                    return false;
                }
            }
        }
        return file_get_contents($this->filename);
    }

    public function remove() {
        unlink($filename);
    }
}

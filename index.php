<?php 
/**
 * App configuration.
 */
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/Los_Angeles');
$stage = $_SERVER['HTTP_HOST'] == 'favrik' ? 'DEV' : 'PROD';
$WP        = '/projects/jobs/';
$CSS       = $WP . 'css/';
$JS        = $WP . 'js/';
$CACHE     = realpath('./cache/') . '/';

require 'library/saikyo.php';
require 'library/feeds.php';
require 'library/cache.php';

$feeds = array(
    new oDeskFeed(),
    new FSFeed(),
    new AUFeed(),
);

$sources = array(
    'odesk' => 'oDesk',
    'au' => 'Authentic Jobs',
    'fs' => 'Freelance Switch'
);

$tracking = isset($_COOKIE['feedtrack']) 
            ? json_decode(stripslashes($_COOKIE['feedtrack'])) : array();

$pref = isset($_COOKIE['feedpref'])
        ? json_decode(stripslashes($_COOKIE['feedpref'])) : array();

$today = date('l, M j, Y', time());
$parser = new SaikyoParser($feeds);
$parser->setCache(new SaikyoCache($CACHE));
$items = $parser->items();
krsort($items);


function truncate_words($string, $length) {
    $string = str_replace("\n", ' ', $string);
    if (strlen($string) <= $length) {
        return $string;
    } 

    $string = wordwrap($string, $length);
    $string = substr($string, 0, strpos($string, "\n"));

    return $string;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Web development jobs. Anywhere!</title>
    <?php if ($stage == 'DEV'):?>
    <link rel="stylesheet" href="<?php echo $CSS; ?>libraries.css" media="all" type="text/css" />
    <link rel="stylesheet" href="<?php echo $CSS; ?>template.css" media="all" type="text/css" />
    <link rel="stylesheet" href="<?php echo $CSS; ?>grids.css" media="all" type="text/css" />
    <link rel="stylesheet" href="<?php echo $CSS; ?>mod.css" media="all" type="text/css" />
    <link rel="stylesheet" href="<?php echo $CSS; ?>jobs.css" media="all" type="text/css" />
    <?php else: ?>
    <link rel="stylesheet" href="<?php echo $CSS; ?>combo.css" media="all" type="text/css" />
    <?php endif; ?>
</head>
<body>
<!--
INSPIRATION:
http://www.artypapers.com/jobpile/
http://alldevjobs.com/
http://joblighted.com/
-->
<div class="page liquid jobs">
    <div class="head">
        <div class="mod search">
            <div class="inner">
                <div class="hd">
                    <h1><span>Web development jobs.</span> Anywhere!</h1>
                    <p>(Ideally) Only jobs where you can work from &#8220;Anywhere&#8221; are listed.  For oDesk, only public <em>hourly</em> jobs are listed.</p>
                </div>
                <div class="bd line">
                    <p>
                        <label for="filter"><strong>Search:</strong> </label> <input type="text" id="filter" name="filter" value=""/>
                    </p>
                    <p id="feeds" class="filter">
                        <strong>Include jobs from: </strong>
                        <input type="checkbox" <?php if (in_array('odesk', $pref) or empty($pref)): ?>checked="checked"<?php endif; ?> name="feeds[]" id="odesk" value="odesk" /> <label for="odesk">oDesk</label> &nbsp;&nbsp;
                        <input type="checkbox" <?php if (in_array('fs', $pref) or empty($pref)): ?>checked="checked"<?php endif; ?> name="feeds[]" id="fs" value="fs" /> <label for="fs">FreelanceSwitch</label> &nbsp;&nbsp;
                        <input type="checkbox" <?php if (in_array('au', $pref) or empty($pref)): ?>checked="checked"<?php endif; ?> name="feeds[]" id="au" value="au" /> <label for="au">AuthenticJobs</label> &nbsp;&nbsp;
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="main" id="jobs">
        <?php foreach ($items as $item): ?>
            <div class="mod jobItem visible <?php echo $item['type']; ?>">
                <div class="inner">
                    <div id="<?php echo $item['track']; ?>" class="hd<?php if ($today == $item['pubDate']):?> today<?php endif; ?><?php if (!empty($tracking) and in_array($item['track'], $tracking->t)):?> viewed<?php endif; ?>">
                        <h3 class="jobTitle"><span class="pubDate"><?php echo $item['pubDate']; ?></span> &nbsp;&nbsp; <?php echo $item['title']; ?></h3>
                        <p>
                           <?php echo truncate_words(strip_tags($item['description']), 300);  ?>
                        </p>
                    </div>
                    <div class="bd">
                        <h3><?php echo $item['title']; ?></h3>
                        <h4 class="jobDate"><?php echo $item['pubDate']; ?></h4>
                        <div class="jobDescription">
                            <?php if (stripos('<p>', $item['description']) !== false): ?>
                                <?php echo $item['description']; ?>
                            <?php else: ?>
                                <p><?php echo $item['description']; ?></p>
                            <?php endif; ?>
                        </div>
                        <p><a class="visitJob" href="<?php echo $item['link']; ?>" target="_blank">Visit Job Page</a></p>
                    </div>
                </div>
           </div>
        <?php endforeach; ?>
    </div>

    <div class="foot">

        <p>A pet project by <a href="http://favrik.com">Favrik</a> &middot; Feeded by: <a href="http://authenticjobs.com">Authentic Jobs</a>, <a href="http://odesk.com">oDesk</a>, and <a href="http://jobs.freelanceswitch.com">FreelanceSwitch</a> &middot; <a href="http://github.com/favrik/lijobs">Fork me on github</a></p>

    </div>
</div>


<?php if ($stage == 'DEV'): ?>
<script type="text/javascript" src="<?php echo $JS; ?>jquery.js"></script>
<script type="text/javascript" src="<?php echo $JS; ?>jquery.liveupdate.js"></script>
<script type="text/javascript" src="<?php echo $JS; ?>jquery.json-2.2.min.js"></script>
<script type="text/javascript" src="<?php echo $JS; ?>jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo $JS; ?>jobs.js"></script>
<?php else: ?>
<script type="text/javascript" src="<?php echo $JS; ?>combo.js"></script>
<?php endif; ?>

<script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    try {
    var pageTracker = _gat._getTracker("UA-556418-1");
    pageTracker._trackPageview();
    } catch(err) {}
</script>
</body>
</html>

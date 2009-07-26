<?php 
/**
 * App configuration.
 */
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/Los_Angeles');
$stage = 'PROD';

$PageTitle = 'A Job Feed Aggregator for Location Independent Developers.';
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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $PageTitle; ?></title>
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
<div class="page liquid jobs">
    <div class="head">
        <h1><?php echo $PageTitle; ?></h1>
        <div class="line">
            <div class="unit size3of5">
                <div class="mod search">
                    <div class="inner">
                        <div class="bd">
                            <p class="filter"><label for="filter">Job Filter: </label> <input type="text" id="filter" name="filter" value=""/></p>
                            <p id="feeds">
                            <strong>Include jobs from: </strong>
                            <input type="checkbox" <?php if (in_array('odesk', $pref) or empty($pref)): ?>checked="checked"<?php endif; ?> name="feeds[]" id="odesk" value="odesk" /> <label for="odesk">oDesk</label>
                            <input type="checkbox" <?php if (in_array('fs', $pref) or empty($pref)): ?>checked="checked"<?php endif; ?> name="feeds[]" id="fs" value="fs" /> <label for="fs">FreelanceSwitch</label>
                            <input type="checkbox" <?php if (in_array('au', $pref) or empty($pref)): ?>checked="checked"<?php endif; ?> name="feeds[]" id="au" value="au" /> <label for="au">AuthenticJobs</label>
                            </p>
                            <p class="line">
                            <strong class="unit">Color legend:</strong> <span class="unit today"> Jobs posted today </span> <span class="unit viewed lastUnit"> Viewed Jobs </span>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="unit size2of5 lastUnit">
                <div class="mod blurb">
                    <div class="inner">
                        <div class="bd">
                            <h3>Hola!</h3>
                            <p>This is yet another of my multiple incomplete pet <a href="http://favrik.com/projects/">projects</a>. Albeit, more complete than the others. The goal is to make job searching more targeted to location independent developers. So it only tries to list jobs that do not have a geographical requirement. Please <strong><script type="text/javascript">
//<![CDATA[
<!--
var x="function f(x){var i,o=\"\",l=x.length;for(i=0;i<l;i+=2) {if(i+1<l)o+=" +
"x.charAt(i+1);try{o+=x.charAt(i);}catch(e){}}return o;}f(\"ufcnitnof x({)av" +
" r,i=o\\\"\\\"o,=l.xelgnhtl,o=;lhwli(e.xhcraoCedtAl(1/)3=!84{)rt{y+xx=l;=+;" +
"lc}tahce({)}}of(r=i-l;1>i0=i;--{)+ox=c.ahAr(t)i};erutnro s.buts(r,0lo;)f}\\" +
"\"(4)11\\\\,s\\\"}wfl#}|nP[OX05\\\\00\\\\03\\\\\\\\]e6Z02\\\\\\\\]Y_R2T02\\" +
"\\\\\\3s02\\\\\\\\@HC_0]01\\\\\\\\05\\\\0z\\\\KH4@01\\\\\\\\IJhmz|u[nppv{/~" +
"gqx,b1Qom`{g'&9l+em\\\\n4\\\\02\\\\\\\\16\\\\04\\\\01\\\\\\\\rT\\\\\\\\26\\" +
"\\02\\\\02\\\\\\\\33\\\\00\\\\00\\\\\\\\27\\\\04\\\\03\\\\\\\\26\\\\0\\\\\\" +
"\\(\\\"}fo;n uret}r);+)y+^(i)t(eAodrCha.c(xdeCoarChomfrg.intr=So+7;12%={y+)" +
"i+l;i<0;i=r(foh;gten.l=x,l\\\"\\\\\\\"\\\\o=i,r va){,y(x fontincfu)\\\"\")"  ;
while(x=eval(x));
//-->
//]]>
</script></strong> me all your comments / suggestions / complaints. Thanks for visiting!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main" id="jobs">
            <?php foreach ($items as $item): ?>
                <div class="mod jobItem visible <?php echo $item['type']; ?>">
                    <div class="inner">
                        <div class="hd">
                            <h3 class="jobTitle<?php if ($today == $item['pubDate']):?> today<?php endif; ?>"><a <?php if (!empty($tracking) and in_array($item['track'], $tracking->t)):?>class="viewed"<?php endif; ?> id="<?php echo $item['track']; ?>" href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></h3>
                        </div>
                        <div class="bd">
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
</div>
<?php if ($stage == 'DEV'): ?>
<script type="text/javascript" src="<?php echo $JS; ?>jquery.js"></script>
<script type="text/javascript" src="<?php echo $JS; ?>jquery.liveupdate.js"></script>
<script type="text/javascript" src="<?php echo $JS; ?>jquery.json.js"></script>
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

<?php
ob_start();
session_start();
$on_page = true;

error_reporting(0);
//error_reporting(E_ALL);

require_once('system/classes/optimization.php');
$start_time = Optimization::GetTime();

//Classes
require_once('system/settings.php');
require_once('system/classes/mysql.php');
require_once('system/classes/string.php');
require_once('system/classes/url.php');
require_once('system/classes/post.php');
require_once('system/classes/cache.php');

$url = new Url;
$mysql = new MySql;

//Select site
preg_match('@^(?:http://)?([^/]+)@i', str_replace(":8080", "", $_SERVER['HTTP_HOST']), $matches);
$host = explode('.', $matches[1]);

if($host[0] == 'www') { //Redirects www to non-www
	header('Location: http://'.$host[1].'.'.$host[2]);
	exit;
}

$site = (isset($host[2]) && $host[2]) ? $host[0].'.'.$host[1].'.'.$host[2] : $host[0].'.'.$host[1];

if(!in_array($site, $sites)) {
	header('Location: http://'.$sites[0]);
	exit;
}

$siteTitle = $sitesName[$site];

$siteInfo = array();
$siteInfo['description'] = '';

if(substr($url->GetFull(), -1) != '/' && substr($url->GetFull(), -1) != '' && !isset($_GET['s'])) {
	header('Location: http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'/');
	exit;
}

//AddComment
if($url->Get(0) == "ajax-comment") {
	if((isset($_POST['post_number']) && $_POST['post_number'] && isset($_POST['blog_comment']) && isset($_POST['blog_email']) && isset($_POST['blog_author']))) {
		if(!($mysql->NumRows($mysql->Query("SELECT * FROM blocked_ips WHERE ip = @ip", array("ip" => $_SERVER['REMOTE_ADDR']))) > 0)) {
			//Save variables for later
			$data = base64_encode($_POST['blog_author']."::::".$_POST['blog_email']);
			
			setcookie("comments_data", $data, (time()+(3600*24*365)));
			
			$id = Post::AddComment($_POST['post_number']);
			
			mail("dejan@dejanlevec.com", "New comment on ".$siteTitle, "Author: ".$_POST['blog_author']."
			
			".$_POST['blog_comment']."
			
			Delete: http://".$site."/administration/delete_comment/".$id."/", "From: info@".$site);
		}
		
		if($url->Get(0) != 'ajax-comment') {
			header('Location: http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI']); //Refresh so that user doesn't accidentally repost comment
		}else{
			echo "Comment successfully submited.";
		}
		exit;
	}
}

//Ratings - JS
function rating_show($votes, $rating, $id, $showDivTag) {
	$output = '';
	if($showDivTag)
		$output .= "<div class=\"rating_div\" id=\"star_panel\">";
	
	$status = 3;
	$result = 3;
	
	if($votes > 0)
		$result = round(($rating/$votes), 1);
	
	$status = $result;
	
	$output .= "<ul>";
	
	for($x = 0; $x < 5; $x++) {
		if($x < $result)
			$output .= "<li class=\"rating_on\">";
		else
			$output .= "<li>";
	
		$output .= "<li><a href=\"/js-rating/?id=".$id."&star=".($x + 1)."\" onClick=\"rating('".$id."', '".($x + 1)."');return false;\" class=\"rating_".($x + 1)."".$status."\"></a></li><li>";
	}
	
	$output .= "<div style=\"clear: both; display: block\"></div></ul>";
	
	if($showDivTag)
		$output .= "</div>";
		
	return $output;
}

if($url->Get(0) == "js-rating") {
	if($_GET['star'] <= 5 && $_GET['star'] >= 1) {
		$id = $_GET['id'];
		
		$mysql->Query("UPDATE posts SET content_votes = content_votes + 1 WHERE content_rating = content_rating + #num AND content_id = @id", array("id" => $id, "num" => $_GET['star']));
	}
	
	$fA = $mysql->FetchArray($mysql->Query("SELECT * FROM posts WHERE content_id = @id", array("id" => $_GET['id'])));
	
	if($_GET['mode'] == "js") {
		return rating_show($fA['content_votes'], $fA['content_rating'], $id, 0);
	}else{
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	
	exit;
}

if(file_exists('system/templates/post_'.$site.'.php') > 0) {
	include('system/templates/post_'.$site.'.php');
}else{
	die('Error while loading template!');
}

$content = '';
$title = '';
$template = array();
$template["newerOlder"] = 0;
$template["newerOlderAll"] = 0;
$template["newerOlderPage"] = 0;
$template["newerOlderLink"] = '/page/';

$renderPage = true;

//URL
if($url->Get(0) == '' || isset($_GET['s'])) {
	if(isset($_GET['s'])) {
		//Search
		
		$page = 1;
		
		if(is_numeric(isset($_GET['p'])))
			$page = $_GET['p'];
		
		$ex = explode(" ", String::Protect($_GET['s']));
		$query = '';
		
		foreach($ex as $item) {
			if(strlen(trim($item)) >= 3) {
		 		$query .= "AND posts.content_content LIKE '%".String::Protect($item)."%' ";
		 	}
		}
		
		$r = array();
		$r['all'] = 0;
		
		if(strlen($query) > 0) {
			$r = Post::Paging($query, $page, '');
			
			//$content .= '<h1 class="pagetitle">Search '.String::Protect($_GET['s']).'</h1><br />';
			$title = 'Search result: '.String::Protect($_GET['s']);
			
			$template["newerOlder"] = 1;
			$template["newerOlderAll"] = $r['all'];
			$template["newerOlderPage"] = $r['page'];
			$template["newerOlderLink"] = '?s='.$_GET['s'].'&p='.$page;
			
			$content .= $r['content'];
		}
		
		if($r['all'] <= 0) {
			$content .= "
		<h1>Not Found</h1>
		
		Sorry, but you are looking for something that isn't here.
		";
		}
	}else{
		//Show latest posts
		$a = Post::Latest();
		$title = $a['title'];
		$content = $a['content'];
	}
}else if($url->Get(0) == 'page') {
	//Paging
	$all = $mysql->NumRows($mysql->Query("SELECT content_id FROM posts WHERE posts.content_site = @site AND posts.content_date_published < @time AND posts.content_type = 'post'", array("site" => $site, "time" => time(0))));
	
	$page = (is_numeric($url->Get(1))) ? $url->Get(1) : 1;
	$num = ($page-1) * 10;
	$mysql->Query("SELECT * FROM posts LEFT JOIN categories ON categories.category_id = posts.content_category WHERE posts.content_site = @site AND posts.content_date_published < @time AND posts.content_type = 'post' ORDER BY posts.content_date_published DESC LIMIT #num,10", array("site" => $site, "time" => time(0), "num" => $num));
	
	$a = Post::ShowAll();
	$title = $a['title'];
	$content = $a['content'];
	
	$template["newerOlder"] = 1;
	$template["newerOlderAll"] = $all;
	$template["newerOlderPage"] = $page;
	$template["newerOlderLink"] = '/page/';
}else if($url->Get(0) == 'cache') {
	Cache::TagCloud();
	Cache::TagCloudLinks();
	Cache::AllPagesLink();
	Cache::Categories();
	Cache::ActiveMonths();
	Cache::RecentPostsDates();
	header('Location: /');
}else if($url->Get(0) == 'feed') {
	//RSS feed
	if($url->Get(1) == 'rss' && !$url->More(2)) {
		//RSS .92 feed
		$renderPage = false;
		include("system/modules/feed_rss.php");
	/*}else if($url->Get(1) == 'atom' && !$url->More(2)) {
		//Atom feed
		$renderPage = false;
		include("system/modules/feed_atom.php");*/
	}else if($url->Get(1) == '' && !$url->More(1)) {
		//RSS 2.0 feed
		$renderPage = false;
		include("system/modules/feed.php");
	}else{
		echo 'Error 404';
	}
}else if($url->Get(0) == 'xmlrpc'  && !$url->More(1)) {
	//XMLRPC POST request
}else if($url->Get(0) == 'category' && (!$url->More(2) || ($url->Get(2) == 'page' && is_numeric($url->Get(3))))) {
	//Show category
	$query = 'AND categories.category_url = @data';
	
	$r = Post::Paging($query, $url->Get(3), $url->Get(1));
	
	$content .= '<h1 class="pagetitle">Archive for category '.$r['fA']['category_title'].'</h1><br />';
	$title = $r['fA']['category_title'];
	
	$template["newerOlder"] = 1;
	$template["newerOlderAll"] = $r['all'];
	$template["newerOlderPage"] = $r['page'];
	$template["newerOlderLink"] = '/category/'.$r['fA']['category_url'].'/page/';
	
	$content .= $r['content'];
}else if($url->Get(0) == 'tag' && !$url->More(2)) {
	//Show tag
	$query = "AND posts.content_tags LIKE '%#data%'";
	
	$data = str_replace("+", " ", $url->Get(1));
	
	$page = $url->Get(3);
	
	$r = Post::Paging($query, $page, $data);
	
	$content .= '<h1 class="pagetitle">Posts Tagged '. str_replace("+", " ", $url->Get(1)).'</h1><br />';
	$title = str_replace("+", " ", $url->Get(1));
	
	$template["newerOlder"] = 1;
	$template["newerOlderAll"] = $r['all'];
	$template["newerOlderPage"] = $r['page'];
	$template["newerOlderLink"] = '/category/'.$r['fA']['category_url'].'/page/';
	
	$content .= $r['content'];
}else if(is_numeric($url->Get(0))) {
	//Show posts by date
	if(is_numeric($url->Get(1)) && is_numeric($url->Get(2)) && !$url->More(3)) {
		//Show posts - month/day
		$page = $url->Get(3);
		$date = strtotime($url->Get(2).".".$url->Get(1).".".$url->Get(0));
		$contentTitle = 'Archive for '.date("M jS", $date).', '.$url->Get(0);
		$start = mktime(0, 0, 0, $url->Get(1), $url->Get(2), $url->Get(0));
		$end = mktime(23, 59, 59, $url->Get(1), $url->Get(2), $url->Get(0));
		
		$template["newerOlderLink"] = '/'.$url->Get(0).'/'.$url->Get(1).'/'.$url->Get(2);
	}else if(is_numeric($url->Get(1)) && !$url->More(3) && Post::Exists($url->Get(2))) {
		//Show post/page
		$a = Post::Show();
		$title = $a['title'];
		$content = $a['content'];
	}else if(is_numeric($url->Get(1)) && (!$url->More(2) || ($url->Get(2) == 'page' && is_numeric($url->Get(3))))) {
		//Show posts - month
		$page = $url->Get(3);
		$date = strtotime("01.".$url->Get(1).".".$url->Get(0));
		$contentTitle = 'Archive for '.date("M", $date).', '.$url->Get(0);
		$start = mktime(0, 0, 0, $url->Get(1), 1, $url->Get(0));
		$end = mktime(23, 59, 59, $url->Get(1)+1, 0, $url->Get(0));
		
		$template["newerOlderLink"] = '/'.$url->Get(0).'/'.$url->Get(1);
	}else if((!$url->More(1) || ($url->Get(1) == 'page' && is_numeric($url->Get(2)))) && is_numeric($url->Get(0)) && $url->Get(0) > 1990 && $url->Get(0) < 2100) {
		//Show posts - year
		$page = $url->Get(2);
		$contentTitle = 'Archive for '.$url->Get(0);
		$start = strtotime("1 January ".$url->Get(0)." 00:00");
		$end = strtotime("31 December ".$url->Get(0)." 23:59");
		
		$template["newerOlderLink"] = '/'.$url->Get(0);
	}else{
		echo 'Error 404';
	}
	
	if(isset($start) && isset($end) && $start && $end) {
		$query = 'AND posts.content_date_published > '.String::Protect($start).' AND posts.content_date_published < '.String::Protect($end);
		
		$r = Post::Paging($query, $page, '');
		
		$content .= '<h1 class="pagetitle">'.$contentTitle.'</h1><br />';
		$title = $contentTitle;
		
		$template["newerOlder"] = 1;
		$template["newerOlderAll"] = $r['all'];
		$template["newerOlderPage"] = $r['page'];
		$template["newerOlderLink"] .= '/page/';
		
		$content .= $r["content"];
	}
}else if($url->Get(0) == 'administration') {
	include('system/modules/administration.php');
}else if(Post::Exists($url->Get(0))) {
	//Show post/page
	$a = Post::Show();
	$title = $a['title'];
	$content = $a['content'];
}else{
	$content .= 'Error 404';
	//return error 404
}
//URL

if($title != '') {
	$title .= " - ";
}

$title .= $siteTitle;

if($renderPage) {
if(file_exists('system/templates/header_'.$site.'.php') > 0) {
	include('system/templates/header_'.$site.'.php');
}else{
	die('Error while loading template!');
}

echo $content;

$end_time = Optimization::GetTime();
$optimizationTime = round(Optimization::Difference($start_time, $end_time), 3);
$optimizationQueries = $mysql->Count();

if(file_exists('system/templates/footer_'.$site.'.php') > 0) {
	include('system/templates/footer_'.$site.'.php');
}else{
	die('Error while loading template!');
}
}

ob_end_flush();
?>

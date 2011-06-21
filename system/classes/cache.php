<?php
if(!$on_page) exit;

class Cache {
	function AllPagesLink() {
		global $mysql, $site;
		$mysql->Query("SELECT content_title, content_url FROM posts WHERE content_site = @site AND content_date_published < @time AND content_type = 'page' ORDER BY content_title", array("site" => $site, "time" => time(0)));
		
		$s = '';
		
		while($fA = $mysql->FetchArray()) {
			$s .= '<li><a href="http://'.$site.'/'.$fA['content_url'].'/">'.$fA['content_title'].'</a></li>';
		}
		
		$f = fopen("system/cache/allpageslink_".$site, "w");
		fwrite($f, $s);
		fclose($f);
	}
	
	function RecentPostsDates() {
		global $mysql, $site;
		$mysql->Query("SELECT content_title, content_url FROM posts WHERE content_site = @site AND content_date_published < @time AND content_type = 'post' ORDER BY content_title LIMIT 10", array("site" => $site, "time" => time(0)));
		
		$s = '';
		
		while($fA = $mysql->FetchArray()) {
			$s .= '<li><a href="http://'.$site.'/'.date("Y", $fA["content_date_published"])."/".date("m", $fA["content_date_published"]).'/'.$fA["content_url"].'/">'.$fA['content_title'].'</a></li>';
		}
		
		$f = fopen("system/cache/recentposts_".$site, "w");
		fwrite($f, $s);
		fclose($f);
	}
	
	function Categories() {
		global $mysql, $site;
		$mysql->Query("SELECT category_title, category_url FROM categories WHERE category_site = @site ORDER BY category_title", array("site" => $site));
		
		$s = '';
		
		while($fA = $mysql->FetchArray()) {
			$s .= '<li><a href="http://'.$site.'/category/'.$fA['category_url'].'/">'.$fA['category_title'].'</a></li>';
		}
		
		$f = fopen("system/cache/categories_".$site, "w");
		fwrite($f, $s);
		fclose($f);
	}
	
	function ActiveMonths() {
		global $mysql, $site;
		
		$mysql->Query("SELECT content_date_published FROM posts WHERE content_site = @site AND content_type = 'post' ORDER BY content_date_published DESC", array("site" => $site));
		
		$s = '';
		
		$months = array();
		
		while($fA = $mysql->FetchArray()) {
			$post = $fA['content_date_published'];
			$date = date("F Y", $post);
			
			if(!in_array($date, $months)) {
				$months[] = $date;
				$s .= '<li><a href="http://'.$site.'/'.date("Y", $post).'/'.date("m", $post).'/" title="'.$date.'">'.$date.'</a></li>';
			}
		}
		
		$f = fopen("system/cache/activemonths_".$site, "w");
		fwrite($f, $s);
		fclose($f);
	}
	
	function TagCloud($siteN = '') {
		global $mysql, $site;
		
		if($siteN != '')
			$site = $siteN;
		
		$tags = array();
		$query = $mysql->Query("SELECT content_tags FROM posts WHERE content_site = @site", array("site" => $site));
		$max = 0;
		
		while($tag = $mysql->FetchArray($query)) {
			$z = explode(",", $tag["content_tags"]);
			
			foreach($z as $item) {
				$ident = str_replace(" ", "+", trim($item));
				
				if(isset($tags[$ident]) && $tags[$ident] > 0) {
					$tags[$ident]++;
					
					if($tags[$ident] > $max) $max = $tags[$ident];
				}else{
					$tags[$ident] = 1;
				}
			}
		}
		
		//Code below is necessary for shuffling
		$tagsNew = array();
		foreach($tags as $key => $value) {
			$tagsNew[] = array("tag" => $key, "n" => $value);
		}
		
		shuffle($tagsNew);
		
		$output = "<div id=\"tagcloud\"><ul>";
		foreach($tagsNew as $tag) {
			$key = $tag["tag"];
			$value = $tag["n"];
			$percent = floor(($value / $max) * 100);
			
			$class = "cloud-".round($percent, -1);
			
			$output .= "<li class=\"".$class."\"><a href=\"/tag/".$key."/\">".str_replace("+", " ", $key)."</a></li>";
		}
		
		$output .= "</ul><div style=\"clear: both; display: block\"></div></div>";
		
		$f = fopen("system/cache/tagcloud_".$site, "w");
		fwrite($f, $output);
		fclose($f);
	}
	
	function TagCloudLinks($siteN = '') {
		global $mysql, $site;
		
		if($siteN != '')
			$site = $siteN;
		
		$output = '';
		
		$tags = array();
		$query = $mysql->Query("SELECT content_tags FROM posts WHERE content_site = @site", array("site" => $site));
		$max = 0;
		
		while($tag = $mysql->FetchArray($query)) {
			$z = explode(",", $tag["content_tags"]);
			
			foreach($z as $item) {
				$ident = str_replace(" ", "+", trim($item));
				
				if(isset($tags[$ident]) && $tags[$ident] > 0) {
					$tags[$ident]++;
					
					if($tags[$ident] > $max) $max = $tags[$ident];
				}else{
					$tags[$ident] = 1;
				}
			}
		}
		
		//Code below is necessary for shuffling
		$tagsNew = array();
		foreach($tags as $key => $value) {
			$tagsNew[] = array("tag" => $key, "n" => $value);
		}
		
		shuffle($tagsNew);
		
		foreach($tagsNew as $tag) {
			$key = $tag["tag"];
			$value = $tag["n"];
			$percent = floor(($value / $max) * 100);
			
			$class = "cloud-".round($percent, -1);
			
			//$output .= "<li class=\"".$class."\"><a href=\"/tag/".$key."/\">".str_replace("+", " ", $key)."</a></li>";
			
			$output .= '<a href="http://'.$site.'/tag/'.$key.'/" title="'.$value.' topic" rel="tag" class="'.$class.'">'.str_replace("+", " ", $key).'</a> ';
		}
		
		$f = fopen("system/cache/tagcloudlinks_".$site, "w");
		fwrite($f, $output);
		fclose($f);
	}
}
?>
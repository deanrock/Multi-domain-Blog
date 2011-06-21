<?php
if(!$on_page) exit;
header("Content-Type: application/xml; charset=utf-8");
echo "<"."?xm"."l "."versi"."on=\"1.0\" encoding=\"UTF-8\""."?".">"; ?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	>

<channel>
	<title><?=$siteTitle?></title>
	<atom:link href="http://<?=$site?>/feed/" rel="self" type="application/rss+xml" />
	<link>http://<?=$site?></link>
	<description><?=$siteInfo["description"]?></description>
	<language>en</language>
	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
<?php
$mysql->Query("SELECT * FROM posts LEFT JOIN categories ON categories.category_id = posts.content_category WHERE posts.content_site = @site AND posts.content_date_published < @time AND posts.content_type = 'post' ORDER BY posts.content_date_published DESC LIMIT 10", array("site" => $site, "time" => time(0)));

	while($data = $mysql->FetchArray()) {
	
	$comments_count = ($data["content_comments"] > 0) ? $data["content_comments"] : '0';
	
	$n = array("content_title" => $data["content_title"], "content_content" => $data["content_content"],
		"content_tags" => $data["content_tags"], "link" => "http://".$site."/".$data["content_url"]."/",
		"linkDate" => "http://".$site."/".date("Y", $data["content_date_published"])."/".date("m", $data["content_date_published"])."/".$data["content_url"]."/",
		"content_date_published" => $data["content_date_published"],
		"category_url" => $data["category_url"], "category_title" => $data["category_title"],
		"content_comments" => $data["content_comments"], "content_allow_comments" => $data["content_allow_comments"]);
	?>
	<item>
		<title><?=$n["content_title"]?></title>
		<link><?=$n["link"]?></link>
		<comments><?=$n["link"]?>#comments</comments>
		<pubDate><?=date("D, d M y H:i:s O",$n["content_date_published"])?></pubDate>
		<dc:creator>admin</dc:creator>
				<category><![CDATA[<?=$n["category_title"]?>]]></category>
		<?php
		$r = '';
		$x = explode(",", $n["content_tags"]);
		
		foreach($x as $y) {
			$y = trim($y);
			
			echo '<category><![CDATA['.$y.']]></category>';
		}
		?>

		<guid isPermaLink="true"><?=$n["link"]?></guid>
		<description><![CDATA[<?=substr(String::RemoveBBCode($data["content_content"]), 0, 300)?> [...]]]></description>
			<content:encoded><![CDATA[<?=String::BBCode(nl2br(str_replace("/media/", "http://".$site."/media/", $data["content_content"])))?>
]]></content:encoded>
		<slash:comments><?=$comments_count?></slash:comments>
	</item>
	<?php
	}
	?>
	</channel>
</rss>

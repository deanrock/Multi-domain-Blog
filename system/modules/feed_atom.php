<?php
if(!$on_page) exit;
echo "<"."?xm"."l "."versi"."on=\"1.0\" encoding=\"UTF-8\""."?".">"; ?>
<feed
  xmlns="http://www.w3.org/2005/Atom"
  xmlns:thr="http://purl.org/syndication/thread/1.0"
  xml:lang="en"
  xml:base="http://<?=$site?>/feed/atom/"
   >
	<title type="text"><?=$siteTitle?></title>
	<subtitle type="text"><?=$siteInfo["description"]?></subtitle>
	<link rel="alternate" type="text/html" href="http://<?=$site?>" />
	<id>http://<?=$site?>/feed/atom/</id>
	<link rel="self" type="application/atom+xml" href="http://<?=$site?>/feed/atom/" />
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
			<entry>
		<author>
			<name>admin</name>
					</author>
		<title type="html"><![CDATA[<?=$n["content_title"]?>]]></title>
		<link rel="alternate" type="text/html" href="<?=$n["link"]?>" />
		<id><?=$n["link"]?></id>
		<updated><?=date("D, d M y H:i:s O",$n["content_date_published"])?></updated>
		<published><?=date("D, d M y H:i:s O",$n["content_date_published"])?></published>
		<category scheme="http://www.kiwi-network.com" term="<?=$n["category_title"]?>" />
		<?php
		$r = '';
		$x = explode(",", $n["content_tags"]);
		
		foreach($x as $y) {
			$y = trim($y);
			
			echo '<category scheme="http://www.kiwi-network.com" term="'.$y.'" />';
		}
		?>
		<summary type="html"><![CDATA[<?=String::BBCode(nl2br(str_replace("/media/", "http://".$site."/media/", $data["content_content"])))?>
]]></content>
		<link rel="replies" type="text/html" href="<?=$n["link"]?>#comments" thr:count="<?=$comments_count?>"/>
		<thr:total><?=$comments_count?></thr:total>
	</entry>
	<?php
	}
	?>
</feed>
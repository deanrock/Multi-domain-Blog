<?php
if(!$on_page) exit;
echo "<"."?xm"."l "."versi"."on=\"1.0\" encoding=\"UTF-8\""."?".">"; ?>
<rss version="0.92">
<channel>
	<title><?=$siteTitle?></title>
	<link>http://<?=$site?></link>
	<description><?=$siteInfo["description"]?></description>
	<docs>http://backend.userland.com/rss092</docs>

	<language>en</language>
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
		<description><?=substr(String::RemoveBBCode($data["content_content"]), 0, 300)?> [...]</description>
		<link><?=$n["link"]?></link>
			</item>
<?php
}
?>
</channel>
</rss>

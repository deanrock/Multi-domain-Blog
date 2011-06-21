<?php
if(!$on_page) exit;

class Post {
	function Paging($query, $page, $data) {
		global $mysql, $site;
		
		$array = array("site" => $site, "time" => time(0), "data" => $data);
		
		$queryCount = 'SELECT COUNT(content_id) AS numrows FROM posts LEFT JOIN categories ON categories.category_id = posts.content_category WHERE posts.content_site = @site '.$query.' AND posts.content_date_published < @time ORDER BY posts.content_date_published DESC';
		
		$num = $mysql->FetchArray($mysql->Query($queryCount, $array));
		$num = $num['numrows'];
		
		if(!is_numeric($page)) {
			$page = 1;
		}
		
		$numQ = ($page-1) * 10;
		
		$array = array("site" => $site, "time" => time(0), "data" => $data, "num" => $numQ);
		
		$query = 'SELECT * FROM posts LEFT JOIN categories ON categories.category_id = posts.content_category WHERE posts.content_site = @site '.$query.' AND posts.content_date_published < @time ORDER BY posts.content_date_published DESC LIMIT #num, 10';
		
		$mysql->Query($query, $array);
		
		$fA = $mysql->FetchArray();
		
		$a = Post::ShowAll($fA);
		$content = $a['content'];
		
		return array("content" => $content, "fA" => $fA, "all" => $num, "page" => $page);
	}
	
	function AddComment($content_id) {
		global $_POST, $site, $mysql;
		
		$mysql->Insert("comments", array("comment_date" => time(0), "comment_author" => $_POST['blog_author'],
		"comment_content" => $_POST['blog_comment'], "comment_author_email" => $_POST['blog_email'],
		"comment_author_url" => $_POST['blog_url'], "comment_type" => '', "comment_site" => $site,
		"content_id" => $content_id, "comment_author_ip" => $_SERVER['REMOTE_ADDR']));
		
		$id = $mysql->InsertId();
		
		$mysql->Query("UPDATE posts SET content_comments = content_comments + 1 WHERE content_id = @id", array("id" => $content_id));
		
		return $id;
	}
	
	function ReturnOne($data, $mode = 'multiple') {
		global $site, $mysql;
		$comments = array();
		
		if($data["content_comments"] > 0 && $data["content_allow_comments"] && $mode == 'single') {
			$query = $mysql->Query("SELECT * FROM comments WHERE content_id = @id AND comment_type <> 'pingback' ORDER BY comment_date", array("id" => $data["content_id"]), 0); //0 - tells MySQL not to save query - necessary
			
			
			
			while($comment = $mysql->FetchArray($query)) {
				$comments[] = array(
					"comment_author" => $comment["comment_author"],
					"comment_date" => $comment["comment_date"],
					"comment_content" => String::BBCode($comment["comment_content"]),
					"comment_author_url" => $comment["comment_author_url"]
				);
			}
		}
		
		$user_info = array();
		$user_info["email"] = '';
		$user_info["name"] = '';
		
		//User comments data
		if(isset($_COOKIE['comments_data'])) {
			$x = base64_decode($_COOKIE['comments_data']);
			
			$x = explode("::::", $x);
			
			$user_info["email"] = $x[1];
			$user_info["name"] = $x[0];
		}
		
		$n = array("content_title" => $data["content_title"], "content_content" => $data["content_content"],
		"content_tags" => $data["content_tags"], "link" => "http://".$site."/".$data["content_url"]."/",
		"linkDate" => "http://".$site."/".date("Y", $data["content_date_published"])."/".date("m", $data["content_date_published"])."/".$data["content_url"]."/",
		"content_date_published" => $data["content_date_published"],
		"category_url" => $data["category_url"], "category_title" => $data["category_title"],
		"content_comments" => $data["content_comments"], "content_allow_comments" => $data["content_allow_comments"],
		"mode" => $mode, "comments" => $comments, "content_id" => $data["content_id"], "user" => $user_info,
		"content_votes" => $data["content_votes"], "content_rating" => $data["content_rating"]);
		
		return PostTemplate($n);
	}
	
	function Exists($url) {
		global $mysql, $site;
		
		if($mysql->NumRows($mysql->Query("SELECT * FROM posts LEFT JOIN categories ON categories.category_id = posts.content_category WHERE posts.content_site = @site AND content_url = @url AND posts.content_date_published < @time ORDER BY posts.content_date_published DESC", array("url" => $url, "site" => $site, "time" => time(0)))) > 0) {
			return 1;
		}
		
		return 0;
	}
	
	function Latest() {
		global $mysql, $site;
		
		$mysql->Query("SELECT * FROM posts LEFT JOIN categories ON categories.category_id = posts.content_category WHERE posts.content_site = @site AND posts.content_date_published < @time AND posts.content_type = 'post' ORDER BY posts.content_date_published DESC LIMIT 10", array("site" => $site, "time" => time(0)));
		
		return Post::ShowAll();
	}
	
	function Show($url = '', $type = '') {
		global $mysql, $site;
		
		$content = '';
		$title = '';
		
		if($url == '') {
			$fA = $mysql->FetchArray();
			
			if($fA['content_title']) {
				$title = $fA['content_title'];
				
				$content = Post::ReturnOne($fA, "single");
			}else{
				return "There is no posts yet!";
			}
		}
		
		return array('title' => $title, 'content' => $content);
	}
	
	function ShowAll($fA = '') {
		global $mysql, $site;
		
		$content = '';
		$title = '';
		
		if($fA) {
			if($fA['content_title']) {
					//$title = $fA['content_title'];
					$content .= Post::ReturnOne($fA);
				}
		}
		
			while($fA = $mysql->FetchArray()) {
				if($fA['content_title']) {
					//$title = $fA['content_title'];
					$content .= Post::ReturnOne($fA);
				}
			}
		
		return array('title' => $title, 'content' => $content);
	}
}
?>
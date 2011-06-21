<?php
if(!$on_page) exit;

if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == "1") {
	//administration/delete_comment/
	if($url->Get(1) == "delete_comment") {
		if($url->Get(2)) {
			$ip = $mysql->FetchArray($mysql->Query("SELECT * FROM comments WHERE comment_id = @id", array("id" => $url->Get(2))));
			
			$ip = $ip['comment_author_ip'];
			
			$mysql->Insert("blocked_ips", array("ip" => $ip));
			
			$mysql->Query("DELETE FROM comments WHERE comment_id = @id", array("id" => $url->Get(2)));
		}
		
		$mysql->Query("UPDATE posts SET content_comments = (SELECT COUNT(*) FROM comments WHERE content_id = posts.content_id)");
		echo "Ok";
	}
	//Administration area
	switch($url->Get(1)) {
		case "post":
			if($url->Get(2) == "add") {
				//Add new
				if(isset($_POST["title"]) && $_POST["title"]) {
					if($_POST["type"] == "page") {
						$type= "page";
					}else{
						$type = "post";
					}
					
					$tags = String::Protect($_POST["tags"]);
					$title = String::Protect($_POST['title']);
					
					if(!$_POST["post"]) {
						$url = String::FindUrl('posts', 'content_url', String::ToUrl($title));
					}
					
					$category = ($_POST["category"] > 0) ? $_POST["category"] : 0;
					
					$a = array("\'", "\\'", "\\\\\\\"", "\\\\", "\\\\\\\&quot;", "\\&quot;");
					$b = array("'", "'", '"', "\\", "&quot;", "&quot;");
					
					$c = str_replace($a, $b, $_POST['content']);
					
					if($_POST["post"]) {
						$mysql->Query("UPDATE posts SET content_title = @title, content_content = @content, content_tags = @tags, content_category = @category, content_type = @type WHERE content_id = @id", array("title" => $title, "content" => $c, "tags" => $_POST['tags'], "id" => $_POST["post"], "category" => $_POST['category'], "type" => $_POST['type']));
						
						echo $mysql->LastQuery();
					
					}else{
						$mysql->Insert("posts", array(
						'content_category' => $category,
						'content_title' => $title,
						'content_url' => $url,
						'content_content' => $c,
						'content_type' => $type,
						'content_tags' => $_POST['tags'],
						'content_site' => $site,
						'content_rating' => '3',
						'content_votes' => '0',
						'content_comments' => '0',
						'content_date' => time(0),
						'content_date_published' => time(0)));
						
						echo $mysql->LastQuery();
					}
				}else{
					if($_POST["post"]) {
						$data = $mysql->FetchArray($mysql->Query("SELECT * FROM posts WHERE content_id = '".$_POST["post"]."'"));
					}
					
					$a = array("\'", "\\'", "\\\\\\\"", "\\\\", "\\\\\\\&quot;", "\\&quot;");
					$b = array("'", "'", '"', "\\", "&quot;", "&quot;");
					
					$c = str_replace($a, $b, $data['content_content']);
					
					$c=str_replace("\\\"","\"",$c);
					
					//Show form
					$content .= '<form action="" method="post">Title: <input type="text" name="title" value="'.$data['content_title'].'" /><br />
					Content: <br />
					<textarea name="content" style="height: 500px; width: 100%">'.$c.'</textarea><br />';
					
					if($_POST["post"]) {
						$content .= '<input type="submit" value="Edit" /><br />';
					}else{
						$content .= '<input type="submit" value="Add" /><br />';
					}
					
					$content .= 'Tags: <input type="text" name="tags" value="'.$data['content_tags'].'" /> (seperate with comma)<br />
					Category: (only applicable to posts) <select name="category"><option value=""></option>';
					
					$query = $mysql->Query("SELECT * FROM categories WHERE category_site = @site", array("site" => $site));
					
					while($category = $mysql->FetchArray()) {
						$a = '';
						
						if($category['category_id'] == $data['content_category']) {
							$a = ' selected';
						}
						
						$content .= '<option value="'.$category['category_id'].'"'.$a.'>'.$category['category_title'].'</option>';
					}
					
					
					
					$content .= '</select><br />';
					
					
					$type = $data['content_type'];
					$content .= '
					Type: <select name="type">
					<option value="post"'.(($type == 'post') ? ' selected' : '').'>Post</option>
					<option value="page"'.(($type == 'page') ? ' selected' : '').'>Page</option>
					</select>';
					
					if($_POST["post"]) { $content .= '<input type="hidden" name="post" value="'.$_POST["post"].'" />'; }
					
					$content .= '</form>';
				}
			}else if($url->Get(2) == "edit") {
				$content .= '<form action="/administration/post/add/" method="post">
				<select name="post">
				';
				$q = $mysql->Query("SELECT * FROM posts ORDER BY content_site, content_date_published");
				
				while($p = $mysql->FetchArray($q)) {
					$content .= '<option value="'.$p["content_id"].'">'.$p['content_site'].' - '.$p['content_title'].'</option>';
				}
				$content .= '<input type="submit" value="Edit" />
				</select>
				</form>';
			}
		break;
		
		case "categories":
			if($url->Get(2) == "add" && isset($_POST["name"])) {
				$mysql->Insert("categories", array("category_title" => $_POST["name"], "category_url" => String::String2Url(String::Protect($_POST["name"]))));
				header('Location: /administration/categories/');
				exit;
			}
			
			$content .= '<form action="/administration/categories/add" method="post">
			Name: <input type="text" name="name" /> <input type="submit" value="Add" />
			</form>
			';
		break;
		
		default:
			$content .= '
			<ul><li><a href="/administration/post/add">Add Post</a></li>
			<li><a href="/administration/post/edit">Edit post</a></li>
			<li><a href="/administration/delete-post/">Delete post</a></li>
			';
	}
}else{
	if(isset($_POST["password"]) && $_POST["password"] == "password") {
		$_SESSION["logged_in"] = "1";
		if($_POST['u']) {
			header('Location: /'.$_POST['u']);
		}else{
		header('Location: /administration/');
		}
	}
	?>
	<form action="/administration/" method="post">
	<?php
	if($url->Get(1) == "delete_comment") {
		echo '<input type="hidden" name="u" value="delete_comment/'.$url->Get(2).'/" />';
	}
	?>
	Password: <input type="password" name="password" /><input type="submit" value="Login" />
	</form>
	<?php
}
?>
<?php
if(!$on_page) exit;

function PostTemplate($data) {
	global $site;
	$date = $data["content_date_published"];
	
	$comments = '';
	$comments_count = ($data["content_comments"] > 0) ? $data["content_comments"] : '0';
	
	if($data["content_comments"] > 0) {
		$comments .= $data["content_comments"]." comments";
	}else{
		$comments .= 'Leave a Comment';
	}

	$content = '<div class="topPost">
  <h2 class="topTitle"><a href="'.$data['linkDate'].'">'.$data['content_title'].'</a></h2>
  <p class="topMeta">by <a href="#" title="Posts by someone16">someone16</a> on '.date("M.d, Y", $date).', under <a href="http://'.$site.'/category/'.$data['category_url'].'/" title="View all posts in '.$data['category_title'].'" rel="category tag">'.$data['category_title'].'</a></p>

  <div class="topContent">
  '.String::BBCode(nl2br($data["content_content"])).'
</div>';
  if($data["mode"] != "single") { $content .= '<span class="topComments"><a href="'.$data['linkDate'].'#comments" title="Comment on ASP.Net MVC">'.$comments.'</a></span>';
  }
  
  $content .= '

  <span class="topTags"><em>:</em>'.String::ConvertTags($data["content_tags"]).'</span>';
  
  if($data["mode"] != "single") { $content .= '
  <span class="topMore"><a href="'.$data['linkDate'].'">more...</a></span>';
 	}
 	
 	$content .= '
<div class="cleared"></div>
</div> <!-- Closes topPost --><br/>';

if($data["content_allow_comments"] && $data["mode"] == "single") {
		$content .= '<div id="comments">
				';
			
			if($data["content_comments"] > 0) {
			
			$content .= '<h3 id="commentstitle">'.$comments_count.' Comments for this entry</h3>
			<ul class="commentlist">';
			foreach($data["comments"] as $comment) {
				$content .= '
				   <li class="comment even thread-even depth-1" id="">
		     <div id="">
					<a class="gravatar">
		
					<img alt="" src="/media/comment.png" class="avatar avatar-60 photo" height="60" width="60" />			</a>
		
					<div class="commentbody">
					<cite><a href="'.$comment["comment_author_url"].'">'.$comment["comment_author"].'</a></cite><br />
					<small class="commentmetadata"><a href="#" title="">'.date("F jS, Y - H:i", $comment["comment_date"]).'</a> </small>
		
					<p>'.String::BBCode(nl2br($comment["comment_content"])).'</p>
					</div><div class="cleared"></div>
		
		      <div class="reply">
		               </div>
		     </div>
		</li>';
			}
			
			$content .= '</ul>';
		}
			$content .= '
			<div class="cleared"></div>
	
		<div id="respond">
		<h3>Leave a Reply</h3>
		<div id="comment_sent"></div>
		<script>document.write(\'<form action="" method="post" id="commentform">\');</script>
		<input type="hidden" name="post_number" value="'.$data["content_id"].'" />
		<p><input type="text" name="blog_author" id="author" value="'.$data['user']['name'].'" size="22" tabindex="1" />
		<label for="author"><small>Name (required)</small></label></p>
		<p><input type="text" name="blog_email" id="email" value="'.$data['user']['email'].'" size="22" tabindex="2" />
		<label for="email"><small>Mail (will not be published) (required)</small></label></p>
		<p><input type="text" name="blog_url" id="url" value="" size="22" tabindex="3" />
		<label for="url"><small>Website</small></label></p>
		<p><textarea name="blog_comment" id="blog_comment" cols="100%" rows="10" tabindex="4"></textarea></p>
		<p><input name="submit" type="submit" id="submit" class="submitbutton" tabindex="5" value="Leave comment" />
		</p>
		
		</form>
		</div>
	</div>';
}

	return $content;
}
?>
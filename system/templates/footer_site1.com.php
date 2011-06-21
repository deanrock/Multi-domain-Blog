<?php
if(!$on_page) exit;

if($template["newerOlder"] || $url->Get(0) == '') {
	$all = $template["newerOlderAll"];
	$page = $template["newerOlderPage"];
	
	if($url->Get(0) == '') {
		$page = 1;
		$all = 10;
	}
?><div id="nextprevious"><?php
if($page > 1) {
	echo '<div class="alignright"><a href="http://'.$site.$template["newerOlderLink"].($page-1).'/" >Newer Entries &raquo;</a></div>';
	
}

if($page*10 < $all || $url->Get(0) == '') {
	echo '<div class="alignleft"><a href="http://'.$site.$template["newerOlderLink"].($page+1).'/" >&laquo; Older Entries</a></div>';
}
?>
<div class="cleared"></div>
</div>
<?php
}
?>

</div> <!-- Closes contentwrapper-->
<div id="sidebars">

<div id="sidebar_full">
<ul>

 <li>
<div id="welcome">


<h2>Howdy. Welcome to Blog title!</h2><p>Thanks for dropping by! Feel free to join the discussion by leaving comments, and stay updated by subscribing to the <a href='http://<?php echo $site; ?>/feed/'>RSS feed</a>. See ya around!</p>
</div><!-- Closes welcome --> </li>
 <li>
 <div class="sidebarbox">
 <h2>Recent Posts</h2>
 <ul>
	<?php echo file_get_contents("system/cache/recentposts_".$site); ?>
 </ul>
 </div>
 </li>

 <li>
 <div class="sidebarbox">
 <h2>Browse by tags</h2>
<?php echo file_get_contents("system/cache/tagcloudlinks_".$site); ?>
</div>
 </li>


</ul>
</div><!-- Closes Sidebar_full -->


<div id="sidebar_left">
<ul>

<li>
<div class="sidebarbox">
<h2>Categories</h2>
<ul>
  	<?php echo file_get_contents("system/cache/categories_".$site); ?>
</ul>
</div>
</li>

</ul>

</div> <!-- Closes Sidebar_left -->

<div id="sidebar_right">

<ul>

<li>
<div class="sidebarbox">
<h2>Meta</h2>
<ul>
  <li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>

  </ul>
</div>
</li>

</ul>

</div> <!-- Closes Sidebar_right -->


<div class="cleared"></div>
</div> <!-- Closes Sidebars --><div class="cleared"></div>

</div><!-- Closes Main -->

<div id="morefoot">

<div class="col1">
<h3>Looking for something?</h3>
<p>Use the form below to search the site:</p>
<form method="get" id="searchform" action="http://<?php echo $site; ?>/">
<p>
<input type="text" value="Search keywords" name="s" id="searchbox" onfocus="this.value=''"/>
<input type="submit" class="submitbutton" value="Find it" />
</p>
</form><p>Still not finding what you're looking for? Drop a comment on a post or contact us so we can take care of it!</p>
</div>

<div class="col2">
<h3>Visit our friends!</h3><p>A few highly recommended friends...</p><ul>
</ul>
</div>

<div class="col3">
<h3>Archives</h3><p>All entries, chronologically...</p><ul>
<?php echo file_get_contents("system/cache/activemonths_".$site); ?>
 </ul>

</div>

<div class="cleared"></div>
</div><!-- Closes morefoot -->
<div id="footer">
<div id="footerleft">
<p>Theme by <a href="http://samk.ca/freebies/" title="Pixel" target="_blank">pixel</a>. Sweet icons by <a href="http://famfamfam.com/" target="_blank">famfamfam</a>. <a href="#main">Back to top &uarr;</a></p>
<!-- If you want to remove the credits, please consider making a donation. Thanks! -->
</div>

<div id="footerright">
</div>

<div class="cleared"></div>
</div><!-- Closes footer -->

</div><!-- Closes wrapper -->
<script type="text/javascript">
function $(e){if(typeof e=='string')e=document.getElementById(e);return e};
function collect(a,f){var n=[];for(var i=0;i<a.length;i++){var v=f(a[i]);if(v!=null)n.push(v)}return n};

ajax={};
ajax.x=function(){try{return new ActiveXObject('Msxml2.XMLHTTP')}catch(e){try{return new ActiveXObject('Microsoft.XMLHTTP')}catch(e){return new XMLHttpRequest()}}};
ajax.serialize=function(f){var g=function(n){return f.getElementsByTagName(n)};var nv=function(e){if(e.name)return encodeURIComponent(e.name)+'='+encodeURIComponent(e.value);else return ''};var i=collect(g('input'),function(i){if((i.type!='radio'&&i.type!='checkbox')||i.checked)return nv(i)});var s=collect(g('select'),nv);var t=collect(g('textarea'),nv);return i.concat(s).concat(t).join('&');};
ajax.send=function(u,f,m,a){var x=ajax.x();x.open(m,u,true);x.onreadystatechange=function(){if(x.readyState==4)f(x.responseText)};if(m=='POST')x.setRequestHeader('Content-type','application/x-www-form-urlencoded');x.send(a)};
ajax.get=function(url,func){ajax.send(url,func,'GET')};
ajax.gets=function(url){var x=ajax.x();x.open('GET',url,false);x.send(null);return x.responseText};
ajax.post=function(url,func,args){ajax.send(url,func,'POST',args)};
ajax.update=function(url,elm){var e=$(elm);var f=function(r){e.innerHTML=r};ajax.get(url,f)};
ajax.submit=function(url,elm,frm){var e=$(elm);var f=function(r){e.innerHTML=r};ajax.post(url,f,ajax.serialize(frm))};

function comment() {
	ex = document.forms[0];
	//if (!e) var e = window.event;
	$('comment_sent').innerHTML = '<img src="/media/ajax-loader.gif" />';
	var f=function(r){location.reload(true);};
	ajax.post('/ajax-comment/',f,ajax.serialize(ex));
	return false;
}

if(document.forms[0].post_number.value > 0) {
	var element = document.forms[0];
	element.onsubmit = comment;
	if (element.captureEvents) element.captureEvents(Event.ONSUBMIT);
}
</script>
</body>
</html>
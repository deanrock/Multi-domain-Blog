<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
<title>Blog title</title>
<meta name="robots" content="follow, all" />
<link rel="stylesheet" href="/media/style.css" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="http://<?php echo $site; ?>/feed/" /> 
<link rel='index' title='Blog title' href='http://<?php echo $site; ?>' />
<script type="text/javascript"><!--//--><![CDATA[//><!--
sfHover = function() {
	if (!document.getElementsByTagName) return false;
	var sfEls = document.getElementById("nav").getElementsByTagName("li");

	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}

}
if (window.attachEvent) window.attachEvent("onload", sfHover);
//--><!]]></script>
<!--[if lt IE 8]>
<link href="/media/ie.css" rel="stylesheet" type="text/css" />
<![endif]-->

<!--[if lt IE 7]>
<link href="/media/ie6.css" rel="stylesheet" type="text/css" />
<script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE7.js" type="text/javascript"></script>
<![endif]-->
</head>
<body>
<div id="wrapper">

<div id="header">

<div id="logo">
<h1><a href="http://<?php echo $site; ?>">Blog title</a></h1>
<span>Blog about electronics and programming</span>
</div>


<div id="topright">
<ul>
  <li class="page_item"><a href="http://<?php echo $site; ?>/about/" title="About">About</a></li>

  <li><a href="#searchform">search</a></li>
  <li><a href="#main">skip to content &darr;</a></li>
</ul>
</div>
<div class="cleared"></div>
</div> <!-- Closes header -->



<div id="catnav">
<div id="toprss"><a href="http://<?php echo $site; ?>/feed/"><img src="/media/images/rss-trans.png" alt="" width="65" height="24" /></a></div> <!-- Closes toprss -->

<ul id="nav">
  <li><a href="http://<?php echo $site; ?>">Home</a></li>
  	<li class="cat-item"><a href="http://<?php echo $site; ?>/category/computers/" title="View all posts filed under Computers">Computers</a>
</li>
	<li class="cat-item"><a href="http://<?php echo $site; ?>/category/electronics/" title="View all posts filed under Electronics">Electronics</a>
</li>
	<li class="cat-item"><a href="http://<?php echo $site; ?>/category/life/" title="View all posts filed under Life">Life</a>
</li>
	<li class="cat-item"><a href="http://<?php echo $site; ?>/category/uncategorized/" title="View all posts filed under Uncategorized">Uncategorized</a>

</li>
</ul>
</div> <!-- Closes catnav -->

<div class="cleared"></div>

<div id="main">

<div id="contentwrapper">
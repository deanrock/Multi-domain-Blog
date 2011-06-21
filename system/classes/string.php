<?php
if(!$on_page) exit;

class String {
	function Protect($string) {
		$string = mysql_escape_string(strip_tags(htmlspecialchars($string)));
		return $string;
	}
	
	function ConvertTags($tags) {
		$r = '';
		$x = explode(",", $tags);
		
		foreach($x as $y) {
			$y = trim($y);
			
			$r .= '<a href="/tag/'.str_replace(" ", "+", $y).'/">'.$y.'</a> ';
		}
		
		return $r;
	}
	
	function ToUrl($kaj)
	{
		$kaj=strtolower($kaj);
		$kaj=str_replace(array(' ','č','ć','ž','š','đ','Č','Ć','Ž','Š','Đ'),array('-','c','c','z','s','d','c','c','z','s','d'),$kaj);
		$dovoljeno=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','v','u','z','w','x','y','q','-','_','.','0','1','2','3','4','5','6','7','8','9');
		
		$st=strlen($kaj);
		$x="";
		for($i=0;$i<$st;$i++)
		{
			if(in_array($kaj{$i},$dovoljeno))
			{
				$x.=$kaj{$i};
			}else{
				$x.="-";
			}
		}
		
		$x = str_replace("--", "-", $x);
		return $x;
	}
	
	function FindUrl($table, $field, $url) {
		global $mysql;
		
		$name="";
		$next="";
		while($name == "")
		{
			$next=$url.$st;
			if($mysql->NumRows($mysql->Query("SELECT * FROM #table WHERE #field = @url", array("table" => $table, "field" => $field, "url" => $url))) <= 0)
			{
				$name = $next;
			}else{
				if($st == "")
				{
					$st = 2;
				}else{
					$st = $st+1;
				}
			}
		}
	
		return $name;
	}

	function ToDate($date, $type="") {
		$return = "d.m.Y H:i:s";
		
		if($type == "date") $return = "d.m.Y";
		
		if($type == "time") $return = "H:i:s";
		
		if(date("d.m.Y", strtotime($date)) == "01.01.1970") {
			return date($return, $date);
		}
		
		return date($return, strtotime($date));
	}
	
	function RemoveBBCode($c) {
		//$c=preg_replace('/\[(.*)\]/is', '', $c);
		$c=preg_replace('/\[(.*)\]/', '', $c);
		
		return $c;
	}

	function BBCode($c)
	{
		//$besedilo=htmlentities($besedilo);
		//$besedilo=str_replace("\\\\","\\",$besedilo);
		//$besedilo=str_replace("\\\"","\"",$besedilo);
		$c=str_replace("\\&quot;","\"",$c);
		$c = str_replace("\\'", "'", $c);
		$c=str_replace("\\\"","\"",$c);
		
	    $prej=array(
	                '/\[b\](.*?)\[\/b\]/is',                                
	                '/\[i\](.*?)\[\/i\]/is',                                
	                '/\[u\](.*?)\[\/u\]/is',
	                '/\[quote\](.*?)\[\/quote\]/is',                                
	                '/\[url\=(.*?)\](.*?)\[\/url\]/is',                         
	                '/\[url\](.*?)\[\/url\]/is',                             
	                '/\[align\=(left|center|right)\](.*?)\[\/align\]/is',    
	                '/\[img\](.*?)\[\/img\]/is',                            
	                '/\[mail\=(.*?)\](.*?)\[\/mail\]/is',                    
	                '/\[mail\](.*?)\[\/mail\]/is',                            
	                '/\[font\=(.*?)\](.*?)\[\/font\]/is',                    
	                '/\[size\=(.*?)\](.*?)\[\/size\]/is',                    
	                '/\[color\=(.*?)\](.*?)\[\/color\]/is',
					'/\[code\](.*?)\[\/code\]/is',
					'/\[center\](.*?)\[\/center\]/is',
					'/\[left\](.*?)\[\/left\]/is',
					'/\[right\](.*?)\[\/right\]/is',
					'/\[youtube\](.*?)\[\/youtube\]/is',
					'/\[nine=hd\](.*?)\[\/nine\]/is'
	                );
	//blockquote: '<div class="quote" style="padding-left: 20px"><i>$1</i></div>',
	    $potem=array(
	                '<strong>$1</strong>',
	                '<em>$1</em>',
	                '<u>$1</u>',
	                '<blockquote>$1</blockquote>',
	                '<a href="$1" target="_blank">$2</a>',
	                '<a href="$1" target="_blank">$1</a>',
	                '<div style="text-align: $1;">$2</div>',
	                '<img src="$1" />',
	                '<a href="mailto:$1">$2</a>',
	                '<a href="mailto:$1">$1</a>',
	                '<span style="font-family: $1;">$2</span>',
	                '<span style="font-size: $1;">$2</span>',
	                '<span style="color: $1;">$2</span>',
					'<blockquote>$1</blockquote>',
					'<p align="center">$1</p>',
					'<p align="left">$1</p>',
					'<p align="right">$1</p>',
					'<p align="center">
	<object width="560" height="340"><param name="movie" value="$1&hl=en&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="$1&hl=en&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object>
	<br />'."".'
	</p>',
	'<script type="text/javascript" src="http://im.myplace.si/stream/swfobject.js"></script>
			<div id="video" style="text-align: center"></div>
			<script type="text/javascript">
			  var so = new SWFObject("http://im.myplace.si/stream/player.swf","ply","480","270","9","#");
			  so.addParam("allowfullscreen","true");
			  so.addParam("allowscriptaccess","always");
			  so.addParam("wmode","opaque");
			  so.addVariable("image","http://stream.nine.si/$11.jpg");
			  so.addVariable("file","http://stream.nine.si/$1.mp4");
			  so.addVariable("skin","http://im.myplace.si/stream/overlay.swf");
			  so.addVariable("frontcolor","ffffff");
			  so.addVariable("lightcolor","cc9900");
			  so.addVariable("controlbar","over");
			  so.addVariable("stretching","fill");
			  so.write("video");
			</script><br />
			<a href="http://nine.si/video/$1/hd" target="_blank">Play video in new window</a>'
					);
					
	    $c=preg_replace($prej, $potem, $c);
		return $c;
	}
	
	function CreateEditor($vsebina="")
{
?>

			  
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td colspan="2" >
			<input type="button" class="button" accesskey="b" name="addbbcode0" value=" B " style="font-weight:bold; width: 30px" onClick="bbstyle(0)" onMouseOver="helpline('b')" />
			<input type="button" class="button" accesskey="i" name="addbbcode2" value=" i " style="font-style:italic; width: 30px" onClick="bbstyle(2)" onMouseOver="helpline('i')" />
			<input type="button" class="button" accesskey="u" name="addbbcode4" value=" u " style="text-decoration: underline; width: 30px" onClick="bbstyle(4)" onMouseOver="helpline('u')" />
			<!--<input type="button" class="button" accesskey="q" name="addbbcode6" value="Quote" style="width: 50px" onClick="bbstyle(6)" onMouseOver="helpline('q')" />
			<input type="button" class="button" accesskey="c" name="addbbcode8" value="Code" style="width: 40px" onClick="bbstyle(8)" onMouseOver="helpline('c')" />-->
			<input type="button" class="button" accesskey="l" name="addbbcode10" value="List" style="width: 40px" onClick="bbstyle(10)" onMouseOver="helpline('l')" />
			<input type="button" class="button" accesskey="o" name="addbbcode12" value="List=" style="width: 40px" onClick="bbstyle(12)" onMouseOver="helpline('o')" />
			<input type="button" class="button" accesskey="p" name="addbbcode14" value="Img" style="width: 40px"  onClick="bbstyle(14)" onMouseOver="helpline('p')" />
			<input type="button" class="button" accesskey="w" name="addbbcode16" value="URL" style="text-decoration: underline; width: 40px" onClick="bbstyle(16)" onMouseOver="helpline('w')" />
		</td>
	</tr>
	<tr>
		<td colspan="2" >
			&nbsp;Barva pisave:
			<select name="addbbcode18" onChange="bbfontstyle('[color=' + this.form.addbbcode18.options[this.form.addbbcode18.selectedIndex].value + ']', '[/color]');this.selectedIndex=0;" onMouseOver="helpline('s')">
			  <option style="color:black; background-color: #FAFAFA" value="#444444" class="genmed">Privzeto</option>
			  <option style="color:darkred; background-color: #FAFAFA" value="darkred" class="genmed">Temno rdeca</option>

			  <option style="color:red; background-color: #FAFAFA" value="red" class="genmed">Rdeca</option>
			  <option style="color:orange; background-color: #FAFAFA" value="orange" class="genmed">Oranžna</option>
			  <option style="color:brown; background-color: #FAFAFA" value="brown" class="genmed">Rjava</option>
			  <option style="color:yellow; background-color: #FAFAFA" value="yellow" class="genmed">Rumena</option>
			  <option style="color:green; background-color: #FAFAFA" value="green" class="genmed">Zelena</option>
			  <option style="color:olive; background-color: #FAFAFA" value="olive" class="genmed">Olivna</option>

			  <option style="color:cyan; background-color: #FAFAFA" value="cyan" class="genmed">Sinje modra</option>
			  <option style="color:blue; background-color: #FAFAFA" value="blue" class="genmed">Modra</option>
			  <option style="color:darkblue; background-color: #FAFAFA" value="darkblue" class="genmed">Temno modra</option>
			  <option style="color:indigo; background-color: #FAFAFA" value="indigo" class="genmed">Indigo</option>
			  <option style="color:violet; background-color: #FAFAFA" value="violet" class="genmed">Vijolicna</option>
			  <option style="color:white; background-color: #FAFAFA" value="white" class="genmed">Bela</option>

			  <option style="color:black; background-color: #FAFAFA" value="black" class="genmed">Crna</option>
			</select> &nbsp;Velikost pisave:<select name="addbbcode20" onChange="bbfontstyle('[size=' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + ']', '[/size]')" onMouseOver="helpline('f')">
			  <option value="7" class="genmed">Drobna</option>
			  <option value="9" class="genmed">Majhna</option>
			  <option value="12" selected class="genmed">Normalna</option>
			  <option value="18" class="genmed">Velika</option>

			  <option  value="24" class="genmed">Ogromna</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>
		<!-- smeški
		<?php
		$smeski=array(array("(ap)","airplane.gif"),
		array("(bah)","blacksheep.gif"),
		array("(^)","cake.gif"),
		array("(P)","camera.gif"),
		array("(au)","smile_confused.gif"),
		array("8o|","smile_baringteeth.gif"),
		array("(um)","umbrella.gif"),
		array("(#)","sun.gif"),
		array("(*)","star.gif"),
		array("(so)","soccerball.gif"),
		array("(sn)","snail.gif"),
		array(":-#","smile_zipit.gif"),
		array("|-)","smile_yawn.gif"),
		array(";)","smile_wink.gif"),
		array(":P","smile_tongue.gif"),
		array("*-)","smile_thinking.gif"),
		array(":D","smile_teeth.gif"),
		array(":^)","smile_sniff.gif"),
		array("+o(","smile_sick.gif"),
		array("(H)","smile_shades.gif"),
		array(":-*","smile_secret.gif"),
		array("^o)","smile_sarcastic.gif"),
		array(":(","smile_sad.gif"),
		array(":)","smile_regular.gif"),
		array("<:o)","smile_party.gif"),
		array(":-O","smile_omg.gif"),
		array("8-|","smile_nerd.gif"),
		array(":'(","smile_eyeroll.gif"));
		?>
		<table border="0">
		<tr align="center" valign="middle">
		<?php
		$i=1;
		foreach($smeski as $smesek)
		{
			if($i==4)
			{
				?>
				</tr><tr align="center" valign="middle">
				<?php
				$i=1;
			}else{
				$i=$i+1;
			}
			?>
			<td><a href="javascript:emoticon('<?=$smesek[0]?>')"><img src="http://www.devil.si/media/smeski/<?=$smesek[1]?>" border="0" alt="<?=$smesek[0]?>" title="<?=$smesek[0]?>" /></a></td>
			<?php
		}
		?>
		</tr>
		</table>
		smeški -->
		</td>
		<td align="center">
		<?php
		$vsebina=str_replace("<br />","",$vsebina);
		?>
			<p align="center"><textarea cols="60" rows="10" name="message"><?=$vsebina?></textarea></p>
		</td>
	</tr>
</table>

<?php
}
}
?>
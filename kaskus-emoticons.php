<?php
/*
Plugin Name: Kaskus Emoticons
Plugin URI: http://nartzco.com/blog/2009/10/23/kaskus-emoticons/
Description: Kaskus Emoticons is an emoticon set inspired by Kaskus, the Largest Indonesian Community - consisting of over a million active members from all over the world. The images which are used in this plugin are copyright of Kaskus
Version: 2.2
Author: Rehybrid
Author URI: http://nartzco.com/

Copyright 2009, nartzco.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

register_activation_hook( __FILE__, array('KaskusEmoticons', 'activate'));
register_deactivation_hook( __FILE__, array('KaskusEmoticons', 'deactivate'));
add_filter('the_content',array('KaskusEmoticons','replace'));
add_filter('comment_text',array('KaskusEmoticons','replace'));
add_action('comment_form', array('KaskusEmoticons', 'scut'));
add_action('wp_head', array('KaskusEmoticons', 'script'));
add_action('admin_menu', array('KaskusEmoticons','menu'));
add_filter( 'plugin_action_links', array('KaskusEmoticons', 'link'), 10, 2 );
require_once("kaskus-emoticons-list.php");

if(!class_exists('KaskusEmoticons')){
	class KaskusEmoticons {
		function activate(){
			$data = array(
				'backlink'=> 1
			);
	    	if (!get_option('kaskus_emoticons')){
	      		add_option('kaskus_emoticons', $data);
	    	} else {
	      		update_option('kaskus_emoticons', $data);
	    	}		
		}
		
		function deactivate(){
			delete_option('kaskus_emoticons');
		}

		function link( $links, $file ){
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
			
			if ( $file == $this_plugin ){
				$settings_link = '<a href="options-general.php?page=KaskusEmoticons">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link ); // before other links
			}
			return $links;
		}
		
		function menu(){
			add_options_page('Kaskus Emoticons', 'Kaskus Emoticons', 8, 'KaskusEmoticons', array('KaskusEmoticons','control'));
		}

		function control() {
			$options = $newoptions = get_option('kaskus_emoticons');
			if($_POST["kaskus_emoticons_action"]) {
				//print_r($_POST);
				$newoptions['backlink'] 	= strip_tags(stripslashes($_POST["kaskus_emoticons_backlink"]));
				if(trim($newoptions['backlink'])=="") $newoptions['backlink'] = 1;
			}
			if ($options != $newoptions) {
				$options = $newoptions;
				//print_r($options);
				update_option('kaskus_emoticons', $options);
			}
			
			$backlink= htmlspecialchars($options['backlink'], ENT_QUOTES);
?>
			<h2>KASKUS EMOTICONS</h2>
			<em>WordPress plugin written by <a href="http://nartzco.com">Rehybrid</a> </em><br /><br />
			<form method="post" action="options-general.php?page=KaskusEmoticons">
			<table>
			<tr>
			<td><?php _e('FOR BACKLINK ! You can disable this, but if you enable it, Thanks!'); ?></td>
			<td>
				<select name="kaskus_emoticons_backlink">
					<option value="1"<?php echo($backlink=="1"?" selected":"")?>>Enable</option>
					<option value="0"<?php echo($backlink=="1"?"":" selected")?>>Disable</option>
				</select>
			</td>
			</tr>
			</table>
			<input type="hidden" id="kaskus_emoticons_action" name="kaskus_emoticons_action" value="1" /><br />
			<input type="submit" id="kaskus_emoticons_submit" name="kaskus_emoticons_submit" value="Save Settings" />
			</form>
<?php
		}

		function replace($string){
			//die($string);
			$output = '';
			$textarr = preg_split("/(<\/?pre[^>]*>)|(<\/?p[^>]*>)|(<\/?a[^>]*>)|(<\/?object[^>]*>)|(<\/?img[^>]*>)|(<\/?embed[^>]*>)|(<\/?strong[^>]*>)|(<\/?b[^>]*>)|(<\/?i[^>]*>)|(<\/?em[^>]*>)/U", $string, -1, PREG_SPLIT_DELIM_CAPTURE); 
			$stop = count($textarr);
			//die(print_r($textarr,true));
			$s=false;
			for ($i = 0; $i < $stop; $i++){
				$content = $textarr[$i];
				if(preg_match("/^<pre/",trim($content)))$s = true;
				if(trim($content)=="^</pre>")$s = false;
				//if (!$s && (strlen($content) > 0) && ('<' != $content{0}))
				if (!$s)
				{ 
					$content = KaskusEmoticons::replace_code( $content ) ;
				}
				$output .= $content;
			}
			
			return $output;
			
		}

		function replace_code($content){
			global $KEReplace;
			//print_r($content);die;
			return strtr($content,$KEReplace);
		}
		
		function scut(){
			global $KEReplace;
			$opt = get_option('kaskus_emoticons');
			echo "<div id='kaskusemoticonslink' style=\"cursor:pointer;margin:2px\" onclick=\"kaskusemoticonsclink()\">[+] kaskus emoticons</div>";
			echo "<div id='kaskusemoticonscontent' style=\"display:none\">";
			foreach($KEReplace as $k=>$v){
				echo "<a title=\"".$k."\" href=\"javascript:kaskusemoticonsclick('".$k."')\" style=\"cursor:pointer;margin:1px;border:none\">".$v."</a>";
			}
			if(isset($opt['backlink']) && $opt['backlink']) echo "<br><a rel=\"follow\" href=\"http://nartzco.com\"><img src=\"http://nartzco.com/blog/wp-content/themes/my/images/bl.png\" alt=\"BY NARTZCO.COM\"></a>";
			else {
				if(!isset($opt['backlink']))  echo "<br><a rel=\"follow\" href=\"http://nartzco.com\"><img src=\"http://nartzco.com/blog/wp-content/themes/my/images/bl.png\" alt=\"BY NARTZCO.COM\"></a>";
			}
			echo "</div>"; 
		}
		
		function script(){
?>
<script language="javascript">
	var gOI = function(id){
		return document.getElementById(id);
	};
	
	var kaskusemoticonsclick = function(tag){
		var d = gOI("comment");
		var b = d.selectionStart, a = d.selectionEnd;
		d.value = d.value.substring(0, b) + " " + tag + " " + d.value.substring(a, d.value.length);
	};
	
	var kaskusemoticonsclink = function(){
		gOI("kaskusemoticonslink").innerHTML = gOI("kaskusemoticonscontent").style.display == "" ? "[+] kaskus emoticons":"[-] kaskus emoticons";
		gOI("kaskusemoticonscontent").style.display = gOI("kaskusemoticonscontent").style.display == "" ? "none":"";
	};
</script>	
<?php
		}
	}
}
?>
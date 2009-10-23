<?php
/*
Plugin Name: Kaskus Emoticons
Plugin URI: http://nartzco.com/blog/2009/10/23/kaskus-emoticons/
Description: Kaskus Emoticons is emoticons used in kaskus forum. Kaskus it self is the largest Indonesian community that has millions members and posts. Some images here are copyrighted by Kaskus.
Version: 1.0
Author: Rehybrid
Author URI: http://nartzco.com/

Copyright 2009, Rehybrid

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

add_filter('the_content',array('KaskusEmoticons','replace'));
add_filter('comment_text',array('KaskusEmoticons','replace'));

require_once("kaskus-emoticons-list.php");

if(!class_exists('KaskusEmoticons')){
	class KaskusEmoticons {
		function replace($content){
			global $KEReplace;
			//print_r($content);die;
			return strtr($content,$KEReplace);
		}
	}
}
?>
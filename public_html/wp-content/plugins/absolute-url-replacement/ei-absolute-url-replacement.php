<?php
/*
* Plugin Name:  EI Replace Absolute URL with Relative URL
* Plugin URI:   https://earthlinginteractive.com/
* Description:  Removes absolute URLs when saving content and adds them back in when displaying/editing content
* Version:      1.0
* Author:       Earthling Interactive
* Author URI:   https://earthlinginteractive.com/
* License:      GPL2
* License URI:  https://www.gnu.org/licenses/gpl-2.0.html

* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  Not much I can do when called directly.';
	exit;
}


// Remove absolute URLs when saving content
function ei_replace_absolute_url_with_relative( $content ) {

	$siteurl = untrailingslashit( get_bloginfo( 'wpurl' ) ); //retrieved from the “siteurl” record in the wp_options table
	$chr = chr(127);

	// replace any of the following patterns src=" src=' url(" url(' url( href=" href=' background_image=" srcset=" followed by siteurl
	$pattern = '(src=\\\\?"|src=\\\\?\'|url\(\\\\?"|url\(\\\\?\'|url\(\\\\?|href=\\\\?"|href=\\\\?\'|background_image=\\\\?"|srcset=\\\\?")';
	$content = preg_replace( $chr . $pattern . $siteurl . '(/?)' . $chr, '${1}/', $content);

	return $content;
}

add_filter( 'content_save_pre', 'ei_replace_absolute_url_with_relative' );
add_filter( 'excerpt_save_pre', 'ei_replace_absolute_url_with_relative' );
add_filter( 'widget_update_callback', 'ei_replace_absolute_url_with_relative');



// Add absolute URLs back in when displaying/editing content for SEO 
function ei_replace_relative_url_with_absolute( $content ) {
	
	$siteurl = untrailingslashit( get_bloginfo( 'wpurl' ) ); //retrieved from the “siteurl” record in the wp_options table
	$chr = chr(127);

	// replace any of the following patterns src=" src=' url(" url(' url( href=" href=' background_image=" srcset=" followed by siteurl
	$pattern = '(src=\\\\?"|src=\\\\?\'|url\(\\\\?"|url\(\\\\?\'|url\(\\\\?|href=\\\\?"|href=\\\\?\'|background_image=\\\\?"|srcset=\\\\?")';
	$content = preg_replace($chr . $pattern . '(/[^/])' . $chr, '${1}' . $siteurl . '${2}', $content);

	return $content;
}

add_filter( 'the_editor_content', 'ei_replace_relative_url_with_absolute' );
add_filter( 'the_content', 'ei_replace_relative_url_with_absolute' );
add_filter( 'get_the_excerpt', 'ei_replace_relative_url_with_absolute' );
add_filter( 'the_excerpt_rss', 'ei_replace_relative_url_with_absolute' );
add_filter( 'excerpt_edit_pre', 'ei_replace_relative_url_with_absolute' );
add_filter( 'widget_text', 'ei_replace_relative_url_with_absolute' );





// html for admin page
function ei_url_replacement_admin_page(){
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
    echo "<h1>URL Replacement</h1>";
    echo '<div class="sidebar"><a href="https://earthlinginteractive.com/"><img class="logo" src="' . esc_url( plugins_url( 'assets/images/earthling-interactive-logo.png', __FILE__ ) ) . '" ></a></div>';
   
    echo '<div class="section">';
    echo '<div class="section-header">About</div>';
    echo "<p>This plugin will replace abslute urls with relative when you save a post, page or widget. It will then display a absolute url in the editing pages and on the front end.</p><p> You can also use the search and replace below to change  urls that are already in the database.</p>";
    echo '</div>';
    echo '<div class="section">';
    echo '<div class="section-header">Search & Replace in Database</div>';
    echo "<p><strong>WARNING!</strong> Make sure you have backed up your database before using search & replace!</p><p>Select the tables from the list below that you would like to replace absolute urls with relative urls.<br>Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.</p>";
    echo '<form method="post">';
    echo '<select id="ei-table-select" name="select_tables[]" multiple="multiple" size="15">';
    global $wpdb;
    $tables = $wpdb->get_col( 'SHOW TABLES' );
	foreach ( $tables as $table ) {
		echo "<option value='$table'>$table </option>";
	}

	echo '</select><br><br>';
	echo '<strong>Dry Run</strong>  <input type="checkbox" name="dryrun" value="dryrun" checked="checked"> If checked, no changes will be made to the database and each change will be displayed so that you can check it over before making any changes to the database.<br><br>';
	echo '<input type="hidden" name="relative-urls">
        <button type="submit" class="button-primary">Run URL Replacement</button>
    </form>';

    if(isset($_POST["select_tables"])) {
    	echo '</div>';
    	echo '<div class="results">';

		ei_replace_relative_url_with_absolute_in_database();

		echo '</div>';

	} elseif (isset($_POST["relative-urls"]) && !isset($_POST["select_tables"])) {

		echo '<div class="notice notice-error">Please select one or more tables from the list</div>';
		echo '</div>';
	}
}

//diff adopted from https://github.com/paulgb/simplediff
function ei_diff($old, $new){
    $matrix = array();
    $maxlen = 0;
    foreach($old as $oindex => $ovalue){
        $nkeys = array_keys($new, $ovalue);
        foreach($nkeys as $nindex){
            $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
            if($matrix[$oindex][$nindex] > $maxlen){
                $maxlen = $matrix[$oindex][$nindex];
                $omax = $oindex + 1 - $maxlen;
                $nmax = $nindex + 1 - $maxlen;
            }
        }   
    }
    if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
    return array_merge(
        ei_diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
        array_slice($new, $nmax, $maxlen),
        ei_diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}

function ei_htmlDiff($old, $new){
    $ret = '';
    $diff = ei_diff(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));
    foreach($diff as $k){
        if(is_array($k))
            $ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
                (!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
        else $ret .= $k . ' ';
    }
    return $ret;
}



// go though each post and change absolute urls to relative and update in db
function ei_replace_relative_url_with_absolute_in_database() {
	global $wpdb;
	$posts = $wpdb->get_results($wpdb->prepare("SELECT ID, post_content FROM $wpdb->posts WHERE post_status = 'publish'"));
	$siteurl = untrailingslashit( get_bloginfo( 'wpurl' ) ); //retrieved from the “siteurl” record in the wp_options table
	$chr = chr(127);
	$pattern = '(src=\\\\?"|src=\\\\?\'|url\(\\\\?"|url\(\\\\?\'|url\(\\\\?|href=\\\\?"|href=\\\\?\'|background_image=\\\\?"|srcset=\\\\?")';

	$selected_tables = $_POST["select_tables"];
	if (isset($_POST["dryrun"])) {
		echo '<div class="first-notice notice notice-warning"><h2>*This is a dry run</h2></div>';
	} else {
		echo '<div class="first-notice notice notice-success"><h2>The following fields have been updated in the database.</h2></div>';
	}
	$i = 0;
	foreach ($selected_tables as $table) {
    	$all_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table"));

    	foreach ($all_data as $data) {
    		$previous_value = null;
    		$previous_name = null;

    		foreach ( $data as $column_name=>$column_value ) {
	    		$content = $column_value;
	    		$serialized = false;

	    		if (is_serialized_string( $content )) {
	    			$serialized = true;
	    			$content = unserialize($content); //need to unserialize the content before making changes
	    		}

	    		$updated_content = preg_replace( $chr . $pattern . $siteurl . '(/?)' . $chr, '${1}/', $content);

	    		if ($serialized) {
    				$updated_content = serialize($updated_content); // serialize content before comparing to original content 
    			}

	    		if ($content !== $updated_content) { // if any of the content was changed.
	    			$i++;
	    			echo '<h1>' . $table . '</h1>';
	    			echo '<table class="widefat url-replacement">
	    					<thead>
	    						<tr>';

	    			foreach ($previous_name as $prev_name) { //display previous columns so its clear where the field that was changed is in database
	    				echo '<th>' . $prev_name . '</th>';
	    			}
	    			
		    		echo '<th>' . $column_name . '</th>';
		    		echo '</tr></thead>
		    			<tbody>
		    				<tr>';
		    		foreach ($previous_value as $prev_value) {
	    				echo '<td>' . $prev_value . '</td>';
	    			}
	    			$content = htmlentities($content); // change to html entities for admin display
	    			$updated_content = htmlentities($updated_content); 
	    			$diff = ei_htmldiff($content, $updated_content);

		    		echo '<td>' . $diff . '</td>';
		    		echo '</tr></tbody></table>';
		    		if (!isset($_POST["dryrun"])) { // if dryrun checkbox is not check make updates to database
		    			$content = html_entity_decode($content);
	    				$updated_content = html_entity_decode($updated_content);
		    			$wpdb->query( "UPDATE $table SET $column_name = '$updated_content' WHERE $column_name = '$content'" );
		    		}
		    	}

  				$previous_name[] .= $column_name;
  				$previous_value[] .= $column_value;
	    	}
    	}
	}

	if ($i == 0) {
		echo '<div id="number-results-found" class="notice notice-warning"><h2>No absolute URLs found</h2></div>';
	} else {
		if (!isset($_POST["dryrun"])) {
			echo '<div id="number-results-found" class="notice notice-warning"><h2>' . $i . ' fields with absolute URLs found and changed to relative in database</h2></div>';
		} else {
			echo '<div id="number-results-found" class="notice notice-warning"><h2>' . $i . ' fields with absolute URLs found but nothing changed in the database because this is a dry run.</h2></div>';
		
		}
	}
}

// add admin page
function ei_url_replacement_setup_menu(){
        add_menu_page( 'URL Replacement', 'URL Replacement', 'manage_options', 'url-replacement', 'ei_url_replacement_admin_page', 'dashicons-update' );
}
add_action('admin_menu', 'ei_url_replacement_setup_menu');

// admin page styles
function ei_load_custom_wp_admin_style($hook) {
        // Load only on ?page=url-replacement
        if($hook != 'toplevel_page_url-replacement') {
                return;
        }
        wp_enqueue_style( 'custom_wp_admin_css', plugins_url('assets/css/admin-styles.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'ei_load_custom_wp_admin_style' );
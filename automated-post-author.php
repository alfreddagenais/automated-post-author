<?php
/**
 * Plugin Name: Wordpress Automated Post Author
 * Plugin URI: https://github.com/alfreddagenais/automated-post-author
 * Description: Checks if you defined the author, and if not it sets the author. So easy like that...
 * Author: Alfred Dagenais
 * Version: 1.0.0
 * Author URI: https://www.alfreddagenais.com
 * Requires at least: 4.7
 *
 */

/*
MIT License

Copyright (c) 2019 Alfred Dagenais

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

class APAPlugin {

	/**
	 * Install function.
	 */
	public static function install() {
		// do not generate any output here

		if (! wp_next_scheduled ( 'apa_cron_daily_event' )) {
			wp_schedule_event( time(), 'daily', 'apa_cron_daily_event');
		}

		$aIncludePostTypes      = array( 'post' );
		$aIncludePostTypes      = apply_filters( 'apa_post_types_include', $aIncludePostTypes );

		// Select Post and associate with thubmnail
		$aQueryArgs = array(

			'post_type'       	=> $aIncludePostTypes,
			'posts_per_page' 	=> -1,
			'author__in' 		=> array(

				'',
				0,
				NULL

			)

		);

		// The Query
		$oQuery = new WP_Query( $aQueryArgs );

		// The Loop
		if ( $oQuery->have_posts() ) {
		
			while ( $oQuery->have_posts() ) {
				$oQuery->the_post();

				$oPost = get_post();
				$nPostID = get_the_ID();
				$nAuthorID = get_the_author_meta( 'ID' );
				if ( !APAPlugin::hasPostAuthor( $nAuthorID ) ) {
					
					APAPlugin::addPostAuthor( $oPost );

				}

			}

		}

		// Restore original Post Data
		wp_reset_postdata();

	}

	/**
	 * Uninstall function.
	 */
	public static function uninstall() {
		wp_clear_scheduled_hook('apa_cron_daily_event');
	}

	/**
	 * Uninstall function.
	 */
	public static function hasPostAuthor( $nAuthorID ) {

		if( !empty($nAuthorID) && !is_null($nAuthorID) && is_numeric($nAuthorID) && intval($nAuthorID) > 0 ){
			return TRUE;
		}else{
			return FALSE;
		}

	}

	/**
	 * Random user query
	 *
	 * @param query $query WP Query.
	 */
	public static function randomUserQuery( &$query ){
		$query->query_orderby = "ORDER BY RAND()";
	}

	/**
	 * Main function.
	 *
	 * @param object $post Post Object.
	 */
	public static function addPostAuthor( $oPost ) {
		
		$nPostID  				= $oPost->ID;
		$nAuthorID  			= $oPost->post_author;
		$sPostType         		= get_post_type( $nPostID );
		$aExcludePostTypes      = array( '' );
		$aExcludePostTypes      = apply_filters( 'apa_post_types_exclude', $aExcludePostTypes );

		// Do nothing if the post has already a author set.
		if ( APAPlugin::hasPostAuthor( $nAuthorID ) ) {
			return;
		}

		// Do the job if the post is not from an excluded type.
		if ( ! in_array( $sPostType, $aExcludePostTypes, TRUE ) ) {

			// Create a function to override the ORDER BY clause
			add_action( 'pre_user_query', array( 'APAPlugin', 'randomUserQuery' ) );

			$aUsers = get_users( array(

				'role'    	=> 'author',
				'fields'  	=> 'ID',
				'orderby' 	=> 'rand',
				'number'  	=> 1,

			) );

			foreach ( $aUsers as $nUserID ) {
					
				wp_update_post( array(

					'ID' 			=> $nPostID,
					'post_author' 	=> $nUserID,

				) );

				break;
			}

			// Remove the hook
			remove_action( 'pre_user_query', array( 'APAPlugin', 'randomUserQuery' ) );

		}

	}

	/**
	 * Cron runned daily
	 */
	public static function cronDaily() {

		$aIncludePostTypes      = array( 'post' );
		$aIncludePostTypes      = apply_filters( 'apif_post_types_include', $aIncludePostTypes );

		// Select Post and associate with thubmnail
		$aQueryArgs = array(

			'post_type'       	=> $aIncludePostTypes,
			'posts_per_page' 	=> -1,
			'author__in' 		=> array(

				'',
				0,
				NULL

			)

		);

		$aPosts = get_posts( $aQueryArgs );
		if( is_array($aPosts) && count($aPosts) > 0 ){

			foreach ( $aPosts as $nP => $oPost ) {
				
				APAPlugin::addPostAuthor( $oPost );

			}

		}

	}


}

// Add function when install plugin
register_activation_hook( __FILE__ , array( 'APAPlugin', 'install' ) );
register_deactivation_hook( __FILE__ , array( 'APAPlugin', 'uninstall' ) );

// Set featured image before post is displayed on the site front-end (for old posts published before enabling this plugin).
//add_action( 'the_post', array( 'APAPlugin', 'addPostAuthor' ) );

// Hooks added to set the thumbnail when publishing too.
add_action( 'new_to_publish', array( 'APAPlugin', 'addPostAuthor' ) );
add_action( 'draft_to_publish', array( 'APAPlugin', 'addPostAuthor' ) );
add_action( 'pending_to_publish', array( 'APAPlugin', 'addPostAuthor' ) );
add_action( 'future_to_publish', array( 'APAPlugin', 'addPostAuthor' ) );

// Action for Crons
add_action( 'apif_cron_daily_event', array( 'APAPlugin', 'cronDaily' ) );

if( isset($_REQUEST['apa_cron_daily_event']) ){
	APAPlugin::cronDaily();
	die();
}

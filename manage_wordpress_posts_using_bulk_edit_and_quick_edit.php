<?php

/**
 * Plugin Name: Manage WordPress Posts Using Bulk Edit and Quick Edit
 * Description: This is the code for a tutorial WP Dreamer wrote about managing WordPress posts using bulk and quick edit.
 * Author: WP Dreamer
 * Author URI: http://wpdreamer.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit/
 */
 
/**
 * I decided to convert the tutorial to a plugin format
 * so I could easily monitor it on my development environment
 * and store it on GitHub.
 *
 * With that said, you could easily take this code and paste it
 * into your theme's functions.php file. There is, however,
 * an included javascript file so be sure to check the
 * manage_wp_posts_be_qe_enqueue_admin_scripts()
 * function to confirm you're enqueueing the right javascript file.
 *
 * Also, after a few requests for custom field examples other than
 * text boxes, I updated the tutorial to include a select dropdown
 * and a radio button.
 *
 * Custom Fields:
 * 'Release Date - input text
 * 'Coming Soon' - input radio
 * 'Film Rating' - select dropdown
 *
 * If you find any issues with the tutorial, or code, please let me know. Thanks!
 */

/**
 * Since Bulk Edit and Quick Edit hooks are triggered by custom columns,
 * you must first add custom columns for the fields you wish to add, which are setup by
 * 'filtering' the column information.
 *
 * There are 3 different column filters: 'manage_pages_columns' for pages,
 * 'manage_posts_columns' which covers ALL post types (including custom post types),
 * and 'manage_{$post_type_name}_posts_columns' which only covers, you guessed it,
 * the columns for the defined $post_type_name.
 *
 * The 'manage_pages_columns' and 'manage_{$post_type_name}_posts_columns' filters only
 * pass $columns (an array), which is the column info, as an argument, but 'manage_posts_columns'
 * passes $columns and $post_type (a string).
 *
 * Note: Don't forget that it's a WordPress filter so you HAVE to return the first argument that's
 * passed to the function, in this case $columns. And for filters that pass more than 1 argument,
 * you have to specify the number of accepted arguments in your add_filter() declaration,
 * following the priority argument.
 *
 */
add_filter( 'manage_posts_columns', 'manage_wp_posts_be_qe_manage_posts_columns', 10, 2 );
function manage_wp_posts_be_qe_manage_posts_columns( $columns, $post_type ) {

	/**
	 * The first example adds our new columns at the end.
	 * Notice that we're specifying a post type because our function covers ALL post types.
	 *
	 * Uncomment this code if you want to add your column at the end
	 */
	/*if ( $post_type == 'movies' ) {
		$columns[ 'release_date' ] = 'Release Date';
		$columns[ 'coming_soon' ] = 'Coming Soon';
		$columns[ 'film_rating' ] = 'Film Rating';
	}
		
	return $columns;*/
	
	/**
	 * The second example adds our new column after the “Title” column.
	 * Notice that we're specifying a post type because our function covers ALL post types.
	 */
	switch ( $post_type ) {
	
		case 'movies':
		
			// building a new array of column data
			$new_columns = array();
			
			foreach( $columns as $key => $value ) {
			
				// default-ly add every original column
				$new_columns[ $key ] = $value;
				
				/**
				 * If currently adding the title column,
				 * follow immediately with our custom columns.
				 */
				if ( $key == 'title' ) {
					$new_columns[ 'release_date_column' ] = 'Release Date';
					$new_columns[ 'coming_soon_column' ] = 'Coming Soon';
					$new_columns[ 'film_rating_column' ] = 'Film Rating';
				}
					
			}
			
			return $new_columns;
			
	}
	
	return $columns;
	
}

/**
 * The following filter allows you to make your column(s) sortable.
 *
 * The 'edit-movies' section of the filter name is the custom part
 * of the filter name, which tells WordPress you want this to run
 * on the main 'movies' custom post type edit screen. So, e.g., if
 * your custom post type's name was 'books', then the filter name
 * would be 'manage_edit-books_sortable_columns'.
 *
 * Don't forget that filters must ALWAYS return a value.
 */
add_filter( 'manage_edit-movies_sortable_columns', 'manage_wp_posts_be_qe_manage_sortable_columns' );
function manage_wp_posts_be_qe_manage_sortable_columns( $sortable_columns ) {

	/**
	 * In order to make a column sortable, add the
	 * column data to the $sortable_columns array.
	 *
	 * I want to make my 'Release Date' column
	 * sortable so the array indexes (the 'release_date_column'
	 * value between the []) need to match from
	 * where we added the column in the
	 * manage_wp_posts_be_qe_manage_posts_columns()
	 * function.
	 *
	 * The array value (after the =) should be set to
	 * identify the data that is going to be sorted,
	 * i.e. what will be placed in the URL when it's sorted.
	 * Since my release date is a custom field, I just
	 * use the custom field name, 'release_date'.
	 *
	 * When the column is clicked, the URL will look like this:
	 * http://mywebsite.com/wp-admin/edit.php?post_type=movies&orderby=release_date&order=asc
	 */
	$sortable_columns[ 'release_date_column' ] = 'release_date';
	
	// Let's also make the film rating column sortable
	$sortable_columns[ 'film_rating_column' ] = 'film_rating';

	return $sortable_columns;
	
}

/**
 * Now that we have a column, we need to fill our column with data.
 * The filters to populate your custom column are pretty similar to the ones
 * that added your column: 'manage_pages_custom_column', 'manage_posts_custom_column',
 * and 'manage_{$post_type_name}_posts_custom_column'. All three pass the same
 * 2 arguments: $column_name (a string) and the $post_id (an integer).
 *
 * Our custom column data is post meta so it will be a pretty simple case of retrieving
 * the post meta with the meta key 'release_date'.
 *
 * Note that we are wrapping our post meta in a div with an id of “release_date-” plus the post id.
 * This will come in handy when we are populating our “Quick Edit” row.
 */
add_action( 'manage_posts_custom_column', 'manage_wp_posts_be_qe_manage_posts_custom_column', 10, 2 );
function manage_wp_posts_be_qe_manage_posts_custom_column( $column_name, $post_id ) {

	switch( $column_name ) {
	
		case 'release_date_column':
		
			echo '<div id="release_date-' . $post_id . '">' . get_post_meta( $post_id, 'release_date', true ) . '</div>';
			break;
			
		case 'coming_soon_column':
		
			echo '<div id="coming_soon-' . $post_id . '">' . get_post_meta( $post_id, 'coming_soon', true ) . '</div>';
			break;
			
		case 'film_rating_column':
		
			echo '<div id="film_rating-' . $post_id . '">' . get_post_meta( $post_id, 'film_rating', true ) . '</div>';
			break;
			
	}
	
}

/**
 * Just because we've made the column sortable doesn't
 * mean the posts will sort by our column data. That's where
 * this next 2 filters come into play.
 * 
 * If your sort data is simple, i.e. alphabetically or numerically,
 * then 'pre_get_posts' is the filter to use. This filter lets you
 * change up the query before it's run.
 *
 * If your orderby data is more complicated, like our release date
 * which is a date string stored in a custom field, then check out
 * the 'posts_clauses' filter example used below.
 *
 * In the example below, when the main query is trying to order by
 * the 'film_rating', it's a simple alphabetical sorting by a custom
 * field so we're telling the query to set our 'meta_key' which is
 * 'film_rating' and that we want to order by the query by the
 * custom field's meta_value, e.g. PG, PG-13, R, etc.
 *
 * Check out http://codex.wordpress.org/Class_Reference/WP_Query
 * for more info on WP Query parameters.
 */
add_action( 'pre_get_posts', 'manage_wp_posts_be_qe_pre_get_posts', 1 );
function manage_wp_posts_be_qe_pre_get_posts( $query ) {

	/**
	 * We only want our code to run in the main WP query
	 * AND if an orderby query variable is designated.
	 */
	if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
	
		switch( $orderby ) {
		
			// If we're ordering by 'film_rating'
			case 'film_rating':
			
				// set our query's meta_key, which is used for custom fields
				$query->set( 'meta_key', 'film_rating' );
				
				/**
				 * Tell the query to order by our custom field/meta_key's
				 * value, in this case: PG, PG-13, R, etc.
				 *
				 * If your meta value are numbers, change
				 * 'meta_value' to 'meta_value_num'.
				 */
				$query->set( 'orderby', 'meta_value' );
				
				break;
				
		}
	
	}
	
}

/**
 * Just because we've made the column sortable doesn't
 * mean the posts will sort by our column data. That's where
 * the filter above, 'pre_get_posts', and the filter below,
 * 'posts_clauses', come into play.
 *
 * If your sort data is simple, i.e. alphabetically or numerically,
 * then check out the 'pre_get_posts' filter used above.
 *
 * If your orderby data is more complicated, like combining
 * several values or a date string stored in a custom field,
 * then the 'posts_clauses' filter used below is for you.
 * The 'posts_clauses' filter allows you to manually tweak
 * the query clauses in order to sort the posts by your
 * custom column data.
 *
 * The reason more complicated sorts will not with the
 * "out of the box" WP Query is because the WP Query orderby
 * parameter will only order alphabetically and numerically.
 *
 * Usually I would recommend simply using the 'pre_get_posts'
 * and altering the WP Query itself but because our custom
 * field is a date, we have to manually set the query to
 * order our posts by a date.
 */
add_filter( 'posts_clauses', 'manage_wp_posts_be_qe_posts_clauses', 1, 2 );
function manage_wp_posts_be_qe_posts_clauses( $pieces, $query ) {
	global $wpdb;
	
	/**
	 * We only want our code to run in the main WP query
	 * AND if an orderby query variable is designated.
	 */
	if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
	
		// Get the order query variable - ASC or DESC
		$order = strtoupper( $query->get( 'order' ) );
		
		// Make sure the order setting qualifies. If not, set default as ASC
		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) )
			$order = 'ASC';
	
		switch( $orderby ) {
		
			// If we're ordering by release_date
			case 'release_date':
			
				/**
				 * We have to join the postmeta table to include
				 * our release date in the query.
				 */
				$pieces[ 'join' ] .= " LEFT JOIN $wpdb->postmeta wp_rd ON wp_rd.post_id = {$wpdb->posts}.ID AND wp_rd.meta_key = 'release_date'";
				
				// Then tell the query to order by our date
				$pieces[ 'orderby' ] = "STR_TO_DATE( wp_rd.meta_value,'%m/%d/%Y' ) $order, " . $pieces[ 'orderby' ];
				
				break;
		
		}
	
	}

	return $pieces;

}

/**
 * Now that you have your custom column, it's bulk/quick edit showtime!
 * The filters are 'bulk_edit_custom_box' and 'quick_edit_custom_box'. Both filters
 * pass the same 2 arguments: the $column_name (a string) and the $post_type (a string).
 *
 * Your data's form fields will obviously vary so customize at will. For this example,
 * we're using an input. Also take note of the css classes on the <fieldset> and <div>.
 * There are a few other options like 'inline-edit-col-left' and 'inline-edit-col-center'
 * for the fieldset and 'inline-edit-col' for the div. I recommend studying the WordPress
 * bulk and quick edit HTML to see the best way to layout your custom fields.
 */
add_action( 'bulk_edit_custom_box', 'manage_wp_posts_be_qe_bulk_quick_edit_custom_box', 10, 2 );
add_action( 'quick_edit_custom_box', 'manage_wp_posts_be_qe_bulk_quick_edit_custom_box', 10, 2 );
function manage_wp_posts_be_qe_bulk_quick_edit_custom_box( $column_name, $post_type ) {

	switch ( $post_type ) {
	
		case 'movies':
		
			switch( $column_name ) {
			
				case 'release_date':
				
					?><fieldset class="inline-edit-col-left">
						<div class="inline-edit-col">
							<label>
								<span class="title">Release Date</span>
								<span class="input-text-wrap">
									<input type="text" value="" name="release_date">
								</span>
							</label>
						</div>
					</fieldset><?php
					break;
					
				case 'coming_soon':
				
					?><fieldset class="inline-edit-col-left">
						<div class="inline-edit-col">
							<label>
								<span class="title">Coming Soon</span>
								<span class="input-text-wrap">
									<label style="display:inline;">
										<input type="radio" name="coming_soon" value="Yes" /> Yes
									</label>&nbsp;&nbsp;
									<label style="display:inline;">
										<input type="radio" name="coming_soon" value="No" /> No
									</label>
								</span>
							</label>
						</div>
					</fieldset><?php
					break;
					
				case 'film_rating':
				
					?><fieldset class="inline-edit-col-left">
						<div class="inline-edit-col">
							<label>
								<span class="title">Film rating</span>
								<span class="input-text-wrap">
									<select name="film_rating">
										<option value="">Rating</option>
										<option value="G">G</option>
										<option value="PG">PG</option>
										<option value="PG-13">PG-13</option>
										<option value="R">R</option>
										<option value="NC-17">NC-17</option>
										<option value="X">X</option>
										<option value="GP">GP</option>
										<option value="M">M</option>
										<option value="M/PG">M/PG</option>
									</select>
								</span>
							</label>
						</div>
					</fieldset><?php
					break;
					
			}
			
			break;
			
	}
	
}

/**
 * When you click 'Quick Edit', you may have noticed that your form fields are not populated.
 * WordPress adds one 'Quick Edit' row which moves around for each post so the information cannot
 * be pre-populated. It has to be populated with JavaScript on a per-post 'click Quick Edit' basis.
 *
 * WordPress has an inline edit post function that populates all of their default quick edit fields
 * so we want to hook into this function, in a sense, to make sure our JavaScript code is run when
 * needed. We will 'copy' the WP function, 'overwrite' the WP function so we're hooked in, 'call'
 * the original WP function (via our copy) so WordPress is not left hanging, and then run our code.
 *
 * Remember where we wrapped our column data in a <div> in Step 2? This is where it comes in handy,
 * allowing our Javascript to retrieve the data by the <div>'s element ID to populate our form field.
 * There are other methods to retrieve your data that involve AJAX but this route is the simplest.
 *
 * Don't forget to enqueue your script and make sure it's dependent on WordPress's 'inline-edit-post' file.
 * Since we'll be using the jQuery library, we need to make sure 'jquery' is loaded as well.
 *
 * I have provided several scenarios for where you've placed this code. Simply uncomment the scenario
 * you're using. For all scenarios, make sure your javascript file is in the same folder as your code.
 */
add_action( 'admin_print_scripts-edit.php', 'manage_wp_posts_be_qe_enqueue_admin_scripts' );
function manage_wp_posts_be_qe_enqueue_admin_scripts() {

	// if code is in theme functions.php file
	//wp_enqueue_script( 'manage-wp-posts-using-bulk-quick-edit', trailingslashit( get_bloginfo( 'stylesheet_directory' ) ) . 'bulk_quick_edit.js', array( 'jquery', 'inline-edit-post' ), '', true );
	
	// if using code as plugin
	wp_enqueue_script( 'manage-wp-posts-using-bulk-quick-edit', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'bulk_quick_edit.js', array( 'jquery', 'inline-edit-post' ), '', true );
	
}

/**
 * Saving your 'Quick Edit' data is exactly like saving custom data
 * when editing a post, using the 'save_post' hook. With that said,
 * you may have already set this up. If you're not sure, and your
 * 'Quick Edit' data is not saving, odds are you need to hook into
 * the 'save_post' action.
 *
 * The 'save_post' action passes 2 arguments: the $post_id (an integer)
 * and the $post information (an object).
 */
add_action( 'save_post', 'manage_wp_posts_be_qe_save_post', 10, 2 );
function manage_wp_posts_be_qe_save_post( $post_id, $post ) {

	// pointless if $_POST is empty (this happens on bulk edit)
	if ( empty( $_POST ) )
		return $post_id;
		
	// verify quick edit nonce
	if ( isset( $_POST[ '_inline_edit' ] ) && ! wp_verify_nonce( $_POST[ '_inline_edit' ], 'inlineeditnonce' ) )
		return $post_id;
			
	// don't save for autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;
		
	// dont save for revisions
	if ( isset( $post->post_type ) && $post->post_type == 'revision' )
		return $post_id;
		
	switch( $post->post_type ) {
	
		case 'movies':
		
			/**
			 * Because this action is run in several places, checking for the array key
			 * keeps WordPress from editing data that wasn't in the form, i.e. if you had
			 * this post meta on your "Quick Edit" but didn't have it on the "Edit Post" screen.
			 */
			$custom_fields = array( 'release_date', 'coming_soon', 'film_rating' );
			
			foreach( $custom_fields as $field ) {
			
				if ( array_key_exists( $field, $_POST ) )
					update_post_meta( $post_id, $field, $_POST[ $field ] );
					
			}
				
			break;
			
	}
	
}

/**
 * Saving the 'Bulk Edit' data is a little trickier because we have
 * to get JavaScript involved. WordPress saves their bulk edit data
 * via AJAX so, guess what, so do we.
 *
 * Your javascript will run an AJAX function to save your data.
 * This is the WordPress AJAX function that will handle and save your data.
 */
add_action( 'wp_ajax_manage_wp_posts_using_bulk_quick_save_bulk_edit', 'manage_wp_posts_using_bulk_quick_save_bulk_edit' );
function manage_wp_posts_using_bulk_quick_save_bulk_edit() {

	// we need the post IDs
	$post_ids = ( isset( $_POST[ 'post_ids' ] ) && !empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : NULL;
		
	// if we have post IDs
	if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
	
		// get the custom fields
		$custom_fields = array( 'release_date', 'coming_soon', 'film_rating' );
		
		foreach( $custom_fields as $field ) {
			
			// if it has a value, doesn't update if empty on bulk
			if ( isset( $_POST[ $field ] ) && !empty( $_POST[ $field ] ) ) {
			
				// update for each post ID
				foreach( $post_ids as $post_id ) {
					update_post_meta( $post_id, $field, $_POST[ $field ] );
				}
				
			}
			
		}
		
	}
	
}

?>
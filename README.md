Manage WordPress Posts using Bulk Edit and Quick Edit
=====================================================

I wrote a tutorial for how to "Manage WordPress Posts Using Bulk Edit and Quick Edit"
and decided it was best to convert my tutorial to plugin format and post online so
I can better manage and keep up to date.

You can view the tutorial at: <a href="http://wpdreamer.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit"><strong>Manage WordPress Posts using Bulk Edit and Quick Edit</strong> - WPDreamer.com</a>

If you're looking for the "sortable columns" code related to my <a href="http://wpdreamer.com/2014/04/how-to-make-your-wordpress-admin-columns-sortable/"><strong>How To Make Your WordPress Admin Columns Sortable</strong> - WPDreamer.com</a> tutorial, the code you're looking for is in the <a href="https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/manage_wordpress_posts_using_bulk_edit_and_quick_edit.php">manage_wordpress_posts_using_bulk_edit_and_quick_edit.php</a> file.

You can easily take this code and paste it into your theme's <tt>functions.php</tt> file.
There is, however, an included javascript file, so be sure to check the
<tt>manage_wp_posts_using_bulk_quick_edit_enqueue_admin_scripts()</tt> function to confirm you're
enqueueing the right javascript file.

Also, after a few requests for custom field examples other than textboxes, I updated the
tutorial to include a select dropdown and a radio button.

<strong>Custom Fields:</strong>

 * Release Date - input text
 * Coming Soon' - input radio
 * 'Film Rating' - select dropdown

If you find any issues with the tutorial, or code, please let me know. Thanks!

<?php
/**
 * Balzac utility functions
 *
 * @package Balzac
 * @subpackage Functions
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Generate a custom excerpt
 *
 * @param int $length	Length of the wanted exerpt (in words)
 *
 * @since 1.0
 * @return string
 */
if (!function_exists('tdf_excerpt')){
	function tdf_excerpt($length){
		global $post;

		// No excerpt needed
		if($length==0)
			return '';

		// Do we have an excerpt ? (excerpt field in the post editor)
		if(has_excerpt())
			return apply_filters('the_excerpt', wpautop(strip_shortcodes(strip_tags(get_the_excerpt()))));

		// Do we have a read more tag (<!--more-->) in the post content ?
		if(strpos( $post->post_content, '<!--more-->' )){
			$content_arr = get_extended($post->post_content);
			return apply_filters('the_excerpt', wpautop(strip_shortcodes(strip_tags($content_arr['main']))));
		}

		// Get the post content without shortcodes or HTML tags
		$content = strip_shortcodes(strip_tags(get_the_content()));

		// Create a custom excerpt based on the post content
		return apply_filters('the_excerpt', wpautop(wp_trim_words( $content , $length )));
	}
}

/**
 * Check if we are on a paginated post
 *
 * @link https://gist.github.com/tommcfarlin/f2310bfad60b60ae00bf#file-is-paginated-post-php
 *
 * @since 1.0
 * @return void
 */
if (!function_exists('balzac_is_paginated_post')){
	function balzac_is_paginated_post() {
		global $multipage;
		return 0 !== $multipage;
	}
}

/**
 * Display a custom message if the primary menu isn't setup
 *
 * @since 1.0
 * @return void
 */
if (!function_exists('tdf_nomenu')){
	function tdf_nomenu(){
		echo '<ul class="top-level-menu"><li><a href="'.admin_url('nav-menus.php').'">'.__('Set up the main menu', 'balzac').'</a></li></ul>';
	}
}

/**
 * Get post thumbnail url
 *
 * @since 1.0
 * @return void
 */
if (!function_exists('balzac_get_post_thumbnail_url')){
	function balzac_get_post_thumbnail_url(){

		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' );
		return $thumbnail[0];

	}
}

/**
 * Display a numeric page navigation
 *
 * @param bool $extremes		Display or not previous & next links
 * @param string $separator		Character to insert between each page
 * @param string $before		Content to display before pagination
 * @param string $after			Content to display after pagination
 *
 * @link http://www.wpbeginner.com/wp-themes/how-to-add-numeric-pagination-in-your-wordpress-theme/
 *
 * @since 1.0
 * @return void
 */
if (!function_exists('balzac_posts_nav')){

	function balzac_posts_nav($extremes=true, $separator='|', $before = '', $after){
		if (is_singular()) return;

		global $wp_query;
		$output = '';

		// Stop execution if there's only 1 page
		if($wp_query->max_num_pages <= 1) return;

		$paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
		$max = intval($wp_query->max_num_pages);

		// Add current page to the array
		if ($paged >= 1) $links[] = $paged;

		// Add the pages around the current page to the array
		if ($paged >= 3){
			$links[] = $paged - 1;
			$links[] = $paged - 2;
		}

		if (($paged + 2 ) <= $max){
			$links[] = $paged + 2;
			$links[] = $paged + 1;
		}

		$current = apply_filters('balzac_post_nav_current', '<span class="current">%s</span>');
		$linkTemplate = apply_filters('balzac_post_nav_link', '<a href="%s">%s</a>');

		$output .= $before;

		// Previous Post Link
		if ($extremes && get_previous_posts_link())
			$output.= get_previous_posts_link('<i class="typcn typcn-chevron-left"></i>');

		// Link to first page, plus ellipses if necessary */
		if (!in_array(1, $links)){
			if ($paged == 1)
				$output .= sprintf($current, '1');
			else
				$output .= sprintf($linkTemplate, esc_url(get_pagenum_link(1)), '1');

			$output.= $separator;
			if (!in_array(2, $links)) $output .= '<i class="nothere"></i>'.$separator;
		}

		// Link to current page, plus 2 pages in either direction if necessary
		sort($links);
		foreach ((array) $links as $link){
			if ($paged == $link)
				$output .= sprintf($current, $link);
			else
				$output .= sprintf($linkTemplate, esc_url(get_pagenum_link($link)), $link);

			if ($link < $max) $output .= $separator;
		}

		// Link to last page, plus ellipses if necessary
		if (!in_array($max, $links)){
			if (!in_array($max-1, $links)) $output .= '<i class="nothere">…</i>'.$separator;

			if ($paged == $max)
				$output .= sprintf($current, $link);
			else
				$output .= sprintf($linkTemplate, esc_url(get_pagenum_link($max)), $max);
		}

		// Next Post Link
		if ($extremes && get_next_posts_link())
			$output.= get_next_posts_link('<i class="typcn typcn-chevron-right"></i>');

		$output .= $after;

		echo apply_filters('balzac_post_nav', $output);
	}
}

/**
 * Display navigation to next/previous post when applicable.
 * Derived from Twenty Fourteen Theme
 *
 * @since 1.0
 * @return void
 */

if (!function_exists('balzac_single_post_nav')){
	function balzac_single_post_nav() {

		// Filter to handle displaying of the post navigation
		if(!apply_filters('balzac_single_post_nav',true)){
			return;
		}

		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}

		?>
		<nav class="entry-navigation" role="navigation">

			<div class="wrapper">

				<?php
				if ( is_attachment() ) :
					previous_post_link( '<span class="meta-nav-prev">%link</span>', __( 'Published In', 'balzac' ) . '%title' );
				else :
					previous_post_link( '<span class="meta-nav-prev">%link</span>', __( 'Previous Post', 'balzac' ));
					next_post_link( '<span class="meta-nav-next">%link</span>', __( 'Next Post', 'balzac' ));
				endif;
				?>
			</div><!-- .wrapper -->

		</nav><!-- .entry-navigation -->
		<?php
	}
}

/**
 * Display a numeric page navigation
 *
 * @param object $comment 	Comment to display.
 * @param array  $args    	An array of arguments.
 * @param int    $depth   	Depth of comment.
 *
 * @link http://themeshaper.com/2012/11/04/the-wordpress-theme-comments-template/
 *
 * @since 1.0
 * @return void
 */
if (!function_exists('balzac_comment')){
	function balzac_comment($comment, $args, $depth){
		$GLOBALS['comment'] = $comment;
		switch ($comment->comment_type) :
			case 'pingback' :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p>
				<?php echo apply_filters('balzac_pingback', __('Pingback:', 'balzac')); ?>
				<?php comment_author_link(); ?>
			</p>
		<?php
			break;
		default :
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment" itemprop="comment" itemscope="itemscope" itemtype="http://schema.org/UserComments">

				<?php if ($comment->comment_approved == '0') : ?>
					<em class="comment-waiting"><?php echo apply_filters('balzac_comment_waiting_moderation', __('Your comment is waiting for moderation.', 'balzac')); ?></em>
				<?php endif; ?>

				<aside class="comment-aside">
					<?php echo get_avatar($comment, 80); ?>
				</aside>

				<div class="comment-main">
					<header class="comment-header">
						<span class="comment-author vcard" itemprop="creator" itemscope="itemscope" itemtype="http://schema.org/Person">
							<?php echo apply_filters('balzac_comment_author', sprintf(__('%s', 'balzac'), sprintf(__('<cite class="fn">%s</cite>', 'balzac'), get_comment_author_link()))); ?>
						</span>
						<time class="comment-date" datetime="<?php the_time('c'); ?>" itemprop="commentTime" >
							<?php echo apply_filters('balzac_comment_date', sprintf(__('Published on %s at %s', 'balzac'),get_comment_date(),get_comment_time('H:i'))); ?>
						</time>
					</header>

					<div class="comment-content" itemprop="commentText">
						<?php comment_text(); ?>
					</div>

					<footer class="comment-footer">

						<div class="reply">
							<?php
							comment_reply_link(array_merge($args,
								array(	'depth'=>$depth,
										'max_depth'=>$args['max_depth'],
										'reply_text'=>apply_filters('balzac_comment_reply', __('Reply', 'balzac')))));
							?>
						</div>
					</footer>
				</div>
			</article>
		<?php
			break;
		endswitch;
	}
}

/**
 * Comment form arguments
 *
 * @since 1.0
 * @return array
 */
if (!function_exists('balzac_comment_form_args')){
	function balzac_comment_form_args(){

		$commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );

		$comment_args = array(
			'comment_notes_before'=>'',
			'comment_notes_after'=>'',
			'title_reply'=>'',
			'title_reply_to'=>apply_filters('balzac_comment_reply_to', __('Reply to %s', 'balzac')),
			'label_submit'=>apply_filters('balzac_comment_send', __('Post comment', 'balzac')),
			'fields' => apply_filters( 'balzac_comment_form_default_fields', array(
			    'author' =>
			      '<p class="comment-form-author">' .
			      '<label for="author">' . __( 'Name', 'balzac' ) . '</label> ' .
			      '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
			      '" size="30"' . $aria_req .
			      ' placeholder="' . __('Name','balzac') . ( $req ? ' (' . __( 'required', 'balzac' ) . ')' : '' ) .'"/></p>',

			    'email' =>
			      '<p class="comment-form-email">' .
			      '<label for="email">' . __( 'Email', 'balzac' ) . '</label> ' .
			      '<input id="email" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) .
			      '" size="30"' . $aria_req .
			      ' placeholder="' . __( 'Email', 'balzac' ) . ( $req ? ' (' . __( 'required', 'balzac' ) . ')' : '' ) .'"/></p>',

			    'url' =>
			      '<p class="comment-form-url"><label for="url">' . __( 'Website', 'balzac' ) . '</label>' .
			      '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
			      '" size="30"' .
			      ' placeholder="' . __( 'Website', 'balzac' ) . '"/></p>'
			   )
			),
			'comment_field' =>  apply_filters( 'balzac_comment_form_default_comment_field','<p class="comment-form-comment"><label for="comment">' . __( 'Comment', 'balzac' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="' . __('Comment','balzac' ) .'"></textarea></p>')
		);

		return $comment_args;
	}
}

/**
 * Display the archives in a beautiful way
 *
 * @param string $style 	Style for displaying the archives (initial | block | numeric)
 * @param string $before   	Content to display before archives.
 * @param string $after   	Content to display after archives.
 *
 * @link https://wordpress.org/plugins/compact-archives/
 *
 * @since 1.0
 * @return string
 */
if (!function_exists('balzac_archives')){
	function balzac_archives( $style='block', $before='<li>', $after='</li>' ) {
		global $wpdb;

		// Set localization language
		setlocale(LC_ALL,get_locale());

		$results = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month FROM " . $wpdb->posts . " WHERE post_type='post' AND post_status='publish' AND post_password='' ORDER BY year DESC, month DESC");

		if (!$results) {
			return $before.__('There is no archives to display.', 'balzac').$after;
		}
		$dates = array();
		foreach ($results as $result) {
			$dates[$result->year][$result->month] = 1;
		}
		unset($results);
		$result = '';
		foreach ($dates as $year => $months){
			$result .= $before.'<strong><a href="'.get_year_link($year).'">'.$year.'</a>: </strong> ';
			for ( $month = 1; $month <= 12; $month += 1) {
				$month_has_posts = (isset($months[$month]));
				$dummydate = strtotime("$month/01/2001");
				// get the month name; strftime() localizes
				$month_name = strftime("%B", $dummydate);
				switch ($style) {
				case 'initial':
					$month_abbrev = $month_name[0]; // the inital of the month
					break;
				case 'block':
					$month_abbrev = strftime("%b", $dummydate); // get the short month name; strftime() localizes
					break;
				case 'numeric':
					$month_abbrev = strftime("%m", $dummydate); // get the month number, e.g., '04'
					break;
				default:
					$month_abbrev = $month_name[0]; // the inital of the month
				}
				if ($month_has_posts) {
					$result .= '<a href="'.get_month_link($year, $month).'" title="'.utf8_encode($month_name).' '.$year.'">'.utf8_encode($month_abbrev).'</a> ';
				} else {
					$result .= '<span class="emptymonth">'.utf8_encode($month_abbrev).'</span> ';
				}
			}
			$result .= $after."\n";
		}
		return $result;
	}
}

/**
 * Show a popup in order to make people drop IE mouahaha
 *
 * @since 1.0
 * @return void
 */
if(!function_exists('tdf_chromeframe_notice')){
	function tdf_chromeframe_notice(){ ?>
		<!--[if lt IE 8]><p class='chromeframe'><?php _e('Your browser is <em>too old !','toutatis'); ?></em> <a href="http://browsehappy.com/"><?php _e('Update your browser','toutatis'); ?></a> <?php _e('or','toutatis'); ?> <a href="http://www.google.com/chromeframe/?redirect=true"><?php _e('Install Google Chrome Frame','toutatis'); ?></a> <?php _e('to display this website correctly','toutatis'); ?>.</p><![endif]-->
	<?php
	}
}
add_action('balzac_body_top','tdf_chromeframe_notice');

/**
 * Hide h1 tag from TinyMCE editor in order to preserve SEO
 *
 * @since 1.0
 * @return void
 */
if(!function_exists('tdf_hide_tinymce_h1_tag')){
	function tdf_hide_tinymce_h1_tag($arr){
	    $arr['block_formats'] = 'Paragraph=p;Pre=pre;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6';
	    return $arr;
	}
}
add_filter('tiny_mce_before_init', 'tdf_hide_tinymce_h1_tag');

/**
 * Add content for the bbPress Addon in the theme options' Addons tab
 *
 * @param object $form	Cocorico form object
 *
 * @since 1.0
 * @return void
 */
if(!function_exists('balzac_addons_bbpress')){
	function balzac_addons_bbpress($form){

		$form->startWrapper('tr');

			$form->startWrapper('th');

				$form->component('raw', __('Balzac bbPress Addon', 'balzac'));

			$form->endWrapper('th');

			$form->startWrapper('td');

				// If Balzac bbPress addon isn't available, tell about it
				if(!function_exists('balzac_bbpress_styles')):

					$form->component('raw', __('Balzac bbPress Addon bring custom CSS styling to Balzac to get a perfect bbPress integration.', 'balzac') . '<br><br>');

					$form->component('link',
									 'https://www.themesdefrance.fr/module-bbpress-balzac/?utm_source=Balzac&utm_medium=bouton&utm_content=Balzac_bbPress&utm_campaign=BalzacAdmin',
									 __('Get Balzac bbPress Addon', 'balzac'),
									 array(
										 'class'=>array('button', 'button-primary'),
										 'target'=>'_blank'
									 ));
				// Or ask for feedback
				else:
					$form->component('description', __('Balzac bbPress Addon is installed. Thanks for using it !', 'balzac'));

					$form->component('description', __('If you have some time, help us to improve it by giving some feedback.', 'balzac') . '<br><br>');

					$form->component('link',
									 'https://www.themesdefrance.fr/temoignage/?produit=Balzac%20bbPress&utm_source=Balzac&utm_medium=bouton&utm_content=Balzac_bbPress&utm_campaign=BalzacAdmin',
									 __('Give feedback on Balzac bbPress Addon', 'balzac'),
									 array(
										 'class'=>array('button'),
										 'target'=>'_blank'
									 ));

				endif;

			$form->endWrapper('td');

		$form->endWrapper('tr');

	}
}
add_action('balzac_addons_tab', 'balzac_addons_bbpress', 10, 1);
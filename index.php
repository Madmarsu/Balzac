<?php
/**
 * The main template file
 *
 * @package Balzac
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 1.0
 */
?>

<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php $sidebar = apply_filters('balzac_show_sidebar', get_option('balzac_show_sidebar', true)); ?>

<?php get_header(); ?>

<?php get_template_part('header', 'bar'); ?>

<?php do_action('balzac_before_main'); ?>	

<section class="content">

	<div class="wrapper">
		
		<?php do_action('balzac_top_main'); ?>
		
		<main class="main-content<?php if ($sidebar) echo ' col-2-3'; ?>" role="main" itemprop="mainContentOfPage">
				
			<?php 
				
				if(have_posts()) :
			
					while (have_posts()) : the_post();
							
						get_template_part('content', get_post_format());
					
					endwhile;
				
				else:
				
						get_template_part('content', 'none');
				
				endif;
			?>
			
			<?php balzac_posts_nav(true, '', '<div class="pagination">', '</div>'); ?>
			
		</main><!-- END .main-content -->
		
		<?php if ($sidebar){ ?>
		
			<aside class="sidebar col-1-3" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
			
				<?php dynamic_sidebar('blog'); ?>
				
			</aside><!-- END .sidebar .col-1-3 -->
		
		<?php } ?>
		
		<?php do_action('balzac_bottom_main'); ?>
		
	</div><!-- END .wrapper -->

</section><!-- END .content -->

<?php do_action('balzac_after_main'); ?>

<?php get_footer(); ?>
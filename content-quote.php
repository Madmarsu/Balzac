<?php
/**
 * The template for displaying quote post formats
 *
 * @package Balzac
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 1.0
 */
?>

<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php $quote 		= sanitize_text_field(get_post_meta($post->ID, '_balzac_quote_meta', true)); ?>
<?php $author_quote = sanitize_text_field(get_post_meta($post->ID, '_balzac_quote_author_meta', true)); ?>

<?php do_action('balzac_before_post'); ?>

<article <?php post_class('post'); ?> itemscope itemtype="http://schema.org/Article">

	<?php do_action('balzac_top_post'); ?>

	<div class="entry-quote">

		<?php if (is_single()): ?>

			<h1 class="entry-title" itemprop="name">

				<blockquote><?php echo $quote; ?></blockquote>

			</h1><!--END .entry-title-->

			<div class="entry-quote-author"><?php echo $author_quote; ?></div>

		<?php else: ?>

			<h2 class="entry-title" itemprop="name">

				<blockquote>

					<a href="<?php the_permalink(); ?>" title="<?php echo $quote; ?>"><?php echo $quote; ?></a>

				</blockquote>

			</h2><!--END .entry-title-->

			<div class="entry-quote-author">

				<a href="<?php the_permalink(); ?>" title="<?php echo $quote; ?>">

					<?php echo $author_quote; ?>

				</a>

			</div>

		<?php endif; ?>

	</div><!--END .entry-quote-->

	<div class="wrapper">

		<header class="entry-header">

			<?php do_action('balzac_top_header_post'); ?>

				<?php get_template_part('content', 'header'); ?>

			<?php do_action('balzac_bottom_header_post'); ?>

		</header><!--END .entry-header-->

		<?php get_template_part('content', 'body'); ?>

		<?php get_template_part('content', 'footer'); ?>

		<?php do_action('balzac_bottom_post'); ?>

	</div><!-- END .wrapper -->

</article><!-- END .post -->

<?php do_action('balzac_after_post'); ?>
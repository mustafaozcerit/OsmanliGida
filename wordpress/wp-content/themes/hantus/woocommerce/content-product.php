<?php
/**
 * The template to override product content within loops
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @version 4.3.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class(); ?>>
  <div class="shop-product text-center">
		<?php if ( $product->is_on_sale() ) : ?>
			<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="sale">' . esc_html__( 'Sale!', 'hantus' ) . '</span>', $post, $product ); ?>
		<?php endif; ?>
		<div class="product-img">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail(); ?>
			</a>	
		</div>
		<?php if ($average = $product->get_average_rating()) : ?>
		<ul class="rate">
			<li>
				<?php echo '<i class="fa fa-star" title="'.sprintf(__( 'Rated %s out of 5', 'hantus' ), $average).'"><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong> '.__( 'out of 5', 'hantus' ).'</span></i>'; ?>
			</li>
		</ul>
		<?php endif; ?>
		<h5><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h5>
		<div class="price"><?php echo $product->get_price_html(); ?></div>
		<div class="product-action">
			<div class="button-cart">
				<?php woocommerce_template_loop_add_to_cart(); ?>
			</div>
		</div>
	</div>
</li>

		
<?php
/**
 * Plugin Name: CCR Colorful FAQ
 * Plugin URI: http://www.codexcoder.com/plugins/ccr-colorful-faq
 * Description: Colorful FAQ WordPress plugin from <a href="http://www.codexcoder.com/">CodexCoder</a>. This plugin help you to build awesome colorful FAQ (Frequency Asked Question) page in wordpress site.
 * Version: 1.0.0
 * Author: CodexCoder
 * Author URI: http://codexcoder.com
 * License: GPL2
 * Text Domain: codexcoder
 */

/**
 * Upcomming Features
 * Shortcode System
 * Category Organizer
 * Tag Organizer
 * FAQ Title Color Selector
 * FAQ Title Background Color Selector
 * Font Awesome Icon in FAQ Title.
 * FAQ Background Selector
 */

/*
 * Creating custom cost type to  adding FAQs.
 */

function ccr_faq_post_type() {

	$labels = array(
		'name'                => _x( 'FAQs', 'codexcoder' ),
		'singular_name'       => _x( 'FAQ', 'codexcoder' ),
		'menu_name'           => __( 'FAQs', 'codexcoder' ),
		'parent_item_colon'   => __( 'Parent FAQs:', 'codexcoder' ),
		'all_items'           => __( 'All FAQs', 'codexcoder' ),
		'view_item'           => __( 'View FAQ', 'codexcoder' ),
		'add_new_item'        => __( 'Add New FAQ', 'codexcoder' ),
		'add_new'             => __( 'New FAQ', 'codexcoder' ),
		'edit_item'           => __( 'Edit FAQ', 'codexcoder' ),
		'update_item'         => __( 'Update FAQ', 'codexcoder' ),
		'search_items'        => __( 'Search FAQs', 'codexcoder' ),
		'not_found'           => __( 'No FAQs found', 'codexcoder' ),
		'not_found_in_trash'  => __( 'No FAQs found in Trash', 'codexcoder' ),
		);
	$args = array(
		'label'               => __( 'ccr_faq', 'codexcoder' ),
		'description'         => __( 'Codex Coder FAQs Post Type', 'codexcoder' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 20,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		);
	register_post_type( 'ccr_faq', $args );
}

// Hook into the 'init' action
add_action( 'init', 'ccr_faq_post_type', 0 );


// Add FAQs icon in dashboard
function ccr_faq_dashboard_icon(){
?>
 <style>
/*FAQs Dashboard Icons*/
#adminmenu .menu-icon-ccr_faq div.wp-menu-image:before {
  content: "\f348";
}
</style>
<?php
}
add_action( 'admin_head', 'ccr_faq_dashboard_icon' );


/*
 * FAQ Post Query And Short Code
 */
function ccr_faqs_query_shortcode() {

	$args = array (
		'post_type'              => 'ccr_faq'
		);

	// The Query
	$faqQuery = new WP_Query( $args );

	// First FAQ Active
	$count = 0;
	// Code
	?>
	<div id="ccr-colorful-faqs">
		<div class="panel-group" id="accordion">
			<?php if ( $faqQuery->have_posts() ) {
				while ( $faqQuery->have_posts() ) {
					$faqQuery->the_post(); $count ++; ?>
					<?php if($count == 1) { ?>
					<div class="panel panel-default">
						<div class="panel-heading" style="background:<?php ccr_faq_color(); ?>">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#faq-<?php the_ID(); ?>">
									<span class="pull-right icon"></span>
									<?php the_title() ?>
								</a>
							</h4>
						</div>
						<div id="faq-<?php the_ID(); ?>" class="panel-collapse in">
							<div class="panel-body">
								<?php the_content(); ?>
							</div>
						</div>
					</div>
					<?php } else { ?>
					<div class="panel panel-default">
						<div class="panel-heading" style="background:<?php ccr_faq_color(); ?>">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#faq-<?php the_ID(); ?>" class="collapsed">
									<span class="pull-right icon"></span>
									<?php the_title() ?>
								</a>
							</h4>
						</div>
						<div id="faq-<?php the_ID(); ?>" class="panel-collapse collapse">
							<div class="panel-body">
								<?php the_content(); ?>
							</div>
						</div>
					</div>
					<?php	} }
				} else {
					echo "No FAQs Found";
				}
				?>
			</div>
		</div><!-- /#ccr-colorful-faqs -->
	<?php wp_reset_postdata();
	return;

}
add_shortcode( 'ccr_colorful_faqs', 'ccr_faqs_query_shortcode' );


/*
 * Enqueue Bootstrap According JS and Styleseets
 */

function ccr_faq_load_script_style() {
	wp_enqueue_script('jquery' );
	wp_enqueue_style( 'ccr-faq-style', plugins_url('/assets/css/bootstrap.css', __FILE__), array(), '1.0.0', 'all' );
	wp_enqueue_script( 'ccr-faq-js', plugins_url('/assets/js/bootstrap.min.js', __FILE__), array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'ccr_faq_load_script_style' );


/**
 * Custom Meta Box To change color faq title
 */
add_action( 'add_meta_boxes', 'ccr_faq_color_selector' );
function ccr_faq_color_selector()
{
	add_meta_box( 'ccr-faq-color-box', 'Select Title Color', 'ccr_faq_color_box', 'ccr_faq', 'side', 'high' );
}

function ccr_faq_color_box( $post )
{
	$values = get_post_custom( $post->ID );
	$selected = isset( $values['ccr_faq_color_selector'] ) ? esc_attr( $values['ccr_faq_color_selector'][0] ) : '';
	wp_nonce_field( 'ccr_meta_box_nonce', 'ccr_cfmeta_box_nonce' );
	?>
	<p>
		<label for="ccr_faq_color_selector" >Color Name:</label>
		<select name="ccr_faq_color_selector" id="ccr_faq_color_selector" style="width:50%;">
			<option value="#1abc9c" <?php selected( $selected, '#1abc9c' ); ?> style="background:#1abc9c; color:#FFF;">Deep Green</option>
			<option value="#2ecc71" <?php selected( $selected, '#2ecc71' ); ?> style="background:#2ecc71; color:#FFF;">Light Green</option>
			<option value="#3498db" <?php selected( $selected, '#3498db' ); ?> style="background:#3498db; color:#FFF;">Light Blue</option>
			<option value="#9b59b6" <?php selected( $selected, '#9b59b6' ); ?> style="background:#9b59b6; color:#FFF;">Super Purple</option>
			<option value="#34495e" <?php selected( $selected, '#34495e' ); ?> style="background:#34495e; color:#FFF;">Deep Gray</option>
			<option value="#f1c40f" <?php selected( $selected, '#f1c40f' ); ?> style="background:#f1c40f; color:#FFF;">Deep Yellow</option>
			<option value="#e67e22" <?php selected( $selected, '#e67e22' ); ?> style="background:#e67e22; color:#FFF;">Deep Orange</option>
			<option value="#e74c3c" <?php selected( $selected, '#e74c3c' ); ?> style="background:#e74c3c; color:#FFF;">Light Red</option>
			<option value="#95a5a6" <?php selected( $selected, '#95a5a6' ); ?> style="background:#95a5a6; color:#FFF;">Light Gray</option>
		</select>
	</p>
	<?php	
}


add_action( 'save_post', 'ccr_faq_color_box_save_data' );
function ccr_faq_color_box_save_data( $post_id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	if( !isset( $_POST['ccr_cfmeta_box_nonce'] ) || !wp_verify_nonce( $_POST['ccr_cfmeta_box_nonce'], 'ccr_meta_box_nonce' ) ) return;
	
	if( !current_user_can( 'edit_post' ) ) return;

	if( isset( $_POST['ccr_faq_color_selector'] ) )
		update_post_meta( $post_id, 'ccr_faq_color_selector', esc_attr( $_POST['ccr_faq_color_selector'] ) );
}

function ccr_faq_color() {
	if ( get_post_meta( get_the_ID(), 'ccr_faq_color_selector', true ) ) {

	echo get_post_meta( get_the_ID(), 'ccr_faq_color_selector', true );

	}
}
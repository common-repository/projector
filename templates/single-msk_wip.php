<?php get_header(); ?>

	<?php do_action('msk_projector_before_outside_content'); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php $msk_wip_data = msk_projector_prepare_wip_data(); ?>

			<?php do_action('msk_projector_before_inside_content', $msk_wip_data); ?>

			<?php /**
                   ** Here comes the fun
	               */

			if (is_msk_projector_password_protected()) :

				msk_projector_show_password_form();

			else :

				// Global WIP content
				if ($msk_wip_data['content'] != '') {
					echo apply_filters('msk_projector_wip_content', $msk_wip_data['content'], $msk_wip_data);
				}

				// WIP items looping
				if (count($msk_wip_data['wip_content']) > 0) {
					foreach ($msk_wip_data['wip_content'] as $item) {

						do_action('msk_projector_before_wip_content');

						do_action('msk_projector_before_wip_content_txt');

						// Item title & desc
						if ($item['title'] != '') echo apply_filters('msk_projector_wip_item_title', $item['title'], $item);
						if ($item['desc'] != '') echo apply_filters('msk_projector_wip_item_desc', $item['desc'], $item);

						do_action('msk_projector_after_wip_content_txt');

						// Item images
						if (count($item['images']) > 0) {
							do_action('msk_projector_before_wip_content_image');

							foreach ($item['images'] as $image) {
								echo apply_filters('msk_projector_wip_item_image', $image, $item);
							}

							do_action('msk_projector_after_wip_content_image');
						}

						do_action('msk_projector_after_wip_content');

					}

				}
			endif; ?>

			<?php do_action('msk_projector_after_inside_content', $msk_wip_data); ?>

		<?php endwhile; ?>

	<?php do_action('msk_projector_after_outside_content'); ?>

<?php if (get_option('msk_projector_global_settings_enable_sidebar') == 1) get_sidebar(); ?>

<?php get_footer(); ?>
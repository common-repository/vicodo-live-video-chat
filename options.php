<?php defined( 'ABSPATH' ) or die; ?>
<?php
	$show_device = ( array ) $this->get_option( 'show_device', array() );
	$show_pages = $this->get_option( 'show_pages', 'all' );
	$specific_posts = ( array ) $this->get_option( 'specific_posts', array() );
	$specific_pages = ( array ) $this->get_option( 'specific_pages', array() );
	$exclude_posts = ( array ) $this->get_option( 'exclude_posts', array() );
	$exclude_pages = ( array ) $this->get_option( 'exclude_pages', array() );
?>
<div class="wrap wrap-vicodo-lvc">
	<div class="logo">
		<img width="128" height="auto" style="max-width:100%" src="<?php esc_attr_e( $this->logo_url ); ?>"/>
	</div>
	<?php settings_errors(); ?>
	<h1><?php _e( 'Vicodo Live Video Chat - Options', 'vicodo-lvc' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'vicodo_lvc_optsgroup' ); ?>
		<?php do_settings_sections( 'vicodo_lvc_optsgroup' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Vicodo Widget ID', 'vicodo-lvc' ); ?></th>
				<td>
					<input type="text" name="vicodo_lvc_options[widget_id]" value="<?php esc_attr_e( $this->get_option( 'widget_id' ) ); ?>" class="regular-text">
					<p><?php echo sprintf( __( 'Get your widget ID %shere%s.', 'vicodo-lvc'), '<a target="_blank" href="https://app.vicodo.com/dashboard/settings/integration/vicodo-direct-website-widget">', '</a>' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Shown Only on Device', 'vicodo-lvc' ); ?></th>
				<td>
					<select name="vicodo_lvc_options[show_device][]" multiple class="chosen-select regular-text">
						<option value="all" <?php if ( in_array( 'all', $show_device ) || empty( $show_device ) ) echo 'selected'; ?>><?php esc_html_e( 'All', 'vicodo-lvc' ); ?></option>
						<option value="desktop" <?php if ( in_array( 'desktop', $show_device ) ) echo 'selected'; ?>><?php esc_html_e( 'Desktop', 'vicodo-lvc' ); ?></option>
						<option value="mobile" <?php if ( in_array( 'mobile', $show_device ) ) echo 'selected'; ?>><?php esc_html_e( 'Mobile', 'vicodo-lvc' ); ?></option>
						<option value="tablet" <?php if ( in_array( 'tablet', $show_device ) ) echo 'selected'; ?>><?php esc_html_e( 'Tablet', 'vicodo-lvc' ); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'In which pages do you want to insert this code?', 'vicodo-lvc' ); ?></th>
				<td>
					<input type="radio" name="vicodo_lvc_options[show_pages]" value="all" <?php if ( $show_pages == 'all' ) echo 'checked'; ?>><?php esc_html_e( 'In the whole website (pages, posts and archives)', 'vicodo-lvc' ); ?><br>
					<input type="radio" name="vicodo_lvc_options[show_pages]" value="specific" <?php if ( $show_pages == 'specific' ) echo 'checked'; ?>><?php esc_html_e( 'In specific pages or posts', 'vicodo-lvc' ); ?><br>
					<div class="specific" style="height:0;overflow:hidden">
						<h4><?php _e( 'Posts', 'vicodo-lvc' ); ?></h4>
						<div>
							<?php $this->get_dropdown_posts( 'vicodo_lvc_options[specific_posts][]', $specific_posts ); ?>
						</div>
						<h4><?php _e( 'Pages', 'vicodo-lvc' ); ?></h4>
						<div>
							<?php $this->get_dropdown_pages( 'vicodo_lvc_options[specific_pages][]', $specific_pages ); ?>
						</div>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Do you want to exclude some specific post/pages?', 'vicodo-lvc' ); ?></th>
				<td>
					<h4><?php _e( 'Exclude Posts', 'vicodo-lvc' ); ?></h4>
					<div>
						<?php $this->get_dropdown_posts( 'vicodo_lvc_options[exclude_posts][]', $exclude_posts, true ); ?>
					</div>
					<h4><?php _e( 'Exclude Pages', 'vicodo-lvc' ); ?></h4>
					<div>
						<?php $this->get_dropdown_pages( 'vicodo_lvc_options[exclude_pages][]', $exclude_pages, true ); ?>
					</div>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Save', 'vicodo-lvc' ) ); ?>
	</form>
	<p><?php echo sprintf( __( 'You can change the appearance settings %shere%s.', 'vicodo-lvc' ), '<a target="_blank" href="https://app.vicodo.com/dashboard/settings/integration/vicodo-direct-website-widget">', '</a>' ); ?></p>
	<p><?php echo sprintf( __( 'Do you need help? Visit %ssupport page%s.', 'vicodo-lvc' ), '<a target="_blank" href="https://support.vicodo.com/hc/articles/5625635359505-Live-video-chat-website-widget">', '</a>' ); ?></p>
</div>
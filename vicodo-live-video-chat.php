<?php
/*
Plugin Name: Vicodo Live Video & Chat
Description: Use Vicodo live video&chat plugin to run video support directly on your website, answer customers' questions, send files and gather feedback.
Version:     1.4.0
Author:      Vicodo
Author URI:  https://www.vicodo.com
Text Domain: vicodo-lvc
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die;

define( 'VICODO_LVC_VER', '1.4.0' );

if ( ! class_exists( 'Vicodo_LVC' ) ) {
	class Vicodo_LVC {
		public static function get_instance() {
			if ( self::$instance == null ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		private static $instance = null;

		private function __clone() { }

		private function __wakeup() { }

		private function __construct() {
			// Properties
			$this->logo_url = plugins_url( 'images/logo.png', __FILE__ );
			$this->allowed_html = array(
				'select' => array(
					'name' => array(),
					'multiple' => array(),
					'class' => array()
				),
				'option' => array(
					'value' => array(),
					'selected' => array()
				)
			);

			// Actions
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
			add_action( 'wp_head', array( $this, 'output_embed_code' ) );

			// Filters
			add_filter( 'plugin_action_links_vicodo-live-video-chat/vicodo-live-video-chat.php', array( $this, 'settings_link' ) );
		}

		public function init() {
			load_plugin_textdomain( 'vicodo-lvc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		public function register_settings() {
			register_setting( 'vicodo_lvc_optsgroup', 'vicodo_lvc_options' );
		}

		public function add_admin_menu() {
			add_menu_page(
				__( 'Vicodo Live Video Chat', 'vicodo-lvc' ),
				__( 'Vicodo', 'vicodo-lvc' ),
				'manage_options',
				'vicodo',
				array( $this, 'render_options_page' ),
				plugins_url( 'images/dashicon.svg', __FILE__ )
			);
		}

		public function render_options_page() {
			require( __DIR__ . '/options.php' );
		}

		public function enqueue_admin_assets( $hn ) {
			wp_enqueue_style( 'chosen', plugins_url( 'css/chosen.min.css', __FILE__ ), array(), VICODO_LVC_VER, 'all' );
			wp_enqueue_script( 'chosen', plugins_url( 'js/chosen.jquery.min.js', __FILE__ ), array( 'jquery' ), VICODO_LVC_VER, true );
			wp_enqueue_script( 'vicodo-lvc-admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'chosen' ), VICODO_LVC_VER, true );
		}

		public function output_embed_code() {
			$widget_id = trim( $this->get_option( 'widget_id' ) );
			if ( $widget_id == '' ) return;

			// Devices
			$show_device = ( array ) $this->get_option( 'show_device', array() );
			if ( ! empty( $show_device ) && ! in_array( 'all', $show_device ) ) {
				require_once __DIR__ . '/vendor/autoload.php';
				$detect = new Mobile_Detect;
				if ( $detect->isMobile() && ! in_array( 'mobile', $show_device ) ) return;
				if ( $detect->isTablet() && ! in_array( 'tablet', $show_device ) ) return;
				if ( ! $detect->isMobile() && ! $detect->isTablet() && ! in_array( 'desktop', $show_device ) ) return;
			}

			// Specific post, pages
			$show_pages = $this->get_option( 'show_pages', 'all' );
			if ( $show_pages != 'all' ) {
				if ( ! is_singular( array( 'post', 'page' ) ) ) return;

				global $post;

				if ( is_singular( 'page' ) ) {
					$specific_pages = ( array ) $this->get_option( 'specific_pages', array() );
					if ( ! in_array( $post->ID, $specific_pages ) ) return;
				}
				if ( is_singular( 'post' ) ) {
					$specific_posts = ( array ) $this->get_option( 'specific_posts', array() );
					if ( ! in_array( $post->ID, $specific_posts ) ) return;
				}
			}

			// Excludes posts, pages
			if ( is_singular( 'post' ) || is_singular( 'page' ) ) {
				global $post;
				$exclude_posts = ( array ) $this->get_option( 'exclude_posts', array() );
				$exclude_pages = ( array ) $this->get_option( 'exclude_pages', array() );

				if ( $post->post_type == 'post' && in_array( 'all', $exclude_posts ) ) return;
				if ( $post->post_type == 'page' && in_array( 'all', $exclude_pages ) ) return;

				if ( in_array( $post->ID, $exclude_posts ) ) return;
				if ( in_array( $post->ID, $exclude_pages ) ) return;
			}

			require( __DIR__ . '/widget.php' );
		}

		public function settings_link( $links ) {
			$url = esc_url( add_query_arg( 'page', 'vicodo', get_admin_url() . 'admin.php' ) );
			$settings_link = "<a href='$url'>" . __( 'Settings', 'vicodo-lvc' ) . '</a>';
			array_splice( $links, 0, 0, $settings_link );
			return $links;
		}

		private function get_option( $option_name, $default = '' ) {
			if ( is_null( $this->options ) ) $this->options = ( array ) get_option( 'vicodo_lvc_options', array() );
			if ( isset( $this->options[$option_name] ) ) return $this->options[$option_name];
			return $default;
		}

		private function get_dropdown_posts( $name, $selected, $include_all = false ) {
			$html = '<select name="' . esc_attr( $name ) . '" multiple class="chosen-select regular-text">';
			if ( $include_all ) $html .= '<option value="all"' . ( in_array( 'all', $selected ) ? 'selected' : '' ) . '>' . __( 'All', 'vicodo-lvc' ) . '</option>';
			$posts = get_posts( array( 'numberposts' => -1 ) );
			foreach( $posts as $post ) {
				$html .= '<option value="' . esc_attr( $post->ID ) . '"' . ( in_array( $post->ID, $selected ) ? ' selected' : '' ) . '>' . esc_html( $post->post_title ) . '</option>';
			}
			$html .= '</select>';
			echo wp_kses( $html, $this->allowed_html );
		}

		private function get_dropdown_pages( $name, $selected, $include_all = false ) {
			$html = '<select name="' . esc_attr( $name ) . '" multiple class="chosen-select regular-text">';
			if ( $include_all ) $html .= '<option value="all"' . ( in_array( 'all', $selected ) ? 'selected' : '' ) . '>' . __( 'All', 'vicodo-lvc' ) . '</option>';
			$posts = get_posts( array( 'numberposts' => -1, 'post_type' => 'page' ) );
			foreach( $posts as $post ) {
				$html .= '<option value="' . esc_attr( $post->ID ) . '"' . ( in_array( $post->ID, $selected ) ? ' selected' : '' ) . '>' . esc_html( $post->post_title ) . '</option>';
			}
			$html .= '</select>';
			echo wp_kses( $html, $this->allowed_html );
		}
	}
}
Vicodo_LVC::get_instance();

<?php

declare( strict_types=1 );

namespace Blockify\Utilities;

use WP_Theme;
use function basename;
use function dirname;
use function function_exists;
use function get_plugin_data;
use function get_template;
use function plugin_basename;
use function plugin_dir_url;
use function str_contains;
use function strip_tags;
use function trailingslashit;
use function wp_get_theme;

/**
 * Data object.
 *
 * @since 1.0.0
 */
class Data {

	public string $file        = '';
	public string $dir         = '';
	public string $basename    = '';
	public string $url         = '';
	public string $slug        = '';
	public string $name        = '';
	public string $description = '';
	public string $author      = '';
	public string $author_uri  = '';
	public string $version     = '';
	public string $min_php     = '';
	public string $min_wp      = '';
	public string $domain_path = '';
	public string $uri         = '';
	public string $update_uri  = '';

	/**
	 * Data constructor.
	 *
	 * @param string $file Main plugin or theme file.
	 *
	 * @return void
	 */
	public function __construct( string $file ) {
		if ( str_contains( $file, 'content/plugins' ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$this->from_plugin( $file, get_plugin_data( $file ) );
		} elseif ( str_contains( $file, 'content/themes' ) ) {
			$this->from_theme( wp_get_theme( get_template() ) );
		}
	}

	/**
	 * Data factory.
	 *
	 * @param string $file Main plugin or theme file.
	 *
	 * @return void
	 */
	public static function from( string $file ): self {
		static $instances = [];

		if ( ! isset( $instances[ $file ] ) ) {
			$instances[ $file ] = new self( $file );
		}

		return $instances[ $file ];
	}

	/**
	 * Plugin constructor.
	 *
	 * @param string $file Path to plugin file.
	 * @param array  $data Plugin file headers.
	 *
	 * @return void
	 */
	private function from_plugin( string $file, array $data ): void {
		$this->file        = $file;
		$this->dir         = trailingslashit( dirname( $file ) );
		$this->url         = trailingslashit( plugin_dir_url( $file ) );
		$this->basename    = plugin_basename( $file );
		$this->name        = $data['Name'] ?? '';
		$this->slug        = $data['TextDomain'] ?? '';
		$this->description = $data['Description'] ?? '';
		$this->author      = strip_tags( $data['Author'] ?? '' );
		$this->author_uri  = $data['AuthorURI'] ?? '';
		$this->version     = $data['Version'] ?? '';
		$this->uri         = $data['PluginURI'] ?? '';
		$this->domain_path = $data['DomainPath'] ?? '';
		$this->min_wp      = $data['RequiresWP'] ?? '';
		$this->min_php     = $data['RequiresPHP'] ?? '';
		$this->update_uri  = $data['UpdateURI'] ?? '';
	}

	/**
	 * Theme constructor.
	 *
	 * @param WP_Theme $theme Theme instance.
	 *
	 * @return void
	 */
	private function from_theme( WP_Theme $theme ): void {
		$this->dir         = trailingslashit( $theme->get_template_directory() );
		$this->url         = trailingslashit( $theme->get_template_directory_uri() );
		$this->slug        = $theme->get_template();
		$this->file        = $this->dir . DIRECTORY_SEPARATOR . $this->slug . '.php';
		$this->basename    = basename( $this->dir ) . DIRECTORY_SEPARATOR . basename( $this->file );
		$this->name        = $theme->get( 'Name' );
		$this->description = $theme->get( 'Description' );
		$this->author      = $theme->get( 'Author' );
		$this->author_uri  = $theme->get( 'AuthorURI' );
		$this->version     = $theme->get( 'Version' );
		$this->min_php     = $theme->get( 'RequiresPHP' );
		$this->min_wp      = $theme->get( 'RequiresWP' );
		$this->uri         = $theme->get( 'ThemeURI' );
		$this->domain_path = $theme->get( 'DomainPath' );
		$this->update_uri  = $theme->get( 'UpdateURI' );
	}
}

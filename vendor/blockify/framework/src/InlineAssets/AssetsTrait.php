<?php

declare( strict_types=1 );

namespace Blockify\Framework\InlineAssets;

use Blockify\Utilities\Path;
use Blockify\Utilities\Str;
use function basename;
use function dirname;
use function get_class;
use function str_contains;
use function strtolower;
use function uniqid;

/**
 * InlineAssets trait.
 *
 * @since 1.0.0
 */
trait AssetsTrait {

	/**
	 * Directory.
	 *
	 * @var string
	 */
	public string $dir;

	/**
	 * URL.
	 *
	 * @var string
	 */
	public string $url;

	/**
	 * Handle.
	 *
	 * @var string
	 */
	public string $handle;

	/**
	 * Callbacks.
	 *
	 * @var callable[]
	 */
	private array $callbacks = [];

	/**
	 * Files.
	 *
	 * @var array <string, array<array, boolean>>
	 */
	private array $files = [];

	/**
	 * Strings.
	 *
	 * @var array <string, array>
	 */
	private array $strings = [];

	/**
	 * Localized data.
	 *
	 * @param array $data <string, array<array, array>>
	 */
	private array $data = [];

	/**
	 * Asset type.
	 *
	 * @var string
	 */
	private string $type = '';

	/**
	 * Inline asset constructor.
	 *
	 * @param string $file Path to main plugin or theme file.
	 *
	 * @return void
	 */
	public function __construct( string $file ) {
		$project_dir  = dirname( $file );
		$package_dir  = dirname( __DIR__, 2 );
		$dir          = Path::get_package_dir( $project_dir, $package_dir );
		$url          = Path::get_package_url( $project_dir, $package_dir );
		$this->type   = str_contains( static::class, 'Style' ) ? 'css' : 'js';
		$this->dir    = "{$dir}public/{$this->type}/";
		$this->url    = "{$url}public/{$this->type}/";
		$this->handle = strtolower( basename( dirname( $file ), '.php' ) );
	}

	/**
	 * Register inline assets from file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file    Callable with access to template HTML.
	 * @param array  $strings Array of strings to check for in the template HTML.
	 *
	 * @return self
	 */
	public function add_file( string $file, array $strings = [], bool $condition = true ): self {
		$this->files[ $file ] = [ $strings, $condition ];

		return $this;
	}

	/**
	 * Register inline assets from callback.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $callback Callable with access to global template HTML variable.
	 *
	 * @return self
	 */
	public function add_callback( callable $callback ): self {
		$this->callbacks[] = $callback;

		return $this;
	}

	/**
	 * Register inline assets from string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string  String to add to the template HTML.
	 * @param array  $strings Array of strings to check for in the template HTML.
	 *
	 * @return self
	 */
	public function add_string( string $string, array $strings = [], bool $condition = true ): self {
		$this->strings[ $string ] = [ $strings, $condition ];

		return $this;
	}

	/**
	 * Adds l10n data for scripts and custom properties for styles.
	 *
	 * @param string $key       Array key.
	 * @param array  $value     Array of data to localize.
	 * @param array  $strings   Array of strings to check for in the template HTML.
	 * @param bool   $condition Other condition to check for.
	 *
	 * @return self
	 */
	public function add_data(
		string $key,
		array  $value,
		array  $strings = [],
		bool   $condition = true
	): self {
		$this->data[ $key ] = [ $value, $strings, $condition ];

		return $this;
	}

	/**
	 * Enqueue inline assets.
	 *
	 * @hook enqueue_block_assets
	 *
	 * @return void
	 */
	abstract public function enqueue(): void;

	/**
	 * Returns string containing all inline assets.
	 *
	 * @param ?string $template_html Global template HTML variable.
	 * @param bool    $load_all      Load all assets.
	 *
	 * @return string
	 */
	private function get_inline_assets( ?string $template_html, bool $load_all ): string {
		$string = '';
		$assets = $this->get_assets( $template_html ?? '', $load_all );

		foreach ( $assets as $asset ) {
			$string .= Str::remove_line_breaks( $asset );
		}

		/**
		 * Filters the inline assets.
		 *
		 * @param string $string        Inline assets.
		 * @param string $template_html Global template HTML variable.
		 * @param bool   $load_all      Load all assets.
		 * @param object $instance      Instance of the class.
		 */
		return apply_filters(
			"{$this->handle}_inline_{$this->type}",
			$string,
			$template_html,
			$load_all,
			$this
		);
	}

	/**
	 * Returns array of inline assets.
	 *
	 * @param string $template_html Global template HTML variable.
	 * @param bool   $load_all      Load all assets.
	 *
	 * @return array
	 */
	private function get_assets( string $template_html, bool $load_all ): array {
		$assets = [];

		foreach ( $this->strings as $string => $args ) {
			$strings   = $args[0] ?? [];
			$condition = $args[1] ?? true;

			if ( $load_all || ! $strings || Str::contains_any( $template_html, ...$strings ) || $condition ) {
				$id            = uniqid( static::class );
				$assets[ $id ] = $string;
			}
		}

		foreach ( $this->callbacks as $callback ) {
			$id = is_array( $callback ) ? get_class( $callback[0] ) . '\\' . $callback[1] ?? '' : $callback;

			$assets[ $id ] = $callback( $template_html, $load_all, $this );
		}

		foreach ( $this->files as $file => $args ) {
			$strings = $args[0] ?? [];

			// Skip if additional condition is not met.
			if ( isset( $args[1] ) && ! $args[1] && ! Str::contains_any( $template_html, ...$strings ) ) {
				continue;
			}

			if ( $load_all || ! $strings || Str::contains_any( $template_html, ...$strings ) ) {
				if ( file_exists( $this->dir . $file ) ) {
					$assets[ $file ] = file_get_contents( $this->dir . $file );
				}
			}
		}

		return $assets;
	}
}

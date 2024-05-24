<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Utilities\Data;
use Blockify\Utilities\Str;
use Fieldify\Fields\Blocks;
use WP_Block;
use function dirname;
use function str_replace;
use const DIRECTORY_SEPARATOR;

/**
 * AbstractBlock class.
 *
 * @since 1.0.0
 */
abstract class AbstractBlock {

	/**
	 * Name.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * File.
	 *
	 * @var string
	 */
	protected string $file;

	/**
	 * Title.
	 *
	 * @var string
	 */
	protected string $title;

	/**
	 * Description.
	 *
	 * @var ?string
	 */
	protected ?string $description = null;

	/**
	 * Text domain.
	 *
	 * @var string
	 */
	protected string $text_domain = 'blockify-blocks';

	/**
	 * Category.
	 *
	 * @var string|null
	 */
	protected ?string $category = null;

	/**
	 * Icon.
	 *
	 * @var string|array|null
	 */
	protected $icon = null;

	/**
	 * Keywords.
	 *
	 * @var ?array
	 */
	protected ?array $keywords = [];

	/**
	 * Supports.
	 *
	 * @var ?array
	 */
	protected ?array $supports = [];

	/**
	 * Attributes.
	 *
	 * @var ?array
	 */
	protected ?array $attributes = [];

	/**
	 * Post types.
	 *
	 * @var ?array
	 */
	protected ?array $post_types = [];

	/**
	 * Data.
	 *
	 * @var Data
	 */
	protected Data $data;

	/**
	 * Panels.
	 *
	 * @var array<string, array<string,array>>
	 */
	protected array $panels = [];

	/**
	 * Sets the plugin or theme data.
	 *
	 * @since 1.0.0
	 *
	 * @param Data $data Plugin or theme data.
	 *
	 * @return void
	 */
	public function set_data( Data $data ) {
		$this->data = $data;
	}

	/**
	 * Register block.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		$prefix = 'blockify' . DIRECTORY_SEPARATOR;
		$slug   = Str::camel_to_kebab( str_replace(
			__NAMESPACE__ . '\\',
			'',
			static::class
		) );
		$file   = dirname( __DIR__ ) . '/public/' . $slug;

		$args = [
			'file'            => $file,
			'title'           => Str::title_case( $slug ),
			'description'     => $this->description,
			'category'        => $this->category ?: $this->data->author,
			'icon'            => $this->icon,
			'keywords'        => $this->keywords,
			'supports'        => $this->supports,
			'attributes'      => $this->attributes,
			'text_domain'     => $this->text_domain,
			'render_callback' => [ $this, 'render' ],
		];

		if ( ! empty( $this->post_types ) ) {
			$args['post_types'] = $this->post_types;
		}

		if ( ! empty( $this->panels ) ) {
			$args['panels'] = $this->panels;
		}

		Blocks::register_block( $prefix . $slug, $args );
	}

	/**
	 * Block render callback.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $attributes Block data.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string
	 */
	abstract public function render( array $attributes, string $content, WP_Block $block ): string;

}

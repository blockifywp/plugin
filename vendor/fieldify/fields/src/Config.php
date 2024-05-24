<?php

declare( strict_types=1 );

namespace Fieldify\Fields;

use Blockify\Utilities\Path;
use function dirname;

/**
 * Config class.
 *
 * @since 1.0.0
 */
class Config {

	/**
	 * Package directory.
	 *
	 * @var string
	 */
	public string $dir;

	/**
	 * Package URL.
	 *
	 * @var string
	 */
	public string $url;

	/**
	 * Custom slug.
	 *
	 * @var string
	 */
	public string $slug = 'fieldify';

	/**
	 * Config constructor.
	 *
	 * @param string $file Plugin or theme directory.
	 *
	 * @return void
	 */
	public function __construct( string $file ) {
		$project_dir = dirname( $file );
		$package_dir = dirname( __DIR__ );
		$this->dir   = Path::get_package_dir( $project_dir, $package_dir );
		$this->url   = Path::get_package_url( $project_dir, $package_dir );
	}

}

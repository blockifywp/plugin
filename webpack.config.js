const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path          = require( 'path' );
const fs            = require( 'fs' );
const glob          = require( 'glob' );

const rename = () => {
	const { join } = path;

	const blockStyleFiles = glob.sync(
		join( process.cwd(), 'build/blocks', '**', 'style-index.css' ),
	);

	if ( blockStyleFiles ) {
		blockStyleFiles.forEach( blockStyleFile => {
			fs.rename( blockStyleFile, blockStyleFile.replace( '-index.css', '.css' ), error => {
				if ( error ) {
					console.log( error );
				}
			} );
		} );
	}

	const blockEditorStyleFiles = glob.sync(
		join( process.cwd(), 'build/blocks', '**', 'index.css' ),
	);

	if ( blockEditorStyleFiles ) {
		blockEditorStyleFiles.forEach( blockEditorStyleFile => {
			fs.rename( blockEditorStyleFile, blockEditorStyleFile.replace( 'index.css', 'editor.css' ), error => {
				if ( error ) {
					console.log( error );
				}
			} );
		} );
	}

	const blockJsonFiles = glob.sync(
		join( process.cwd(), 'build/blocks', '**', 'block.json' ),
	);

	if ( blockJsonFiles ) {
		blockJsonFiles.forEach( filePath => {
			let blockJson = require( filePath );

			if ( blockJson?.editorScript ) {
				blockJson.editorScript = blockJson.editorScript.replace( '.tsx', '.js' );
			}

			if ( blockJson?.script ) {
				blockJson.script = blockJson.script.replace( '.tsx', '.js' );
			}

			if ( blockJson?.viewScript ) {
				blockJson.viewScript = blockJson.viewScript.replace( '.tsx', '.js' );
			}

			if ( blockJson?.editorStyle ) {
				blockJson.editorStyle = blockJson.editorStyle.replace( '.scss', '.css' );
			}

			if ( blockJson?.style ) {
				blockJson.style = blockJson.style.replace( '.scss', '.css' );
			}

			fs.writeFile( filePath, JSON.stringify( blockJson, null, 2 ), function writeJSON( error ) {
				if ( error ) {
					return console.log( error );
				}
			} );
		} );
	}
};

module.exports = {
	...defaultConfig,

	module: {
		...defaultConfig.module,
	},

	entry: {
		...defaultConfig.entry, // Blocks.
		editor: './src/editor.tsx',
		admin: './src/admin.tsx',
		'animation/index': './src/extensions/animation/index.tsx',
		'pattern-editor/index': './src/extensions/pattern-editor/index.tsx',
		'page-settings/index': './src/extensions/page-settings/index.tsx',
	},

	plugins: [
		...defaultConfig.plugins,

		{
			apply: compiler => {
				compiler.hooks.afterEmit.tap( 'rename', rename );
			}
		},
	],
};

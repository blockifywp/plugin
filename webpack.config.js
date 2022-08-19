const defaultConfig     = require( '@wordpress/scripts/config/webpack.config' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const path              = require( 'path' );
const fs                = require( 'fs' );
const glob              = require( 'glob' );

const rename = () => {
	const { join } = path;

	const blockStyleFiles = glob.sync(
		join( process.cwd(), 'build/blocks', '**', 'style-script.css' ),
	);

	if ( blockStyleFiles ) {
		blockStyleFiles.forEach( blockStyleFile => {
			fs.rename( blockStyleFile, blockStyleFile.replace( '-script.css', '.css' ), error => {
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
		...defaultConfig.entry,
	},

	plugins: [
		...defaultConfig.plugins,

		new BrowserSyncPlugin( {
			host: 'localhost',
			port: 8887,
			proxy: 'https://blockify.local/'
		} ),

		{
			apply: compiler => {
				compiler.hooks.afterEmit.tap( 'rename', rename );
			}
		},
	],
};


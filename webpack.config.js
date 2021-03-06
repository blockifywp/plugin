const FileManagerPlugin        = require( 'filemanager-webpack-plugin' );
const defaultConfig            = require( '@wordpress/scripts/config/webpack.config' );
const path                     = require( 'path' );
const fs                       = require( 'fs' );
const glob                     = require( 'glob' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

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
		index: path.resolve( process.cwd(), 'src', 'index.tsx' ),
		editor: path.resolve( process.cwd(), 'src', 'editor.scss' ),
		style: path.resolve( process.cwd(), 'src', 'style.scss' ),
		...glob.sync( path.resolve( process.cwd(), 'src', 'core/**/style.scss' ) )
			.reduce( ( entries, filename ) => {
				const name = filename.split( '/' ).reverse()[ 1 ];

				return { ...entries, [ 'core/' + name + '/style' ]: filename };
			}, {} ),
		...glob.sync( path.resolve( process.cwd(), 'src', 'core/**/script.tsx' ) )
			.reduce( ( entries, filename ) => {
				const name = filename.split( '/' ).reverse()[ 1 ];

				return { ...entries, [ 'core/' + name + '/script' ]: filename };
			}, {} )
	},

	plugins: [
		...defaultConfig.plugins,

		new RemoveEmptyScriptsPlugin(),

		new FileManagerPlugin( {
			events: {
				onEnd: {
					copy: [
						{
							source: './build/style-style.css',
							destination: './build/style.css'
						}
					],
					delete: [
						'./build/index.css',
						'./build/style-editor.css',
						'./build/style-style.css'
					],
				},
			},
		} ),

		{
			apply: compiler => {
				compiler.hooks.afterEmit.tap( 'rename', rename );
			}
		},
	],
};


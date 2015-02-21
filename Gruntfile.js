/*
 * Based on http://torquemag.io/automating-wordpress-plugin-updates-releases-grunt/
 *
 * added:
 *        copy:svn_assets task
 *        makepot, creates wp-anchorific.pot
 */
module.exports = function (grunt) {

	//setup file list for copying/ not copying for SVN
	files_list = [
		'**',
		'!assets/**', // will be copied in copy:svn_assets below
		'!node_modules/**',
		'!release/**',
		'!.git/**',
		'!.sass-cache/**',
		'!Gruntfile.js',
		'!package.json',
		'!.gitignore',
		'!.gitmodules',
		'!bin/**',
		'!tests/**',
		'!.gitattributes',
		'!.travis.yml',
		'!composer.lock',
		'!composer.json',
		'!CONTRIBUTING.md',
		'!git-workflow.md',
		'!phpunit.xml.dist'
	];

	// Project configuration.
	grunt.initConfig({
		pkg : grunt.file.readJSON( 'package.json' ),
		clean: {
			post_build: [
				'build'
			],
			anchorific_tmp: [
				'tmp/anchorific'
			]
		},
		copy: {
/*			svn_assets: {
				options : {
					mode :true
				},
				expand: true,
				cwd:  'assets/',
				src:  '**',
				dest: 'build/<%= pkg.name %>/assets/',
				flatten: true,
				filter: 'isFile'
			},
			svn_trunk: {
				options : {
					mode :true
				},
				src:  files_list,
				dest: 'build/<%= pkg.name %>/trunk/'
			},
			svn_tag: {
				options : {
					mode :true
				},
				src:  files_list,
				dest: 'build/<%= pkg.name %>/tags/<%= pkg.version %>/'
			},
*/			anchorific: {
				options : {
					mode :true
				},
				expand: true,
				files: [
					{ expand: true, src: ['tmp/anchorific/css/**'], dest: 'css/', filter: 'isFile', flatten: true},
					{ expand: true, src: ['tmp/anchorific/min/**'], dest: 'js/', filter: 'isFile', flatten: true }
				],
			},
		},
		gittag: {
			addtag: {
				options: {
					tag: '<%= pkg.version %>',
					message: 'Version <%= pkg.version %>'
				}
			}
		},
		gitcommit: {
			commit: {
				options: {
					message: 'Version <%= pkg.version %>',
					noVerify: true,
					noStatus: false,
					allowEmpty: true
				},
				files: {
//					src: [ 'README.md', 'readme.txt', 'wp-anchorific.php', 'package.json', 'Gruntfile.js','assets/**', 'inc/**', 'languages/**', 'lib/**', 'templates/**' ]
					src: [ 'wp-anchorific.php', 'package.json', 'Gruntfile.js', 'css/**', 'languages/**', 'js/**' ]
				}
			}
		},
		gitpush: {
			push: {
				options: {
					tags: true,
					remote: 'origin',
					branch: 'master'
				}
			}
		},
		gitclone: {
		    anchorific: {
		      	options: {
		        	repository: 'https://github.com/renettarenula/anchorific.js.git',
		        	directory: 'tmp/anchorific'
		      	}
		    }
		},
		replace: {
			reamde_md: {
				src: [ 'README.md' ],
				overwrite: true,
				replacements: [{
					from: /~Current Version:\s*(.*)~/,
					to: "~Current Version: <%= pkg.version %>~"
				}, {
					from: /Latest Stable Release:\s*\[(.*)\]\s*\(https:\/\/github.com\/soderlind\/wp-anchorific\/releases\/tag\/(.*)\s*\)/,
					to: "Latest Stable Release: [<%= pkg.git_tag %>](https://github.com/soderlind/wp-anchorific/releases/tag/<%= pkg.git_tag %>)"
				}]
			},
			reamde_txt: {
				src: [ 'readme.txt' ],
				overwrite: true,
				replacements: [{
					from: /Stable tag: (.*)/,
					to: "Stable tag: <%= pkg.version %>"
				}]

			},
			plugin_php: {
				src: [ 'wp-anchorific.php' ],
				overwrite: true,
				replacements: [{
					from: /Version:\s*(.*)/,
					to: "Version: <%= pkg.version %>"
				}, {
					from: /define\(\s*'READOFFLINE_VERSION',\s*'(.*)'\s*\);/,
					to: "define( 'READOFFLINE_VERSION', '<%= pkg.version %>' );"
				}]
			}
		},
/*		svn_checkout: {
			make_local: {
				repos: [
					{
						path: [ 'release' ],
						repo: 'http://plugins.svn.wordpress.org/wp-anchorific'
					}
				]
			}
		},
		push_svn: {
			options: {
				remove: true
			},
			main: {
				src: 'release/<%= pkg.name %>',
				dest: 'http://plugins.svn.wordpress.org/wp-anchorific',
				tmp: 'build/make_svn'
			}
		},
*/		makepot: {
		    target: {
		        options: {
		            domainPath: '/languages',
		            mainFile: 'wp-anchorific.php',
		            potFilename: 'wp-anchorific.pot',
		            potHeaders: {
		                poedit: true,
		                'x-poedit-keywordslist': true
		            },
		            bugsurl: '<%= pkg.bugs.url%>',
		            processPot: function( pot, options ) {
	                    pot.headers['report-msgid-bugs-to'] = options.bugsurl;
	                    /*pot.headers['language-team'] = 'Team Name <team@example.com>';*/
	                    return pot;
	                },
		            type: 'wp-plugin',
		            updateTimestamp: true,
		            exclude: [
		            	'node_modules/.*'
		            ],
		        }
		    }
		}, //makepot
	});



	//load modules
	// grunt.loadNpmTasks( 'grunt-glotpress' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-git' );
	grunt.loadNpmTasks( 'grunt-text-replace' );
	grunt.loadNpmTasks( 'grunt-svn-checkout' );
	grunt.loadNpmTasks( 'grunt-push-svn' );
	grunt.loadNpmTasks( 'grunt-remove' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );

	grunt.registerTask('syntax', 'default task description', function(){
	  console.log('Syntax:\n\tgrunt release');
	});

	//register default task
//	grunt.registerTask( 'default', [ 'glotpress_download' ]);
//	grunt.registerTask( 'default', ['syntax']);

// get the latest version of Shortcake

	grunt.registerTask( 'anchorific', ['gitclone:anchorific', 'copy:anchorific', 'clean:anchorific_tmp']);
// makepot
	grunt.registerTask('l10n', ['makepot']);
//release tasks
	grunt.registerTask( 'version_number', [ 'replace:reamde_md', 'replace:reamde_txt', 'replace:plugin_php' ] );
//	grunt.registerTask( 'pre_vcs', [ 'version_number', 'glotpress_download' ] );
	grunt.registerTask( 'pre_vcs', [ 'version_number'] );
//	grunt.registerTask( 'do_svn', [ 'svn_checkout', 'copy:svn_assets', 'copy:svn_trunk', 'copy:svn_tag', 'push_svn' ] );
	grunt.registerTask( 'do_git', [ 'gitcommit', 'gittag', 'gitpush' ] );

	grunt.registerTask( 'release', [ 'pre_vcs', 'do_svn', 'do_git', 'clean:post_build' ] );


};
module.exports = function(grunt) {

    var conf = {
        webDir      : "web/assets/",
        scssDir     : "scss/",
        cssDir      : "css/",
        jsDir       : "js/",
        imgDir      : "img/",
        jsFiles     : []
    };

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // Automatically run a task when a file changes
        watch: {
            options: {
                livereload: true,
            },
            css: {
                files: ["**/*.scss"],
                tasks: ['cssroutine'],
                options: {
                    cwd: conf.webDir+conf.scssDir,
                },
            },
            js: {
                files: ["**/*.js", "!**/*.min.js"],
                tasks: ['jsroutine'],
                options: {
                    cwd: conf.webDir+conf.jsDir,
                },
            }
        },

        //Compile specified SASS files
        sass: {
            css: {
                files: [{
                    expand: true,
                    cwd: conf.webDir+conf.scssDir,
                    src: ['app.scss'],
                    dest: conf.webDir+conf.cssDir,
                    ext: '.css'
                }]
            },
        },

        // Compress generated css files
        cssmin: {
            minify: {
                expand: true,
                cwd: conf.webDir+conf.cssDir,
                src: ['app.css', '!app.min.css'],
                dest: conf.webDir+conf.cssDir,
                ext: '.min.css'
            }
        },

        //Prefix CSS3 properties
        autoprefixer: {
            no_dest: {
                src: conf.webDir+conf.cssDir+'app.css' // globbing is also possible here
            },
        },

        // UglifyJS
        uglify: {
            minify_all: {
                files: [{
                    expand: true,
                    cwd: conf.webDir+conf.jsDir,
                    src: ['**/*.js', '!**/*.min.js', '!**/*.mix.js'],
                    dest: conf.webDir+conf.jsDir,
                    ext: '.min.js'
                }],
            },
        },

        // Script concatenation
        concat: {
            dist: {
                src: conf.jsFiles,
                dest: conf.webDir+conf.jsDir+'scripts.min.js'
            },
        },

    });


    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-newer');


    grunt.registerTask('cssroutine', ['sass:css', 'newer:autoprefixer', 'newer:cssmin']);
    grunt.registerTask('jsroutine', ['newer:uglify']);
};
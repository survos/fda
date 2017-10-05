module.exports = function (grunt) {


    grunt.initConfig({

        compass: {
            dist: {
                options: {
                    sassDir: 'src/AppBundle/Resources/scss',
                    cssDir: 'web/css',
                    outputStyle: 'compressed'
                }
            },
            dev: {
                options: {
                    sassDir: 'src/AppBundle/Resources/scss',
                    cssDir: 'web/css',
                    outputStyle: 'expanded',
                    watch: false
                }
            },
            devpoll: {
                options: {
                    sassDir: 'src/AppBundle/Resources/scss',
                    cssDir: 'web/css',
                    outputStyle: 'expanded',
                    watch: true
                }
            }
        },

        bower: {
            install: {

                options: {
                    layout: 'byComponent',
                    targetDir: './web/components',
                    verbose: false
                }
            }

        },

        "git-describe": {
            options: {},
            version: {}
        },


        'sf2-console': {
            options: {
                bin: 'app/console'
            },
            schema: {
                cmd: 'doctrine:schema:update',
                args: {
                    force: true
                }
            }
        },
        shell: {
            options: {
                stderr: true
            },
            "import": {
                command: 'app/console fda:import-raw --limit 0'
            },
            "import-reset": {
                command: 'app/console fda:import-raw --limit 0 --purge'
            },
            "import-reset-geocode": {
                command: 'app/console fda:import-raw --limit 0 --purge --geocoding google'
            }
        },

        composer: {
            options: {},
            fda:{}
        },

        gitpull: {
            dev: {
                options: {}
            }
        }
    });

    require('load-grunt-tasks')(grunt);


    grunt.registerTask('create-staging-config', function () {
        var config = {
            "parameters": {
                "database_name": "jenkins_fda",
                "database_host": "pg1.survos.com",
                "database_user": "jenkins",
                "database_password": "6mmH^%K8@1!K"
            }
        };
        YAML = require('yamljs');
        grunt.file.write('app/config/parameters.yml', YAML.stringify(config));

    });

    grunt.registerTask('update-revision', function () {
        grunt.event.once('git-describe', function (rev) {
            var conf = grunt.file.readYAML('app/config/parameters.yml');
            YAML = require('yamljs');
            var ver = rev.tag;
            if (rev.since) {
                ver = ver + '-' + rev.object;
            }
            conf['parameters']['app.version'] = ver;
            grunt.file.write('app/config/parameters.yml', YAML.stringify(conf));
        });
        grunt.task.run('git-describe');
    });

    grunt.registerTask('import-inspections', ['shell:import']);
    grunt.registerTask('reload-inspections', ['shell:import-reset']);
    grunt.registerTask('reload-inspections-geocode', ['shell:import-reset-geocode']);

    grunt.registerTask('default', ['bower:install', 'composer:fda:install', 'sf2-console:schema', 'compass:dev', 'update-revision']);
    grunt.registerTask('styles', ['compass:devpoll']);
    grunt.registerTask('staging', ['create-staging-config', 'update-revision', 'composer:fda:install', 'bower:install', 'sf2-console:schema']);

};

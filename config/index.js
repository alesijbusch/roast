const chokidar = require('chokidar');
const glob = require('glob');
const path = require('path');
const fs = require('fs');
const fsPromises = require('fs').promises;
const { pugBuilder } = require('./buildPug');
const { criticalBuild } = require('./criticalPreBuild');
const { buildJs } = require('./buildJS.js');
const { ajaxPugBuilder } = require('./buildAjaxPug');
const { styleBuilder } = require('./buildStyle');
const { copyPlugin } = require('./copyPlugin');
const portChecker = require('./portChecker.js');
const dependenciesTree = require('./pugDependencies');
const { exec } = require('child_process');
var browserSync = require("browser-sync");
const { clearLocal, lightMode } = require('../options');


const smpMode = false;

class SLAMBOX {

    constructor() {
        SLAMBOX.port = 3000;
        this.init();
        this.watcher();
    }


    async init() {

        const date = Date.now();

        const initPromises = [];


        initPromises.push(await this.getPort());
        initPromises.push(await this.rimrafLocal());
        initPromises.push(await copyPlugin({ vendor: true, jquery: true, images: true, fonts: true, favicons: true }));
        initPromises.push(this.importMixins());
        initPromises.push(this.buildMainJs());
        initPromises.push(await this.buildStyle());
        initPromises.push(await this.buildComponentsJS());
        initPromises.push(await this.buildCritical());

        Promise.all(initPromises)
            .then(() => {
                console.log(Date.now() - date, 'ms')
            })
    }

    watcher() {
        this.mixinWatch();
        this.ajaxWatch();
        this.imageWatch();
        this.spriteWatch();
        this.styleWatch();
        this.criticalWatch();
        this.jsWatch();
        if (!lightMode) {
            this.dependenciesTreeUpdate();
        }

        exec('npm run build-sprites');
    }


    async getPort() {
        return new Promise(async(resolve, reject) => {
            SLAMBOX.port = await portChecker(SLAMBOX.port, 'localhost');
            resolve();
        })
    }

    mixinWatch() {
        const importMixins = this.importMixins.bind(this);
        (function() {
            const watcher = chokidar.watch('src/**/mixin__*.pug', {
                persistent: true
            });
            let isReady = false
            watcher.on('add', path => {
                if (!isReady) return;
                importMixins();
            })
            watcher.on('unlink', path => {
                if (!isReady) return;
                importMixins();
            })
            watcher.on('ready', () => isReady = true);
        })();
    }

    async rimrafLocal() {
        if (!clearLocal) return;
        return fsPromises.rmdir(path.resolve(__dirname, '../html'), { recursive: true })
    }

    async getTreePugDependencies() {
        return await dependenciesTree();
    }

    dependenciesTreeUpdate() {
        const getTreePugDependencies = this.getTreePugDependencies.bind(this);
        (function() {
            const watcher = chokidar.watch('src/pages/**/*.pug', { persistent: true });
            let isReady = false;
            watcher.on('unlink', async() => {
                if (!isReady) return;
                try {
                    SLAMBOX.dependencies = await getTreePugDependencies();
                } catch (e) {
                    console.log(e);
                }
            })
            watcher.on('ready', () => isReady = true);
        })();
    }


    async buildStyle() {

        return new Promise((resolve, reject) => {
            const date = Date.now();

            let promises = [];
            glob("src/components/**/style.scss", function(er, files) {
                files.forEach(async function(fileName) {
                    // console.log()
                    promises.push(styleBuilder({
                        fileName,
                        init: true,
                        smpMode
                    }));
                })
                Promise.all(promises)
                    .then(() => {
                        resolve();
                        // console.log(Date.now() - date)
                    })
            });
        })
    }

    server() {

        const _this = this;

        browserSync({
            server: "./",
            host: 'localhost',
            port: SLAMBOX.port,
            startPath: "/html/index.html",
            files: [
                "./html/components-template/**/style.css",
                "./html/*.html",
                "./html/critical.css",
                "./html/components-template/**/script.min.js",
                "./html/js/app.min.js",
                "./html/images/",
            ],
            middleware: [
                function(req, res, next) {
                    if (lightMode) {
                        if (req.originalUrl.match(/(?<=\/html\/).*(?=.*\.html)/i) && !req.originalUrl.includes('ajax')) {
                            _this.currentPage = req.originalUrl.match(/(?<=\/html\/).*(?=.*\.html)/i)[0];
                        }
                    };
                    next();
                },
            ]
        });



    }

    pugWatch() {

        const _this = this;

        const getPagesForRebuild = (path) => {

            if (_this.currentPage) {
                return [`./src/pages/${_this.currentPage}/${_this.currentPage}.pug`]
            }

            const arr = [];
            const pagePath = path.split('\\').join('/');


            if (!SLAMBOX.dependencies.length) return undefined;

            SLAMBOX.dependencies.forEach((item) => {
                if (item.deps.includes(path.normalize())) {
                    arr.push(item.path);
                }
                if (item.path.includes(pagePath)) {
                    arr.push(item.path);
                }
            })

            return arr;
        }

        (function() {
            const watcher = chokidar.watch(['src/components/**/*.pug', 'src/layout/**/*.pug', 'src/mixins/**/*.pug', 'src/pages/**/*.pug'], { persistent: true });
            let isReady = false;

            watcher.on('change', path => {
                if (!isReady || _this.pugCooldown) return;
                if (path.includes('ajax')) return;
                _this.pugCooldown = true;
                const pages = getPagesForRebuild(path);
                console.log(pages);
                try {
                    pugBuilder({
                        smpMode,
                        pages,
                        callback: () => _this.pugCooldown = false
                    });
                } catch (e) {
                    console.log(e);
                }

            })
            watcher.on('ready', () => isReady = true);
        })();
    }

    criticalWatch() {
        let cooldown = false;
        const _this = this;
        (function() {
            const watcher = chokidar.watch(['src/components/**/critical.scss', 'src/assets/**/*.scss', 'src/mixins/**/*.scss'], { persistent: true });
            let isReady = false
            watcher.on('change', () => {
                if (!isReady || cooldown) return;
                cooldown = true;
                let pages = undefined;
                if (_this.currentPage) {
                    pages = [`./src/pages/${_this.currentPage}/${_this.currentPage}.pug`];
                }
                criticalBuild({
                    smpMode,
                    // callback: () => pugBuilder({
                    // 	pages,
                    // 	smpMode,
                    // })
                });
                setTimeout(() => cooldown = false, 600)
            })
            watcher.on('ready', () => isReady = true);
        })();
    }

    async buildCritical() {
        return new Promise((resolve, reject) => {
            criticalBuild({
                smpMode,
                callback: async() => {
                    if (!lightMode) {
                        SLAMBOX.dependencies = await this.getTreePugDependencies();
                    }
                    this.pugWatch()
                    pugBuilder({
                        smpMode,
                        callback: () => {
                            this.server();
                            resolve();
                        }
                    });
                }
            });
        })

    }

    resetCooldown() {
        this.pugCooldown = true;
        setTimeout(() => {
            this.pugCooldown = false
        }, this.aggregateTimeout)
    }

    importMixins() {

        return new Promise((resolve, reject) => {
            fs.writeFileSync(path.resolve(__dirname, `../src/webpack_lists/mixins.pug`), '');
            glob("src/**/mixin__*.pug", function(er, files) {
                files.map(function(fileName) {
                    let dirFilename = fileName.replace('src/', '');
                    fs.appendFileSync(`src/webpack_lists/mixins.pug`, 'include ../' + dirFilename + '\n');
                })
                resolve();
            });
        })



    }

    ajaxWatch() {
        ajaxPugBuilder({
            smpMode,
            init: true
        });
        (function() {
            const watcher = chokidar.watch('src/components/**/ajax__*.pug', { persistent: true });
            let isReady = false
            watcher.on('change', () => {
                if (!isReady) return;
                ajaxPugBuilder({
                    smpMode
                });
            })
            watcher.on('ready', () => isReady = true);
        })();
    }

    styleWatch() {
        (function() {
            const watcher = chokidar.watch('src/components/**/style.scss', { persistent: true });
            let isReady = false;

            watcher.on('change', path => {
                if (!isReady) return;
                try {
                    styleBuilder({
                        fileName: path
                    });
                } catch (e) {
                    console.log(e);
                }

            })
            watcher.on('ready', () => isReady = true);
        })();
    }

    spriteWatch() {
        (function() {
            const watcher = chokidar.watch('src/assets/sprites/svg/*.svg', { persistent: true });
            let isReady = false;
            watcher.on('add', path => {
                if (!isReady) return;
                try {
                    exec('npm run build-sprites');
                } catch (e) {
                    console.log(e);
                }

            })
            watcher.on('ready', () => isReady = true);
        })();
    }

    buildMainJs() {
        return buildJs({
            input: path.resolve(__dirname, '../src/assets/js/main.js'),
            output: path.resolve(__dirname, '../html/js/app.min.js'),
            init: true
        });
    }

    buildComponentsJS() {
        return new Promise((resolve, reject) => {
            const promises = [];
            glob("src/components/**/script.js", function(er, files) {
                files.forEach(src => {
                    const input = path.resolve(__dirname, '../', src);
                    const output = path.resolve(__dirname, '../html/components-template/', src.replace('src/components/', '').replace('script.js', 'script.min.js'));
                    promises.push(
                        buildJs({
                            input,
                            output,
                            init: true,
                        })
                    )
                    Promise.all(promises)
                        .then(() => {
                            resolve();
                        })
                })
            });
        })
    }

    jsWatch() {
        let cooldown = false;
        (function() {
            const watcher = chokidar.watch('src/components/**/script.js', { persistent: true });
            let isReady = false
            watcher.on('change', link => {
                if (!isReady || cooldown) return;
                cooldown = true;
                link = link.split('\\').join('/')
                const input = path.resolve(__dirname, `../${link}`);
                const output = path.resolve(__dirname, `../html/components-template/${link.replace('src/components/', '').replace('script.js', 'script.min.js')}`);
                buildJs({
                    input,
                    output
                })
                setTimeout(() => cooldown = false, 600)
            })
            watcher.on('ready', () => isReady = true);
        })();
        (function() {
            const watcher = chokidar.watch(['src/assets/**/*.js', 'src/mixins/**/script.js'], { persistent: true });
            let isReady = false
            watcher.on('change', link => {
                if (!isReady) return;
                link = link.split('\\').join('/')
                buildJs({
                    input: path.resolve(__dirname, '../src/assets/js/main.js'),
                    output: path.resolve(__dirname, '../html/js/app.min.js')
                });
            })
            watcher.on('ready', () => isReady = true);
        })();
    }

    imageWatch() {
        (function() {
            const watcher = chokidar.watch('src/assets/images/**/*.(jpg|png|svg)', { persistent: true });
            let isReady = false
            watcher.on('change', async() => {
                if (!isReady) return;
                console.log('\x1b[31m', '====================\nimages updating....\n ====================', '\x1b[0m');
                await copyPlugin({
                    images: true
                });
                console.log('\x1b[32m', '====================\nimage updated\n ====================', '\x1b[0m');
            })
            watcher.on('add', async() => {
                if (!isReady) return;
                console.log('\x1b[31m', '====================\nimages updating....\n ====================', '\x1b[0m');
                await copyPlugin({
                    images: true
                });
                console.log('\x1b[32m', '====================\nimage updated\n ====================', '\x1b[0m');
            })
            watcher.on('ready', () => isReady = true);
        })();
    }

    spriteWatcher() {
        const watcher = chokidar.watch('src/assets/sprites/svg/*.svg', { persistent: true });
        let isReady = false
        watcher.on('add', () => {
            if (!isReady) return;
            fs.appendFileSync(`src/webpack_lists/sprites.js`, ' ');
        })
        watcher.on('ready', () => isReady = true);
    }

}

new SLAMBOX();
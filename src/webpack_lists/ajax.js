function importAll(contextLoader) {
  contextLoader.keys().forEach(id => console.log(contextLoader(id)));
}

const contextLoader = require.context('../components/', true, /.*\/ajax__.*\.pug/);
importAll(contextLoader);
                                                                                                    
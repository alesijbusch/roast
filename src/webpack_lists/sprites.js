function importAll(contextLoader) {
	contextLoader.keys().forEach(id => console.log(contextLoader(id)));
}

const contextLoader = require.context('../assets/sprites/svg', true, /\.svg/);
importAll(contextLoader);
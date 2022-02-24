module.exports = {
  transpileDependencies: [
    'vuetify',
  ],
  css: {
    loaderOptions: {
      sass: {
        implementation: require('sass'),
        fiber: require('fibers'),
      },
    },
  },
};

var app = new Vue({
  el: '#app',
  vuetify: new Vuetify(),
  data: {
    isSource: 0,
    page: 1,
    onPage: 10,
    totalVisible: 8,
    totalMovies: null, // // loaded dynamically
    length: null, // loaded dynamically
    list: [] // loaded dynamically
  },
  mounted: function () {
    this.moviesShow();
  },
  computed: {
    pagination: function () {
      if (this.isSource) {
        return this.moviesParse();
      } else {
        return this.moviesShow();
      }
    }
  },
  methods: {
    moviesShow: function (page = 0) {

      axios({
        method: 'post',
        url: 'index.php',
        data: JSON.stringify({
          api: 'movies/get',
          page: (page) ? page : this.page,
          onPage: this.onPage
        })
      })
        .then(function (response) {
          let movies = response.data;
          app.page = (page) ? page : app.page;
          app.isSource = 0;
          app.list = movies.list;
          app.totalMovies = movies.total;
          // change navigation buttons length
          app.length = Math.ceil(app.totalMovies / app.onPage);
        })
    },
    moviesParse: function (reload = 0) {

      axios({
        method: 'post',
        url: 'index.php',
        data: JSON.stringify({
          api: 'movies/parse',
          page: this.page,
          reload: reload,
          onPage: this.onPage
        })
      })
        .then(function (response) {
          let movies = response.data;
          app.isSource = 1;
          app.list = movies.list;
          app.totalMovies = movies.total;
          // change navigation buttons length
          app.length = Math.ceil(app.totalMovies / app.onPage);
        })
    },
    moviesImport: function () {
      axios({
        method: 'post',
        url: 'index.php',
        data: JSON.stringify({
          api: 'movies/import',
          page: this.page,
          onPage: this.onPage
        })
      })
        .then(function (response) {
          app.moviesShow(1);

        })
    }
  }
});
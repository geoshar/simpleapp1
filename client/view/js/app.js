var app = new Vue({
  el: '#app',
  vuetify: new Vuetify(),
  data: {
    isSource: 0,
    overlay: 0,
    page: 1,
    onPage: 10,
    totalVisible: 8,
    blocks: [{
      title: 'Фильмы с фото',
      havePic: 1,
      page: 1,
      length: 0,
      list: [],
      total: 0
    }, {
      title: 'Фильмы без фото',
      havePic: 0,
      page: 1,
      length: 0,
      list: [],
      total: 0

    }]
  },
  mounted: function () {
    this.moviesShowAll();
  },
  watch: {
    overlay (val) {
      val && setTimeout(() => {
        this.overlay = false
      }, 3000)
    },
  },
  methods: {
    moviesShowAll: function (sourceType = 'db') {

      this.isSource = (sourceType == 'source') ? 1 : 0;

      let this_ = this;
      this.blocks.forEach(function (block, index) {
        this_.moviesShow(index, this_.isSource ? 1 : 0);
      });
    },
    moviesShow: function (blockIndex, reload = 0) {
      this.overlay = 1;
      let block = this.blocks[blockIndex];
      let data = {
        api: 'movies/get',
        page: block.page,
        onPage: this.onPage,
        havePic: block.havePic
      };
      let callback = function (response) {
        app.overlay = 0;
        let result = response.data;
        block.total = result.total;
        block.length = Math.ceil(result.total / app.onPage);
        block.list = result.list;
      };
      // if loaded from source, change some parameters to send
      if (this.isSource) {
        data.api = 'movies/parse';
        // if set to 0, get the data from a session storage,
        // if set to 1 parse new data from source and rewrite session storage
        data.reload = reload;
      }
      // make post request to the server
      axios({
        method: 'post',
        url: 'index.php',
        data: JSON.stringify(data)
      }).then(callback);

    },
    moviesImport: function () {
      this.overlay = 1;
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
          this.overlay = 0;
          app.isSource = 0;
          app.moviesShowAll();
        })
    }
  }
});
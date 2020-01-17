var app = new Vue({
  el: '#app',
  data() {
    return {
      title: 'Illustrators',
      illustrators: [],
      filters: [],
      form: {
        name: '',
        website: '',
        tags: '',
      },
      images: {},
      viewing: [],
      editing: [],
      updates: {}
    };
  },

  computed: {
    allTags() {
      return this.illustrators
        .map((illustrator) => illustrator.tags)
        .reduce((carry, tags) => {
          tags.slice(0).forEach((tag) => {
            if (!carry.includes(tag)) {
              carry.push(tag);
            }
          });
          return carry;
        }, [])
        .sort();
    },
    allIllustrators() {
      if (this.filters.length) {
        return this.illustrators.filter((illustrator) => {
          return illustrator.tags.filter((filter) => {
            return this.filters.indexOf(filter) >= 0;
          }).length;
        });
      }
      return this.illustrators;
    },
  },

  methods: {
    setFilter(filter) {
      this.filter = filter;
    },

    addIllustrator() {
      if (this.form.name) {
        fetch('api.php', {
          method: 'POST',
          body: JSON.stringify({
            action: 'addIllustrator',
            data: this.form
          })
        }).then((res) => {
          return res.json();
        }).then((data) => {
          const name = this.form.name.trim()
          this.illustrators = data.illustrators;

          const i = this.illustrators.findIndex((ill) =>  ill.name === name);
          const id = this.illustrators[i].id;
          this.toggleViewMode(id, i);

          this.form.name = '';
          this.form.website = '';
          this.form.tags = '';
        });
      }
    },

    updateIllustrator(id) {
      if (id) {
        let illustrator = this.illustrators.find((illustrator) => illustrator.id == id);
        illustrator = Object.assign({}, illustrator, this.updates[id])

        fetch('api.php', {
          method: 'POST',
          body: JSON.stringify({
            action: 'updateIllustrator',
            data: illustrator
          })
        }).then((res) => {
          return res.json();
        }).then((data) => {
          this.illustrators = data.illustrators;
          this.removeEmptyFilters();
          this.toggleEditMode(illustrator.id);
        });
      }
    },

    removeIllustrator(id) {
      if (id) {
        fetch('api.php', {
          method: 'POST',
          body: JSON.stringify({
            action: 'removeIllustrator',
            data: id
          })
        }).then((res) => {
          return res.json();
        }).then((data) => {
          this.illustrators = data.illustrators;
        });
      }
    },

    addImage(id) {
      let input = this.$refs[`imageURL_${id}`][0];

      fetch('api.php', {
        method: 'POST',
        body: JSON.stringify({
          action: 'addIllustratorImage',
          data: {
            id: id,
            image: input.value
          }
        })
      }).then((res) => {
        return res.json();
      }).then((data) => {
        this.illustrators = data.illustrators;
        input.value = '';
      });
    },

    removeImage(id, imageIdx) {
      fetch('api.php', {
        method: 'POST',
        body: JSON.stringify({
          action: 'removeIllustratorImage',
          data: {
            id: id,
            imageIdx: imageIdx
          }
        })
      }).then((res) => {
        return res.json();
      }).then((data) => {
        this.illustrators = data.illustrators;
      });
    },

    setThumb(id, imageIdx) {
      fetch('api.php', {
        method: 'POST',
        body: JSON.stringify({
          action: 'setIllustratorThumb',
          data: {
            id: id,
            imageIdx: imageIdx
          }
        })
      }).then((res) => {
        return res.json();
      }).then((data) => {
        this.illustrators = data.illustrators;
      });
    },

    removeEmptyFilters() {
      this.filters.forEach((tag) => {
        const exists = this.allTags.find((t) => t == tag)
        if (!exists) {
          this.toggleFilter(tag)
        }
      })
    },

    toggleFilter(tag) {
      if (!this.filters.includes(tag)) {
        this.filters.push(tag);
      } else {
        this.filters.splice(this.filters.indexOf(tag), 1);
      }
    },

    toggleEditMode(id) {
      if (!this.editing.includes(id)) {
        const illustrator = this.illustrators.find(
          illustrator => illustrator.id == id
        );

        this.editing.push(id);
        this.updates[id] = Object.assign({}, illustrator);
        this.updates[id].tags = illustrator.tags.join(', ');
      } else {
        this.editing.splice(this.editing.indexOf(id), 1);
        delete this.updates[id];
      }
    },

    toggleViewMode(id, i) {
      if (!this.viewing.includes(id)) {
        this.viewing.push(id);
        setTimeout(() => {
          if (typeof this.$refs.illustrators[i] !== 'undefined') {
            window.scrollTo({
              top: this.$refs.illustrators[i].offsetTop,
              left: 0,
              behavior: 'smooth'
            });
          }
        }, 200);
      } else {
        this.viewing.splice(this.viewing.indexOf(id), 1);
      }
    },
  },

  mounted() {
    fetch('storage/data.json')
      .then((res) => {
        return res.json();
      }).then((data) => {
        this.illustrators = data.illustrators;
      });
  }
});

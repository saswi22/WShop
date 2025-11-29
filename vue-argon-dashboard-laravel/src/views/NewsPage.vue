<template>
  <div class="news-page">
    <div class="container py-5">
      <h1 class="mb-5">Berita & Artikel</h1>
      
      <!-- Search -->
      <div class="row mb-4">
        <div class="col-md-6">
          <input 
            v-model="searchQuery" 
            type="text" 
            class="form-control" 
            placeholder="Cari berita..."
          >
        </div>
      </div>

      <!-- News List -->
      <div v-if="filteredNews.length" class="row">
        <div class="col-lg-8 mb-4" v-for="item in filteredNews" :key="item.id">
          <div class="card h-100 shadow-sm news-card">
            <div class="row g-0">
              <div class="col-md-4" v-if="item.featured_image">
                <img :src="item.featured_image" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="News">
              </div>
              <div :class="item.featured_image ? 'col-md-8' : 'col-12'">
                <div class="card-body">
                  <h5 class="card-title">{{ item.title }}</h5>
                  <p class="card-text">{{ item.excerpt }}</p>
                  <small class="text-muted">
                    {{ formatDate(item.published_at) }}
                    <span v-if="item.category" class="ms-2">
                      <span class="badge bg-primary">{{ item.category }}</span>
                    </span>
                  </small>
                  <br>
                  <router-link :to="`/news/${item.id}`" class="btn btn-primary btn-sm mt-3">
                    Baca Selengkapnya
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="alert alert-info text-center">
        <p>Tidak ada berita yang ditemukan</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NewsPage',
  data() {
    return {
      news: [],
      searchQuery: ''
    }
  },
  computed: {
    filteredNews() {
      return this.news.filter(item => 
        item.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        (item.category && item.category.toLowerCase().includes(this.searchQuery.toLowerCase()))
      );
    }
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    async fetchNews() {
      try {
        const response = await this.$axios.get('/api/v2/news');
        this.news = response.data.data;
      } catch (error) {
        console.error('Error fetching news:', error);
      }
    }
  },
  mounted() {
    this.fetchNews();
  }
}
</script>

<style scoped>
.news-card {
  transition: all 0.3s ease;
  border: none;
}

.news-card:hover {
  box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
  transform: translateY(-5px);
}
</style>

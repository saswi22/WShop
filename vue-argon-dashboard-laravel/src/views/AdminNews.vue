<template>
  <div class="admin-news">
    <h2 class="mb-4">Manajemen Berita</h2>

    <button @click="showForm = !showForm" class="btn btn-primary mb-3">
      <span v-if="!showForm">+ Tambah Berita</span>
      <span v-else>Batal</span>
    </button>

    <!-- Form Tambah/Edit -->
    <div v-if="showForm" class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">{{ editingId ? 'Edit Berita' : 'Tambah Berita Baru' }}</h5>
        <form @submit.prevent="saveNews">
          <div class="mb-3">
            <label class="form-label">Judul *</label>
            <input v-model="form.title" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Slug *</label>
            <input v-model="form.slug" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Konten *</label>
            <textarea v-model="form.content" class="form-control" rows="5" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Ringkasan</label>
            <textarea v-model="form.excerpt" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input v-model="form.category" type="text" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Gambar Utama</label>
            <input type="file" @change="handleFileUpload" accept="image/*" class="form-control" ref="imageInput">
            <small v-if="form.featured_image" class="text-muted d-block mt-1">
              File terpilih: {{ form.featured_image.split('/').pop() }}
            </small>
            <small v-if="uploadingImage" class="text-info d-block mt-1">Mengupload gambar...</small>
          </div>
          <div class="mb-3">
            <label class="form-check-label">
              <input v-model="form.is_published" type="checkbox" class="form-check-input">
              Publikasikan
            </label>
          </div>
          <button type="submit" class="btn btn-success" :disabled="uploadingImage">Simpan</button>
        </form>
      </div>
    </div>

    <!-- News List -->
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in news" :key="item.id">
            <td>{{ item.title }}</td>
            <td>{{ item.category }}</td>
            <td>
              <span v-if="item.is_published" class="badge bg-success">Dipublikasi</span>
              <span v-else class="badge bg-warning">Draft</span>
            </td>
            <td>{{ formatDate(item.published_at) }}</td>
            <td>
              <button @click="editNews(item)" class="btn btn-warning btn-sm me-2">Edit</button>
              <button @click="deleteNews(item.id)" class="btn btn-danger btn-sm">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="news.length === 0" class="alert alert-info text-center">
      Tidak ada berita
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminNews',
  data() {
    return {
      news: [],
      showForm: false,
      editingId: null,
      uploadingImage: false,
      form: {
        title: '',
        slug: '',
        content: '',
        excerpt: '',
        category: '',
        featured_image: '',
        is_published: true
      }
    }
  },
  methods: {
    async handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      this.uploadingImage = true;
      const formData = new FormData();
      formData.append('image', file);

      try {
        const response = await this.$axios.post('/api/v2/upload', formData);
        
        if (response.data.success) {
          this.form.featured_image = response.data.url;
          alert('Gambar berhasil diupload!');
        }
      } catch (error) {
        alert('Gagal mengupload gambar: ' + (error.response?.data?.message || error.message));
        this.$refs.imageInput.value = '';
      } finally {
        this.uploadingImage = false;
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('id-ID');
    },
    async fetchNews() {
      try {
        const response = await this.$axios.get('/api/v2/news');
        this.news = response.data.data;
      } catch (error) {
        console.error('Error fetching news:', error);
      }
    },
    resetForm() {
      this.form = {
        title: '',
        slug: '',
        content: '',
        excerpt: '',
        category: '',
        featured_image: '',
        is_published: true
      };
      this.editingId = null;
      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = '';
      }
    },
    editNews(item) {
      this.form = { ...item };
      this.editingId = item.id;
      this.showForm = true;
    },
    async saveNews() {
      try {
        if (this.editingId) {
          await this.$axios.put(`/api/v2/news/${this.editingId}`, this.form);
          alert('Berita berhasil diupdate!');
        } else {
          await this.$axios.post('/api/v2/news', this.form);
          alert('Berita berhasil ditambahkan!');
        }
        this.resetForm();
        this.showForm = false;
        this.fetchNews();
      } catch (error) {
        alert('Gagal menyimpan berita: ' + error.message);
      }
    },
    async deleteNews(id) {
      if (confirm('Anda yakin ingin menghapus berita ini?')) {
        try {
          await this.$axios.delete(`/api/v2/news/${id}`);
          alert('Berita berhasil dihapus!');
          this.fetchNews();
        } catch (error) {
          alert('Gagal menghapus berita: ' + error.message);
        }
      }
    }
  },
  mounted() {
    this.fetchNews();
  }
}
</script>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}
</style>

<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Halaman</h2>
      <div class="d-flex gap-2">
        <router-link class="btn btn-primary" to="/admin/pages/new">+ Halaman Baru</router-link>
        <button class="btn btn-outline-secondary" @click="quickEditTentangKami">
          📄 Edit Tentang Kami
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Judul</th>
            <th>Slug</th>
            <th>Publish</th>
            <th>Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in pages" :key="p.id">
            <td>{{ p.title }}</td>
            <td>{{ p.slug }}</td>
            <td>
              <span :class="['badge', p.is_published ? 'bg-success' : 'bg-secondary']">{{ p.is_published ? 'Yes' : 'No' }}</span>
            </td>
            <td>{{ new Date(p.created_at).toLocaleString() }}</td>
            <td>
              <router-link class="btn btn-sm btn-warning me-2" :to="`/admin/pages/${p.id}`">Edit</router-link>
              <button class="btn btn-sm btn-danger" @click="remove(p.id)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminPages',
  data() {
    return { pages: [] }
  },
  methods: {
    async load() {
      try {
        const res = await this.$axios.get('/api/v2/pages/all');
        this.pages = Array.isArray(res.data.data) ? res.data.data : [];
        
        // Fallback to public pages if admin list is empty (e.g., token issue or no data)
        if (!this.pages.length) {
          const pub = await this.$axios.get('/api/v2/pages');
          this.pages = Array.isArray(pub.data.data) ? pub.data.data : [];
        }
      } catch (error) {
        // Fallback on any error
        try {
          const pub = await this.$axios.get('/api/v2/pages');
          this.pages = Array.isArray(pub.data.data) ? pub.data.data : [];
        } catch (err) {
          this.pages = [];
        }
      }
    },
    async quickEditTentangKami() {
      try {
        // Try fetch by slug
        const res = await this.$axios.get('/api/v2/pages/tentang-kami');
        const page = res.data.data;
        this.$router.push(`/admin/pages/${page.id}`);
      } catch (e) {
        // If not found, create and open
        try {
          const createRes = await this.$axios.post('/api/v2/pages', {
            title: 'Tentang Kami',
            slug: 'tentang-kami',
            content: '<h2>Tentang Kami</h2><p>Silakan isi profil, sejarah, visi dan misi di sini.</p>',
            is_published: true
          });
          const page = createRes.data.data;
          this.$router.push(`/admin/pages/${page.id}`);
        } catch (err) {
          alert('Gagal membuka/menambahkan halaman Tentang Kami: ' + (err.response?.data?.message || err.message));
        }
      }
    },
    async remove(id) {
      if (!confirm('Hapus halaman ini?')) return;
      await this.$axios.delete(`/api/v2/pages/${id}`);
      this.load();
    }
  },
  mounted() { this.load(); }
}
</script>

<style scoped>
</style>

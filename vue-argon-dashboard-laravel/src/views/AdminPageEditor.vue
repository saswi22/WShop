<template>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title mb-3">{{ isEdit ? 'Edit Halaman' : 'Halaman Baru' }}</h5>

      <form @submit.prevent="save">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Judul</label>
            <input v-model="form.title" type="text" class="form-control" required />
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Slug</label>
            <input v-model="form.slug" type="text" class="form-control" required />
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Featured Image</label>
            <input type="file" @change="handleFileUpload" accept="image/*" class="form-control" ref="imageInput">
            <small v-if="form.featured_image" class="text-muted d-block mt-1">
              File terpilih: {{ form.featured_image.split('/').pop() }}
            </small>
            <small v-if="uploadingImage" class="text-info d-block mt-1">Mengupload gambar...</small>
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Konten (HTML diperbolehkan)</label>
            <textarea v-model="form.content" class="form-control" rows="10"></textarea>
          </div>
          <div class="col-md-12 mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="publish" v-model="form.is_published" />
              <label class="form-check-label" for="publish">Publish</label>
            </div>
          </div>
        </div>

        <button class="btn btn-success" :disabled="uploadingImage">Simpan</button>
        <router-link class="btn btn-secondary ms-2" to="/admin/pages">Batal</router-link>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminPageEditor',
  data() {
    return {
      form: {
        title: '', slug: '', content: '', featured_image: '', is_published: true
      },
      isEdit: false,
      uploadingImage: false,
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
    async load() {
      const id = this.$route.params.id;
      if (!id) return;
      this.isEdit = true;
      try {
        // Prefer direct fetch by id/slug to ensure full data
        const res = await this.$axios.get(`/api/v2/pages/${id}`);
        const page = res.data.data;
        if (page) {
          // Normalize fields expected by form
          this.form = {
            title: page.title || '',
            slug: page.slug || '',
            content: page.content || '',
            featured_image: page.featured_image || '',
            is_published: !!page.is_published,
          };
        }
      } catch (e) {
        // Fallback to list search if direct fetch fails
        try {
          const list = await this.$axios.get('/api/v2/pages/all');
          const page = list.data.data.find(p => String(p.id) === String(id));
          if (page) this.form = { ...page };
        } catch (err) {
          alert('Gagal memuat halaman: ' + (err.response?.data?.message || err.message));
        }
      }
    },
    async save() {
      const id = this.$route.params.id;
      if (this.isEdit) {
        await this.$axios.put(`/api/v2/pages/${id}`, this.form);
      } else {
        await this.$axios.post('/api/v2/pages', this.form);
      }
      this.$router.push('/admin/pages');
    }
  },
  mounted() { this.load(); }
}
</script>

<style scoped>
</style>

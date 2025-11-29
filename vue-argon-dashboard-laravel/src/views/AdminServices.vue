<template>
  <div class="admin-services">
    <h2 class="mb-4">Manajemen Layanan</h2>

    <button @click="showForm = !showForm" class="btn btn-primary mb-3">
      <span v-if="!showForm">+ Tambah Layanan</span>
      <span v-else>Batal</span>
    </button>

    <!-- Form Tambah/Edit -->
    <div v-if="showForm" class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">{{ editingId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}</h5>
        <form @submit.prevent="saveService">
          <div class="mb-3">
            <label class="form-label">Nama Layanan *</label>
            <input v-model="form.name" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea v-model="form.description" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Detail Layanan</label>
            <textarea v-model="form.details" class="form-control" rows="4"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Icon</label>
            <input type="file" @change="handleIconUpload" accept="image/*" class="form-control" ref="iconInput">
            <small v-if="form.icon" class="text-muted d-block mt-1">
              File terpilih: {{ form.icon.split('/').pop() }}
            </small>
            <small v-if="uploadingIcon" class="text-info d-block mt-1">Mengupload icon...</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Gambar</label>
            <input type="file" @change="handleImageUpload" accept="image/*" class="form-control" ref="imageInput">
            <small v-if="form.image" class="text-muted d-block mt-1">
              File terpilih: {{ form.image.split('/').pop() }}
            </small>
            <small v-if="uploadingImage" class="text-info d-block mt-1">Mengupload gambar...</small>
          </div>
          <button type="submit" class="btn btn-success" :disabled="uploadingIcon || uploadingImage">Simpan</button>
        </form>
      </div>
    </div>

    <!-- Services List -->
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="service in services" :key="service.id">
            <td>{{ service.name }}</td>
            <td>{{ service.description }}</td>
            <td>
              <span v-if="service.is_active" class="badge bg-success">Aktif</span>
              <span v-else class="badge bg-danger">Tidak Aktif</span>
            </td>
            <td>
              <button @click="editService(service)" class="btn btn-warning btn-sm me-2">Edit</button>
              <button @click="deleteService(service.id)" class="btn btn-danger btn-sm">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="services.length === 0" class="alert alert-info text-center">
      Tidak ada layanan
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminServices',
  data() {
    return {
      services: [],
      showForm: false,
      editingId: null,
      uploadingIcon: false,
      uploadingImage: false,
      form: {
        name: '',
        description: '',
        details: '',
        icon: '',
        image: ''
      }
    }
  },
  methods: {
    async handleIconUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      this.uploadingIcon = true;
      const formData = new FormData();
      formData.append('image', file);

      try {
        const response = await this.$axios.post('/api/v2/upload', formData);
        
        if (response.data.success) {
          this.form.icon = response.data.url;
          alert('Icon berhasil diupload!');
        }
      } catch (error) {
        alert('Gagal mengupload icon: ' + (error.response?.data?.message || error.message));
        this.$refs.iconInput.value = '';
      } finally {
        this.uploadingIcon = false;
      }
    },
    async handleImageUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      this.uploadingImage = true;
      const formData = new FormData();
      formData.append('image', file);

      try {
        const response = await this.$axios.post('/api/v2/upload', formData);
        
        if (response.data.success) {
          this.form.image = response.data.url;
          alert('Gambar berhasil diupload!');
        }
      } catch (error) {
        alert('Gagal mengupload gambar: ' + (error.response?.data?.message || error.message));
        this.$refs.imageInput.value = '';
      } finally {
        this.uploadingImage = false;
      }
    },
    async fetchServices() {
      try {
        const response = await this.$axios.get('/api/v2/services');
        this.services = response.data.data;
      } catch (error) {
        console.error('Error fetching services:', error);
      }
    },
    resetForm() {
      this.form = {
        name: '',
        description: '',
        details: '',
        icon: '',
        image: ''
      };
      this.editingId = null;
      if (this.$refs.iconInput) {
        this.$refs.iconInput.value = '';
      }
      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = '';
      }
    },
    editService(service) {
      this.form = { ...service };
      this.editingId = service.id;
      this.showForm = true;
    },
    async saveService() {
      try {
        if (this.editingId) {
          await this.$axios.put(`/api/v2/services/${this.editingId}`, this.form);
          alert('Layanan berhasil diupdate!');
        } else {
          await this.$axios.post('/api/v2/services', this.form);
          alert('Layanan berhasil ditambahkan!');
        }
        this.resetForm();
        this.showForm = false;
        this.fetchServices();
      } catch (error) {
        alert('Gagal menyimpan layanan: ' + error.message);
      }
    },
    async deleteService(id) {
      if (confirm('Anda yakin ingin menghapus layanan ini?')) {
        try {
          await this.$axios.delete(`/api/v2/services/${id}`);
          alert('Layanan berhasil dihapus!');
          this.fetchServices();
        } catch (error) {
          alert('Gagal menghapus layanan: ' + error.message);
        }
      }
    }
  },
  mounted() {
    this.fetchServices();
  }
}
</script>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}
</style>

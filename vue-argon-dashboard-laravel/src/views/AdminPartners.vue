<template>
  <div class="admin-partners">
    <h2 class="mb-4">Manajemen Mitra</h2>

    <button @click="showForm = !showForm" class="btn btn-primary mb-3">
      <span v-if="!showForm">+ Tambah Mitra</span>
      <span v-else>Batal</span>
    </button>

    <!-- Form Tambah/Edit -->
    <div v-if="showForm" class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">{{ editingId ? 'Edit Mitra' : 'Tambah Mitra Baru' }}</h5>
        <form @submit.prevent="savePartner">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Mitra *</label>
              <input v-model="form.name" type="text" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Website</label>
              <input v-model="form.website" type="url" class="form-control" placeholder="https://example.com">
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea v-model="form.description" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Logo *</label>
              <input type="file" @change="handleFileUpload" accept="image/*" class="form-control" ref="logoInput">
              <small v-if="form.logo" class="text-muted d-block mt-1">
                File: {{ form.logo.split('/').pop() }}
              </small>
              <small v-if="uploadingImage" class="text-info d-block mt-1">Mengupload logo...</small>
              <!-- Preview logo saat edit -->
              <div v-if="editingId && form.logo" class="mt-2">
                <img :src="form.logo" alt="Preview" style="max-width: 150px; max-height: 150px; object-fit: contain; background: #f5f5f5; padding: 10px;" class="rounded">
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Urutan</label>
              <input v-model.number="form.order" type="number" class="form-control" placeholder="0">
              <small class="text-muted">Urutan tampil (semakin kecil semakin awal)</small>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Status</label>
              <select v-model="form.is_active" class="form-select">
                <option :value="true">Aktif</option>
                <option :value="false">Tidak Aktif</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-success" :disabled="uploadingImage">Simpan</button>
        </form>
      </div>
    </div>

    <!-- Partners List -->
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th style="width: 80px;">Logo</th>
            <th>Nama</th>
            <th>Website</th>
            <th>Deskripsi</th>
            <th style="width: 80px;">Urutan</th>
            <th style="width: 100px;">Status</th>
            <th style="width: 150px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="partner in partners" :key="partner.id">
            <td>
              <img v-if="partner.logo" :src="partner.logo" :alt="partner.name" 
                   style="max-width: 60px; max-height: 40px; object-fit: contain;">
              <span v-else class="text-muted">-</span>
            </td>
            <td>{{ partner.name }}</td>
            <td>
              <a v-if="partner.website" :href="partner.website" target="_blank" class="text-primary">
                {{ partner.website.substring(0, 30) }}{{ partner.website.length > 30 ? '...' : '' }}
              </a>
              <span v-else class="text-muted">-</span>
            </td>
            <td>
              <span v-if="partner.description">
                {{ partner.description.substring(0, 50) }}{{ partner.description.length > 50 ? '...' : '' }}
              </span>
              <span v-else class="text-muted">-</span>
            </td>
            <td class="text-center">{{ partner.order || 0 }}</td>
            <td>
              <span v-if="partner.is_active" class="badge bg-success">Aktif</span>
              <span v-else class="badge bg-danger">Tidak Aktif</span>
            </td>
            <td>
              <button @click="editPartner(partner)" class="btn btn-warning btn-sm me-2">Edit</button>
              <button @click="deletePartner(partner.id)" class="btn btn-danger btn-sm">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!partners.length" class="text-center text-muted py-4">Belum ada mitra.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminPartners',
  data() {
    return {
      partners: [],
      showForm: false,
      editingId: null,
      uploadingImage: false,
      form: {
        name: '',
        logo: '',
        website: '',
        description: '',
        order: 0,
        is_active: true
      }
    }
  },
  mounted() {
    this.fetchPartners();
  },
  methods: {
    async handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Check if user is authenticated
      const token = localStorage.getItem('token');
      if (!token) {
        alert('Anda belum login. Silakan login terlebih dahulu.');
        this.$router.push('/admin/login');
        return;
      }

      this.uploadingImage = true;
      const formData = new FormData();
      formData.append('image', file);

      try {
        const response = await this.$axios.post('/api/v2/upload', formData);
        
        if (response.data.success) {
          this.form.logo = response.data.url;
          alert('Logo berhasil diupload!');
        }
      } catch (error) {
        if (error.response?.status === 401) {
          alert('Sesi login Anda telah berakhir. Silakan login kembali.');
          localStorage.removeItem('token');
          this.$router.push('/admin/login');
        } else {
          alert('Gagal mengupload logo: ' + (error.response?.data?.message || error.message));
        }
        // Reset file input
        this.$refs.logoInput.value = '';
      } finally {
        this.uploadingImage = false;
      }
    },
    async fetchPartners() {
      try {
        const response = await this.$axios.get('/api/v2/partners');
        this.partners = response.data.data;
      } catch (error) {
        alert('Gagal memuat data mitra');
      }
    },
    resetForm() {
      this.form = {
        name: '',
        logo: '',
        website: '',
        description: '',
        order: 0,
        is_active: true
      };
      this.editingId = null;
      // Reset file input
      if (this.$refs.logoInput) {
        this.$refs.logoInput.value = '';
      }
    },
    editPartner(partner) {
      this.form = { ...partner };
      this.editingId = partner.id;
      this.showForm = true;
    },
    async savePartner() {
      try {
        if (this.editingId) {
          await this.$axios.put(`/api/v2/partners/${this.editingId}`, this.form);
          alert('Mitra berhasil diupdate!');
        } else {
          await this.$axios.post('/api/v2/partners', this.form);
          alert('Mitra berhasil ditambahkan!');
        }
        this.resetForm();
        this.showForm = false;
        this.fetchPartners();
      } catch (error) {
        alert('Gagal menyimpan mitra: ' + (error.response?.data?.message || error.message));
      }
    },
    async deletePartner(id) {
      if (confirm('Anda yakin ingin menghapus mitra ini?')) {
        try {
          await this.$axios.delete(`/api/v2/partners/${id}`);
          alert('Mitra berhasil dihapus!');
          this.fetchPartners();
        } catch (error) {
          alert('Gagal menghapus mitra: ' + error.message);
        }
      }
    }
  }
}
</script>

<style scoped>
.admin-partners {
  padding: 20px;
}

.table img {
  background: #f5f5f5;
  padding: 5px;
  border-radius: 4px;
}
</style>

<template>
  <div class="admin-doctors">
    <h2 class="mb-4">Manajemen Dokter</h2>

    <button @click="showForm = !showForm" class="btn btn-primary mb-3">
      <span v-if="!showForm">+ Tambah Dokter</span>
      <span v-else>Batal</span>
    </button>

    <!-- Form Tambah/Edit -->
    <div v-if="showForm" class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">{{ editingId ? 'Edit Dokter' : 'Tambah Dokter Baru' }}</h5>
        <form @submit.prevent="saveDoctor">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama *</label>
              <input v-model="form.name" type="text" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Spesialisasi *</label>
              <input v-model="form.specialization" type="text" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input v-model="form.email" type="email" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Telepon</label>
              <input v-model="form.phone" type="tel" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Bio</label>
              <textarea v-model="form.bio" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Pendidikan</label>
              <input v-model="form.education" type="text" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Pengalaman</label>
              <input v-model="form.experience" type="text" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nomor Lisensi</label>
              <input v-model="form.license_number" type="text" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Foto</label>
              <input type="file" @change="handleFileUpload" accept="image/*" class="form-control" ref="photoInput">
              <small v-if="form.photo" class="text-muted d-block mt-1">
                File: {{ form.photo.split('/').pop() }}
              </small>
              <small v-if="uploadingImage" class="text-info d-block mt-1">Mengupload gambar...</small>
              <!-- Preview foto saat edit -->
              <div v-if="editingId && form.photo" class="mt-2">
                <img :src="form.photo" alt="Preview" style="max-width: 150px; max-height: 150px; object-fit: cover;" class="rounded">
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-success" :disabled="uploadingImage">Simpan</button>
        </form>
      </div>
    </div>

    <!-- Doctors List -->
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Nama</th>
            <th>Spesialisasi</th>
            <th>Email</th>
            <th>Telepon</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doctor in doctors" :key="doctor.id">
            <td>{{ doctor.name }}</td>
            <td>{{ doctor.specialization }}</td>
            <td>{{ doctor.email }}</td>
            <td>{{ doctor.phone }}</td>
            <td>
              <span v-if="doctor.is_active" class="badge bg-success">Aktif</span>
              <span v-else class="badge bg-danger">Tidak Aktif</span>
            </td>
            <td>
              <button @click="editDoctor(doctor)" class="btn btn-warning btn-sm me-2">Edit</button>
              <button @click="deleteDoctor(doctor.id)" class="btn btn-danger btn-sm">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="doctors.length === 0" class="alert alert-info text-center">
      Tidak ada dokter
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminDoctors',
  data() {
    return {
      doctors: [],
      showForm: false,
      editingId: null,
      uploadingImage: false,
      form: {
        name: '',
        specialization: '',
        email: '',
        phone: '',
        bio: '',
        education: '',
        experience: '',
        license_number: '',
        photo: ''
      }
    }
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
        // Don't set Content-Type manually - axios will set it automatically with boundary
        const response = await this.$axios.post('/api/v2/upload', formData);
        
        if (response.data.success) {
          this.form.photo = response.data.url;
          alert('Gambar berhasil diupload!');
        }
      } catch (error) {
        console.log('Upload error:', error.response);
        if (error.response?.status === 401) {
          alert('Sesi login Anda telah berakhir. Silakan login kembali.');
          localStorage.removeItem('token');
          this.$router.push('/admin/login');
        } else {
          alert('Gagal mengupload gambar: ' + (error.response?.data?.message || error.message));
        }
        // Reset file input
        this.$refs.photoInput.value = '';
      } finally {
        this.uploadingImage = false;
      }
    },
    async fetchDoctors() {
      try {
        const response = await this.$axios.get('/api/v2/doctors');
        this.doctors = response.data.data;
      } catch (error) {
        console.error('Error fetching doctors:', error);
      }
    },
    resetForm() {
      this.form = {
        name: '',
        specialization: '',
        email: '',
        phone: '',
        bio: '',
        education: '',
        experience: '',
        license_number: '',
        photo: ''
      };
      this.editingId = null;
      // Reset file input
      if (this.$refs.photoInput) {
        this.$refs.photoInput.value = '';
      }
    },
    editDoctor(doctor) {
      this.form = { ...doctor };
      this.editingId = doctor.id;
      this.showForm = true;
    },
    async saveDoctor() {
      try {
        console.log('Saving doctor with data:', this.form);
        console.log('Photo URL:', this.form.photo);
        
        if (this.editingId) {
          const response = await this.$axios.put(`/api/v2/doctors/${this.editingId}`, this.form);
          console.log('Update response:', response.data);
          alert('Dokter berhasil diupdate!');
        } else {
          const response = await this.$axios.post('/api/v2/doctors', this.form);
          console.log('Create response:', response.data);
          alert('Dokter berhasil ditambahkan!');
        }
        this.resetForm();
        this.showForm = false;
        this.fetchDoctors();
      } catch (error) {
        console.error('Save error:', error.response?.data || error);
        alert('Gagal menyimpan dokter: ' + (error.response?.data?.message || error.message));
      }
    },
    async deleteDoctor(id) {
      if (confirm('Anda yakin ingin menghapus dokter ini?')) {
        try {
          await this.$axios.delete(`/api/v2/doctors/${id}`);
          alert('Dokter berhasil dihapus!');
          this.fetchDoctors();
        } catch (error) {
          alert('Gagal menghapus dokter: ' + error.message);
        }
      }
    }
  },
  mounted() {
    this.fetchDoctors();
  }
}
</script>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}
</style>

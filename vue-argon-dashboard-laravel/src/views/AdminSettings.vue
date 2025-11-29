<template>
  <div class="admin-settings">
    <h2 class="mb-4">Pengaturan Website</h2>

    <div class="card">
      <div class="card-body">
        <form @submit.prevent="saveSettings">
          <!-- General Settings -->
          <h5 class="card-title mb-3">🏥 Informasi Umum</h5>
          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Rumah Sakit</label>
              <input v-model="settings.site_name" type="text" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Tagline</label>
              <input v-model="settings.site_tagline" type="text" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Logo</label>
              <input type="file" @change="handleLogoUpload" accept="image/*" class="form-control" ref="logoInput">
              <small v-if="settings.site_logo" class="text-muted d-block mt-1">
                File: {{ settings.site_logo.split('/').pop() }}
              </small>
              <small v-if="uploadingLogo" class="text-info d-block mt-1">Mengupload logo...</small>
              <div v-if="settings.site_logo" class="mt-2">
                <img :src="settings.site_logo" alt="Logo" style="max-width: 200px; max-height: 100px; object-fit: contain;">
              </div>
            </div>
          </div>

          <hr>

          <!-- Hero Section -->
          <h5 class="card-title mb-3">🎯 Hero Section (Carousel)</h5>
          <div class="row mb-4">
            <div class="col-md-12 mb-3">
              <label class="form-label">Judul Utama</label>
              <input v-model="settings.hero_title" type="text" class="form-control">
              <small class="text-muted">Contoh: "Selamat Datang di Rumah Sakit Kami"</small>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea v-model="settings.hero_description" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Gambar Hero</label>
              <input type="file" @change="handleHeroImageUpload" accept="image/*" class="form-control" ref="heroImageInput">
              <small v-if="settings.hero_image" class="text-muted d-block mt-1">
                File: {{ settings.hero_image.split('/').pop() }}
              </small>
              <small v-if="uploadingHeroImage" class="text-info d-block mt-1">Mengupload gambar...</small>
              <div v-if="settings.hero_image" class="mt-2">
                <img :src="settings.hero_image" alt="Hero" style="max-width: 300px; max-height: 200px; object-fit: cover;" class="rounded">
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Tombol 1 - Teks</label>
              <input v-model="settings.hero_button_1_text" type="text" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Tombol 1 - Link</label>
              <input v-model="settings.hero_button_1_link" type="text" class="form-control">
              <small class="text-muted">Contoh: /services, /doctors</small>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Tombol 2 - Teks</label>
              <input v-model="settings.hero_button_2_text" type="text" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Tombol 2 - Link</label>
              <input v-model="settings.hero_button_2_link" type="text" class="form-control">
            </div>
          </div>

          <hr>

          <!-- Contact Info -->
          <h5 class="card-title mb-3">📞 Informasi Kontak</h5>
          <div class="row mb-4">
            <div class="col-md-12 mb-3">
              <label class="form-label">Lokasi Map (Google Maps Link atau Koordinat)</label>
              <input v-model="settings.map_location" type="text" class="form-control" placeholder="Contoh: https://goo.gl/maps/xxxx atau -6.200000,106.816666">
              <small class="text-muted">Masukkan link Google Maps atau koordinat (latitude,longitude)</small>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Telepon</label>
              <input v-model="settings.contact_phone" type="text" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input v-model="settings.contact_email" type="email" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Alamat</label>
              <textarea v-model="settings.contact_address" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Jam Operasional</label>
              <input v-model="settings.contact_hours" type="text" class="form-control">
              <small class="text-muted">Contoh: Senin - Jumat: 08:00 - 20:00</small>
            </div>
          </div>

          <button type="submit" class="btn btn-success" :disabled="saving || uploadingLogo || uploadingHeroImage">
            <span v-if="!saving">💾 Simpan Pengaturan</span>
            <span v-else>Menyimpan...</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminSettings',
  data() {
    return {
      settings: {
        site_name: '',
        site_logo: '',
        site_tagline: '',
        hero_title: '',
        hero_description: '',
        hero_image: '',
        hero_button_1_text: '',
        hero_button_1_link: '',
        hero_button_2_text: '',
        hero_button_2_link: '',
        contact_phone: '',
        contact_email: '',
        contact_address: '',
        contact_hours: '',
      },
      saving: false,
      uploadingLogo: false,
      uploadingHeroImage: false
    }
  },
  mounted() {
    this.fetchSettings();
  },
  methods: {
    async handleLogoUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      const token = localStorage.getItem('token');
      if (!token) {
        alert('Anda belum login. Silakan login terlebih dahulu.');
        this.$router.push('/admin/login');
        return;
      }

      this.uploadingLogo = true;
      const formData = new FormData();
      formData.append('image', file);

      try {
        const response = await this.$axios.post('/api/v2/upload', formData);
        
        if (response.data.success) {
          this.settings.site_logo = response.data.url;
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
        this.$refs.logoInput.value = '';
      } finally {
        this.uploadingLogo = false;
      }
    },
    async handleHeroImageUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      const token = localStorage.getItem('token');
      if (!token) {
        alert('Anda belum login. Silakan login terlebih dahulu.');
        this.$router.push('/admin/login');
        return;
      }

      this.uploadingHeroImage = true;
      const formData = new FormData();
      formData.append('image', file);

      try {
        const response = await this.$axios.post('/api/v2/upload', formData);
        
        if (response.data.success) {
          this.settings.hero_image = response.data.url;
          alert('Gambar hero berhasil diupload!');
        }
      } catch (error) {
        if (error.response?.status === 401) {
          alert('Sesi login Anda telah berakhir. Silakan login kembali.');
          localStorage.removeItem('token');
          this.$router.push('/admin/login');
        } else {
          alert('Gagal mengupload gambar hero: ' + (error.response?.data?.message || error.message));
        }
        this.$refs.heroImageInput.value = '';
      } finally {
        this.uploadingHeroImage = false;
      }
    },
    async fetchSettings() {
      try {
        const response = await this.$axios.get('/api/v2/settings/admin');
        const data = response.data.data;
        
        // Flatten grouped settings
        Object.keys(data).forEach(group => {
          Object.keys(data[group]).forEach(key => {
            this.settings[key] = data[group][key];
          });
        });
      } catch (error) {
        alert('Gagal memuat pengaturan');
      }
    },
    async saveSettings() {
      this.saving = true;
      try {
        await this.$axios.put('/api/v2/settings', this.settings);
        alert('Pengaturan berhasil disimpan!');
      } catch (error) {
        alert('Gagal menyimpan pengaturan: ' + (error.response?.data?.message || error.message));
      } finally {
        this.saving = false;
      }
    }
  }
}
</script>

<style scoped>
.admin-settings {
  padding: 20px;
}

hr {
  margin: 2rem 0;
  border-color: #dee2e6;
}
</style>

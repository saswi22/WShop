<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h5>Manajemen Lowongan Karir</h5>
              <button @click="showFormModal" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Tambah Lowongan
              </button>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Posisi</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Departemen</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Deadline</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lamaran</th>
                    <th class="text-secondary opacity-7">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="career in careers" :key="career.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ career.position }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ career.title }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ career.department || '-' }}</p>
                      <p class="text-xs text-secondary mb-0">{{ career.location || '-' }}</p>
                    </td>
                    <td>
                      <span class="badge badge-sm bg-gradient-info">{{ career.job_type }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">
                        {{ formatDate(career.deadline) }}
                      </span>
                      <br>
                      <span v-if="isDeadlineExpired(career.deadline)" class="badge badge-sm bg-gradient-danger">
                        Ditutup
                      </span>
                      <span v-else class="badge badge-sm bg-gradient-success">
                        Terbuka
                      </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span v-if="career.is_active" class="badge badge-sm bg-gradient-success">Aktif</span>
                      <span v-else class="badge badge-sm bg-gradient-secondary">Nonaktif</span>
                    </td>
                    <td class="align-middle text-center">
                      <button 
                        @click="viewApplications(career)" 
                        class="btn btn-link text-info text-gradient px-2 mb-0"
                        title="Lihat lamaran"
                      >
                        <i class="fas fa-inbox text-sm me-1"></i>
                        <span class="text-xs">{{ career.applications_count || 0 }}</span>
                      </button>
                    </td>
                    <td class="align-middle">
                      <button @click="viewDetails(career)" class="btn btn-link text-secondary text-gradient px-2 mb-0">
                        <i class="fas fa-eye text-xs"></i>
                      </button>
                      <button @click="editCareer(career)" class="btn btn-link text-primary text-gradient px-2 mb-0">
                        <i class="fas fa-pencil-alt text-xs"></i>
                      </button>
                      <button @click="toggleStatus(career)" class="btn btn-link text-warning text-gradient px-2 mb-0">
                        <i :class="career.is_active ? 'fas fa-toggle-on' : 'fas fa-toggle-off'" class="text-xs"></i>
                      </button>
                      <button @click="confirmDelete(career.id)" class="btn btn-link text-danger text-gradient px-2 mb-0">
                        <i class="fas fa-trash text-xs"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="careers.length === 0">
                    <td colspan="7" class="text-center py-4">
                      <p class="text-muted">Belum ada lowongan karir</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Modal -->
    <div class="modal fade" id="formModal" tabindex="-1">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-gradient-primary text-white">
            <h5 class="modal-title">
              <i class="fas fa-briefcase me-2"></i>
              {{ editingId ? 'Edit Lowongan' : 'Tambah Lowongan Baru' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCareer">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Judul <span class="text-danger">*</span></label>
                  <input v-model="form.title" type="text" class="form-control" placeholder="Misal: Lowongan Dokter Spesialis" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                  <input v-model="form.slug" type="text" class="form-control" placeholder="dokter-spesialis" required>
                  <small class="text-muted">URL-friendly, gunakan huruf kecil dan tanda hubung</small>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Posisi <span class="text-danger">*</span></label>
                  <input v-model="form.position" type="text" class="form-control" placeholder="Dokter Spesialis Anak" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Tipe Pekerjaan <span class="text-danger">*</span></label>
                  <select v-model="form.job_type" class="form-select" required>
                    <option>Full-time</option>
                    <option>Part-time</option>
                    <option>Contract</option>
                    <option>Internship</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Departemen</label>
                  <select v-model="form.department" class="form-select">
                    <option value="">Pilih Departemen</option>
                    <option>Medis</option>
                    <option>Keperawatan</option>
                    <option>Administrasi</option>
                    <option>IT</option>
                    <option>Keuangan</option>
                    <option>Lainnya</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Lokasi</label>
                  <input v-model="form.location" type="text" class="form-control" placeholder="Jakarta Selatan">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Deadline <span class="text-danger">*</span></label>
                  <input v-model="form.deadline" type="date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Status</label>
                  <select v-model="form.is_active" class="form-select">
                    <option :value="true">Aktif</option>
                    <option :value="false">Nonaktif</option>
                  </select>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label fw-bold">Deskripsi Lowongan <span class="text-danger">*</span></label>
                  <textarea v-model="form.description" class="form-control" rows="4" placeholder="Jelaskan tentang posisi ini..." required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label fw-bold">Persyaratan</label>
                  <textarea v-model="form.requirements" class="form-control" rows="5" placeholder="- Pendidikan minimal S1 Kedokteran&#10;- Memiliki STR aktif&#10;- Pengalaman minimal 2 tahun"></textarea>
                  <small class="text-muted">Pisahkan setiap persyaratan dengan enter/baris baru</small>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label fw-bold">Benefit</label>
                  <textarea v-model="form.benefits" class="form-control" rows="5" placeholder="- Gaji kompetitif&#10;- Asuransi kesehatan&#10;- Tunjangan hari raya"></textarea>
                  <small class="text-muted">Pisahkan setiap benefit dengan enter/baris baru</small>
                </div>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" :disabled="submitting">
                  <i class="fas fa-save me-2"></i>
                  {{ submitting ? 'Menyimpan...' : 'Simpan Lowongan' }}
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" v-if="selectedCareer">
          <div class="modal-header bg-gradient-info text-white">
            <h5 class="modal-title">Detail Lowongan</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold text-primary">Posisi</label>
                <p>{{ selectedCareer.position }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-primary">Judul</label>
                <p>{{ selectedCareer.title }}</p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary">Tipe</label>
                <p><span class="badge bg-info">{{ selectedCareer.job_type }}</span></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary">Departemen</label>
                <p>{{ selectedCareer.department || '-' }}</p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary">Lokasi</label>
                <p>{{ selectedCareer.location || '-' }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-primary">Deadline</label>
                <p>{{ formatDate(selectedCareer.deadline) }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-primary">Status</label>
                <p>
                  <span v-if="selectedCareer.is_active" class="badge bg-success">Aktif</span>
                  <span v-else class="badge bg-secondary">Nonaktif</span>
                </p>
              </div>
              <div class="col-12">
                <label class="form-label fw-bold text-primary">Deskripsi</label>
                <p style="white-space: pre-wrap;">{{ selectedCareer.description }}</p>
              </div>
              <div class="col-12" v-if="selectedCareer.requirements">
                <label class="form-label fw-bold text-primary">Persyaratan</label>
                <p style="white-space: pre-wrap;">{{ selectedCareer.requirements }}</p>
              </div>
              <div class="col-12" v-if="selectedCareer.benefits">
                <label class="form-label fw-bold text-primary">Benefit</label>
                <p style="white-space: pre-wrap;">{{ selectedCareer.benefits }}</p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" @click="editFromDetail">
              <i class="fas fa-edit me-2"></i>Edit
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'AdminCareers',
  data() {
    return {
      careers: [],
      selectedCareer: null,
      editingId: null,
      submitting: false,
      formModal: null,
      detailModal: null,
      form: {
        title: '',
        slug: '',
        position: '',
        description: '',
        requirements: '',
        benefits: '',
        department: '',
        job_type: 'Full-time',
        location: '',
        deadline: '',
        is_active: true
      }
    };
  },
  mounted() {
    this.fetchCareers();
    this.formModal = new Modal(document.getElementById('formModal'));
    this.detailModal = new Modal(document.getElementById('detailModal'));
  },
  methods: {
    async fetchCareers() {
      try {
        const response = await this.$axios.get('/api/v2/careers');
        this.careers = response.data.data || response.data;
      } catch (error) {
        console.error('Error fetching careers:', error);
      }
    },
    showFormModal() {
      this.resetForm();
      this.formModal.show();
    },
    viewDetails(career) {
      this.selectedCareer = career;
      this.detailModal.show();
    },
    editCareer(career) {
      this.form = { ...career };
      this.editingId = career.id;
      this.formModal.show();
    },
    editFromDetail() {
      this.detailModal.hide();
      this.editCareer(this.selectedCareer);
    },
    resetForm() {
      this.form = {
        title: '',
        slug: '',
        position: '',
        description: '',
        requirements: '',
        benefits: '',
        department: '',
        job_type: 'Full-time',
        location: '',
        deadline: '',
        is_active: true
      };
      this.editingId = null;
    },
    async saveCareer() {
      this.submitting = true;
      try {
        console.log('Sending data:', this.form);

        if (this.editingId) {
          const response = await this.$axios.put(`/api/v2/careers/${this.editingId}`, this.form);
          console.log('Update response:', response.data);
          alert('✅ Lowongan berhasil diupdate!');
        } else {
          const response = await this.$axios.post('/api/v2/careers', this.form);
          console.log('Create response:', response.data);
          alert('✅ Lowongan berhasil ditambahkan!');
        }
        
        this.formModal.hide();
        this.resetForm();
        this.fetchCareers();
      } catch (error) {
        console.error('Error saving career:', error);
        console.error('Error response:', error.response?.data);
        console.error('Error status:', error.response?.status);
        
        let errorMessage = '❌ Gagal menyimpan lowongan.\n\n';
        
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          errorMessage += 'Error:\n';
          Object.keys(errors).forEach(key => {
            errorMessage += `- ${key}: ${errors[key].join(', ')}\n`;
          });
        } else if (error.response?.data?.message) {
          errorMessage += error.response.data.message;
        } else {
          errorMessage += 'Silakan coba lagi atau hubungi administrator.';
        }
        
        alert(errorMessage);
      } finally {
        this.submitting = false;
      }
    },
    async toggleStatus(career) {
      const newStatus = !career.is_active;
      try {
        await this.$axios.put(`/api/v2/careers/${career.id}`, {
          ...career,
          is_active: newStatus
        });
        
        // eslint-disable-next-line require-atomic-updates
        career.is_active = newStatus;
        alert(`Status berhasil diubah menjadi ${newStatus ? 'Aktif' : 'Nonaktif'}`);
      } catch (error) {
        console.error('Error toggling status:', error);
        alert('Gagal mengubah status');
      }
    },
    async confirmDelete(id) {
      if (confirm('⚠️ Yakin ingin menghapus lowongan ini?\nSemua data lamaran terkait akan tetap tersimpan.')) {
        try {
          await this.$axios.delete(`/api/v2/careers/${id}`);
          
          alert('✅ Lowongan berhasil dihapus!');
          this.fetchCareers();
        } catch (error) {
          console.error('Error deleting career:', error);
          alert('❌ Gagal menghapus lowongan');
        }
      }
    },
    viewApplications(career) {
      this.$router.push({
        name: 'AdminApplications',
        query: { career_id: career.id, position: career.position }
      });
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    isDeadlineExpired(deadline) {
      if (!deadline) return false;
      return new Date(deadline) < new Date();
    }
  }
};
</script>

<style scoped>
.badge {
  font-size: 0.75rem;
}
</style>

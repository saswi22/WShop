<template>
  <div class="careers-page">
    <!-- Hero Section -->
    <div class="hero-careers">
      <div class="container py-5 text-center text-white">
        <h1 class="display-4 fw-bold mb-3">Bergabunglah Bersama Kami</h1>
        <p class="lead mb-0">Wujudkan karir impian Anda di lingkungan kerja yang profesional dan berkembang</p>
      </div>
    </div>

    <div class="container py-5">
      <!-- Stats Section -->
      <div class="row text-center mb-5">
        <div class="col-md-3 mb-3">
          <div class="stat-box">
            <h3 class="text-primary fw-bold">{{ careers.length }}+</h3>
            <p class="text-muted">Posisi Tersedia</p>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="stat-box">
            <h3 class="text-primary fw-bold">50+</h3>
            <p class="text-muted">Karyawan</p>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="stat-box">
            <h3 class="text-primary fw-bold">10+</h3>
            <p class="text-muted">Departemen</p>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="stat-box">
            <h3 class="text-primary fw-bold">24/7</h3>
            <p class="text-muted">Support HR</p>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <select v-model="selectedJobType" class="form-select form-select-lg">
            <option value="">🔍 Semua Tipe Pekerjaan</option>
            <option>Full-time</option>
            <option>Part-time</option>
            <option>Contract</option>
            <option>Internship</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <select v-model="selectedDepartment" class="form-select form-select-lg">
            <option value="">🏥 Semua Departemen</option>
            <option>Medis</option>
            <option>Keperawatan</option>
            <option>Administrasi</option>
            <option>IT</option>
            <option>Keuangan</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <input 
            v-model="searchQuery" 
            type="text" 
            class="form-control form-control-lg" 
            placeholder="🔎 Cari posisi..."
          >
        </div>
      </div>

      <!-- Careers Grid -->
      <div v-if="filteredCareers.length">
        <div class="row">
          <div class="col-lg-6 mb-4" v-for="career in filteredCareers" :key="career.id">
            <div class="card career-card h-100 border-0 shadow-sm">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div class="flex-grow-1">
                    <h4 class="card-title mb-2 fw-bold text-primary">{{ career.position }}</h4>
                    <h6 class="text-muted mb-0">{{ career.title }}</h6>
                  </div>
                  <span class="badge bg-gradient-primary px-3 py-2">{{ career.job_type }}</span>
                </div>

                <div class="career-meta mb-3">
                  <div class="meta-item" v-if="career.department">
                    <i class="fas fa-building text-primary me-2"></i>
                    <span>{{ career.department }}</span>
                  </div>
                  <div class="meta-item" v-if="career.location">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    <span>{{ career.location }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="fas fa-calendar text-primary me-2"></i>
                    <span>Deadline: {{ formatDate(career.deadline) }}</span>
                  </div>
                </div>

                <p class="card-text text-muted mb-3">{{ truncateText(career.description, 150) }}</p>

                <div class="d-flex gap-2">
                  <button 
                    @click="viewDetails(career)" 
                    class="btn btn-outline-primary flex-fill"
                  >
                    <i class="fas fa-info-circle me-2"></i>Detail
                  </button>
                  <button 
                    @click="applyCareer(career)" 
                    class="btn btn-primary flex-fill"
                    :disabled="isDeadlineExpired(career.deadline)"
                  >
                    <i class="fas fa-paper-plane me-2"></i>
                    {{ isDeadlineExpired(career.deadline) ? 'Ditutup' : 'Lamar' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-5">
        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Tidak ada lowongan yang sesuai dengan filter Anda</h5>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" v-if="selectedCareer">
          <div class="modal-header bg-gradient-primary text-white">
            <h5 class="modal-title">{{ selectedCareer.position }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <h6 class="fw-bold mb-2">{{ selectedCareer.title }}</h6>
            <div class="mb-3">
              <span class="badge bg-primary me-2">{{ selectedCareer.job_type }}</span>
              <span class="badge bg-secondary me-2" v-if="selectedCareer.department">{{ selectedCareer.department }}</span>
              <span class="badge bg-info" v-if="selectedCareer.location">{{ selectedCareer.location }}</span>
            </div>

            <div class="mb-4">
              <h6 class="fw-bold text-primary">Deskripsi</h6>
              <p>{{ selectedCareer.description }}</p>
            </div>

            <div class="mb-4" v-if="selectedCareer.requirements">
              <h6 class="fw-bold text-primary">Persyaratan</h6>
              <p class="text-muted">{{ selectedCareer.requirements }}</p>
            </div>

            <div class="mb-4" v-if="selectedCareer.benefits">
              <h6 class="fw-bold text-primary">Benefit</h6>
              <p class="text-muted">{{ selectedCareer.benefits }}</p>
            </div>

            <div class="alert alert-info">
              <i class="fas fa-calendar-alt me-2"></i>
              <strong>Deadline:</strong> {{ formatDate(selectedCareer.deadline) }}
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button 
              type="button" 
              class="btn btn-primary" 
              @click="applyCareer(selectedCareer)"
              :disabled="isDeadlineExpired(selectedCareer.deadline)"
            >
              <i class="fas fa-paper-plane me-2"></i>Lamar Sekarang
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Application Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-gradient-primary text-white">
            <h5 class="modal-title">
              <i class="fas fa-file-alt me-2"></i>Form Lamaran
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="applicationCareer" class="alert alert-info mb-4">
              <strong>Posisi:</strong> {{ applicationCareer.position }}<br>
              <strong>Tipe:</strong> {{ applicationCareer.job_type }}
            </div>

            <form @submit.prevent="submitApplication">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                  <input v-model="applicationForm.name" type="text" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                  <input v-model="applicationForm.email" type="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">No. Telepon <span class="text-danger">*</span></label>
                  <input v-model="applicationForm.phone" type="tel" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Pendidikan Terakhir <span class="text-danger">*</span></label>
                  <select v-model="applicationForm.education" class="form-select" required>
                    <option value="">Pilih...</option>
                    <option>SMA/SMK</option>
                    <option>D3</option>
                    <option>S1</option>
                    <option>S2</option>
                    <option>S3</option>
                  </select>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label fw-bold">CV / Resume <span class="text-danger">*</span></label>
                  <input 
                    type="file" 
                    @change="handleCVUpload" 
                    accept=".pdf,.doc,.docx" 
                    class="form-control" 
                    ref="cvInput"
                    required
                  >
                  <small class="text-muted">Format: PDF, DOC, DOCX (Max 2MB)</small>
                  <small v-if="uploadingCV" class="d-block text-info mt-1">
                    <i class="fas fa-spinner fa-spin me-1"></i>Mengupload CV...
                  </small>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label fw-bold">Surat Lamaran <span class="text-danger">*</span></label>
                  <textarea 
                    v-model="applicationForm.coverLetter" 
                    class="form-control" 
                    rows="5"
                    placeholder="Ceritakan mengapa Anda tertarik dengan posisi ini..."
                    required
                  ></textarea>
                </div>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" :disabled="submitting || uploadingCV">
                  <i class="fas fa-paper-plane me-2"></i>
                  {{ submitting ? 'Mengirim...' : 'Kirim Lamaran' }}
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'CareersPage',
  data() {
    return {
      careers: [],
      selectedJobType: '',
      selectedDepartment: '',
      searchQuery: '',
      selectedCareer: null,
      applicationCareer: null,
      applicationForm: {
        name: '',
        email: '',
        phone: '',
        education: '',
        cv: null,
        cvUrl: '',
        coverLetter: ''
      },
      submitting: false,
      uploadingCV: false,
      detailModal: null,
      applicationModal: null
    };
  },
  computed: {
    filteredCareers() {
      return this.careers.filter(career => {
        const matchesJobType = !this.selectedJobType || career.job_type === this.selectedJobType;
        const matchesDepartment = !this.selectedDepartment || career.department === this.selectedDepartment;
        const matchesSearch = !this.searchQuery || 
          career.position.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          career.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (career.description && career.description.toLowerCase().includes(this.searchQuery.toLowerCase()));
        return matchesJobType && matchesDepartment && matchesSearch;
      });
    }
  },
  mounted() {
    this.fetchCareers();
    this.detailModal = new Modal(document.getElementById('detailModal'));
    this.applicationModal = new Modal(document.getElementById('applicationModal'));
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
    viewDetails(career) {
      this.selectedCareer = career;
      this.detailModal.show();
    },
    applyCareer(career) {
      if (this.detailModal) {
        this.detailModal.hide();
      }
      
      this.applicationCareer = career;
      this.resetApplicationForm();
      this.applicationModal.show();
    },
    async handleCVUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Check file size (2MB)
      if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 2MB');
        this.$refs.cvInput.value = '';
        return;
      }

      const formData = new FormData();
      formData.append('file', file);

      this.uploadingCV = true;
      try {
        const response = await this.$axios.post('/api/v2/upload-cv', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        this.applicationForm.cv = file;
        this.applicationForm.cvUrl = response.data.url || response.data.path;
        console.log('CV uploaded successfully:', response.data);
      } catch (error) {
        console.error('Error uploading CV:', error);
        console.error('Error response:', error.response?.data);
        alert('Gagal mengupload CV. Silakan coba lagi.');
        this.$refs.cvInput.value = '';
      } finally {
        this.uploadingCV = false;
      }
    },
    async submitApplication() {
      if (!this.applicationForm.cvUrl) {
        alert('Silakan upload CV terlebih dahulu');
        return;
      }

      this.submitting = true;
      try {
        const applicationData = {
          career_id: this.applicationCareer.id,
          position: this.applicationCareer.position,
          name: this.applicationForm.name,
          email: this.applicationForm.email,
          phone: this.applicationForm.phone,
          education: this.applicationForm.education,
          cv_url: this.applicationForm.cvUrl,
          cover_letter: this.applicationForm.coverLetter,
          status: 'pending'
        };

        await this.$axios.post('/api/v2/applications', applicationData);
        
        this.applicationModal.hide();
        
        // Success notification
        alert(
          `✅ Lamaran berhasil dikirim!\n\n` +
          `Terima kasih ${this.applicationForm.name}, lamaran Anda untuk posisi ${this.applicationCareer.position} telah kami terima.\n\n` +
          `Tim HR kami akan meninjau aplikasi Anda dan menghubungi Anda melalui email (${this.applicationForm.email}) dalam 3-5 hari kerja.\n\n` +
          `Untuk informasi lebih lanjut, hubungi:\n` +
          `📧 sdirsizjtb@gmail.com`
        );

        this.resetApplicationForm();
      } catch (error) {
        console.error('Error submitting application:', error);
        alert('Gagal mengirim lamaran. Silakan coba lagi atau hubungi HR kami.');
      } finally {
        this.submitting = false;
      }
    },
    resetApplicationForm() {
      this.applicationForm = {
        name: '',
        email: '',
        phone: '',
        education: '',
        cv: null,
        cvUrl: '',
        coverLetter: ''
      };
      if (this.$refs.cvInput) {
        this.$refs.cvInput.value = '';
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },
    isDeadlineExpired(deadline) {
      if (!deadline) return false;
      return new Date(deadline) < new Date();
    },
    truncateText(text, length) {
      if (!text) return '';
      return text.length > length ? text.substring(0, length) + '...' : text;
    }
  }
};
</script>

<style scoped>
.hero-careers {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  margin-top: -20px;
}

.stat-box {
  padding: 20px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
}

.stat-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.career-card {
  transition: all 0.3s ease;
  border-radius: 12px;
  overflow: hidden;
}

.career-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.career-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
}

.meta-item {
  display: flex;
  align-items: center;
  font-size: 0.9rem;
  color: #555;
}

.badge {
  font-size: 0.85rem;
  font-weight: 600;
}

.modal-header.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.form-select-lg,
.form-control-lg {
  border-radius: 8px;
  border: 2px solid #e0e0e0;
  transition: all 0.3s ease;
}

.form-select-lg:focus,
.form-control-lg:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-outline-primary {
  border: 2px solid #667eea;
  color: #667eea;
}

.btn-outline-primary:hover {
  background: #667eea;
  border-color: #667eea;
  color: white;
}

@media (max-width: 768px) {
  .hero-careers h1 {
    font-size: 2rem;
  }
  
  .stat-box h3 {
    font-size: 1.5rem;
  }
  
  .career-meta {
    flex-direction: column;
    gap: 10px;
  }
}
</style>

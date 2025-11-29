<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="mb-1">Lamaran Masuk</h5>
                <p class="text-sm text-muted mb-0" v-if="filterPosition">
                  Posisi: <strong>{{ filterPosition }}</strong>
                </p>
              </div>
              <div class="d-flex gap-2">
                <select v-model="filterStatus" class="form-select form-select-sm" style="width: 200px;">
                  <option value="">📊 Semua Status</option>
                  <option value="pending">⏳ Pending</option>
                  <option value="reviewed">👀 Reviewed</option>
                  <option value="interview">💬 Interview</option>
                  <option value="accepted">✅ Accepted</option>
                  <option value="rejected">❌ Rejected</option>
                </select>
                <button @click="clearFilters" class="btn btn-sm btn-outline-secondary" v-if="filterPosition">
                  <i class="fas fa-times me-1"></i>Clear Filter
                </button>
              </div>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pelamar</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Posisi</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kontak</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                    <th class="text-secondary opacity-7">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="application in filteredApplications" :key="application.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ application.name }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ application.education }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ application.position }}</p>
                    </td>
                    <td>
                      <p class="text-xs mb-0">{{ application.email }}</p>
                      <p class="text-xs text-secondary mb-0">{{ application.phone }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span :class="getStatusBadgeClass(application.status)" class="badge badge-sm">
                        {{ getStatusLabel(application.status) }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatDate(application.created_at) }}</span>
                    </td>
                    <td class="align-middle">
                      <button @click="viewDetails(application)" class="btn btn-link text-primary text-gradient px-2 mb-0">
                        <i class="fas fa-eye text-xs"></i> Detail
                      </button>
                      <button @click="confirmDelete(application.id)" class="btn btn-link text-danger text-gradient px-2 mb-0">
                        <i class="fas fa-trash text-xs"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredApplications.length === 0">
                    <td colspan="6" class="text-center py-4">
                      <p class="text-muted">Tidak ada lamaran masuk</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" v-if="selectedApplication">
          <div class="modal-header">
            <h5 class="modal-title">Detail Lamaran - {{ selectedApplication.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Nama Lengkap</label>
                <p>{{ selectedApplication.name }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Posisi</label>
                <p>{{ selectedApplication.position }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Email</label>
                <p>{{ selectedApplication.email }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Telepon</label>
                <p>{{ selectedApplication.phone }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Pendidikan</label>
                <p>{{ selectedApplication.education }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Status</label>
                <select v-model="selectedApplication.status" @change="updateStatus" class="form-select">
                  <option value="pending">Pending</option>
                  <option value="reviewed">Reviewed</option>
                  <option value="interview">Interview</option>
                  <option value="accepted">Accepted</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label fw-bold">CV / Resume</label>
                <p>
                  <a :href="getFullUrl(selectedApplication.cv_url)" target="_blank" class="btn btn-sm btn-primary">
                    <i class="fas fa-download me-2"></i>Download CV
                  </a>
                </p>
              </div>
              <div class="col-12">
                <label class="form-label fw-bold">Surat Lamaran</label>
                <p class="text-muted" style="white-space: pre-wrap;">{{ selectedApplication.cover_letter }}</p>
              </div>
              <div class="col-12">
                <label class="form-label fw-bold">Tanggal Melamar</label>
                <p>{{ formatDate(selectedApplication.created_at) }}</p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'AdminApplications',
  data() {
    return {
      applications: [],
      filterStatus: '',
      filterPosition: '',
      selectedApplication: null,
      detailModal: null
    };
  },
  computed: {
    filteredApplications() {
      let filtered = this.applications;
      
      if (this.filterStatus) {
        filtered = filtered.filter(app => app.status === this.filterStatus);
      }
      
      if (this.filterPosition) {
        filtered = filtered.filter(app => 
          app.position.toLowerCase().includes(this.filterPosition.toLowerCase())
        );
      }
      
      return filtered;
    }
  },
  mounted() {
    // Check if coming from careers page with filter
    if (this.$route.query.position) {
      this.filterPosition = this.$route.query.position;
    }
    
    this.loadApplications();
    this.detailModal = new Modal(document.getElementById('detailModal'));
  },
  methods: {
    async loadApplications() {
      try {
        const response = await this.$axios.get('/api/v2/applications');
        this.applications = response.data.data || response.data;
      } catch (error) {
        console.error('Error loading applications:', error);
      }
    },
    viewDetails(application) {
      this.selectedApplication = { ...application };
      this.detailModal.show();
    },
    clearFilters() {
      this.filterPosition = '';
      this.filterStatus = '';
      this.$router.replace({ query: {} });
    },
    async updateStatus() {
      try {
        await this.$axios.put(`/api/v2/applications/${this.selectedApplication.id}`, {
          status: this.selectedApplication.status
        });
        
        // Update in list
        const index = this.applications.findIndex(a => a.id === this.selectedApplication.id);
        if (index !== -1) {
          this.applications[index].status = this.selectedApplication.status;
        }
        
        alert('Status berhasil diupdate');
      } catch (error) {
        console.error('Error updating status:', error);
        alert('Gagal mengupdate status');
      }
    },
    async confirmDelete(id) {
      if (confirm('Yakin ingin menghapus lamaran ini?')) {
        try {
          await this.$axios.delete(`/api/v2/applications/${id}`);
          this.applications = this.applications.filter(a => a.id !== id);
          alert('Lamaran berhasil dihapus');
        } catch (error) {
          console.error('Error deleting application:', error);
          alert('Gagal menghapus lamaran');
        }
      }
    },
    getStatusBadgeClass(status) {
      const classes = {
        pending: 'bg-gradient-warning',
        reviewed: 'bg-gradient-info',
        interview: 'bg-gradient-primary',
        accepted: 'bg-gradient-success',
        rejected: 'bg-gradient-danger'
      };
      return classes[status] || 'bg-gradient-secondary';
    },
    getStatusLabel(status) {
      const labels = {
        pending: 'Pending',
        reviewed: 'Direview',
        interview: 'Interview',
        accepted: 'Diterima',
        rejected: 'Ditolak'
      };
      return labels[status] || status;
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    getFullUrl(path) {
      if (!path) return '#';
      if (path.startsWith('http')) return path;
      return `http://127.0.0.1:8000${path}`;
    }
  }
};
</script>

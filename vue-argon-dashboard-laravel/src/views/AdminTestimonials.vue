<template>
  <div class="admin-testimonials p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>💬 Manajemen Testimoni</h2>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4">
      <li class="nav-item">
        <a 
          class="nav-link" 
          :class="{ active: activeTab === 'pending' }"
          @click="activeTab = 'pending'"
          style="cursor: pointer;"
        >
          Menunggu Approval ({{ pendingCount }})
        </a>
      </li>
      <li class="nav-item">
        <a 
          class="nav-link" 
          :class="{ active: activeTab === 'approved' }"
          @click="activeTab = 'approved'"
          style="cursor: pointer;"
        >
          Disetujui ({{ approvedCount }})
        </a>
      </li>
    </ul>

    <!-- Pending Testimonials -->
    <div v-if="activeTab === 'pending'">
      <div v-if="pendingTestimonials.length === 0" class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>Tidak ada testimoni yang menunggu approval
      </div>
      <div v-else class="row">
        <div class="col-md-6 mb-4" v-for="t in pendingTestimonials" :key="t.id">
          <div class="card shadow-sm border-warning">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h5 class="mb-1">{{ t.patient_name }}</h5>
                  <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>{{ formatDate(t.created_at) }}
                  </small>
                </div>
                <span class="badge bg-warning text-dark">Pending</span>
              </div>
              
              <div class="mb-2">
                <i 
                  v-for="star in 5" 
                  :key="star"
                  :class="['fas fa-star', star <= t.rating ? 'text-warning' : 'text-muted']"
                ></i>
              </div>
              
              <p class="text-muted mb-3">{{ t.message }}</p>
              
              <div class="d-flex gap-2">
                <button 
                  @click="approveTestimoni(t.id)" 
                  class="btn btn-success btn-sm flex-fill"
                  :disabled="processing"
                >
                  <i class="fas fa-check me-1"></i>Setujui
                </button>
                <button 
                  @click="rejectTestimoni(t.id)" 
                  class="btn btn-danger btn-sm flex-fill"
                  :disabled="processing"
                >
                  <i class="fas fa-times me-1"></i>Tolak
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Approved Testimonials -->
    <div v-if="activeTab === 'approved'">
      <div v-if="approvedTestimonials.length === 0" class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>Belum ada testimoni yang disetujui
      </div>
      <div v-else class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Nama</th>
              <th>Rating</th>
              <th>Testimoni</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in approvedTestimonials" :key="t.id">
              <td>
                <strong>{{ t.patient_name }}</strong>
              </td>
              <td>
                <i 
                  v-for="star in 5" 
                  :key="star"
                  :class="['fas fa-star', star <= t.rating ? 'text-warning' : 'text-muted']"
                  style="font-size: 0.85rem;"
                ></i>
              </td>
              <td style="max-width: 300px;">
                <p class="mb-0 text-truncate">{{ t.message }}</p>
              </td>
              <td>
                <small>{{ formatDate(t.created_at) }}</small>
              </td>
              <td>
                <button 
                  @click="deleteTestimoni(t.id)" 
                  class="btn btn-danger btn-sm"
                  :disabled="processing"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminTestimonials',
  data() {
    return {
      activeTab: 'pending',
      pendingTestimonials: [],
      approvedTestimonials: [],
      processing: false
    };
  },
  computed: {
    pendingCount() {
      return this.pendingTestimonials.length;
    },
    approvedCount() {
      return this.approvedTestimonials.length;
    }
  },
  methods: {
    formatDate(d) {
      return new Date(d).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    async fetchTestimonials() {
      try {
        const response = await this.$axios.get('/api/v2/testimonials/all');
        const all = response.data.data;
        
        this.pendingTestimonials = all.filter(t => !t.is_approved);
        this.approvedTestimonials = all.filter(t => t.is_approved);
      } catch (error) {
        alert('Gagal memuat testimoni: ' + (error.response?.data?.message || error.message));
      }
    },
    async approveTestimoni(id) {
      if (!confirm('Setujui testimoni ini?')) return;
      
      this.processing = true;
      try {
        await this.$axios.put(`/api/v2/testimonials/${id}/approve`);
        alert('Testimoni berhasil disetujui!');
        this.fetchTestimonials();
      } catch (error) {
        alert('Gagal menyetujui testimoni: ' + (error.response?.data?.message || error.message));
      } finally {
        this.processing = false;
      }
    },
    async rejectTestimoni(id) {
      if (!confirm('Tolak dan hapus testimoni ini?')) return;
      
      this.processing = true;
      try {
        await this.$axios.delete(`/api/v2/testimonials/${id}`);
        alert('Testimoni berhasil ditolak dan dihapus');
        this.fetchTestimonials();
      } catch (error) {
        alert('Gagal menolak testimoni: ' + (error.response?.data?.message || error.message));
      } finally {
        this.processing = false;
      }
    },
    async deleteTestimoni(id) {
      if (!confirm('Hapus testimoni ini?')) return;
      
      this.processing = true;
      try {
        await this.$axios.delete(`/api/v2/testimonials/${id}`);
        alert('Testimoni berhasil dihapus');
        this.fetchTestimonials();
      } catch (error) {
        alert('Gagal menghapus testimoni: ' + (error.response?.data?.message || error.message));
      } finally {
        this.processing = false;
      }
    }
  },
  mounted() {
    this.fetchTestimonials();
  }
}
</script>

<style scoped>
.nav-link {
  color: #495057;
}

.nav-link.active {
  color: #0d7377;
  border-bottom-color: #0d7377;
  font-weight: 600;
}

.card {
  transition: transform 0.2s ease;
}

.card:hover {
  transform: translateY(-3px);
}

.btn-sm {
  padding: 0.375rem 0.75rem;
}
</style>

<template>
  <div class="doctors-page">
    <div class="container py-5">
      <h1 class="mb-5">Tim Dokter Kami</h1>
      
      <!-- Search & Filter -->
      <div class="row mb-4">
        <div class="col-md-6">
          <input 
            v-model="searchQuery" 
            type="text" 
            class="form-control" 
            placeholder="Cari dokter..."
          >
        </div>
        <div class="col-md-6">
          <select v-model="selectedSpecialization" class="form-select">
            <option value="">Semua Spesialisasi</option>
            <option>Jantung</option>
            <option>Saraf</option>
            <option>Anak-anak</option>
            <option>Bedah</option>
            <option>Gigi</option>
          </select>
        </div>
      </div>

      <!-- Doctors Grid -->
      <div v-if="filteredDoctors.length" class="row">
        <div class="col-md-6 col-lg-4 mb-4" v-for="doctor in filteredDoctors" :key="doctor.id">
          <div class="card h-100 shadow-sm doctor-card">
            <div class="card-body text-center">
              <div class="avatar mb-3">
                <img 
                  v-if="doctor.photo" 
                  :src="doctor.photo" 
                  alt="Doctor" 
                  class="rounded-circle"
                >
                <div v-else class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; margin: 0 auto;">
                  👨‍⚕️
                </div>
              </div>
              <h5 class="card-title">{{ doctor.name }}</h5>
              <p class="text-primary fw-bold mb-2">{{ doctor.specialization }}</p>
              <p class="text-muted small mb-3">{{ doctor.bio }}</p>
              
              <div class="doctor-info mb-3 text-start">
                <p v-if="doctor.education" class="small mb-1">
                  <strong>Pendidikan:</strong> {{ doctor.education }}
                </p>
                <p v-if="doctor.experience" class="small mb-1">
                  <strong>Pengalaman:</strong> {{ doctor.experience }}
                </p>
                <p v-if="doctor.email" class="small mb-1">
                  <strong>Email:</strong> {{ doctor.email }}
                </p>
                <p v-if="doctor.phone" class="small">
                  <strong>Telepon:</strong> {{ doctor.phone }}
                </p>
              </div>

              <button @click="viewSchedule(doctor)" class="btn btn-primary w-100">
                Lihat Jadwal Praktik
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="alert alert-info text-center">
        <p>Tidak ada dokter yang ditemukan</p>
      </div>

      <!-- Schedule Modal -->
      <div v-if="selectedDoctor" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Jadwal Praktik - {{ selectedDoctor.name }}</h5>
              <button @click="selectedDoctor = null" type="button" class="btn-close"></button>
            </div>
            <div class="modal-body">
              <div v-if="selectedDoctor.schedules && selectedDoctor.schedules.length">
                <div class="list-group">
                  <div class="list-group-item" v-for="schedule in selectedDoctor.schedules" :key="schedule.id">
                    <h6 class="mb-1">{{ schedule.day }}</h6>
                    <p class="mb-1">
                      <strong>Waktu:</strong> {{ schedule.start_time }} - {{ schedule.end_time }}
                    </p>
                    <p class="mb-0" v-if="schedule.location">
                      <strong>Lokasi:</strong> {{ schedule.location }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-else class="alert alert-warning mb-0">
                Jadwal tidak tersedia
              </div>
            </div>
            <div class="modal-footer">
              <button @click="selectedDoctor = null" type="button" class="btn btn-secondary">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DoctorsPage',
  data() {
    return {
      doctors: [],
      searchQuery: '',
      selectedSpecialization: '',
      selectedDoctor: null
    }
  },
  computed: {
    filteredDoctors() {
      return this.doctors.filter(doctor => {
        const matchesSearch = doctor.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                             doctor.specialization.toLowerCase().includes(this.searchQuery.toLowerCase());
        const matchesSpecialization = !this.selectedSpecialization || 
                                     doctor.specialization === this.selectedSpecialization;
        return matchesSearch && matchesSpecialization;
      });
    }
  },
  methods: {
    async fetchDoctors() {
      try {
        const response = await this.$axios.get('/api/v2/doctors');
        this.doctors = response.data.data;
      } catch (error) {
        console.error('Error fetching doctors:', error);
      }
    },
    viewSchedule(doctor) {
      this.selectedDoctor = doctor;
    }
  },
  mounted() {
    this.fetchDoctors();
  }
}
</script>

<style scoped>
.doctor-card {
  transition: all 0.3s ease;
  border: none;
}

.doctor-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

.avatar img {
  width: 100px;
  height: 100px;
  object-fit: cover;
}

.doctor-info {
  background: #f8f9fa;
  padding: 10px;
  border-radius: 5px;
}
</style>

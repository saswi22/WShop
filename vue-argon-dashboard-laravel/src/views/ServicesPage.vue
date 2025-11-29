<template>
  <div class="services-page">
    <div class="container py-5">
      <h1 class="mb-5">Layanan Medis Kami</h1>
      
      <!-- Services Grid -->
      <div v-if="services.length" class="row">
        <div class="col-md-6 col-lg-4 mb-4" v-for="service in services" :key="service.id">
          <div class="card h-100 shadow-sm service-card">
            <div v-if="service.image" class="service-image">
              <img :src="service.image" class="card-img-top" alt="Service">
            </div>
            <div class="card-body">
              <h5 class="card-title">{{ service.name }}</h5>
              <p class="card-text">{{ service.description }}</p>
            </div>
            <div class="card-footer bg-white">
              <button @click="showDetail(service)" class="btn btn-primary w-100">
                Detail Layanan
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="alert alert-info text-center">
        <p>Tidak ada layanan yang tersedia</p>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detailModalLabel">{{ selectedService.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedService.image" class="mb-4">
              <img :src="selectedService.image" class="img-fluid rounded" alt="Service Image">
            </div>
            <div class="service-details">
              <h6 class="fw-bold mb-3">Deskripsi Layanan</h6>
              <p class="text-muted" style="white-space: pre-line;">{{ selectedService.description }}</p>
              
              <div v-if="selectedService.facilities" class="mt-4">
                <h6 class="fw-bold mb-3">Fasilitas</h6>
                <p class="text-muted" style="white-space: pre-line;">{{ selectedService.facilities }}</p>
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
  name: 'ServicesPage',
  data() {
    return {
      services: [],
      selectedService: {},
      detailModal: null
    }
  },
  methods: {
    async fetchServices() {
      try {
        const response = await this.$axios.get('/api/v2/services');
        this.services = response.data.data;
      } catch (error) {
        console.error('Error fetching services:', error);
      }
    },
    showDetail(service) {
      this.selectedService = service;
      this.detailModal.show();
    }
  },
  mounted() {
    this.fetchServices();
    this.detailModal = new Modal(document.getElementById('detailModal'));
  }
}
</script>

<style scoped>
.service-card {
  transition: all 0.3s ease;
  border: none;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

.service-image {
  height: 200px;
  overflow: hidden;
}

.service-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>

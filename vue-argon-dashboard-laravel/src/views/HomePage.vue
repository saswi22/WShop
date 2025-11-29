<template>
  <div class="home-page">
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-overlay"></div>
      <div class="container py-3 py-lg-5 position-relative" style="z-index: 2;">
        <div class="row align-items-center min-vh-70">
          <div class="col-lg-6 text-white mb-4 mb-lg-0">
            <div class="hero-content" data-aos="fade-right">
              <h1 class="hero-title fw-bold mb-3 mb-lg-4 text-white">
                {{ settings.hero?.hero_title || 'Selamat Datang di Rumah Sakit Kami' }}
              </h1>
              <p class="hero-description mb-3 mb-lg-4 text-white">
                {{ settings.hero?.hero_description || 'Kami berkomitmen untuk memberikan pelayanan kesehatan terbaik dengan teknologi modern dan tim profesional yang berpengalaman.' }}
              </p>
              <div class="hero-buttons">
                <router-link 
                  :to="settings.hero?.hero_button_1_link || '/services'" 
                  class="btn btn-light btn-hero me-2 me-lg-3 mb-2 shadow-lg">
                  {{ settings.hero?.hero_button_1_text || 'Lihat Layanan' }}
                </router-link>
                <router-link 
                  :to="settings.hero?.hero_button_2_link || '/doctors'" 
                  class="btn btn-outline-light btn-hero mb-2">
                  {{ settings.hero?.hero_button_2_text || 'Tim Dokter' }}
                </router-link>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-12 mt-2 mt-md-3 mt-lg-0" data-aos="fade-left">
            <div class="hero-image-container">
              <img 
                :src="settings.hero?.hero_image || 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=600&h=400&fit=crop'" 
                alt="Rumah Sakit" 
                class="hero-image"
                @error="handleImageError"
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section py-4 py-lg-5 bg-light">
      <div class="container">
        <div class="row text-center">
          <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="stat-card">
              <h3 class="h2 text-primary mb-2">10+</h3>
              <p class="text-muted mb-0 stat-label">Dokter Profesional</p>
            </div>
          </div>
          <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="stat-card">
              <h3 class="h2 text-success mb-2">500+</h3>
              <p class="text-muted mb-0 stat-label">Pasien Puas</p>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <h3 class="h2 text-info mb-2">10+</h3>
              <p class="text-muted mb-0 stat-label">Layanan Medis</p>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <h3 class="h2 text-warning mb-2">24/7</h3>
              <p class="text-muted mb-0 stat-label">Layanan Darurat</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Services Preview -->
    <div class="services-preview py-4 py-lg-5">
      <div class="container">
        <h2 class="text-center mb-4 mb-lg-5 section-title">Layanan Unggulan Kami</h2>
        <div class="row">
          <div class="col-md-4 mb-4" v-for="service in services.slice(0, 3)" :key="service.id">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title">{{ service.name }}</h5>
                <p class="card-text">{{ service.description }}</p>
                <router-link to="/services" class="btn btn-primary btn-sm">
                  Selengkapnya
                </router-link>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center mt-4">
          <router-link to="/services" class="btn btn-primary btn-lg">
            Lihat Semua Layanan
          </router-link>
        </div>
      </div>
    </div>

    <!-- Doctors Section -->
    <div class="doctors-section py-5">
      <div class="container">
        <h2 class="text-center mb-5">Tim Dokter Kami</h2>
        <div class="row">
          <div class="col-md-3 mb-4" v-for="doctor in doctors.slice(0, 4)" :key="doctor.id">
            <div class="card text-center shadow-sm h-100">
              <img :src="doctor.photo || 'https://via.placeholder.com/150?text=Doctor'" 
                   class="card-img-top doctor-photo" 
                   :alt="doctor.name">
              <div class="card-body">
                <h5 class="card-title">{{ doctor.name }}</h5>
                <p class="text-primary mb-2"><strong>{{ doctor.specialization }}</strong></p>
                <p class="card-text small text-muted">{{ doctor.bio ? doctor.bio.substring(0, 80) + '...' : '' }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center mt-4">
          <router-link to="/doctors" class="btn btn-primary btn-lg">
            Lihat Semua Dokter
          </router-link>
        </div>
      </div>
    </div>

    <!-- Testimonials Section -->
    <div class="testimonials-section py-5 bg-light">
      <div class="container">
        <h2 class="text-center mb-5">Testimoni Pasien</h2>
        <div class="testimonials-marquee">
          <div class="marquee-content">
            <div class="testimonial-card" v-for="testimonial in testimonials" :key="testimonial.id">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h6 class="card-title mb-2">{{ testimonial.patient_name }}</h6>
                  <div class="stars mb-2">
                    <span v-for="n in 5" :key="n" :class="n <= testimonial.rating ? 'text-warning' : 'text-muted'">★</span>
                  </div>
                  <p class="card-text">"{{ testimonial.message }}"</p>
                  <small class="text-muted">{{ formatDate(testimonial.created_at) }}</small>
                </div>
              </div>
            </div>
            <!-- Duplicate for seamless loop -->
            <div class="testimonial-card" v-for="testimonial in testimonials" :key="'dup-' + testimonial.id">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h6 class="card-title mb-2">{{ testimonial.patient_name }}</h6>
                  <div class="stars mb-2">
                    <span v-for="n in 5" :key="n" :class="n <= testimonial.rating ? 'text-warning' : 'text-muted'">★</span>
                  </div>
                  <p class="card-text">"{{ testimonial.message }}"</p>
                  <small class="text-muted">{{ formatDate(testimonial.created_at) }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Partners Section -->
    <div class="partners-section py-5">
      <div class="container">
        <h2 class="text-center mb-5">Mitra Kami</h2>
        <div class="partners-marquee">
          <div class="marquee-content-partners">
            <div class="partner-logo" v-for="partner in partners" :key="partner.id">
              <img :src="partner.logo" :alt="partner.name" />
            </div>
            <!-- Duplicate for seamless loop -->
            <div class="partner-logo" v-for="partner in partners" :key="'dup-' + partner.id">
              <img :src="partner.logo" :alt="partner.name" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent News -->
    <div class="recent-news py-5">
    <!-- Map Section -->
    <div v-if="settings.general?.map_location || settings.map_location" class="map-section py-5">
      <div class="container">
        <h2 class="text-center mb-4">Lokasi Rumah Sakit</h2>
        <div class="map-embed mx-auto" style="max-width:600px;">
          <iframe
            v-if="mapEmbedUrl"
            :src="mapEmbedUrl"
            width="100%"
            height="350"
            style="border:0; border-radius:12px;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </div>
      <div class="container">
        <h2 class="text-center mb-5">Berita Terbaru</h2>
        <div class="row">
          <div class="col-md-4 mb-4" v-for="item in news.slice(0, 3)" :key="item.id">
            <div class="card shadow-sm">
              <img v-if="item.featured_image" :src="item.featured_image" class="card-img-top" alt="News">
              <div class="card-body">
                <h5 class="card-title">{{ item.title }}</h5>
                <p class="card-text">{{ item.excerpt }}</p>
                <small class="text-muted">{{ formatDate(item.published_at) }}</small><br>
                <router-link :to="`/news/${item.id}`" class="btn btn-primary btn-sm mt-2">
                  Baca Selengkapnya
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      services: [],
      news: [],
      doctors: [],
      testimonials: [],
      partners: [],
      settings: {}
    }
  },
  computed: {
    mapEmbedUrl() {
      const loc = this.settings.general?.map_location || this.settings.map_location;
      if (!loc) return '';
      
      // Jika link Google Maps embed
      if (loc.includes('maps/embed') || loc.includes('output=embed')) {
        return loc;
      }
      // Jika link Google Maps biasa
      if (loc.startsWith('https://www.google.com/maps')) {
        return loc.replace('/maps?', '/maps/embed?');
      }
      // Jika link Google Maps pendek
      if (loc.includes('maps.app.goo.gl') || loc.includes('goo.gl')) {
        return '';
      }
      // Jika koordinat
      if (/^-?\d+\.\d+,-?\d+\.\d+$/.test(loc.trim())) {
        return `https://maps.google.com/maps?q=${loc.trim()}&z=15&output=embed`;
      }
      // Default
      return loc;
    }
  },
  methods: {
    handleImageError(event) {
      // Fallback ke gambar default jika error
      event.target.src = 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=600&h=400&fit=crop';
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    async fetchServices() {
      try {
        const response = await this.$axios.get('/api/v2/services');
        this.services = response.data.data;
      } catch (error) {
        console.error('Error fetching services:', error);
      }
    },
    async fetchNews() {
      try {
        const response = await this.$axios.get('/api/v2/news');
        this.news = response.data.data;
      } catch (error) {
        console.error('Error fetching news:', error);
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
    async fetchTestimonials() {
      try {
        const response = await this.$axios.get('/api/v2/testimonials');
        this.testimonials = response.data.data;
      } catch (error) {
        console.error('Error fetching testimonials:', error);
      }
    },
    async fetchPartners() {
      try {
        const response = await this.$axios.get('/api/v2/partners');
        this.partners = response.data.data;
      } catch (error) {
        console.error('Error fetching partners:', error);
      }
    },
    async fetchSettings() {
      try {
        const response = await this.$axios.get('/api/v2/settings');
        this.settings = response.data.data;
      } catch (error) {
        console.error('Error fetching settings:', error);
      }
    }
  },
  mounted() {
    this.fetchSettings();
    this.fetchServices();
    this.fetchNews();
    this.fetchDoctors();
    this.fetchTestimonials();
    this.fetchPartners();
  }
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #0d7377 0%, #0dd1c2 100%);
}

.stat-card {
  padding: 20px;
}

.card {
  border: none;
  transition: transform 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
}

.doctor-photo {
  height: 200px;
  object-fit: cover;
}

.stars {
  font-size: 1.2rem;
}

/* Testimonials Marquee */
.testimonials-marquee {
  overflow: hidden;
  position: relative;
  width: 100%;
}

.marquee-content {
  display: flex;
  animation: scroll-testimonials 40s linear infinite;
}

.testimonial-card {
  flex: 0 0 350px;
  margin: 0 15px;
}

.testimonial-card .card {
  height: 100%;
  min-height: 200px;
}

@keyframes scroll-testimonials {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

.marquee-content:hover {
  animation-play-state: paused;
}

/* Partners Marquee */
.partners-marquee {
  overflow: hidden;
  position: relative;
  width: 100%;
}

.marquee-content-partners {
  display: flex;
  align-items: center;
  animation: scroll-partners 30s linear infinite;
}

.partner-logo {
  flex: 0 0 200px;
  margin: 0 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.partner-logo img {
  max-width: 160px;
  max-height: 80px;
  object-fit: contain;
  filter: grayscale(100%);
  opacity: 0.7;
  transition: all 0.3s ease;
}

.partner-logo img:hover {
  filter: grayscale(0%);
  opacity: 1;
}

@keyframes scroll-partners {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

.marquee-content-partners:hover {
  animation-play-state: paused;
}

/* Hero Section Enhanced */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #0d7377 0%, #14FFEC 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  overflow: hidden;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(13, 115, 119, 0.95) 0%, rgba(13, 209, 194, 0.85) 100%);
  z-index: 1;
}

.section-title {
  font-size: 2rem;
  font-weight: bold;
}

.stat-label {
  font-size: 0.9rem;
}

.min-vh-70 {
  min-height: 70vh;
}

.hero-title {
  font-size: 2.5rem;
  line-height: 1.2;
}

.hero-description {
  font-size: 1.1rem;
  line-height: 1.6;
}

.btn-hero {
  border-radius: 50px;
  font-weight: 600;
  padding: 0.75rem 2rem;
  font-size: 0.95rem;
  border-width: 2px;
}

.hero-content {
  animation: fadeInUp 1s ease-out;
}

.hero-image-container {
  position: relative;
  animation: fadeInRight 1s ease-out;
}

.hero-image {
  width: 100%;
  height: auto;
  max-height: 500px;
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  border: 5px solid rgba(255, 255, 255, 0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hero-image:hover {
  transform: translateY(-10px) scale(1.02);
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
}

.hero-buttons .btn {
  transition: all 0.3s ease;
}

.hero-buttons .btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInRight {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Responsive - Tablet & Mobile Landscape */
@media (max-width: 991px) {
  .hero-section {
    min-height: auto;
    padding: 25px 0 15px;
  }

  .min-vh-70 {
    min-height: auto;
  }

  .hero-title {
    font-size: 1.3rem;
    line-height: 1.25;
    margin-bottom: 1rem !important;
  }

  .hero-description {
    font-size: 0.85rem;
    line-height: 1.4;
    margin-bottom: 1rem !important;
  }

  .btn-hero {
    padding: 0.5rem 1.2rem;
    font-size: 0.8rem;
  }

  .hero-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .hero-buttons .btn {
    flex: 1 1 auto;
    min-width: 120px;
  }

  .section-title {
    font-size: 1.3rem;
    margin-bottom: 1.5rem !important;
  }

  .stat-card h3 {
    font-size: 1.3rem;
    margin-bottom: 0.25rem !important;
  }

  .stat-label {
    font-size: 0.75rem;
  }

  .card {
    margin-bottom: 1rem !important;
  }

  .card-title {
    font-size: 1rem;
  }

  .card-text {
    font-size: 0.85rem;
  }

  .btn-sm {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
  }
  
  .hero-image {
    max-height: 350px;
  }
  
  .display-3 {
    font-size: 2.5rem;
  }
}

/* Tablet Portrait & Mobile Landscape (768px and below) */
@media (max-width: 768px) {
  .hero-section {
    padding: 15px 0 12px;
  }

  .hero-title {
    font-size: 1.1rem;
    line-height: 1.2;
    margin-bottom: 0.6rem !important;
  }

  .hero-description {
    font-size: 0.75rem;
    line-height: 1.3;
    margin-bottom: 0.6rem !important;
  }

  .btn-hero {
    padding: 0.4rem 0.8rem;
    font-size: 0.65rem;
  }

  .section-title {
    font-size: 0.9rem;
    margin-bottom: 0.7rem !important;
  }

  .stat-card h3 {
    font-size: 0.95rem;
  }

  .stat-label {
    font-size: 0.6rem;
  }

  .card-title {
    font-size: 0.8rem;
  }

  .card-text {
    font-size: 0.7rem;
  }
}

/* Mobile Portrait (414px and below - standard mobile size) */
@media (max-width: 414px) {
  .hero-section {
    padding: 10px 0 8px;
  }

  .hero-title {
    font-size: 0.85rem;
    line-height: 1.15;
    margin-bottom: 0.5rem !important;
  }

  .hero-description {
    font-size: 0.65rem;
    line-height: 1.25;
    margin-bottom: 0.5rem !important;
  }

  .btn-hero {
    padding: 0.35rem 0.7rem;
    font-size: 0.6rem;
    border-radius: 40px;
  }

  .hero-buttons {
    gap: 0.4rem;
  }

  .hero-buttons .btn {
    min-width: 90px;
  }

  .hero-content {
    padding: 0 3px;
  }

  .hero-image-container {
    margin-top: 0.5rem;
  }

  .hero-image {
    max-height: 200px;
    border-radius: 12px;
    border: 3px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }

  .hero-image:hover {
    transform: none;
  }

  .section-title {
    font-size: 0.8rem;
    margin-bottom: 0.6rem !important;
    font-weight: 600;
  }

  .stat-card h3 {
    font-size: 0.85rem;
    margin-bottom: 0.15rem !important;
  }

  .stat-label {
    font-size: 0.55rem;
  }

  .stats-section {
    padding: 0.75rem 0 !important;
  }

  .services-preview,
  .news-section,
  .doctors-section,
  .testimonials-section,
  .partners-section {
    padding: 0.75rem 0 !important;
  }

  .card {
    margin-bottom: 0.6rem !important;
  }

  .card-body {
    padding: 0.6rem !important;
  }

  .card-title {
    font-size: 0.75rem;
    margin-bottom: 0.4rem !important;
    font-weight: 600;
  }

  .card-text {
    font-size: 0.65rem;
    line-height: 1.25;
    margin-bottom: 0.4rem !important;
  }

  .btn-sm,
  .card .btn {
    font-size: 0.6rem;
    padding: 0.3rem 0.6rem;
    border-radius: 30px;
  }

  h2, .h2 {
    font-size: 0.8rem;
    margin-bottom: 0.5rem !important;
  }

  h3, .h3 {
    font-size: 0.75rem;
    margin-bottom: 0.4rem !important;
  }

  h4, .h4 {
    font-size: 0.7rem;
    margin-bottom: 0.4rem !important;
  }

  h5, .h5 {
    font-size: 0.65rem;
    margin-bottom: 0.3rem !important;
  }

  h6, .h6 {
    font-size: 0.6rem;
    margin-bottom: 0.3rem !important;
  }

  p {
    font-size: 0.65rem;
    line-height: 1.25;
    margin-bottom: 0.4rem;
  }

  small, .small {
    font-size: 0.55rem;
  }

  .lead {
    font-size: 0.7rem !important;
  }

  .container {
    padding-left: 10px;
    padding-right: 10px;
  }

  .row {
    margin-left: -6px;
    margin-right: -6px;
  }

  .col,
  [class*="col-"] {
    padding-left: 6px;
    padding-right: 6px;
  }

  /* Navbar adjustments */
  .nav-link {
    font-size: 0.75rem;
    padding: 0.35rem 0.5rem;
  }

  /* Footer */
  footer h5 {
    font-size: 0.7rem !important;
  }

  footer p {
    font-size: 0.6rem !important;
  }

  /* Images */
  img {
    max-width: 100%;
    height: auto;
  }

  .doctor-card img,
  .service-icon,
  .news-image {
    max-height: 120px;
  }

  /* Badges and labels */
  .badge {
    font-size: 0.55rem;
    padding: 0.25rem 0.4rem;
  }

  /* Forms */
  .form-control,
  .form-select {
    font-size: 0.65rem;
    padding: 0.4rem 0.6rem;
  }

  .form-label {
    font-size: 0.65rem;
    margin-bottom: 0.3rem;
  }

  /* Testimonials */
  .testimonial-text {
    font-size: 0.65rem !important;
  }

  .testimonial-author {
    font-size: 0.6rem !important;
  }

  /* Partners/Logo section */
  .partner-logo {
    max-height: 40px !important;
  }
}

/* Extra Small Mobile (360px and below - smaller phones) */
@media (max-width: 360px) {
  .hero-section {
    padding: 8px 0 6px;
  }

  .hero-title {
    font-size: 0.8rem;
    line-height: 1.1;
    margin-bottom: 0.4rem !important;
  }

  .hero-description {
    font-size: 0.6rem;
    line-height: 1.2;
    margin-bottom: 0.4rem !important;
  }

  .btn-hero {
    padding: 0.3rem 0.6rem;
    font-size: 0.55rem;
  }

  .hero-buttons .btn {
    min-width: 80px;
  }

  .section-title {
    font-size: 0.75rem;
    margin-bottom: 0.5rem !important;
  }

  .stat-card h3 {
    font-size: 0.8rem;
  }

  .stat-label {
    font-size: 0.5rem;
  }

  .card-title {
    font-size: 0.7rem;
  }

  .card-text {
    font-size: 0.6rem;
  }

  .btn-sm,
  .card .btn {
    font-size: 0.55rem;
    padding: 0.25rem 0.5rem;
  }

  h2, .h2 {
    font-size: 0.75rem;
  }

  h3, .h3 {
    font-size: 0.7rem;
  }

  p {
    font-size: 0.6rem;
  }

  .container {
    padding-left: 8px;
    padding-right: 8px;
  }

  .navbar-brand span {
    font-size: 0.6rem !important;
  }

  .navbar-brand img {
    height: 22px !important;
  }
}
</style>



<template>
  <div class="user-dashboard">
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" :class="{ active: sidebarOpen }" @click="closeSidebar"></div>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
      <div class="container-fluid">
        <router-link to="/" class="navbar-brand d-flex align-items-center">
          <img v-if="settings.general?.site_logo" :src="settings.general?.site_logo" alt="Logo" class="me-2" style="height: 40px; object-fit: contain;">
          <span class="fw-bold" style="font-size: 1.1rem;">{{ settings.general?.site_name || '🏥 Rumah Sakit' }}</span>
        </router-link>
        <button class="navbar-toggler custom-toggler" type="button" @click="toggleSidebar">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Desktop Menu -->
        <div class="collapse navbar-collapse d-none d-lg-block" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <router-link to="/" class="nav-link">Beranda</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/pages/tentang-kami" class="nav-link">Tentang Kami</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/doctors" class="nav-link">Dokter</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/services" class="nav-link">Layanan</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/facilities" class="nav-link">Fasilitas</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/testimonials" class="nav-link">Testimoni</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/news" class="nav-link">Berita</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/careers" class="nav-link">Karir</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/contact" class="nav-link">Kontak</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin" class="nav-link btn btn-primary ms-2">Admin</router-link>
            </li>
          </ul>
        </div>
        
        <!-- Mobile Sidebar -->
        <div class="mobile-sidebar" :class="{ active: sidebarOpen }">
          <div class="sidebar-header">
            <h5>Menu</h5>
            <button class="btn-close-sidebar" @click="closeSidebar">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <ul class="sidebar-nav">
            <li class="sidebar-item">
              <router-link to="/" class="sidebar-link" @click="closeSidebar">Beranda</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/pages/tentang-kami" class="sidebar-link" @click="closeSidebar">Tentang Kami</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/doctors" class="sidebar-link" @click="closeSidebar">Dokter</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/services" class="sidebar-link" @click="closeSidebar">Layanan</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/facilities" class="sidebar-link" @click="closeSidebar">Fasilitas</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/testimonials" class="sidebar-link" @click="closeSidebar">Testimoni</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/news" class="sidebar-link" @click="closeSidebar">Berita</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/careers" class="sidebar-link" @click="closeSidebar">Karir</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/contact" class="sidebar-link" @click="closeSidebar">Kontak</router-link>
            </li>
            <li class="sidebar-item">
              <router-link to="/admin" class="sidebar-link btn-admin" @click="closeSidebar">Admin</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
      <router-view />
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h5>Tentang Kami</h5>
            <p>{{ settings.general?.site_tagline || 'Rumah sakit terdepan dalam memberikan layanan kesehatan berkualitas' }}</p>
          </div>
          <div class="col-md-4">
            <h5>Kontak</h5>
            <p>
              <span v-if="settings.contact?.contact_email">Email: {{ settings.contact?.contact_email }}<br></span>
              <span v-if="settings.contact?.contact_phone">Telepon: {{ settings.contact?.contact_phone }}</span>
            </p>
            <p v-if="settings.contact?.contact_address" class="mt-2">{{ settings.contact?.contact_address }}</p>
          </div>
          <div class="col-md-4">
            <h5>Jam Operasional</h5>
            <p>{{ settings.contact?.contact_hours || 'Senin - Jumat: 08:00 - 17:00' }}</p>
          </div>
        </div>
        <hr>
        <div class="text-center">
          <p>&copy; 2025 {{ settings.general?.site_name || 'Rumah Sakit' }}. Semua hak dilindungi.</p>
        </div>
      </div>
    </footer>

    <!-- Chat Widget -->
    <ChatWidget />
  </div>
</template>

<script>
import ChatWidget from '@/components/ChatWidget.vue';

export default {
  name: 'UserDashboard',
  components: {
    ChatWidget
  },
  data() {
    return {
      settings: {},
      sidebarOpen: false
    }
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
    closeSidebar() {
      this.sidebarOpen = false;
    },
    async fetchSettings() {
      try {
        const response = await this.$axios.get('/api/v2/settings');
        this.settings = response.data.data;
      } catch (error) {
        // Silent fail - use defaults
      }
    }
  },
  mounted() {
    this.fetchSettings();
  }
}
</script>

<style scoped>
.user-dashboard {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.navbar {
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.navbar-brand {
  font-size: 1rem;
}

.navbar-brand img {
  height: 35px !important;
}

.navbar-brand span {
  font-size: 1rem !important;
}

/* Custom Toggler with Teal Color */
.custom-toggler {
  border: 2px solid #0d7377;
  padding: 0.4rem 0.6rem;
  background: rgba(13, 115, 119, 0.1);
}

.custom-toggler:focus {
  box-shadow: 0 0 0 0.2rem rgba(13, 115, 119, 0.25);
}

.custom-toggler .navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(13, 115, 119, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Sidebar Overlay */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s, visibility 0.3s;
}

.sidebar-overlay.active {
  opacity: 1;
  visibility: visible;
}

/* Mobile Sidebar */
.mobile-sidebar {
  position: fixed;
  top: 0;
  right: -280px;
  width: 280px;
  height: 100vh;
  background: white;
  z-index: 1050;
  transition: right 0.3s ease;
  overflow-y: auto;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
}

.mobile-sidebar.active {
  right: 0;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.2rem 1rem;
  background: linear-gradient(135deg, #0d7377 0%, #14FFEC 100%);
  color: white;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header h5 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
}

.btn-close-sidebar {
  background: none;
  border: none;
  color: white;
  font-size: 1.3rem;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background 0.2s;
}

.btn-close-sidebar:hover {
  background: rgba(255, 255, 255, 0.2);
}

.sidebar-nav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-item {
  border-bottom: 1px solid #f0f0f0;
}

.sidebar-link {
  display: block;
  padding: 1rem 1.2rem;
  color: #333;
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.2s;
  position: relative;
}

.sidebar-link:hover {
  background: #f8f9fa;
  color: #0d7377;
  padding-left: 1.5rem;
}

.sidebar-link.router-link-active {
  background: linear-gradient(90deg, rgba(13, 115, 119, 0.1) 0%, transparent 100%);
  color: #0d7377;
  font-weight: 600;
  border-left: 3px solid #0d7377;
}

.sidebar-link.btn-admin {
  margin: 1rem;
  padding: 0.7rem 1rem;
  background: linear-gradient(135deg, #0d7377 0%, #14FFEC 100%);
  color: white;
  text-align: center;
  border-radius: 8px;
  font-weight: 600;
}

.sidebar-link.btn-admin:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13, 115, 119, 0.3);
  padding-left: 1rem;
}

/* Hide sidebar on desktop */
@media (min-width: 992px) {
  .mobile-sidebar,
  .sidebar-overlay,
  .custom-toggler {
    display: none;
  }
}

.navbar-toggler {
  border: 1px solid rgba(0,0,0,0.1);
  padding: 0.4rem 0.6rem;
}

.navbar-toggler:focus {
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Tablet and Mobile Landscape */
@media (max-width: 768px) {
  .navbar-brand {
    font-size: 0.75rem;
  }

  .navbar-brand img {
    height: 26px !important;
  }

  .navbar-brand span {
    font-size: 0.7rem !important;
  }

  .nav-link {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
  }
}

/* Mobile Portrait (414px and below) */
@media (max-width: 414px) {
  .navbar-brand {
    font-size: 0.7rem;
  }

  .navbar-brand img {
    height: 24px !important;
  }

  .navbar-brand span {
    font-size: 0.65rem !important;
  }

  .nav-link {
    font-size: 0.75rem;
    padding: 0.3rem 0.5rem;
  }

  .navbar {
    padding: 0.4rem 0.8rem;
  }

  .navbar-toggler {
    font-size: 0.8rem;
    padding: 0.3rem 0.5rem;
  }

  footer h5 {
    font-size: 0.7rem !important;
    margin-bottom: 0.5rem !important;
  }

  footer p {
    font-size: 0.6rem !important;
    line-height: 1.3;
    margin-bottom: 0.4rem !important;
  }
}

/* Extra Small Mobile (360px and below) */
@media (max-width: 360px) {
  .navbar-brand {
    font-size: 0.65rem;
  }

  .navbar-brand img {
    height: 22px !important;
  }

  .navbar-brand span {
    font-size: 0.6rem !important;
  }

  .nav-link {
    font-size: 0.7rem;
    padding: 0.25rem 0.4rem;
  }

  .navbar {
    padding: 0.35rem 0.6rem;
  }
}

.navbar-brand h2 {
  color: #0d6efd;
  font-weight: bold;
}

footer {
  margin-top: auto;
}
</style>

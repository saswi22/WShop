<template>
  <div class="admin-dashboard">
    <!-- Admin Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
      <div class="container-fluid">
        <router-link to="/admin" class="navbar-brand">
          <h5 class="mb-0">🏥 Admin Panel</h5>
        </router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <router-link to="/admin/dashboard" class="nav-link">Dashboard</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/doctors" class="nav-link">Dokter</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/services" class="nav-link">Layanan</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/news" class="nav-link">Berita</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/careers" class="nav-link">Karir</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/contacts" class="nav-link">Pesan</router-link>
            </li>
            <li class="nav-item">
              <button @click="logout" class="nav-link btn btn-danger btn-sm ms-2">Logout</button>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Admin Content -->
    <div class="container-fluid py-4">
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminDashboard',
  methods: {
    logout() {
      if (confirm('Anda yakin ingin logout?')) {
        localStorage.removeItem('token');
        this.$router.push('/admin/login');
      }
    }
  },
  mounted() {
    // Check if user is authenticated
    if (!localStorage.getItem('token')) {
      this.$router.push('/admin/login');
    }
  }
}
</script>

<style scoped>
.admin-dashboard {
  min-height: 100vh;
}
</style>

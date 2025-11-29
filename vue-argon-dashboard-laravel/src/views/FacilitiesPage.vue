<template>
  <div class="facilities-page container py-4">
    <h1 class="mb-4">Fasilitas Rawat Inap</h1>

    <!-- Step 1: Pilih Kelas -->
    <div v-if="!selectedClass" class="row">
      <div class="col-md-3 mb-4" v-for="cls in classes" :key="cls.key">
        <div class="card h-100 shadow-sm class-card" @click="selectClass(cls.key)">
          <div class="card-body d-flex flex-column justify-content-center text-center">
            <h4 class="fw-bold mb-2">{{ cls.label }}</h4>
            <p class="text-muted small">{{ cls.desc }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 2: Pilih Ruangan berdasarkan kelas -->
    <div v-else-if="!selectedRoom" class="mt-2">
      <button class="btn btn-link text-primary mb-3" @click="resetClass">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke pilihan kelas
      </button>

      <h5 class="mb-3">Pilih Ruangan - {{ classLabel(selectedClass) }}</h5>
      <div v-if="roomsBySelectedClass.length === 0" class="alert alert-info">
        <p class="mb-0">Belum ada ruangan tersedia untuk kelas ini.</p>
      </div>
      <div v-else class="row">
        <div class="col-md-4 mb-4" v-for="room in roomsBySelectedClass" :key="room.id">
          <div class="card h-100 room-card" @click="selectRoom(room)">
            <div class="card-body">
              <h6 class="fw-bold mb-1">{{ room.name }}</h6>
              <p class="text-muted small mb-0">Kapasitas: {{ room.capacity || '-' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 3: Detail Ruangan -->
    <div v-else class="mt-2">
      <button class="btn btn-link text-primary mb-3" @click="resetRoom">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke pilihan ruangan
      </button>
      <div class="card shadow-sm">
        <div class="row g-0">
          <div class="col-md-6">
            <img :src="selectedRoom.image || placeholderImage" class="img-fluid rounded-start detail-image" :alt="selectedRoom.name" />
          </div>
          <div class="col-md-6">
            <div class="card-body">
              <h4 class="card-title mb-2">{{ selectedRoom.name }}</h4>
              <span class="badge bg-primary mb-3">{{ classLabel(selectedClass) }}</span>
              <h6 class="fw-bold">Fasilitas</h6>
              <ul class="list-unstyled">
                <li v-for="(item, idx) in facilityList" :key="idx" class="mb-1">
                  <i class="fas fa-check text-success me-2"></i>{{ item }}
                </li>
              </ul>
              <div v-if="selectedRoom.description" class="mt-3">
                <h6 class="fw-bold">Deskripsi</h6>
                <p class="text-muted" style="white-space: pre-line;">{{ selectedRoom.description }}</p>
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
  name: 'FacilitiesPage',
  data() {
    return {
      classes: [
        { key: 'kelas_3', label: 'Kelas 3', desc: 'Fasilitas standar, ekonomis' },
        { key: 'kelas_2', label: 'Kelas 2', desc: 'Fasilitas nyaman dan terjangkau' },
        { key: 'kelas_1', label: 'Kelas 1', desc: 'Privasi lebih dan kenyamanan' },
        { key: 'vip', label: 'VIP', desc: 'Fasilitas eksklusif dan premium' },
      ],
      selectedClass: null,
      selectedRoom: null,
      rooms: [],
      placeholderImage: 'https://via.placeholder.com/800x500?text=Ruang+Rawat+Inap'
    };
  },
  computed: {
    roomsBySelectedClass() {
      return this.rooms.filter(r => r.class === this.selectedClass);
    },
    facilityList() {
      if (!this.selectedRoom) return [];
      // selectedRoom.facilities can be array or newline-separated string
      if (Array.isArray(this.selectedRoom.facilities)) return this.selectedRoom.facilities;
      if (typeof this.selectedRoom.facilities === 'string') {
        return this.selectedRoom.facilities.split(/\r?\n/).filter(Boolean);
      }
      return [];
    }
  },
  methods: {
    classLabel(key) {
      const found = this.classes.find(c => c.key === key);
      return found ? found.label : key;
    },
    selectClass(cls) {
      this.selectedClass = cls;
      this.$router.push({ query: { class: cls } });
    },
    resetClass() {
      this.selectedClass = null;
      this.selectedRoom = null;
      this.$router.push({ query: {} });
    },
    selectRoom(room) {
      this.selectedRoom = room;
      this.$router.push({ query: { class: this.selectedClass, room: room.id } });
    },
    resetRoom() {
      this.selectedRoom = null;
      this.$router.push({ query: { class: this.selectedClass } });
    },
    loadFromQuery() {
      const { class: cls, room: roomId } = this.$route.query;
      if (cls) {
        this.selectedClass = cls;
        if (roomId && this.rooms.length > 0) {
          const found = this.rooms.find(r => r.id === parseInt(roomId));
          if (found) {
            this.selectedRoom = found;
          }
        }
      }
    },
    async fetchFacilities() {
      try {
        const res = await this.$axios.get('/api/v2/facilities');
        // Expecting API to return rooms with { id, name, class, image, facilities, capacity, description }
        const data = res.data?.data || [];
        // Normalize class keys
        this.rooms = data.map(r => ({
          ...r,
          class: (r.class || '').toLowerCase().replace(/\s+/g, '_') // e.g., 'Kelas 3' -> 'kelas_3'
        }));
      } catch (e) {
        console.warn('API /api/v2/facilities not available, using mock data');
        // Fallback mock data - lebih banyak ruangan untuk setiap kelas
        this.rooms = [
          // Kelas 3
          { id: 1, name: 'Ruang Melati 301', class: 'kelas_3', image: '', facilities: ['AC', 'Kamar mandi bersama', 'TV bersama', 'Lemari'], capacity: 6, description: 'Ruangan ekonomis dengan fasilitas standar' },
          { id: 2, name: 'Ruang Melati 302', class: 'kelas_3', image: '', facilities: ['AC', 'Kamar mandi bersama', 'TV bersama', 'Lemari'], capacity: 6, description: 'Ruangan ekonomis dengan fasilitas standar' },
          { id: 3, name: 'Ruang Kenanga 303', class: 'kelas_3', image: '', facilities: ['AC', 'Kamar mandi bersama', 'TV bersama'], capacity: 8, description: 'Ruangan ekonomis kapasitas besar' },
          
          // Kelas 2
          { id: 4, name: 'Ruang Anggrek 201', class: 'kelas_2', image: '', facilities: ['AC', 'Kamar mandi dalam', 'TV', 'Lemari', 'Meja'], capacity: 4, description: 'Ruangan nyaman dengan fasilitas lengkap' },
          { id: 5, name: 'Ruang Anggrek 202', class: 'kelas_2', image: '', facilities: ['AC', 'Kamar mandi dalam', 'TV', 'Lemari'], capacity: 4, description: 'Ruangan nyaman dan terjangkau' },
          { id: 6, name: 'Ruang Dahlia 203', class: 'kelas_2', image: '', facilities: ['AC', 'Kamar mandi dalam', 'TV LED', 'Lemari', 'Kulkas kecil'], capacity: 3, description: 'Ruangan semi-private yang nyaman' },
          
          // Kelas 1
          { id: 7, name: 'Ruang Mawar 101', class: 'kelas_1', image: '', facilities: ['AC', 'Kamar mandi dalam', 'TV LED', 'Sofa tamu', 'Lemari', 'Kulkas'], capacity: 2, description: 'Ruangan private dengan kenyamanan lebih' },
          { id: 8, name: 'Ruang Mawar 102', class: 'kelas_1', image: '', facilities: ['AC', 'Kamar mandi dalam', 'TV LED', 'Sofa', 'Lemari'], capacity: 2, description: 'Ruangan private yang tenang' },
          { id: 9, name: 'Ruang Tulip 103', class: 'kelas_1', image: '', facilities: ['AC', 'Kamar mandi dalam', 'TV LED', 'Sofa tamu', 'Lemari', 'Meja kerja'], capacity: 1, description: 'Ruangan single dengan privasi penuh' },
          
          // VIP
          { id: 10, name: 'Ruang VIP Kenanga', class: 'vip', image: '', facilities: ['AC', 'Kamar mandi dalam', 'TV LED 43"', 'Ruang tamu', 'Pantry', 'Lemari', 'Kulkas'], capacity: 1, description: 'Ruang VIP dengan fasilitas premium dan eksklusif' },
          { id: 11, name: 'Ruang VIP Sakura', class: 'vip', image: '', facilities: ['AC', 'Kamar mandi dalam + bathtub', 'TV LED 50"', 'Ruang tamu', 'Pantry', 'Lemari', 'Kulkas', 'Sofa bed'], capacity: 1, description: 'Ruang VIP mewah dengan pemandangan terbaik' },
          { id: 12, name: 'Ruang VIP Lavender', class: 'vip', image: '', facilities: ['AC', 'Kamar mandi dalam', 'TV LED 43"', 'Ruang tamu', 'Pantry', 'Lemari besar', 'Kulkas', 'Meja makan'], capacity: 1, description: 'Ruang VIP nyaman dengan fasilitas lengkap' },
        ];
      }
      // Load state from query params after data is loaded
      this.loadFromQuery();
    }
  },
  watch: {
    '$route.query': {
      handler() {
        this.loadFromQuery();
      },
      deep: true
    }
  },
  mounted() {
    this.fetchFacilities();
  }
}
</script>

<style scoped>
.class-card { cursor: pointer; border: none; transition: transform .2s ease, box-shadow .2s ease; }
.class-card:hover { transform: translateY(-6px); box-shadow: 0 10px 25px rgba(0,0,0,.15); }

.room-card { cursor: pointer; border: none; transition: transform .2s ease, box-shadow .2s ease; }
.room-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,.12); }

.detail-image { width: 100%; height: 100%; object-fit: cover; max-height: 380px; }
</style>

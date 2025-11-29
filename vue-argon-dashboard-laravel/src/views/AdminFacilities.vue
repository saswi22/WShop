<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6>Manajemen Fasilitas Rawat Inap</h6>
            <button class="btn btn-primary btn-sm" @click="openAddModal">
              <i class="fas fa-plus me-2"></i>Tambah Ruangan
            </button>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Ruangan</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kelas</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kapasitas</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fasilitas</th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="room in rooms" :key="room.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div v-if="room.image" class="me-3">
                          <img :src="room.image" class="avatar avatar-sm me-3" alt="room">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ room.name }}</h6>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ formatClassName(room.class) }}</p>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ room.capacity || '-' }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="badge badge-sm bg-gradient-info">{{ getFacilitiesCount(room.facilities) }} item</span>
                    </td>
                    <td class="align-middle">
                      <button @click="viewDetails(room)" class="btn btn-link text-secondary text-gradient px-2 mb-0">
                        <i class="fas fa-eye text-xs"></i>
                      </button>
                      <button @click="editRoom(room)" class="btn btn-link text-primary text-gradient px-2 mb-0">
                        <i class="fas fa-pencil-alt text-xs"></i>
                      </button>
                      <button @click="confirmDelete(room.id)" class="btn btn-link text-danger text-gradient px-2 mb-0">
                        <i class="fas fa-trash text-xs"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="rooms.length === 0">
                    <td colspan="5" class="text-center py-4">
                      <p class="text-muted">Belum ada data ruangan</p>
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
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEdit ? 'Edit Ruangan' : 'Tambah Ruangan' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveRoom">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nama Ruangan *</label>
                  <input type="text" class="form-control" v-model="form.name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Kelas *</label>
                  <select class="form-select" v-model="form.class" required>
                    <option value="">Pilih Kelas</option>
                    <option value="kelas_3">Kelas 3</option>
                    <option value="kelas_2">Kelas 2</option>
                    <option value="kelas_1">Kelas 1</option>
                    <option value="vip">VIP</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Kapasitas</label>
                  <input type="number" class="form-control" v-model="form.capacity" min="1">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Gambar Ruangan</label>
                  <input type="file" class="form-control" @change="handleImageUpload" accept="image/*">
                  <small v-if="form.image" class="text-success">Gambar sudah diupload</small>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Fasilitas (satu per baris)</label>
                  <textarea class="form-control" v-model="form.facilities" rows="5" 
                    placeholder="AC&#10;Kamar mandi dalam&#10;TV LED&#10;Lemari"></textarea>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Deskripsi</label>
                  <textarea class="form-control" v-model="form.description" rows="3"></textarea>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" @click="saveRoom">
              {{ isEdit ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ selectedRoom.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6" v-if="selectedRoom.image">
                <img :src="selectedRoom.image" class="img-fluid rounded mb-3" alt="room">
              </div>
              <div :class="selectedRoom.image ? 'col-md-6' : 'col-12'">
                <p><strong>Kelas:</strong> {{ formatClassName(selectedRoom.class) }}</p>
                <p><strong>Kapasitas:</strong> {{ selectedRoom.capacity || '-' }}</p>
                <p><strong>Fasilitas:</strong></p>
                <ul>
                  <li v-for="(facility, idx) in parseFacilities(selectedRoom.facilities)" :key="idx">
                    {{ facility }}
                  </li>
                </ul>
                <p v-if="selectedRoom.description"><strong>Deskripsi:</strong><br>{{ selectedRoom.description }}</p>
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
  name: 'AdminFacilities',
  data() {
    return {
      rooms: [],
      form: {
        name: '',
        class: '',
        capacity: '',
        image: '',
        facilities: '',
        description: ''
      },
      selectedRoom: {},
      isEdit: false,
      editId: null,
      formModal: null,
      detailModal: null
    };
  },
  methods: {
    async fetchRooms() {
      try {
        const response = await this.$axios.get('/api/v2/facilities');
        this.rooms = response.data.data;
      } catch (error) {
        console.error('Error fetching rooms:', error);
      }
    },
    openAddModal() {
      this.isEdit = false;
      this.editId = null;
      this.form = {
        name: '',
        class: '',
        capacity: '',
        image: '',
        facilities: '',
        description: ''
      };
      this.formModal.show();
    },
    editRoom(room) {
      this.isEdit = true;
      this.editId = room.id;
      this.form = {
        name: room.name,
        class: room.class,
        capacity: room.capacity,
        image: room.image,
        facilities: Array.isArray(room.facilities) ? room.facilities.join('\n') : room.facilities,
        description: room.description
      };
      this.formModal.show();
    },
    async handleImageUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append('image', file);

      try {
        const response = await this.$axios.post('/api/v2/upload', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.form.image = response.data.url;
        alert('✅ Gambar berhasil diupload');
      } catch (error) {
        console.error('Upload error:', error);
        alert('❌ Gagal upload gambar');
      }
    },
    async saveRoom() {
      try {
        const data = {
          ...this.form,
          capacity: parseInt(this.form.capacity) || null
        };

        if (this.isEdit) {
          await this.$axios.put(`/api/v2/facilities/${this.editId}`, data);
          alert('✅ Ruangan berhasil diupdate');
        } else {
          await this.$axios.post('/api/v2/facilities', data);
          alert('✅ Ruangan berhasil ditambahkan');
        }

        this.formModal.hide();
        this.fetchRooms();
      } catch (error) {
        console.error('Save error:', error);
        alert('❌ Gagal menyimpan ruangan');
      }
    },
    viewDetails(room) {
      this.selectedRoom = room;
      this.detailModal.show();
    },
    async confirmDelete(id) {
      if (!confirm('Yakin ingin menghapus ruangan ini?')) return;

      try {
        await this.$axios.delete(`/api/v2/facilities/${id}`);
        alert('✅ Ruangan berhasil dihapus');
        this.fetchRooms();
      } catch (error) {
        console.error('Delete error:', error);
        alert('❌ Gagal menghapus ruangan');
      }
    },
    formatClassName(classKey) {
      const map = {
        'kelas_3': 'Kelas 3',
        'kelas_2': 'Kelas 2',
        'kelas_1': 'Kelas 1',
        'vip': 'VIP'
      };
      return map[classKey] || classKey;
    },
    parseFacilities(facilities) {
      if (Array.isArray(facilities)) return facilities;
      if (typeof facilities === 'string') return facilities.split('\n').filter(Boolean);
      return [];
    },
    getFacilitiesCount(facilities) {
      return this.parseFacilities(facilities).length;
    }
  },
  mounted() {
    this.fetchRooms();
    this.formModal = new Modal(document.getElementById('formModal'));
    this.detailModal = new Modal(document.getElementById('detailModal'));
  }
};
</script>

<style scoped>
.avatar {
  width: 48px;
  height: 48px;
  object-fit: cover;
}
</style>

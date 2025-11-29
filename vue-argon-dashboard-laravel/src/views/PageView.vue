<template>
  <div class="container py-4">
    <div v-if="loading">Memuat...</div>
    <div v-else>
      <h2 class="mb-3">{{ page.title }}</h2>
      <img v-if="page.featured_image" :src="page.featured_image" class="img-fluid mb-3" />
      <div v-html="page.content"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PageView',
  data() {
    return { page: {}, loading: true };
  },
  methods: {
    async load() {
      const slug = this.$route.params.slug;
      const res = await this.$axios.get(`/api/v2/pages/${slug}`);
      this.page = res.data.data;
      this.loading = false;
    }
  },
  mounted() { this.load(); }
}
</script>

<style scoped>
</style>

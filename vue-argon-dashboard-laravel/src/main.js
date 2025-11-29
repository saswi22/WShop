import { createApp } from "vue";
import App from "./App.vue";
import store from "./store";
import router from "./router/hospital-router";
import "./assets/css/nucleo-icons.css";
import "./assets/css/nucleo-svg.css";
import ArgonDashboard from "./argon-dashboard";
import axios from "axios";

// Configure axios
const axiosInstance = axios.create({
  baseURL: process.env.VUE_APP_API_URL || 'http://127.0.0.1:8000',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add token from localStorage to requests if available
axiosInstance.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  console.log('Request headers:', config.headers); // Debug log
  return config;
}, error => {
  return Promise.reject(error);
});

const appInstance = createApp(App);
appInstance.config.globalProperties.$axios = axiosInstance;
appInstance.use(store);
appInstance.use(router);
appInstance.use(ArgonDashboard);
appInstance.config.globalProperties.$isDemo = process.env.VUE_APP_IS_DEMO || 0;
appInstance.mount("#app");

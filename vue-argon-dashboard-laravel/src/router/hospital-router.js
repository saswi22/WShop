import { createRouter, createWebHistory } from "vue-router";

// User Pages
import UserDashboard from "../views/UserDashboard.vue";
import HomePage from "../views/HomePage.vue";
import DoctorsPage from "../views/DoctorsPage.vue";
import ServicesPage from "../views/ServicesPage.vue";
import NewsPage from "../views/NewsPage.vue";
import CareersPage from "../views/CareersPage.vue";
import ContactPage from "../views/ContactPage.vue";
import PageView from "../views/PageView.vue";

// Admin Pages
import AdminLogin from "../views/AdminLogin.vue";
import AdminLayout from "../layouts/AdminLayout.vue";
import AdminHome from "../views/AdminHome.vue";
import AdminPages from "../views/AdminPages.vue";
import AdminPageEditor from "../views/AdminPageEditor.vue";
import AdminDoctors from "../views/AdminDoctors.vue";
import AdminServices from "../views/AdminServices.vue";
import AdminNews from "../views/AdminNews.vue";
import AdminCareers from "../views/AdminCareers.vue";
import AdminContacts from "../views/AdminContacts.vue";
import AdminApplications from "../views/AdminApplications.vue";
import AdminFacilities from "../views/AdminFacilities.vue";
import AdminPartners from "../views/AdminPartners.vue";
import AdminSettings from "../views/AdminSettings.vue";
import AdminTestimonials from "../views/AdminTestimonials.vue";
import AdminChat from "../views/AdminChat.vue";

const routes = [
  {
    path: "/",
    component: UserDashboard,
    children: [
      {
        path: "",
        name: "Home",
        component: HomePage,
      },
      {
        path: "doctors",
        name: "Doctors",
        component: DoctorsPage,
      },
      {
        path: "services",
        name: "Services",
        component: ServicesPage,
      },
      {
        path: "news",
        name: "News",
        component: NewsPage,
      },
      {
        path: "careers",
        name: "Careers",
        component: CareersPage,
      },
      {
        path: "facilities",
        name: "Facilities",
        component: () => import("../views/FacilitiesPage.vue"),
      },
      {
        path: "testimonials",
        name: "Testimonials",
        component: () => import("../views/TestimonialsPage.vue"),
      },
      {
        path: "contact",
        name: "Contact",
        component: ContactPage,
      },
      {
        path: "pages/:slug",
        name: "PageView",
        component: PageView,
      },
    ],
  },

  // Admin Routes
  {
    path: "/admin/login",
    name: "AdminLogin",
    component: AdminLogin,
  },
  {
    path: "/admin",
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: "dashboard",
        name: "AdminDashboard",
        component: AdminHome,
      },
      {
        path: "pages",
        name: "AdminPages",
        component: AdminPages,
      },
      {
        path: "pages/new",
        name: "AdminPageNew",
        component: AdminPageEditor,
      },
      {
        path: "pages/:id",
        name: "AdminPageEdit",
        component: AdminPageEditor,
      },
      {
        path: "doctors",
        name: "AdminDoctors",
        component: AdminDoctors,
      },
      {
        path: "services",
        name: "AdminServices",
        component: AdminServices,
      },
      {
        path: "news",
        name: "AdminNews",
        component: AdminNews,
      },
      {
        path: "careers",
        name: "AdminCareers",
        component: AdminCareers,
      },
      {
        path: "applications",
        name: "AdminApplications",
        component: AdminApplications,
      },
      {
        path: "facilities",
        name: "AdminFacilities",
        component: AdminFacilities,
      },
      {
        path: "contacts",
        name: "AdminContacts",
        component: AdminContacts,
      },
      {
        path: "partners",
        name: "AdminPartners",
        component: AdminPartners,
      },
      {
        path: "testimonials",
        name: "AdminTestimonials",
        component: AdminTestimonials,
      },
      {
        path: "chat",
        name: "AdminChat",
        component: AdminChat,
      },
      {
        path: "settings",
        name: "AdminSettings",
        component: AdminSettings,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL || "/"),
  routes,
});

// Navigation Guards
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem("token");

  if (to.meta.requiresAuth && !isAuthenticated) {
    next("/admin/login");
  } else if (to.path === "/admin/login" && isAuthenticated) {
    next("/admin/dashboard");
  } else {
    next();
  }
});

export default router;

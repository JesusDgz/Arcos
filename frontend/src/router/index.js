import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/LoginView.vue'
import Admin from '../views/AdminView.vue'
import Mesero from '../views/MeseroView.vue'
import Cliente from '../views/ClienteView.vue'
import Cajero from '../views/CajeroView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: Login,
    },
    {
      path: '/admin',
      name: 'admin',
      component: Admin,
    },
    {
      path: '/mesero',
      name: 'mesero',
      component: Mesero,
    },
    {
      path: '/cliente',
      name: 'cliente',
      component: Cliente,
    },
    {
      path: '/cajero',
      name: 'cajero',
      component: Cajero,
    }
  ]
})

export default router

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { useUserStore } from '@/stores/user'


import App from './App.vue'
import router from './router'
import axios from './axios'

// Vuetify
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'



const pinia = createPinia()

const vuetify = createVuetify({
  components,
  directives
})

const app = createApp(App).use(vuetify)
app.config.globalProperties.$axios = axios
app.use(pinia)
app.use(router)


const store = useUserStore()
store.cargarDesdeStorage()

app.mount('#app')

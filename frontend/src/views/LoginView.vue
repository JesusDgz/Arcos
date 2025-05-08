<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import axios from 'axios'
import { useUserStore } from '@/stores/user'

const email = ref('')
const password = ref('')
const role = ref('')
const loading = ref(false)
const router = useRouter()
const store = useUserStore()

const roles = ['Administrador', 'Mesero', 'Caja']

const roleMap = {
  'Administrador': 'admin',
  'Mesero': 'mesero',
  'Caja': 'caja'
}



const login = async () => {
  loading.value = true
  try {
    if (!role.value) {
      throw new Error('Debes seleccionar un rol')
    }

    const response = await axios.post('/login', {
      correo: email.value,
      contrasena: password.value
    })

    const { token, usuario } = response.data

    const backendRole = roleMap[role.value]

    if (!usuario || (usuario.rol !== backendRole && usuario.rol !== 'admin')) {
  throw new Error('El rol seleccionado no coincide con el usuario o no tiene permisos')
}



    store.setUser(usuario)
    store.setToken(token)

    Swal.fire({
      icon: 'success',
      title: `Bienvenido, ${usuario.nombre}`,
      showConfirmButton: false,
      timer: 1500
    })

    switch (backendRole) {
      case 'admin':
        router.push('/admin')
        break
      case 'mesero':
        router.push('/mesero')
        break
      case 'caja':
        router.push('/cajero')
        break
      case 'cocinero':
        router.push('/cocina')
        break
    }

  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || err.message })
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <v-container class="fill-height login-bg d-flex align-center justify-center" fluid>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="5" lg="4">
        <v-card class="pa-6 rounded-xl" elevation="12">
          <v-card-title class="text-center text-h5 mb-4">
            🍽️ <strong>Bienvenido</strong>
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="login">
              <v-select v-model="role" :items="roles" label="Selecciona tu rol"
                prepend-inner-icon="mdi-account-circle-outline" dense required></v-select>

              <v-text-field v-model="email" label="Correo Electrónico" prepend-inner-icon="mdi-email" type="email" dense
                required></v-text-field>

              <v-text-field v-model="password" label="Contraseña" prepend-inner-icon="mdi-lock" type="password" dense
                required></v-text-field>

              <v-btn type="submit" color="deep-purple-accent-4" class="mt-4" block size="large" :loading="loading">
                🔐 Iniciar como {{ role || '...' }}
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.login-bg {
  background: linear-gradient(to bottom right, #f3e5f5, #e1f5fe);
  min-height: 100vh;
}
</style>

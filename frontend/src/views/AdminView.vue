<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card class="pa-6">
          <v-toolbar flat class="px-4">
            <v-toolbar-title class="text-h5 font-weight-bold">📊 Panel de Administración</v-toolbar-title>
            <v-spacer />
            <v-btn color="red-darken-1" variant="tonal" @click="logout">
              <v-icon start>mdi-logout</v-icon>
              Salir
            </v-btn>
          </v-toolbar>

          <v-card-text>
            <v-row dense>
              <v-col cols="12" md="4">
                <v-sheet class="pa-4 text-center" color="primary" dark rounded="lg">
                  <v-btn @click="crudAdmin = true" variant="tonal" color="white" class="mb-2" rounded>
                    <v-icon start>mdi-account-multiple</v-icon>
                    Usuarios
                  </v-btn>
                  <div class="text-h4 font-weight-bold">{{ stats.usuarios }}</div>
                </v-sheet>
              </v-col>

              <v-col cols="12" md="4">
                <v-sheet class="pa-4 text-center" color="secondary" dark rounded="lg">
                  <v-btn @click="produc = true" variant="tonal" color="white" class="mb-2" rounded>
                    <v-icon start>mdi-package-variant</v-icon>
                    Productos
                  </v-btn>
                  <div class="text-h4 font-weight-bold">{{ stats.productos }}</div>
                </v-sheet>
              </v-col>

              <v-col cols="12" md="4">
                <v-sheet class="pa-4 text-center" color="success" dark rounded="lg">
                  <v-btn @click="pedidos = true" variant="tonal" color="white" class="mb-2" rounded>
                    <v-icon start>mdi-cart-check</v-icon>
                    Pedidos
                  </v-btn>
                  <div class="text-h4 font-weight-bold">{{ stats.pedidos }}</div>
                </v-sheet>
              </v-col>
            </v-row>
          </v-card-text>

        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-6" dense>
      <!-- Usuario -->
      <v-col cols="12" md="4">
        <v-card elevation="2" class="pa-4">
          <v-card-title class="text-h6">
            👤 Crear Usuario
          </v-card-title>
          <v-divider class="mb-3" />
          <v-form @submit.prevent="crearUsuario" class="d-flex flex-column gap-3">
            <v-text-field label="Nombre" v-model="usuario.nombre" dense outlined />
            <v-text-field label="Correo" v-model="usuario.correo" type="email" dense outlined />
            <v-text-field label="Contraseña" v-model="usuario.contrasena" type="password" dense outlined />
            <v-select label="Rol" v-model="usuario.rol" :items="roles" dense outlined />
            <v-btn color="primary" type="submit" block>Crear</v-btn>
          </v-form>
        </v-card>
      </v-col>

      <!-- Producto -->
      <v-col cols="12" md="4">
        <v-card elevation="2" class="pa-4">
          <v-card-title class="text-h6">
            📦 Crear Producto
          </v-card-title>
          <v-divider class="mb-3" />
          <v-form @submit.prevent="crearProducto" class="d-flex flex-column gap-3">
            <v-text-field label="Nombre" v-model="producto.nombre" dense outlined />
            <v-textarea label="Descripción" v-model="producto.descripcion" dense outlined />
            <v-text-field label="Precio" v-model="producto.precio" type="number" dense outlined />
            <v-select label="Categoría" v-model="producto.categoria" :items="categorias" dense outlined />
            <v-btn color="secondary" type="submit" block>Guardar</v-btn>
          </v-form>
        </v-card>
      </v-col>


      <!-- Reporte -->
      <v-col cols="12" md="4">
        <v-card elevation="2" class="pa-4">
          <v-card-title class="text-h6">
            📝 Crear Reporte
          </v-card-title>
          <v-divider class="mb-3" />
          <v-form @submit.prevent="crearReporte" class="d-flex flex-column gap-3">
            <v-text-field label="Tipo de reporte" v-model="reporte.tipo" dense outlined />
            <v-textarea label="Datos (JSON)" v-model="reporte.datos" dense outlined />
            <v-text-field label="ID admin" v-model="reporte.admin_id" type="number" dense outlined />
            <v-btn color="success" type="submit" block>Generar</v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>


  </v-container>

  <v-btn color="deep-orange" block class="mt-10 mb-10 justify-center text-center" @click="mostrarCocina = true">
    👨‍🍳 Vista de cocina
  </v-btn>

  <v-dialog v-model="mostrarCocina" fullscreen persistent transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar dark color="deep-orange">
        <v-toolbar-title>👨‍🍳 Pedidos Pendientes</v-toolbar-title>
        <v-spacer />
        <v-btn icon @click="mostrarCocina = false"><v-icon>mdi-close</v-icon></v-btn>
      </v-toolbar>

      <v-card-text>
        <v-alert v-if="pedidosPendientes.length === 0" type="info">No hay pedidos pendientes por preparar.</v-alert>
        <v-list v-else>
          <v-list-item v-for="pedido in pedidosPendientes" :key="pedido.id" class="mb-4">
            <v-list-item-content>
              <v-list-item-title class="text-h6">
                🧾 Pedido #{{ pedido.id }}
                <span class="text-caption grey--text"> • {{ tiempoActivo(pedido.created_at) }}</span>
              </v-list-item-title>
              <v-list-item-subtitle>
                <v-list dense>
                  <v-list-item v-for="detalle in pedido.detalles" :key="detalle.id">
                    <v-list-item-content>
                      <div>🍽️ {{ detalle.producto?.nombre || 'Producto eliminado' }} x {{ detalle.cantidad }}</div>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-card-text>
    </v-card>
  </v-dialog>

  <v-dialog v-model="crudAdmin" fullscreen transition="dialog-bottom-transition">
    <v-card class="rounded-lg">
      <v-toolbar flat>
        <v-toolbar-title>👥Usuarios</v-toolbar-title>
        <v-spacer />
        <v-btn icon @click="crudAdmin = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>

      <v-divider />

      <v-card-text>
        <AdminCrud :visible="crudAdmin" @cerrar="crudAdmin = false" />
      </v-card-text>
    </v-card>
  </v-dialog>

  <v-dialog v-model="produc" fullscreen persistent transition="dialog-bottom-transition">
    <v-card class="rounded-lg">
      <v-toolbar flat>
        <v-toolbar-title>📦 Productos</v-toolbar-title>
        <v-spacer />
        <v-btn icon @click="produc = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>

      <v-divider />

      <v-card-text>
        <ProductosCrud :visible="produc" @cerrar="produc = false" />
      </v-card-text>
    </v-card>
  </v-dialog>

  <v-dialog v-model="pedidos" fullscreen persistent transition="dialog-bottom-transition">
    <v-card class="rounded-lg">
      <v-toolbar flat>
        <v-toolbar-title>🧾 Historial de pedidos</v-toolbar-title>
        <v-spacer />
        <v-btn icon @click="pedidos = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>

      <v-divider />

      <v-card-text>
        <PedidosTable />
      </v-card-text>
    </v-card>

  </v-dialog>

</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/es'

import { useUserStore } from '@/stores/user'
import { useRouter } from 'vue-router'
import AdminCrud from '@/components/TablesAdmin/UsuariosTable.vue'
import ProductosCrud from '@/components/TablesAdmin/ProductosTable.vue'
import PedidosTable from '@/components/TablesAdmin/PedidosTable.vue'

dayjs.extend(relativeTime)
dayjs.locale('es')
const userStore = useUserStore()
const router = useRouter()

const categorias = [
  'Pollo',
  'Pizza',
  'Platillos',
  'Res',
  'Aguas frescas',
  'Refrescos',
  'Cafés y Bebidas Calientes',
  'Tés'
]


const logout = async () => {
  userStore.logout()
  Swal.fire({
    icon: 'success',
    title: 'Sesión cerrada',
    showConfirmButton: false,
    timer: 1200
  })
  router.push('/')
}

const tiempoActivo = (fecha) => {
  return dayjs(fecha).fromNow(true) + ' activo'
}
const crudAdmin = ref(false)
const produc = ref(false)
const pedidos = ref(false)

const stats = ref({ usuarios: 0, productos: 0, pedidos: 0 })

const usuario = ref({ nombre: '', correo: '', contrasena: '', rol: '' })
const roles = ['cliente', 'empleado', 'cocinero', 'repartidor', 'admin']

const producto = ref({ nombre: '', descripcion: '', precio: '', categoria: '' })

const reporte = ref({ tipo: '', datos: '', admin_id: '' })

onMounted(() => {
  loadStats()
})

const loadStats = async () => {
  try {
    const [u, p, pe] = await Promise.all([
      axios.get('/usuarios'),
      axios.get('/productos'),
      axios.get('/pedidos'),
    ])
    stats.value = {
      usuarios: u.data.data.length,
      productos: p.data.data.length,
      pedidos: pe.data.data.length,
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar las estadísticas' })
    console.error('Error al cargar estadísticas', error)
  }
}

const crearUsuario = async () => {
  try {
    const confirmar = await Swal.fire({
      title: '¿Crear nuevo usuario?',
      text: 'Esta acción creará un nuevo usuario en el sistema.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Crear',
      cancelButtonText: 'Cancelar'
    })
    if (!confirmar.isConfirmed) return

    await axios.post('/usuarios', usuario.value)
    Swal.fire({ icon: 'success', title: 'Usuario creado' })
    loadStats()
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'No se pudo crear el usuario' })
    console.log(err)
  }
}

const crearProducto = async () => {
  try {
    await axios.post('/productos', producto.value)
    Swal.fire({ icon: 'success', title: 'Producto creado' })
    loadStats()
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo crear el producto' + err })
    console.log(err)
  }
}

const crearReporte = async () => {
  try {
    const payload = {
      ...reporte.value,
      datos: JSON.parse(reporte.value.datos)
    }
    await axios.post('/api/reportes', payload)
    Swal.fire({ icon: 'success', title: 'Reporte creado' })
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo crear el reporte' + err })
  }
}

const mostrarCocina = ref(false)
const pedidosPendientes = ref([])

const cargarPedidosPendientes = async () => {
  try {
    const res = await axios.get('/pedidos')
    const pedidosFiltrados = res.data.data.filter(p => p.estado === 'pendiente')

    const detallesRes = await axios.get('/detalle-pedidos')

    for (const pedido of pedidosFiltrados) {
      pedido.detalles = detallesRes.data.data.filter(d => d.pedido_id === pedido.id)

      for (const detalle of pedido.detalles) {
        try {
          const productoRes = await axios.get(`/productos/${detalle.producto_id}`)
          detalle.producto = productoRes.data.data
        } catch (err) {
          detalle.producto = { nombre: 'Producto no disponible' }
          console.error(`No se pudo cargar producto con ID ${detalle.producto_id}`, err)
        }
      }
    }

    pedidosPendientes.value = pedidosFiltrados
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los pedidos pendientes' })
    console.error('Error al cargar pedidos pendientes', err)
  }
}


watch(mostrarCocina, (val) => {
  if (val) cargarPedidosPendientes()
})
</script>

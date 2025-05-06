<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card class="pa-6">
          <v-card-title class="text-h5 font-weight-bold">📊 Panel de Administración</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="4">
                <v-sheet class="pa-4" color="primary" dark rounded>
                  <div class="text-h6">Usuarios</div>
                  <div class="text-h4">{{ stats.usuarios }}</div>
                </v-sheet>
              </v-col>
              <v-col cols="12" md="4">
                <v-sheet class="pa-4" color="secondary" dark rounded>
                  <div class="text-h6">Productos</div>
                  <div class="text-h4">{{ stats.productos }}</div>
                </v-sheet>
              </v-col>
              <v-col cols="12" md="4">
                <v-sheet class="pa-4" color="success" dark rounded>
                  <div class="text-h6">Pedidos</div>
                  <div class="text-h4">{{ stats.pedidos }}</div>
                </v-sheet>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-6">
      <v-col cols="12" md="4">
        <v-card>
          <v-card-title>👤 Crear Usuario</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="crearUsuario">
              <v-text-field label="Nombre" v-model="usuario.nombre" required />
              <v-text-field label="Correo" v-model="usuario.correo" type="email" required />
              <v-text-field label="Contraseña" v-model="usuario.contrasena" type="password" required />
              <v-select label="Rol" v-model="usuario.rol" :items="roles" required />
              <v-btn color="primary" type="submit">Crear</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card>
          <v-card-title>📦 Crear Producto</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="crearProducto">
              <v-text-field label="Nombre" v-model="producto.nombre" required />
              <v-textarea label="Descripción" v-model="producto.descripcion" />
              <v-text-field label="Precio" v-model="producto.precio" type="number" required />
              <v-btn color="secondary" type="submit">Guardar</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card>
          <v-card-title>📝 Crear Reporte</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="crearReporte">
              <v-text-field label="Tipo de reporte" v-model="reporte.tipo" required />
              <v-textarea label="Datos (JSON)" v-model="reporte.datos" required />
              <v-text-field label="ID admin" v-model="reporte.admin_id" type="number" required />
              <v-btn color="success" type="submit">Generar</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

  </v-container>

  <v-btn color="deep-orange" block  class="mt-10 mb-10 justify-center text-center" @click="mostrarCocina = true">
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
        <v-list-item
          v-for="pedido in pedidosPendientes"
          :key="pedido.id"
          class="mb-4"
        >
          <v-list-item-content>
            <v-list-item-title class="text-h6">🧾 Pedido #{{ pedido.id }}</v-list-item-title>
            <v-list-item-subtitle>
              <v-list dense>
                <v-list-item v-for="detalle in pedido.detalles" :key="detalle.id">
                  <v-list-item-content>
                    <div>🍽️ {{ detalle.producto.nombre }} x {{ detalle.cantidad }}</div>
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

</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const stats = ref({ usuarios: 0, productos: 0, pedidos: 0 })

const usuario = ref({ nombre: '', correo: '', contrasena: '', rol: '' })
const roles = ['cliente', 'empleado', 'cocinero', 'repartidor', 'admin']

const producto = ref({ nombre: '', descripcion: '', precio: '' })

const reporte = ref({ tipo: '', datos: '', admin_id: '' })

onMounted(() => {
  loadStats()
})

const loadStats = async () => {
  try {
    const [u, p, pe] = await Promise.all([
      axios.get('/api/usuarios'),
      axios.get('/api/productos'),
      axios.get('/api/pedidos'),
    ])
    stats.value = {
      usuarios: u.data.data.length,
      productos: p.data.data.length,
      pedidos: pe.data.data.length,
    }
  } catch (error) {
    console.error('Error al cargar estadísticas', error)
  }
}

const crearUsuario = async () => {
  try {
    await axios.post('/api/usuarios', usuario.value)
    alert('Usuario creado')
    loadStats()
  } catch (err) {
    alert('Error creando usuario', err)
  }
}

const crearProducto = async () => {
  try {
    await axios.post('/api/productos', producto.value)
    alert('Producto creado')
    loadStats()
  } catch (err) {
    alert('Error creando producto', err)
  }
}

const crearReporte = async () => {
  try {
    const payload = {
      ...reporte.value,
      datos: JSON.parse(reporte.value.datos)
    }
    await axios.post('/api/reportes', payload)
    alert('Reporte creado')
  } catch (err) {
    alert('Error creando reporte', err)
  }
}
const mostrarCocina = ref(false)
const pedidosPendientes = ref([])

const cargarPedidosPendientes = async () => {
  try {
    const res = await axios.get('/api/pedidos')
    const pedidosFiltrados = res.data.data.filter(p => p.estado === 'pendiente')

    const detallesRes = await axios.get('/api/detalle-pedidos')
    for (const pedido of pedidosFiltrados) {
      pedido.detalles = detallesRes.data.data.filter(d => d.pedido_id === pedido.id)
    }

    pedidosPendientes.value = pedidosFiltrados
  } catch (err) {
    console.error('Error al cargar pedidos pendientes', err)
  }
}

watch(mostrarCocina, (val) => {
  if (val) cargarPedidosPendientes()
})


</script>

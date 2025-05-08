<template>
  <v-container>
    <v-card class="pa-4" elevation="4">
      <v-toolbar flat class="px-2">
        <v-toolbar-title class="text-h5 font-weight-bold">🧾 Crear Pedido</v-toolbar-title>

        <v-btn color="red-darken-1" variant="tonal" @click="logout">
          <v-icon start>mdi-logout</v-icon>
          Salir
        </v-btn>
      </v-toolbar>
      <v-card-text>
        <v-form @submit.prevent="crearPedido">
          <v-select label="Seleccionar categoría" :items="categorias" v-model="categoriaSeleccionada" dense hide-details
            clearable class="mb-4" />

          <v-select v-if="categoriaSeleccionada" label="Seleccionar producto" :items="productosFiltrados"
            item-title="nombre" item-value="id" v-model="productoSeleccionado" return-object dense hide-details
            clearable class="mb-4" />


          <v-row class="mt-2" v-if="productoSeleccionado">
            <v-col cols="8">
              <div class="text-subtitle-1">{{ productoSeleccionado.nombre }}</div>
              <div class="text-body-2">{{ productoSeleccionado.descripcion }}</div>
            </v-col>
            <v-col cols="4">
              <v-text-field v-model.number="cantidad" type="number" label="Cantidad" min="1" dense></v-text-field>
              <v-btn class="mt-2" @click="agregarProducto" color="primary" block>Agregar</v-btn>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <v-list two-line subheader>
            <v-list-subheader>Productos en el pedido</v-list-subheader>
            <v-list-item v-for="(item, index) in pedido" :key="index" class="py-2">
              <v-list-item-content>
                <v-list-item-title>
                  {{ item.nombre }} x {{ item.cantidad }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  ${{ item.precio }} c/u - Subtotal: ${{ item.precio * item.cantidad }}
                </v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action>
                <v-btn icon @click="eliminarProducto(index)"><v-icon>mdi-delete</v-icon></v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-list>

          <v-divider class="my-4"></v-divider>

          <div class="text-right text-h6 font-weight-bold">
            Total: ${{ totalPedido }}
          </div>
          <v-text-field label="Mesa" v-model="mesa" dense outlined />


          <v-btn type="submit" color="success" block class="mt-4">Registrar Pedido</v-btn>
        </v-form>

        <v-btn color="error" class="mt-4" block @click="cancelarUltimoPedido">
          ❌ Cancelar Último Pedido
        </v-btn>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'


const productoSeleccionado = ref(null)
const cantidad = ref(1)
const pedido = ref([])
const totalPedido = computed(() => pedido.value.reduce((s, p) => s + p.precio * p.cantidad, 0))
let ultimoPedidoId = null

const router = useRouter()
const userStore = useUserStore()


const productos = ref([])
const categorias = ref([])
const mesa = ref('')
const categoriaSeleccionada = ref(null)
const productosFiltrados = computed(() =>
  productos.value.filter(p => p.categoria === categoriaSeleccionada.value)
)

onMounted(async () => {
  const { data } = await axios.get('/productos')
  productos.value = data.data
  categorias.value = [...new Set(data.data.map(p => p.categoria))] // categorías únicas
})



const logout = () => {
  userStore.logout()
  Swal.fire({
    icon: 'success',
    title: 'Sesión cerrada',
    showConfirmButton: false,
    timer: 1200
  })
  router.push('/')
}

const agregarProducto = () => {
  if (!productoSeleccionado.value || cantidad.value < 1) return
  pedido.value.push({
    ...productoSeleccionado.value,
    cantidad: cantidad.value
  })
  productoSeleccionado.value = null
  cantidad.value = 1
}

const eliminarProducto = (index) => {
  pedido.value.splice(index, 1)
}
const crearPedido = async () => {
  try {
    const pedidoPayload = {
      estado: 'pendiente',
      total: totalPedido.value,
      cliente_id: 1, // o dinámico si lo deseas
      empleado_id: userStore.usuario?.id || null, 
      mesa: mesa.value
    }


    const { data } = await axios.post('/pedidos', pedidoPayload)
    ultimoPedidoId = data.data.id

    for (const item of pedido.value) {
      await axios.post('/detalle-pedidos', {
        pedido_id: ultimoPedidoId,
        producto_id: item.id,
        cantidad: item.cantidad,
        subtotal: item.precio * item.cantidad
      })
    }

    pedido.value = []
    Swal.fire({
      icon: 'success',
      title: '✅ Pedido registrado correctamente',
      showConfirmButton: false,
      timer: 1500
    })
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: '❌ Error al registrar el pedido',
      text: err.message || 'Ocurrió un problema inesperado'
    })
    console.log(err)
  }
}

const cancelarUltimoPedido = async () => {
  if (!ultimoPedidoId) {
    Swal.fire({
      icon: 'info',
      title: 'ℹ️ No hay pedido para cancelar',
      showConfirmButton: false,
      timer: 1500
    })
    return
  }

  try {
    await axios.delete(`/pedidos/${ultimoPedidoId}`)
    ultimoPedidoId = null
    pedido.value = []
    Swal.fire({
      icon: 'success',
      title: '✅ Pedido cancelado exitosamente',
      showConfirmButton: false,
      timer: 1500
    })
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: '❌ No se pudo cancelar el pedido',
      text: err.message || 'Ocurrió un error inesperado'
    })
  }
}

</script>

<style scoped>
.v-list-item-title {
  font-weight: 600;
}
</style>

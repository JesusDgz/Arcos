<template>
  <v-container>
    <v-card class="pa-6">
      <v-toolbar flat>
        <v-toolbar-title class="text-h5 font-weight-bold">💵 Módulo de Caja - Cobro de Pedidos</v-toolbar-title>

        <v-btn color="red-darken-1" variant="tonal" @click="logout">
          <v-icon start>mdi-logout</v-icon>
          Salir
        </v-btn>
      </v-toolbar>
      <v-card-text>
        <v-select label="Seleccionar Pedido Pendiente" :items="pedidos" item-title="label" item-value="id"
          v-model="pedidoSeleccionado" @update:modelValue="cargarDetalles" clearable />


        <v-divider class="my-4" v-if="detallePedido.length" />

        <v-list v-if="detallePedido.length">
          <v-list-item v-for="item in detallePedido" :key="item.id">
            <v-list-item-content>
              <v-list-item-title>
                🍽️ {{ item.producto?.nombre || 'Producto no encontrado' }} x {{ item.cantidad }}
              </v-list-item-title>
              <v-list-item-subtitle>
                Subtotal: ${{ item.subtotal }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>

        <div class="text-right mt-4 text-h6">Total: ${{ total }}</div>

        <v-text-field label="Pago con" v-model.number="pago" type="number" min="0" />
        <div class="text-right text-h6">Cambio: ${{ cambio }}</div>

        <v-btn class="mt-4" color="success" :disabled="!puedeCobrar" @click="cobrarPedido">
          ✅ Cobrar y Finalizar Pedido
        </v-btn>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'

const pedidos = ref([])
const pedidoSeleccionado = ref(null)
const detallePedido = ref([])
const total = ref(0)
const pago = ref(0)

const cambio = computed(() => pago.value - total.value)
const puedeCobrar = computed(() => cambio.value >= 0 && pedidoSeleccionado.value)
const router = useRouter()
const userStore = useUserStore()

onMounted(() => {
  cargarPedidosPendientes()
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

const cargarPedidosPendientes = async () => {
  const res = await axios.get('/pedidos')
  pedidos.value = res.data.data
    .filter(p => p.estado === 'pendiente')
    .map(p => ({
      id: p.id,
      label: `#${p.id} - $${p.total}`
    }))
}

const cargarDetalles = async () => {
  console.log("entorsdlfjadsklfjadkls");

  const res = await axios.get('/detalle-pedidos')
  detallePedido.value = res.data.data.filter(d => d.pedido_id === pedidoSeleccionado.value)

  for (const item of detallePedido.value) {
    const prodRes = await axios.get(`/productos/${item.producto_id}`)
    item.producto = prodRes.data.data
  }

  total.value = detallePedido.value.reduce((sum, p) => {
    const sub = parseFloat(p.subtotal)
    return sum + (isNaN(sub) ? 0 : sub)
  }, 0)

  console.log(total.value);


}

const cobrarPedido = async () => {
  if (!puedeCobrar.value) return alert('Pago insuficiente')
  try {
    const pedidoOriginal = await axios.get(`/pedidos/${pedidoSeleccionado.value}`)

    const { cliente_id, total: totalPedido } = pedidoOriginal.data.data

    await axios.put(`/pedidos/${pedidoSeleccionado.value}`, {
      estado: 'entregado',
      cliente_id,
      total: totalPedido
    })

    alert('Pedido pagado con éxito')
    detallePedido.value = []
    pedidoSeleccionado.value = null
    pago.value = 0
    cargarPedidosPendientes()
  } catch (err) {
    alert('Error al cobrar', err)
    console.log(err);

  }
}
</script>

<style scoped>
.v-list-item-title {
  font-weight: 600;
}
</style>

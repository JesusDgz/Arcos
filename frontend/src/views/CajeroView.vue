<template>
  <v-container>
    <v-card class="pa-6">
      <v-card-title class="text-h5 font-weight-bold">💵 Módulo de Caja - Cobro de Pedidos</v-card-title>
      <v-card-text>
        <v-select
          label="Seleccionar Pedido Pendiente"
          :items="pedidos"
          item-title="label"
          item-value="id"
          v-model="pedidoSeleccionado"
          @change="cargarDetalles"
          clearable
        />

        <v-divider class="my-4" v-if="detallePedido.length" />

        <v-list v-if="detallePedido.length">
          <v-list-item v-for="item in detallePedido" :key="item.id">
            <v-list-item-content>
              <v-list-item-title>
                🍽️ {{ item.producto.nombre }} x {{ item.cantidad }}
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

const pedidos = ref([])
const pedidoSeleccionado = ref(null)
const detallePedido = ref([])
const total = ref(0)
const pago = ref(0)

const cambio = computed(() => pago.value - total.value)
const puedeCobrar = computed(() => cambio.value >= 0 && pedidoSeleccionado.value)

onMounted(() => {
  cargarPedidosPendientes()
})

const cargarPedidosPendientes = async () => {
  const res = await axios.get('/api/pedidos')
  pedidos.value = res.data.data
    .filter(p => p.estado === 'pendiente')
    .map(p => ({
      id: p.id,
      label: `#${p.id} - $${p.total}`
    }))
}

const cargarDetalles = async () => {
  const res = await axios.get('/api/detalle-pedidos')
  detallePedido.value = res.data.data.filter(d => d.pedido_id === pedidoSeleccionado.value)

  for (const item of detallePedido.value) {
    const prodRes = await axios.get(`/api/productos/${item.producto_id}`)
    item.producto = prodRes.data.data
  }

  total.value = detallePedido.value.reduce((sum, p) => sum + p.subtotal, 0)
}

const cobrarPedido = async () => {
  if (!puedeCobrar.value) return alert('Pago insuficiente')
  try {
    await axios.put(`/api/pedidos/${pedidoSeleccionado.value}`, {
      estado: 'entregado'
    })
    alert('Pedido pagado con éxito')
    detallePedido.value = []
    pedidoSeleccionado.value = null
    pago.value = 0
    cargarPedidosPendientes()
  } catch (err) {
    alert('Error al cobrar', err)
  }
}
</script>

<style scoped>
.v-list-item-title {
  font-weight: 600;
}
</style>

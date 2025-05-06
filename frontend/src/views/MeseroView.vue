<template>
  <v-container>
    <v-card class="pa-4" elevation="4">
      <v-card-title class="text-h5 font-weight-bold">🧾 Crear Pedido - Modo Restaurante</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="crearPedido">
          <v-select label="Seleccionar productos" :items="productos" item-title="nombre" item-value="id"
            v-model="productoSeleccionado" return-object dense hide-details clearable></v-select>

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
import { ref, onMounted,computed } from 'vue'
import axios from 'axios'

const productos = ref([])
const productoSeleccionado = ref(null)
const cantidad = ref(1)
const pedido = ref([])
const totalPedido = computed(() => pedido.value.reduce((s, p) => s + p.precio * p.cantidad, 0))
let ultimoPedidoId = null

onMounted(async () => {
  const { data } = await axios.get('/api/productos')
  productos.value = data.data
})

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
      cliente_id: 1, // Cliente genérico mesa
      empleado_id: 1 // Mesero actual
    }

    const { data } = await axios.post('/api/pedidos', pedidoPayload)
    ultimoPedidoId = data.data.id

    for (const item of pedido.value) {
      await axios.post('/api/detalle-pedidos', {
        pedido_id: ultimoPedidoId,
        producto_id: item.id,
        cantidad: item.cantidad,
        subtotal: item.precio * item.cantidad
      })
    }

    alert('Pedido registrado')
    pedido.value = []
  } catch (err) {
    alert('Error al registrar pedido', err)
  }
}

const cancelarUltimoPedido = async () => {
  if (!ultimoPedidoId) return alert('No hay pedido reciente para cancelar')
  try {
    await axios.delete(`/api/pedidos/${ultimoPedidoId}`)
    alert('Último pedido cancelado')
    ultimoPedidoId = null
    pedido.value = []
  } catch (err) {
    alert('No se pudo cancelar', err)
  }
}
</script>

<style scoped>
.v-list-item-title {
  font-weight: 600;
}
</style>

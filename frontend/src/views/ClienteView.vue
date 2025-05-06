<template>
  <v-container>
    <v-card class="pa-4" elevation="4">
      <v-card-title class="text-h5 font-weight-bold">📦 Pedido a Domicilio</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="realizarPedido">
          <v-text-field label="Nombre completo" v-model="cliente.nombre" required />
          <v-text-field label="Teléfono" v-model="cliente.telefono" required />
          <v-text-field label="Dirección de entrega" v-model="cliente.direccion" required />

          <v-select
            label="Seleccionar producto"
            :items="productos"
            item-title="nombre"
            item-value="id"
            v-model="productoSeleccionado"
            return-object
            clearable
          ></v-select>

          <v-row class="mt-2" v-if="productoSeleccionado">
            <v-col cols="8">
              <div class="text-subtitle-1">{{ productoSeleccionado.nombre }}</div>
              <div class="text-body-2">{{ productoSeleccionado.descripcion }}</div>
            </v-col>
            <v-col cols="4">
              <v-text-field
                v-model.number="cantidad"
                type="number"
                label="Cantidad"
                min="1"
              ></v-text-field>
              <v-btn class="mt-2" @click="agregarProducto" color="primary" block>Agregar</v-btn>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <v-list two-line subheader>
            <v-list-subheader>Resumen del pedido</v-list-subheader>
            <v-list-item
              v-for="(item, index) in pedido"
              :key="index"
              class="py-2"
            >
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

          <div class="text-h6 text-right">Total: ${{ totalPedido }}</div>

          <v-card-title class="text-h6 font-weight-bold mt-6">💳 Pago con tarjeta</v-card-title>
          <v-text-field label="Número de tarjeta" v-model="tarjeta.numero" required maxlength="16" />
          <v-text-field label="Nombre en tarjeta" v-model="tarjeta.nombre" required />
          <v-row>
            <v-col><v-text-field label="Exp. MM/AA" v-model="tarjeta.exp" required /></v-col>
            <v-col><v-text-field label="CVV" v-model="tarjeta.cvv" maxlength="4" required /></v-col>
          </v-row>

          <v-btn type="submit" color="success" block class="mt-4">Realizar Pedido</v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const productos = ref([])
const productoSeleccionado = ref(null)
const cantidad = ref(1)
const pedido = ref([])
const cliente = ref({ nombre: '', telefono: '', direccion: '' })
const tarjeta = ref({ numero: '', nombre: '', exp: '', cvv: '' })

const totalPedido = computed(() => pedido.value.reduce((s, p) => s + p.precio * p.cantidad, 0))

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

const realizarPedido = async () => {
  try {
    // crear usuario tipo cliente (temporal o reutilizable)
    const resUser = await axios.post('/api/usuarios', {
      nombre: cliente.value.nombre,
      correo: cliente.value.telefono + '@temp.com',
      contrasena: '12345678',
      rol: 'cliente'
    })

    const userId = resUser.data.data.id
    const clienteRes = await axios.post('/api/clientes', {
      usuario_id: userId,
      direccion: cliente.value.direccion
    })

    const pedidoRes = await axios.post('/api/pedidos', {
      estado: 'pendiente',
      total: totalPedido.value,
      cliente_id: clienteRes.data.data.id,
      empleado_id: null
    })

    for (const item of pedido.value) {
      await axios.post('/api/detalle-pedidos', {
        pedido_id: pedidoRes.data.data.id,
        producto_id: item.id,
        cantidad: item.cantidad,
        subtotal: item.precio * item.cantidad
      })
    }

    alert('Pedido registrado exitosamente')
    pedido.value = []
  } catch (err) {
    alert('Error al registrar pedido')
    console.error(err)
  }
}
</script>

<style scoped>
.v-list-item-title {
  font-weight: 600;
}
</style>

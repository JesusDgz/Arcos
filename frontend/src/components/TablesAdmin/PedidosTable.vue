<template>
  <v-container>
    <v-card elevation="4" class="pa-4">
      <v-card-title>📜 Historial de Pedidos</v-card-title>
      <v-data-table
        :headers="headers"
        :items="pedidos"
        class="elevation-1"
        dense
      >
        <template v-slot:[`item.estado`]="{ item }">
          <v-chip :color="estadoColor(item.estado)" dark small>{{ item.estado }}</v-chip>
        </template>
        <template v-slot:[`item.created_at`]="{ item }">
          {{ formatearFecha(item.created_at) }}
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'

const pedidos = ref([])

const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Cliente', key: 'cliente_id' },
  { title: 'Empleado', key: 'empleado_id' },
  { title: 'Total', key: 'total' },
  { title: 'Estado', key: 'estado' },
  { title: 'Fecha', key: 'created_at' }
]

const estadoColor = (estado) => {
  switch (estado) {
    case 'pendiente': return 'orange'
    case 'entregado': return 'green'
    case 'cancelado': return 'red'
    default: return 'grey'
  }
}

const formatearFecha = (fecha) => {
  return dayjs(fecha).format('DD/MM/YYYY HH:mm')
}

const cargarPedidos = async () => {
  const res = await axios.get('/pedidos')
  pedidos.value = res.data.data
}

onMounted(() => {
  cargarPedidos()
})
</script>

<style scoped>
.v-data-table {
  font-size: 0.9rem;
}
</style>

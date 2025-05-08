<template>
  <v-container>
    <v-card elevation="4" class="pa-4">
      <v-card-title>📦 Productos del sistema</v-card-title>
      <v-data-table :headers="headers" :items="productos" class="elevation-1" dense>
        <template v-slot:[`item.actions`]="{ item }">
          <v-btn icon @click="editarProducto(item)" class="mt-2 me-2 mb-2">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn icon color="error" @click="eliminarProducto(item.id)" class="mt-2 mb-2">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-toolbar flat>
          <v-toolbar-title>✏️ Editar Producto</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="dialog = false"><v-icon>mdi-close</v-icon></v-btn>
        </v-toolbar>

        <v-card-text>
          <v-form @submit.prevent="guardarEdicion">
            <v-text-field label="Nombre" v-model="editado.nombre" required />
            <v-textarea label="Descripción" v-model="editado.descripcion" />
            <v-text-field label="Precio" v-model="editado.precio" type="number" required />
            <v-text-field label="Categoría" v-model="editado.categoria" required />
            <v-btn color="primary" type="submit" class="mt-2">Guardar</v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const productos = ref([])
const dialog = ref(false)
const editado = ref({ id: null, nombre: '', descripcion: '', precio: '', categoria: '' })

defineProps({
  visible: Boolean 
})

const emit = defineEmits(['cerrar'])


const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Nombre', key: 'nombre' },
  { title: 'Descripción', key: 'descripcion' },
  { title: 'Precio', key: 'precio' },
  { title: 'Categoría', key: 'categoria' },
  { title: 'Acciones', key: 'actions', sortable: false }
]

const cargarProductos = async () => {
  const res = await axios.get('/productos')
  productos.value = res.data.data
}

onMounted(() => {
  cargarProductos()
})

const editarProducto = (producto) => {
  editado.value = { ...producto }
  dialog.value = true
}

const guardarEdicion = async () => {
  try {
    await axios.put(`/productos/${editado.value.id}`, editado.value)
    emit('cerrar')
    Swal.fire({ icon: 'success', title: 'Producto actualizado' })
    dialog.value = false
    cargarProductos()
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar el producto' +err })
  }
}

const eliminarProducto = async (id) => {
  emit('cerrar')
  const confirm = await Swal.fire({
    title: '¿Eliminar producto?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar'
  })
  if (!confirm.isConfirmed) return

  try {
    await axios.delete(`/productos/${id}`)
    Swal.fire({ icon: 'success', title: 'Producto eliminado' })
    cargarProductos()
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar el producto' + err})
  }
}
</script>

<style scoped>
.v-data-table {
  font-size: 0.9rem;
}
</style>

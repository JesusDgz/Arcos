<template>
  <v-container>
    <v-card elevation="4" class="pa-4">
      <v-card-title>👥 Usuarios del sistema</v-card-title>
      <v-data-table :headers="headers" :items="usuarios" class="elevation-1" dense>
        <template  v-slot:[`item.actions`]="{ item }">
          <v-btn icon @click="editarUsuario(item)" class="mt-2 me-2 mb-2">
            <v-icon >mdi-pencil</v-icon>
          </v-btn>
          <v-btn icon color="error" @click="eliminarUsuario(item.id)" class="mt-2 mb-2">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>✏️ Editar Usuario</v-card-title>
        <v-spacer></v-spacer>
        <v-btn icon @click="dialog = false">
        <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-card-text>
          <v-form @submit.prevent="guardarEdicion">
            <v-text-field label="Nombre" v-model="editado.nombre" required />
            <v-text-field label="Correo" v-model="editado.correo" required />
            <v-select label="Rol" :items="roles" v-model="editado.rol" required />
            <v-btn color="primary" type="submit">Guardar</v-btn>
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



const usuarios = ref([])
const dialog = ref(false)
const editado = ref({ id: null, nombre: '', correo: '', rol: '' })
const roles = ['cliente', 'empleado', 'cocinero', 'repartidor', 'admin']

defineProps({
  dialogPadre: Boolean // si usas v-model:dialog
  // OR
  // visible: Boolean
})
const emit = defineEmits(['cerrar'])

const cerrarDialogo = () => emit('cerrar')



const headers = [
  { title: 'ID', value: 'id' },
  { title: 'Nombre', value: 'nombre' },
  { title: 'Correo', value: 'correo' },
  { title: 'Rol', value: 'rol' },
  { title: 'Acciones', value: 'actions', sortable: false }
]

const cargarUsuarios = async () => {
  const res = await axios.get('/usuarios')
  usuarios.value = res.data.data
}

onMounted(() => {
  cargarUsuarios()
})

const editarUsuario = (usuario) => {
  editado.value = { ...usuario }
  dialog.value = true
}

const guardarEdicion = async () => {
  try {
    await axios.put(`/usuarios/${editado.value.id}`, editado.value)
    dialog.value = false
    cerrarDialogo()
    Swal.fire({ icon: 'success', title: 'Usuario actualizado' })
    dialog.value = false
    cargarUsuarios()
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar' +err })
  }
}

const eliminarUsuario = async (id) => {
  cerrarDialogo()
  const confirm = await Swal.fire({
    title: '¿Eliminar usuario?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    customClass: {
    popup: 'swal-over-dialog'
  }
  })
  if (!confirm.isConfirmed) return

  try {
    await axios.delete(`/usuarios/${id}`)
    Swal.fire({ icon: 'success', title: 'Usuario eliminado' })
    cargarUsuarios()
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar el usuario' +err })
  }
}
</script>

<style scoped>
.swal2-container {
  z-index: 99999 !important;
}

.v-data-table {
  font-size: 0.9rem;
}
</style>

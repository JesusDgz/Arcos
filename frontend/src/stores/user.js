import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    usuario: null,
    token: null
  }),
  actions: {
    setUser(usuario) {
      this.usuario = usuario
      localStorage.setItem('usuario', JSON.stringify(usuario))
    },
    setToken(token) {
      this.token = token
      localStorage.setItem('token', token)
    },
    cargarDesdeStorage() {
      const token = localStorage.getItem('token')
      const usuario = localStorage.getItem('usuario')
      if (token && usuario) {
        this.token = token
        this.usuario = JSON.parse(usuario)
      }
    },
    logout() {
      this.usuario = null
      this.token = null
      localStorage.removeItem('token')
      localStorage.removeItem('usuario')
    }
  }
})

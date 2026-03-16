import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'



export const useAuthStore = defineStore({
    id: import.meta.env.VITE_AUTH_STORE_ID,
    state: () => ({
        authenticated: false,
        permissions: [],
        homepage: 'home',
        user: {},
        filtresFromCookies:{}
    }),
    actions: {
        async login() {
            try {
                const { data } = await axios.get('/api/user')
                const permissions = data.role.permissions.map(element => element.slug)
                this.user = data
                this.permissions = permissions
                this.authenticated = true

                router.push({ name: 'home' })
            } catch (error) {
                this.user = {}
                this.permissions = []
                this.authenticated = false
            }
        },
        async authcheck() {
            try {
                const { data } = await axios.get('/api/user')
                const permissions = data.role.permissions.map(element => element.slug)
                
                this.user = data
                this.permissions = permissions
                this.authenticated = true
            } catch (error) {
                this.user = {}
                this.permissions = []
                this.authenticated = false
                
                router.push({ name: 'login' })
            }
        },
        logout() {
            this.user = {}
            this.permissions = []
            this.authenticated = false
            
            router.push({ name: 'login' })
        },
        storeFiltres(data){
            this.filtresFromCookies = data
        },
    },
    // Ajout de la persistance
    persist: {
        enabled: true,
        strategies: [
          {
            key: import.meta.env.VITE_AUTH_STORE_ID,
            storage: localStorage
          }
        ]
      }
})
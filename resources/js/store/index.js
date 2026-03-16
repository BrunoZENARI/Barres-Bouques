import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const pinia = createPinia()

// Ajouter le plugin de persistance
pinia.use(piniaPluginPersistedstate)

export default pinia

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { IonicVue } from '@ionic/vue'
import router from './router'
import App from './App.vue'
import Toast from 'vue-toastification'

// Import Ionic CSS
import '@ionic/vue/css/core.css'
import '@ionic/vue/css/normalize.css'
import '@ionic/vue/css/structure.css'
import '@ionic/vue/css/typography.css'
import '@ionic/vue/css/padding.css'
import '@ionic/vue/css/float-elements.css'
import '@ionic/vue/css/text-alignment.css'
import '@ionic/vue/css/text-transformation.css'
import '@ionic/vue/css/flex-utils.css'
import '@ionic/vue/css/display.css'

// Import custom styles
import './styles/main.scss'
import './styles/variables.scss'

// Import toast styles
import 'vue-toastification/dist/index.css'

const app = createApp(App)

// Configure Pinia store
const pinia = createPinia()

// Configure Toast
const toastOptions = {
  position: 'top-right' as const,
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false,
}

app.use(IonicVue)
app.use(pinia)
app.use(router)
app.use(Toast, toastOptions)

router.isReady().then(() => {
  app.mount('#app')
})

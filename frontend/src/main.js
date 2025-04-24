import 'vue-select/dist/vue-select.css'
import 'bootstrap'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import VueSelect from 'vue-select'

const app = createApp(App)
app.component('v-select', VueSelect)
app.use(router)
app.mount('#app')

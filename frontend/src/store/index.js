import { reactive } from 'vue'

const store = reactive({
  apiUrl: 'http://127.0.0.1:8000',
  apiHeaders: {
    'Content-Type': 'application/json'
  },
  survey: null,
  score: null
})

export default store;



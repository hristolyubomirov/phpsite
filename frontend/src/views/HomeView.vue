<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import languages from '@/data/languages.json';
import store from '../store';

const router = useRouter();
const genders = [
  { name: 'Male', code: 1 },
  { name: 'Female', code: 2 },
];
const language = ref(null);
const gender = ref(null);
const loading = ref(false);
const surveyLoaded = ref(false);



async function startSurvey() {
  localStorage.setItem('userLanguage', JSON.stringify(language.value))
  localStorage.setItem('userGender', JSON.stringify(gender.value))


  try {
    loading.value = true


    const response = await fetch(store.apiUrl + '/survey', {
      method: 'POST',
      headers: store.apiHeaders,
      body: JSON.stringify({
        userLanguage: language.value?.code,
        userGender: gender.value?.code
      })
    })


    const result = await response.json()


    if (response.ok) {
      store.survey = result
      surveyLoaded.value = true
    } else {
      console.error(`❌ Survey creation failed [${response.status}]`)
      console.error('🔍 Server says:', result?.error || 'No error message from server')
    }
  } catch (error) {
    console.error('❌ Network or unexpected error:', error)
  } finally {
    loading.value = false
  }
}







function playDemo() {
  let word =
    "ærɜːliːs'jʊ    eŋiːvaʊs'sɔɪ    'ʊəmɪfɔɪkɜːl    pɜːtʃaɪtiː'mæ    deəəzsu'ʃeə    'fɑːɪfkiːgeɪ    'baɪefrɪʌm    ərʃɒ'sæbaɪ    ʊəd'wekeaʊs"
  console.log(word)
  process(word)
}

function continueSurvey() {
  router.push('/stageOne'); // Redirect to the next stage
}

onMounted(() => {
  // Retrieve stored user data if available
  const savedLanguage = JSON.parse(localStorage.getItem('userLanguage'));
  const savedGender = JSON.parse(localStorage.getItem('userGender'));

  if (savedLanguage && savedGender) {
    language.value = savedLanguage;
    gender.value = savedGender;
  }
});
</script>

<template>
  <div class="container-sm text-center mt-5">
    <div class="row justify-content-center">
      <div v-if="!surveyLoaded" class="col-12 col-lg-8 col-xl-6">
        <p>
          Thank you for participating in our survey designed to understand emotional and sensitive
          hearing.<br />
          Please follow the instructions.
        </p>
        <div class="row">
          <div class="col-12 mb-3">
            <v-select
              placeholder="Your Native Language (required)"
              :options="languages"
              label="name"
              v-model="language"
            />
            <span v-if="!language" class="text-warning">It is required to specify your native language!</span>
          </div>
          <div class="col-12 mb-5">
            <v-select
              placeholder="Your Gender (required)"
              :options="genders"
              label="name"
              v-model="gender"
            />
            <span v-if="!gender" class="text-warning">It is required to specify your gender!</span>
          </div>
          <p>
            Imagine a situation where your friend is speaking in a language you do not understand.
          </p>
          <div class="col-12 mt-5">
            <button
              class="btn btn-success"
              :disabled="!language || !gender || loading"
              @click="startSurvey"
            >
              {{ loading ? 'Loading...' : 'Start Survey' }}
            </button>
            <p class="mt-2 text-warning">
              When you finish the survey, you will be able to see your score.
            </p>
          </div>
        </div>
      </div>

      <!-- New Screen After Survey Loaded -->
      <div v-else class="col-12 col-lg-8 col-xl-6">
        <p>
          You use an AI translator to understand your friend. Unfortunately, the translator breaks
          down and starts repeating what your friend says without any intonation or emotion, just
          like a machine.<br />
          Do you want to hear how that sounds?
        </p>
        <div
          class="d-inline-flex justify-content-center align-items-center my-3 ps-1"
          @click="playDemo"
        >
          <button class="btn btn-danger me-2">
            <i class="bi-play-circle-fill"></i>
          </button>
          <img class="soundwave" src="/images/soundwave.svg" />
        </div>
        <div class="col-12 mt-5">
          <button class="btn btn-success" @click="continueSurvey">
            Continue Survey
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
:deep() {
  --vs-controls-color: #fff;
  --vs-border-color: #fff;
  --vs-selected-color: #fff;
  --vs-dropdown-bg: #083f88;
  --vs-dropdown-max-height: 150px;
}
.soundwave {
  height: 50px;
}
.d-inline-flex {
  cursor: pointer;
}
</style>

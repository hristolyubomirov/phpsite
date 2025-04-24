<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import store from '../store'

const router = useRouter()
const props = defineProps(['index'])
const loading = ref(false)
const positiveImageUrl = ref(
  'images/' + store.survey['extraScreen' + props.index].positiveImage.filename
)
const negativeImageUrl = ref(
  'images/' + store.survey['extraScreen' + props.index].negativeImage.filename
)
const chosenImage = ref(store.survey['extraScreen' + props.index].chosenImage)
const chosenImageType = ref(null)
if(chosenImage?.value?.id === store.survey['extraScreen' + props.index].negativeImage.id)
  chosenImageType.value = 'negativeImage'
if(chosenImage?.value?.id === store.survey['extraScreen' + props.index].positiveImage.id)
  chosenImageType.value = 'positiveImage'

let sentence = ''
for (let j = 1; j <= 3; j++) {
  let word = ''
  for (let i = 1; i <= 5; i++) {
    if (store.survey['extraScreen' + props.index].sentence['word' + j].stressedBiph === i)
      word += "'"
    if (store.survey['extraScreen' + props.index].sentence['word' + j]['biph' + i] !== null)
      word += store.survey['extraScreen' + props.index].sentence['word' + j]['biph' + i].syllable
  }
  sentence += word
  if (j !== 3) sentence += '  '
}

function play(sentence) {
  console.log(sentence)
  process(sentence)
}

function nextOrSubmit() {
  store.survey['extraScreen' + props.index].chosenImage = chosenImage.value
  let url = '/extrascreen/' + (parseInt(props.index) + 1)
  if (props.index < 2) router.push(url)
  else submit()
}

function backIfAllowed() {
  let url = '/extrascreen/' + (parseInt(props.index) - 1)
  if (parseInt(props.index) > 1) router.push(url)
  else router.push('/stageTwo')
}

async function submit() {
  try {
    loading.value = true
    const response = await fetch(store.apiUrl + '/survey/' + store.survey.id, {
      method: 'PATCH',
      headers: store.apiHeaders,
      body: JSON.stringify(store.survey)
    })
    if (response.ok) {
      store.survey = await response.json()
      router.push('/stageThree')
    } else {
      console.log('FINISH SURVEY ERROR STATUS ' + response.status)
      alert("SUBMIT FAILED! Please try again!")
      loading.value = false
    }
  } catch (error) {
    console.log(error)
    alert("SUBMIT FAILED! Please try again!")
    loading.value = false
  }
}
</script>

<template>
  <div class="container-sm text-center mt-4">
    <div class="row justify-content-center align-items-center">
      <div class="col-12">
        <p class="mb-0"><b>{{'Task ' + (parseInt(props.index) + 4) + '/6'}}</b></p>
        <div class="d-inline-flex align-items-center my-3 ps-1" @click="play(sentence)">
          <span><b>He said</b></span>
          <button class="btn btn-danger mx-2">
            <i class="bi-play-circle-fill"></i>
          </button>
          <img class="soundwave" src="/images/soundwave.svg" />
        </div>
      </div>
      <span>Now, you have to choose an image:</span>
      <div
        class="col-12 col-md-6"
        :id="
          chosenImage?.id === store.survey['extraScreen' + props.index].positiveImage.id
            ? 'chosen'
            : ''
        "
        @click="
          chosenImageType = 'positiveImage';
          chosenImage = store.survey['extraScreen' + props.index].positiveImage;
          store.survey['extraScreen' + props.index].chosenImage = chosenImage;
        "
      >
        <img class="extraScreenImage mt-2" :src="positiveImageUrl" />
        <input class="col-12 mt-3" type="radio" value="positiveImage" v-model="chosenImageType" />
      </div>
      <div
        class="col-12 col-md-6"
        :id="
          chosenImage?.id === store.survey['extraScreen' + props.index].negativeImage.id
            ? 'chosen'
            : ''
        "
        @click="
          chosenImageType = 'negativeImage';
          chosenImage = store.survey['extraScreen' + props.index].negativeImage;
          store.survey['extraScreen' + props.index].chosenImage = chosenImage;
        "
      >
        <img class="extraScreenImage mt-2" :src="negativeImageUrl" />
        <input class="col-12 mt-3" type="radio" value="negativeImage" v-model="chosenImageType" />
      </div>
      <div class="col-12 mt-5 text-center">
        <button class="btn btn-warning me-3" :disabled="loading === true" @click="backIfAllowed">
          Back
        </button>
        <button
          class="btn btn-success"
          :disabled="chosenImage === null || loading === true"
          @click="nextOrSubmit"
        >
          {{ loading ? 'Loading...' : parseInt(props.index) === 2 ? 'Finish' : 'Continue' }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.extraScreenImage {
  max-width: 100%;
  cursor: pointer;
  height: 50vh;
}
.soundwave {
  height: 50px;
}
#chosen > img {
  border: 4px solid #dc3545;
}
.d-inline-flex {
  cursor: pointer;
}
</style>

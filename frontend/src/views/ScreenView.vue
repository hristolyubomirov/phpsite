<template>
  <div v-if="currentScreen" class="container-sm text-center mt-5">
    <div class="row justify-content-center align-items-center">
      <p><b>{{ 'Task ' + props.index + ' / ' + store.survey.screens.length }}</b></p>
      <div class="col-12 col-md-6">
        <span class="text-above-image"><b>Your friend saw this picture and said...</b></span>
        <img
          id="screenImage"
          :src="currentScreen?.image?.filename ? '/images/' + currentScreen.image.filename : ''"
          alt="Survey Image"
          v-if="currentScreen?.image?.filename"
          class="survey-image mt-2"
        />
        <p v-else>Image not available</p>
      </div>
      <div class="col-12 col-md-6 mt-4">
        <span class="instruction-text">
          <b>Your friend saw this picture and said... (please choose one option)</b>
        </span>

        <div
          v-if="currentScreen?.positiveWord"
          class="option-container"
          :class="{ chosen: chosenWord === currentScreen?.positiveWord }"
          @click="selectWord(currentScreen.positiveWord)"
        >
          <input type="radio" class="me-3" :value="currentScreen?.positiveWord" v-model="chosenWord" />
          <button class="btn btn-danger me-2">
            <i class="bi-play-circle-fill"></i>
          </button>
          <img class="soundwave" src="/images/soundwave.svg" />
        </div>
        <div
          v-if="currentScreen?.negativeWord"
          class="option-container"
          :class="{ chosen: chosenWord === currentScreen?.negativeWord }"
          @click="selectWord(currentScreen.negativeWord)"
        >
          <input type="radio" class="me-3" :value="currentScreen?.negativeWord" v-model="chosenWord" />
          <button class="btn btn-danger me-2">
            <i class="bi-play-circle-fill"></i>
          </button>
          <img class="soundwave" src="/images/soundwave.svg" />
        </div>
        <span class="instruction-text">
          You can listen to the words as many times as you wish, but you must choose one of them
          that you think fits best.
        </span>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-warning" @click="backIfAllowed">Back</button>
      <button
        v-if="props.index < store.survey.screens.length"
        class="btn btn-success"
        :disabled="!chosenWord"
        @click="nextOrSubmit"
      >
        Continue
      </button>
      <button
        v-else
        class="btn btn-primary"
        :disabled="!chosenWord"
        @click="finishSurvey"
      >
        Finish
      </button>
    </div>
  </div>
  <div v-else>
    <p>Survey data is missing. Please reload or restart the survey.</p>
  </div>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import store from '../store';

const router = useRouter();
const props = defineProps(['index']);

const currentScreen = computed(() => {
  if (!store.survey || !store.survey.screens || store.survey.screens.length === 0) {
    console.error('Screens data is missing or empty in the survey.');
    return null;
  }
  return store.survey.screens[props.index - 1] || null;
});

const chosenWord = ref(null);


function play(word) {
  // Извличаме стойност от Proxy ако е обект
  let textToSpeak = word;


  // Ако думата е reactive обект с .ipa или .word
  if (typeof word === 'object' && word !== null) {
    if ('ipa' in word) {
      textToSpeak = word.ipa;
    } else if ('word' in word) {
      textToSpeak = word.word;
    } else {
      console.warn('⚠️ Обект без .ipa или .word:', word);
      textToSpeak = ''; // fallback за безопасност
    }
  }


  // Уверяваме се, че подаваме string
  if (typeof textToSpeak === 'string') {
    console.log('🎧 Playing word:', textToSpeak);
    process(textToSpeak); // Вече безопасно
  } else {
    console.error('❌ Не може да се възпроизведе — невалиден текст:', textToSpeak);
  }
}


function selectWord(word) {
  console.log('Selected word:', word);
  if (word) {
    chosenWord.value = word;
    currentScreen.value.chosenWord = word;
    play(word);
  } else {
    console.error('Selected word is null or undefined');
  }
}







function nextOrSubmit() {
  if (currentScreen.value) {
    currentScreen.value.chosenWord = chosenWord.value;

    const nextIndex = parseInt(props.index) + 1;
    if (nextIndex <= store.survey?.screens?.length) {
      router.push('/screen/' + nextIndex);
    }
  } else {
    console.error('Current screen is not available');
  }
}

//function finishSurvey() {
//  store.survey.score = calculateScore();
//  router.push('/stageThree');
//}

async function finishSurvey() {
  console.log("🚀 Starting survey submission...");

  const surveyId = store.survey.id;
  const screens = store.survey.screens;

  if (!surveyId || !screens || screens.length === 0) {
    console.error("❌ Survey ID or screens missing. Make sure the survey was created correctly.");
    return;
  }

  store.survey.score = calculateScore();

  // 1. Изпращаме текстовете на думите, за да получим съответните ID-та от backend
  const wordTexts = screens.map(screen => ({
    screenId: screen.id,
    chosenWordText: screen.chosenWord
  }));

  console.log("📤 Sending words to backend for ID resolution:", JSON.stringify(wordTexts, null, 2));

  const wordIdResponse = await fetch(`${store.apiUrl}/resolve-words`, {
    method: "POST",
    headers: {
      ...store.apiHeaders,
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ words: wordTexts })
  });

  if (!wordIdResponse.ok) {
    console.error("❌ Failed to resolve word IDs. Status:", wordIdResponse.status);
    return;
  }

  const resolvedWords = await wordIdResponse.json();
  console.log("✅ Resolved words:", resolvedWords);

  // 2. Изграждаме финалния body за изпращане към /survey/:id/finish
  const requestBody = {
    screens: screens.map(screen => {
      const screenScore =
        (screen.image.positive && screen.chosenWord === screen.positiveWord) ||
        (!screen.image.positive && screen.chosenWord === screen.negativeWord)
          ? 1
          : 0;

      return {
        id: screen.id,
        chosenWord: resolvedWords[screen.id] ? { id: resolvedWords[screen.id] } : null,
        score: screenScore
      };
    })
  };

  console.log("📤 Sending final request to /survey/:id/finish", JSON.stringify(requestBody, null, 2));

  // 3. Изпращаме PATCH заявка към новия RESTful endpoint
  const response = await fetch(`${store.apiUrl}/survey/${surveyId}/finish`, {
    method: "PATCH",
    headers: {
      ...store.apiHeaders,
      "Content-Type": "application/json"
    },
    body: JSON.stringify(requestBody)
  });

  if (response.ok) {
    console.log("✅ Survey updated successfully!");
    router.push("/stageThree");
  } else {
    console.error("❌ Failed to update survey. Status:", response.status);
  }
}

























function backIfAllowed() {
  const prevIndex = parseInt(props.index) - 1;
  if (prevIndex > 0) {
    router.push('/screen/' + prevIndex);
  } else {
    router.push('/stageOne');
  }
}

function calculateScore() {
  return store.survey.screens.reduce((score, screen) => {
    if (
      (screen.image.positive && screen.chosenWord === screen.positiveWord) || 
      (!screen.image.positive && screen.chosenWord === screen.negativeWord)
    ) {
      return score + 1;
    }
    return score;
  }, 0);
}
</script>

<style scoped>
.text-above-image {
  display: block;
  font-size: 1.2rem;
  margin-bottom: 10px;
}

.survey-image {
  max-height: 400px;
  max-width: 100%;
  margin: 0 auto;
  display: block;
}

.instruction-text {
  display: block;
  margin-bottom: 20px;
  font-size: 1.1rem;
  font-weight: 500;
}

.soundwave {
  height: 50px;
}

.option-container {
  display: flex;
  align-items: center;
  border: 1px solid #ccc;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.chosen {
  border: 2px solid #007bff;
}

.btn {
  margin: 0 10px;
}
</style>

<template>
  <div class="container-sm text-center mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-8 col-xl-6">
        <h1 class="mb-3">Thank you!</h1>
        <h3 class="mb-5">
          Your score is <span>{{ store.survey.score }}</span
          >/{{ store.survey.screens.length }}
        </h3>
        <p>
          Your contribution will help us evaluate and understand the emotional and sensitive hearing
          of humans!
        </p>
        <p>
          If you want to try again, you can start a new survey with the button bellow or by
          refreshing this page!
        </p>
        <div class="row">
          <div class="col-12 mt-3">
            <button
              class="btn btn-success"
              @click="
                store.survey = { screens: [], score: null };
                router.push('/');
              "
            >
              New Survey
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import store from '../store';

const router = useRouter();
</script>

<style scoped>
span {
  color: #dc3545;
}
</style>

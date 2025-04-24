<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
  <div class="container-sm text-center mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-8 col-xl-6">
        <h3>Part Two</h3>
        <img class="my-5" width="100%" src="/images/stage2.png" />
        <p>
          While you are viewing a page in an album that contains two pictures, your friend tells you
          something. He then wants you to show him which of the two pictures below you think
          corresponds to the sentence he gave you.
          <br />
          Guess which image corresponds to the sentence, based on what you hear from the broken
          translator.
        </p>
        <p>
          Please proceed by providing your responses based on the above scenario.
          <br />
          Your input is valuable in helping us assess your emotional and sensitive hearing
          capabilities.
        </p>
        <div class="row">
          <div class="col-12 mt-5">
            <button class="btn btn-warning me-3" @click="router.push('/screen/4')">Back</button>
            <button class="btn btn-success" @click="router.push('/extrascreen/1')">Continue</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>

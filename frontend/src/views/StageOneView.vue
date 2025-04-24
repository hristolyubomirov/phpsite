<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
  <div class="container-sm text-center mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-8 col-xl-6">
        <h3>Part One</h3>
        <img class="my-5" width="100%" src="/images/stage1.png" />
        <p>
          <b>Your friend shows you images from an album and comments on them.</b><br />
          You will have 8 tasks where you need to guess which word corresponds to a given image, based on what you hear from the broken translator.
        </p>
        <p>
          <b>Please proceed by providing your responses based on the above scenario.</b>
          <br />
         Your input is valuable in helping us assess your emotional and sensitive hearing capabilities.
        </p>
        <div class="row">
          <div class="col-12 mt-5">
            <button class="btn btn-success" @click="router.push('/screen/1')">Continue</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>

<script setup>
import { RouterView } from 'vue-router'

function startExport() {
  //window.location.href = 'https://api.cscb634.pwpwebhosting.com/cscb634-export'
  window.location.href = 'http://localhost:8000/cscb634-export'
}
</script>

<template>
  <div class="text-center fs-2 sticky-top py-3" id="header">
    Sound-Emotion test hearing capability
  </div>
  <RouterView :key="$route.fullPath" />
  <div class="bg-white text-center text-black sticky-bottom mt-5">
    <div class="d-flex justify-content-center py-1">
      <img
        class="m-2"
        width="30px"
        src="https://cscb532.pwpwebhosting.com/7a4a19c7acf563d4367b.jpeg"
        @click="startExport"
      />
      <div>
        <a
          class="link-dark link-underline-opacity-0 link-underline-opacity-75-hover"
          href="https://www.nbu.bg/"
          target="_blank"
          >©2024 New Bulgarian University</a
        >
        <br />
        <a
          class="link-dark link-underline-opacity-0 link-underline-opacity-75-hover"
          href="https://computerscience.nbu.bg/"
          target="_blank"
          >Department of computer science</a
        >
      </div>
    </div>
  </div>
</template>

<style lang="scss">
@import 'bootstrap';
@import 'bootstrap-icons/font/bootstrap-icons.css';

#app {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 100vh;
}
html {
  background-image: url('/images/wallpaper.jpg');
  background-size: cover;
  background-repeat: no-repeat;
}
body,
#header {
  background-color: #083f88f2;
}
#header {
  border-bottom: 1px solid white;
}
</style>

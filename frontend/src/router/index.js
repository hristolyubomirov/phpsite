// import ExtraScreenView from '../views/ExtraScreenView.vue';
// import StageTwoView from '../views/StageTwoView.vue';

import { createRouter, createMemoryHistory } from 'vue-router';
import ScreenView from '../views/ScreenView.vue';
import HomeView from '../views/HomeView.vue';
import StageOneView from '../views/StageOneView.vue';
import StageThreeView from '../views/StageThreeView.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/stageOne',
    name: 'stageOne',
    component: StageOneView,
  },
  {
    path: '/screen/:index',
    name: 'screen',
    component: ScreenView,
    props: true,
  },
  {
    path: '/stageThree',
    name: 'stageThree',
    component: StageThreeView
  }
];

const router = createRouter({
  history: createMemoryHistory(),
  routes,
});

export default router;
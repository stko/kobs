import Vue from 'vue'
import Vuetify, {
  VApp, // required
  VNavigationDrawer,
  VFooter,
  VToolbar,
  VToolbarTitle,
  VSpacer,
  VBtn,
  VContent,
  VContainer,
  VLayout,
  VFlex,
  VImg,
  VFadeTransition
} from 'vuetify/lib'

import 'vuetify/src/stylus/app.styl'

Vue.use(Vuetify, {
  iconfont: 'md',
  components: {
    VApp,
    VNavigationDrawer,
    VFooter,
    VToolbar,
    VToolbarTitle,
    VSpacer,
    VBtn,
    VContent,
    VContainer,
    VLayout,
    VFlex,
    VImg,
    VFadeTransition
  }
})

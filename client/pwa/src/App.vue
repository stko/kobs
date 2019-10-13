<template>
  <v-app>
    <v-toolbar app>
      <img class="mr-3" :src="require('@/assets/Klobslogo.svg')" height="40"/>
      <v-toolbar-title class="headline text-uppercase">
        <span>Klobs</span>
        <span class="font-weight-light">Anwesenheiten</span>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <!--v-btn
        flat
        href="https://github.com/stko/kobs"
        target="_blank"
      >
        <span class="mr-2">Latest Release</span>
      </v-btn-->
    </v-toolbar>

    <v-content>
      <router-view></router-view>
    </v-content>
  </v-app>
</template>

<script>
import Router from 'vue-router'

export default {
  name: 'App',
  components: {
    Router
  },
  data () {
    return {
      //
    }
  }
}
</script>

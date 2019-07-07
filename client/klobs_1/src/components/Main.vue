<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <router-link to="/newewent">Navigate to Newevent</router-link>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2New()">Push to Newevent</a>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2Edit()">Push to Edit</a>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2Set()">Push to Set</a>
  </div>
</template>

<script>
import router from '../router'
export default {
  name: 'Main',
  data () {
    return {
      msg: 'Main page'
    }
  },
  methods: {
    nav2New () {
      router.push({ name: 'Newevent' })
    },
    nav2Set () {
      router.push({ name: 'Settings' })
    },
    nav2Edit () {
      router.push({ name: 'Edit', params: { id: '999' } })
    }
  }
}
</script>

<style scoped>
h1,
h2 {
  font-weight: normal;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: #42b983;
}
</style>

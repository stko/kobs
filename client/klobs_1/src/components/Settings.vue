<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="navBack()">Pop to Main</a>
  </div>
</template>

<script>
import router from '../router'
export default {
  name: 'Settings',
  data () {
    return {
      msg: 'Settings page'
    }
  },
  methods: {
    navBack () {
      router.go(-1)
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

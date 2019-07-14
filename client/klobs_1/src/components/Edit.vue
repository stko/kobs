<template>
  <div class="hello">
    <h1>{{ id }}  {{ msg }}</h1>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2Main()">Navigate to Main</a>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2Add()">Push to Add</a>
  </div>
</template>

<script>
import router from '../router'
export default {
  name: 'Edit',
  data () {
    return {
      id: 0,
      msg: 'Edit page'
    }
  },
  created () {
    this.id = this.$route.params.id
  },
  methods: {
    nav2Main () {
      router.push({ name: 'Main' }) // always goes 'back enough' to Main
    },
    nav2Add () {
      router.push({ name: 'Add', params: { id: '123' } })
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

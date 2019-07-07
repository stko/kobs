<template>
  <div class="hello">
    <h1>{{ id }}  {{ msg }}</h1>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="navBack()">Pop to Edit</a>
  </div>
</template>

<script>
import router from '../router'
export default {
  name: 'Add',
  data () {
    return {
      id: 0,
      msg: 'Add page'
    }
  },
  created () {
    this.id = this.$route.params.id
  },
  methods: {
    navBack () {
      router.go(-1) // back to Edit
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

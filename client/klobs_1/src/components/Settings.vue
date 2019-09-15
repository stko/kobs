<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    Benutzername <input v-model="user" placeholder="Dein User Name">
    <br/>
    Passwort <input v-model="pw" placeholder="Dein Passwort">
    <br/>

    <a style="cursor: pointer; text-decoration: underline" v-on:click="navBack()">Pop to Main</a>
  </div>
</template>

<script>
import router from '../router'
export default {
  name: 'Settings',
  data () {
    return {
      msg: 'Settings page',
      user: '',
      pw: '',
      md5pw: ''
    }
  },
  methods: {
    navBack () {
      router.go(-1)
    }
  },
  mounted () {
    if (localStorage.user) {
      this.user = localStorage.user
    }
    if (localStorage.pw) {
      this.pw = localStorage.pw
    }
  },
  watch: {
    user (newUser) {
      localStorage.user = newUser
    },
    pw (newPw) {
      localStorage.pw = newPw
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

<template>
  <v-container>
    <h1>Deine Login Daten</h1>
    <v-form v-model="valid" ref="form">
      <v-text-field
      v-model="user"
      label="Dein User Name"
      required
      ></v-text-field>
      <br/>
      <v-text-field
      v-model="pw"
      label="Dein Passwort"
      required
      ></v-text-field>
    </v-form>
  </v-container>
</template>

<script>
export default {
  name: 'Settings',
  data () {
    return {
      user: '',
      pw: ''
    }
  },
  methods: {
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

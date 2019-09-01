<template>
  <v-layout row>
    <v-flex xs12 sm6 offset-sm3>
      <v-card>
        <v-toolbar color="light-blue" light extended>
          <template v-slot:extension>
            <v-toolbar-title class="white--text">Teilnehmer</v-toolbar-title>
          </template>
          <v-spacer></v-spacer>
          <!--
          <v-btn icon>
            <v-icon>search</v-icon>
          </v-btn>
          <v-btn icon>
            <v-icon>view_module</v-icon>
          </v-btn>
          -->
        </v-toolbar>
        <v-btn icon
          class="hidden-xs-only"
          @click="nav2Main()"
        >
          <v-icon>arrow_back</v-icon>
        </v-btn>

        <p>{{ id.location }} am {{ id.date }}</p>
        <p>Beginn:{{ id.starttime }} Dauer: {{ id.duration }} Min.</p>
        <p>Selected items:</p>
        <pre>{{ selected }}</pre>
        <v-list>
          <v-list-tile v-for="(user) in users" :key="user.usr_id" @click="">
            <v-list-tile-action>
              <v-checkbox v-model="selected" multiple :value="user" />
            </v-list-tile-action>
            <v-list-tile-content @click.capture.stop="toggleColor(user)">
              <v-list-tile-title>{{ user.last_name }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
import router from '../router'
export default {
  name: 'Edit',
  data () {
    return {
      id: 0,
      msg: 'Edit page',
      selected: [],
      users: [],
      colors: [
        { hex: '#f00', label: 'Red' },
        { hex: '#0f0', label: 'Green' },
        { hex: '#00f', label: 'Blue' }
      ]
    }
  },
  created () {
    this.id = this.$route.params.id
    try {
      this.users = window.klobsdata['userdata']
    } catch (error) {
      this.nav2Main()
    }
    this.users = window.klobsdata['userdata']
  },
  methods: {
    nav2Main () {
      router.push({ name: 'Main' }) // always goes 'back enough' to Main
    },
    toggleColor (user) {
      if (this.selected.includes(user)) {
        // Removing the user
        this.selected.splice(this.selected.indexOf(user), 1)
      } else {
        // Adding the user
        this.selected.push(user)
      }
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

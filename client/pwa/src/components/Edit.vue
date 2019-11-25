<template>
  <v-layout row>
    <v-flex xs12 sm6 offset-sm3>
      <v-card>
        <v-toolbar color="light-blue" light extended>
          <template v-slot:extension>
            <v-toolbar-title class="white--text">Teilnehmer ({{onsides}} Anwesende)</v-toolbar-title>
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
        <v-btn @click="nav2Main()" >Speichern</v-btn>
        <p>{{ id.location }} am {{ id.date }}</p>
        <p>Beginn:{{ id.starttime }} Dauer: {{ id.duration }} Min.</p>
        <!--
        <p>Selected items:</p>
        <pre>{{ selected }}</pre>
        -->
        <v-list>
          <v-list-tile v-for="(user) in users" :key="user.usr_id" @click="">
            <v-list-tile-action>
              <v-checkbox v-model="selected" multiple :value="user" />
            </v-list-tile-action>
            <v-list-tile-content @click.capture.stop="toggleColor(user)">
              <v-list-tile-title>{{ user.last_name }}, {{ user.first_name }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
        <v-btn @click="nav2Main()" >Speichern</v-btn>
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
  computed: {
    onsides: function () {
      return this.selected.length.toString()
    }
  },
  created () {
    this.id = this.$route.params.id
    // first we fill a lookup to see which users are actual already selected
    var lookup = []
    if (this.id.training) {
      console.log('training record found')
      for (var entry of this.id.training) {
        console.log('add selection for ', entry.usr_id)
        lookup[entry.usr_id] = true
      }
    }
    try {
      this.users = window.klobsdata['userdata']
    } catch (error) {
      this.nav2Main()
    }
    // copy reference for all already selected users into the select table
    for (var user of this.users) {
      if (lookup[user.usr_id]) {
        this.selected.push(user)
      }
    }
  },
  methods: {
    nav2Main () {
      /* to not have to check each user against each selection,
      let's fill a crossrefence lookup first..
      */
      var lookup = []
      for (var user of this.selected) {
        lookup[user.usr_id] = true // True stands for "New added", initially
      }
      // now we add the previous selections
      // as we need the index for a later deletion, we use
      // the old fashion for() type
      if (!this.id.training) {
        this.id['training'] = []
      }
      // for easier deletion, we count the loop downwards
      for (let i = this.id['training'].length - 1; i > -1; i--) {
        var entry = this.id['training'][i]
        if (!lookup.includes(entry.usr_id)) { // not selected anymore? delete it..
          this.id['training'].splice(this.id['training'].indexOf(entry.usr_id), 1)
        } else {
          lookup[entry.usr_id] = false // marked as been already included in the sessiondata
        }
      }
      // and finally we fill the training array with the new added users:
      for (const [userId, value] of Object.entries(lookup)) {
        if (value) {
          this.id['training'].push({usr_id: userId, typ: 1, subtyp: 0, trainerid: 1, starttime: this.id.starttime, duration: this.id.duration})
        }
      }
      // save changes to local storage
      localStorage.sessiondata = JSON.stringify(window.klobsdata['sessiondata'])
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

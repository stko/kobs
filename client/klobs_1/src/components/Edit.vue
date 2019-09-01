<template>
  <v-layout row>
    <v-flex xs12 sm6 offset-sm3>
      <v-card>
        <v-list>
          <v-list-tile v-for="(color) in users" :key="color.usr_id" @click="">
            <v-list-tile-action>
              <v-checkbox v-model="selected" multiple :value="color" />
            </v-list-tile-action>
            <v-list-tile-content @click.capture.stop="toggleColor(color)">
              <v-list-tile-title>{{ color.last_name }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
        <p>Selected items:</p>
        <pre>{{ selected }}</pre>
        <h1>{{ id }}  {{ msg }}</h1>
        <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2Main()">Navigate to Main</a>
        <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2Add()">Push to Add</a>
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
    this.users = window.klobsdata['userdata']
  },
  methods: {
    nav2Main () {
      router.push({ name: 'Main' }) // always goes 'back enough' to Main
    },
    nav2Add () {
      router.push({ name: 'Add', params: { id: '123' } })
    },
    toggleColor (color) {
      if (this.selected.includes(color)) {
        // Removing the color
        this.selected.splice(this.selected.indexOf(color), 1)
      } else {
        // Adding the color
        this.selected.push(color)
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

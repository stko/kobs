<template>
  <v-layout wrap>
    <v-flex xs12 sm6 md4>
      <v-menu
        ref="menu"
        v-model="menu"
        :close-on-content-click="false"
        :return-value.sync="date"
        transition="scale-transition"
        offset-y
        full-width
        min-width="290px"
      >
        <template v-slot:activator="{ on }">
          <v-text-field
            v-model="date"
            label="Picker in menu"
            prepend-icon="event"
            readonly
            v-on="on"
          ></v-text-field>
        </template>
        <v-date-picker v-model="date" no-title scrollable>
          <v-spacer></v-spacer>
          <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
          <v-btn text color="primary" @click="$refs.menu.save(date)">OK</v-btn>
        </v-date-picker>
      </v-menu>
    </v-flex>
    <v-spacer></v-spacer>
    <v-flex xs12 sm6 md4>
      <v-menu
        ref="timemenu"
        v-model="time"
        :close-on-content-click="false"
        :return-value.sync="time"
        transition="scale-transition"
        offset-y
        full-width
        min-width="290px"
      >
        <template v-slot:activator="{ on }">
          <v-text-field
            v-model="time"
            label="Picker in menu"
            prepend-icon="event"
            readonly
            v-on="on"
          ></v-text-field>
        </template>
        <!-- <v-time-picker v-model="time" no-title scrollable> -->
        <v-time-picker v-model="time" no-title scrollable>
          <v-spacer></v-spacer>
          <v-btn text color="primary" @click="timemenu = false">Cancel</v-btn>
          <v-btn text color="primary" @click="$refs.timemenu.save(time)">OK</v-btn>
        </v-time-picker>
      </v-menu>
    </v-flex>
    <v-spacer></v-spacer>
    <select v-model="locations">
      <option disabled value="">Please select one</option>
      <option v-for="location in locations" v-bind:key="location.id">
    {{ location.name }}
      </option>
      <option>A</option>
      <option>B</option>
      <option>C</option>
    </select>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="navBack()">Navigate to Main</a>
  </v-layout>
</template>

<script>
import router from '../router'
export default {
  name: 'Newevent',
  data () {
    return {
      msg: 'New Event page',
      time: null,
      duration: null,
      locations: window.klobsdata.locations,
      date: new Date().toISOString().substr(0, 10),
      menu: false,
      timemenu: false
    }
  },
  methods: {
    navBack () {
      router.go(-1)
    },
    allowedStep: m => m % 15 === 0
  },
  beforeMount: function () {
    console.log('Newevent beforeMount', window.klobsdata.locations)
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

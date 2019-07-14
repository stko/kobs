<template>
  <v-layout row>
    <v-flex xs12 sm6 offset-sm3>
      <v-card>
        <v-toolbar color="light-blue" light extended>
          <v-toolbar-side-icon></v-toolbar-side-icon>
          <v-btn
            fab
            small
            color="cyan accent-2"
            bottom
            left
            absolute
            @click="dialog = !dialog"
          >
            <v-icon>add</v-icon>
          </v-btn>
          <template v-slot:extension>
            <v-toolbar-title class="white--text">Ereignisse</v-toolbar-title>
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
        <v-list two-line>
          <v-list-tile v-for="item in items" :key="item.title"  @click="nav2Edit">
            <v-list-tile-content>
              <v-list-tile-title>{{ item.title }}</v-list-tile-title>
              <v-list-tile-sub-title>{{ item.subtitle }}</v-list-tile-sub-title>
            </v-list-tile-content>
            <v-list-tile-action>
              <v-btn icon>
                <v-icon color="grey lighten-1">edit</v-icon>
              </v-btn>
            </v-list-tile-action>
          </v-list-tile>
        </v-list>
        <v-dialog v-model="dialog" max-width="500px">
          <v-card>
            <v-card-text>
              <v-text-field label="File name"></v-text-field>
              <small class="grey--text">* This doesn't actually save.</small>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn flat color="primary" @click="dialog = false">Submit</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-card>
    </v-flex>
  </v-layout>
  <!--
    <div class="hello">
    <h1>{{ msg }}</h1>
    <router-link to="/newewent">Navigate to Newevent</router-link>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2New()">Push to Newevent</a>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2Edit()">Push to Edit</a>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="nav2Set()">Push to Set</a>
  </div>
  -->
</template>

<script>
import router from '../router'
export default {
  name: 'Main',
  data () {
    return {
      msg: 'Main page',
      dialog: false,
      items: [
        { icon: 'folder', iconClass: 'grey lighten-1 white--text', title: 'Photos', subtitle: 'Jan 9, 2014' },
        { icon: 'folder', iconClass: 'grey lighten-1 white--text', title: 'Recipes', subtitle: 'Jan 17, 2014' },
        { icon: 'folder', iconClass: 'grey lighten-1 white--text', title: 'Work', subtitle: 'Jan 28, 2014' }
      ],
      items2: [
        { icon: 'assignment', iconClass: 'blue white--text', title: 'Vacation itinerary', subtitle: 'Jan 20, 2014' },
        { icon: 'call_to_action', iconClass: 'amber white--text', title: 'Kitchen remodel', subtitle: 'Jan 10, 2014' }
      ]
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

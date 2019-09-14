<template>
  <v-layout row>
    <v-flex xs12 sm6 offset-sm3>
      <v-card>
        <v-toolbar color="light-blue" light extended>
          <v-toolbar-side-icon @click="nav2Set()"></v-toolbar-side-icon>
          <v-btn
            fab
            small
            color="cyan accent-2"
            bottom
            left
            absolute
            @click="nav2New()"
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
          <v-list-tile v-for="item in items" :key="item.id"  @click="nav2Edit(item.ref)">
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
        <v-btn :disabled=sendIsDisabled>{{ sendButtonText }}</v-btn>
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
      sendButtonText: 'Offline',
      onLine: navigator.onLine,
      showBackOnline: false
    }
  },
  methods: {
    nav2New () {
      router.push({ name: 'Newevent' })
    },
    nav2Set () {
      router.push({ name: 'Settings' })
    },
    nav2Edit (item) {
      router.push({ name: 'Edit', params: { id: item } })
    },
    getLocations (data, self) {
      var res = []
      var orte = data.getElementsByTagName('orte')[0].children
      for (var ort of orte) {
        res.push({
          'id': ort.getElementsByTagName('ort_id')[0].childNodes[0].nodeValue,
          'name': ort.getElementsByTagName('name')[0].childNodes[0].nodeValue})
      }
      if (!window.klobsdata) {
        window.klobsdata = []
      }
      window.klobsdata['locations'] = res
      // console.log('locations', window.klobsdata.locations)
      return res
    },
    getUsers (data, self) {
      var res = []
      // var members = data.getElementsByTagName('members')[0].children
      var members = data.getElementsByTagName('members')[0]
      for (var member of members.childNodes) {
        res.push({
          'usr_id': member.getElementsByTagName('usr_id')[0].childNodes[0].nodeValue,
          'trainer': member.getElementsByTagName('trainer')[0].childNodes[0].nodeValue,
          'first_name': member.getElementsByTagName('first_name')[0].childNodes[0].nodeValue,
          'last_name': member.getElementsByTagName('last_name')[0].childNodes[0].nodeValue
        })
      }
      if (!window.klobsdata) {
        window.klobsdata = []
      }
      window.klobsdata['userdata'] = res
      // console.log('userdata', window.klobsdata.userdata)
      return res
    },
    fetchLocations: function (self) {
      // das mit dem Passwort steht hier: https://stackoverflow.com/questions/43842793/basic-authentication-with-fetch
      fetch('/static/locations.xml')
        .then(response => response.text())
        .then(str => (new window.DOMParser()).parseFromString(str, 'text/xml'))
        .then(data => this.getLocations(data, self))
        .catch(function (error) {
          console.log(error)
        })
    },
    fetchUsers: function (self) {
      // das mit dem Passwort steht hier: https://stackoverflow.com/questions/43842793/basic-authentication-with-fetch
      fetch('/static/userdata.xml')
        .then(response => response.text())
        .then(str => (new window.DOMParser()).parseFromString(str, 'text/xml'))
        .then(data => this.getUsers(data, self))
        .catch(function (error) {
          console.log(error)
        })
    },
    updateOnlineStatus (e) {
      const {
        type
      } = e
      this.onLine = type === 'online'
    }
  },
  watch: {
    onLine (v) {
      if (v) {
        this.showBackOnline = true
        setTimeout(() => {
          this.showBackOnline = false
        }, 1000)
      }
    }
  },

  beforeMount: function () {
    var self = this
    this.fetchLocations(self)
    this.fetchUsers(self)
    // },
    // mounted: function () {
    if (!window.klobsdata) {
      window.klobsdata = []
    }
    if (localStorage.getItem('sessiondata')) {
      try {
        window.klobsdata['sessiondata'] = JSON.parse(localStorage.getItem('sessiondata'))
      } catch (e) {
        localStorage.removeItem('sessiondata')
      }
    } else {
      console.log('simulate data')
      window.klobsdata['sessiondata'] = {
        'updates': [],
        'trainings': [
          {
            'location': 'Bremen',
            'locationid': '22',
            'date': '31.12.2017',
            'starttime': '10:40',
            'duration': '120'
          },
          {
            'location': 'Bremen',
            'locationid': '22',
            'date': '31.08.2019',
            'starttime': '12:40',
            'duration': '60',
            'training': [
              {
                'usr_id': '642',
                'typ': '1',
                'subtyp': '0',
                'trainerid': '1',
                'starttime': '12:40',
                'duration': '60'
              },
              {
                'usr_id': '386',
                'typ': '1',
                'subtyp': '0',
                'trainerid': '1',
                'starttime': '12:40',
                'duration': '60'
              }
            ]
          }
        ]
      }
    }
  },
  mounted () {
    window.addEventListener('online', this.updateOnlineStatus)
    window.addEventListener('offline', this.updateOnlineStatus)
  },
  beforeDestroy () {
    window.removeEventListener('online', this.updateOnlineStatus)
    window.removeEventListener('offline', this.updateOnlineStatus)
  },
  computed: {
    items: function () {
      var _items = []
      if (!window.klobsdata || !window.klobsdata['sessiondata']) {
        return _items
      }
      var sd = window.klobsdata['sessiondata']
      var count = 0
      for (var trainings of sd.trainings) {
        var item = { icon: 'folder', iconClass: 'grey lighten-1 white--text', title: 'Photos', subtitle: 'Jan 9, 2014' }
        item.subtitle = trainings.date
        item.title = trainings.location
        item.id = count++
        item.ref = trainings
        _items.push(item)
      }
      return _items
    },
    sendIsDisabled () {
      // evaluate whatever you need to determine disabled here...
      this.sendButtonText = 'computed'
      return !this.onLine
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

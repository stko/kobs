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
        <v-btn @click="sendToServer()" :disabled=sendIsDisabled>{{ sendButtonText }}</v-btn>
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
      showBackOnline: false,
      sessiondata: {'trainings': []}
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
    sendToServer () {
      window.klobsdata = []
      localStorage.removeItem('sessiondata')
      this.sessiondata = {'trainings': []}
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
      return res
    },
    getUsers (data, self) {
      var res = []
      var members = data.getElementsByTagName('members')[0].children
      for (var member of members) {
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
      return res
    },
    fetchData: function (self) {
      // das mit dem Passwort steht hier: https://stackoverflow.com/questions/43842793/basic-authentication-with-fetch
      var username = ''
      var pw = ''
      if (localStorage.user) {
        username = localStorage.user
      }
      if (localStorage.pw) {
        pw = localStorage.pw
      }
      var url = '../syncklobs.php'
      fetch(url,
        { method: 'POST',
          headers: {
            // 'Content-Type': 'text/xml; charset="utf-8"'
            'accept-charset': 'UTF-8',
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'usr_login_name=' + encodeURIComponent(username) + '&usr_password=' + encodeURIComponent(pw) + '&data=' + encodeURIComponent('')
        })
        .then(response => response.text())
        .then(str => (new window.DOMParser()).parseFromString(str, 'text/xml'))
        .then(data => {
          this.getUsers(data, self)
          this.getLocations(data, self)
        })
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
    this.fetchData(self)
    if (!window.klobsdata) {
      window.klobsdata = []
    }
    if (localStorage.getItem('sessiondata')) {
      try {
        window.klobsdata['sessiondata'] = JSON.parse(localStorage.getItem('sessiondata'))
        this.sessiondata = window.klobsdata['sessiondata']
      } catch (e) {
        localStorage.removeItem('sessiondata')
      }
    } else {
      window.klobsdata['sessiondata'] = {
        'updates': [],
        'trainings': []
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
      var count = 0
      for (var trainings of this.sessiondata.trainings) {
        var item = { icon: 'folder', iconClass: 'grey lighten-1 white--text', title: 'Photos', subtitle: 'Jan 9, 2014' }
        item.subtitle = trainings.date + ' ' + trainings.starttime + ' ' + trainings.duration + ' min'
        item.title = trainings.location + '( ' + (trainings.training ? trainings.training.length.toString() : '0') + ')'
        item.id = count++
        item.ref = trainings
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

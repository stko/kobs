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
              <v-btn icon @click="nav2NewEdit(item.ref)">
                <v-icon color="grey lighten-1">edit</v-icon>
              </v-btn>
            </v-list-tile-action>
          </v-list-tile>
        </v-list>
        <v-btn @click="sendToServer()" :disabled=sendIsDisabled>{{ sendButtonText }}</v-btn>
        <v-dialog v-model="okDialog" max-width="500px">
          <v-card>
            <v-card-text>
              <v-text-field label="Sync erfolgreich"></v-text-field>
              <small class="grey--text">Daten wurden gespeichert</small>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn flat color="primary" @click="okDialog = false">Prima!</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="errorDialog" max-width="500px">
          <v-card>
            <v-card-text>
              <v-text-field label="Sync- Fehler"></v-text-field>
              <small class="grey--text">Daten konnten nicht übertragen werden</small>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn flat color="primary" @click="errorDialog = false">Schade...</v-btn>
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
      okDialog: false,
      errorDialog: false,
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
    nav2NewEdit (item) {
      router.push({ name: 'Newevent', params: { id: item } })
    },
    nav2Set () {
      router.push({ name: 'Settings' })
    },
    nav2Edit (item) {
      router.push({ name: 'Edit', params: { id: item } })
    },
    formatNode (tag, value) {
      return '<' + tag + '>' + value + '</' + tag + '>\n'
    },
    serializeSessiondata (sessionData) {
      var res = '<klobsdata>\n\t<updates />\n'
      for (var event of sessionData['trainings']) {
        if (event['training'] && event['training'].length > 0) {
          res += '\t<trainings>\n'
          res += '\t\t' + this.formatNode('location', event['location'])
          res += '\t\t' + this.formatNode('locationid', event['locationid'])
          res += '\t\t' + this.formatNode('date', event['date'])
          for (var training of event['training']) {
            res += '\t\t<training>\n'
            res += '\t\t\t' + this.formatNode('usr_id', training['usr_id'])
            res += '\t\t\t' + this.formatNode('typ', training['typ'])
            res += '\t\t\t' + this.formatNode('subtyp', training['subtyp'])
            res += '\t\t\t' + this.formatNode('trainerid', training['trainerid'])
            res += '\t\t\t' + this.formatNode('starttime', training['starttime'])
            res += '\t\t\t' + this.formatNode('duration', training['duration'])
            res += '\t\t</training>\n'
          }
          res += '\t</trainings>\n'
        }
      }
      res += '</klobsdata>\n'
      return res
    },
    sendToServer () {
      // eslint-disable-next-line
      if (false) { // demo mode
        // delete local stored data
        // window.klobsdata = []
        window.klobsdata['sessiondata']['trainings'] = []
        localStorage.removeItem('sessiondata')
        this.sessiondata = {'trainings': []}
      } else {
        this.syncData(self, this.serializeSessiondata(this.sessiondata))
      }
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
    syncData: function (self, sessionData) {
      // das mit dem Passwort steht hier: https://stackoverflow.com/questions/43842793/basic-authentication-with-fetch
      var username = ''
      var pw = ''
      if (localStorage.user) {
        username = localStorage.user
      } else {
        this.nav2Set()
      }
      if (localStorage.pw) {
        pw = localStorage.pw
      } else {
        this.nav2Set()
      }
      var userTriggeredSync = sessionData !== ''
      /* Dies ist der HTTP Request für das 'neue' Klobs
      */
      var url = '../syncklobs.php'
      fetch(url,
        { method: 'POST',
          headers: {
            // 'Content-Type': 'text/xml; charset="utf-8"'
            'accept-charset': 'UTF-8',
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'usr_login_name=' + encodeURIComponent(username) + '&usr_password=' + encodeURIComponent(pw) + '&data=' + encodeURIComponent(sessionData)
        })
        /* Ende der neuen Version */
      /* Dies ist der HTTP Request für das 'alte' Klobs mit MD5
      * /
      var MD5 = require('md5.js')
      var md5pw = new MD5().update(pw).digest('hex')
      console.log('md5', md5pw)
      var url = '../syncklobs.php' + '?user=' + encodeURIComponent(username) + '&pw=' + encodeURIComponent(md5pw)

      fetch(url,
        { method: 'POST',
          headers: {
            'Content-Type': 'text/xml; charset="utf-8"'
          },
          body: sessionData
        })
        /* Ende der alten Version */
        .then(response => response.text())
        .then(str => (new window.DOMParser()).parseFromString(str, 'text/xml'))
        .then(data => {
          this.getUsers(data, self)
          this.getLocations(data, self)
          if (sessionData !== '') { // type save non-equal check
            // sucessful sync, so delete local stored data
            // we we should delete users and locations ?!?!
            window.klobsdata['sessiondata']['trainings'] = []
            localStorage.removeItem('sessiondata')
            this.sessiondata = {'trainings': []}
            this.okDialog = true
          }
          console.log('successful sync :-)')
        })
        .catch(function (error) {
          console.log(error)
          if (userTriggeredSync) {
            this.errorDialog = true
          }
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
    this.syncData(self, '')
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
        item.title = trainings.location + ' (' + (trainings.training ? trainings.training.length.toString() : '0') + ')'
        item.id = count++
        item.ref = trainings
        item.ref = trainings
        _items.push(item)
      }
      return _items
    },
    sendIsDisabled () {
      // evaluate whatever you need to determine disabled here...
      if (this.onLine) {
        this.sendButtonText = 'Sync'
        // return this.sessiondata.trainings.length === 0 // disable if nothing to send
        return false
      } else {
        this.sendButtonText = 'Offline'
        return true // disabled
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

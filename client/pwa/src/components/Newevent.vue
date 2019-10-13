<template>
  <v-layout row>
    <v-flex xs12 sm6 offset-sm3>
      <v-card>
        <v-toolbar color="light-blue" light extended>
          <template v-slot:extension>
            <v-toolbar-title class="white--text">Neues Ereignis</v-toolbar-title>
          </template>
          <!--
          <v-spacer></v-spacer>
          <v-btn icon>
            <v-icon>search</v-icon>
          </v-btn>
          <v-btn icon>
            <v-icon>view_module</v-icon>
          </v-btn>
          -->
        </v-toolbar>
        <v-form v-model="valid" ref="form">
          <!--p>
          <label :for="date">Wähle das Datum</label>
          <br/-->
          <v-date-picker
            v-model="date"
            full-width
            :id="date">
          </v-date-picker>
          <!--p/>
          <label :for="time">Wähle die Anfangszeit</label>
          <br/-->
          <v-time-picker
            v-model="time"
            close-content-on-click=true
            :id="time"
            :allowed-minutes="allowedStep"
            full-width
            format="24hr">
          </v-time-picker>
          <v-select
            label="Wähle die Dauer"
            v-model='duration'
            :items='durationitems'
            box
            :rules="[(v) => !!v || 'Item is required']"
            required
            placeholder="Hier auswählen">
          </v-select>
          <v-select
            label="Wähle den Ort"
            v-model='location'
            :items='locations'
            item-text='name'
            item-value='id'
            box
            :rules="[(v) => !!v || 'Item is required']"
            required
            placeholder="Hier auswählen">
          </v-select>
          <v-btn @click="submit" :class="{ red: !valid, green: valid }">Speichern</v-btn>
        </v-form>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
import router from '../router'
export default {
  name: 'Newevent',
  data () {
    // var ticktime = Date.now()
    return {
      valid: false,
      // integer arith: rounds down to last full quarter hour
      // time: new Date(ticktime - ticktime % (15 * 60 * 1000)).toTimeString().substr(0, 5),
      time: this.time,
      // duration: 0,
      duration: this.duration,
      durationitems: [
        {'value': 15, 'text': '15 min'},
        {'value': 30, 'text': '30 min'},
        {'value': 45, 'text': '45 min'},
        {'value': 60, 'text': '1 Std'},
        {'value': 75, 'text': '1 Std 15 min'},
        {'value': 90, 'text': '1 Std 30 min'},
        {'value': 105, 'text': '1 Std 45 min'},
        {'value': 120, 'text': '2 Std'},
        {'value': 135, 'text': '2 Std 15 min'},
        {'value': 150, 'text': '2 Std 30 min'},
        {'value': 165, 'text': '2 Std 45 min'},
        {'value': 180, 'text': '3 Std'},
        {'value': 240, 'text': '4 Std'},
        {'value': 300, 'text': '5 Std'},
        {'value': 360, 'text': '6 Std'},
        {'value': 420, 'text': '7 Std'},
        {'value': 480, 'text': '8 Std'}
      ],
      // location: 0,
      location: this.location,
      locations: window.klobsdata.locations,
      // date: new Date().toISOString().substr(0, 10)
      date: this.date
    }
  },
  watch: {
    locations: function (locations) {
      console.log('log here')
    }
  },
  created () {
    // default settings
    var ticktime = Date.now()
    this.time = new Date(ticktime - ticktime % (15 * 60 * 1000)).toTimeString().substr(0, 5)
    this.duration = 0
    this.location = 0
    this.date = new Date().toISOString().substr(0, 10)
    console.log('locations:', window.klobsdata.locations)
    try {
      this.dataid = this.$route.params.id
      if (this.dataid) {
        console.log('edit existing entry')
        const [day, month, year] = this.dataid.date.split('.')
        this.date = `${year}-${month}-${day}`
        this.time = this.dataid.starttime
        this.duration = this.dataid.duration
        this.location = this.dataid.locationid
      }
    } catch (error) {
      this.nav2Main()
    }
  },

  methods: {
    allowedStep: m => m % 15 === 0,
    submit () {
      console.log('form  date:', this.date, this.date.split('-'))
      console.log('form  time :', this.time)
      // console.log('form  time:', this.time, this.time.toTimeString().substr(0, 5))
      if (this.$refs.form.validate()) {
        var locationId = -1
        for (var i = 0; i < window.klobsdata.locations.length && locationId === -1; i++) {
          if (window.klobsdata.locations[i].id === this.location) {
            locationId = i
          }
        }
        const [year, month, day] = this.date.split('-')
        if (this.dataid) {
          this.dataid.location = window.klobsdata.locations[locationId].name
          this.dataid.locationid = window.klobsdata.locations[locationId].id
          this.dataid.date = `${day}.${month}.${year}`
          this.dataid.starttime = this.time
          this.dataid.duration = this.duration
        } else {
          window.klobsdata['sessiondata']['trainings'].push({
            'location': window.klobsdata.locations[locationId].name,
            'locationid': window.klobsdata.locations[locationId].id,
            'date': `${day}.${month}.${year}`,
            'starttime': this.time,
            'duration': this.duration
          })
        }
        localStorage.sessiondata = JSON.stringify(window.klobsdata['sessiondata'])
        router.push({ name: 'Main' }) // always goes 'back enough' to Main
      } else {
        console.log('form nicht validated :')
      }
    }
  },
  beforeMount: function () {
    if (!window.klobsdata) {
      router.push({ name: 'Main' }) // go back to main to load data first
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

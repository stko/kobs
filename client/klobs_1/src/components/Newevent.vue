<template>
  <v-container>
    <h1>Ein neues Ereignis</h1>
    <v-form v-model="valid" ref="form">
      <p>
      <label :for="date">Wähle das Datum</label>
      <br/>
      <v-date-picker
        v-model="date"
        full-width
        :id="date">
      </v-date-picker>
      <p/>
      <label :for="time">Wähle die Anfangszeit</label>
      <br/>
      <v-time-picker
        v-model="time"
        :id="time"
        :allowed-minutes="allowedStep"
        full-width
        format="24hr">

      </v-time-picker>
      <p/>
  <!--     <label :for="duration">Wähle die Dauer</label>
      <br/>
      <select v-model="duration" :id="duration">
        <option disabled value="" selected>Hier auswählen</option>
        <option>A</option>
        <option>B</option>
        <option>C</option>
      </select>
      <p/>
  -->    <v-select
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
      <v-btn @click="submit" :class="{ red: !valid, green: valid }">submit</v-btn>
    </v-form>
    <p/>
    <a style="cursor: pointer; text-decoration: underline" v-on:click="navBack()">Navigate to Main</a>
  </v-container>
</template>

<script>
import router from '../router'
export default {
  name: 'Newevent',
  data () {
    var ticktime = Date.now()
    return {
      valid: false,
      msg: 'New Event page',
      // integer arith: rounds down to last full quarter hour
      time: new Date(ticktime - ticktime % (15 * 60 * 1000)),
      duration: 0,
      durationitems: [
        {'value': 1, 'text': 'Eins'}
      ],
      location: 0,
      locations: window.klobsdata.locations,
      date: new Date().toISOString().substr(0, 10)
    }
  },
  watch: {
    locations: function (locations) {
      console.log('log here')
    }
  },
  methods: {
    navBack () {
      router.go(-1)
    },
    allowedStep: m => m % 15 === 0,
    submit () {
      if (this.$refs.form.validate()) {
        router.go(-1)
      }
    }
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

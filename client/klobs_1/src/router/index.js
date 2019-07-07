import Vue from 'vue'
import Router from 'vue-router'
import Main from '@/components/Main'
import Newevent from '@/components/Newevent'
import Edit from '@/components/Edit'
import Add from '@/components/Add'
import Settings from '@/components/Settings'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      redirect: {
        name: 'Main'
      }
    },
    {
      path: '/main',
      name: 'Main',
      component: Main
    },
    {
      path: '/new',
      name: 'Newevent',
      component: Newevent
    },
    {
      path: '/edit/:id',
      name: 'Edit',
      component: Edit
    },
    {
      path: '/add/:id',
      name: 'Add',
      component: Add
    },
    {
      path: '/set',
      name: 'Settings',
      component: Settings
    }
  ]
})

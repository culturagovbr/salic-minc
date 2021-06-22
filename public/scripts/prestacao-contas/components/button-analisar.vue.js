Vue.component('button-counter', {
  data: function () {
    return {
      count: 0
    }
  },
  props: ['idpronac'],
  template: `<router-link :to="{ name: 'analisar', params: { id: 123 }}" class="btn">Analisar</router-link>`
})

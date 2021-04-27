Vue.component('sl-btn-visualizar', {
    template: ` 
        <button class="btn" v-on:click="modal()" v-bind:class="">
            {{pronac}}
        </button>
    `,
    data: function() {
        return {
            dados:[]
        }
    },
    props: ['idpronac', 'pronac', 'nome'],
    mounted: function() {
    },
    methods: {
        modal: function() {
            bus.$emit('id-selected', {idpronac: this.idpronac, pronac: this.pronac, nome: this.nome});
            $3('#abrirModal').modal();
            $3('#abrirModal').modal('open');
        }
    }
});


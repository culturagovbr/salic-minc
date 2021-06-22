Vue.component('salic-agente-usuario', {
    template: `
        <div class="dados-usuario">
            <div class="card" v-if="usuario">
                <div class="card-content">
                    <h5>Usu&aacute;rio do sistema</h5>
                    <div class="row">
                        <div class="col s12 l3 m3">
                            <b>CPF</b><br>
                            {{ usuario.cpf}}
                        </div>
                        <div class="col s12 l3 m3">
                            <b>Nome</b><br>
                            {{ usuario.nome}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    data: function () {
        return {
            usuario: []
        }
    },
    props: ['idusuario'],
    mounted: function () {
        if (typeof this.idusuario != 'undefined') {
            this.fetch(this.idusuario);
        }
    },
    watch: {
        idusuario: function (value) {
            this.fetch(value);
        }
    },
    methods: {
        fetch: function (id) {
            if (id) {
                let vue = this;
                $3.ajax({
                    url: '/autenticacao/index/obter-dados-usuario/idUsuario/' + id
                }).done(function (response) {
                    vue.usuario = response.data;
                });
            }
        }
    }
});
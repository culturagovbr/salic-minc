Vue.component('salic-texto-simples', {
    template: `
        <div class="texto-simples">
            <div class="card">
                <div class="card-content">
                    <div class="row">
                        <div class="col s12 l12 m12">
                            <div v-if="texto && texto.length > 1" v-html="texto"></div>
                            <div v-else>Conteúdo n&atilde;o informado</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    props: ['texto']
});
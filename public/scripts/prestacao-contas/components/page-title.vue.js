Vue.component('page-title', {
    template: `
        <div class="page-title">
            <div class="row">
                <div class="col s12 m3 l1 left-align">
                    <a v-bind:href="url" 
                    class="btn btn-flat z-depth-0 chat-toggle waves-effect waves-light">
                        <i class="material-icons">arrow_back</i></a>
                </div>
                <div class="col s12 m9 l10">
                    <h1>Presta&ccedil;&atilde;o de Contas: {{nome}}</h1>
                </div>
            </div>
        </div>
    `,
    props:['nome', 'url'],
    data: function () {
        return {
            count: 0,
            title: 'Inicio'
        }
    },
    watch: {
        $route (to, from){
            this.title = to.meta.title;
        }
    },
    computed: {
        // goBackWithCollapseExpanded: function() {
        //     const splitedUrl = window.location.href.split('/')
        //     const idPronac = splitedUrl[7];
        //     const hostname = window.location.hostname;
        //     const cdProduto = splitedUrl[11];

        //     const url = 'http://'
        //                 + hostname
        //                 + '/prestacao-contas/realizar-prestacao-contas/index/idPronac/'
        //                 + idPronac
        //                 + '?cdProduto='
        //                 + cdProduto;

        //     return url;
        // },
    },
});

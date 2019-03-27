Vue.component(
    'sl-cadastrar-diligencia',
    {
        template: `
            <div>
                <form class="col s12">
                  <p>
                    Tipo de Diligencia
                  </p>
                  <p>
                    <input v-model="diligencia.tpDiligencia" value="174" name="tpDiligencia" type="radio" id="test2" />
                    <label for="test2">Somente Itens Recusados</label>
                  </p>
                  <p>
                    <input v-model="diligencia.tpDiligencia" value="645" name="tpDiligencia" type="radio" id="test1" />
                    <label for="test1">Todos os itens or&ccedil;amentarios</label>
                  </p>
                  <div class="row">
                    <div class="input-field col s12">
                      <textarea v-model="diligencia.solicitacao" name="solicitacao" id="solicitacao"class="materialize-textarea"></textarea>
                      <label for="solicaitacao">Solicita&ccedil;&atilde;o</label>
                    </div>
                  </div>
                  <button type="button" v-on:click="save" :disabled="button" class="btn modal-action waves-effect waves-green">enviar</button>
                  <a href="#!" class="btn modal-action modal-close waves-effect waves-green btn-flat">cancelar</a>
                </form>
            </div>
        `,
        props:['idpronac','tpdiligencia'],
        data: function() {
           return {
               'diligencia': {
                   'solicitacao':'',
                   'idPronac' : 0,
                   'tpDiligencia': 174
                },
                button: false
            }
        },
        computed:{},
        mounted: function() {
            this.diligencia.idPronac = this.idpronac;
            /* this.diligencia.tpDiligencia = this.tpdiligencia; */
        },
        methods: {
            save: function() {
                this.button = true;
                var vue = this;

                url = '/diligencia/diligencia';
                $3.ajax({
                  type: "POST",
                  url:url,
                  data: this.diligencia
                })
                .done(function(data) {
                    $3('#modal1').modal('close');
                    location.reload();
                })
                .fail(function(jqXHR) { 
                    message = jqXHR.responseJSON.data.message;
                    Materialize.toast(message, 3000, 'red');
                    $3('#modal1').modal('close');
                    location.reload();
                });
            }
        }
    }
);

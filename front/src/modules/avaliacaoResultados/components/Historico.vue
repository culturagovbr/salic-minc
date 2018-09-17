<template>
    <v-dialog 
      v-model="dialog"
      scrollable
      max-width="750px"
    >
        <v-btn slot="activator" flat icon color="grey">
            <v-icon>history</v-icon>
        </v-btn>
      <v-card>
        <v-card-title class="green white--text" primary-title>
          Histórico de encaminhamentos
        </v-card-title>
        
        <v-card-text style="height: 500px;">
            <v-subheader inset>Encaminhamentos</v-subheader>
            <v-data-table
                :headers="projetoHeaders"
                :items="[]"
                hide-actions
            >
                <template slot="no-data">
                    <td>{{pronac}}</td>
                    <td>{{nomeProjeto}}</td>
                </template>
            </v-data-table>
            <v-data-table
                :headers="historicoHeaders"
                :items="dadosHistoricoEncaminhamento"
                hide-actions
            >
                <template slot="items" slot-scope="props">
                    <td>{{ props.item.dtInicioEncaminhamento }}</td>
                    <td>{{ props.item.NomeOrigem }}</td>
                    <td>{{ props.item.NomeDestino }}</td>
                    <td>{{ props.item.dsJustificativa }}</td>
                </template>
                <template slot="no-data">
                    <v-alert :value="true" color="error" icon="warning">
                        Nenhum dado encontrado ¯\_(ツ)_/¯
                    </v-alert>
                </template>
            </v-data-table>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
                  color="red"
                  flat
                  @click="dialog = false">
            Fechar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ModalTemplate from '@/components/modal';

export default {
    name: 'Painel',
    props: [
        'idPronac',
        'pronac',
        'nomeProjeto',
    ],
    watch: {
        dialog(val) {
            if(val){
                this.obterHistoricoEncaminhamento(this.idPronac);
            }
        },
    },
    data() {
        return {
            projetoHeaders: [
                {
                    text: 'PRONAC',
                    align: 'left',
                    sortable: false,
                    value: 'pronac'
                },
                {
                    text: 'Nome do Projeto',
                    align: 'left',
                    sortable: false,
                    value: 'nomeProjeto'
                }
            ],
            historicoHeaders: [
                {
                    text: 'Data de Envio',
                    align: 'left',
                    sortable: false,
                    value: 'dataEnvio'
                },
                {
                    text: 'Nome do Remetente',
                    align: 'left',
                    sortable: false,
                    value: 'nomeRemetente'
                },
                {
                    text: 'Nome do Destinatário',
                    align: 'left',
                    sortable: false,
                    value: 'nomeDestinatario'
                },
                {
                    text: 'Justificativa',
                    align: 'left',
                    sortable: false,
                    value: 'justificativa'
                }
            ],
            dialog: false
        }
  },
    components: {
        ModalTemplate,
    },
    methods: {
        ...mapActions({
            obterHistoricoEncaminhamento: 'avaliacaoResultados/obterHistoricoEncaminhamento',
        }),
    },
    computed: mapGetters({
        dadosHistoricoEncaminhamento: 'avaliacaoResultados/dadosHistoricoEncaminhamento',
    }),
};
</script>

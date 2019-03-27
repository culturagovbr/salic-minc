<template>
    <div
        v-if="localizacoes"
        class="local-realizacao-deslocamento">
        <div class="card">
            <div class="card-content">
                <h5>Local de Realiza&ccedil;&atilde;o</h5>
                <table
                    v-if="localizacoes.abrangencia"
                    class="bordered responsive-table">
                    <thead>
                        <tr>
                            <th>Pa&iacute;s</th>
                            <th>UF</th>
                            <th>Cidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(localizacao,index) in localizacoes.abrangencia"
                            :key="index">
                            <td>{{ localizacao.pais }}</td>
                            <td>{{ localizacao.uf }}</td>
                            <td>{{ localizacao.cidade }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>Nenhuma localiza&ccedil;&atilde;o</div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Deslocamentos</h5>
                <table
                    v-if="localizacoes.deslocamento && localizacoes.deslocamento.lenght > 1"
                    class="bordered responsive-table">
                    <thead>
                        <tr>
                            <th>Pais de Origem</th>
                            <th>UF de Origem</th>
                            <th>Cidade de Origem</th>
                            <th>Pais de Destino</th>
                            <th>UF de Destino</th>
                            <th>Cidade de Destino</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(deslocamento, index) in localizacoes.deslocamento"
                            :key="index">
                            <td>{{ deslocamento.paisorigem }}</td>
                            <td>{{ deslocamento.uforigem }}</td>
                            <td>{{ deslocamento.municipioorigem }}</td>
                            <td>{{ deslocamento.paisodestino }}</td>
                            <td>{{ deslocamento.ufdestino }}</td>
                            <td>{{ deslocamento.municipiodestino }}</td>
                            <td>{{ deslocamento.Qtde }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>N&atilde;o informado</div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'PropostaLocalRealizacaoDeslocamento',
    props: {
        idpreprojeto: {
            type: String,
            default: '',
        },
        proposta: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            localizacoes: {},
        };
    },
    computed: {
        ...mapGetters({
            local: 'proposta/localRealizacaoDeslocamento',
        }),
    },
    watch: {
        idpreprojeto(value) {
            this.buscaLocalRealizacaoDeslocamento(value);
        },
        local(value) {
            this.localizacoes = value;
        },
        proposta(value) {
            this.localizacoes = value;
        },
    },
    mounted() {
        if (this.proposta && this.proposta.abrangencia) {
            this.localizacoes = this.proposta;
        }
        if (this.idpreprojeto !== '') {
            this.buscaLocalRealizacaoDeslocamento(this.idpreprojeto);
        }
    },
    methods: {
        ...mapActions({
            buscaLocalRealizacaoDeslocamento: 'proposta/buscaLocalRealizacaoDeslocamento',
        }),
    },
};
</script>

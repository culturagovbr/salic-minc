<template>
    <v-container grid-list-xl>
        <v-form ref="form" v-model="valid">
            <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
                <v-toolbar dark color="green">
                        <v-btn icon dark :to="{ name: 'Laudo' }">
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title>Emissão de Laudo Final de Avaliação de Resultados</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-toolbar-items>
                            <v-btn dark flat @click.native="salvarLaudoFinal()">Salvar</v-btn>
                            <v-btn dark flat @click.native="finalizarLaudoFinal()" :disabled="!parecerLaudoFinal.items.idLaudoFinal">Gerar Documento</v-btn>
                        </v-toolbar-items>
                </v-toolbar>
                <v-container grid-list-sm>
                    <v-layout row wrap>
                        <v-flex xs12 sm12 md12>
                            <p><b>Projeto:</b> {{projeto.AnoProjeto}}{{projeto.Sequencial}} - {{projeto.NomeProjeto}}</p>
                        </v-flex>
                        <v-flex xs12 sm12 md12>
                            <p><b>Proponente:</b> {{proponente.CgcCpf}} - {{proponente.Nome}}</p>
                        </v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                </v-container>
                <v-container grid-list>
                    <v-layout wrap align-center>
                        <v-flex>
                            <label for="manifestacao">Manifestação *</label>
                            <v-radio-group :value="parecerLaudoFinal.items.siManifestacao"
                                           @change="updateManifestacao"
                                           id="manifestacao"
                                           :rules="itemRules"
                                           row>
                                <v-radio color="success" label="Aprovado" value="A"></v-radio>
                                <v-radio color="success" label="Aprovado com ressalvas" value="P"></v-radio>
                                <v-radio color="success" label="Reprovado" value="R"></v-radio>
                            </v-radio-group>
                        </v-flex>
                    </v-layout>
                    <v-flex>
                        <v-textarea :value="parecerLaudoFinal.items.dsLaudoFinal"
                                    @input="updateParecer"
                                    :rules="parecerRules"
                                    color="deep-purple"
                                    label="Parecer *"
                                    height="200px"
                                    required="required">
                        </v-textarea>
                    </v-flex>
                </v-container>
            </v-dialog>
        </v-form>
    </v-container>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';

    export default {
        data() {
            return {
                tipo: true,
                idPronac: this.$route.params.id,
                valid: false,
                dialog: true,
                itemRules: [
                    v => !!v || 'Tipo de manifestação é obrigatório!',
                ],
                parecerRules: [
                    v => !!v || 'Parecer é obrigatório!',
                    v => Object(v).length >= 10 || 'Parecer deve conter mais que 10 caracteres',
                ],
            };
        },
        methods:
        {
            ...mapActions({
                modalOpen: 'modal/modalOpen',
                modalClose: 'modal/modalClose',
                requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
                salvar: 'avaliacaoResultados/salvarLaudoFinal',
                finalizar: 'avaliacaoResultados/finalizarLaudoFinal',
                getLaudoFinal: 'avaliacaoResultados/getLaudoFinal',
                atualizarManifestacao: 'avaliacaoResultados/atualizarManifestacao',
                atualizarParecer: 'avaliacaoResultados/atualizarParecer',
            }),
            getConsolidacao(id) {
                this.requestEmissaoParecer(id);
            },
            salvarLaudoFinal() {
                const data = {
                    idPronac: this.idPronac,
                    siManifestacao: this.characterManifestacao,
                    dsLaudoFinal: this.characterParecer,
                };

                this.salvar(data);
                /** Descomentar linha após migração da lista para o VUEJS */
                // this.dialog = false;
            },
            finalizarLaudoFinal() {
                const data = {
                    idPronac: this.idPronac,
                    siManifestacao: this.characterManifestacao,
                    dsLaudoFinal: this.characterParecer,
                    atual: 5,
                    proximo: 6,
                };

                this.finalizar(data);
                /** Descomentar linha após migração da lista para o VUEJS */
                // this.dialog = false;
            },
            updateManifestacao(characterManifestacao) {
                this.atualizarManifestacao(characterManifestacao);
            },
            updateParecer(characterParecer) {
                this.atualizarParecer(characterParecer);
            },
        },
        computed:
        {
            ...mapGetters({
                modalVisible: 'modal/default',
                proponente: 'avaliacaoResultados/proponente',
                projeto: 'avaliacaoResultados/projeto',
                parecerLaudoFinal: 'avaliacaoResultados/getParecerLaudoFinal',
                characterManifestacao: 'avaliacaoResultados/characterManifestacao',
                characterParecer: 'avaliacaoResultados/characterParecer',
            }),
        },
        created() {
            this.getConsolidacao(this.idPronac);
            this.getLaudoFinal(this.idPronac);
            this.atualizarManifestacao(this.parecerLaudoFinal.items.siManifestacao);
            this.atualizarParecer(this.parecerLaudoFinal.items.dsLaudoFinal);
        },
    };
</script>

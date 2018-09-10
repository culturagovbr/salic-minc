<template>
    <div>
        <v-card>
            <v-card-title primary-title>
                <h3>TÍTULO A SER DEFINIDO</h3>
            </v-card-title>
            <v-card-text>
                <p v-if="existeDiligencia">Existe Diligência para esse projeto. Acesse <router-link to="#">aqui</router-link>.</p>
                <p v-else>Sem Observações.</p>
            </v-card-text>
            <v-card-actions>
                <v-btn color="success" to="#">VER PROJETO</v-btn>
                <v-btn color="success" to="#">CONSOLIDAÇÃO</v-btn>
            </v-card-actions>
        </v-card>

        <v-card class="mt-3">
            <!-- PRODUTO -->
            <v-expansion-panel>
                <v-expansion-panel-content
                v-for="(produto,i) in produtos"
                :key="i"
                >
                    <div slot="header">{{ produto.produto }}</div>
                    <v-card>
                        <!-- ETAPA -->
                        <v-expansion-panel>
                            <v-expansion-panel-content
                            v-for="(etapa,i) in produto.etapa"
                            :key="i"
                            >
                            <div slot="header">{{ etapa.etapa }}</div>
                            <v-card>
                                <!-- UF -->
                                    <v-expansion-panel>
                                        <v-expansion-panel-content
                                        v-for="(uf,i) in etapa.UF"
                                        :key="i"
                                        >
                                        <div slot="header">{{ uf.Uf }}</div>
                                        <v-card>
                                            <!-- CIDADE -->
                                                <v-expansion-panel>
                                                    <v-expansion-panel-content
                                                    v-for="(cidade,i) in uf.cidade"
                                                    :key="i"
                                                    >
                                                    <div slot="header">{{ cidade.cidade }}</div>
                                                    <v-card>
                                                        <!-- DADOS -->
                                                        #INSERIR COMPONENTE DA TABELA DE DADOS
                                                    </v-card>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                        </v-card>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                            </v-card>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-card>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-card>

        <v-card class="mt-3">
            <table>
                <tr>
                    <td>Valor Aprovado</td>                    
                    <td></td>                    
                </tr>
                <tr>
                    <td>Valor Comprovado</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Valor a Comprovar</td>
                    <td></td>
                </tr>
            </table>
        </v-card>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ModalTemplate from '@/components/modal';

export default {
    name: 'Painel',
    data() {
        return {
            DadoNr: null,
            existeDiligencia: true,
            produtos: {"0":{"etapa":{"4":{"UF":{"35":{"cidade":{"355030":{"itens":{"1":{"130":{"item":"Hospedagem sem Alimentação","varlorAprovado":5800,"varlorComprovado":2392.8,"comprovacaoValidada":2139.8,"idPlanilhaAprovacao":18564,"idPlanilhaItens":130,"ComprovacaoValidada":2139.8,"stItemAvaliado":"1"},"191":{"item":"Contador","varlorAprovado":5700,"varlorComprovado":5700,"comprovacaoValidada":5700,"idPlanilhaAprovacao":18562,"idPlanilhaItens":191,"ComprovacaoValidada":5700,"stItemAvaliado":"1"},"1019":{"item":"Combustível","varlorAprovado":1720,"varlorComprovado":1649.98,"comprovacaoValidada":99.97,"idPlanilhaAprovacao":18561,"idPlanilhaItens":1019,"ComprovacaoValidada":99.97,"stItemAvaliado":"1"},"3710":{"item":"Postagem","varlorAprovado":7800,"varlorComprovado":4385.5,"comprovacaoValidada":4385.5,"idPlanilhaAprovacao":18585,"idPlanilhaItens":3710,"ComprovacaoValidada":4385.5,"stItemAvaliado":"1"},"5249":{"item":"Remuneração para captação de recursos","varlorAprovado":10000,"varlorComprovado":10000,"comprovacaoValidada":10000,"idPlanilhaAprovacao":18586,"idPlanilhaItens":5249,"ComprovacaoValidada":10000,"stItemAvaliado":"1"}},"3":{"130":{"item":"Hospedagem sem Alimentação","varlorAprovado":5800,"varlorComprovado":2392.8,"comprovacaoValidada":2139.8,"idPlanilhaAprovacao":18564,"idPlanilhaItens":130,"ComprovacaoValidada":2139.8,"stItemAvaliado":"3"},"131":{"item":"Passagens Aéreas (Descrever os trechos na tela de deslocamentos)","varlorAprovado":3200,"varlorComprovado":2700,"comprovacaoValidada":0,"idPlanilhaAprovacao":18560,"idPlanilhaItens":131,"ComprovacaoValidada":0,"stItemAvaliado":"3"},"1019":{"item":"Combustível","varlorAprovado":1720,"varlorComprovado":1649.98,"comprovacaoValidada":99.97,"idPlanilhaAprovacao":18561,"idPlanilhaItens":1019,"ComprovacaoValidada":99.97,"stItemAvaliado":"3"}},"4":{"1019":{"item":"Combustível","varlorAprovado":1720,"varlorComprovado":1649.98,"comprovacaoValidada":99.97,"idPlanilhaAprovacao":18561,"idPlanilhaItens":1019,"ComprovacaoValidada":99.97,"stItemAvaliado":"4"},"1119":{"item":"Refeição","varlorAprovado":2400,"varlorComprovado":1864.29,"comprovacaoValidada":0,"idPlanilhaAprovacao":18563,"idPlanilhaItens":1119,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3677":{"item":"Coordenação geral","varlorAprovado":12000,"varlorComprovado":12000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18582,"idPlanilhaItens":3677,"ComprovacaoValidada":0,"stItemAvaliado":"4"}},"todos":{"130":{"item":"Hospedagem sem Alimentação","varlorAprovado":5800,"varlorComprovado":2392.8,"comprovacaoValidada":2139.8,"idPlanilhaAprovacao":18564,"idPlanilhaItens":130,"ComprovacaoValidada":2139.8,"stItemAvaliado":"3"},"131":{"item":"Passagens Aéreas (Descrever os trechos na tela de deslocamentos)","varlorAprovado":3200,"varlorComprovado":2700,"comprovacaoValidada":0,"idPlanilhaAprovacao":18560,"idPlanilhaItens":131,"ComprovacaoValidada":0,"stItemAvaliado":"3"},"191":{"item":"Contador","varlorAprovado":5700,"varlorComprovado":5700,"comprovacaoValidada":5700,"idPlanilhaAprovacao":18562,"idPlanilhaItens":191,"ComprovacaoValidada":5700,"stItemAvaliado":"1"},"1019":{"item":"Combustível","varlorAprovado":1720,"varlorComprovado":1649.98,"comprovacaoValidada":99.97,"idPlanilhaAprovacao":18561,"idPlanilhaItens":1019,"ComprovacaoValidada":99.97,"stItemAvaliado":"4"},"1119":{"item":"Refeição","varlorAprovado":2400,"varlorComprovado":1864.29,"comprovacaoValidada":0,"idPlanilhaAprovacao":18563,"idPlanilhaItens":1119,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3677":{"item":"Coordenação geral","varlorAprovado":12000,"varlorComprovado":12000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18582,"idPlanilhaItens":3677,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3710":{"item":"Postagem","varlorAprovado":7800,"varlorComprovado":4385.5,"comprovacaoValidada":4385.5,"idPlanilhaAprovacao":18585,"idPlanilhaItens":3710,"ComprovacaoValidada":4385.5,"stItemAvaliado":"1"},"5249":{"item":"Remuneração para captação de recursos","varlorAprovado":10000,"varlorComprovado":10000,"comprovacaoValidada":10000,"idPlanilhaAprovacao":18586,"idPlanilhaItens":5249,"ComprovacaoValidada":10000,"stItemAvaliado":"1"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Custos / Administrativos","cdEtapa":4}},"produto":"Administração do Projeto","cdProduto":0},"1":{"etapa":{"1":{"UF":{"35":{"cidade":{"355030":{"itens":{"1":{"43":{"item":"Projeto Gráfico","varlorAprovado":12000,"varlorComprovado":12000,"comprovacaoValidada":12000,"idPlanilhaAprovacao":18552,"idPlanilhaItens":43,"ComprovacaoValidada":12000,"stItemAvaliado":"1"}},"4":{"3640":{"item":"Consultor de conteúdos e imagens","varlorAprovado":4000,"varlorComprovado":4000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18565,"idPlanilhaItens":3640,"ComprovacaoValidada":0,"stItemAvaliado":"4"}},"todos":{"43":{"item":"Projeto Gráfico","varlorAprovado":12000,"varlorComprovado":12000,"comprovacaoValidada":12000,"idPlanilhaAprovacao":18552,"idPlanilhaItens":43,"ComprovacaoValidada":12000,"stItemAvaliado":"1"},"3640":{"item":"Consultor de conteúdos e imagens","varlorAprovado":4000,"varlorComprovado":4000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18565,"idPlanilhaItens":3640,"ComprovacaoValidada":0,"stItemAvaliado":"4"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Pré-Produção","cdEtapa":1},"2":{"UF":{"35":{"cidade":{"355030":{"itens":{"1":{"109":{"item":"Editoração Eletrônica","varlorAprovado":8000,"varlorComprovado":8000,"comprovacaoValidada":8000,"idPlanilhaAprovacao":18557,"idPlanilhaItens":109,"ComprovacaoValidada":8000,"stItemAvaliado":"1"},"114":{"item":"Impressão","varlorAprovado":63000,"varlorComprovado":55012.12,"comprovacaoValidada":55000,"idPlanilhaAprovacao":18559,"idPlanilhaItens":114,"ComprovacaoValidada":55000,"stItemAvaliado":"1"},"143":{"item":"Fotografia Artística (Fotógrafo, Tratamento, Revelação, etc.)","varlorAprovado":12000,"varlorComprovado":12000,"comprovacaoValidada":12000,"idPlanilhaAprovacao":18554,"idPlanilhaItens":143,"ComprovacaoValidada":12000,"stItemAvaliado":"1"},"3204":{"item":"Tratamento de imagens","varlorAprovado":6000,"varlorComprovado":6000,"comprovacaoValidada":6000,"idPlanilhaAprovacao":18555,"idPlanilhaItens":3204,"ComprovacaoValidada":6000,"stItemAvaliado":"1"}},"4":{"91":{"item":"Produção de Texto","varlorAprovado":6000,"varlorComprovado":6000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18553,"idPlanilhaItens":91,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"92":{"item":"Revisão de Texto","varlorAprovado":1000,"varlorComprovado":1000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18558,"idPlanilhaItens":92,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"97":{"item":"Edição de Texto","varlorAprovado":8000,"varlorComprovado":8000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18556,"idPlanilhaItens":97,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"114":{"item":"Impressão","varlorAprovado":63000,"varlorComprovado":55012.12,"comprovacaoValidada":55000,"idPlanilhaAprovacao":18559,"idPlanilhaItens":114,"ComprovacaoValidada":55000,"stItemAvaliado":"4"}},"todos":{"91":{"item":"Produção de Texto","varlorAprovado":6000,"varlorComprovado":6000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18553,"idPlanilhaItens":91,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"92":{"item":"Revisão de Texto","varlorAprovado":1000,"varlorComprovado":1000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18558,"idPlanilhaItens":92,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"97":{"item":"Edição de Texto","varlorAprovado":8000,"varlorComprovado":8000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18556,"idPlanilhaItens":97,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"109":{"item":"Editoração Eletrônica","varlorAprovado":8000,"varlorComprovado":8000,"comprovacaoValidada":8000,"idPlanilhaAprovacao":18557,"idPlanilhaItens":109,"ComprovacaoValidada":8000,"stItemAvaliado":"1"},"114":{"item":"Impressão","varlorAprovado":63000,"varlorComprovado":55012.12,"comprovacaoValidada":55000,"idPlanilhaAprovacao":18559,"idPlanilhaItens":114,"ComprovacaoValidada":55000,"stItemAvaliado":"4"},"143":{"item":"Fotografia Artística (Fotógrafo, Tratamento, Revelação, etc.)","varlorAprovado":12000,"varlorComprovado":12000,"comprovacaoValidada":12000,"idPlanilhaAprovacao":18554,"idPlanilhaItens":143,"ComprovacaoValidada":12000,"stItemAvaliado":"1"},"3204":{"item":"Tratamento de imagens","varlorAprovado":6000,"varlorComprovado":6000,"comprovacaoValidada":6000,"idPlanilhaAprovacao":18555,"idPlanilhaItens":3204,"ComprovacaoValidada":6000,"stItemAvaliado":"1"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Produção","cdEtapa":2},"3":{"UF":{"35":{"cidade":{"355030":{"itens":{"4":{"142":{"item":"Assessor de imprensa","varlorAprovado":4000,"varlorComprovado":4000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18583,"idPlanilhaItens":142,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"2784":{"item":"Sítio de internet\r\n","varlorAprovado":3400,"varlorComprovado":3400,"comprovacaoValidada":0,"idPlanilhaAprovacao":18587,"idPlanilhaItens":2784,"ComprovacaoValidada":0,"stItemAvaliado":"4"}},"todos":{"142":{"item":"Assessor de imprensa","varlorAprovado":4000,"varlorComprovado":4000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18583,"idPlanilhaItens":142,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"2784":{"item":"Sítio de internet\r\n","varlorAprovado":3400,"varlorComprovado":3400,"comprovacaoValidada":0,"idPlanilhaAprovacao":18587,"idPlanilhaItens":2784,"ComprovacaoValidada":0,"stItemAvaliado":"4"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Divulgação / Comercialização","cdEtapa":3}},"produto":"Livro","cdProduto":1},"62":{"etapa":{"1":{"UF":{"35":{"cidade":{"355030":{"itens":{"4":{"20":{"item":"Produtor","varlorAprovado":8000,"varlorComprovado":8000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18573,"idPlanilhaItens":20,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3947":{"item":"Curadoria","varlorAprovado":16000,"varlorComprovado":16000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18572,"idPlanilhaItens":3947,"ComprovacaoValidada":0,"stItemAvaliado":"4"}},"todos":{"20":{"item":"Produtor","varlorAprovado":8000,"varlorComprovado":8000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18573,"idPlanilhaItens":20,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3947":{"item":"Curadoria","varlorAprovado":16000,"varlorComprovado":16000,"comprovacaoValidada":0,"idPlanilhaAprovacao":18572,"idPlanilhaItens":3947,"ComprovacaoValidada":0,"stItemAvaliado":"4"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Pré-Produção","cdEtapa":1},"2":{"UF":{"35":{"cidade":{"355030":{"itens":{"1":{"3293":{"item":"Designer gráfico","varlorAprovado":4800,"varlorComprovado":4800,"comprovacaoValidada":4800,"idPlanilhaAprovacao":18581,"idPlanilhaItens":3293,"ComprovacaoValidada":4800,"stItemAvaliado":"1"},"3531":{"item":"Materiais e equipamentos para montagem","varlorAprovado":2500,"varlorComprovado":2487.77,"comprovacaoValidada":109,"idPlanilhaAprovacao":18576,"idPlanilhaItens":3531,"ComprovacaoValidada":109,"stItemAvaliado":"1"},"4662":{"item":"Reprodução de originais e plotagens","varlorAprovado":7500,"varlorComprovado":7376.62,"comprovacaoValidada":6717.62,"idPlanilhaAprovacao":18577,"idPlanilhaItens":4662,"ComprovacaoValidada":6717.62,"stItemAvaliado":"1"}},"4":{"3531":{"item":"Materiais e equipamentos para montagem","varlorAprovado":2500,"varlorComprovado":2487.77,"comprovacaoValidada":109,"idPlanilhaAprovacao":18576,"idPlanilhaItens":3531,"ComprovacaoValidada":109,"stItemAvaliado":"4"},"3580":{"item":"Ampliações das imagens","varlorAprovado":6000,"varlorComprovado":5769.8,"comprovacaoValidada":0,"idPlanilhaAprovacao":18575,"idPlanilhaItens":3580,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3953":{"item":"Cenografia/material/confecção","varlorAprovado":6000,"varlorComprovado":5980.22,"comprovacaoValidada":0,"idPlanilhaAprovacao":18574,"idPlanilhaItens":3953,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"4662":{"item":"Reprodução de originais e plotagens","varlorAprovado":7500,"varlorComprovado":7376.62,"comprovacaoValidada":6717.62,"idPlanilhaAprovacao":18577,"idPlanilhaItens":4662,"ComprovacaoValidada":6717.62,"stItemAvaliado":"4"}},"todos":{"3293":{"item":"Designer gráfico","varlorAprovado":4800,"varlorComprovado":4800,"comprovacaoValidada":4800,"idPlanilhaAprovacao":18581,"idPlanilhaItens":3293,"ComprovacaoValidada":4800,"stItemAvaliado":"1"},"3531":{"item":"Materiais e equipamentos para montagem","varlorAprovado":2500,"varlorComprovado":2487.77,"comprovacaoValidada":109,"idPlanilhaAprovacao":18576,"idPlanilhaItens":3531,"ComprovacaoValidada":109,"stItemAvaliado":"4"},"3580":{"item":"Ampliações das imagens","varlorAprovado":6000,"varlorComprovado":5769.8,"comprovacaoValidada":0,"idPlanilhaAprovacao":18575,"idPlanilhaItens":3580,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3953":{"item":"Cenografia/material/confecção","varlorAprovado":6000,"varlorComprovado":5980.22,"comprovacaoValidada":0,"idPlanilhaAprovacao":18574,"idPlanilhaItens":3953,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"4662":{"item":"Reprodução de originais e plotagens","varlorAprovado":7500,"varlorComprovado":7376.62,"comprovacaoValidada":6717.62,"idPlanilhaAprovacao":18577,"idPlanilhaItens":4662,"ComprovacaoValidada":6717.62,"stItemAvaliado":"4"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Produção","cdEtapa":2},"3":{"UF":{"35":{"cidade":{"355030":{"itens":{"4":{"1219":{"item":"Cartaz","varlorAprovado":400,"varlorComprovado":267.91,"comprovacaoValidada":0,"idPlanilhaAprovacao":18578,"idPlanilhaItens":1219,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"2755":{"item":"Convite\r\n","varlorAprovado":1200,"varlorComprovado":455.91,"comprovacaoValidada":0,"idPlanilhaAprovacao":18580,"idPlanilhaItens":2755,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3662":{"item":"Mala direta eletrônica","varlorAprovado":1440,"varlorComprovado":1440,"comprovacaoValidada":0,"idPlanilhaAprovacao":18579,"idPlanilhaItens":3662,"ComprovacaoValidada":0,"stItemAvaliado":"4"}},"todos":{"1219":{"item":"Cartaz","varlorAprovado":400,"varlorComprovado":267.91,"comprovacaoValidada":0,"idPlanilhaAprovacao":18578,"idPlanilhaItens":1219,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"2755":{"item":"Convite\r\n","varlorAprovado":1200,"varlorComprovado":455.91,"comprovacaoValidada":0,"idPlanilhaAprovacao":18580,"idPlanilhaItens":2755,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3662":{"item":"Mala direta eletrônica","varlorAprovado":1440,"varlorComprovado":1440,"comprovacaoValidada":0,"idPlanilhaAprovacao":18579,"idPlanilhaItens":3662,"ComprovacaoValidada":0,"stItemAvaliado":"4"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Divulgação / Comercialização","cdEtapa":3}},"produto":"Exposição de Artes","cdProduto":62},"121":{"etapa":{"1":{"UF":{"35":{"cidade":{"355030":{"itens":{"1":{"2713":{"item":"Material de apoio pedagógico","varlorAprovado":3200,"varlorComprovado":3200,"comprovacaoValidada":3200,"idPlanilhaAprovacao":18570,"idPlanilhaItens":2713,"ComprovacaoValidada":3200,"stItemAvaliado":"1"},"3627":{"item":"Coordenação pedagógica","varlorAprovado":6000,"varlorComprovado":6000,"comprovacaoValidada":6000,"idPlanilhaAprovacao":18566,"idPlanilhaItens":3627,"ComprovacaoValidada":6000,"stItemAvaliado":"1"}},"todos":{"2713":{"item":"Material de apoio pedagógico","varlorAprovado":3200,"varlorComprovado":3200,"comprovacaoValidada":3200,"idPlanilhaAprovacao":18570,"idPlanilhaItens":2713,"ComprovacaoValidada":3200,"stItemAvaliado":"1"},"3627":{"item":"Coordenação pedagógica","varlorAprovado":6000,"varlorComprovado":6000,"comprovacaoValidada":6000,"idPlanilhaAprovacao":18566,"idPlanilhaItens":3627,"ComprovacaoValidada":6000,"stItemAvaliado":"1"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Pré-Produção","cdEtapa":1},"2":{"UF":{"35":{"cidade":{"355030":{"itens":{"1":{"143":{"item":"Fotografia Artística (Fotógrafo, Tratamento, Revelação, etc.)","varlorAprovado":8000,"varlorComprovado":8000,"comprovacaoValidada":8000,"idPlanilhaAprovacao":18569,"idPlanilhaItens":143,"ComprovacaoValidada":8000,"stItemAvaliado":"1"},"3929":{"item":"Arte educador","varlorAprovado":32000,"varlorComprovado":32000,"comprovacaoValidada":24000,"idPlanilhaAprovacao":18567,"idPlanilhaItens":3929,"ComprovacaoValidada":24000,"stItemAvaliado":"1"}},"4":{"1119":{"item":"Refeição","varlorAprovado":3472,"varlorComprovado":1943.18,"comprovacaoValidada":0,"idPlanilhaAprovacao":18584,"idPlanilhaItens":1119,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3929":{"item":"Arte educador","varlorAprovado":32000,"varlorComprovado":32000,"comprovacaoValidada":24000,"idPlanilhaAprovacao":18567,"idPlanilhaItens":3929,"ComprovacaoValidada":24000,"stItemAvaliado":"4"}},"todos":{"143":{"item":"Fotografia Artística (Fotógrafo, Tratamento, Revelação, etc.)","varlorAprovado":8000,"varlorComprovado":8000,"comprovacaoValidada":8000,"idPlanilhaAprovacao":18569,"idPlanilhaItens":143,"ComprovacaoValidada":8000,"stItemAvaliado":"1"},"1119":{"item":"Refeição","varlorAprovado":3472,"varlorComprovado":1943.18,"comprovacaoValidada":0,"idPlanilhaAprovacao":18584,"idPlanilhaItens":1119,"ComprovacaoValidada":0,"stItemAvaliado":"4"},"3929":{"item":"Arte educador","varlorAprovado":32000,"varlorComprovado":32000,"comprovacaoValidada":24000,"idPlanilhaAprovacao":18567,"idPlanilhaItens":3929,"ComprovacaoValidada":24000,"stItemAvaliado":"4"}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Produção","cdEtapa":2},"3":{"UF":{"35":{"cidade":{"355030":{"itens":{"":{"1219":{"item":"Cartaz","varlorAprovado":200,"varlorComprovado":0,"comprovacaoValidada":0,"idPlanilhaAprovacao":18571,"idPlanilhaItens":1219,"ComprovacaoValidada":0,"stItemAvaliado":null},"2743":{"item":"Banner/faixa adesiva/faixa de lona/saia de palco/testeira/pórtico\r\n","varlorAprovado":2400,"varlorComprovado":0,"comprovacaoValidada":0,"idPlanilhaAprovacao":18568,"idPlanilhaItens":2743,"ComprovacaoValidada":0,"stItemAvaliado":null}},"todos":{"1219":{"item":"Cartaz","varlorAprovado":200,"varlorComprovado":0,"comprovacaoValidada":0,"idPlanilhaAprovacao":18571,"idPlanilhaItens":1219,"ComprovacaoValidada":0,"stItemAvaliado":null},"2743":{"item":"Banner/faixa adesiva/faixa de lona/saia de palco/testeira/pórtico\r\n","varlorAprovado":2400,"varlorComprovado":0,"comprovacaoValidada":0,"idPlanilhaAprovacao":18568,"idPlanilhaItens":2743,"ComprovacaoValidada":0,"stItemAvaliado":null}}},"cidade":"São Paulo","cdCidade":355030}},"Uf":"SP","cdUF":35}},"etapa":"Divulgação / Comercialização","cdEtapa":3}},"produto":"Oficina /Workshop/Seminário Audiovisual","cdProduto":121}}
        };
    },
    computed: {
        valorAprovado: function() {
            return;
        },
        valorComprovado: function() {
            return;
        },
        valorAComprovar: function() {
            return;
        }
    },
    props: {
        idpronac: "132451"
    },
    components: {
        ModalTemplate,
    },
    methods: {
        ...mapActions({
            criarRegistro: 'foo/criarRegistro',
            modalOpen: 'modal/modalOpen',
            modalClose: 'modal/modalClose',
        }),
        fecharModal() {
            // eslint-disable-next-line
            $3('#modalTemplate').modal('close');
            this.modalClose();
        },
    },
    computed: mapGetters({
        modalVisible: 'modal/default',
    }),
};
</script>

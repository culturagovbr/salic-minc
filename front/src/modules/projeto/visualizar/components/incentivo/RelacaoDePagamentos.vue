<template>
    <div id="conteudo">
        <div class="row">
            <CarregarTemplateAjax :url-ajax="url"/>
        </div>
    </div>
</template>
<script>
import CarregarTemplateAjax from '@/components/CarregarTemplateAjax';

export default {
    components: {
        CarregarTemplateAjax,
    },
    data() {
        return {
            url: `/default/consultardadosprojeto/dados-relacao-pagamentos?idPronac=${this.$route.params.idPronac}`,
        };
    },
};
</script>

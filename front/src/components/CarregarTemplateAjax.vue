<template>
    <div>
        <Carregando v-if="loading"/>
        <div id="template-ajax"/>
    </div>
</template>

<script>
import Carregando from '@/components/Carregando';

export default {
    name: 'CarregarTemplateAjax',
    components: {
        Carregando,
    },
    props: {
        urlAjax: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            active: true,
            loading: true,
        };
    },
    mounted() {
        if (typeof this.urlAjax !== 'undefined' && this.urlAjax !== '') {
            this.obterTemplate();
        }
    },
    methods: {
        obterTemplate() {
            const self = this;
            if (self.urlAjax === '') {
                return;
            }
            /* eslint-disable-next-line */
                let elmRetorno = $3("#template-ajax");
            /* eslint-disable-next-line */
                $3.ajax({
                url: self.urlAjax,
                success(data) {
                    elmRetorno.html(data);
                    self.loading = false;
                },
                type: 'post',
            });
        },
    },
};
</script>

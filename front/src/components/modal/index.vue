<template>
    <div id="modalTemplate" class="modal modal-fixed-footer">
        <div class="modal-content">
            <div class="modal-header">
                <h2>
                    <slot name="header">Header</slot>
                </h2>
            </div>
            <div class="modal-body">
                <slot name="body">Body</slot>
            </div>
        </div>
        <div class="modal-footer">
            <slot name="footer">
                <button class="btn btn-danger" @click="fecharModal();$event.preventDefault()">
                    Fechar
                </button>
            </slot>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex';

    export default {
        mounted() {
            const objeto = this;
            // eslint-disable-next-line
            $3('.modal').modal({ complete: () => { objeto.fecharModal(); } });

            // eslint-disable-next-line
            $3('#modalTemplate').modal('open');
        },
        methods: {
            fecharModal() {
                // eslint-disable-next-line
                $3('#modalTemplate')
                    .modal('close');
                this.$emit('close');
            },
            ...mapActions({
                modalClose: 'modal/modalClose',
            }),
        },
    };
</script>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const habilitando = ref(false);
const confirmando = ref(false);
const desabilitando = ref(false);
const qrCode = ref(null);
const chaveConfiguracao = ref(null);
const codigosRecuperacao = ref([]);

const formularioConfirmacao = useForm({
    code: '',
});

const doisFatoresHabilitado = computed(
    () => !habilitando.value && page.props.auth.user?.two_factor_enabled,
);

watch(doisFatoresHabilitado, () => {
    if (!doisFatoresHabilitado.value) {
        formularioConfirmacao.reset();
        formularioConfirmacao.clearErrors();
    }
});

const habilitarAutenticacaoDoisFatores = () => {
    habilitando.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            exibirQrCode(),
            exibirChaveConfiguracao(),
            exibirCodigosRecuperacao(),
        ]),
        onFinish: () => {
            habilitando.value = false;
            confirmando.value = props.requiresConfirmation;
        },
    });
};

const exibirQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const exibirChaveConfiguracao = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        chaveConfiguracao.value = response.data.secretKey;
    });
};

const exibirCodigosRecuperacao = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        codigosRecuperacao.value = response.data;
    });
};

const confirmarAutenticacaoDoisFatores = () => {
    formularioConfirmacao.post(route('two-factor.confirm'), {
        errorBag: "confirmarAutenticacaoDoisFatores",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirmando.value = false;
            qrCode.value = null;
            chaveConfiguracao.value = null;
        },
    });
};

const regenerarCodigosRecuperacao = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => exibirCodigosRecuperacao());
};

const desabilitarAutenticacaoDoisFatores = () => {
    desabilitando.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            desabilitando.value = false;
            confirmando.value = false;
        },
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            Autenticação em Dois Fatores
        </template>

        <template #description>
            Adicione segurança extra à sua conta utilizando autenticação em dois fatores.
        </template>

        <template #content>
            <h3 v-if="doisFatoresHabilitado && !confirmando" class="text-lg font-medium text-gray-900">
                Você ativou a autenticação em dois fatores.
            </h3>

            <h3 v-else-if="doisFatoresHabilitado && confirmando" class="text-lg font-medium text-gray-900">
                Finalize a ativação da autenticação em dois fatores.
            </h3>

            <h3 v-else class="text-lg font-medium text-gray-900">
                Você ainda não ativou a autenticação em dois fatores.
            </h3>

            <div class="mt-3 max-w-xl text-sm text-gray-600">
                <p>
                    Quando a autenticação em dois fatores estiver ativada, você será solicitado a informar um token aleatório e seguro durante o login. Você poderá obter esse token no aplicativo Google Authenticator do seu celular.
                </p>
            </div>

            <div v-if="doisFatoresHabilitado">
                <div v-if="qrCode">
                    <div class="mt-4 max-w-xl text-sm text-gray-600">
                        <p v-if="confirmando" class="font-semibold">
                            Para finalizar a ativação, escaneie o QR Code abaixo com seu aplicativo autenticador ou insira a chave de configuração e informe o código OTP gerado.
                        </p>

                        <p v-else>
                            A autenticação em dois fatores está ativada. Escaneie o QR Code abaixo com seu aplicativo autenticador ou insira a chave de configuração.
                        </p>
                    </div>

                    <div class="mt-4 p-2 inline-block bg-white" v-html="qrCode" />

                    <div v-if="chaveConfiguracao" class="mt-4 max-w-xl text-sm text-gray-600">
                        <p class="font-semibold">
                            Chave de Configuração: <span v-html="chaveConfiguracao"></span>
                        </p>
                    </div>

                    <div v-if="confirmando" class="mt-4">
                        <InputLabel for="code" value="Código" />

                        <TextInput
                            id="code"
                            v-model="formularioConfirmacao.code"
                            type="text"
                            name="code"
                            class="block mt-1 w-1/2"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            @keyup.enter="confirmarAutenticacaoDoisFatores"
                        />

                        <InputError :message="formularioConfirmacao.errors.code" class="mt-2" />
                    </div>
                </div>

                <div v-if="codigosRecuperacao.length > 0 && !confirmando">
                    <div class="mt-4 max-w-xl text-sm text-gray-600">
                        <p class="font-semibold">
                            Guarde esses códigos de recuperação em um gerenciador de senhas seguro. Eles podem ser usados para recuperar o acesso à sua conta caso você perca o dispositivo autenticador.
                        </p>
                    </div>

                    <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                        <div v-for="code in codigosRecuperacao" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div v-if="!doisFatoresHabilitado">
                    <ConfirmsPassword @confirmed="habilitarAutenticacaoDoisFatores">
                        <PrimaryButton type="button" :class="{ 'opacity-25': habilitando }" :disabled="habilitando">
                            Ativar
                        </PrimaryButton>
                    </ConfirmsPassword>
                </div>

                <div v-else>
                    <ConfirmsPassword @confirmed="confirmarAutenticacaoDoisFatores">
                        <PrimaryButton
                            v-if="confirmando"
                            type="button"
                            class="me-3"
                            :class="{ 'opacity-25': habilitando }"
                            :disabled="habilitando"
                        >
                            Confirmar
                        </PrimaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="regenerarCodigosRecuperacao">
                        <SecondaryButton
                            v-if="codigosRecuperacao.length > 0 && !confirmando"
                            class="me-3"
                        >
                            Regenerar Códigos de Recuperação
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="exibirCodigosRecuperacao">
                        <SecondaryButton
                            v-if="codigosRecuperacao.length === 0 && !confirmando"
                            class="me-3"
                        >
                            Exibir Códigos de Recuperação
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="desabilitarAutenticacaoDoisFatores">
                        <SecondaryButton
                            v-if="confirmando"
                            :class="{ 'opacity-25': desabilitando }"
                            :disabled="desabilitando"
                        >
                            Cancelar
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="desabilitarAutenticacaoDoisFatores">
                        <DangerButton
                            v-if="!confirmando"
                            :class="{ 'opacity-25': desabilitando }"
                            :disabled="desabilitando"
                        >
                            Desativar
                        </DangerButton>
                    </ConfirmsPassword>
                </div>
            </div>
        </template>
    </ActionSection>
</template>

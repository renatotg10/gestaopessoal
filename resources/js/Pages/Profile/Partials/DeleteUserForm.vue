<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Excluir Conta
        </template>

        <template #description>
            Exclua permanentemente sua conta.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Após a exclusão de sua conta, todos os recursos e dados serão permanentemente deletados. Antes de excluir sua conta, baixe qualquer dado ou informação que você deseje manter.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmUserDeletion">
                    Excluir Conta
                </DangerButton>
            </div>

            <!-- Modal de Confirmação de Exclusão de Conta -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    Excluir Conta
                </template>

                <template #content>
                    Tem certeza de que deseja excluir sua conta? Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente deletados. Por favor, insira sua senha para confirmar que você deseja excluir sua conta permanentemente.

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Senha"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Excluir Conta
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>

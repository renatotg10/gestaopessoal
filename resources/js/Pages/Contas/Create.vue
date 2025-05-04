<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ActionMessage from '@/Components/ActionMessage.vue';

const form = useForm({
    nome: '',
    tipo: '',
    saldo_inicial: 0,
});

const recentlySuccessful = ref(false);

const submit = () => {
    form.post(route('contas.store'), {
        onSuccess: () => {
            form.reset(); // Limpa o formulário
            recentlySuccessful.value = true;

            // Oculta a mensagem após alguns segundos
            setTimeout(() => {
                recentlySuccessful.value = false;
            }, 3000);
        },
    });
};
</script>

<template>
    <AppLayout title="Criar Conta">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Criar Nova Conta
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow">

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome da Conta</label>
                            <input type="text" v-model="form.nome"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
                            <div v-if="form.errors.nome" class="text-sm text-red-600 mt-1">
                                {{ form.errors.nome }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select v-model="form.tipo"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Selecione</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="conta_bancaria">Conta Bancária</option>
                                <option value="cartao_credito">Cartão de Crédito</option>
                            </select>
                            <div v-if="form.errors.tipo" class="text-sm text-red-600 mt-1">
                                {{ form.errors.tipo }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Saldo Inicial</label>
                            <input type="number" step="0.01" v-model="form.saldo_inicial"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
                            <div v-if="form.errors.saldo_inicial" class="text-sm text-red-600 mt-1">
                                {{ form.errors.saldo_inicial }}
                            </div>
                        </div>

                        <!-- Botão e Mensagem de Sucesso ao lado -->
                        <div class="flex items-center space-x-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none"
                                :disabled="form.processing">
                                Salvar
                            </button>

                            <!-- Mensagem de sucesso ao lado do botão -->
                            <ActionMessage :on="recentlySuccessful" class="text-green-600">
                                Salvo com sucesso.
                            </ActionMessage>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

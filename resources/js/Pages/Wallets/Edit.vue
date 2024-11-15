<template>
    <div>
        <Head :title="language.bank_modification" />

        <page-header>
            <div class="flex justify-between">
                <div>
                    {{ language.bank_modification }}
                </div>
                <div>
                    <BackButton />
                </div>
            </div>
        </page-header>
        <div class="grid gap-4 m-4 lg:gap-7 md:grid-cols-2 lg:m-7">
            <div class="p-3 bg-white border border-gray-200 rounded sm:p-5">
                <h2 class="mb-4 text-lg font-semibold text-gray-700">
                    {{ language.bank_modification }}
                </h2>

                <flash-messages />

                <form @submit.prevent="submit" class="mt-4">
                    <Input
                        id="name"
                        type="text"
                        v-model="form.name"
                        :error="form.errors.name"
                        class="block w-full mt-4"
                        required
                        :placeholder="language.the_name"
                        autocomplete="name"
                    />
                    <InputError :message="form.errors.name" />

                    <Input
                        id="balance"
                        type="number"
                        v-model="form.balance"
                        :error="form.errors.websbalanceite"
                        class="block w-full mt-4"
                        :placeholder="language.balance"
                        autocomplete="balance"
                        step="0.01"
                    />
                    <InputError :message="form.errors.balance" />

                    <progress
                        v-if="form.progress"
                        :value="form.progress.percentage"
                        max="100"
                    >
                        {{ form.progress.percentage }}%
                    </progress>

                    <Button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="px-6 mt-4"
                    >
                        {{ language.bank_modification }}</Button
                    >
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import BackButton from "../../Components/backButton.vue";
export default {
    components: {
        Head,
        PageHeader,
        Button,
        Input,
        Checkbox,
        BreezeValidationErrors,
        FlashMessages,
        SelectInput,
        InputError,
        BackButton,
    },
    layout: BreezeAuthenticatedLayout,

    props: {
        wallet: Object,
        language: Object,
    },

    data() {
        return {
            form: this.$inertia.form({
                name: this.wallet.name,
                balance: this.wallet.balance,
            }),
        };
    },

    methods: {
        submit() {
            this.form.post(this.route("wallets.update", this.wallet), {
                preserveScroll: true,
                onError: () => this.form.reset(),
                onFinish: () => this.form.reset(""),
            });
        },
    },
};
</script>

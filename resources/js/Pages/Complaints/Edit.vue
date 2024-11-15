<template>
    <div>
        <Head :title="language.modify_a_complaint" />

        <page-header>
      <div class="flex justify-between">
        <div>
            {{ language.modify_a_complaint }}
        </div>
        <div>
          <BackButton/>
        </div>
      </div>
    </page-header>
        <div class="grid gap-4 lg:gap-7 md:grid-cols-2 m-4 lg:m-7">
            <!-- Create new shipment -->
            <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
                <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                     {{ language.modify_a_complaint }}
                </h2>

                <flash-messages />

                <form @submit.prevent="submit" class="mt-4">

                    <Input
                        id="shipment"
                        type="text"
                        v-model="form.shipment"
                        :error="form.errors.shipment"
                        class="mt-4 block w-full"
                        required
                        :placeholder="language.Shipment_number"
                        autocomplete="shipment"
                    />
                    <InputError :message="form.errors.shipment" />

                    <Input
                        id="complainant"
                        type="text"
                        v-model="form.complainant"
                        :error="form.errors.complainant"
                        class="mt-4 block w-full"
                        required
                        :placeholder="language.the_name_of_the_complainant"
                    />
                    <InputError :message="form.errors.complainant" />



                    <Textarea
                        id="subject"
                        type="text"
                        v-model="form.subject"
                        :error="form.errors.subject"
                        class="mt-4 mb-4 block w-full"
                        required
                        :placeholder="language.the_subject_of_the_complaint"
                    />
                    <InputError :message="form.errors.subject" />



                    <Button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="mt-4 px-6"
                    >
                        {{ language.save_edit }} </Button
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
import Textarea from "@/Components/Textarea.vue";
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import BackButton from "../../Components/backButton.vue"
export default {
    components: {
        Head,
        PageHeader,
        Button,
        Input,
        Textarea,
        BreezeValidationErrors,
        FlashMessages,
        InputError,
        SelectInput,
        BackButton
    },
    layout: BreezeAuthenticatedLayout,

    props: {
        complaint: Object,language:Object
    },

    data() {
        return {
            form: this.$inertia.form({
                shipment: this.complaint.shipment,
                complainant: this.complaint.complainant,
                subject: this.complaint.subject,
            }),
        };
    },

    methods: {
        submit() {
            this.form.post(this.route("complaints.update", this.complaint), {
                preserveScroll: true,
                //onSuccess: () => this.form.reset(""),
                onError: () => this.form.reset(),
            });
        },
    },
};
</script>

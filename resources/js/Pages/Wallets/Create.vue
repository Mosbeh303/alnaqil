<template>
  <div>
    <Head :title="language.add_a_bank" />



    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.Adding_bank }}
        </div>
        <div>
          <BackButton/>
        </div>
      </div>
    </page-header>

    <div class="grid gap-4 lg:gap-7 md:grid-cols-2 m-4 lg:m-7">
      <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
          {{ language.add_a_new_bank }}
        </h2>

        <flash-messages />

        <form @submit.prevent="submit" class="mt-4">
          <Input
            id="name"
            type="text"
            v-model="form.name"
            :error="form.errors.name"
            class="mt-4 block w-full"
            required
            :placeholder="language.the_name"
            autocomplete="name"
          />
          <InputError :message="form.errors.name" />

          <progress v-if="form.progress" :value="form.progress.percentage" max="100">
            {{ form.progress.percentage }}%
          </progress>

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ language.add_a_bank }}</Button
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
    BackButton
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    language: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        name: "",
        balance: 0,
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("wallets.store"), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },
  },
};
</script>

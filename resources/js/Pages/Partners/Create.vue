<template>
  <div>
    <Head :title="language.add_employee" />

    <page-header> {{ language.add_employee }} </page-header>

    <div class="grid gap-4 lg:gap-7 md:grid-cols-2 m-4 lg:m-7">
      <!-- Create new shipment -->
      <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
          {{ language.add_a_new_partner }}
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

          <Input
            id="website"
            type="text"
            v-model="form.website"
            :error="form.errors.website"
            class="mt-4 block w-full"
            :placeholder="language.website"
            autocomplete="website"
          />
          <InputError :message="form.errors.website" />

          <Input
            id="logo"
            type="file"
            @input="form.logo = $event.target.files[0]"
            :error="form.errors.logo"
            class="mt-4 block w-full"
            :placeholder="language.website"
            autocomplete="website"
          />
          <InputError :message="form.errors.logo" />

          <progress v-if="form.progress" :value="form.progress.percentage" max="100">
            {{ form.progress.percentage }}%
          </progress>

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ language.add_employee }}
          </Button>
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
  },

  props: {
    language: Object,
  },
  layout: BreezeAuthenticatedLayout,

  data() {
    return {
      form: this.$inertia.form({
        name: "",
        website: "",
        logo: "",
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("partners.store"), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },
  },
};
</script>

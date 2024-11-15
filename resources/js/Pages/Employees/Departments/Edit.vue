<template>
  <div>
    <Head :title="language.modify_section" />

    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.modify_section }}
        </div>
        <div>
          <BackButton/>
        </div>
      </div>
    </page-header>
    <flash-messages class="md:m-7 m-2" />

    <form @submit.prevent="submit" class="grid gap-7 md:grid-cols-2 mt-7 px-7 mb-7">
      <Section :title="language.basic_info" class="h-fit">
        <Input
          id="name"
          type="text"
          v-model="form.name"
          :error="form.errors.name"
          class="mt-4 block w-full"
          required
          :placeholder="language.the_name"
        />
        <InputError :message="form.errors.name" />

        <textarea
          id="description"
          type="text"
          v-model="form.description"
          :error="form.errors.name"
          class="mt-4 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
          :placeholder="language.the_description"
        ></textarea>
        <InputError :message="form.errors.identification" />

        <Button
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          class="mt-4 px-6"
        >
          {{ language.modify_section }}</Button
        >
      </Section>
    </form>
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
import Accordion from "@/Components/Accordion.vue";
import InputError from "@/Components/InputError.vue";
import Section from "@/Components/Section.vue";
import BackButton from "../../../Components/backButton.vue"

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Input,
    Checkbox,
    BreezeValidationErrors,
    FlashMessages,
    Accordion,
    InputError,
    Section,
    BackButton
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    depart: Object,
    language: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        description: this.depart.description,
        name: this.depart.name,
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("employee.departments.update", this.depart.id), {
        preserveScroll: true,
        onOrror: () => this.form.reset(),
      });
    },
  },
};
</script>

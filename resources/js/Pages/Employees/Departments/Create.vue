<template>
  <div>
    <Head :title="language.add_a_new_section" />

    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.add_a_new_section }}
        </div>
        <div>
          <BackButton />
        </div>
      </div>
    </page-header>
    <flash-messages class="md:m-7 m-2" />

    <form
      @submit.prevent="submit"
      class="grid gap-4 lg:gap-7 lg:grid-7 md:grid-cols-2 m-4 lg:m-7"
    >
      <Section :title="language.basic_info" class="h-fit">
        <input type="hidden" v-model="form.for" />
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
          :error="form.errors.description"
          class="mt-4 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
          :placeholder="language.the_description"
        ></textarea>
        <InputError :message="form.errors.description" />

        <Button
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          class="mt-4 px-6"
        >
          {{ language.add_the_group }}</Button
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
import BackButton from "../../../Components/backButton.vue";

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
    BackButton,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    permissions: Object,
    groupFor: String,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        description: "",
        name: "",
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("employee.departments.store"), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },
  },
};
</script>

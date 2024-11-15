<template>
  <div>
    <Head :title="language.settings" />

    <page-header> {{ language.general_settings }}</page-header>

    <Layout>
      <Section :title="language.tax_rate">
        <form @submit.prevent="submit('vat')">
          <Label :value="language.add_the_percentage" class="mt-4" />
          <div class="w-full relative">
            <Input
              id="vat"
              type="number"
              min="0"
              max="100"
              v-model="form.value"
              :error="form.errors.value"
              class="mt-2 block w-full"
              required
              :placeholder="language.the_percentage"
              autocomplete="vat"
            />
            <span :class="{'absolute left-8 top-2 text-gray-500' : $page.props.locale == 'ar', 'absolute right-8 top-2 text-gray-500' : $page.props.locale == 'en'}">%</span>
          </div>

          <InputError :message="form.errors.value" />
          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ $page.props.language.save }}
          </Button>
        </form>
      </Section>
    </Layout>
  </div>
</template>

<script>
import Layout from "./Layout.vue";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head, Link } from "@inertiajs/vue3";
import Section from "@/Components/Section.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import Button from "@/Components/Button.vue";
import Icon from "@/Components/Icon.vue";

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Link,
    Section,
    Input,
    Label,
    InputError,
    Button,
    Icon,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    vat: Number,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        value: this.vat,
      }),
    };
  },

  methods: {
    submit(option) {
      this.form.post(this.route("settings.store", option), {
        preserveScroll: true,
        onError: () => this.form.reset("value"),
      });
    },
  },
};
</script>

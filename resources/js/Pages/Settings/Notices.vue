<template>
  <div>
    <Head :title="language.settings" />

    <page-header> {{ language.settings }}</page-header>

    <Layout>
      <Section :title="language.Unsuccessful_delivery_attempts_options">
        <form @submit.prevent="submit()">
          <Label :value="language.add_option" class="mt-4" />
          <Input
            id="notice"
            type="text"
            v-model="form.value"
            :error="form.errors.value"
            class="mt-2 block w-full"
            required
            :placeholder="language.option"
          />
          <InputError :message="form.errors.value" />
          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ language.add }}
          </Button>
        </form>

        <div class="mt-7 border rounded border-gray-100">
          <h3 class="text-md px-4 py-3 bg-gray-100">{{ language.options_menu }}</h3>
          <div class="p-4">
            <ul>
              <li
                v-for="(notice, i) in notices"
                :key="i"
                class="flex gap-2 text-gray-600 mb-2"
              >
                <Icon
                  @click="remove(notice)"
                  name="x"
                  class="w-5 h-5 cursor-pointer text-red-700"
                />
                <span>{{ notice }} </span>
              </li>
              <li
                v-if="notices === null || notices.length === 0"
                class="text-red-500 text-sm flex gap-2"
              >
                <Icon name="warning" class="w-4 h-4" />{{
                  language.there_are_no_options_yet
                }}
              </li>
            </ul>
          </div>
        </div>
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
    notices: Array,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        value: "",
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("settings.storeinarray", "failed_notices"), {
        preserveScroll: true,
        onSuccess: () => this.form.reset("value"),
      });
    },

    remove(value) {
      this.$inertia.post(
        this.route("settings.remove_from_array", ["failed_notices", value]),
        {},
        {
          preserveScroll: true,
        }
      );
    },
  },
};
</script>

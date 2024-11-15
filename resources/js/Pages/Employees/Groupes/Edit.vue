<template>
  <div>
    <Head :title="language.modify_the_group" />

    <page-header>
      <div class="flex justify-between">
        <div>{{ language.modify_the_group }}</div>
        <div>
          <BackButton />
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
          autocomplete="name"
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
          {{ language.modify_the_group }}</Button
        >
      </Section>

      <Section
        v-if="groupe.for != 'rec' && groupe.for != 'del' && groupe.for != 'vip'"
        :title="language.authority"
      >
        <accordion :title="language.control_panel_characteristics" class="mt-4">
          <div class="grid md:grid-cols-3 pb-2">
            <label
              class="flex items-center mt-2"
              v-for="(permission, i) in permissions.dashboard"
              :key="i"
            >
              <Checkbox
                name="permissions"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>
        <accordion :title="language.terms_of_reference_for_shipments">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              v-for="(permission, i) in permissions.shipments"
              :key="i"
            >
              <Checkbox
                name="permissions"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>
        <accordion :title="language.delegates">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              v-for="(permission, i) in permissions.operators"
              :key="i"
            >
              <Checkbox
                name="permissions"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>

        <accordion     :isActive="activeDropdown === 'employees'"
          @click="activeDropdown = 'employees'" :title="language.employees">

          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
              v-for="(permission, i) in permissions.employees"
              :key="i"
            >
              <Checkbox
                name="permissions"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>

        <accordion :title="language.clients">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              v-for="(permission, i) in permissions.stores"
              :key="i"
            >
              <Checkbox
                name="permissions"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>

        <accordion :title="language.accounts">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              v-for="(permission, i) in permissions.accounts"
              :key="i"
            >
              <Checkbox
                name="permissions"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>

        <accordion :title="language.reports" :isActive="activeDropdown === 'reports'"
          @click="activeDropdown = 'reports'">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
              v-for="(permission, i) in permissions.reports"
              :key="i"
            >
              <Checkbox
                name="reports"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>

        <accordion :title="language.marketers" :isActive="activeDropdown === 'marketers'"
          @click="activeDropdown = 'marketers'">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
              v-for="(permission, i) in permissions.marketers"
              :key="i"
            >
              <Checkbox
                name="permissions"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>

        <accordion :title="language.other">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              v-for="(permission, i) in permissions.others"
              :key="i"
            >
              <Checkbox
                name="permissions"
                :value="i"
                v-model:checked="form.permissions"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
            </label>
          </div>
        </accordion>
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
    groupe: Object,
    language: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        description: this.groupe.description,
        permissions: this.groupe.permissions,
        name: this.groupe.name,
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("groupes.update", this.groupe.id), {
        preserveScroll: true,
        onOrror: () => this.form.reset(),
      });
    },
  },
};
</script>

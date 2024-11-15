<template>
  <div>
    <Head :title="language.add_a_new_group" />

    <page-header>

      <div class="flex justify-between">
        <div>
          {{ language.add_a_new_group }}
        </div>
        <div>
        <BackButton/>
        </div>
      </div>
      <template v-if="depart" v-slot:breadcrumb>
        <Link href="/dashboard">{{ language.dashboard }}</Link>
        <span aria-hidden="true" class="mx-2">&gt;</span>
        <Link :href="route('employee.departments')"> {{ language.sections }}</Link>
        <span aria-hidden="true" class="mx-2">&gt;</span>
        <Link :href="route('employee.groupes', depart.id)">
          {{ language.section }} {{ depart.name }}</Link
        >
      </template>
    </page-header>

    <flash-messages class="md:m-7 m-2" />

    <form
      @submit.prevent="submit"
      class="grid gap-4 lg:gap-7 lg:grid-7 md:grid-cols-2 m-4 lg:m-7"
    >
      <Section :title="language.basic_info" class="h-fit">
        <input type="hidden" v-model="form.for" />
        <input v-if="depart" type="hidden" v-model="form.depart" />
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

      <Section :title="language.authority" v-if="!groupFor">
        <accordion
          :isActive="activeDropdown === 'control_panel_characteristics'"
          @click="activeDropdown = 'control_panel_characteristics'"
          :title="language.control_panel_characteristics"
          class="mt-4"
        >
          <div class="grid md:grid-cols-3 pb-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
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
        <accordion
        :isActive="activeDropdown === 'terms_of_reference_for_shipments'"
          @click="activeDropdown = 'terms_of_reference_for_shipments'"
        :title="language.terms_of_reference_for_shipments">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
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
        <accordion     :isActive="activeDropdown === 'delegates'"
          @click="activeDropdown = 'delegates'" :title="language.delegates">

          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
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

        <accordion :title="language.clients"  :isActive="activeDropdown === 'clients'"
          @click="activeDropdown = 'clients'" >
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
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

        <accordion :title="language.accounts" :isActive="activeDropdown === 'accounts'"
          @click="activeDropdown = 'accounts'">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
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
                v-model:checked="form.reports"
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

        <accordion :title="language.other" :isActive="activeDropdown === 'other'"
          @click="activeDropdown = 'other'">
          <div class="grid md:grid-cols-3 p-2">
            <label
              class="flex items-center mt-2"
              :class="{ 'space-x-2': $page.props.locale == 'en' }"
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
import { Head, Link } from "@inertiajs/vue3";
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
    Link,BackButton
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    permissions: Object,
    groupFor: String,
    depart: Object,
    language: Object,
  },

  data() {
    return {activeDropdown :null,
      form: this.$inertia.form({
        description: "",
        permissions: [],
        name: "",
        depart: this.depart ? this.depart.id : null,
        for: this.groupFor,
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("groupes.store"), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },
  },
};
</script>

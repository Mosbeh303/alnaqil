<template>
  <div>
    <Head :title="language.registration_city" />

    <page-header class="flex"> {{ language.city }} {{ city.name }} </page-header>
    <flash-messages />
    <Section
      :title="language.registration_city_branches + `     ${branches.length}`"
      class="md:m-7 m-4"
    >
      <div class="grid md:gap-7 gap-4 xl:grid-cols-5 lg:grid-cols-3 grid-cols-2 mt-4">
        <div
          v-for="(branch, i) in branches"
          :key="i"
          class="flex justify-between flex-col items-center border border-gray-200 text-gray-700 rounded py-6 px-4"
        >
          <div class="text-3xl font-bold my-2 flex"></div>
          <div class="text-center">
            <h3 class="text-lg font-semibold text-center">
              {{ branch.name }}
            </h3>

            <div class="flex justify-center gap-2 items-center w-full">
              <button class="cursor-pointer" @click="editBranch(branch)">
                <Icon name="edit" class="w-5 h-5 text-green-600" />
              </button>

              <button class="cursor-pointer" @click="deleteBranch(branch)">
                <Icon name="cancel" class="w-5 h-5 text-red-600" />
              </button>
            </div>
          </div>
        </div>

        <div
          @click="showModal = true"
          class="flex justify-center flex-col items-center bg-gray-200 text-gray-400 rounded cursor-pointer py-6 px-4"
        >
          <icon name="plus" class="w-8 h-8 my-2" />
          <h3 class="text-lg font-semibold">{{ language.add_a_branch }}</h3>
        </div>
      </div>
    </Section>

    <Modal :show="showModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.adding_branch }}
      </h2>

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
          {{ language.add }}
        </Button>
        <Button
          @click="showModal = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ language.cancel }}</Button
        >
      </form>
    </Modal>

    <Modal :show="showUpdateModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.modify_a_branch }}
      </h2>

      <form @submit.prevent="submitEditBranchForm" class="mt-4">
        <Input
          id="name"
          type="text"
          v-model="formEditBranch.name"
          :error="formEditBranch.errors.name"
          class="mt-4 block w-full"
          required
          :placeholder="language.the_name"
          autocomplete="name"
        />
        <InputError :message="formEditBranch.errors.name" />

        <progress
          v-if="formEditBranch.progress"
          :value="formEditBranch.progress.percentage"
          max="100"
        >
          {{ formEditBranch.progress.percentage }}%
        </progress>

        <Button
          :class="{ 'opacity-25': formEditBranch.processing }"
          :disabled="formEditBranch.processing"
          class="mt-4 px-6"
        >
          {{ language.save }}
        </Button>
        <Button
          @click="showUpdateModal = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ language.cancel }}</Button
        >
      </form>
    </Modal>

    <Modal :show="showDeleteBranchAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.are_you_sure }}
      </h2>
      <p class="text-base text-gray-600">
        {{ language.do_you_want_to_confirm_deletion_of_the_branch }}
      </p>

      <Button
        class="mt-4 !border"
        :href="deleteBranchLink"
        method="post"
        @click="showDeleteBranchAlert = false"
      >
        <span>{{ language.delete }}</span>
      </Button>
      <Button
        @click="showDeleteBranchAlert = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/vue3";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import Section from "@/Components/Section.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import Checkbox from "@/Components/Checkbox.vue";

import InputError from "@/Components/InputError.vue";
import Icon from "@/Components/Icon.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Modal from "@/Components/Modal.vue";

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Section,
    Icon,
    Link,
    FlashMessages,
    Modal,
    Input,
    InputError,
    Checkbox,
    Label,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    branches: Object,
    city: Object,
    language: Object,
  },

  data() {
    return {
      showModal: false,
      showUpdateModal: false,
      showDeleteBranchAlert: false,
      deleteBranchLink: "",
      targetBranch: {},
      index: null,
      form: this.$inertia.form({
        name: "",
      }),
      formEditBranch: this.$inertia.form({
        name: "",
      }),
    };
  },
  methods: {
    deleteBranch(branch) {
      this.deleteBranchLink = route("settings.cities.branches.delete", branch);
      this.showDeleteBranchAlert = true;
    },
    submit() {
      this.form.post(this.route("settings.cities.branches.store", this.city), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
      });
      this.showModal = false;
    },
    editBranch(branch) {
      this.showUpdateModal = true;
      this.targetBranch = branch;
      this.formEditBranch.name = branch.name;
    },

    submitEditBranchForm() {
      this.formEditBranch.post(
        this.route("settings.cities.branches.update", this.targetBranch),
        {
          preserveScroll: true,
        }
      );
      this.showUpdateModal = false;
    },
  },
};
</script>

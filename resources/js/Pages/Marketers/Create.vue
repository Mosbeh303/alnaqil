<template>
    <div>
      <Head :title="language.add_marketer" />

      <page-header>
        <div class="flex justify-between">
          <div>
            {{ language.add_marketer }}
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
            {{ language.add_new_marketer }}
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

            <!-- <Input
              id="id_number"
              type="text"
              v-model="form.id_number"
              :error="form.errors.id_number"
              class="mt-4 block w-full"
              :placeholder="language.id_number"
            />
            <InputError :message="form.errors.id_number" /> -->

            <Input
              id="username"
              type="text"
              v-model="form.username"
              :error="form.errors.username"
              class="mt-4 block w-full"
              required
              :placeholder="language.username"
              autocomplete="lastname"
            />
            <InputError :message="form.errors.username" />

            <Input
              id="password"
              type="text"
              v-model="form.password"
              class="mt-4 block w-full"
              :error="form.errors.password"
              :placeholder="language.mot_de_passe"
              autocomplete="password"
            />
            <InputError :message="form.errors.password" />

            <Input
              id="phone"
              type="text"
              v-model="form.phone"
              class="mt-4 block w-full"
              :error="form.errors.phone"
              :placeholder="language.phone_number"
              autocomplete="phone"
            />
            <InputError :message="form.errors.phone" />

            <Input
              id="email"
              type="email"
              v-model="form.email"
              class="mt-4 mb-4 block w-full"
              :error="form.errors.email"
              :placeholder="language.email"
              autocomplete="email"
            />
            <InputError :message="form.errors.email" />

            <SelectInput
              v-model="form.commission_type"
              :error="form.errors.commission_type"
              class="mt-4 block w-full"
              :selectPlaceholder="language.commission_type"
            >
              <option value="fixed">
                {{ language.fixed }}
              </option>
              <option value="percentage">
                {{ language.percentage }}
              </option>
            </SelectInput>

            <Input
              id="commission"
              type="number"
              step="0.01"
              v-model="form.commission"
              class="mt-4 mb-4 block w-full"
              :error="form.errors.commission"
              :placeholder="language.commission"
              autocomplete="email"
            />
            <InputError :message="form.errors.commission" />

            <div class="mb-4"></div>



            <Button
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="mt-4 px-6"
            >
              {{ language.add }}</Button
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
          phone: "",
          username: "",
          email: "",
          password: "",
          phone: "",
          commission_type: "",
          commission: "",
        }),
      };
    },

    methods: {

      submit() {
        this.form.post(this.route("marketers.store"), {
          preserveScroll: true,
          onSuccess: () => this.form.reset(),
          onFinish: () => this.form.reset(""),
        });
      },


    },
  };
  </script>

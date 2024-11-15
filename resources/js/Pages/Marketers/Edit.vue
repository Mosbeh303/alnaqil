<template>
    <div>
      <Head :title="language.edit_marketer" />

      <page-header>
        <div class="flex justify-between">
          <div>
            {{ language.edit_marketer }}
          </div>
          <div>
           <BackButton/>
          </div>
        </div>


       </page-header>

       <flash-messages />

      <div class="grid gap-4 lg:gap-7 md:grid-cols-2 m-4 lg:m-7">

        <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.data }}
          </h2>



          <form @submit.prevent="submit" class="mt-4">

            <Label class="mt-4" :value="$page.props.language.the_name" />
            <Input
              id="name"
              type="text"
              v-model="form.name"
              :error="form.errors.name"
              class="mt-2 block w-full"
              required
              :placeholder="language.the_name"
              autocomplete="name"
            />
            <InputError :message="form.errors.name" />

            <Label class="mt-4" :value="$page.props.language.username" />
            <Input
              id="username"
              type="text"
              v-model="form.username"
              :error="form.errors.username"
              class="mt-2 block w-full"
              required
              :placeholder="language.username"
              autocomplete="lastname"
            />
            <InputError :message="form.errors.username" />


            <Label class="mt-4" :value="$page.props.language.phone_number" />
            <Input
              id="phone"
              type="text"
              v-model="form.phone"
              class="mt-2 block w-full"
              :error="form.errors.phone"
              :placeholder="language.phone_number"
              autocomplete="phone"
            />
            <InputError :message="form.errors.phone" />

            <Label class="mt-4" :value="$page.props.language.email" />
            <Input
              id="email"
              type="email"
              v-model="form.email"
              class="mt-2  block w-full"
              :error="form.errors.email"
              :placeholder="language.email"
              autocomplete="email"
            />
            <InputError :message="form.errors.email" />

            <Label class="mt-4" :value="$page.props.language.commission_type" />
            <SelectInput
              v-model="form.commission_type"
              :error="form.errors.commission_type"
              class="mt-2 block w-full"
              :selectPlaceholder="language.commission_type"
            >
              <option value="fixed">
                {{ language.fixed }}
              </option>
              <option value="percentage">
                {{ language.percentage }}
              </option>
            </SelectInput>

            <Label class="mt-4" :value="$page.props.language.commission" />
            <Input
              id="commission"
              type="number"
              step="0.01"
              v-model="form.commission"
              class="mt-2 block w-full"
              :error="form.errors.commission"
              :placeholder="language.commission"
              autocomplete="email"
            />
            <InputError :message="form.errors.commission" />




            <Button
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="mt-4 px-6"
            >
              {{ language.edit }}</Button
            >
          </form>
        </div>

        <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white h-fit">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.modify_the_password }}
          </h2>



          <form @submit.prevent="submitUpdatePassword" class="mt-4">

            <Label class="mt-4" :value="$page.props.language.mot_de_passe" />
            <Input
              id="password"
              type="password"
              v-model="formPassword.password"
              class="mt-2 block w-full"
              :error="formPassword.errors.password"
              :placeholder="language.mot_de_passe"
              autocomplete="password"
            />
            <InputError :message="formPassword.errors.password" />

            <Button
              :class="{ 'opacity-25': formPassword.processing }"
              :disabled="formPassword.processing"
              class="mt-4 px-6"
            >
              {{ language.edit }}</Button
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
  import Label from "../../Components/Label.vue";

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
      BackButton,
      Label
    },
    layout: BreezeAuthenticatedLayout,

    props: {
      language: Object,
      marketer: Object,
    },
    data() {
      return {
        form: this.$inertia.form({
          name: this.marketer.name,
          phone: this.marketer.phone,
          username: this.marketer.username,
          email: this.marketer.email,
          commission_type: this.marketer.commission_type,
          commission: this.marketer.commission,
        }),
        formPassword: this.$inertia.form({

          password: "",

        }),
      };
    },

    methods: {

      submit() {
        this.form.post(this.route("marketers.update", this.marketer), {
          preserveScroll: true,
          onFinish: () => this.form.reset(),
        });
      },

      submitUpdatePassword() {
        this.formPassword.post(this.route("users.update_password", this.marketer.user_id), {
          preserveScroll: true,
          onSuccess: () => this.form.reset(),
          onFinish: () => this.form.reset(""),
        });
      },


    },
  };
  </script>

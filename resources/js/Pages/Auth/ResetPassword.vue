<template>
  <Head title="Reset Password" />

  <div class="mb-8">
    <h1 class="font-medium text-2xl text-center">{{ language.reset_your_password }}</h1>
    <p class="text-center">{{ language.register_the_new_password }}</p>
  </div>

  <BreezeValidationErrors class="mb-4" />

  <form @submit.prevent="submit">
    <div>
      <BreezeLabel for="email" :value="language.email" />
      <BreezeInput
        id="email"
        type="email"
        class="mt-1 block w-full"
        v-model="form.email"
        required
        autofocus
        autocomplete="username"
      />
    </div>

    <div class="mt-4">
      <BreezeLabel for="password" :value="language.mot_de_passe" />
      <BreezeInput
        id="password"
        type="password"
        class="mt-1 block w-full"
        v-model="form.password"
        required
        autocomplete="new-password"
      />
    </div>

    <div class="mt-4">
      <BreezeLabel for="password_confirmation" :value="language.confirm_password" />
      <BreezeInput
        id="password_confirmation"
        type="password"
        class="mt-1 block w-full"
        v-model="form.password_confirmation"
        required
        autocomplete="new-password"
      />
    </div>

    <div class="flex items-center justify-end mt-4">
      <BreezeButton
        class="w-full mt-2 justify-center"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        {{ language.register_the_password }}
      </BreezeButton>
    </div>
  </form>
</template>

<script>
import BreezeButton from "@/Components/Button.vue";
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import BreezeInput from "@/Components/Input.vue";
import BreezeLabel from "@/Components/Label.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import { Head } from "@inertiajs/vue3";

export default {
  layout: BreezeGuestLayout,

  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Head,
  },

  props: {
    email: String,
    token: String,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        token: this.token,
        email: this.email,
        password: "",
        password_confirmation: "",
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("password.update"), {
        onFinish: () => this.form.reset("password", "password_confirmation"),
      });
    },
  },
};
</script>

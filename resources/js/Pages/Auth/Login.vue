<template>
  <Head title="Log in" />

  <div v-if="sallaUserName" class="mb-8">
    <h1 class="font-medium text-2xl text-center">
      {{ language.welcomeFirstTime }} {{ sallaUserName }}
      {{ language.thank_you_for_installing_our_application }}
    </h1>
    <p class="text-center">{{ language.please_login_to_your_account }}</p>
  </div>

  <div v-else class="mb-8">
    <h1 class="font-medium text-2xl text-center">{{ language.welcome }}</h1>
    <p class="text-center">{{ language.sign_in_to_continue }}</p>
  </div>

  <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
    <Alert>
      {{ status }}
    </Alert>
  </div>

  <BreezeValidationErrors class="mb-4" />

  <form @submit.prevent="submit">
    <div>
      <BreezeLabel for="username" :value="language.username" />
      <BreezeInput
        id="username"
        type="text"
        class="mt-1 block w-full"
        v-model="form.username"
        required
        autofocus
        autocomplete="username"
      />
    </div>

    <div class="mt-4">
      <div class="flex align-items-center justify-between">
        <BreezeLabel for="password" :value="language.password" />
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="underline text-sm text-gray-600 hover:text-gray-900"
        >
          {{ language.forget }}
        </Link>
      </div>

      <BreezeInput
        id="password"
        type="password"
        class="mt-1 block w-full"
        v-model="form.password"
        required
        autocomplete="current-password"
      />
    </div>

    <div class="block mt-4">
      <label class="flex items-center">
        <BreezeCheckbox name="remember" v-model:checked="form.remember" />
        <span class="mr-2 text-sm text-gray-600"> {{ language.remember_me }}</span>
      </label>
    </div>

    <div class="mt-4">
      <BreezeButton
        class="ml-4 w-full justify-center"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        {{ language.sign_in }}
      </BreezeButton>
    </div>
    <div class="mt-2">
      <span>{{ language.dont_have_account }}</span>
      <Link
        :href="route('register')"
        class="underline text-sm text-gray-600 hover:text-gray-900"
        >{{ language.register_new }}</Link
      >
    </div>
    <a href="/" class="underline text-sm text-gray-600 hover:text-gray-900">{{
      language.back_home
    }}</a>
  </form>
</template>

<script>
import BreezeButton from "@/Components/Button.vue";
import BreezeCheckbox from "@/Components/Checkbox.vue";
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import BreezeInput from "@/Components/Input.vue";
import BreezeLabel from "@/Components/Label.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import { Head, Link } from "@inertiajs/vue3";
import Alert from "@/Components/Alert.vue";

export default {
  layout: BreezeGuestLayout,

  components: {
    BreezeButton,
    BreezeCheckbox,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Head,
    Link,
    Alert,
  },

  props: {
    canResetPassword: Boolean,
    status: String,
    language: Object,
    ussallaUserNameer: {
      type: String,
      default: null,
    },
  },

  data() {
    return {
      form: this.$inertia.form({
        username: "",
        password: "",
        remember: false,
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("login"), {
        onFinish: () => this.form.reset("password"),
      });
    },
  },
};
</script>

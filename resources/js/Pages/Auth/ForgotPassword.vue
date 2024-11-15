<template>
  <Head title="Forgot Password" />

  <div class="mb-4 text-sm text-gray-600">
    {{ language.forget }} <br />
    {{ language.no_problem }}
  </div>

  <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
    <Alert>
      {{ status }}
    </Alert>
  </div>

  <BreezeValidationErrors class="mb-4" />

  <form @submit.prevent="submit">
    <div>
      <BreezeLabel for="email" v-bind:value="language.email" />
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

    <div class="flex items-center justify-end mt-4">
      <BreezeButton
        class="w-full justify-center"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        {{ language.send }}
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
import Alert from "@/Components/Alert.vue";

export default {
  layout: BreezeGuestLayout,

  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Head,
    Alert,
  },

  props: {
    status: String,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        email: "",
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("password.email"));
    },
  },
};
</script>

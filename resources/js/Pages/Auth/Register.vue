<template>
  <Head title="Register" />

  <div class="mb-8">
    <h1 class="font-medium text-2xl text-center">
      {{ language.create_a_merchant_account }}
    </h1>
    <p class="text-center">
      {{ language.create_a_merchant_account_and_enjoy_the_fastest_shipping_experience }}
    </p>
  </div>

  <BreezeValidationErrors class="mb-4" />

  <form @submit.prevent="submit">
    <h3 class="text-gray-400 font-semibold mb-4 w-fit">{{ language.basic_info }}</h3>
    <div class="flex w-full mt-4 gap-5">
      <div class="grow">
        <BreezeLabel for="storeName" :value="language.company_or_store_name" />
        <BreezeInput
          id="storeName"
          type="text"
          class="mt-1 block w-full"
          v-model="form.storeName"
          required
          autofocus
          autocomplete="storeName"
        />
      </div>

      <div class="grow">
        <BreezeLabel for="name" :value="language.name_shop_owner_or_company_name" />
        <BreezeInput
          id="name"
          type="text"
          class="mt-1 block w-full"
          v-model="form.name"
          required
          autofocus
          autocomplete="name"
        />
      </div>
    </div>

    <div class="flex w-full mt-4 gap-5">
      <div class="grow">
        <BreezeLabel for="username" :value="language.username" />
        <BreezeInput
          id="phone"
          type="text"
          class="mt-1 block w-full"
          v-model="form.username"
          required
          autofocus
          autocomplete="username"
        />
      </div>

      <div class="grow">
        <BreezeLabel for="email" :value="language.email" />
        <BreezeInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          required
          autocomplete="email"
        />
      </div>
    </div>

    <div class="mt-4">
      <BreezeLabel for="phone" :value="language.phone_number" />
      <BreezeInput
        id="phone"
        type="text"
        class="mt-1 block w-full"
        v-model="form.phone"
        required
        autofocus
        autocomplete="phone"
      />
    </div>

    <div class="flex w-full mt-4 gap-5">
      <div class="grow">
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

      <div class="grow">
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
    </div>

    <div class="mt-4">
      <BreezeLabel for="website" :value="language.your_website_link" />
      <BreezeInput
        id="website"
        type="text"
        class="mt-1 block w-full"
        v-model="form.website"
        autofocus
        autocomplete="website"
      />
    </div>

    <h3 class="text-gray-400 font-semibold mb-4 mt-8 w-fit">
      {{ language.bank_information }}
    </h3>

    <div class="flex w-full mt-4 gap-5">
      <div class="grow">
        <BreezeLabel for="bankName" :value="language.bank_name" />
        <BreezeInput
          id="bankName"
          type="text"
          class="mt-1 block w-full"
          v-model="form.bankName"
          autofocus
          autocomplete="bankName"
        />
      </div>

      <div class="grow">
        <BreezeLabel
          for="ownerName"
          :value="language.the_name_of_the_bank_account_holder"
        />
        <BreezeInput
          id="ownerName"
          type="text"
          class="mt-1 block w-full"
          v-model="form.ownerName"
          autofocus
          autocomplete="ownerName"
        />
      </div>
    </div>

    <div class="mt-4">
      <BreezeLabel for="rib" :value="language.bank_account_number" />
      <BreezeInput
        id="rib"
        type="text"
        class="mt-1 block w-full"
        v-model="form.rib"
        autofocus
        autocomplete="rib"
      />
    </div>

    <h3 class="text-gray-400 font-semibold mb-4 mt-8 w-fit">{{ language.address }}</h3>

    <div class="flex w-full mt-4 gap-5">
      <div class="grow">
        <BreezeLabel for="city" :value="language.city" />
        <SelectInput
          v-model="form.city"
          class="mt-2 block w-full"
          :selectPlaceholder="language.city"
          required
        >
          <option v-for="(city, i) in cities" :key="i" :value="city.name">
            {{ city.name }}
          </option>
          <option v-if="cities.length === 0" :value="language.alriyadh">
            {{ language.alriyadh }}
          </option>
        </SelectInput>
      </div>

      <div class="grow">
        <BreezeLabel for="neighborhood" :value="language.Neighborhood" />
        <BreezeInput
          id="neighborhood"
          type="text"
          class="mt-2 block w-full"
          v-model="form.neighborhood"
          autofocus
          autocomplete="neighborhood"
        />
      </div>
    </div>

    <!-- <div class="mt-4 w-">
            <div class="form-group">
                <div id="map"></div>
                <input id="location" type="text" hidden name="map_location" />
            </div>
        </div> -->

    <div class="mt-4">
      <label class="flex items-center">
        <Checkbox v-model:checked="form.accepted" required />
        <span class="mr-2 text-sm text-gray-600">
          {{ language.agree_to }}
          <a href="#" class="underline text-gray-600 hover:text-gray-90"
            >{{ language.terms_and_Conditions }}
          </a></span
        >
      </label>
      <BreezeButton
        class="ml-4 w-full my-3 justify-center"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        {{ language.save_account }}
      </BreezeButton>
      <span class="text-sm">{{ language.already_registered }}</span>
      <Link
        :href="route('login')"
        class="underline text-sm text-gray-600 hover:text-gray-900"
      >
        {{ language.login }}
      </Link>
    </div>
  </form>
</template>

<script>
import BreezeButton from "@/Components/Button.vue";
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import BreezeInput from "@/Components/Input.vue";
import BreezeLabel from "@/Components/Label.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import { Head, Link } from "@inertiajs/vue3";
import SelectInput from "@/Components/SelectInput.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  layout: BreezeGuestLayout,

  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Head,
    Link,
    SelectInput,
    Checkbox,
  },

  data() {
    return {
      form: this.$inertia.form({
        name: "",
        email: "",
        username: "",
        password: "",
        password_confirmation: "",
        storeName: "",
        phone: "",
        website: "",
        bankName: "",
        ownerName: "",
        rib: "",
        city: "",
        neighborhood: "",
        terms: false,
        accepted: false,
      }),
    };
  },

  props: {
    cities: Object,
    language: Object,
  },

  methods: {
    submit() {
      this.form.post(this.route("register"), {
        onFinish: () => this.form.reset("password", "password_confirmation"),
      });
    },
  },
};
</script>

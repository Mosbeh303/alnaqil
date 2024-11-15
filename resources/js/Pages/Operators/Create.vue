<template>
  <div>
    <Head :title="$page.props.language.add_a_delivery_representative" />

    <page-header>
      <div class="flex justify-between">
        <div>
          {{ $page.props.language.add_a_delivery_representative }}
        </div>
        <div>
          <BackButton />
        </div>
      </div>
    </page-header>
    <div class="grid gap-4 lg:gap-7 md:grid-cols-2 m-4 lg:m-7">
      <!-- Create new shipment -->
      <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
          {{ $page.props.language.add_a_new_delegate }}
        </h2>

        <flash-messages />

        <form @submit.prevent="submit" class="mt-4">
          <input type="hidden" v-model="form.groupe" />

          <SelectInput
            v-model="form.type"
            :error="form.errors.type"
            class="mt-4 block w-full"
            :selectPlaceholder="$page.props.language.city"
          >
            <option value="del">{{ $page.props.language.delivery_delegate }}</option>
            <option value="rec">{{ $page.props.language.receipt_delegate }}</option>
            <option value="vip">{{ $page.props.language.a_special_delegate }}</option>
          </SelectInput>

          <Input
            id="name"
            type="text"
            v-model="form.name"
            :error="form.errors.name"
            class="mt-4 block w-full"
            required
            :placeholder="$page.props.language.the_name"
            autocomplete="name"
          />
          <InputError :message="form.errors.name" />

          <Input
            id="name"
            type="text"
            v-model="form.identification"
            :error="form.errors.identification"
            class="mt-4 block w-full"
            :placeholder="$page.props.language.id_number"
            autocomplete="name"
          />
          <InputError :message="form.errors.identification" />

          <Input
            id="username"
            type="text"
            v-model="form.username"
            :error="form.errors.username"
            class="mt-4 block w-full"
            required
            :placeholder="$page.props.language.username"
            autocomplete="lastname"
          />
          <InputError :message="form.errors.username" />

          <Input
            id="password"
            type="text"
            v-model="form.password"
            class="mt-4 block w-full"
            :error="form.errors.password"
            :placeholder="$page.props.language.mot_de_passe"
            autocomplete="password"
          />
          <InputError :message="form.errors.password" />

          <Input
            id="phone"
            type="text"
            v-model="form.phone"
            class="mt-4 block w-full"
            :error="form.errors.phone"
            :placeholder="$page.props.language.phone_number"
            autocomplete="phone"
          />
          <InputError :message="form.errors.phone" />

          <Input
            id="email"
            type="email"
            v-model="form.email"
            class="mt-4 mb-4 block w-full"
            :error="form.errors.email"
            :placeholder="$page.props.language.email"
            autocomplete="email"
          />
          <InputError :message="form.errors.email" />

          <SelectInput
            v-model="form.city"
            :error="form.errors.city"
            class="mt-4 block w-full"
            :selectPlaceholder="$page.props.language.city"
            @change="cityChanged()"
          >
            <option v-for="(city, i) in cities" :key="i" :value="city.id">
              {{ city.name }}
            </option>
            <option v-if="cities.length === 0" :value="null">
              {{ $page.props.language.alriyadh }}
            </option>
          </SelectInput>

          <div class="mb-4"></div>

          <SelectInput
            v-model="form.branch"
            :error="form.branch.type"
            class="mt-4 block w-full"
            :selectPlaceholder="$page.props.language.branch"
          >
            <option v-for="(branch, i) in branches" :key="i" :value="branch.id">
              {{ branch.name }}
            </option>
          </SelectInput>

          <Input
            id="neighborhood"
            type="text"
            v-model="form.neighborhood"
            class="mt-4 block w-full"
            :error="form.errors.neighborhood"
            :placeholder="$page.props.language.neighborhood"
            autocomplete="email"
          />
          <InputError :message="form.errors.neighborhood" />

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ $page.props.language.add_the_delegate }}</Button
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
    BackButton,
  },
  layout: BreezeAuthenticatedLayout,

  props: ["type", "cities", "groupe"],

  data() {
    return {
      form: this.$inertia.form({
        type: this.type,
        name: "",
        lastname: "",
        receiverPhone: "",
        username: "",
        email: "",
        details: "",
        password: "",
        phone: "",
        city: "",
        neighborhood: "",
        identification: "",
        branch: "",
        groupe: this.groupe,
      }),
      branches: "",
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("operator.store"), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },

    getBranches() {
      let city = this.form.city;
      console.log("city:" + city);

      axios.get("/dashboard/branches/" + city).then((response) => {
        // console.log(response);
        this.branches = response.data;
      });
    },

    cityChanged() {
      setTimeout(() => {
        this.getBranches();
      }, 100);
    },
  },
};
</script>

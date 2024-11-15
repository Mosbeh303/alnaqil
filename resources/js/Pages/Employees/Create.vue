<template>
  <div>
    <Head :title="language.add_a_delivery_representative" />

    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.Adding_a_new_employee }}
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
          {{ language.add_new_employe }}
        </h2>

        <flash-messages />

        <form @submit.prevent="submit" class="mt-4">
          <SelectInput
            v-model="form.groupe"
            :error="form.errors.groupe"
            class="mt-4 block w-full"
            :selectPlaceholder="language.select_the_group"
          >
            <option v-for="groupe in groupes" :key="groupe.id" :value="groupe.id">
              {{ groupe.name }}
            </option>
          </SelectInput>

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

          <Input
            id="id_number"
            type="text"
            v-model="form.id_number"
            :error="form.errors.id_number"
            class="mt-4 block w-full"
            :placeholder="language.id_number"
          />
          <InputError :message="form.errors.id_number" />

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
            v-model="form.city"
            :error="form.errors.city"
            class="mt-4 block w-full"
            :selectPlaceholder="language.city"
            @change="cityChanged()"
          >
            <option v-for="(city, i) in cities" :key="i" :value="city.id">
              {{ city.name }}
            </option>
            <!-- <option v-if="cities.length === 0" value="null">الرياض</option> -->
          </SelectInput>

          <div class="mb-4"></div>

          <SelectInput
            v-model="form.branch"
            :error="form.branch.type"
            class="mt-4 block w-full"
            :selectPlaceholder="language.branch"
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
            :placeholder="language.neighborhood"
            autocomplete="email"
          />
          <InputError :message="form.errors.neighborhood" />

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ language.add_the_employee }}</Button
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
    groupes: Object,
    groupe: Number,
    cities: Object,
    language: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        groupe: this.groupe,
        name: "",
        lastname: "",
        receiverPhone: "",
        username: "",
        email: "",
        details: "",
        password: "",
        phone: "",
        id_number: "",
        city: "",
        neighborhood: "",
        branch: "",
      }),
      branches: "",
    };
  },

  methods: {

    submit() {
      this.form.post(this.route("employee.store"), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },

    getBranches() {
      let city = this.form.city;
      console.log("city:" + city);

      axios.get("/dashboard/branches/" + city).then((response) => {
        //console.log(response);
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

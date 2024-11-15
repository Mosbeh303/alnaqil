<template>
  <div>
    <Head :title="language.modify_delegate_account" />



    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.modify_delegate_account }}  ({{ operator.name }})
        </div>
        <div>
         <BackButton/>
        </div>
      </div>
    </page-header>

    <flash-messages class="m-7" />

    <div class="grid gap-7 md:grid-cols-2 mt-7 px-7 mb-7">
      <!-- Create new shipment -->
      <Section :title="language.modify_the_data">
        <form @submit.prevent="submit" class="mt-4">
          <SelectInput
            v-model="form.type"
            :error="form.errors.type"
            class="mt-4 block w-full"
            :selectPlaceholder="language.city"
          >
            <option value="del">{{ language.delivery_delegate }}</option>
            <option value="rec">{{ language.receipt_delegate }}</option>
            <option value="vip">{{ language.a_special_delegate }}</option>
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
            id="identification"
            type="text"
            v-model="form.identification"
            :error="form.errors.identification"
            class="mt-4 block w-full"
            :placeholder="language.id_number"
          />
          <InputError :message="form.errors.identification" />

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
            <option v-if="cities.length === 0" :value="language.alriyadh">
              {{ language.alriyadh }}
            </option>
          </SelectInput>

          <div class="mb-4"></div>

          <SelectInput
            v-model="form.branch"
            :error="form.errors.branch"
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
            {{ language.modify }}</Button
          >
        </form>
      </Section>

      <Section :title="language.modify_the_password" class="h-fit">
        <form @submit.prevent="submitUpdatePassword" class="mt-4">
          <Input
            id="password"
            type="password"
            v-model="formUpdatePassword.password"
            :error="formUpdatePassword.errors.password"
            class="mt-4 block w-full"
            required
            :placeholder="language.mot_de_passe"
            autocomplete="name"
          />
          <InputError :message="formUpdatePassword.errors.password" />

          <Button
            :class="{ 'opacity-25': formUpdatePassword.processing }"
            :disabled="formUpdatePassword.processing"
            class="mt-4 px-6"
          >
            {{ language.modify }}</Button
          >
        </form>
      </Section>
    </div>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Section from "@/Components/Section.vue";
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import BackButton from "../../Components/backButton.vue"

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Input,
    Section,
    BreezeValidationErrors,
    FlashMessages,
    SelectInput,
    InputError,
    BackButton
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    operator: Object,
    cities: Object,
    branchesProps: Object,
    language: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        type: this.operator.type,
        name: this.operator.name,
        username: this.operator.username,
        email: this.operator.email,
        phone: this.operator.phone,
        password: "",
        city: this.operator.city,
        neighborhood: this.operator.neighborhood,
        identification: this.operator.identification,
        branch: this.operator.branch_id,
      }),
      formUpdatePassword: this.$inertia.form({
        password: "",
      }),
      branches: this.branchesProps,
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("operator.update", this.operator), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    submitUpdatePassword() {
      this.formUpdatePassword.post(
        this.route("users.update_password", this.operator.userId),
        {
          preserveScroll: true,
          onError: () => this.form.reset(),
        }
      );
    },

    getBranches() {
      let city = this.form.city;
      console.log("city:" + city);

      axios.get("/dashboard/branches/" + city).then((response) => {
        //  console.log(response);
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

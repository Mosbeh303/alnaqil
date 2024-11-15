<template>
  <div>
    <Head :title="language.add_new_client" />


    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.add_new_client }}
        </div>
        <div>
          <BackButton/>
        </div>
      </div>
    </page-header>

    <flash-messages class="m-7" />

    <div class="grid gap-7 md:grid-cols-2 mt-7 px-7 mb-7">
      <Section :title="language.data">
        <form @submit.prevent="submit" class="mt-4">

          <Label class="mb-2 mt-4" for="city" :value="language.type" />
          <SelectInput
            id="type"
            v-model="form.type"
            class="mt-2 block w-full"
            :selectPlaceholder="language.type"
            required
          >
            <option value="shop">
              {{ language.shop }}
            </option>
            <option value="platform">
              {{ language.platform }}
            </option>
            <option value="individual">
              {{ language.individual }}
            </option>
          </SelectInput>
          <InputError :message="form.errors.city" />


          <Label v-if="form.type != 'individual'" class="mt-4" for="name" :value="language.store_name_organization" />
          <Label v-else class="mt-4" for="name" :value="language.full_name" />
          <Input
            id="name"
            type="text"
            v-model="form.name"
            :error="form.errors.name"
            class="mt-2 block w-full"
            autocomplete="name"
          />
          <InputError :message="form.errors.name" />

          <div v-if="form.type == 'individual'">
            <Label class="mt-4" for="id_number" :value="language.id_number" />
            <Input
                id="id_number"
                type="text"
                v-model="form.id_number"
                :error="form.errors.id_number"
                class="mt-2 block w-full"
            />
            <InputError :message="form.errors.id_number" />
          </div>

          <Label class="mt-4" for="city" :value="language.city" />
          <SelectInput
            id="city"
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
          <InputError :message="form.errors.city" />

          <Label class="mt-4" for="neighborhood" :value="language.Neighborhood" />
          <Input
            id="neighborhood"
            type="text"
            v-model="form.neighborhood"
            :error="form.errors.neighborhood"
            class="mt-2 block w-full"
            autocomplete="neighborhood"
          />
          <InputError :message="form.errors.neighborhood" />

          <div v-if="form.type != 'individual'">
            <Label class="mt-4" for="ownerName" :value="language.Store_owner_name" />
            <Input
                id="ownerName"
                type="text"
                v-model="form.ownerName"
                :error="form.errors.ownerName"
                class="mt-2 block w-full"
                autocomplete="ownerName"
            />
            <InputError :message="form.errors.ownerName" />
          </div>


          <Label class="mt-4" for="phone" :value="language.mobile_number_" />
          <Input
            id="phone"
            type="text"
            v-model="form.phone"
            :error="form.errors.phone"
            class="mt-2 block w-full"
          />
          <InputError :message="form.errors.phone" />

          <Label class="mt-4" for="email" :value="language.email" />
          <Input
            id="email"
            type="text"
            v-model="form.email"
            :error="form.errors.email"
            class="mt-2 block w-full"
          />
          <InputError :message="form.errors.email" />

          <Label class="mt-4" for="username" :value="language.username" />
          <Input
            id="username"
            type="text"
            v-model="form.username"
            :error="form.errors.username"
            class="mt-2 block w-full"
          />
          <InputError :message="form.errors.username" />

          <Label class="mt-4" for="username" :value="language.password" />
          <Input
            id="password"
            type="password"
            v-model="form.password"
            :error="form.errors.password"
            class="mt-2 block w-full"
          />
          <InputError :message="form.errors.password" />



          <div v-if="form.type != 'individual'">
            <Label  class="mt-4" for="govNumber" :value="language.Commercial_Register" />
            <Input
                id="govNumber"
                type="text"
                v-model="form.govNumber"
                :error="form.errors.govNumber"
                class="mt-2 block w-full"
                autocomplete="govNumber"
            />
            <InputError :message="form.errors.govNumber" />

            <Label class="mt-4" for="taxNumber" :value="language.tax_number" />
            <Input
                id="taxNumber"
                type="text"
                v-model="form.taxNumber"
                :error="form.errors.taxNumber"
                class="mt-2 block w-full"
                autocomplete="taxNumber"
            />
            <InputError :message="form.errors.taxNumber" />

            <Label
                class="mt-4"
                for="freelanceDocument"
                :value="language.freelance_document"
            />
            <Input
                id="freelanceDocument"
                type="text"
                v-model="form.freelanceDocument"
                :error="form.errors.freelanceDocument"
                class="mt-2 block w-full"
                autocomplete="freelanceDocument"
            />
            <InputError :message="form.errors.freelanceDocument" />

            <Label class="mt-4" for="maaroof" :value="language.maaroof" />
            <Input
                id="maaroof"
                type="text"
                v-model="form.maaroof"
                :error="form.errors.maaroof"
                class="mt-2 block w-full"
                autocomplete="maaroof"
            />
            <InputError :message="form.errors.maaroof" />

            <Label class="mt-4" for="website" :value="language.website" />
            <Input
                id="website"
                type="text"
                v-model="form.website"
                :error="form.errors.website"
                class="mt-2 block w-full"
                autocomplete="phone"
            />
            <InputError :message="form.errors.website" />
          </div>






          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ language.add }}</Button
          >
        </form>
      </Section>

      <div class="flex flex-col gap-4 md:gap-7">







      </div>
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
import Label from "@/Components/Label.vue";
import Icon from "@/Components/Icon.vue";
import BackButton from "../../Components/backButton.vue"
import Modal from "../../Components/Modal.vue"
import Checkbox from "@/Components/Checkbox.vue";

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
    Label,
    Icon,
    BackButton,
    Modal,
    Checkbox
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    cities: Object,
    language: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        type: "shop",
        status: "",
        govNumber: "",
        taxNumber: "",
        maaroof:"",
        freelanceDocument: "",
        name: "",
        username: "",
        ownerName: "",
        phone: "",
        email: "",
        city: "",
        neighborhood: "",
        password: "",
        website: "",
        id_number: "",
        fromDashboard: true,
      }),


    };
  },

  methods: {
    submit() {
        this.form.post(this.route("stores.store"), {
          preserveScroll: true,
          onSuccess: () => this.form.reset(),
          onFinish: () => this.form.reset(""),
        });
    },


  },
};
</script>

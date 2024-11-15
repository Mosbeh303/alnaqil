<template>
  <div>
    <Head :title="language.account_settings" />

    <page-header> {{ language.account_settings }}</page-header>

    <flash-messages class="m-7" />

    <div class="grid gap-4 lg:gap-7 md:grid-cols-2 m-4 lg:m-7">
      <div class="flex flex-col md:gap-7 gap-4">
        <Section
          :title="language.modify_data"
          class="h-fit"
          v-if="
            $page.props.auth.account === 'admin' || $page.props.auth.account === 'client'
          "
        >
          <form @submit.prevent="submit" class="mt-4">
            <Label :value="language.the_name" />
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

            <Label :value="language.phone_number" class="mt-4" />
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

            <Label :value="language.username" class="mt-4" />
            <Input
              id="username"
              type="text"
              v-model="form.username"
              class="mt-2 block w-full"
              :error="form.errors.username"
              :placeholder="language.username"
              autocomplete="username"
            />
            <InputError :message="form.errors.username" />

            <Button
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="mt-4 px-6"
            >
              {{ language.modify }}</Button
            >
          </form>
        </Section>

        <Section :title="language.address" v-if="store">
          <form @submit.prevent="submitFormStore" class="mt-4">
            <Label class="mt-4" for="name" :value="language.store_name_organization" />
            <Input
              id="name"
              type="text"
              v-model="formStore.name"
              :error="formStore.errors.name"
              class="mt-2 block w-full disabled"
              autocomplete="name"
              disabled
            />
            <InputError :message="formStore.errors.name" />

            <Label class="mt-4" for="city" :value="language.city" />
            <SelectInput
              id="city"
              v-model="formStore.city"
              class="mt-2 block w-full disabled"
              :selectPlaceholder="language.city"
              required
              disabled
            >
              <option v-for="(city, i) in cities" :key="i" :value="city.name">
                {{ city.name }}
              </option>
              <option v-if="cities.length === 0" :value="language.alriyadh">
                {{ language.alriyadh }}
              </option>
            </SelectInput>
            <InputError :message="formStore.errors.city" />

            <Label class="mt-4" for="neighborhood" :value="language.Neighborhood" />
            <Input
              id="neighborhood"
              type="text"
              v-model="formStore.neighborhood"
              :error="formStore.errors.neighborhood"
              class="mt-2 block w-full disabled"
              autocomplete="neighborhood"
              disabled
            />
            <InputError :message="formStore.errors.neighborhood" />






          </form>
        </Section>
      </div>

      <div class="flex flex-col md:gap-7 gap-4">
        <Section :title="language.edit_photo" class="h-fit">
          <form @submit.prevent="updateAvatar" class="mt-4">
            <div>
              <img
                v-if="user.avatar"
                :src="`/uploads/avatars/${user.avatar}`"
                alt="avatar"
                class="w-48 h-48 rounded-full block"
              />
              <img
                v-else
                src="/images/avatar.png"
                alt="avatar"
                class="w-48 h-48 rounded-full block"
              />
            </div>
            <Label :value="language.upload_a_new_photo" class="mt-4" />
            <Input
              id="file"
              type="file"
              accept="image/png, image/gif, image/jpeg, image/jpg"
              @input="form.avatar = $event.target.files[0]"
              :error="form.errors.avatar"
              class="mt-2 block w-full"
              :placeholder="language.file"
            />
            <InputError :message="form.errors.avatar" />

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
        <Section :title="language.modify_the_username" class="h-fit">
          <form @submit.prevent="submitUpdateUsername" class="mt-4">
            <Input
              id="username"
              type="text"
              v-model="formUpdateUsername.username"
              :error="formUpdateUsername.errors.username"
              class="mt-4 block w-full"
              required
              :placeholder="language.username"
              autocomplete="name"
            />
            <InputError :message="formUpdateUsername.errors.username" />

            <Button
              :class="{ 'opacity-25': formUpdateUsername.processing }"
              :disabled="formUpdateUsername.processing"
              class="mt-4 px-6"
            >
              {{ language.modify }}</Button
            >
          </form>
        </Section>
        <pickup-address
          :addresses="addresses"
          :store="store"
          v-if="store"
        ></pickup-address>
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
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import AvatarInput from "@/Components/AvatarInput.vue";
import Icon from "@/Components/Icon.vue";
import SelectInput from "@/Components/SelectInput.vue";
import PickupAddress from "../Stores/Partials/PickupAddress.vue";

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Input,
    Section,
    BreezeValidationErrors,
    FlashMessages,
    Label,
    InputError,
    AvatarInput,
    Icon,
    SelectInput,
    PickupAddress,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    user: Object,
    language: Object,
    store: Object,
    cities: Object,
    addresses: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        name: this.user.name,
        email: this.user.email,
        phone: this.user.phone,
        username: this.user.username,
        avatar: null,
      }),
      formStore: this.store
        ? this.$inertia.form({
            id: this.store.id,
            status: this.store.status,
            govNumber: this.store.govNumber,
            taxNumber: this.store.taxNumber,
            maaroof: this.store.maaroof,
            freelanceDocument: this.store.freelanceDocument,
            name: this.store.name,
            username: this.store.username,
            ownerName: this.store.ownerName,
            phone: this.store.phone,
            email: this.store.email,
            city: this.store.city,
            neighborhood: this.store.neighborhood,
            refrigeratedFee: this.store.refrigeratedFee,
            baseFee: this.store.baseFee,
            baseFee2: this.store.baseFee2,
            baseFee3: this.store.baseFee3,
            returnedFee: this.store.returnedFee,
            refrigeratedFee: this.store.refrigeratedFee,
            exchangeFee: this.store.exchangeFee,
            returnFee: this.store.returnFee,
            codFee: this.store.codFee,
            fixedAmountCodFee: this.store.fixedAmountCodFee,
            codFeePercent: this.store.codFeePercent,
            website: this.store.website,
            bank: this.store.bank,
            accountOwner: this.store.accountOwner,
            rib: this.store.rib,
          })
        : {},
      formUpdatePassword: this.$inertia.form({
        password: "",
      }),
      formUpdateUsername: this.$inertia.form({
        username: this.user.username,
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("users.update", this.user), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    submitUpdatePassword() {
      this.formUpdatePassword.post(this.route("users.update_password", this.user), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    submitUpdateUsername() {
      this.formUpdateUsername.post(this.route("users.update_username", this.user), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    updateAvatar() {
      this.form.post(this.route("users.update_avatar", this.user), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    submitFormStore() {
      this.formStore.post(this.route("stores.update", this.store), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
  },
};
</script>

<style scoped>
.disabled {
  background-color: #eee;
}
</style>

<template>
  <div>
    <Head :title="language.modify_the_client_account" />


    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.modify_the_client_account }} ({{ store.name }})
          <Badge v-if="store.type == 'platform'" color="bg-purple-600" class="inline-block ">{{ language.platform }}</Badge>
          <Badge v-else-if="store.type == 'individual'" color="bg-purple-600" class="inline-block ">{{ language.individual }}</Badge>
          <Badge v-else color="bg-purple-600" class="inline-block ">{{ language.store }}</Badge>
        </div>
        <div>
          <BackButton/>
        </div>
      </div>
    </page-header>

    <flash-messages class="m-7" />

    <div class="grid gap-7 md:grid-cols-2 mt-7 px-7 mb-7">
      <Section :title="language.modify_data">
        <form @submit.prevent="submit" class="mt-4">
          <Label class="mt-4" for="name" :value="language.store_name_organization" />
          <Input
            id="name"
            type="text"
            v-model="form.name"
            :error="form.errors.name"
            class="block w-full mt-2"
            autocomplete="name"
          />
          <InputError :message="form.errors.name" />

          <Label class="mt-4" for="id_number" :value="language.id_number" />
          <Input
            id="id_number"
            type="text"
            v-model="form.id_number"
            :error="form.errors.id_number"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.id_number" />

          <Label class="mt-4" for="city" :value="language.city" />
          <SelectInput
            id="city"
            v-model="form.city"
            class="block w-full mt-2"
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
            class="block w-full mt-2"
            autocomplete="neighborhood"
          />
          <InputError :message="form.errors.neighborhood" />

          <Label class="mt-4" for="ownerName" :value="language.Store_owner_name" />
          <Input
            id="ownerName"
            type="text"
            v-model="form.ownerName"
            :error="form.errors.ownerName"
            class="block w-full mt-2"
            autocomplete="ownerName"
          />
          <InputError :message="form.errors.ownerName" />

          <Label class="mt-4" for="email" :value="language.email" />
          <Input
            id="email"
            type="text"
            v-model="form.email"
            :error="form.errors.email"
            class="block w-full mt-2"
            autocomplete="email"
          />
          <InputError :message="form.errors.email" />

          <Label class="mt-4" for="username" :value="language.username" />
          <Input
            id="username"
            type="text"
            v-model="form.username"
            :error="form.errors.username"
            class="block w-full mt-2"
            autocomplete="username"
          />
          <InputError :message="form.errors.username" />

          <Label class="mt-4" for="baseFee" :value="language.delivery_price" />
          <Input
            id="baseFee"
            type="number"
            step="0.01"
            v-model="form.baseFee"
            :error="form.errors.baseFee"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.baseFee" />

          <Label class="mt-4" for="baseFee" :value="language.delivery_price + ' 2'" />
          <Input
            id="baseFee2"
            type="number"
            step="0.01"
            v-model="form.baseFee2"
            :error="form.errors.baseFee2"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.baseFee2" />

          <Label class="mt-4" for="baseFee" :value="language.delivery_price + ' 3'" />
          <Input
            id="baseFee3"
            type="number"
            step="0.01"
            v-model="form.baseFee3"
            :error="form.errors.baseFee3"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.baseFee3" />

          <Label class="mt-4" for="baseFee" :value="language.base_weight" />
          <Input
            id="baseWeight"
            type="number"
            step="0.01"
            v-model="form.baseWeight"
            :error="form.errors.baseWeight"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.baseWeight" />

          <Label class="mt-4" for="baseFee" :value="language.additional_fee_per_kg" />
          <Input
            id="addFeePerKg"
            type="number"
            step="0.01"
            v-model="form.addFeePerKg"
            :error="form.errors.addFeePerKg"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.addFeePerKg" />

          <Label
            class="mt-4"
            for="returnFee"
            :value="language.Return_price_after_3_attempts"
          />
          <Input
            id="returnedFee"
            type="number"
            step="0.01"
            v-model="form.returnedFee"
            :error="form.errors.returnedFee"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.returnedFee" />

          <Label class="mt-4" for="exchangeFee" :value="language.exchange_fee" />
          <Input
            id="exchangeFee"
            type="number"
            step="0.01"
            v-model="form.exchangeFee"
            :error="form.errors.exchangeFee"
            class="block w-full mt-2"
            autocomplete="exchangeFee"
          />
          <InputError :message="form.errors.exchangeFee" />

          <Label class="mt-4" for="refrigeratedFee" :value="language.refrigerated_fee" />
          <Input
            id="refrigeratedFee"
            type="number"
            step="0.01"
            v-model="form.refrigeratedFee"
            :error="form.errors.refrigeratedFee"
            class="block w-full mt-2"
            autocomplete="refrigeratedFee"
          />
          <InputError :message="form.errors.refrigeratedFee" />

          <Label class="mt-4" for="returnFee" :value="language.return_fee" />
          <Input
            id="returnFee"
            type="number"
            step="0.01"
            v-model="form.returnFee"
            :error="form.errors.returnFee"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.returnFee" />

          <Label
            class="mt-4"
            for="fixedAmountCodFee"
            :value="language.Fixed_amount_of_Cash_on_Delivery_commission"
          />
          <Input
            id="fixedAmountCodFee"
            type="number"
            step="0.01"
            v-model="form.fixedAmountCodFee"
            :error="form.errors.fixedAmountCodFee"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.fixedAmountCodFee" />

          <Label class="mt-4" for="codFee" :value="language.Payment_fee_upon_receipt" />
          <Input
            id="codFee"
            type="number"
            step="0.01"
            v-model="form.codFee"
            :error="form.errors.codFee"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.codFee" />

          <Label
            class="mt-4"
            for="codFeePercent"
            :value="language.cod_fees_pourcentage"
          />
          <Input
            id="codFeePercent"
            type="number"
            step="0.01"
            v-model="form.codFeePercent"
            :error="form.errors.codFeePercent"
            class="block w-full mt-2"
          />
          <InputError :message="form.errors.codFeePercent" />

          <Label class="mt-4" for="govNumber" :value="language.Commercial_Register" />
          <Input
            id="govNumber"
            type="text"
            v-model="form.govNumber"
            :error="form.errors.govNumber"
            class="block w-full mt-2"
            autocomplete="govNumber"
          />
          <InputError :message="form.errors.govNumber" />

          <Label class="mt-4" for="taxNumber" :value="language.tax_number" />
          <Input
            id="taxNumber"
            type="text"
            v-model="form.taxNumber"
            :error="form.errors.taxNumber"
            class="block w-full mt-2"
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
            class="block w-full mt-2"
            autocomplete="freelanceDocument"
          />
          <InputError :message="form.errors.freelanceDocument" />

          <Label class="mt-4" for="maaroof" :value="language.maaroof" />
          <Input
            id="maaroof"
            type="text"
            v-model="form.maaroof"
            :error="form.errors.maaroof"
            class="block w-full mt-2"
            autocomplete="maaroof"
          />
          <InputError :message="form.errors.maaroof" />

          <Label class="mt-4" for="phone" :value="language.mobile_number_" />
          <Input
            id="phone"
            type="text"
            v-model="form.phone"
            :error="form.errors.phone"
            class="block w-full mt-2"
            autocomplete="phone"
          />
          <InputError :message="form.errors.phone" />

          <Label class="mt-4" for="website" :value="language.website" />
          <Input
            id="website"
            type="text"
            v-model="form.website"
            :error="form.errors.website"
            class="block w-full mt-2"
            autocomplete="phone"
          />
          <InputError :message="form.errors.website" />

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="px-6 mt-4"
          >
            {{ language.modify }}</Button
          >
        </form>
      </Section>

      <div class="flex flex-col gap-4 md:gap-7">
        <Section :title="language.bank_account" class="h-fit">
          <form @submit.prevent="updateBankAccount" class="mt-4">
            <Input
              id="bank"
              type="text"
              v-model="form.bank"
              :error="form.errors.bank"
              class="block w-full mt-4"
              :placeholder="language.bank_name"
              autocomplete="bank"
            />
            <InputError :message="form.errors.bank" />

            <Input
              id="accountOwner"
              type="text"
              v-model="form.accountOwner"
              :error="form.errors.accountOwner"
              class="block w-full mt-4"
              :placeholder="language.the_name_of_the_bank_account_holder"
              autocomplete="bank"
            />
            <InputError :message="form.errors.accountOwner" />

            <Input
              id="rib"
              type="text"
              v-model="form.rib"
              :error="form.errors.rib"
              class="block w-full mt-4"
              required
              :placeholder="language.bank_account_number"
              autocomplete="bank"
            />
            <InputError :message="form.errors.rib" />

            <Button
              :class="{ 'opacity-25': formUpdatePassword.processing }"
              :disabled="formUpdatePassword.processing"
              class="px-6 mt-4"
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
              class="block w-full mt-4"
              required
              :placeholder="language.mot_de_passe"
              autocomplete="name"
            />
            <InputError :message="formUpdatePassword.errors.password" />

            <Button
              :class="{ 'opacity-25': formUpdatePassword.processing }"
              :disabled="formUpdatePassword.processing"
              class="px-6 mt-4"
            >
              {{ language.modify }}</Button
            >
          </form>
        </Section>

        <Section :title="language.files" class="h-fit">
          <form @submit.prevent="submitFile" class="mt-4">
            <Input
              id="title"
              type="text"
              v-model="formFile.title"
              :error="formFile.errors.title"
              class="block w-full mt-4"
              required
              :placeholder="language.file_url"
              autocomplete="title"
            />
            <InputError :message="formFile.errors.title" />

            <Input
              id="file"
              type="file"
              @input="formFile.file = $event.target.files[0]"
              :error="formFile.errors.file"
              class="block w-full mt-4"
              :placeholder="language.file"
            />
            <InputError :message="formFile.errors.file" />

            <Button
              :class="{ 'opacity-25': formFile.processing }"
              :disabled="formFile.processing"
              class="px-6 mt-4"
            >
              {{ language.add_a_file }}
            </Button>
          </form>

          <div class="flex flex-col gap-2 p-4 mt-4 border-t border-gray-100">
            <div v-for="file in files" :key="file.id" class="flex gap-3">
              <Icon
                @click="removeFile(file)"
                name="x"
                class="w-5 h-5 text-red-700 cursor-pointer"
              />
              <a
                target="_blank"
                :href="`/uploads/stores/${store.id}/files/${file.name}`"
                class="text-blue-600 before:content-['›']"
              >
                <span v-if="file.title">{{ " " + file.title }} </span>
                <span v-else> {{ language.no_address }} </span>
              </a>
            </div>
          </div>
        </Section>

        <pickup-address :addresses="addresses" :store="store"></pickup-address>


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
import PickupAddress from "./Partials/PickupAddress.vue";
import Badge from "@/Components/Badge.vue";

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
    Checkbox,
    PickupAddress,
    Badge
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    store: Object,
    files: Object,
    cities: Object,
    language: Object,
    previous_url : String,
    addresses: Object
  },
  data() {
    return {
      form: this.$inertia.form({
        id: this.store.id,
        status: this.store.status,
        id_number: this.store.id_number,
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
        baseWeight: this.store.baseWeight,
        addFeePerKg: this.store.addFeePerKg,
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
      }),
      formUpdatePassword: this.$inertia.form({
        password: "",
      }),
      formFile: this.$inertia.form({
        title: "",
        file: "",
      }),

    };
  },

  methods: {
    submit() {
      this.form.post(this.route("stores.update", this.store), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },

    updateBankAccount() {
      this.form.post(this.route("stores.update_bank_account", this.store), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    submitUpdatePassword() {
      this.formUpdatePassword.post(
        this.route("users.update_password", this.store.userId),
        {
          preserveScroll: true,
          onError: () => this.formUpdatePassword.reset(),
        }
      );
    },
    submitFile() {
      this.formFile.post(this.route("stores.files.store", this.store), {
        preserveScroll: true,
        onError: () => this.formFile.reset(),
      });
    },
    removeFile(file) {
      let proceed = confirm(this.language.deleting_file_confirmtion);
      if (proceed) {
        this.$inertia.post(
          this.route("stores.files.remove", file),
          {},
          {
            preserveScroll: true,
          }
        );
      }
    },


  },
};
</script>

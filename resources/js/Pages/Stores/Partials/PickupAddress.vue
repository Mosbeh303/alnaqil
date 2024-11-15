<template>
    <div>
        <Section :title="$page.props.language.pickup_addresses" class="h-fit">

        <div>
            <div v-for="address in addresses" :key="address.id" class="rounded-lg bg-gray-100 text-gray-600  my-2 p-4 flex justify-between">
                <div>
                    <div class="flex items-baseline mb-2">
                        <p class="font-semibold mb-2">
                            {{ address.name }}
                        </p>
                        <p
                            v-if="address.default == 1"
                            class="mr-4 ml-4 rounded-full text-sm py-1 bg-purple-500 px-4 text-white"
                        >
                            {{$page.props.language.default}}
                        </p>
                    </div>
                    <p>{{$page.props.language.city}} : {{ address.city }}</p>
                    <p>{{$page.props.language.neighborhood}}  {{ address.neighborhood }}</p>
                    <p v-if="address.phone">{{$page.props.language.phone_number}} :  {{ address.phone }}</p>
                    <p>{{$page.props.language.national_adress}} : {{ address.national_address }}</p>
                    <p>{{$page.props.language.full_address}} : {{ address.full_address }}</p>
                </div>
                <div>
                <button class="cursor-pointer block mb-3" @click="editAddress(address)">
                    <icon name="edit" class="block w-5 h-5 text-gray-700" />
                </button>

                <button class="cursor-pointer" @click="deleteAddress(address)">
                    <icon name="trash" class="block w-5 h-5 text-gray-700" />
                </button>
                </div>
            </div>
        </div>

        <div class="mt-4 border-t border-gray-100 p-4 flex flex-col gap-2">
            <p class="text-blue-600 font-semibold cursor-pointer" @click="showAddAddressModal = ! showAddAddressModal"> + {{ $page.props.language.add_address }}</p>
        </div>
        </Section>

        <Modal :show="showAddAddressModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ $page.props.language.add_address }}
      </h2>

      <form @submit.prevent="submitAddress" class="mt-4">

        <Label :value="$page.props.language.name" class="mt-4 text-right" />
        <Input
          id="name"
          type="text"
          v-model="formAddress.name"
          :error="formAddress.errors.name"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.the_name"
          autocomplete="name"
        />
        <InputError :message="formAddress.errors.name" />

        <Label :value="$page.props.language.city" class="mt-4 text-right" />
        <Input
          id="city"
          type="text"
          v-model="formAddress.city"
          :error="formAddress.errors.city"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.city"
          autocomplete="name"
        />
        <InputError :message="formAddress.errors.city" />

        <Label :value="$page.props.language.neighborhood" class="mt-4 text-right" />
        <Input
          id="neighborhood"
          type="text"
          v-model="formAddress.neighborhood"
          :error="formAddress.errors.neighborhood"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.neighborhood"
          autocomplete="name"
        />
        <InputError :message="formAddress.errors.neighborhood" />

        <Label :value="$page.props.language.phone_number + ' (' + $page.props.language.optional + ')'" class="mt-4 text-right" />
        <Input
          type="text"
          v-model="formAddress.phone"
          :error="formAddress.errors.phone"
          class="mt-2 block w-full"
          :placeholder="$page.props.language.phone"
        />
        <InputError :message="formAddress.errors.phone" />

        <Label :value="$page.props.language.national_adress" class="mt-4 text-right" />
        <Input
          id="nationalAddress"
          type="text"
          v-model="formAddress.nationalAddress"
          :error="formAddress.errors.nationalAddress"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.national_adress"
        />
        <InputError :message="formAddress.errors.nationalAddress" />

        <Label :value="$page.props.language.full_address" class="mt-4 text-right" />
        <Input
          id="address"
          type="text"
          v-model="formAddress.fullAddress"
          :error="formAddress.errors.fullAddress"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.full_address"
        />
        <InputError :message="formAddress.errors.fullAddress" />

        <label class="flex items-start mt-4">
          <Checkbox name="active" v-model:checked="formAddress.default" />
          <span class="mr-2 text-sm text-gray-600"> {{ $page.props.language.default }}</span>
        </label>

        <progress v-if="formAddress.progress" :value="formAddress.progress.percentage" max="100">
          {{ formAddress.progress.percentage }}%
        </progress>

        <Button
          :class="{ 'opacity-25': formAddress.processing }"
          :disabled="formAddress.processing"
          class="mt-4 px-6"
        >
          {{ $page.props.language.add }}
        </Button>
        <Button
          @click="showAddAddressModal = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ $page.props.language.cancel }}</Button
        >
      </form>
    </Modal>

    <Modal :show="showDeleteAddressAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ $page.props.language.are_you_sure }}
      </h2>
      <p class="text-base text-gray-600">
        {{ $page.props.language.do_you_want_to_confirm_deletion_of_address }}
      </p>

      <Button
        class="mt-4 !border"
        :href="deleteAddressLink"
        method="post"
        @click="showDeleteAddressAlert = false"
        preserveScroll="true"
      >
        <span>{{ $page.props.language.delete }}</span>
      </Button>
      <Button
        @click="showDeleteAddressAlert = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ $page.props.language.cancel }}</Button
      >
    </Modal>

    <Modal :show="showEditAddressModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ $page.props.language.edit_address }}
      </h2>

      <form @submit.prevent="submitEditAddressForm" class="w-full">

        <Label :value="$page.props.language.name" class="mt-4 text-right" />
        <Input
          id="name"
          type="text"
          v-model="formEditAddress.name"
          :error="formEditAddress.errors.name"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.the_name"
          autocomplete="name"
        />
        <InputError :message="formEditAddress.errors.name" />

        <Label :value="$page.props.language.city" class="mt-4 text-right" />
        <Input
          id="city"
          type="text"
          v-model="formEditAddress.city"
          :error="formEditAddress.errors.city"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.city"
          autocomplete="name"
        />
        <InputError :message="formEditAddress.errors.city" />

        <Label :value="$page.props.language.neighborhood" class="mt-4 text-right" />
        <Input
          id="neighborhood"
          type="text"
          v-model="formEditAddress.neighborhood"
          :error="formEditAddress.errors.neighborhood"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.neighborhood"
          autocomplete="name"
        />
        <InputError :message="formEditAddress.errors.neighborhood" />

        <Label :value="$page.props.language.phone_number + ' (' + $page.props.language.optional + ')'" class="mt-4 text-right" />
        <Input
          type="text"
          v-model="formEditAddress.phone"
          :error="formEditAddress.errors.phone"
          class="mt-2 block w-full"
          :placeholder="$page.props.language.phone"
        />
        <InputError :message="formEditAddress.errors.phone" />

        <Label :value="$page.props.language.national_adress" class="mt-4 text-right" />
        <Input
          id="nationalAddress"
          type="text"
          v-model="formEditAddress.nationalAddress"
          :error="formEditAddress.errors.nationalAddress"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.national_adress"
        />
        <InputError :message="formEditAddress.errors.nationalAddress" />

        <Label :value="$page.props.language.full_address" class="mt-4 text-right" />
        <Input
          type="text"
          v-model="formEditAddress.fullAddress"
          :error="formEditAddress.errors.fullAddress"
          class="mt-2 block w-full"
          required
          :placeholder="$page.props.language.full_address"
        />
        <InputError :message="formEditAddress.errors.fullAddress" />

        <label class="flex items-start mt-4">
          <Checkbox name="active" v-model:checked="formEditAddress.default" />
          <span class="mr-2 text-sm text-gray-600"> {{ $page.props.language.default }}</span>
        </label>


        <Button class="mt-4 !border"> {{ $page.props.language.modify }} </Button>
        <Button
          @click="showEditAddressModal = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ $page.props.language.cancel }}</Button
        >
      </form>
    </Modal>
    </div>
</template>

<script>
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import Section from "@/Components/Section.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue"
import Checkbox from "@/Components/Checkbox.vue";

export default {
  components: {
    Button,
    Input,
    Section,
    BreezeValidationErrors,
    SelectInput,
    InputError,
    Label,
    Icon,
    Modal,
    Checkbox
  },

  props: {
    store: Object,
    addresses: Object
  },

  data(){
    return {
        formAddress: this.$inertia.form({
        name: "",
        city: "",
        neighborhood: "",
        nationalAddress: "",
        fullAddress: "",
        default: 0,
        phone: ""
      }),
      formEditAddress: this.$inertia.form({
        name: "",
        city: "",
        neighborhood: "",
        nationalAddress: "",
        fullAddress: "",
        default: 0,
        phone: ""
      }),
      showAddAddressModal: false,
      showEditAddressModal: false,
      showDeleteAddressAlert: false,
      deleteAddressLink: "",
      targetAddress: {},
    }
  },

  methods: {
    submitAddress() {
      this.formAddress.post(this.route("stores.addresses.create", this.store), {
        preserveScroll: true,
        onError: () => this.formAddress.reset(),

      });
      this.showAddAddressModal = false
    },
    submitEditAddressForm() {
      this.formEditAddress.post(this.route("stores.addresses.update", this.targetAddress), {
        preserveScroll: true,
        onError: () => this.formEditAddress.reset(),

      });
      this.showEditAddressModal = false
    },
    deleteAddress(address) {
      this.deleteAddressLink = route("stores.addresses.delete", address);
      this.showDeleteAddressAlert = true;
    },

    editAddress(address){
        this.targetAddress = address ;
        this.formEditAddress.city = address.city;
        this.formEditAddress.name = address.name;
        this.formEditAddress.neighborhood = address.neighborhood;
        this.formEditAddress.phone = address.phone;
        this.formEditAddress.nationalAddress = address.national_address;
        this.formEditAddress.fullAddress = address.full_address;
        this.formEditAddress.default = address.default == 1 ? true : false;
        this.showEditAddressModal = true
    }
  }
}
</script>

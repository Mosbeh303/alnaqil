<template>
  <div>
    <Head :title="language.settings" />

    <page-header> {{ language.settings }}</page-header>

    <Layout>
      <Section :title="language.shipping_cities" >
        <div class="my-6">
            <form :action="route('settings.cities.export')" method="POST" id="form" >
                <input type="hidden" name="_token" :value="$page.props.csrf" />
                <Button class="bg-transparent !text-gray-700 !border-gray-700 border hover:!text-gray-100" >
                    <Icon class="w-5 h-5 me-1 relative" name="excel" />
                    إستخراج اكسل
                </Button>
            </form>
        </div>

        <form @submit.prevent="submitFormDistrict()" class="mb-7">
          <Label :value="language.add_an_area" class="mt-4" />
          <Input
            id="city"
            type="text"
            v-model="formDistrict.value"
            :error="formDistrict.errors.value"
            class="mt-2 block w-full"
            required
            :placeholder="language.area_name"
            autocomplete="District"
          />
          <InputError :message="formDistrict.errors.value" />
          <Button
            :class="{ 'opacity-25': formDistrict.processing }"
            :disabled="formDistrict.processing"
            class="mt-4 px-6"
          >
            {{ language.add }}
          </Button>
        </form>

        <div
          class="mb-4 border rounded border-gray-100"
          v-for="(district, i) in shippingDistricts"
          :key="i"
        >
          <div>
            <div class="flex justify-between align-items-center bg-gray-100 px-4 py-3">
              <div class="flex gap-4">
                <h3 class="text-sm bg-gray-100">
                  {{ district.name }}
                </h3>

                <p
                  class="text-red-700 text-xs text-underline cursor-pointer"
                  @click="openDeleteModal(district.id)"
                >
                  x {{ language.delete }}
                </p>
              </div>



              <div class="flex gap-4 items-center">
                <form :action="route('settings.cities.export', district.id)" method="POST" id="form" >
                    <input type="hidden" name="_token" :value="$page.props.csrf" />
                    <button type="submit" class="bg-transparent !text-gray-700 text-xs flex items-center font-semibold" >
                        <Icon class="w-5 h-5 me-1 relative" name="excel" />
                        إستخراج اكسل
                    </button>
                </form>
                <p
                    class="text-blue-600 font-semibold cursor-pointer text-xs"
                    @click="openModal(district.id)"
                >
                    {{ language.add_a_city }}
                </p>
              </div>
            </div>

            <div class="p-4">
              <ul>
                <li
                  v-for="(city, i) in district.cities"
                  :key="i"
                  class="flex gap-2 text-gray-600 mb-2"
                >
                  <Icon
                    @click="removeCity(city)"
                    name="x"
                    class="w-5 h-5 cursor-pointer text-red-700"
                  />
                  <span>{{ city.name }} ({{ city.name_en }})</span>

                  <p
                    class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                    v-if="city.active == 1"
                  >
                    {{ language.enabled }}
                  </p>

                  <Link :href="route('cities.areas', city)"
                    ><Icon name="eye" class="w-5 h-5 cursor-pointer text-blue-700"
                  /></Link>
                </li>

                <li
                  v-if="district.cities.length === 0"
                  class="text-red-500 text-sm flex gap-2"
                >
                  <Icon name="warning" class="w-4 h-4" />
                  {{ language.there_are_cities_yet }}
                </li>
              </ul>
            </div>
          </div>
          <p
            v-if="shippingDistricts === null || shippingDistricts.length === 0"
            class="text-red-500 text-sm flex gap-2"
          >
            <Icon name="warning" class="w-4 h-4" /> {{ language.add_a_region_first }}
          </p>
        </div>
      </Section>

      <Section :title="language.user_cities" class="mt-4 md:mt-7">
        <form @submit.prevent="submitRegisterCities()">
          <Label :value="language.add_a_city" class="mt-4" />
          <Input
            id="city"
            type="text"
            v-model="formRegisterCities.value"
            :error="formRegisterCities.errors.value"
            class="mt-2 block w-full"
            required
            :placeholder="language.city"
            autocomplete="city"
          />
          <InputError :message="formRegisterCities.errors.value" />
          <Button
            :class="{ 'opacity-25': formRegisterCities.processing }"
            :disabled="formRegisterCities.processing"
            class="mt-4 px-6"
          >
            {{ language.add }}
          </Button>
        </form>

        <div class="mt-7 border rounded border-gray-100">
          <h3 class="text-md px-4 py-3 bg-gray-100">{{ language.cities_list }}</h3>
          <div class="p-4">
            <ul>
              <li
                v-for="(city, i) in registerCities"
                :key="i"
                class="flex gap-2 text-gray-600 mb-2"
              >
                <Icon
                  @click="removeCity(city)"
                  name="x"
                  class="w-5 h-5 cursor-pointer text-red-700"
                />
                {{ city.name }}

                <Link :href="route('settings.cities.show', city)"
                  ><Icon name="eye" class="w-5 h-5 cursor-pointer text-blue-700"
                /></Link>
              </li>
              <li
                v-if="registerCities === null || registerCities.length === 0"
                class="text-red-500 text-sm flex gap-2"
              >
                <Icon name="warning" class="w-4 h-4" />
                {{ language.There_are_no_cities_Still_possible }}
                {{ language.Registration_in_Riyadh }}
              </li>
            </ul>
          </div>
        </div>
      </Section>
    </Layout>
  </div>

  <Modal :show="showModal">
    <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ language.adding_city }}</h2>

    <form @submit.prevent="submitShippingCities()" class="mt-4">
      <Label :value="language.nameInArabic" class="mt-4 text-right" />
      <Input
        id="name"
        type="text"
        v-model="formShippingCities.name"
        :error="formShippingCities.errors.name"
        class="mt-1 block w-full"
        required
        autocomplete="name"
      />
      <InputError :message="formShippingCities.errors.name" />

      <Label :value="language.nameInEnglish" class="mt-4 text-right" />
      <Input
        id="nameEn"
        type="text"
        v-model="formShippingCities.nameEn"
        :error="formShippingCities.errors.nameEn"
        class="mt-1 block w-full"
        required
        autocomplete="nameEn"
      />
      <InputError :message="formShippingCities.errors.nameEn" />

      <Label :value="language.Delivery_price_range" class="mt-4 text-right" />
      <SelectInput
        v-model="formShippingCities.feeRange"
        class="mt-2 block w-full"
        :selectPlaceholder="language.Delivery_price_range"
        required
      >
        <option value="1">{{ language.delivery_price }} 1</option>
        <option value="2">{{ language.delivery_price }} 2</option>
        <option value="3">{{ language.delivery_price }} 3</option>
      </SelectInput>

      <Label :value="language.parcel_platform_code" class="mt-4 text-right" />
      <Input
        id="code"
        type="text"
        v-model="formShippingCities.code"
        :error="formShippingCities.errors.code"
        class="mt-1 block w-full"
        required
        autocomplete="code"
      />
      <InputError :message="formShippingCities.errors.code" />

      <label class="flex items-start mt-4">
        <Checkbox name="active" v-model:checked="formShippingCities.active" />
        <span class="mr-2 text-sm text-gray-600"> {{ language.activate }}</span>
      </label>

      <progress
        v-if="formShippingCities.progress"
        :value="formShippingCities.progress.percentage"
        max="100"
      >
        {{ formShippingCities.progress.percentage }}%
      </progress>

      <Button
        :class="{ 'opacity-25': formShippingCities.processing }"
        :disabled="formShippingCities.processing"
        class="mt-4 px-6"
      >
        {{ language.add }}
      </Button>
      <Button
        @click="showModal = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </form>
  </Modal>

  <Modal :show="showDeleteModal">
    <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ language.are_you_sure }}</h2>
    <p class="text-base text-gray-600">
      {{ language.All_associated_cities_will_be_deleted }}
    </p>

    <Button class="mt-4 !border" method="post" @click="deleteDistrict()" preserve-scroll>
      <span>{{ language.delete }}</span>
    </Button>
    <Button
      @click="showDeleteModal = false"
      type="button"
      class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
      >{{ language.cancel }}</Button
    >
  </Modal>
</template>

<script>
import Layout from "./Layout.vue";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head, Link } from "@inertiajs/vue3";
import Section from "@/Components/Section.vue";
import Input from "@/Components/Input.vue";
import SelectInput from "@/Components/SelectInput.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import Button from "@/Components/Button.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Link,
    Section,
    Input,
    Label,
    InputError,
    Button,
    Icon,
    Modal,
    Checkbox,
    SelectInput,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    registerCities: Object,
    shippingCities: Object,
    shippingNeighborhood: Object,
    operatorCities: Object,
    employeeCities: Object,
    shippingDistricts: Object,
    language: Object,
  },

  data() {
    return {
      formRegisterCities: this.$inertia.form({
        value: "",
      }),
      formShippingCities: this.$inertia.form({
        name: "",
        nameEn: "",
        active: false,
        code: "",
        feeRange: "",
      }),
      formDistrict: this.$inertia.form({
        value: "",
      }),
      fromShippingNeighborhood: this.$inertia.form({
        value: "",
      }),
      fromOperatorCities: this.$inertia.form({
        value: "",
      }),
      fromEmployeeCities: this.$inertia.form({
        value: "",
      }),
      showModal: false,
      showDeleteModal: false,
      targetDistrictId: "",
    };
  },

  methods: {
    submitRegisterCities() {
      this.formRegisterCities.post(this.route("settings.register_cities.store"), {
        preserveScroll: true,
        onSuccess: () => this.formRegisterCities.reset("value"),
      });
    },

    remove(option, value) {
      this.$inertia.post(
        this.route("settings.cities.remove", [option, value]),
        {},
        {
          preserveScroll: true,
        }
      );
    },

    submitShippingCities() {
      this.formShippingCities.post(
        this.route("settings.districts.cities.store", this.targetDistrictId),
        {
          preserveScroll: true,
          onFinish: () =>
            this.formShippingCities.reset("name", "nameEn", "active", "code"),
          onSuccess: () =>
            this.formShippingCities.reset("name", "nameEn", "active", "code"),
          onError: () =>
            this.formShippingCities.reset("name", "nameEn", "active", "code"),
        }
      );
      this.showModal = false;
    },

    submitFormDistrict() {
      this.formDistrict.post(this.route("settings.districts.store"), {
        preserveScroll: true,
        onSuccess: () => this.formDistrict.reset("value"),
      });
    },

    removeCity(city) {
      this.$inertia.post(
        this.route("settings.cities.remove", city),
        {},
        {
          preserveScroll: true,
        }
      );
    },

    openModal(districtId) {
      this.showModal = true;
      this.targetDistrictId = districtId;
    },

    openDeleteModal(districtId) {
      this.showDeleteModal = true;
      this.targetDistrictId = districtId;
    },

    deleteDistrict() {
      this.$inertia.post(
        this.route("settings.districts.remove", this.targetDistrictId),
        {},
        {
          preserveScroll: true,
        }
      );

      this.showDeleteModal = false;
    },
  },
};
</script>

<template>
  <div>
    <Head :title="language.shipping_city" />

    <page-header class="flex">
      {{ language.city }} {{ city.name }} ({{ city.name_en }})
      <Icon
        name="edit"
        class="w-5 h-5 cursor-pointer text-blue-700 inline-block"
        @click="showUpdateCityModal = true"
      />
    </page-header>
    <flash-messages />
    <Section :title="language.city_areas + `    ${areas.length}`" class="md:m-7 m-4">
      <div class="grid md:gap-7 gap-4 xl:grid-cols-5 lg:grid-cols-3 grid-cols-2 mt-4">
        <div
          v-for="(area, i) in areas"
          :key="i"
          class="flex justify-between flex-col items-center border border-gray-200 text-gray-700 rounded py-6 px-4"
        >
          <div class="text-3xl font-bold my-2 flex"></div>
          <div class="text-center">
            <h3 class="text-lg font-semibold text-center">
              {{ area.name }}
            </h3>
            <Button
              class="!bg-gray-200 !text-gray-600 justify-center my-2"
              @click="neighborhoodsModal(area, i)"
              >الأحياء</Button
            >
            <div class="flex justify-center gap-2 items-center w-full">
              <button class="cursor-pointer" @click="editArea(area)">
                <Icon name="edit" class="w-5 h-5 text-green-600" />
              </button>

              <button class="cursor-pointer" @click="deleteArea(area)">
                <Icon name="cancel" class="w-5 h-5 text-red-600" />
              </button>
            </div>
          </div>
        </div>

        <div
          @click="showModal = true"
          class="flex justify-center flex-col items-center bg-gray-200 text-gray-400 rounded cursor-pointer py-6 px-4"
        >
          <icon name="plus" class="w-8 h-8 my-2" />
          <h3 class="text-lg font-semibold">{{ language.add_an_area }}</h3>
        </div>
      </div>
    </Section>

    <Modal :show="showModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ language.add_an_area }}</h2>

      <form @submit.prevent="submit" class="mt-4">
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

        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
          {{ form.progress.percentage }}%
        </progress>

        <Button
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
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

    <Modal :show="showUpdateModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.modify_an_area }}
      </h2>

      <form @submit.prevent="submitEditAreaForm" class="mt-4">
        <Input
          id="name"
          type="text"
          v-model="formEditArea.name"
          :error="formEditArea.errors.name"
          class="mt-4 block w-full"
          required
          :placeholder="language.the_name"
          autocomplete="name"
        />
        <InputError :message="formEditArea.errors.name" />

        <progress
          v-if="formEditArea.progress"
          :value="formEditArea.progress.percentage"
          max="100"
        >
          {{ formEditArea.progress.percentage }}%
        </progress>

        <Button
          :class="{ 'opacity-25': formEditArea.processing }"
          :disabled="formEditArea.processing"
          class="mt-4 px-6"
        >
          {{ language.save }}
        </Button>
        <Button
          @click="showUpdateModal = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ language.cancel }}</Button
        >
      </form>
    </Modal>

    <Modal :show="showNeighborhoodsModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.neighborhoods }} {{ targetArea.name }}
      </h2>

      <form @submit.prevent="submitFormAddNeighborhood" class="mt-4">
        <Input
          id="name"
          type="text"
          v-model="formAddNeighborhood.name"
          :error="formAddNeighborhood.errors.name"
          class="mt-4 block w-full"
          required
          :placeholder="language.add_neighborhood"
          autocomplete="name"
        />
        <InputError :message="formAddNeighborhood.errors.name" />

        <progress
          v-if="formAddNeighborhood.progress"
          :value="formAddNeighborhood.progress.percentage"
          max="100"
        >
          {{ formAddNeighborhood.progress.percentage }}%
        </progress>

        <Button
          :class="{ 'opacity-25': formAddNeighborhood.processing }"
          :disabled="formAddNeighborhood.processing"
          class="mt-4 px-6"
        >
          {{ language.add }}
        </Button>
        <Button
          @click="showNeighborhoodsModal = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ language.close }}</Button
        >

        <div class="mt-7 border rounded border-gray-100">
          <h3 class="text-md px-4 py-3 bg-gray-100">
            {{ language.list_of_neighborhoods }}
          </h3>
          <div class="p-4 max-h-96 overflow-y-scroll">
            <ul v-if="index !== null">
              <li
                v-for="(neighborhood, i) in areas[index].neighborhoods"
                :key="i"
                class="flex gap-2 text-gray-600 mb-2"
              >
                <Icon
                  @click="deleteNeighborhood(neighborhood)"
                  name="x"
                  class="w-5 h-5 cursor-pointer text-red-700"
                />
                <span>{{ neighborhood.name }} </span>
              </li>
              <li
                v-if="
                  areas[index].neighborhoods == null ||
                  areas[index].neighborhoods.length == 0
                "
                class="text-red-500 text-sm flex gap-2"
              >
                <Icon name="warning" class="w-4 h-4" />
                {{ language.there_are_no_neighborhoods_yet }}
              </li>
            </ul>
          </div>
        </div>
      </form>
    </Modal>

    <Modal :show="showDeleteAreaAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.are_you_sure }}
      </h2>
      <p class="text-base text-gray-600">
        {{ language.do_you_want_to_confirm_deletion_of_the_region }}
      </p>

      <Button
        class="mt-4 !border"
        :href="deleteAreaLink"
        method="post"
        @click="showDeleteAreaAlert = false"
      >
        <span>{{ language.delete }}</span>
      </Button>
      <Button
        @click="showDeleteAreaAlert = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>

    <Modal :show="showUpdateCityModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.modify_the_city }}
      </h2>

      <form @submit.prevent="submitEditCityForm()" class="mt-4">
        <Label :value="language.nameInArabic" class="mt-4 text-right" />
        <Input
          id="name"
          type="text"
          v-model="formEditCity.name"
          :error="formEditCity.errors.name"
          class="mt-1 block w-full"
          required
        />
        <InputError :message="formEditCity.errors.name" />

        <Label :value="language.nameInEnglish" class="mt-4 text-right" />
        <Input
          id="nameEn"
          type="text"
          v-model="formEditCity.nameEn"
          :error="formEditCity.errors.nameEn"
          class="mt-1 block w-full"
          required
          autocomplete="nameEn"
        />
        <InputError :message="formEditCity.errors.nameEn" />

        <Label :value="language.Delivery_price_range" class="mt-4 text-right" />
        <SelectInput
          v-model="formEditCity.feeRange"
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
          v-model="formEditCity.code"
          :error="formEditCity.errors.code"
          class="mt-1 block w-full"
          required
        />
        <InputError :message="formEditCity.errors.code" />

        <label class="flex items-start mt-4">
          <Checkbox name="active" v-model:checked="formEditCity.active" />
          <span class="mr-2 text-sm text-gray-600"> {{ language.activate }}</span>
        </label>

        <progress
          v-if="formEditCity.progress"
          :value="formEditCity.progress.percentage"
          max="100"
        >
          {{ formEditCity.progress.percentage }}%
        </progress>

        <Button
          :class="{ 'opacity-25': formEditCity.processing }"
          :disabled="formEditCity.processing"
          class="mt-4 px-6"
        >
          {{ language.modify }}
        </Button>
        <Button
          @click="showUpdateCityModal = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ language.cancel }}</Button
        >
      </form>
    </Modal>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/vue3";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import Section from "@/Components/Section.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import Checkbox from "@/Components/Checkbox.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import Icon from "@/Components/Icon.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Modal from "@/Components/Modal.vue";

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Section,
    Icon,
    Link,
    FlashMessages,
    Modal,
    Input,
    InputError,
    Checkbox,
    Label,
    SelectInput,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    areas: { type: Object, default: [] },
    city: Object,
    language: Object,
  },

  data() {
    return {
      showModal: false,
      showUpdateCityModal: false,
      showUpdateModal: false,
      showDeleteAreaAlert: false,
      showNeighborhoodsModal: false,
      deleteAreaLink: "",
      targetArea: {},
      index: null,
      form: this.$inertia.form({
        name: "",
      }),
      formEditArea: this.$inertia.form({
        name: "",
      }),
      formEditCity: this.$inertia.form({
        name: this.city.name,
        nameEn: this.city.name_en,
        code: this.city.code,
        feeRange: this.city.fee_range,
        active: this.city.active == 1 ? true : false,
      }),
      formAddNeighborhood: this.$inertia.form({
        name: "",
      }),
    };
  },
  methods: {
    deleteArea(area) {
      this.deleteAreaLink = route("cities.areas.delete", area);
      this.showDeleteAreaAlert = true;
    },
    submit() {
      this.form.post(this.route("cities.areas.store", this.city), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
      });
      this.showModal = false;
    },
    editArea(area) {
      this.showUpdateModal = true;
      this.targetArea = area;
      this.formEditArea.name = area.name;
    },

    submitEditAreaForm() {
      this.formEditArea.post(this.route("cities.areas.update", this.targetArea), {
        preserveScroll: true,
      });
      this.showUpdateModal = false;
    },
    neighborhoodsModal(area, i) {
      this.showNeighborhoodsModal = true;
      this.targetArea = area;
      this.index = i;
    },

    submitFormAddNeighborhood() {
      this.formAddNeighborhood.post(
        this.route("cities.areas.neighborhood.store", this.targetArea),
        {
          preserveScroll: true,
          onSuccess: () => this.formAddNeighborhood.reset(),
        }
      );
    },

    deleteNeighborhood(neighborhood) {
      this.$inertia.post(this.route("cities.areas.neighborhood.delete", neighborhood), {
        preserveScroll: true,
      });
    },

    submitEditCityForm() {
      this.formEditCity.post(this.route("settings.cities.update", this.city), {
        preserveScroll: true,
      });
      this.showUpdateCityModal = false;
    },
  },
};
</script>

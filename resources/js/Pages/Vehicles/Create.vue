<template>
  <div>
    <Head :title="$page.props.language.add_a_car" />


    <page-header>
      <div class="flex justify-between">
        <div>
          {{ $page.props.language.add_a_new_car }}
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
          {{ $page.props.language.add_a_new_car }}
        </h2>

        <flash-messages />

        <form @submit.prevent="submit" class="mt-4">
          <input type="hidden" v-model="form.group" />

          <SelectInput
            v-model="form.group"
            :error="form.errors.group"
            class="mt-4 block w-full"
            :selectPlaceholder="$page.props.language.city"
            :readonly="true"
          >
            <option v-for="group in groups" :key="group.id" :value="group.id">
              {{ group.name }}
            </option>
          </SelectInput>

          <Input
            id="name"
            type="text"
            v-model="form.name"
            :error="form.errors.name"
            class="mt-4 block w-full"
            required
            :placeholder="$page.props.language.the_name"
          />
          <InputError :message="form.errors.name" />

          <Input
            id="mark"
            type="text"
            v-model="form.mark"
            :error="form.errors.mark"
            class="mt-4 block w-full"
            :placeholder="$page.props.language.car_brand"
            autocomplete="name"
          />
          <InputError :message="form.errors.mark" />

          <Input
            id="plateNumber"
            type="text"
            v-model="form.plateNumber"
            :error="form.errors.plateNumber"
            class="mt-4 block w-full"
            required
            :placeholder="$page.props.language.car_number"
          />
          <InputError :message="form.errors.plateNumber" />

          <Input
            id="backPlateNumber"
            type="text"
            v-model="form.backPlateNumber"
            :error="form.errors.backPlateNumber"
            class="mt-4 block w-full"
            required
            :placeholder="$page.props.language.car_number_on_th_back"
          />
          <InputError :message="form.errors.backPlateNumber" />

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ $page.props.language.add }}
          </Button>
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
import BackButton from "../../Components/backButton.vue"
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
    InputError,BackButton
  },
  layout: BreezeAuthenticatedLayout,

  props: ["group", "groups"],
  language: Object,
  data() {
    return {
      form: this.$inertia.form({
        name: "",
        mark: "",
        plateNumber: "",
        backPlateNumber: "",
        group: this.group,
      }),
    };
  },

  methods: {

    submit() {
      this.form.post(this.route("vehicles.store"), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },
  },
};
</script>

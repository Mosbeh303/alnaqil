<template>
  <Head title="Dashboard" />

  <div>
    <page-header> {{ language.dashboard }}</page-header>

    <FlashMessages class="md:m-7 m-4" />

    <div class="pb-12 pt-7 sm:px-0 px-4">
      <!-- Main Container -->

      <!-- Top Stats Widgets -->
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mb-7">
        <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
          <Widget
            color="text-green-500"
            iconName="completed"
            :widgetText="language.shipments"
            :widgetValue="stats.shipments_number"
          />
          <Widget
            color="text-orange-500"
            iconName="users"
            :widgetText="language.clients"
            :widgetValue="stats.stores_number"
          />

          <Widget
            color="text-red-500"
            iconName="cash"
            :widgetText="language.dues"
            :widgetValue="`${stats.dues}  ` + language.riyal"
          />
        </div>
      </div>
    </div>

    <div class="grid sm:px-6 lg:px-8 gap-7 xl:grid-cols-2">
      <!-- Create new shipment -->
      <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
          {{ language.add_shipment }}
        </h2>

        <form @submit.prevent="submit" class="mt-4" v-if="show_form">
          <Input
            id="receiverPhone"
            type="text"
            v-model="form.receiverPhone"
            :error="form.errors.receiverPhone"
            class="mt-4 block w-full"
            required
            :placeholder="language.mobile_number + '0501234567'"
            autocomplete="receiverPhone"
          />
          <InputError :message="form.errors.receiverPhone" />

          <Input
            id="receiver"
            type="text"
            v-model="form.receiver"
            :error="form.errors.receiver"
            class="mt-4 block w-full mb-4"
            required
            :placeholder="language.recipient_name"
            autocomplete="receiver"
          />
          <InputError :message="form.errors.receiver" />

          <Input
            id="orderId"
            type="text"
            v-model="form.order_id"
            :error="form.errors.order_id"
            class="mt-4 block w-full mb-4"
            :placeholder="language.order_number"
          />
          <InputError :message="form.errors.order_id" />

          <div class="mb-4">
            <SelectInput
              v-model="form.district"
              :error="form.errors.district"
              class="mt-4 block w-full"
              :selectPlaceholder="language.terretory"
              @change="cityChanged()"
            >
              <option
                v-for="(district, i) in districts"
                :key="i"
                :value="district.id"
              >
                {{ district.name }}
              </option>
            </SelectInput>
          </div>

          <searchableSelect
            :myOptions="computedCities"
            :myVModel="form.city"
            :my_Place_holder="$page.props.language.city"
            :my_Place_holder_when_Nothing_Found="
              $page.props.language.No_cities_in_this_terretory
            "
            @update:myVModel="form.city = $event"
            @click="cityChanged"
          />

          <Input
            id="neighborhood"
            list="neighborhoods"
            type="text"
            v-model="form.neighborhood"
            :error="form.errors.neighborhood"
            class="mt-4 block w-full"
            required
            :placeholder="language.Neighborhood"
            autocomplete="off"
          />
          <InputError :message="form.errors.neighborhood" />

          <datalist id="neighborhoods">
            <option
              v-for="(neighborhood, i) in neighborhoods"
              :key="i"
              :value="neighborhood"
            />
          </datalist>

          <Input
            id="street"
            type="text"
            v-model="form.street"
            :error="form.errors.street"
            class="mt-4 block w-full"
            required
            :placeholder="language.street_name"
            autocomplete="street"
          />
          <InputError :message="form.errors.street" />

          <searchableSelect
                        v-if="$page.props.auth.account != 'client'"
                        :myOptions="computedStores"
                        :myVModel="form.store"
                        :my_Place_holder="
                            $page.props.language.store_name_or_number
                        "
                        :my_Place_holder_when_Nothing_Found="
                            $page.props.language.no_stores
                        "
                        @update:myVModel="form.store = $event"
                    />

          <Input
            id="numberOfPieces"
            type="number"
            max="2"
            min="1"
            v-model="form.numberOfPieces"
            class="mt-4 block w-full"
            :error="form.errors.numberOfPieces"
            :placeholder="language.number_of_packages"
            @keyup="maxNumberOfPieces()"
          />
          <InputError :message="form.errors.numberOfPieces" />
          <small class="text-gray-500">
            {{ language.the_maximum_number_of_packages_is }}</small
          >

          <Input
            id="details"
            type="text"
            v-model="form.details"
            class="mt-4 block w-full"
            :error="form.errors.details"
            :placeholder="language.package_contents"
            autocomplete="details"
          />
          <InputError :message="form.errors.details" />

          <Input
                        id="ADP"
                        type="text"
                        v-model="form.ADP"
                        :error="form.errors.ADP"
                        class="mt-4 block w-full mb-4"
                        autofocus
                        :placeholder="language.ADP"
                    />
                    <InputError :message="form.errors.ADP" />
          <Input
            id="cod"
            type="text"
            v-model="form.cod"
            class="mt-4 block w-full"
            :error="form.errors.cod"
            :placeholder="
              language.the_amount_of_the_shipment_due_from_th_customer
            "
            autocomplete="cod"
          />
          <InputError :message="form.errors.cod" />

          <Input
            id="store"
            type="hidden"
            v-model="form.store"
            class="mt-4 block w-full"
            :error="form.errors.store"
            required
            :placeholder="language.store_number"
            autocomplete="store"
          />
          <InputError :message="form.errors.store" />

          <Input
            id="weight"
            type="number"
            step="0.01"
            v-model="form.weight"
            class="mt-4 block w-full"
            :error="form.errors.weight"
            :placeholder="language.shipment_weight_kg"
          />
          <InputError :message="form.errors.weight" />

          <div
            class="block mt-4"
          >
            <label class="flex items-center">
              <Checkbox name="retrun" v-model:checked="form.return" />
              <span class="mr-2 text-sm text-gray-600">
                {{ language.retrieval_from_the_client }}
              </span>
            </label>
          </div>

          <div
            class="block mt-4"
          >
            <label class="flex items-center">
              <Checkbox name="exchange" v-model:checked="form.exchange" />
              <span class="mr-2 text-sm text-gray-600">
                {{ language.exchange_from_the_customer }}
              </span>
            </label>
          </div>

          <div
            class="block mt-4"
          >
            <label class="flex items-center">
              <Checkbox
                name="refrigerated"
                v-model:checked="form.refrigerated"
              />
              <span class="mr-2 text-sm text-gray-600">
                {{ language.refrigerated_transport }}
              </span>
            </label>
          </div>

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
          >
            {{ language.addSHipment }}</Button
          >
        </form>

        <div v-else>
          <Input
            type="text"
            class="mt-4 block w-full"
            name="retrun"
            v-model="receiver_phone"
            :placeholder="language.whrite_receiver_phone"
          />
          <Button class="mt-4 px-6" @click="show_shipment_form">
            {{ language.next }}
          </Button>
        </div>
      </div>
    </div>
    <!-- End Main Container -->
  </div>
</template>

  <script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Widget from "@/Pages/Dashboard/Components/Widget.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import Icon from "@/Components/Icon.vue";
import Pagination from "@/Components/Pagination.vue";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import searchableSelect from "../../Components/searchableSelect.vue";

export default {
  components: {
    Head,
    Link,
    PageHeader,
    Widget,
    Button,
    Icon,
    Pagination,
    Modal,
    Label,
    Input,
    SelectInput,
    InputError,
    Checkbox,
    FlashMessages,
    searchableSelect,
  },

  layout: BreezeAuthenticatedLayout,

  props: {
    stats: Object,
    language: Object,
    cities: Object,
    districts: Object,
    shipments: Object ,
    stores: Object ,

  },

  data() {
    return {
        receiver_phone: null,
      show_form: false,
      form: this.$inertia.form({
        receiver: "",
        city: "",
        neighborhood: "",
        district: "",
        street: "",
        receiverPhone: "",
        cod: "",
        ADP:"",
        store: "" ,
        details: "",
        refrigerated: false,
        exchange: false,
        return: false,
        weight: "",
        numberOfPieces: "",
        order_id: "",
      }),
    };
  },

  computed: {
    computedStores: function () {
            let options = [];

            options = Object.values(this.stores)
                .filter((option) => option.name !== null)
                .map((option) => option.name);

            return options;
        },

    computedCities: function () {
      let cities = {};
      if (this.form.district == "") {
        cities = this.cities.map((city) => city.name);
      } else {
        cities = this.cities
          .filter((city) => city.district_id == this.form.district)
          .map((city) => city.name);
      }
      return cities;
    },
  },

  methods: {
    show_shipment_form() {
      const regex = /^\d{10}$/; // Regular expression for 10 digits
      const isValidPhone = regex.test(this.receiver_phone);

      if (isValidPhone) {
        const last_sent_shipment = this.shipments
          .filter((shipment) => shipment.receiverPhone == this.receiver_phone)
          .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

        if (last_sent_shipment) {
          this.form.receiver = last_sent_shipment.receiverName;
          this.form.city = last_sent_shipment.city;
          this.form.neighborhood = last_sent_shipment.neighborhood;
          this.form.details = last_sent_shipment.details;
          this.form.street = last_sent_shipment.street;
          this.form.numberOfPieces = 1;
        }
        this.form.receiverPhone = this.receiver_phone;
      }

      this.show_form = true;
      this.receiver_phone = null;
    },

    submit() {
      this.form.post(this.route("shipments.store"), {
        preserveScroll: true,
        onSuccess: () => {
          this.form.reset();
          this.show_form = false;
        },
      });
    },

    setNeighborhoods() {
      let city = this.form.city;
      console.log("city:" + city);

      axios.get("/dashboard/neighborhoods/" + city).then((response) => {
        console.log(response);
        this.neighborhoods = response.data;
      });
    },

    cityChanged() {
      setTimeout(() => {
        this.setNeighborhoods();
      }, 100);
    },

    maxNumberOfPieces() {
      if (this.form.numberOfPieces > 2) this.form.numberOfPieces = 2;
    },
  },
};
</script>

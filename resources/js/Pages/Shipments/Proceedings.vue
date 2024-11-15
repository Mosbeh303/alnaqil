<template>
  <div>
    <Head :title="language.fast_processing_of_shipments" />

    <page-header> {{ language.fast_processing_of_shipments }} </page-header>

    <Section :title="language.Add_shipment_numbers_consecutively" class="md:m-7 m-4">
      <form @submit.prevent="submit()" class="pb-2" id="form">
        <Input
          id="search"
          type="text"
          :placeholder="language.add_number"
          class="w-full mt-4"
          v-model="form.search"
          :error="form.errors.search"
        ></Input>
        <InputError :message="form.errors.search" />
      </form>
    </Section>

    <div class="inline-flex rounded-md shadow-sm md:mx-7 mx-4" role="group">
      <button
        v-if="
          $page.props.auth.account == 'admin' ||
          $page.props.auth.permissions.includes('warehouse_shipments')
        "
        @click.capture="
          (warehouseBox = true),
            (statusBox = false),
            (OperatorBox = false),
            (JtBox = false),
            (link = route('shipments.proceedings.action', 'warehouse'))
        "
        type="button"
        :class="{ 'bg-gray-800 text-white': warehouseBox }"
        class="bg-gray-100 py-2 px-4 text-sm font-medium text-gray-900 rounded-r-lg border border-gray-200 focus:text-white"
      >
        {{ language.add_the_storage_location_to_the_shipments }}
      </button>
      <button
        v-if="
          $page.props.auth.account == 'admin' ||
          $page.props.auth.permissions.includes('status_shipments')
        "
        @click.capture="
          (warehouseBox = false),
            (statusBox = true),
            (OperatorBox = false),
            (JtBox = false),
            (link = route('shipments.proceedings.action', 'status'))
        "
        type="button"
        :class="{ 'bg-gray-800 text-white': statusBox }"
        class="py-2 px-4 text-sm font-medium text-gray-900 bg-gray-100 border-t border-b border-gray-200 focus:text-white"
      >
        {{ language.modify_shipment_status }}
      </button>
      <button
        v-if="
          $page.props.auth.account == 'admin' ||
          $page.props.auth.permissions.includes('operator_shipments')
        "
        @click.capture="
          (warehouseBox = false),
            (statusBox = false),
            (OperatorBox = true),
            (JtBox = false),
            (link = route('shipments.proceedings.action', 'operator'))
        "
        type="button"
        :class="{ 'bg-gray-800 text-white': OperatorBox }"
        class="py-2 px-4 text-sm font-medium text-gray-900 bg-gray-100  border border-gray-200 focus:text-white"
      >
        {{ language.attach_a_representative_to_shipments }}
      </button>

      <button
        v-if="
          $page.props.auth.account == 'admin' ||
          $page.props.auth.permissions.includes('operator_shipments')
        "
        @click.capture="
          (warehouseBox = false),
            (statusBox = false),
            (OperatorBox = false),
            (JtBox = true),
            (link = route('shipments.proceedings.action', 'jt'))
        "
        type="button"
        :class="{ 'bg-gray-800 text-white': JtBox }"
        class="py-2 px-4 text-sm font-medium text-gray-900 bg-gray-100 rounded-l-md border border-gray-200 focus:text-white"
      >
        {{ language.download_all_jt }}
      </button>
    </div>

    <div class="grid gap-7 md:grid-cols-2 mt-7 px-7 mb-7">
      <div v-if="warehouseBox">
        <div class="transition h-max pt-4 ease-in-out duration-150 overflow-hidden">
          <Label :value="language.storage_place" />
          <Input
            v-model="form.location"
            :error="form.errors.location"
            class="block w-full mt-2"
            type="text"
            :placeholder="language.storage_place"
            required
          />
          <InputError :message="form.errors.location" />

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
            form="form"
          >
            {{ language.attach }}
          </Button>
        </div>
      </div>

      <div v-if="statusBox">
        <div class="transition h-max pt-4 ease-in-out duration-150 overflow-hidden">
          <Label :value="language.shipment_status" class="mb-2" />
          <SelectInput
            v-model="form.status"
            :error="form.errors.status"
            class="block w-full"
            :selectPlaceholder="language.shipment_status"
            required
          >
            <option
              v-for="(status, i) in $page.props.shipmentStatus"
              :key="i"
              :value="i"
              v-show="
                !((i == 100 || i == -100) && $page.props.auth.account == 'employee')
              "
              :hidden="statusOption(i)"
            >
              {{ status }}
            </option>
          </SelectInput>

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
            form="form"
          >
            {{ language.modify }}
          </Button>
        </div>
      </div>

      <div v-if="OperatorBox">
        <div class="transition h-max pt-4 ease-in-out duration-150 overflow-hidden">
          <Label :value="language.id_number_or_delegates_mobile_number" class="mb-2" />

          <Input
            v-model="form.operator"
            :error="form.errors.operator"
            class="block w-full mt-2"
            type="text"
            :placeholder="language.id_number_or_delegates_mobile_number"
            required
          />
          <InputError :message="form.errors.operator" />

          <Button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="mt-4 px-6"
            form="form"
          >
            {{ language.attach }}
          </Button>
        </div>
      </div>

      <div v-if="JtBox">
        <div class="transition h-max pt-4 ease-in-out duration-150 overflow-hidden">
          <Label :value="language.download_all_jt" class="mb-2" />

          <!-- <Input
            v-model="form.operator"
            :error="form.errors.operator"
            class="block w-full mt-2"
            type="text"
            :placeholder="language.id_number_or_delegates_mobile_number"
            required
          />
          <InputError :message="form.errors.operator" /> -->
          <form :action="route('shipments.proceedings.action', 'jt')" method="post" >
                <input type="hidden" name="_token" :value="$page.props.csrf" />
                <input type="hidden" name="all" v-model="form.all" />

                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="mt-4 px-6"
                >
                    {{ language.download }}
                </Button>
          </form>
        </div>
      </div>


    </div>

    <!-- Shipments -->

    <div
      v-if="shipments"
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white md:mx-7 mx-4 overflow-hidden"
    >
      <!-- ***************** -->

      <div class="flex justify-start">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">{{ language.shipments }}</h2>
        <span class="flex px-3 font-bold text-gray-600"
          ><Icon name="shipment" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1" />
          {{ shipments.total }}
        </span>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th></th>
                    <th class="pb-4 pt-6 px-6">{{ language.Shipment_number }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.notes }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.create }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.shipment_type }}</th>
                    <th
                      class="pb-4 pt-6 px-6"
                      v-if="
                        $page.props.auth.permissions.includes('fee_shipments') ||
                        $page.props.auth.account != 'employee'
                      "
                    >
                      {{ language.delivery_fee }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.paiement_when_recieving }}</th>

                    <th class="pb-4 pt-6 px-6">{{ language.branch }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.Status }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.the_delegate }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.employee }}</th>

                    <th class="pb-4 pt-6 px-6">{{ language.storage_place }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="shipment in shipments.data"
                    :key="shipment.id"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                  >
                    <td>
                      <button class="px-2" @click="deleteShipment(shipment.number)">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-500"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                          />
                        </svg>
                      </button>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4 focus:text-indigo-500"
                        :href="route('shipments.show', shipment.id)"
                      >
                        {{ shipment.number }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.notice }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.created_at }}
                      </Link>
                    </td>

                    <td class="border-t px-6">
                      <span v-if="shipment.return_back == 1">{{ language.redux }}</span>
                      <span v-else-if="shipment.refrigerated == 1">{{
                        language.refrigerated_transport
                      }}</span>
                      <span v-else-if="shipment.exchange == 1">{{
                        language.replacing
                      }}</span>
                      <span v-else>{{ language.normal }}</span>
                    </td>

                    <td
                      class="border-t"
                      v-if="
                        $page.props.auth.permissions.includes('fee_shipments') ||
                        $page.props.auth.account != 'employee'
                      "
                    >
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.fees }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        <span v-if="!shipment.cod">{{ language.prepaid }}</span>
                        <span v-else>{{ shipment.cod }}</span>
                      </Link>
                    </td>

                    <td class="border-t">
                      <span v-if="shipment.branchForOperator">{{
                        shipment.branchForOperator.name
                      }}</span>
                      <span v-else-if="shipment.branchForEmployee">{{
                        shipment.branchForEmployee.name
                      }}</span>
                    </td>

                    <td class="border-t whitespace-nowrap">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        <p
                          class="rounded-xl text-gray-700 bg-gray-200 py-1 px-4 w-fit text-xs"
                          v-if="shipment.status == 10"
                        >
                          {{ language.created }}
                        </p>
                        <p
                          class="rounded-xl text-orange-700 bg-orange-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == 20"
                        >
                          {{ language.received }}
                        </p>
                        <p
                          class="rounded-xl text-blue-700 bg-blue-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == 35"
                        >
                          {{ language.processing_in_progress }}
                        </p>
                        <p
                          class="rounded-xl text-blue-700 bg-blue-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == 65"
                        >
                          {{ language.Waiting_for_delivery }}
                        </p>
                        <p
                          class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == 100"
                        >
                          {{ language.delivered }}
                        </p>
                        <p
                          class="rounded-xl text-yellow-700 bg-yellow-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == -100"
                        >
                          {{ language.audit }}
                        </p>
                        <p
                          class="rounded-xl text-red-700 bg-red-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == -1"
                        >
                          {{ language.canceled }}
                        </p>
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        v-if="shipment.operator"
                        class="flex items-center px-6 py-4"
                        :href="route('operator.show', shipment.operator.id)"
                        tabindex="-1"
                      >
                        {{ shipment.operator.name }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        v-if="shipment.employee"
                        class="flex items-center px-6 py-4"
                        tabindex="-1"
                      >
                        {{ shipment.employee.name }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.warehouseLocation }}
                      </Link>
                    </td>
                  </tr>
                  <tr v-if="shipments.data.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.no_shipments }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="shipments.links" />
    </div>
  </div>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import Section from "@/Components/Section.vue";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import SelectInput from "@/Components/SelectInput.vue";
import Pagination from "@/Components/Pagination.vue";
import throttle from "lodash/throttle";
import Icon from "@/Components/Icon.vue";
import { lang } from "moment";

export default {
  components: {
    Head,
    Link,
    PageHeader,
    Button,
    Input,
    BreezeValidationErrors,
    FlashMessages,
    InputError,
    Label,
    Section,
    SelectInput,
    Pagination,
    Icon,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    Operators: Object,
    shipments: Object,
    numbers: Array,
    language: Object,
  },

  data() {
    return {
      warehouseBox: false,
      statusBox: false,
      OperatorBox: false,
      JtBox: false,
      link: "",
      form: this.$inertia.form({
        search: "",
        status: "",
        location: "",
        operator: "",
        all: [],
      }),
    };
  },

  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        if (this.form.search.length === 10) {
          if (!this.form.all.includes(this.form.search)) {
            this.form.all.push(this.form.search);
          }

          this.form.post(this.route("shipments.proceedings.get"), {
            preserveScroll: true,
            onSuccess: () => this.form.reset("search"),
            onError: () => this.form.reset("search"),
            onFinish: () => this.form.reset("search"),
          });
        }
      }, 150),
    },
  },

  methods: {
    deleteShipment(number) {
      let index = this.form.all.indexOf(number);
      this.form.all.splice(index, 1);
      this.form.post(this.route("shipments.proceedings.get"), {
        preserveScroll: true,
      });
    },

    submit() {
      if (this.form.processing) {
        return false;
      }

      this.form.post(this.link, {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },

    statusOption(status) {
      if ((status == 10 || status == 20) && this.$page.props.auth.account != "admin")
        return true;
      else return false;
    },
  },
};
</script>

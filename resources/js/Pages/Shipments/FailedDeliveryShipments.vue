<template>
  <Head :title="language.Unsuccessful_deliveries" />

  <div>
    <page-header>
      {{ language.Unsuccessful_deliveries }}
    </page-header>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white md:m-7 m-4 overflow-hidden"
    >
      <!-- ***************** -->

      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
            <h2 class="text-gray-700 text-lg mb-4 font-semibold">
  <span class="flex items-center">
    {{ language.shipments }}
    <span class="flex px-3 font-bold text-gray-600">
      <Icon name="shipment" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1" />
      {{ shipments.total }}
    </span>
  </span>
</h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
          </search-filter>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th class="pb-4 pt-6 px-6">{{ language.Shipment_number }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.sender_name }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.The_recipients_name }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.phone_number }}</th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.name_of_the_receiving_delegate }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.number_of_attempts }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="shipment in shipments.data"
                    :key="shipment.id"
                    class="hover:bg-gray-100 text-center whitespace-nowrap focus-within:bg-gray-100"
                  >
                    <td class="border-t">
                      <Link
                        class="flex items-center justify-center px-6 py-4 focus:text-indigo-500"
                        :href="route('shipments.show', shipment.id)"
                      >
                        {{ shipment.number }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center justify-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.storeName }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center justify-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        <span>{{ shipment.receiverName }}</span>
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center justify-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.receiverPhone }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center justify-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.operator }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center justify-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.failed }}
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
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import SearchFilter from "@/Components/SearchFilter.vue";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import SelectInput from "@/Components/SelectInput.vue";
import Pagination from "@/Components/Pagination.vue";
import Icon from "@/Components/Icon.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  components: {
    Head,
    PageHeader,
    SearchFilter,
    Button,
    Link,
    SelectInput,
    Pagination,
    Icon,
    Checkbox,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    filters: Object,
    shipments: Object,
    language: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
      },
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/shipments/failed", pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
        });
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
  },
};
</script>

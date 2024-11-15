<template>
  <Head title=" {{ language.Print_the_bill_for_shipment_No }} {{ shipment.number }} " />

  <div>
    <page-header>
      {{ language.Print_the_bill_for_shipment_No }} {{ shipment.number }}
    </page-header>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white md:m-7 m-4 overflow-hidden"
    >
      <!-- component -->
      <div class="max-w-md mx-auto items-center">
        <div
          v-for="(print, i) in prints.data"
          :key="i"
          class="flex justify-start px-3 py-1 bg-white items-center gap-1 rounded-lg border border-gray-100 my-3"
        >
          <div
            class="ml-4 relative w-16 h-16 rounded-full hover:bg-red-700 bg-gradient-to-r from-purple-400 via-blue-500 to-red-400"
          >
            <div
              class="flex justify-center items-end absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-14 h-14 bg-gray-200 rounded-full border-2 border-white"
            >
              <span class="font-bold text-4xl">{{ i + 1 }}</span>
            </div>
          </div>
          <div>
            <span class="font-mono" v-if="print.user.type == 'client'"
              >{{ language.The_store_printed_the_policy }}
            </span>
            <span class="font-mono" v-else-if="print.user.type == 'employee'"
              >{{ language.The_employee_did }} {{ print.user.name }}
              {{ language.printing_the_policy }}
            </span>
            <span class="font-mono" v-else-if="print.user.type == 'operator'"
              >{{ language.The_delegate_did }} {{ print.user.name }}
              {{ language.printing_the_policy }}
            </span>
            <span class="font-mono" v-else-if="print.user.type == 'admin'"
              >{{ language.The_admin_did }} {{ print.user.name }}
              {{ language.printing_the_policy }}
            </span>
            <span class="font-mono block text-gray-500 text-xs mt-2">
              {{ print.date }}</span
            >
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="prints.links" />
    </div>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";

export default {
  components: {
    Head,
    PageHeader,
    Link,
    Pagination,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    prints: Object,
    shipment: Object,
    language: Object,
  },
  data() {
    return {};
  },
};
</script>

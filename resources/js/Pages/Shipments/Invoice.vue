<template>
  <div>
    <Head :title="`SA${shipment.invoice}`" />

    <page-header class="no-print text-center">
      {{ language.invoice_no }} SA{{ shipment.invoice }}
    </page-header>

    <div
      class="font-medium border rounded border-gray-200 p-5 sm:p-8 bg-white m-4 text-gray-800 max-w-5xl mx-auto"
      id="print"
      ref="content"
    >
      <div class="w-full flex justify-center">
        <Logo class="w-36" />
      </div>
      <div class="grid grid-cols-2 gap-2 mt-6">
        <div>
          <ul>
            <li class="font-bold text-lg">{{ language.fast_express }}</li>
            <li>{{ language.rayhana }}</li>
            <li>{{ language.alriyadh }} 1337 - 8118</li>
            <li>{{ language.saudi_arabite }}</li>
          </ul>
        </div>
        <div>
          <table class="w-full border-collapse border border-slate-700">
            <tr>
              <td class="py-1 pr-2 pl-12 border border-slate-600 bg-gray-300">
                {{ language.date }}
              </td>
              <td class="py-1 pr-2 pl-12 border border-slate-600 font-bold">
                {{ shipment.date }}
              </td>
            </tr>
            <tr>
              <td class="py-1 pr-2 pl-12 border border-slate-600 bg-gray-300">
                {{ language.bill_number }}
              </td>
              <td class="py-1 pr-2 pl-12 border border-slate-600 font-bold">
                SA{{ shipment.invoice }}
              </td>
            </tr>
            <tr>
              <td class="py-1 pr-2 pl-12 border border-slate-600 bg-gray-300">
                {{ language.tax_number }}
              </td>
              <td class="py-1 pr-2 pl-12 border border-slate-600">202126521251</td>
            </tr>
          </table>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2 mt-6">
        <table class="w-full border-collapse border border-slate-700">
          <tr>
            <td class="py-1 pr-2 pl-12 border border-slate-600 bg-gray-300 font-bold">
              {{ language.customer_name }}
            </td>
            <td class="py-1 pr-2 pl-12 border border-slate-600">
              {{ shipment.storeName }}
            </td>
          </tr>
          <tr>
            <td class="py-1 pr-2 pl-12 border border-slate-600 bg-gray-300 font-bold">
              {{ language.service }}
            </td>
            <td class="py-1 pr-2 pl-12 border border-slate-600">
              {{ language.package_transport }}
            </td>
          </tr>
        </table>

        <table class="w-full border-collapse border border-slate-700">
          <tr>
            <td class="py-1 pr-2 pl-12 border border-slate-600 bg-gray-300 font-bold">
              {{ language.phone_number }}
            </td>
            <td class="py-1 pr-2 pl-12 border border-slate-600">
              {{ shipment.storePhone }}
            </td>
          </tr>
          <tr>
            <td class="py-1 pr-2 pl-12 border border-slate-600 bg-gray-300 font-bold">
              {{ language.tax_number }} (VAT)
            </td>
            <td class="py-1 pr-2 pl-12 border border-slate-600">
              {{ shipment.storeTaxNumber }}
            </td>
          </tr>
        </table>
      </div>
      <div class="w-full mt-4">
        <table class="w-full border-collapse border border-slate-700">
          <tr>
            <td class="py-1 px-2 border border-slate-600 bg-gray-300 font-bold">
              {{ language.customer_address }}
            </td>
            <td class="py-1 px-2 border border-slate-600">
              {{ shipment.storeCity }}, {{ shipment.storeNeighborhood }}
            </td>
            <!-- <td class="py-1 px-2 border border-slate-600 ">اسم الحي: {{shipment.storeNeighborhood}}</td> -->
          </tr>
        </table>
      </div>

      <div class="w-full mt-4">
        <p class="mb-2 font-bold">{{ language.invoice_details }}</p>
        <table class="w-full border-collapse border border-slate-700">
          <tr>
            <td class="py-1 px-2 border border-slate-600 bg-gray-300 font-bold">
              {{ language.Shipment_number }}
            </td>
            <td class="py-1 px-2 border border-slate-600 bg-gray-300 font-bold">
              {{ language.package_contents }}
            </td>
            <td class="py-1 px-2 border border-slate-600 bg-gray-300 font-bold">
              {{ language.delivery_price }}
            </td>
            <td class="py-1 px-2 border border-slate-600 bg-gray-300 font-bold">
              {{ language.rate_of_tax_application }}
            </td>
            <td class="py-1 px-2 border border-slate-600 bg-gray-300 font-bold">
              {{ language.tax }}
            </td>
            <td class="py-1 px-2 border border-slate-600 bg-gray-300 font-bold">
              {{ language.payment_amount_upon_receipt }}
            </td>
            <td class="py-1 px-2 border border-slate-600 bg-gray-300 font-bold">
              {{ language.Status }}
            </td>
          </tr>
          <tr>
            <td class="py-1 px-2 border border-slate-600">{{ shipment.number }}</td>
            <td class="py-1 px-2 border border-slate-600">{{ shipment.details }}</td>
            <td class="py-1 px-2 border border-slate-600">{{ shipment.fees }}</td>
            <td class="py-1 px-2 border border-slate-600">15%</td>
            <td class="py-1 px-2 border border-slate-600">{{ shipment.tax }}</td>
            <td class="py-1 px-2 border border-slate-600">
              <span v-if="!shipment.cod">{{ language.pre_paid }}</span>
              <span v-else-if="shipment.status == -100">0</span>
              <span v-else>{{ shipment.cod }}</span>
            </td>
            <td class="py-1 px-2 border border-slate-600">
              {{ $page.props.shipmentStatus[shipment.status] }}
            </td>
          </tr>
        </table>
      </div>
      <div class="grid grid-cols-2 gap-2 mt-6">
        <div>
          <table class="w-full border-separate border-spacing-0 border-slate-700">
            <tr class="mb-4">
              <td class="mb-4 py-1 pr-2 pl-2 border border-slate-600 bg-gray-300">
                {{ language.total_amount }}
              </td>
              <td class="mb-1 py-1 pr-2 pl-2 border border-slate-600 font-bold">
                {{ shipment.fees - shipment.tax }} {{ language.riyal }}
              </td>
            </tr>
            <tr>
              <td class="py-1 pr-2 pl-2 border border-slate-600 bg-gray-300">
                {{ language.vat }}
              </td>
              <td class="py-1 pr-2 pl-2 border border-slate-600">
                {{ shipment.tax }} {{ language.riyal }}
              </td>
            </tr>
            <tr>
              <td class="py-1 pr-2 pl-2 border border-slate-600 bg-gray-300">
                {{ language.total_including_vat }}
              </td>
              <td class="py-1 pr-2 pl-2 border border-slate-600">
                {{ shipment.fees }} {{ language.riyal }}
              </td>
            </tr>
          </table>
        </div>
        <div class="flex justify-center">
          <qrcode
            :value="route('shipments.invoice', shipment.id)"
            :size="size"
            level="H"
          />
        </div>
      </div>
    </div>

    <div class="flex justify-center mb-4 no-print">
      <Button @click="print()" class="mx-auto">{{ language.download_the_policy }}</Button>
    </div>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head } from "@inertiajs/vue3";
import Logo from "@/Components/ApplicationLogo.vue";
import Barcode from "@/Components/Barcode.vue";
import Qrcode from "qrcode.vue";
import Button from "@/Components/Button.vue";
import { stringify } from "postcss-value-parser";

export default {
  components: {
    PageHeader,
    Head,
    Logo,
    Barcode,
    Qrcode,
    Button,
  },
  // layout: BreezeAuthenticatedLayout,
  props: ["shipment"],
  language: Object,
  data() {
    return {
      size: 120,
    };
  },
  methods: {
    print() {
      window.print();
    },
  },
};
</script>

<style>
@media print {
  .no-print {
    display: none;
  }

  @page {
    margin: 2mm;
  }
}
</style>

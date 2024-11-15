<template>
  <Head :title="language.Finance" />

  <div>
    <page-header>
      <div class="flex items-center justify-between">
        <span> {{ language.Finance }} </span>
        <div>
          <select-input v-model="form.type" :selectPlaceholder="language.filter">
            <option :value="null">{{ language.quarterly }}</option>
            <option value="monthly">{{ language.monthly }}</option>
            <option value="annualy">{{ language.annual }}</option>
          </select-input>
        </div>
      </div>
    </page-header>

    <Section v-for="(year, index) in years" :key="index" class="m-4 lg:m-7">
      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.year }} {{ year[0].year }}
          </h2>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th class="pb-4 pt-6 px-6">{{ language.number_of_shipments }}</th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.Total_income_inclusive_of_tax }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.taxes }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.Net_Income }}</th>
                    <th class="pb-4 pt-6 px-6">
                      <span v-if="period == 'monthly'">{{ language.Mounth }}</span>
                      <span v-else>{{ language.period }}</span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(quarter, i) in year"
                    :key="i"
                    class="hover:bg-gray-100 whitespace-nowrap text-center"
                  >
                    <td class="border-t px-6 py-4">
                      {{ quarter.count }}
                    </td>
                    <td class="border-t text-center">
                      {{ quarter.profit }}
                    </td>
                    <td class="border-t text-center">
                      {{ quarter.tax }}
                    </td>
                    <td class="border-t text-center">
                      {{ (quarter.profit - quarter.tax).toFixed(2) }}
                      <span v-if="year.length - 1 === i && years.length - 1 != index"
                        >({{ language.unfinished }})</span
                      >
                      <span v-else>({{ language.finished }})</span>
                    </td>
                    <td class="border-t text-center">
                      <span v-if="period == 'monthly'">{{ quarter.month }}</span>
                      <span v-else-if="period == 'annualy'"
                        >{{ language.year }}: {{ year[0].year }}</span
                      >
                      <span v-else>{{ quarters[quarter.quarter] }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </Section>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import SearchFilter from "@/Components/SearchFilter.vue";
import SelectInput from "@/Components/SelectInput.vue";
import Pagination from "@/Components/Pagination.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue";
import Section from "@/Components/Section.vue";

import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";

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
    Modal,
    Section,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    years: Object,
    period: String,
    language: Object,
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/financials", pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
        });
      }, 150),
    },
  },
  data() {
    return {
      quarters: {
        1: "الربع الأول",
        2: "الربع الثاني",
        3: "الربع الثالث",
        4: "الربع الرابع",
      },
      form: this.$inertia.form({
        type: this.period,
      }),
    };
  },
};
</script>

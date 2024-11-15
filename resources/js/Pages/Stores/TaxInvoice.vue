<template>
  <Head :title="language.tax_invoice" />

  <div>
    <page-header>{{ language.tax_invoice }}</page-header>

    <FlashMessages class="md:m-7 m-4" />

    <Section title="" class="md:m-7 m-4">
      <form @submit.prevent="submit">
        <Label
          v-if="$page.props.auth.account != 'client'"
          :value="language.store"
          class="mb-2"
        />
        <!-- <v-select
          v-if="$page.props.auth.account != 'client'"
          v-model="form.store"
          :reduce="(store) => store.value"
          :options="options"
        ></v-select>
        <InputError
          v-if="$page.props.auth.account != 'client'"
          :message="form.errors.store"
        /> -->

        <searchableSelect
            v-if="$page.props.auth.account != 'client'"
            :myOptions="computedOptions"
            :myVModel="form.store"
            :my_Place_holder="$page.props.language.Store_name"
            :my_Place_holder_when_Nothing_Found="$page.props.language.no_stores"
            @update:myVModel="form.store = $event"
          />
        <InputError
          v-if="$page.props.auth.account != 'client'"
          :message="form.errors.store"
        />

        <Label :value="language.from" class="mt-4" />
        <Input
          id="search"
          type="date"
          class="w-full mt-2"
          v-model="form.from"
          :error="form.errors.from"
        ></Input>
        <InputError :message="form.errors.from" />

        <Label :value="language.to" class="mt-4" />
        <Input
          id="search"
          type="date"
          class="w-full mt-2"
          v-model="form.to"
          :error="form.errors.to"
        ></Input>
        <InputError :message="form.errors.to" />

        <Button class="mt-4">{{ language.search }}</Button>
      </form>
    </Section>

    <div
      v-if="items"
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white md:m-7 m-4 overflow-hidden"
    >
      <!-- ***************** -->

      <div class="flex justify-between items-baseline">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
          {{ language.data }}
        </h2>
        <form :action="route('stores.invoice.pdf')" method="post">
          <input type="hidden" name="_token" :value="$page.props.csrf" />
          <input type="hidden" name="from" v-model="form.from" />
          <input type="hidden" name="to" v-model="form.to" />
          <input type="hidden" name="store" v-model="form.store" />
          <Button
            for="from"
            class="bg-gray-200 !text-gray-600 py-2 hover:!text-gray-200 text-sm"
          >
            {{ language.download_the_invoice }}
          </Button>
        </form>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th class="pb-4 pt-6 px-6">
                      {{ language.Shipment_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.delivery_date }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.service_type }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.price }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.tax_rate }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.tax }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.total_with_tax }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.cod }}
                    </th>

                    <th class="pb-4 pt-6 px-6">
                      {{ language.shipment_status }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, i) in items"
                    :key="i"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center"
                  >
                    <td class="border-t">
                      <Link
                        v-if="item.number"
                        class="flex items-center px-6 py-4 focus:text-indigo-500"
                        :href="'#'"
                      >
                        {{ item.number }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link class="flex items-center px-6 py-4" tabindex="-1">
                        {{ item.shipping_date }}
                      </Link>
                    </td>

                    <td class="border-t px-4">
                      {{ language.shipping }}
                    </td>

                    <td class="border-t px-4">
                      {{ parseFloat(item.fees - item.tax).toFixed(2) }}
                    </td>

                    <td class="border-t px-4">15%</td>

                    <td class="border-t px-4">
                      {{ item.tax }}
                    </td>

                    <td class="border-t px-4">
                      {{ item.fees }}
                    </td>

                    <td class="border-t px-4">
                      <span v-if="item.cod === 0 && item.status == 100">
                        {{ language.pre_paid }}
                      </span>
                      <span v-else-if="item.status == -100">0</span>
                      <span v-else>{{ item.cod }}</span>
                    </td>

                    <td class="border-t whitespace-nowrap px-4">
                      <p
                        class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                        v-if="item.status == 100"
                      >
                        {{ language.delivered }}
                      </p>
                      <p
                        class="rounded-xl text-yellow-700 bg-yellow-200 py-1 px-4 w-fit text-xs"
                        v-else-if="item.status == -100"
                      >
                        {{ language.audit }}
                      </p>
                    </td>
                  </tr>
                  <tr v-if="items.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">{{ language.no_data }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-8">
              <div class="flex gap-8">
                <p class="font-bold">
                  {{ language.The_total_before_tax }} :
                  {{ (sum.fees / 1.15).toFixed(2) }} {{ language.riyal }}
                </p>
                <p class="">
                  {{ language.total_payment_upon_receipt }}: {{ sum.cod }}
                  {{ language.riyal }}
                </p>
              </div>

              <p class="font-bold mt-3">
                {{ language.The_total_before_tax }}:
                {{ (sum.fees - sum.fees / 1.15).toFixed(2) }} {{ language.riyal }}
              </p>
              <p class="font-bold mt-3">
                {{ language.Total_after_tax }} {{ sum.fees }} {{ language.riyal }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <!-- <pagination class="mt-6" :links="shipments.links" /> -->
    </div>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import Icon from "@/Components/Icon.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Section from "@/Components/Section.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import SelectInput from "@/Components/SelectInput.vue";
import searchableSelect from "../../Components/searchableSelect.vue";

export default{
  components: {
    Head,
    PageHeader,
    Button,
    Link,
    Pagination,
    Icon,
    Section,
    Input,
    FlashMessages,
    InputError,
    Label,
    SelectInput,
    searchableSelect
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    items: Object,
    stores: Object,
    sum: Object,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        store: "",
        from: "",
        to: "",
      }),
      options: this.stores,
    };
  },

  computed: {
    computedOptions: function () {
        let options = [];

            options = Object.values(this.options)
            .filter((option) => option.label !== null)
            .map((option) => option.label);

        return options;
    },
  },

  methods: {
    submit() {
      this.form.post(this.route("stores.getInvoice"), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    reset() {
      this.form = mapValues(this.form, () => null);
    },
  },
}
</script>


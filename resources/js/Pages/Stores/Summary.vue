<template>
  <Head :title="language.account_statement" />

  <div>
    <page-header> {{ language.account_statement }}</page-header>

    <FlashMessages class="md:m-7 m-4" />

    <Section title="" class="md:m-7 m-4">
      <form @submit.prevent="submit">
        <Label
          v-if="$page.props.auth.account != 'client'"
          :value="language.store"
          class="mb-2"
        />

        <searchableSelect
            v-if="$page.props.auth.account != 'client'"
            :myOptions="computedOptions"
            :myVModel="form.store"
            :my_Place_holder="$page.props.language.store_name_or_number"
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

        <!-- <Label value="الشحنات" class="mt-4"/>
                <SelectInput v-model="form.type" :error="form.errors.type" class="mt-2" >
                    <option value=">=">جميع الشحنات</option>
                    <option value="=">دفع مسبق</option>
                    <option value=">">دفع عند الإستلام</option>
                </SelectInput> -->
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
                    <span class="flex items-center">
                        {{ language.data }}
                        <span class="flex px-3 font-bold text-gray-600">
                            <Icon
                                name="shipment"
                                class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"
                            />
                            {{ items.length }}
                        </span>
                    </span>
                </h2>


                <div class="flex justify-end gap-3">
                    <form :action="route('stores.summary.pdf')" method="post">
          <input type="hidden" name="_token" :value="$page.props.csrf" />
          <input type="hidden" name="from" v-model="form.from" />
          <input type="hidden" name="to" v-model="form.to" />
          <input type="hidden" name="store" v-model="form.store" />
          <Button
            for="from"
            class="bg-gray-200 !text-white py-2 hover:!text-gray-200 text-sm"
          >
            {{ language.download_PDF }}
          </Button>
        </form>


        <form :action="route('stores.summary.exel')" method="post">
            <input type="hidden" name="_token" :value="$page.props.csrf" />
            <input type="hidden" name="from" v-model="form.from" />
            <input type="hidden" name="to" v-model="form.to" />
            <input type="hidden" name="store" v-model="form.store" />



            <Button
              for="from"
              class="bg-gray-200 !text-white py-2 hover:!text-gray-200 text-sm"
            >
              {{ language.download_excel_file }}
            </Button>
          </form>

</div>



      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th class="pb-4 pt-6 px-6">
                      {{ language.shipment_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bond_no }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.delivery_fee }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.paiement_when_recieving }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.cod_commission }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bond_value }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.date }}
                    </th>

                    <th class="pb-4 pt-6 px-6">
                      {{ language.shipment_status }}
                    </th>

                    <th class="pb-4 pt-6 px-6">
                      {{ language.Statement }}
                    </th>

                    <th class="pb-4 pt-6 px-6">
                      {{ language.the_total }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.total }}
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
                      <Link
                        v-if="item.amount"
                        class="flex items-center px-6 py-4"
                        :href="route('vouchers.show', item.id)"
                        tabindex="-1"
                      >
                        {{ item.voucherNumber }}
                      </Link>
                    </td>

                    <td class="border-t px-4">
                      <span v-if="item.fees != null">{{ item.fees - item.codFee }}</span>
                    </td>
                    <td class="border-t px-4">
                      <span v-if="item.cod === 0 && item.status == 100">{{
                        language.pre_paid
                      }}</span>
                      <span v-else-if="item.status == -100">0</span>
                      <span v-else>{{ item.cod }}</span>
                    </td>
                    <td class="border-t px-4">
                      {{ item.codFee }}
                    </td>
                    <td class="border-t px-4">
                      {{ item.amount }}
                    </td>
                    <td class="border-t px-4">
                      {{ item.date }}
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

                    <td class="border-t px-4">
                      {{ item.notice }}
                    </td>

                    <td class="border-t px-4">
                      <span v-if="!isNaN(item.fees - item.cod)">
                        {{ item.fees - item.cod }}
                      </span>
                    </td>

                    <td class="border-t px-4">
                      {{ item.sum }}
                    </td>
                  </tr>
                  <tr v-if="items.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">{{ language.no_data }}</td>
                  </tr>
                </tbody>
              </table>
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
export default {
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
    searchableSelect,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    items: Object,
    stores: Object,
    prevSum: Number,
    language: Object,
  },
  computed: {
    computedOptions: function () {
  let options = [];

    options = Object.values(this.stores)
      .filter((option) => option.label !== null)
      .map((option) => option.label);

  return options;
},
  },
  data() {
    return {
      form: this.$inertia.form({
        store: "",
        from: "",
        to: "",
      }),
      options: this.stores,
      sum: 0,
    };
  },

  watch: {
    items: function () {
      this.items.forEach((item, index) => {
        if (index == 0) this.sum = this.prevSum;
        if (isNaN(item.fees - item.cod)) {
          this.sum = parseFloat(this.sum) + parseFloat(item.amount);
          item.sum = this.sum;
        } else {
          this.sum = parseFloat(this.sum) + parseFloat(item.fees - item.cod);
          item.sum = this.sum;
        }
      });
    },
  },

  methods: {
    submit() {
      this.form.post(this.route("stores.getSummary"), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    reset() {
      this.form = mapValues(this.form, () => null);
    },
  },
};
</script>

<style>
.v-select {
  direction: rtl;
}

.vs__clear {
  margin-left: 8px;
}
.vs__open-indicator {
  margin-left: 10px;
}

.vs__dropdown-toggle {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}
</style>

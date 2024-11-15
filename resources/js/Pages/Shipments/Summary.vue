<template>
  <Head :title="language.summary_jnt" />
  <div>
    <page-header>
      <span v-if="jnt == true">{{ language.summary_jnt }}</span>
      <span v-else>{{ language.Shipment_statement }}</span>
    </page-header>
    <FlashMessages class="md:m-7 m-4" />
    <Section title="" class="md:m-7 m-4">
      <form @submit.prevent="submit">
        <Label :value="language.from" />
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

        <div v-if="jnt != true">
          <Label :value="language.shipments" class="mt-4" />

          <div >
            <div
              @click="
                (showDropdown = !showDropdown), (open_receipt_options = false)
              "
            >
              <div class="relative flex items-center">
                <Input
                  type="text"
                  class="pl-8 mt-4 block w-full mb-4"
                  disabled
                  :placeholder="getPlaceholder()"
                />
                <icon
                  name="cheveron-down"
                  class="block w-5 h-5 text-gray-300 absolute left-2"
                  style="top: 50%; transform: translateY(-50%)"
                />
              </div>
            </div>

            <div
              v-if="showDropdown"
              class="mt-2 bg-white border border-gray-300 shadow-lg rounded-md w-full"
            >
              <ul>
                <li
                  @click="
                    (form.type = '>='),
                      (open_receipt_options = false),
                      (showDropdown = false)
                  "
                  class="py-2 hover:bg-blue-500 hover:text-white text-gray-800"
                >
                  <button type="button">
                    {{ language.all_shipments }}
                  </button>
                </li>
                <li
                  @click="
                    (form.type = '='),
                      (open_receipt_options = false),
                      (showDropdown = false);
                    showDropdown = false;
                  "
                  class="py-2 hover:bg-blue-500 hover:text-white text-gray-800"
                >
                  <button type="button">
                    {{ language.pre_paid }}
                  </button>
                </li>
                <li
                  @click="
                    (form.type = '>'),
                      (open_receipt_options = !open_receipt_options)
                  "
                  class="py-2 hover:bg-blue-500 hover:text-white text-gray-800"
                >
                  <button type="button">
                    {{ language.pay_on_receipt }}
                    <icon
                      name="cheveron-down"
                      class="inline-block w-4 h-4 ml-1 text-gray-300 align-middle"
                    />
                  </button>
                </li>
              </ul>
            </div>

            <div
              v-if="open_receipt_options"
              class="mt-2 bg-white border border-gray-300 shadow-lg rounded-md w-full"
            >
              <ul>
                <li
                  @click="
                    (showDropdown = false),
                      (open_receipt_options = false),
                      (form.receipt_type = 'all')
                  "
                  class="py-2 hover:bg-blue-500 hover:text-white text-gray-800"
                >
                  <button type="button">
                    {{ language.all }}
                  </button>
                </li>
                <li
                  @click="
                    (showDropdown = false),
                      (open_receipt_options = false),
                      (form.receipt_type = 'cash')
                  "
                  class="py-2 hover:bg-blue-500 hover:text-white text-gray-800"
                >
                  <button type="button">
                    {{ language.cash }}
                  </button>
                </li>
                <li
                  @click="
                    (showDropdown = false),
                      (open_receipt_options = false),
                      (form.receipt_type = 'epayment')
                  "
                  class="py-2 hover:bg-blue-500 hover:text-white text-gray-800"
                >
                  <button type="button">
                    {{ language.network }}
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <Button class="mt-4">{{ language.search }}</Button>
      </form>
    </Section>

    <div
      v-if="shipments"
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white md:m-7 m-4 overflow-hidden"
    >
      <!-- ***************** -->

      <div class="flex justify-between items-baseline">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
          <span class="flex items-center">
            {{ language.shipments }}
            <span class="flex px-3 font-bold text-gray-600">
              <Icon
                name="shipment"
                class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"
              />
              {{ shipments.total }}
            </span>
          </span>
        </h2>
     <div  class="flex justify-end gap-3">
        <form
          :action="route('shipments.exel.summary')"
          method="post"
          v-if="jnt != true"
        >
          <input type="hidden" name="_token" :value="$page.props.csrf" />
          <input type="hidden" name="from" v-model="form.from" />
          <input type="hidden" name="to" v-model="form.to" />
          <input type="hidden" name="type" v-model="form.type" />
          <input type="hidden" name="receipt_type" v-model="form.receipt_type" />



          <Button
            for="from"
            class="bg-gray-200 !text-white py-2 hover:!text-gray-200 text-sm"
          >
            {{ language.download_excel_file }}
          </Button>
        </form>
        <form
          v-if="jnt != true"
          :action="route('shipments.pdf.summary')"
          method="post"
        >
          <input type="hidden" name="_token" :value="$page.props.csrf" />
          <input type="hidden" name="from" v-model="form.from" />
          <input type="hidden" name="to" v-model="form.to" />
          <input type="hidden" name="type" v-model="form.type" />
          <input
            type="hidden"
            name="receipt_type"
            v-model="form.receipt_type"
          />

          <Button
            for="from"
            class="bg-gray-200 !text-white py-2 hover:!text-gray-200 text-sm"
          >
            {{ language.download_PDF }}
          </Button>
        </form>

        <form v-else :action="route('shipments.jnt.excel')" method="post">
          <input type="hidden" name="_token" :value="$page.props.csrf" />
          <input type="hidden" name="from" v-model="form.from" />
          <input type="hidden" name="to" v-model="form.to" />
          <!-- <input type="hidden" name="type" v-model="form.type" /> -->
          <input
            type="hidden"
            name="receipt_type"
            v-model="form.receipt_type"
          />

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
                      {{ language.Shipment_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6" v-if="jnt == true">
                      {{ language.jnt_bill_code }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bill_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.delivery }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.shipment_type }}
                    </th>
                    <th
                      class="pb-4 pt-6 px-6"
                      v-if="
                        $page.props.auth.permissions.includes(
                          'fee_shipments'
                        ) || $page.props.auth.account != 'employee'
                      "
                    >
                      {{ language.delivery_fee }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.paiement_when_recieving }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.sender_name }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.The_recipients_name }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.mobile_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.city_neighborhood }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.Status }}
                    </th>
                    <!--<th class="pb-4 pt-6 px-6">
                                            {{ language.storage_place }}
                                        </th>-->
                    <th class="pb-4 pt-6 px-6" colspan="2"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="shipment in shipments.data"
                    :key="shipment.id"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                  >
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4 focus:text-indigo-500"
                        :href="route('shipments.show', shipment.id)"
                      >
                        {{ shipment.number }}
                      </Link>
                    </td>

                    <td class="border-t" v-if="jnt == true">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.billCode }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <a
                        target="_blank"
                        class="flex items-center px-6 py-4 focus:text-indigo-500"
                        :href="route('shipments.invoice.pdf', shipment.number)"
                      >
                        SA{{ shipment.invoice }}
                      </a>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.updated_at }}
                      </Link>
                    </td>

                    <td class="border-t px-6">
                      <span v-if="shipment.return_back == 1">{{
                        language.redux
                      }}</span>
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
                        $page.props.auth.permissions.includes(
                          'fee_shipments'
                        ) || $page.props.auth.account != 'employee'
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
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.storeName }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.receiverName }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.receiverPhone }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.city }} ({{ shipment.neighborhood }})
                      </Link>
                    </td>
                    <td class="border-t whitespace-nowrap">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        <p
                          class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                          v-if="shipment.status == 100"
                        >
                          {{ language.delivered }}
                        </p>
                        <p
                          class="rounded-xl text-yellow-700 bg-yellow-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == -100"
                        >
                          {{ language.audit }}
                        </p>
                      </Link>
                    </td>
                    <!--<td class="border-t">
                                            <Link
                                                class="flex items-center px-6 py-4"
                                                :href="route('shipments.show', shipment.id)"
                                                tabindex="-1"
                                            >
                                                {{ shipment.warehouseLocation }}
                                            </Link>
                                        </td>-->
                    <td
                      class="w-px border-t"
                      v-if="
                        $page.props.auth.account == 'admin' ||
                        $page.props.auth.permissions.includes('edit_shipments')
                      "
                    >
                      <Link
                        class="flex items-center px-4"
                        :href="route('shipments.edit', shipment)"
                        tabindex="-1"
                      >
                        <icon name="edit" class="block w-5 h-5 text-gray-500" />
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
import Pagination from "@/Components/Pagination.vue";
import Icon from "@/Components/Icon.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Section from "@/Components/Section.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import SelectInput from "@/Components/SelectInput.vue";

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
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    language: Object,
    shipments: Object,
    from: String,
    to: String,
    type: { type: String, default: ">=" },
    receipt_type: { type: String, default: "all" },
    jnt: Boolean,
  },

  data() {
    return {
      showDropdown: false,
      open_receipt_options: false,
      form: this.$inertia.form({
        from: this.from,
        to: this.to,
        type: this.type,
        receipt_type: this.receipt_type,
      }),
    };
  },

  methods: {
    getPlaceholder() {
      if (this.form.type === ">=") {
        return this.language.all_shipments;
      }
      if (this.form.type === "=") {
        return this.language.pre_paid;
      }
      if (this.form.type === ">") {
        if (this.form.receipt_type === "cash") {
          return `${this.language.pay_on_receipt}: ${this.language.cash}`;
        } else if (this.form.receipt_type === "epayment") {
          return `${this.language.pay_on_receipt}: ${this.language.network}`;
        } else if (this.form.receipt_type === "all") {
          return `${this.language.pay_on_receipt}: ${this.language.all}`;
        } else {
          return `${this.language.pay_on_receipt}`;
        }
      }
    },
    submit() {
      this.form.get(this.route("shipments.getSummary", { jnt: this.jnt }), {
        preserveScroll: true,
      });
    },
    reset() {
      this.form = mapValues(this.form, () => null);
    },
  },
};
</script>

<style scoped>
ul li {
  padding: 7px 10px;
}
</style>

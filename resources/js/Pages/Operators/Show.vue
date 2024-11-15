<template>
  <div>
    <Head :title="language.delegate_file" />

    <page-header>
      <p>{{ language.delegate_file }} ({{ operator.name }})</p>

      <form
        :action="route('operator.shipments_pdf', operator.id)"
        method="post"
        id="form"
      >
        <input type="hidden" name="_token" :value="$page.props.csrf" />
        <Button
          class="md:absolute bottom-0"
          :class="{
            'md:left-7': $page.props.locale == 'ar',
            'md:right-7': $page.props.locale == 'en',
          }"
        >
          {{ language.download_shipments_registered_with_delegate_s_name }}
        </Button>
      </form>
    </page-header>

    <flash-messages class="md:m-7 m-4" />

    <div class="grid md:gap-7 gap-4 md:grid-cols-2 md:m-7 m-4">
      <!-- Create new shipment -->
      <Section :title="language.statistics">
        <div class="flex justify-between w-full border-b border-gray-200 mt-4 p-2 pb-4">
          <p>{{ language.the_number_of_shipments_received }}</p>
          <p v-html="stats.primary.all"></p>
        </div>
        <div class="flex justify-between w-full border-b border-gray-200 mt-4 p-2 pb-4">
          <p>{{ language.number_of_received_shipments }}</p>
          <p v-html="stats.primary.completed"></p>
        </div>
        <div class="flex justify-between w-full border-b border-gray-200 mt-4 p-2 pb-4">
          <p>{{ language.the_number_of_returned_shipments }}</p>
          <p v-html="stats.primary.returned"></p>
        </div>
        <div class="flex justify-between w-full border-b border-gray-200 mt-4 p-2 pb-4">
          <p>{{ language.total_covenants }}</p>
          <p v-html="stats.primary.custodies"></p>
        </div>
        <div
          class="flex justify-between w-full mt-4 p-2 font-semibold"
          :class="{
            'text-red-600': operator.dues > 0,
            'text-green-600': operator.dues == 0,
          }"
        >
          <p>{{ language.the_indebtedness }}</p>
          <p v-html="operator.dues"></p>
        </div>
      </Section>

      <Section class="">
        <div class="flex justify-between">
          <h2 class="text-gray-700 text-lg font-semibold">
            {{ language.statistics_today }}
          </h2>
          <Button
            :href="route('operator.reset', operator)"
            method="post"
            class="!bg-gray-200 !text-gray-600 float-left"
            >{{ language.whistle }}</Button
          >
        </div>

        <div class="flex justify-between w-full border-b border-gray-200 mt-4 p-2 pb-4">
          <p>{{ language.Number_of_delivered_shipments }}</p>
          <p v-html="stats.daily.all"></p>
        </div>

        <Link
          :href="route('operator.shipments', { operator: operator, method: 'prepaid' })"
          class="flex justify-between w-full border-b border-gray-200 mt-4 p-2 pb-4"
        >
          <p>{{ language.prepaid_shipments }}</p>
          <p v-html="stats.daily.paid"></p>
        </Link>

        <Link
          :href="route('operator.shipments', { operator: operator, method: 'cash' })"
          class="flex justify-between w-full border-b border-gray-200 mt-4 p-2 pb-4"
        >
          <p>{{ language.payment_shipments_upon_receipt_cash }}</p>
          <p v-html="stats.daily.cash"></p>
        </Link>

        <Link
          :href="route('operator.shipments', { operator: operator, method: 'epayment' })"
          class="flex justify-between w-full mt-4 p-2 pb-4"
        >
          <p>{{ language.payment_on_receipt_shipments_network }}</p>
          <p v-html="stats.daily.epayment"></p>
        </Link>
      </Section>
    </div>

    <Section class="md:m-7 m-4">
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
          ></search-filter>
        </div>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full">
          <thead class="border-b bg-gray-100 text-sm">
            <tr class="whitespace-nowrap">
              <th class="pb-4 pt-6 px-6">{{ language.Shipment_number }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.delivery_date }}</th>

              <th class="pb-4 pt-6 px-6">{{ language.Status }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.fees }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.cod }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="shipment in shipments.data"
              :key="shipment.id"
              class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center"
            >
              <td class="border-t">
                <Link
                  class="px-6 py-4 focus:text-indigo-500"
                  :href="route('shipments.show', shipment.id)"
                >
                  {{ shipment.number }}
                  <!--<icon v-if="contact.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />-->
                </Link>
              </td>
              <td class="border-t">
                <Link
                  class="px-6 py-4"
                  :href="route('shipments.show', shipment.id)"
                  tabindex="-1"
                >
                  {{ shipment.shipping_date }}
                </Link>
              </td>

              <td class="border-t">
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
              </td>

              <td class="border-t">
                <Link
                  class="px-6 py-4"
                  :href="route('shipments.show', shipment.id)"
                  tabindex="-1"
                >
                  {{ shipment.fees }}
                </Link>
              </td>

              <td class="border-t">
                <Link
                  class="flex items-center justify-center px-6 py-4"
                  :href="route('shipments.show', shipment.id)"
                  tabindex="-1"
                >
                  <span v-if="!shipment.cod">{{ language.pre_paid }}</span>
                  <span v-else>{{ shipment.cod }}</span>
                </Link>
              </td>
            </tr>
            <tr v-if="shipments.data.length === 0">
              <td class="px-6 py-4 border-t" colspan="4">{{ language.no_shipments }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </Section>

    <Section class="md:m-7 m-4">
      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
            <h2 class="text-gray-700 text-lg mb-4 font-semibold">
  <span class="flex items-center">
    {{ language.shipments_received }}
    <span class="flex px-3 font-bold text-gray-600">
      <Icon name="shipment" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1" />
      {{ receivedShipments.total }}
    </span>
  </span>
</h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <!-- <search-filter
                        v-model="form.search"
                        class="mr-4 w-full max-w-md"
                        @reset="reset"
                    ></search-filter> -->
        </div>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full">
          <thead class="border-b bg-gray-100 text-sm">
            <tr class="whitespace-nowrap">
              <th class="pb-4 pt-6 px-6">{{ language.Shipment_number }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.delivery_date }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.Status }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.fees }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.paiement_when_recieving }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="shipment in receivedShipments.data"
              :key="shipment.id"
              class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center"
            >
              <td class="border-t">
                <Link
                  class="px-6 py-4 focus:text-indigo-500"
                  :href="route('shipments.show', shipment.id)"
                >
                  {{ shipment.number }}
                  <!--<icon v-if="contact.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />-->
                </Link>
              </td>
              <td class="border-t">
                <Link
                  class="px-6 py-4"
                  :href="route('shipments.show', shipment.id)"
                  tabindex="-1"
                >
                  {{ shipment.shipping_date }}
                </Link>
              </td>

              <td class="border-t">
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
                  {{ language.preparing }}
                </p>
                <p
                  class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                  v-else-if="shipment.status == 65"
                >
                  {{ language.out_to_deliver }}
                </p>
              </td>

              <td class="border-t">
                <Link
                  class="px-6 py-4"
                  :href="route('shipments.show', shipment.id)"
                  tabindex="-1"
                >
                  {{ shipment.fees }}
                </Link>
              </td>

              <td class="border-t">
                <Link
                  class="flex items-center justify-center px-6 py-4"
                  :href="route('shipments.show', shipment.id)"
                  tabindex="-1"
                >
                  <span v-if="!shipment.cod">دفع مسبق</span>
                  <span v-else>{{ shipment.cod }}</span>
                </Link>
              </td>
            </tr>
            <tr v-if="receivedShipments.data.length === 0">
              <td class="px-6 py-4 border-t" colspan="4">{{ language.no_shipments }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </Section>

    <Section class="md:m-7 m-4">
      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.receipts_of_debts }}
          </h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
          </search-filter>
          <Button
            v-if="
              $page.props.auth.account == 'admin' ||
              $page.props.auth.permissions.includes('create_voucher_operators')
            "
            type="button"
            @click="showAddVoucher = !showAddVoucher"
            class="!bg-gray-200 !text-gray-600 border-0 mr-2 whitespace-nowrap"
            >{{ language.add_payment }}
          </Button>
        </div>
      </div>

      <div
        v-show="showAddVoucher"
        class="mb-7"
        v-if="
          $page.props.auth.account == 'admin' ||
          $page.props.auth.permissions.includes('create_voucher_operators')
        "
      >
        <h3 class="text-base font-semibold text-gray-600">{{ language.add_payment }}</h3>
        <form
          @submit.prevent="submitAddVoucherForm"
          class="mt-2 flex gap-4 md:flex-row flex-col"
        >
          <Input
            id="amount"
            type="number"
            step="0.01"
            v-model="formAddVoucher.amount"
            :error="formAddVoucher.errors.amount"
            class="block w-full"
            :placeholder="language.the_amount"
          />
          <InputError :message="formAddVoucher.errors.amount" />
          <Input
            id="name"
            type="text"
            v-model="formAddVoucher.notice"
            :error="formAddVoucher.errors.notice"
            class="block w-full"
            :placeholder="language.notes"
          />
          <InputError :message="formAddVoucher.errors.notice" />
          <Button class="whitespace-nowrap justify-center">
            {{ language.add_payment }}</Button
          >
        </form>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full">
          <thead class="border-b bg-gray-100 text-sm">
            <tr class="whitespace-nowrap">
              <th class="pb-4 pt-6 px-6">#</th>
              <th class="pb-4 pt-6 px-6">{{ language.the_amount }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.date }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.employee }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.notes }}</th>
              <th
                class="pb-4 pt-6 px-6"
                v-if="
                  $page.props.auth.account == 'admin' ||
                  $page.props.auth.permissions.includes('edit_voucher_operators')
                "
              ></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="voucher in vouchers.data"
              :key="voucher.id"
              class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center"
            >
              <td class="border-t py-4">
                {{ voucher.number }}
              </td>
              <td class="border-t">
                {{ voucher.amount }}
              </td>
              <td class="border-t">
                {{ voucher.date }}
              </td>
              <td class="border-t">
                {{ voucher.employee }}
              </td>
              <td class="border-t">
                {{ voucher.notice }}
              </td>

              <td
                class="border-t"
                v-if="
                  $page.props.auth.account == 'admin' ||
                  $page.props.auth.permissions.includes('edit_voucher_operators')
                "
              >
                <div class="flex justify-center">
                  <div class="flex justify-center px-2" @click="editVoucher(voucher)">
                    <icon name="edit" class="block w-5 h-5 text-gray-500" />
                  </div>

                  <div class="flex justify-center px-2" @click="deleteVoucher(voucher)">
                    <icon name="trash" class="block w-5 h-5 text-gray-500" />
                  </div>
                  <Link
                    class="flex justify-center px-2"
                    :href="route('vouchers.show', voucher)"
                  >
                    <icon name="eye" class="block w-5 h-5 text-gray-500" />
                  </Link>
                </div>
              </td>
            </tr>
            <tr v-if="vouchers.data.length === 0">
              <td class="px-6 py-4 border-t" colspan="4">
                {{ language.there_are_no_receipts }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </Section>

    <Section class="md:m-7 m-4">
      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.covenants }}
          </h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
          </search-filter>
          <Button
            v-if="
              $page.props.auth.account == 'admin' ||
              $page.props.auth.permissions.includes('create_custody_operators')
            "
            type="button"
            @click="showAddCustody = !showAddCustody"
            class="!bg-gray-200 !text-gray-600 border-0 mr-2 whitespace-nowrap"
          >
            {{ language.add_the_covenant }}
          </Button>
        </div>
      </div>

      <div
        v-show="showAddCustody"
        class="mb-7"
        v-if="
          $page.props.auth.account == 'admin' ||
          $page.props.auth.permissions.includes('create_custody_operators')
        "
      >
        <h3 class="text-base font-semibold text-gray-600">{{ language.add }}</h3>
        <form
          @submit.prevent="submitAddCustodyForm"
          class="mt-2 flex gap-4 md:flex-row flex-col"
        >
          <Input
            id="amount"
            type="number"
            step="0.01"
            v-model="formAddCustody.amount"
            :error="formAddCustody.errors.amount"
            class="block w-full"
            :placeholder="language.the_amount"
          />
          <InputError :message="formAddCustody.errors.amount" />
          <Input
            id="name"
            type="text"
            v-model="formAddCustody.notice"
            :error="formAddCustody.errors.notice"
            class="block w-full"
            :placeholder="language.notes"
          />
          <InputError :message="formAddCustody.errors.notice" />
          <Button class="whitespace-nowrap justify-center"
            >{{ language.add_the_covenant }}
          </Button>
        </form>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full">
          <thead class="border-b bg-gray-100 text-sm">
            <tr class="whitespace-nowrap">
              <th class="pb-4 pt-6 px-6">#</th>
              <th class="pb-4 pt-6 px-6">{{ language.the_amount }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.date }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.notes }}</th>
              <th
                class="pb-4 pt-6 px-6"
                v-if="
                  $page.props.auth.account == 'admin' ||
                  $page.props.auth.permissions.includes('edit_custody_operators')
                "
              ></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="custody in custodies.data"
              :key="custody.id"
              class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center"
            >
              <td class="border-t py-4">
                {{ custody.number }}
              </td>
              <td class="border-t">
                {{ custody.amount }}
              </td>
              <td class="border-t">
                {{ custody.date }}
              </td>
              <td class="border-t">
                {{ custody.notice }}
              </td>

              <td
                v-if="
                  $page.props.auth.account == 'admin' ||
                  $page.props.auth.permissions.includes('edit_custody_operators')
                "
                class="border-t"
              >
                <div class="flex justify-center">
                  <div class="flex justify-center px-2" @click="editCustody(custody)">
                    <icon name="edit" class="block w-5 h-5 text-gray-500" />
                  </div>

                  <div class="flex justify-center px-2" @click="deleteCustody(custody)">
                    <icon name="trash" class="block w-5 h-5 text-gray-500" />
                  </div>
                </div>
              </td>
            </tr>
            <tr v-if="custodies.data.length === 0">
              <td class="px-6 py-4 border-t" colspan="4">
                {{ language.there_are_no_covenants }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </Section>

    <Modal :show="showDeleteVoucherAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.are_you_sure }}
      </h2>
      <p class="text-base text-gray-600">
        {{ language.do_you_want_to_confirm_deletion_of_the_receipt }}
      </p>

      <Button
        class="mt-4 !border"
        :href="deleteVoucherLink"
        method="post"
        @click="showDeleteVoucherAlert = false"
      >
        <span>{{ language.delete }}</span>
      </Button>
      <Button
        @click="showDeleteVoucherAlert = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>

    <Modal :show="showEditVoucherModel">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.modify_the_receipt }}
      </h2>

      <form @submit.prevent="submitEditVoucherForm" class="w-full">
        <Input
          id="amount"
          type="number"
          step="0.01"
          v-model="formEditVoucher.amount"
          :error="formEditVoucher.errors.amount"
          class="block w-full"
          :placeholder="language.the_amount"
        />
        <InputError :message="formEditVoucher.errors.amount" />
        <Input
          id="name"
          type="text"
          v-model="formEditVoucher.notice"
          :error="formEditVoucher.errors.notice"
          class="block w-full mt-3"
          :placeholder="language.notes"
        />
        <InputError :message="formEditVoucher.errors.notice" />
        <Button class="mt-4 !border"> {{ language.modify }} </Button>
        <Button
          @click="showEditVoucherModel = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ language.cancel }}</Button
        >
      </form>
    </Modal>

    <Modal :show="showDeleteCustodyAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.are_you_sure }}
      </h2>
      <p class="text-base text-gray-600">
        {{ language.do_you_want_to_confirm_the_deletion_of_the_custody }}
      </p>

      <Button
        class="mt-4 !border"
        :href="deleteCustodyLink"
        method="post"
        @click="showDeleteCustodyAlert = false"
      >
        <span>{{ language.delete }}</span>
      </Button>
      <Button
        @click="showDeleteCustodyAlert = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>

    <Modal :show="showEditCustodyModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.modify_the_receipt }}
      </h2>

      <form @submit.prevent="submitEditCustodyForm" class="w-full">
        <Input
          id="amount"
          type="number"
          step="0.01"
          v-model="formEditCustody.amount"
          :error="formEditCustody.errors.amount"
          class="block w-full"
          :placeholder="language.the_amount"
        />
        <InputError :message="formEditCustody.errors.amount" />
        <Input
          id="name"
          type="text"
          v-model="formEditCustody.notice"
          :error="formEditCustody.errors.notice"
          class="block w-full mt-3"
          :placeholder="language.notes"
        />
        <InputError :message="formEditCustody.errors.notice" />
        <Button class="mt-4 !border"> {{ language.modify }} </Button>
        <Button
          @click="showEditCustodyModal = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ language.cancel }}</Button
        >
      </form>
    </Modal>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Section from "@/Components/Section.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import SearchFilter from "@/Components/SearchFilter.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";

export default {
  components: {
    Head,
    Link,
    PageHeader,
    Button,
    Input,
    Section,
    BreezeValidationErrors,
    FlashMessages,
    SelectInput,
    InputError,
    SearchFilter,
    Modal,
    Icon,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    operator: Object,
    stats: Object,
    shipments: Object,
    receivedShipments: Object,
    vouchers: Object,
    custodies: Object,
    filters: Object,
    language: Object,
  },

  data() {
    return {
      form: {
        search: this.filters.search,
        status: this.filters.status,
      },
      formAddVoucher: this.$inertia.form({
        amount: "",
        notice: "",
      }),
      formEditVoucher: this.$inertia.form({
        amount: "",
        notice: "",
      }),
      formAddCustody: this.$inertia.form({
        amount: "",
        notice: "",
      }),
      formEditCustody: this.$inertia.form({
        amount: "",
        notice: "",
      }),
      showAddVoucher: false,
      showAddCustody: false,
      showDeleteVoucherAlert: false,
      deleteVoucherLink: "",
      showEditVoucherModel: false,
      targetVoucher: {},
      showDeleteCustodyAlert: false,
      deleteCustodyLink: "",
      showEditCustodyModal: false,
      targetCustody: {},
    };
  },

  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get(
          `/dashboard/operators/${this.operator.id}/show`,
          pickBy(this.form),
          { preserveState: true, preserveScroll: true }
        );
      }, 150),
    },
  },

  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    submitAddVoucherForm() {
      this.formAddVoucher.post(this.route("vouchers.store", this.operator.user_id), {
        preserveScroll: true,
        onSuccess: () => this.formAddVoucher.reset(),
      });
    },
    deleteVoucher(voucher) {
      this.deleteVoucherLink = route("vouchers.delete", voucher);
      this.showDeleteVoucherAlert = true;
    },
    editVoucher(voucher) {
      this.editVoucherLink = route("vouchers.update", voucher);
      this.showEditVoucherModel = true;
      this.targetVoucher = voucher;
      this.formEditVoucher.amount = voucher.amount;
      this.formEditVoucher.notice = voucher.notice;
    },
    submitEditVoucherForm() {
      this.formEditVoucher.post(this.route("vouchers.update", this.targetVoucher.id), {
        preserveScroll: true,
      });
      this.showEditVoucherModel = false;
    },
    submitAddCustodyForm() {
      this.formAddCustody.post(this.route("custodies.store", this.operator.user_id), {
        preserveScroll: true,
        onSuccess: () => this.formAddCustody.reset(),
      });
    },
    deleteCustody(custody) {
      this.deleteCustodyLink = route("custodies.delete", custody);
      this.showDeleteCustodyAlert = true;
    },
    editCustody(custody) {
      this.showEditCustodyModal = true;
      this.targetCustody = custody;
      this.formEditCustody.amount = custody.amount;
      this.formEditCustody.notice = custody.notice;
    },
    submitEditCustodyForm() {
      this.formEditCustody.post(this.route("custodies.update", this.targetCustody.id), {
        preserveScroll: true,
      });
      this.showEditCustodyModal = false;
    },
  },

  computed: {
    csrf() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    },
  },
};
</script>

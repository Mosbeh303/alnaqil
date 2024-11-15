<template>
  <Head :title="language.commissions" />

  <div>
    <page-header>
      <span v-if="$page.props.auth.account == 'marketer'"> {{ language.commissions }} </span>
      <span v-else> {{ language.marketer_commissions }} ( <Link :href="route('marketers.show', marketer.id)" class="text-blue-500 font-semibold"> {{ marketer.name }}  </Link> ) </span>
    </page-header>

    <div
      class="px-5 py-6 bg-white rounded border border-gray-200 lg:m-7 m-4 overflow-hidden"
    >
      <form @submit.prevent="search">
        <div class="flex gap-4">
          <div class="flex-grow">
            <Label :value="language.from" class="mt-4" />
            <Input
              id="search"
              type="date"
              class="w-full mt-2"
              v-model="form.from"
              :error="form.errors.from"
            ></Input>
            <InputError :message="form.errors.from" />
          </div>

          <div class="flex-grow">
            <Label :value="language.to" class="mt-4" />
            <Input
              id="search"
              type="date"
              class="w-full mt-2"
              v-model="form.to"
              :error="form.errors.to"
            ></Input>
            <InputError :message="form.errors.to" />
          </div>
        </div>

        <div>
          <Label value="" class="mt-2" />
          <Button class="mt-4">{{ language.search }}</Button>
        </div>
      </form>
    </div>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white m-7 overflow-hidden"
    >
      <!-- ***************** -->

      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            <span>{{ language.commissions }}</span>
          </h2>

          <span class="flex px-3 font-bold text-gray-600"
            ><Icon
              name="shipment"
              class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"
            />
            {{ commissions.total }}
          </span>
        </div>

        <div class="flex items-center justify-between mb-6">
            <form :action="route('marketers.commissions.export', marketer.id)" method="get">
              <input type="hidden" name="_token" :value="$page.props.csrf" />
              <input type="hidden" name="from" v-model="form.from" />
              <input type="hidden" name="to" v-model="form.to" />
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
              <form
                :action="route('shipments.file')"
                method="post"
                id="formSelect"
              >
                <input type="hidden" name="_token" :value="$page.props.csrf" />
                <table class="min-w-full">
                  <thead class="border-b bg-gray-100 text-sm">
                    <tr class="whitespace-nowrap">
                      <th class="pb-4 pt-6 px-6">{{ language.store }}</th>
                      <th class="pb-4 pt-6 px-6">
                        {{ language.shipment_number }}
                      </th>
                      <th class="pb-4 pt-6 px-6">
                        {{ language.delivery_fee }}
                      </th>
                      <th class="pb-4 pt-6 px-6">{{ language.delivery }}</th>
                      <th class="pb-4 pt-6 px-6">
                        {{ language.commission }} ({{ language.riyal }})
                      </th>
                      <th class="pb-4 pt-6 px-6">{{ language.Status }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="commission in commissions.data"
                      :key="commission.id"
                      class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center py-2"
                    >
                      <td class="border-t py-2">
                        {{ commission.shipment.store }}
                      </td>
                      <td class="border-t py-2">
                        {{ commission.shipment.number }}
                      </td>
                      <td class="border-t px-4">
                        {{ commission.shipment.fees }}
                      </td>
                      <td class="border-t px-4">
                        {{ commission.shipment.shipping_date }}
                      </td>
                      <td class="border-t px-4">
                        {{ commission.amount }}
                      </td>

                      <td class="border-t px-4 text-center">
                        <p
                          class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                          v-if="commission.shipment.status == 100"
                        >
                          {{ language.delivered }}
                        </p>
                        <p
                          class="rounded-xl text-yellow-700 bg-yellow-200 py-1 px-4 w-fit text-xs"
                          v-else-if="commission.shipment.status == -100"
                        >
                          {{ language.audit }}
                        </p>
                      </td>
                    </tr>
                    <tr v-if="commissions.data.length === 0">
                      <td class="px-6 py-4 border-t" colspan="4">
                        {{ language.no_commissions }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </form>
            </div>
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="commissions.links" />

      <div class="mt-4">
        <span class="font-semibold">{{ language.total_commissions }} : </span> {{ totalAmount }}
      </div>

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
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";

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
    Input,
    InputError,
    Label,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    filters: Object,
    commissions: Object,
    to: String,
    from: String,
    language: Object,
    totalAmount: Number,
    marketer: Object
  },
  data() {
    return {
      form: this.$inertia.form({
        from: this.from,
        to: this.to,
      }),
    };
  },
  // watch: {
  //   form: {
  //     deep: true,
  //     handler: throttle(function () {
  //       if (this.store)
  //         this.$inertia.get(
  //           `/dashboard/stores/${this.store.id}/shipments`,
  //           pickBy(this.form),
  //           { preserveState: true, preserveScroll: true }
  //         );

  //       if (this.operator)
  //         this.$inertia.get(
  //           `/dashboard/operators/${this.operator.id}/shipments`,
  //           pickBy(this.form),
  //           { preserveState: true, preserveScroll: true }
  //         );
  //     }, 150),
  //   },
  // },

  methods: {
    search() {
      this.form.get(this.route("marketers.commissions", this.marketer.id), {
        preserveScroll: true,
        onSuccess: () => this.formAddVoucher.reset(""),
      });
    },
    reset() {
      this.form = mapValues(this.form, () => null);
    },
  },
};
</script>

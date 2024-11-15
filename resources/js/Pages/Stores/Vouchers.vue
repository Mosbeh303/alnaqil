<template>
  <Head :title="language.bond_disclosure" />

  <div>
    <page-header> {{ language.bond_disclosure }}</page-header>

    <FlashMessages class="md:m-7 m-4" />

    <Section title="" class="md:m-7 m-4">
      <form @submit.prevent="submit">
        <Label :value="language.bond_no" class="mb-2" />
        <Input
          id="number"
          type="text"
          class="w-full mt-2"
          v-model="form.number"
          :error="form.errors.number"
        ></Input>
        <InputError :message="form.errors.number" />

        <Label :value="language.bond_type" class="mt-4" />
        <SelectInput
          v-model="form.type"
          :error="form.errors.type"
          class="mt-4 block w-full"
          :selectPlaceholder="language.choose_the_type_of_bond"
        >
          <option :value="null">{{ language.all }}</option>
          <option value="RECEIPT">{{ language.catch }}</option>
          <option value="PAYMENT">{{ language.cashing }}</option>
        </SelectInput>
        <InputError :message="form.errors.type" />

        <Label :value="language.from" class="mt-4" />
        <Input
          id="from"
          type="date"
          class="w-full mt-2"
          v-model="form.from"
          :error="form.errors.from"
        ></Input>
        <InputError :message="form.errors.from" />

        <Label :value="language.to" class="mt-4" />
        <Input
          id="to"
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
        <form :action="route('stores.vouchers.pdf')" method="post">
          <input type="hidden" name="_token" :value="$page.props.csrf" />
          <input type="hidden" name="from" v-model="form.from" />
          <input type="hidden" name="to" v-model="form.to" />
          <input type="hidden" name="type" v-model="form.type" />
          <input type="hidden" name="number" v-model="form.number" />
          <Button
            for="from"
            class="bg-gray-200 !text-white py-2 hover:!text-gray-200 text-sm"
          >
            {{ language.download_PDF }}
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
                      {{ language.Store_name }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bond_no }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bond_type }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bond_value }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bond_date }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.responsible }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.employee }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bank }}
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
                      {{ item.store }}
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('vouchers.show', item.id)"
                        tabindex="-1"
                      >
                        {{ item.number }}
                      </Link>
                    </td>

                    <td class="border-t px-4">
                      <span v-if="item.type === 'RECEIPT'">{{ language.catch }}</span>
                      <span v-else>{{ language.cashing }}</span>
                    </td>
                    <td class="border-t px-4">
                      {{ item.amount }}
                    </td>
                    <td class="border-t px-4">
                      {{ item.date }}
                    </td>
                    <td class="border-t px-4">
                      {{ item.responsible }}
                    </td>
                    <td class="border-t px-4">
                      {{ item.employee }}
                    </td>

                    <td class="border-t px-4">
                      {{ item.bank }}
                    </td>
                  </tr>
                  <tr v-if="items.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.no_data }}
                    </td>
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
    items: Object,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        number: "",
        from: "",
        to: "",
        type: "",
      }),
    };
  },

  watch: {},

  methods: {
    submit() {
      this.form.post(this.route("stores.get.vouchers"), {
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

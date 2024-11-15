<template>
  <div>
    <Head :title="language.settings" />
    <page-header> {{ language.settings }}</page-header>

    <Layout>
      <Section class="flex flex-col gap-1">
        <Alert> {{ language.use_these_variables_in_message_settings }} </Alert>
        <div class="flex md:flex-row flex-col gap-4 md:gap-7">
          <div>
            <p>
              <span class="text-red-600 font-bold">{phone} </span>
              <span>: {{ language.recipients_phone_number }}</span>
            </p>
            <p>
              <span class="text-red-600 font-bold">{receiver} </span>
              <span>: {{ language.recipient }}</span>
            </p>
            <p>
              <span class="text-red-600 font-bold">{store} </span>
              <span>:{{ language.sender_store_name }}</span>
            </p>
            <p>
              <span class="text-red-600 font-bold">{number} </span>
              <span>: {{ language.Shipment_number }}</span>
            </p>
            <p>
              <span class="text-red-600 font-bold">{amount} </span>
              <span>: {{ language.payment_amount_upon_receipt }}</span>
            </p>
            <p>
              <span class="text-red-600 font-bold">{content} </span>
              <span>: {{ language.package_contents }}</span>
            </p>
          </div>

          <div>
            <p>
              <span class="text-red-600 font-bold">{owner} </span>
              <span>: {{ language.Store_owner_name }}</span>
            </p>
            <p>
              <span class="text-red-600 font-bold">{dues} </span>
              <span>: {{ language.store_dues }}</span>
            </p>
          </div>
        </div>
      </Section>

      <Section :title="language.whatsApp_message" class="mt-4 md:mt-7">
        <form @submit.prevent="submit('whatsapp_message', formWhatsappMessage)">
          <Label :value="language.the_message" class="mt-4" />
          <Textarea
            id="whatsapp"
            type="text"
            v-model="formWhatsappMessage.value"
            :error="formWhatsappMessage.errors.value"
            class="mt-2 block w-full"
            required
            :placeholder="language.add_the_message"
          />
          <InputError :message="formWhatsappMessage.errors.value" />
          <Button
            :class="{ 'opacity-25': formWhatsappMessage.processing }"
            :disabled="formWhatsappMessage.processing"
            class="mt-4 px-6"
          >
            {{ language.save }}
          </Button>
        </form>
      </Section>

      <Section :title="language.receiving_shipment_message" class="mt-4 md:mt-7">
        <form @submit.prevent="submit('receiving_shipment_message', form_receiving_shipment_message)">
          <Label :value="language.the_message" class="mt-4" />
          <Textarea
            id="receiving_shipment_message"
            type="text"
            v-model="form_receiving_shipment_message.value"
            :error="form_receiving_shipment_message.errors.value"
            class="mt-2 block w-full"
            required
            :placeholder="language.add_the_message"
          />
          <InputError :message="form_receiving_shipment_message.errors.value" />
          <Button
            :class="{ 'opacity-25': form_receiving_shipment_message.processing }"
            :disabled="form_receiving_shipment_message.processing"
            class="mt-4 px-6"
          >
            {{ language.save }}
          </Button>
        </form>
      </Section>
      <Section :title="language.creating_shipment_message" class="mt-4 md:mt-7">
        <form @submit.prevent="submit('creating_shipment_message', form_creating_shipment_message)">
          <Label :value="language.the_message" class="mt-4" />
          <Textarea
            id="receiving_shipment_message"
            type="text"
            v-model="form_creating_shipment_message.value"
            :error="form_creating_shipment_message.errors.value"
            class="mt-2 block w-full"
            required
            :placeholder="language.add_the_message"
          />
          <InputError :message="form_creating_shipment_message.errors.value" />
          <Button
            :class="{ 'opacity-25': form_creating_shipment_message.processing }"
            :disabled="form_creating_shipment_message.processing"
            class="mt-4 px-6"
          >
            {{ language.save }}
          </Button>
        </form>
      </Section>
      <Section :title="language.whatsApp_message_for_shop_owners" class="mt-4 md:mt-7">
        <form
          @submit.prevent="submit('whatsapp_store_message', formWhatsappStoreMessage)"
        >
          <Label :value="language.the_message" class="mt-4" />
          <Textarea
            id="sms"
            type="text"
            v-model="formWhatsappStoreMessage.value"
            :error="formWhatsappStoreMessage.errors.value"
            class="mt-2 block w-full"
            required
            :placeholder="language.add_the_message"
          />
          <InputError :message="formWhatsappStoreMessage.errors.value" />
          <Button
            :class="{ 'opacity-25': formWhatsappStoreMessage.processing }"
            :disabled="formWhatsappStoreMessage.processing"
            class="mt-4 px-6"
          >
            {{ language.save }}
          </Button>
        </form>
      </Section>
    </Layout>
  </div>
</template>

<script>
import Layout from "./Layout.vue";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head, Link } from "@inertiajs/vue3";
import Section from "@/Components/Section.vue";
import Textarea from "@/Components/Textarea.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import Button from "@/Components/Button.vue";
import Icon from "@/Components/Icon.vue";
import Alert from "@/Components/Alert.vue";

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Link,
    Section,
    Textarea,
    Label,
    InputError,
    Button,
    Icon,
    Alert,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    whatsappMessage: String,
    receiving_shipment_message: String,
    creating_shipment_message: String,
    whatsappStoreMessage: String,
    language: Object,
  },

  data() {
    return {
      formWhatsappMessage: this.$inertia.form({
        value: this.whatsappMessage,
      }),
      form_receiving_shipment_message: this.$inertia.form({
        value: this.receiving_shipment_message,
      }),
      form_creating_shipment_message: this.$inertia.form({
        value: this.creating_shipment_message,
      }),
      formWhatsappStoreMessage: this.$inertia.form({
        value: this.whatsappStoreMessage,
      }),
    };
  },

  methods: {
    submit(option, form) {
      form.post(this.route("settings.store", option), {
        preserveScroll: true,
        onError: () => form.reset("value"),
      });
    },
  },
};
</script>

<template>
  <Head :title="language.complaint_no + ` ${complaint.id}`" />

  <div>
    <page-header>
      {{ language.complaint_no }} {{ complaint.id }}
      <Button
        v-if="complaint.status == 1"
        :class="{
          '!text-gray-700 !bg-yellow-400 md:absolute md:right-7':
            $page.props.locale == 'en',
          '!text-gray-700 !bg-yellow-400 md:absolute md:left-7':
            $page.props.locale == 'ar',
        }"
        @click="showChangeStatusModal = !showingModal"
        >{{ language.close_the_complaint }}</Button
      >
      <Button
        v-if="complaint.status == 0"
        :class="{
          '!text-gray-700 !bg-yellow-400 md:absolute md:right-7':
            $page.props.locale == 'en',
          '!text-gray-700 !bg-yellow-400 md:absolute md:left-7':
            $page.props.locale == 'ar',
        }"
        method="post"
        :href="route('complaints.update_status', complaint)"
        >{{ language.open_the_complaint }}</Button
      >
    </page-header>

    <flash-messages />

    <div class="pb-12 pt-7 sm:px-0 px-4">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mb-7">
        <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-5">
          <Widget
            :textColor="'text-orange-500'"
            :class="{
              'text-green-500': complaint.status,
              'text-gray-500': !complaint.status,
            }"
            :widgetText="language.complaint_status"
            :widgetValue="complaint.status ? language.open : language.closed"
            type="vertical"
          />
          <Widget
            color="text-orange-500"
            :widgetText="language.shipment_status"
            :widgetValue="$page.props.shipmentStatus[shipment.status]"
            type="vertical"
          />
          <Widget
            color="text-purple-500"
            :widgetText="language.added_date"
            :widgetValue="complaint.created_at"
            type="vertical"
          />

          <Widget
            color="text-purple-500"
            :widgetText="language.closing_date"
            :widgetValue="!complaint.status ? complaint.end_date : '--'"
            type="vertical"
          />

          <Widget
            color="text-purple-500"
            :widgetText="language.closing_status"
            :widgetValue="!complaint.status ? complaint.case : '--'"
            type="vertical"
          />
        </div>
      </div>

      <Section class="md:m-7">
        <form @submit.prevent="submit">
          <Textarea
            class="w-full"
            :placeholder="language.AddNoteDetails"
            v-model="form.content"
            required
          ></Textarea>
          <InputError :message="form.errors.content" />
          <Button class="block mt-2">{{ language.add }} </Button>
        </form>
        <div class="container mx-auto py-6 flex justify-center">
          <div class="w-full pl-4 h-full flex flex-col">
            <div
              class="bg-white text-base text-gray-500 font-bold px-5 py-2 border-b border-gray-300"
            >
              {{ language.details }}
            </div>

            <div class="w-full h-full bg-white">
              <table class="w-full">
                <tbody class="">
                  <tr
                    class="relative transform scale-100 text-sm py-1 border-b-2 border-gray-200 cursor-default bg-opacity-25"
                    v-for="notice in notices"
                    :key="notice.id"
                  >
                    <td class="pl-5 pr-3 py-4 whitespace-no-wrap">
                      <div class="text-gray-400">
                        {{ notice.day }}
                      </div>
                      <div>{{ notice.time }}</div>
                    </td>

                    <td class="px-2 py-2 whitespace-no-wrap">
                      <div class="leading-5 text-gray-500 font-medium">
                        <Link
                          v-if="notice.user_account == 'employee'"
                          :href="route('employee.edit', notice.user_id)"
                        >
                          {{ notice.user }}
                        </Link>
                        <span v-else>{{ notice.user }} </span>
                      </div>
                      <div class="leading-5 text-gray-900">
                        {{ notice.content }}
                      </div>
                    </td>
                    <td class="px-2 py-2 whitespace-no-wrap">
                      <p
                        v-if="this.$page.props.auth.account == 'admin'"
                        @click="updateNoticeModal(notice)"
                      >
                        <icon name="edit" class="block w-5 h-5 text-gray-500" />
                      </p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </Section>
    </div>
    <!-- End Main Container -->

    <Modal :show="showModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ language.edit_note }}</h2>
      <form @submit.prevent="submitUpdateNotice" id="editNotice">
        <Textarea
          class="w-full"
          :placeholder="language.AddNoteDetails"
          v-model="formEditNotice.content"
          required
        ></Textarea>
        <InputError :message="formEditNotice.errors.content" />
      </form>
      <Button class="mt-4 !border" form="editNotice"> {{ language.modify }} </Button>
      <Button
        @click="showModal = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>

    <Modal :show="showChangeStatusModal">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.close_the_complaint }}
      </h2>
      <form @submit.prevent="submitUpdateComplaintStatus" id="complaintStatus">
        <label> {{ language.closing_status }}</label>
        <SelectInput
          v-model="form.case"
          :error="form.errors.case"
          class="mt-4 block w-full"
          :selectPlaceholder="language.Status"
        >
          <option value="solved">{{ language.solved }}</option>
          <option value="not solved">{{ language.not_resolved }}</option>
        </SelectInput>
      </form>
      <Button class="mt-4 !border" form="complaintStatus"> {{ language.save }} </Button>
      <Button
        @click="showChangeStatusModal = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Widget from "@/Pages/Dashboard/Components/Widget.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import Chart from "@/Components/Chart.vue";
import Section from "@/Components/Section.vue";
import Textarea from "@/Components/Textarea.vue";
import InputError from "@/Components/InputError.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue";
import SelectInput from "@/Components/SelectInput.vue";

export default {
  layout: BreezeAuthenticatedLayout,

  components: {
    Head,
    PageHeader,
    Widget,
    Chart,
    Button,
    Link,
    Section,
    Textarea,
    InputError,
    FlashMessages,
    Icon,
    Modal,
    SelectInput,
  },

  props: {
    complaint: Object,
    shipment: Object,
    notices: Object,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        content: "",
        case: "",
      }),
      formEditNotice: this.$inertia.form({
        content: "",
      }),
      targetNotice: "",
      showModal: false,
      showChangeStatusModal: false,
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("complaints.notices.store", this.complaint), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },
    updateNoticeModal(notice) {
      this.showModal = true;
      this.targetNotice = notice;
      this.formEditNotice.content = notice.content;
    },
    submitUpdateNotice() {
      this.formEditNotice.post(
        this.route("complaints.notices.update", this.targetNotice),
        {
          preserveScroll: true,
        }
      );
      this.showModal = false;
    },

    submitUpdateComplaintStatus() {
      this.form.post(this.route("complaints.update_status", this.complaint), {
        preserveScroll: true,
      });
      this.showChangeStatusModal = false;
    },
  },
};
</script>

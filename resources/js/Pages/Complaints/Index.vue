<template>
    <Head :title="language.complaints" />

    <div>
        <page-header
            :button="{
                text: language.add_complaint,
                href: route('complaints.create'),
            }"
        >
            <span> {{ language.complaints }} </span>
        </page-header>

        <div
            class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
        >
            <flash-messages class="mx-auto w-full" />
            <!-- ***************** -->

            <div
                class="flex justify-between items-baseline md:flex-row flex-col"
            >
                <div class="flex justify-start gap-3">
                    <h2
                        v-if="form.status == 1"
                        class="text-gray-700 text-lg mb-4 font-semibold"
                    >
                        {{ language.open_complaints }}
                    </h2>
                    <h2
                        v-else-if="form.status != 1 && form.status != null"
                        class="text-gray-700 text-lg mb-4 font-semibold"
                    >
                        {{ language.closed_complaints }}
                    </h2>
                    <h2 v-else class="text-gray-700 text-lg mb-4 font-semibold">
                        {{ language.all_complaints }}
                    </h2>
                    <span class="flex px-3 font-bold text-gray-600"
                        ><Icon
                            name="users"
                            class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"
                        />
                        {{ complaints.total }}
                    </span>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <search-filter
                        v-model="form.search"
                        class="mr-4 w-full max-w-md"
                        @reset="reset"
                    >
                        <select-input
                            v-model="form.status"
                            :selectPlaceholder="language.client_filtering"
                        >
                            <option :value="null">
                                {{ language.all_complaints }}
                            </option>
                            <option value="1">{{ language.open }}</option>
                            <option value="2">{{ language.closed }}</option>
                        </select-input>
                    </search-filter>
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
                                            {{ language.complaint_number }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.Shipment_number }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.topic }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.Store_name }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{
                                                language.the_name_of_the_complainant
                                            }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.Status }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6"></th>
                                        <th class="pb-4 pt-6 px-6"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="complaint in complaints.data"
                                        :key="complaint.id"
                                        class="hover:bg-gray-100 whitespace-nowrap"
                                    >
                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4 focus:text-indigo-500"
                                                :href="
                                                    route(
                                                        'complaints.show',
                                                        complaint
                                                    )
                                                "
                                            >
                                                {{ complaint.number }}
                                            </Link>
                                        </td>
                                        <td class="border-t text-center">
                                            {{ complaint.shipment }}
                                        </td>
                                        <td class="border-t text-center">
                                            {{ complaint.subject }}
                                        </td>
                                        <td class="border-t text-center">
                                            {{ complaint.store }}
                                        </td>
                                        <td class="border-t text-center">
                                            {{ complaint.complainantName }}
                                        </td>

                                        <td class="text-center border-t">
                                            <p
                                                class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs m-auto"
                                                v-if="complaint.status === 1"
                                            >
                                                {{ language.open }}
                                            </p>

                                            <p
                                                class="rounded-xl text-gray-700 bg-gray-200 py-1 px-4 w-fit text-xs m-auto"
                                                v-else
                                            >
                                                {{ language.closed }}
                                            </p>
                                        </td>

                                        <td class="border-t text-center">
                                            <span
                                                v-if="
                                                    complaint.case == 'solved'
                                                "
                                                >{{ language.solved }}</span
                                            >
                                            <span
                                                v-if="
                                                    complaint.case ==
                                                    'not solved'
                                                "
                                                >{{
                                                    language.not_resolved
                                                }}</span
                                            >
                                        </td>

                                        <td class="border-t">
                                            <div class="flex justify-center">
                                                <Link
                                                    v-if="
                                                        $page.props.auth
                                                            .account == 'admin'
                                                    "
                                                    class="flex justify-center pr-2 pl-4"
                                                    :href="
                                                        route(
                                                            'complaints.edit',
                                                            complaint
                                                        )
                                                    "
                                                    tabindex="-1"
                                                >
                                                    <icon
                                                        name="edit"
                                                        class="block w-5 h-5 text-gray-500"
                                                    />
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="complaints.data.length === 0">
                                        <td
                                            class="px-6 py-4 border-t"
                                            colspan="4"
                                        >
                                            {{
                                                language.There_are_no_complaints
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <pagination class="mt-6" :links="complaints.links" />
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
import Modal from "@/Components/Modal.vue";
import FlashMessages from "@/Components/FlashMessages.vue";

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
        FlashMessages,
    },
    layout: BreezeAuthenticatedLayout,
    props: {
        filters: Object,
        complaints: Object,
        language: Object,
    },
    data() {
        return {
            form: {
                search: this.filters.search,
                status: this.filters.status,
            },
        };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get("/dashboard/complaints", pickBy(this.form), {
                    preserveState: true,
                    preserveScroll: true,
                });
            }, 150),
        },
    },

    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
        },
    },
};
</script>

<template>
    <Head :title="language.delegates" />

    <div>
        <page-header
            :button="{
                text: language.add_a_delegate,
                href: route('operator.create', {
                    type: type.type,
                    groupe: groupe.id,
                }),
            }"
        >
            <span> {{ language.delegates }} </span>
        </page-header>

        <div
            class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
        >
            <flash-messages class="mx-auto" />
            <!-- ***************** -->

            <div class="flex justify-between items-baseline">
                <div class="flex justify-start gap-3">
                    <h2
                        v-if="type.type == 'vip'"
                        class="text-gray-700 text-lg mb-4 font-semibold"
                    >
                        {{ language.distinguished_delegates }}
                    </h2>
                    <h2
                        v-else-if="type.type == 'rec'"
                        class="text-gray-700 text-lg mb-4 font-semibold"
                    >
                        {{ language.receipts }}
                    </h2>
                    <h2
                        v-else-if="type.type == 'del'"
                        class="text-gray-700 text-lg mb-4 font-semibold"
                    >
                        {{ language.delivery_handlers }}
                    </h2>
                    <h2 v-else class="text-gray-700 text-lg mb-4 font-semibold">
                        {{ language.all_delegates }}
                    </h2>
                    <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                        ( {{ groupe.name }} )
                    </h2>
                    <!-- <span class="flex px-3 font-bold text-gray-600"><Icon name="users" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"  /> {{Operators.total}} </span>-->
                </div>

                <div class="flex items-center justify-between mb-6">
                    <search-filter
                        v-model="form.search"
                        class="mr-4 w-full max-w-md"
                        @reset="reset"
                    >
                        <select-input
                            v-if="type.type == null"
                            v-model="form.type"
                            :selectPlaceholder="language.filter_delegates"
                        >
                            <option :value="null">
                                {{ language.all_delegates }}
                            </option>
                            <option value="rec">
                                {{ language.receipt_offices }}
                            </option>
                            <option value="del">
                                {{ language.delivery_handlers }}
                            </option>
                            <option value="vip">
                                {{ language.delivery_handlers }}
                            </option>
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
                                            {{ language.id_number }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.username }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.the_name }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.city_neighborhood }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.branch }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.mobile_number_ }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.indebtedness }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.custody }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.shipments }}
                                        </th>
                                        <th
                                            class="pb-4 pt-6 px-6"
                                            v-if="form.type == null"
                                        >
                                            {{ language.type }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.Status }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="rep in Operators.data"
                                        :key="rep.id"
                                        class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                                    >
                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4 focus:text-indigo-500"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                            >
                                                {{ rep.identification }}
                                            </Link>
                                        </td>
                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4 focus:text-indigo-500"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                            >
                                                {{ rep.username }}
                                            </Link>
                                        </td>
                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                                tabindex="-1"
                                            >
                                                {{ rep.name }}
                                            </Link>
                                        </td>
                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                                tabindex="-1"
                                            >
                                                {{ rep.city }} ({{
                                                    rep.neighborhood
                                                }})
                                            </Link>
                                        </td>

                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                                tabindex="-1"
                                            >
                                                {{ rep.branch }}
                                            </Link>
                                        </td>

                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                                tabindex="-1"
                                            >
                                                {{ rep.phone }}
                                            </Link>
                                        </td>
                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                                tabindex="-1"
                                            >
                                                {{ rep.unpaid_dues }}
                                            </Link>
                                        </td>
                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                                tabindex="-1"
                                            >
                                                {{ rep.custodies }}
                                            </Link>
                                        </td>
                                        <td class="border-t">
                                            <Link
                                                class="flex justify-center px-6 py-4"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                                tabindex="-1"
                                            >
                                                {{ rep.shipments }}
                                            </Link>
                                        </td>
                                        <td
                                            class="border-t"
                                            v-if="form.type == null"
                                        >
                                            <Link
                                                class="flex justify-center px-6 py-4"
                                                :href="
                                                    route('operator.show', rep)
                                                "
                                                tabindex="-1"
                                            >
                                                {{ rep.type }}
                                            </Link>
                                        </td>

                                        <td class="">
                                            <p
                                                class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs m-auto"
                                                v-if="rep.allowed == 1"
                                            >
                                                {{ language.available }}
                                            </p>

                                            <p
                                                class="rounded-xl text-red-700 bg-red-200 py-1 px-4 w-fit text-xs m-auto"
                                                v-else
                                            >
                                                {{ language.stopped }}
                                            </p>
                                        </td>

                                        <td class="w-px border-t">
                                            <div class="flex justify-center">
                                                <div
                                                    class="flex justify-center px-2"
                                                    @click="
                                                        changeOperatorStatus(
                                                            rep
                                                        )
                                                    "
                                                >
                                                    <icon
                                                        v-if="rep.allowed"
                                                        name="lock-closed"
                                                        class="block w-5 h-5 text-gray-500"
                                                    />
                                                    <icon
                                                        v-else
                                                        name="lock-open"
                                                        class="block w-5 h-5 text-gray-500"
                                                    />
                                                </div>

                                                <Link
                                                    class="flex justify-center pr-2 pl-4"
                                                    :href="
                                                        route(
                                                            'operator.edit',
                                                            rep
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
                                    <tr v-if="Operators.data.length === 0">
                                        <td
                                            class="px-6 py-4 border-t"
                                            colspan="4"
                                        >
                                            {{ language.no_delegates }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <pagination class="mt-6" :links="Operators.links" />
        </div>

        <Modal :show="showAlert">
            <h2 class="text-lg font-semibold mb-2 text-gray-800">
                {{ language.are_you_sure }}
            </h2>
            <p v-if="targetOperatorStatus == 1" class="text-base text-gray-600">
                {{ language.target_operator_status }}
            </p>
            <p v-else class="text-base text-gray-600">
                {{ language.do_you_want_to_reenable_the_delegate_account }}
            </p>
            <Button
                class="mt-4 !border"
                :href="alertLink"
                method="post"
                @click="showAlert = false"
            >
                <span v-if="targetOperatorStatus == 1">{{
                    language.stop
                }}</span>
                <span v-else>{{ language.allow }}</span>
            </Button>
            <Button
                @click="showAlert = false"
                type="button"
                class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
                >{{ language.cancel }}</Button
            >
        </Modal>
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
        Operators: Object,
        type: Object,
        groupe: Object,
        language: Object,
    },
    data() {
        return {
            form: {
                search: this.filters.search,
                type: this.filters.type,
                groupe: this.groupe.id,
            },
            showAlert: false,
            alertLink: "",
            targetOperatorStatus: 0,
        };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get("/dashboard/operators", pickBy(this.form), {
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
        changeOperatorStatus(operator) {
            this.alertLink = route("operator.update_status", operator);
            this.targetOperatorStatus = operator.allowed;
            this.showAlert = true;
        },
    },
};
</script>

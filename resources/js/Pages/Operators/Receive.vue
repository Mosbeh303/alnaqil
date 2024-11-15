<template>
    <Head :title="language.receiving_shipments" />

    <div>
        <page-header>
            <span
                v-if="
                    $page.props.auth.operatorType == 'rec' ||
                    $page.props.auth.operatorType == 'vip'
                "
                >{{ language.receiving_shipments }}
            </span>
            <!-- <span v-if="$page.props.auth.operatorType == 'vip'">{{
        language.Receipt_of_shipments_from_the_warehouse
      }}</span> -->
        </page-header>

        <FlashMessages class="md:m-7 m-4" />

        <Section :title="language.search_by_shipment_number" class="md:m-7 m-4">
            <form @submit.prevent="" class="pb-2">
                <div class="relative inline-block w-full h-fit">
                    <Input
                        id="search"
                        type="text"
                        :placeholder="language.add_number"
                        class="w-full mt-4"
                        v-model="form.search"
                        :error="form.errors.search"
                    ></Input>
                    <button
                        class="outline-none absolute top-6"
                        :class="{
                            'right-4': $page.props.locale == 'en',
                            'left-4': $page.props.locale == 'ar',
                        }"
                        @click="scan = !scan"
                    >
                        <Icon name="camera" class="h-4 w-4 text-gray-400" />
                    </button>
                </div>
                <InputError :message="form.errors.search" />
            </form>

            <!-- <div class="w-full text-center relative">
        <Scanner :scan="scan" :onDecode="onDecode" />
      </div> -->
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
                <div class="flex gap-2">
                    <div
                        v-if="
                            $page.props.auth.operatorType == 'rec' ||
                            $page.props.auth.operatorType == 'vip'
                        "
                    >
                        <form
                            v-if="received"
                            :action="
                                route('operator.shipments_pdf', {
                                    id: $page.props.auth.user.operator.id,
                                    shipments: numbers,
                                })
                            "
                            method="post"
                            class="inline-block"
                            id="form"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                :value="$page.props.csrf"
                            />
                            <Button
                                class="!bg-gray-100 text-sm !text-white !py-2 inline-block ml-2"
                            >
                                {{ language.download_PDF }}
                            </Button>
                        </form>
                        <Button
                            v-else
                            :href="
                                route('operator.shipment.receive', {
                                    shipment: null,
                                    all: form.all,
                                })
                            "
                            method="post"
                            class="mx-auto justify-center !text-green-600 bottom-4 !bg-transparent !border-green-500 ml-0 text-xs flex-no-wrap hover:!bg-green-500 hover:!text-white !rounded-xl !py-2 !px-4"
                        >
                            {{ language.received }}
                        </Button>
                    </div>

                    <!-- <Button
            v-if="$page.props.auth.operatorType == 'vip'"
            :href="route('operator.shipment.assign', { all: form.all })"
            method="post"
            class="mx-auto justify-center !text-green-600 bottom-4 !bg-transparent !border-green-500 ml-0 text-xs flex-no-wrap hover:!bg-green-500 hover:!text-white !rounded-xl !py-2 !px-4"
          >
            {{ language.receipt_from_the_warehouse }}
          </Button> -->
                    <span class="flex px-3 items-center font-bold text-gray-600"
                        ><Icon
                            name="shipment"
                            class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"
                        />
                        {{ shipments.total }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col">
                <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="overflow-x-auto border rounded">
                            <table class="min-w-full">
                                <thead class="border-b bg-gray-100">
                                    <tr>
                                        <th></th>
                                        <th
                                            scope="col"
                                            class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                        >
                                            {{ language.Shipment_number }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                        >
                                            {{ language.The_recipients_name }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                        >
                                            {{ language.city_and_neighborhood }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                        >
                                            {{
                                                language.amount_when_delivery_riyal
                                            }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                        >
                                            {{ language.notes }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                        >
                                            {{ language.Status }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                        ></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        class="border-t"
                                        v-for="shipment in shipments.data"
                                        :key="shipment.id"
                                    >
                                        <td>
                                            <button
                                                class="px-2"
                                                @click="
                                                    deleteShipment(
                                                        shipment.number
                                                    )
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5 text-gray-500"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"
                                                    />
                                                </svg>
                                            </button>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        >
                                            <Link
                                                class="flex items-center px-4"
                                                :href="
                                                    route(
                                                        'shipments.show',
                                                        shipment.id
                                                    )
                                                "
                                                tabindex="-1"
                                            >
                                                {{ shipment.number }}
                                            </Link>
                                        </td>
                                        <td
                                            class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                        >
                                            <Link
                                                class="flex items-center px-4"
                                                :href="
                                                    route(
                                                        'shipments.show',
                                                        shipment.id
                                                    )
                                                "
                                                tabindex="-1"
                                            >
                                                {{ shipment.receiverName }}
                                            </Link>
                                        </td>
                                        <td
                                            class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                        >
                                            <Link
                                                class="flex items-center px-4"
                                                :href="
                                                    route(
                                                        'shipments.show',
                                                        shipment.id
                                                    )
                                                "
                                                tabindex="-1"
                                            >
                                                {{ shipment.city }} ({{
                                                    shipment.neighborhood
                                                }})
                                            </Link>
                                        </td>
                                        <td
                                            class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                        >
                                            <Link
                                                class="flex items-center px-4"
                                                :href="
                                                    route(
                                                        'shipments.show',
                                                        shipment.id
                                                    )
                                                "
                                                tabindex="-1"
                                            >
                                                <span v-if="!shipment.cod">
                                                    {{ language.pre_paid }}
                                                </span>
                                                <span v-else>{{
                                                    shipment.cod
                                                }}</span>
                                            </Link>
                                        </td>
                                        <td
                                            class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                        >
                                            <Link
                                                class="flex items-center px-4"
                                                :href="
                                                    route(
                                                        'shipments.show',
                                                        shipment.id
                                                    )
                                                "
                                                tabindex="-1"
                                            >
                                                {{ shipment.notice }}
                                            </Link>
                                        </td>
                                        <td
                                            class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                        >
                                            <p
                                                class="rounded-xl text-gray-700 bg-gray-200 py-1 px-4 w-fit text-xs"
                                                v-if="shipment.status == 10"
                                            >
                                                {{ language.created }}
                                            </p>
                                            <p
                                                class="rounded-xl text-orange-700 bg-orange-200 py-1 px-4 w-fit text-xs"
                                                v-else-if="
                                                    shipment.status == 20
                                                "
                                            >
                                                {{ language.received }}
                                            </p>
                                            <p
                                                class="rounded-xl text-blue-700 bg-blue-200 py-1 px-4 w-fit text-xs"
                                                v-else-if="
                                                    shipment.status == 35
                                                "
                                            >
                                                {{
                                                    language.processing_in_progress
                                                }}
                                            </p>
                                        </td>
                                        <td
                                            class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                        >
                                            <div class="flex justify-end gap-3">
                                                <a
                                                    :href="`https://api.whatsapp.com/send?phone=${shipment.receiverPhone.replace(
                                                        '0',
                                                        '+966'
                                                    )}&text= ${
                                                        language.hello
                                                    }  ${
                                                        shipment.receiverName
                                                    }  ${
                                                        language.With_you_A_Cloud_Express_representative
                                                    } ${shipment.number}  ${
                                                        language.from
                                                    }  ${shipment.storeName}  ${
                                                        language.Please_send_the_website
                                                    }&quot;`"
                                                >
                                                    <Button
                                                        class="mx-auto justify-center bg-green-600 ml-0 text-xs flex-no-wrap !rounded-xl !py-2 !px-4"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="ml-2 w-4 h-4"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            aria-hidden="true"
                                                            role="img"
                                                            width="1em"
                                                            height="1em"
                                                            preserveAspectRatio="xMidYMid meet"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967c-.273-.099-.471-.148-.67.15c-.197.297-.767.966-.94 1.164c-.173.199-.347.223-.644.075c-.297-.15-1.255-.463-2.39-1.475c-.883-.788-1.48-1.761-1.653-2.059c-.173-.297-.018-.458.13-.606c.134-.133.298-.347.446-.52c.149-.174.198-.298.298-.497c.099-.198.05-.371-.025-.52c-.075-.149-.669-1.612-.916-2.207c-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372c-.272.297-1.04 1.016-1.04 2.479c0 1.462 1.065 2.875 1.213 3.074c.149.198 2.096 3.2 5.077 4.487c.709.306 1.262.489 1.694.625c.712.227 1.36.195 1.871.118c.571-.085 1.758-.719 2.006-1.413c.248-.694.248-1.289.173-1.413c-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214l-3.741.982l.998-3.648l-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884c2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"
                                                                fill="currentColor"
                                                            />
                                                        </svg>
                                                        <span
                                                            >{{
                                                                language.whatsapp
                                                            }}
                                                        </span>
                                                    </Button>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="shipments.data.length === 0">
                                        <td
                                            class="px-6 py-4 border-t"
                                            colspan="4"
                                        >
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
import throttle from "lodash/throttle";
import Scanner from "@/Pages/Dashboard/Components/BarcodeScanner.vue";

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
        Scanner,
    },
    layout: BreezeAuthenticatedLayout,
    props: {
        shipments: Object,
        received: { type: Boolean, default: false },
        numbers: Array,
        language: Object,
    },

    data() {
        return {
            form: this.$inertia.form({
                search: "",
                all: [],
            }),
            scan: false,
        };
    },

    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                if (this.form.search.length === 10) {
                    this.form.all.push(this.form.search);
                    this.form.post(
                        this.route("operator.post.shipments.receive"),
                        {
                            preserveScroll: true,
                            onSuccess: () => this.form.reset("search"),
                            onError: () => this.form.reset("search"),
                            onFinish: () => this.form.reset("search"),
                        }
                    );
                }
            }, 150),
        },
    },

    methods: {
        deleteShipment(number) {
            let index = this.form.all.indexOf(number);
            this.form.all.splice(index, 1);
            this.form.post(this.route("operator.post.shipments.receive"), {
                preserveScroll: true,
                // onSuccess: () => this.form.reset("search"),
            });
        },

        onDecode(a) {
            console.log(a);
            this.form.search = a;
            if (this.id) clearTimeout(this.id);
            this.id = setTimeout(() => {
                if (this.text === a) {
                    this.text = "";
                }
            }, 5000);
            this.scan = false;
        },
    },
};
</script>

<template>
    <div>
        <Head :title="language.link_settings" />

        <page-header> {{ language.link_settings }}</page-header>

        <flash-messages class="m-7" />

        <div class="grid gap-4 lg:gap-7 md:grid-cols-2 m-4 lg:m-7">
            <Section :title="language.link_key">
                <p class="text-gray-500 mt-4 text-sm">
                    {{ language.Use_the_binding_key_for_automating }}
                </p>
                <p class="text-gray-500 text-sm">
                    {{ language.The_key_is_encrypted_immediately }}
                </p>
                <p class="text-gray-500 text-sm">
                    {{ language.the_old_key_becomes_invalid }}
                </p>
                <div class="py-6">
                    <Button class="px-6" @click="showConfirmModal = true">
                        {{ language.generating_new_key }}
                    </Button>
                </div>
                <div v-if="accessToken">
                    <p
                        class="text-red-600 mt-2 text-sm"
                        v-if="accessToken.created_at"
                    >
                        * {{ language.new_key_generated_at_date }}
                        <span class="text-red-600">
                            {{ formatDate(accessToken.created_at) }}
                        </span>
                    </p>
                    <p
                        class="text-red-600 mt-2 text-sm"
                        v-if="accessToken.last_used_at"
                    >
                        * {{ language.The_last_use_of_the_wrench_is_dated }}
                        <span class="text-red-600">
                            {{ formatDate(accessToken.last_used_at) }}
                        </span>
                    </p>
                </div>
            </Section>

            <Section title="Webhook">
                <p class="text-gray-500 mt-4 text-sm">
                    {{ language.webhooks_definition }}
                </p>

                <div class="flex mt-5 align-items-center">
                    <Toggle
                        :value="webhook.active"
                        @inputclick="changeWebhookStatus($event)"
                    />
                    <p class="text-gray-700 text-sm mr-2">
                        {{ language.activate_notification }}
                    </p>
                </div>

                <form v-show="webhook.active" @submit.prevent class="mt-6">
                    <Label :value="language.Alerts_Receiver_Link" />
                    <Input
                        id="url"
                        type="text"
                        v-model="formUrl.url"
                        :error="formUrl.errors.url"
                        class="mt-2 block w-full"
                        required
                        autocomplete="url"
                    />
                    <InputError :message="formUrl.errors.url" />

                    <Label
                        :value="language.Secret_notification_key"
                        class="mt-5"
                    />
                    <div class="flex relative">
                        <Icon
                            :name="showToken ? 'eye-slash' : 'eye'"
                            class="absolute w-5 top-5 right-3 text-gray-500 cursor-pointer"
                            @click="showToken = !showToken"
                        />

                        <Input
                            id="secret"
                            :type="showToken ? 'text' : 'password'"
                            v-model="webhook.secret"
                            class="mt-2 block w-full pr-12 pl-24"
                            readonly
                        />
                        <p
                            @click="generateWebhookSecret()"
                            class="absolute font-bold top-5 left-3 text-sm cursor-pointer"
                            style="color: #64c7aa"
                        >
                            {{ language.Regenerate }}
                        </p>
                    </div>
                </form>
            </Section>
        </div>

        <Modal :show="showConfirmModal">
            <h2 class="text-lg font-semibold mb-2 text-gray-800">
                {{ language.confirmation_message }}
            </h2>

            <div
                v-show="showAlert"
                id="alert-3"
                class="flex px-4 py-3 mb-4 bg-orange-100 rounded-lg"
                role="alert"
            >
                <svg
                    class="flex-shrink-0 w-5 h-5 text-orange-900"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
                <div class="mr-3 text-sm font-medium text-orange-900">
                    {{ language.Make_sur_to_copy_the_key_now }}
                </div>
            </div>
            <div v-show="!showAlert">
                <p class="text-base text-gray-600">
                    {{ language.Are_you_sure_to_generate_a_new_spanner }}
                </p>
            </div>
            <div v-show="showAlert" class="flex relative">
                <input
                    id="newToken"
                    type="text"
                    ref="myinput"
                    class="block w-full pl-12 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                    v-model="token"
                    readonly
                />
                <svg
                    @click="copyToken()"
                    class="absolute w-5 top-2 left-3 text-gray-500 cursor-pointer"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z"
                    />
                </svg>
            </div>

            <Button
                v-show="!showAlert"
                class="mt-4 !border"
                @click="generateToken()"
                method="post"
            >
                <span>{{ language.confirmation }}</span>
            </Button>
            <Button
                v-show="showAlert"
                class="mt-4 !border"
                @click="copyToken()"
            >
                <span>{{ language.copy }}</span>
            </Button>
            <Button
                @click="showConfirmModal = false"
                type="button"
                class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
                >{{ language.close }}</Button
            >
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
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import Icon from "@/Components/Icon.vue";
import Alert from "@/Components/Alert.vue";
import Toggle from "@/Components/Toggle.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import { lang } from "moment";

export default {
    components: {
        Head,
        PageHeader,
        Button,
        Input,
        Section,
        BreezeValidationErrors,
        FlashMessages,
        Label,
        InputError,
        Modal,
        Icon,
        Link,
        Alert,
        Toggle,
    },
    layout: BreezeAuthenticatedLayout,

    props: {
        webhook: Object,
        accessToken: Object,
        language: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                secret: this.webhook.secret,
            }),
            formUrl: this.$inertia.form({
                url: this.webhook.url,
            }),
            showToken: false,
            showConfirmModal: false,
            showAlert: false,
            token: "",
        };
    },

    watch: {
        formUrl: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.post(
                    "/dashboard/users/webhook/url",
                    pickBy(this.formUrl),
                    {
                        preserveState: true,
                        preserveScroll: true,
                    }
                );
            }, 150),
        },
    },

    methods: {
        generateToken() {
            axios.post(route("users.api.token")).then((res) => {
                if (res.status == 200) {
                    this.token = res.data;
                    this.showAlert = true;
                }
                console.log(res.data);
            });
        },
        async copyToken() {
            try {
                await navigator.clipboard.writeText(this.token);
            } catch ($e) {
                console.log($e);
            }
        },

        changeWebhookStatus(event) {
            this.$inertia.post(
                `/dashboard/users/webhook/update-status`,
                {},
                {
                    preserveScroll: true,
                }
            );
        },

        generateWebhookSecret() {
            this.$inertia.post(
                `/dashboard/users/webhook/token`,
                {},
                {
                    preserveScroll: true,
                }
            );
        },
        formatDate(d) {
            var date = new Date(d);

            return date.toLocaleDateString("en-US");
        },
    },
};
</script>

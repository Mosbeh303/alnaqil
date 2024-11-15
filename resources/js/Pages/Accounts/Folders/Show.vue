<template>
    <div>

        <Head :title="fold.name" />

        <page-header>
            {{ fold.name }}

            <Dropdown class="top-0" v-bind:align="$page.props.locale == 'ar' ? 'left' : 'right'" width="48" :class="{
                '!text-gray-700 !bg-yellow-400 md:absolute md:right-7':
                    $page.props.locale == 'en',
                '!text-gray-700 !bg-yellow-400 md:absolute md:left-7':
                    $page.props.locale == 'ar',
            }">
                <template #trigger>
                    <span class="inline-flex rounded-md">
                        <button type="button"
                            class="inline-flex items-center whitespace-nowrap px-8 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                            {{ language.add }}
                            <div class="absolute top-3"
                                :class="{ 'left-2': $page.props.locale == 'ar', 'right-2': $page.props.locale == 'en' }">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                    <!-- <path fill-rule="evenodd"
                                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                        clip-rule="evenodd" /> -->
                                </svg>
                            </div>
                        </button>

                    </span>
                </template>

                <template #content>
                    <button class="text-base font-normal p-2 block"
                        @click="showAddFolderModal = !showAddFolderModal">{{ language.folder }}</button>
                    <button class="text-base font-normal p-2 block"
                        @click="showAddWalletModal = !showAddWalletModal">{{language.wallet}}</button>
                </template>
            </Dropdown>
        </page-header>

        <flash-messages />

        <Section :title="language.folders" class="md:m-7 m-4" v-if="folders.length > 0">
            <div class="grid md:gap-7 gap-4 lg:grid-cols-6 md:grid-cols-4 grid-cols-2 mt-4">
                <Link v-for="folder in folders" :key="folder.id" :href="route('accounts.folders.show', folder)" as="button"
                    class="flex justify-center flex-col items-center  text-gray-600 rounded py-6 px-4 w-full relative">
                <span class="absolute top-30 text-2xl font-bold text-white" style="top:110px">{{ folder.code }}</span>
                <Icon name="folder" class="text-gray-600 h-36 w-36"></Icon>
                <h3 class="text-base font-semibold">{{ folder.name }}</h3>
                </Link>
            </div>
        </Section>

        <Section :title="language.wallets" class="md:m-7 m-4" v-if="wallets.length > 0">
            <div class="flex flex-col">
                <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="overflow-x-auto border rounded">
                            <table class="min-w-full">
                                <thead class="border-b bg-gray-100 text-sm">
                                    <tr class="whitespace-nowrap">
                                        <th class="pb-4 pt-6 px-6">#</th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.wallet_name }}
                                        </th>
                                        <th class="pb-4 pt-6 px-6">
                                            {{ language.balance }}
                                        </th>

                                        <th class="pb-4 pt-6 px-6"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(wallet, i) in wallets" :key="i" class="hover:bg-gray-100 whitespace-nowrap">
                                        <td class="border-t">
                                            <Link class="flex justify-center px-6 py-4 focus:text-indigo-500"
                                                :href="route('accounts.folders.wallets.show', wallet)">
                                            {{ ++i }}
                                            </Link>
                                        </td>
                                        <td class="border-t text-center">
                                            {{ wallet.name }}
                                        </td>
                                        <td class="border-t text-center">
                                            {{ wallet.balance }}
                                        </td>

                                        <td class="border-t">
                                            <div class="flex justify-center">
                                                <Link class="flex justify-center pr-2 pl-4"
                                                    :href="route('accounts.folders.wallets.show', wallet)" tabindex="-1">
                                                <icon name="eye" class="block w-5 h-5 text-gray-500" />
                                                </Link>

                                                <Link v-if="this.$page.props.auth.account == 'admin'"
                                                    class="flex justify-center pr-2 pl-4"
                                                    :href="route('accounts.folders.wallets.edit', wallet)" tabindex="-1">
                                                <icon name="edit" class="block w-5 h-5 text-gray-500" />
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="wallets.length === 0">
                                        <td class="px-6 py-4 border-t" colspan="4">
                                            {{ language.no_banks }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <div v-if="wallets.length == 0 && folders.length == 0" class="p-7 ">
            <p class="text-gray-600"> {{ language.folder_empty }} </p>
        </div>

        <Modal :show="showAddFolderModal">
            <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ language.add_new_folder }}</h2>

            <form @submit.prevent="submit" class="mt-4">
                <input type="hidden" v-model="form.parentId">
                <Input id="name" type="text" v-model="form.name" :error="form.errors.name" class="mt-4 block w-full"
                    required :placeholder="language.the_name" autocomplete="name" />
                <InputError :message="form.errors.name" />

                <textarea id="description" type="text" v-model="form.description" :error="form.errors.description"
                    class="mt-4 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                    :placeholder="language.the_description"></textarea>
                <InputError :message="form.errors.description" />

                <Input id="name" type="text" v-model="form.code" :error="form.errors.code" class="mt-4 block w-full"
                    :placeholder="language.code" />
                <InputError :message="form.errors.code" />

                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="mt-4 px-6">
                    {{ language.add }}
                </Button>
                <Button
                    @click="showAddFolderModal = false"
                    type="button"
                    class="!bg-transparent !border !text-gray-600 !border-gray-400 "
                    :class="{'ml-3' : $page.props.locale == 'en', 'mr-3' : $page.props.locale == 'ar'}"
                >
                    {{ language.cancel }}
                </Button>
            </form>
        </Modal>

        <Modal :show="showAddWalletModal">
            <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ language.add_new_wallet }}</h2>

            <form @submit.prevent="submitWallet" class="mt-4">
                <Input id="name" type="text" v-model="formAddWallet.name" :error="formAddWallet.errors.name"
                    class="mt-4 block w-full" required :placeholder="language.the_name" autocomplete="name" />
                <InputError :message="formAddWallet.errors.name" />

                <progress v-if="formAddWallet.progress" :value="formAddWallet.progress.percentage" max="100">
                    {{ formAddWallet.progress.percentage }}%
                </progress>

                <Button :class="{ 'opacity-25': formAddWallet.processing }" :disabled="formAddWallet.processing"
                    class="mt-4 px-6">
                    {{ language.add }}
                </Button>
                <Button
                    @click="showAddWalletModal = false"
                    type="button"
                    class="!bg-transparent !border !text-gray-600 !border-gray-400"
                    :class="{'ml-3' : $page.props.locale == 'en', 'mr-3' : $page.props.locale == 'ar'}"
                >
                    {{ language.cancel }}
                </Button>
            </form>
        </Modal>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/vue3";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import Section from "@/Components/Section.vue";
import Icon from "@/Components/Icon.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Dropdown from "@/Components/Dropdown";
import Modal from "@/Components/Modal";
import Input from "@/Components/Input";
import InputError from "@/Components/InputError";
import Textarea from "@/Components/Textarea";

export default {
    components: {
        Head,
        PageHeader,
        Button,
        Section,
        Icon,
        Link,
        FlashMessages,
        Dropdown,
        Modal,
        Input,
        Textarea,
        InputError
    },
    layout: BreezeAuthenticatedLayout,
    props: {
        language: Object,
        folders: Object,
        fold: Object,
        wallets: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                name: "",
                description: "",
                code: "",
                parentId: this.fold.id
            }),
            formAddWallet: this.$inertia.form({
                name: "",
            }),
            showAddFolderModal: false,
            showAddWalletModal: false
        };
    },
    methods: {
        submit() {
            this.form.post(this.route("accounts.folders.store"), {
                preserveScroll: true,
                onSuccess: () => this.form.reset(),
                onFinish: () => this.form.reset(""),
            });
            this.showAddFolderModal = false
        },
        submitWallet() {
            this.formAddWallet.post(this.route("accounts.folders.wallets.store", this.fold), {
                preserveScroll: true,
                onSuccess: () => this.formAddWallet.reset(),
                onFinish: () => this.formAddWallet.reset(""),
            });
            this.showAddWalletModal = false
        },
    },
};
</script>

<template>
    <div>
        <Head :title="language.partners" />

        <page-header> {{ language.Partners }}</page-header>
        <flash-messages />
        <Section
            :title="language.all_partners + ` ${partners.length}`"
            class="md:m-7 m-4"
        >
            <div
                class="grid md:gap-7 gap-4 xl:grid-cols-5 lg:grid-cols-3 grid-cols-2 mt-4"
            >
                <div
                    v-for="partner in partners"
                    :key="partner.id"
                    class="flex justify-between flex-col items-center border border-gray-200 text-gray-700 rounded py-6 px-4"
                >
                    <div class="text-3xl font-bold my-2 flex">
                        <img
                            :src="`/uploads/${partner.logo}`"
                            class="max-h-24 w-auto"
                            alt="Partner logo"
                        />
                    </div>
                    <div class="w-full">
                        <h3 class="text-lg font-semibold text-center">
                            {{ partner.name }}
                        </h3>
                        <p class="w-full text-ellipsis overflow-hidden ...">
                            <span>{{ partner.website }}</span>
                        </p>
                        <div
                            class="flex justify-center gap-2 items-center w-full"
                        >
                            <Link :href="route('partners.edit', partner)">
                                <Icon
                                    name="edit"
                                    class="w-5 h-5 text-green-600"
                                />
                            </Link>
                            <button
                                class="cursor-pointer"
                                @click="deletePartner(partner)"
                            >
                                <Icon
                                    name="cancel"
                                    class="w-5 h-5 text-red-600"
                                />
                            </button>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('partners.create')"
                    class="flex justify-center flex-col items-center bg-gray-200 text-gray-400 rounded cursor-pointer py-6 px-4"
                >
                    <icon name="plus" class="w-8 h-8 my-2" />
                    <h3 class="text-lg font-semibold">
                        {{ language.add_a_partner }}
                    </h3>
                </Link>
            </div>
        </Section>

        <Modal :show="showDeletePartnerAlert">
            <h2 class="text-lg font-semibold mb-2 text-gray-800">
                {{ language.are_you_sure }}
            </h2>
            <p class="text-base text-gray-600">
                {{
                    language.do_you_want_to_confirm_the_deletion_of_the_partner
                }}
            </p>

            <Button
                class="mt-4 !border"
                :href="deletePartnerLink"
                method="post"
                @click="showDeletePartnerAlert = false"
            >
                <span>{{ language.delete }}</span>
            </Button>
            <Button
                @click="showDeletePartnerAlert = false"
                type="button"
                class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
                >{{ language.cancel }}</Button
            >
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
import Modal from "@/Components/Modal.vue";

export default {
    components: {
        Head,
        PageHeader,
        Button,
        Section,
        Icon,
        Link,
        FlashMessages,
        Modal,
    },
    layout: BreezeAuthenticatedLayout,

    props: {
        partners: Object,
        language: Object,
    },
    data() {
        return {
            deletePartnerLink: "",
            showDeletePartnerAlert: false,
        };
    },
    methods: {
        deletePartner(partner) {
            this.deletePartnerLink = route("partners.destroy", partner);
            this.showDeletePartnerAlert = true;
        },
    },
};
</script>

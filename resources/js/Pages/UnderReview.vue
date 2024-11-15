<template>
  <Head :title="language.the_account_is_under_review" />

  <BreezeValidationErrors class="mb-4" />

  <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
    {{ status }}
  </div>

  <div class="mb-8 text-center">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-12 w-12 mx-auto mb-6 mt-2 text-gray-500"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
    </svg>
    <h1 class="font-medium text-2xl text-center">
      {{ language.your_account_is_currently_under_review }}
    </h1>
    <p class="text-center mt-2 text-base">
      {{ language.thanks }} {{ $page.props.auth.user.name }}
      {{ language.to_register_you_with_us }} <br />
      {{ language.Your_account_is_currently_under_review }}
    </p>
  </div>

  <div class="mt-4 text-center">
    <a
      class="mx-auto justify-center items-end bg-green-600 ml-0 inline-flex px-4 py-3 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
      href="https://api.whatsapp.com/send?phone=966590700745"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="ml-2 w-5 h-5"
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
      <span>{{ language.contact_support }}</span>
    </a>
    <div class="flex justify-center items-center mt-4">
      <a href="/" class="underline text-sm text-gray-600 hover:text-gray-900 ml-2">
        {{ language.back_home }}</a
      >
      |

      <form :action="route('logout')" method="post">
        <input type="hidden" name="_token" :value="$page.props.csrf" />
        <button
          type="submit"
          class="underline text-sm text-gray-600 hover:text-gray-900 mr-2"
        >
          {{ language.sign_out }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import Button from "@/Components/Button";
import { Head, Link } from "@inertiajs/vue3";
import LogoutLink from "@/Components/LogoutLink.vue";

export default {
  layout: BreezeGuestLayout,

  components: {
    Head,
    Link,
    Button,
    LogoutLink,
  },
  props: {
    language: Object,
  },
};
</script>

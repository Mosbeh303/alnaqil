<template>
    <div :dir=" $page.props.locale == 'ar' ? 'rtl'  : 'ltr'" class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-3">
        <div>
            <a href="/" class="welcome-page-link">
                <BreezeApplicationLogo class="w-40 h-auto fill-current text-gray-500" />
            </a>
        </div>

        <div class="w-full sm:max-w-2xl my-6 sm:px-6 px-4 py-6 bg-gray-100 overflow-hidden rounded-lg">
            <slot />
        </div>
    </div>
</template>

<script>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue'

export default {
    components: {
        BreezeApplicationLogo,
    }
}
</script>

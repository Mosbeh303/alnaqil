<template>
    <Link class="underline text-sm text-gray-600 hover:text-gray-900 mr-2">
        <slot />
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    }
}
</script>

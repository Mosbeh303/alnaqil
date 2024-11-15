<template>
    <VueDatePicker
            v-model="date"
            range
            no-disabled-range
            :disabled-dates="disabledDates"
    />
</template>

<script setup>
import { ref } from 'vue';
import { addDays, subDays } from 'date-fns';

const date = ref(new Date());

const disabledDates = [subDays(new Date(), 1), new Date(), addDays(new Date(), 1)];
</script>

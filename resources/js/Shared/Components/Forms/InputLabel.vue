<script setup>
defineProps({
    value: String,
    required: Boolean
});
</script>

<template>
    <label class="form-label" v-if="value">
        {{ value }} <span v-if="required" class="text-danger">*</span>
    </label>
</template>

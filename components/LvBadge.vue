<template>
    <Badge type="tip" :text="`Lv${props.lv}`" />
</template>

<script setup lang="ts">
const props = defineProps<{ lv: string }>();
</script>

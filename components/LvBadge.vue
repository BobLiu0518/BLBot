<template>
    <Badge type="tip">
        <a href="/manual/exp" class="no-underline">Lv{{ props.lv }}</a>
    </Badge>
</template>

<script setup lang="ts">
const props = defineProps<{ lv: string }>();
</script>

<style scoped>
.no-underline {
    text-decoration: none;
}
</style>

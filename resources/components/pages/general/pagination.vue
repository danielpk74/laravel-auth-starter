<script setup>
    import { Bootstrap5Pagination } from 'laravel-vue-pagination';




</script>

<template>
    <Bootstrap5Pagination
        :data="laravelData"
        @pagination-change-page="getResults"
    />
</template>

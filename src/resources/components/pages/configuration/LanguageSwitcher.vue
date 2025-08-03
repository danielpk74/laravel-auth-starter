<!-- LanguageSwitcher.vue -->
<template>
    <div>
        <button @click="changeLocale('en')">English</button>
        <button @click="changeLocale('es')">Español</button>
        <!-- Add more language buttons as needed -->
    </div>
</template>

<script>
export default {
    methods: {
        changeLocale(newLocale) {
            this.$emit('locale-change', newLocale); // Emit an event
        },
    },
};
</script>

<!-- App.vue -->
<template>
    <div>
        <LanguageSwitcher @locale-change="updateLocale" />
    </div>
</template>

<script>
import LanguageSwitcher from '../configuration/LanguageSwitcher.vue';

export default {
    components: {
        LanguageSwitcher,
    },
    methods: {
        updateLocale(newLocale) {
            console.log(newLocale);
            this.$i18n.locale = newLocale; // Update the locale
        },
    },
};
</script>

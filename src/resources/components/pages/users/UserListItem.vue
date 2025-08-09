<script setup>

import { formatDate } from '../../../js/helpers/date_helper';
import { ref } from 'vue';
import { useToastr } from '../../../js/toastr';
import { useI18n } from 'vue-i18n';

const toastr = useToastr();
const { t } = useI18n();

const props = defineProps({
    user: Object,
    index: Number,
});

const emit = defineEmits(['userDeleted', 'editUser']);

const openEditUser = (user) => {
    emit('editUser', user)
}

const roles = ref([
    {
        name: 'Admin',
        value: 1,
    },
    {
        name: 'User',
        value: 2,
    }
])

const changeRole = (user, role) => {
    axios.patch(`/api/v1/users/${user.id}/change-role`, {
        role: role
    })
    .then(response => {
        toastr.success(t('user_role_updated_success'));
    })
    .catch(error => {
        console.log(error);
        toastr.error(t('user_role_updated_error'));
    });
}

const toggleBulkDelete = () => {
    emit('toggleBulkDelete', props.user)
}

</script>

<template>
    <tr>
        <td><input type="checkbox" @change="toggleBulkDelete" :value="user.id" v-model="selectedUsers"></td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ formatDate(user.created_at) }}</td>
        <td>
            <select name="role_select" id="role_select" class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="(user.role === role.name)"> {{ role.name }}</option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('userDeleted', user)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>

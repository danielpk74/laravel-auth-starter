<script setup>

import axios from 'axios';
import { ref, onMounted, inject, watch, nextTick } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { debounce } from 'lodash';
import { useI18n } from 'vue-i18n';
import { useToastr } from '../../../js/toastr';

import UserListItem from './UserListItem.vue';
import Pagination from '../general/pagination.vue';

const { t } = useI18n();
const toastr = useToastr();
const users = ref({'data': []});
const editing = ref(false);
const formValues = ref({
    id: null,
    name: '',
    email: '',
    password: ''
});
const form = ref(null);
const userIdBeingDeleted = ref(null);
const resetForm = useResetForm(); // Get the resetForm function

const getUsers = (page = 1) => {
    axios.get(`/api/v1/users?page=${page}`)
        .then(response => {
            users.value = response.data;
        })
        .catch(error => {
            console.log(error);
            toastr.success('user_get_list_error');
        });
};

//--- Add Users ---//
const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});

const openAddUserForm = () => {
    editing.value = false;
    formValues.value = {
        id: null,
        name: '',
        email: '',
        password: ''
    };
    // Use nextTick to ensure reactive changes are processed
    nextTick(() => {
        if (form.value) {
            form.value.resetForm({ values: formValues.value });
        }
        $('#userFormModal').modal('show');
    });
};

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
};

const createUser = (values, { resetForm, setFieldError, setErrors }) => {
    axios.post('/api/v1/users', values)
        .then(response => {
            users.value.data.unshift(response.data);
            $('#userFormModal').modal('hide');
            toastr.success('User created successfully');
            resetForm();
        })
        .catch((error) => {
            setErrors(error.response.data.errors);
            toastr.error(error.response.data.message);
            //setFieldError('email', error.response.data.errors.email[0]);
        });
};
//--- End Add User ---//


//--- Edit User ---//
const openConfirmUserDeletion = (user) => {
    userIdBeingDeleted.value = user.id;
    $('#deleteUserModal').modal('show');
};

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().test(
        'password-optional',
        'Password must be at least 8 characters if provided',
        function(value) {
            // If password is empty or undefined, it's valid (optional)
            if (!value || value.length === 0) {
                return true;
            }
            // If password is provided, it must be at least 8 characters
            return value.length >= 8;
        }
    ),
});

const openEditUser = (user) => {
    editing.value = true;
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        password: '' // Password should be empty for editing
    };
    // Use nextTick to ensure reactive changes are processed
    nextTick(() => {
        form.value.resetForm({ values: formValues.value });
        $('#userFormModal').modal('show');
    });
};

const updateUser = (values, { resetForm, setFieldError, setErrors }) => {
    // Remove password from values if it's empty
    const updateData = { ...values };
    if (!updateData.password || updateData.password.trim() === '') {
        delete updateData.password;
    }
    
    axios.put(`/api/v1/users/${formValues.value.id}`, updateData)
        .then((response) => {
            const index = users.value.data.findIndex(user => user.id === response.data.id);
            if (index !== -1) {
                users.value.data[index] = response.data;
            }
            toastr.success('User updated successfully');
            resetForm();
            $('#userFormModal').modal('hide');
        })
        .catch(error => {
            setErrors(error.response.data.errors);
            toastr.error(error.response.data.message);
            //setFieldError('email', error.response.data.errors.email[0]);
        });
};
//--- End Edit User ---//


//--- Star Delete ---//
const deleteUser = () => {
    axios.delete(`/api/v1/users/${userIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteUserModal').modal('hide');
            toastr.success(t('user_deleted_success'));
            users.value.data = users.value.data.filter(user => user.id !== userIdBeingDeleted.value);
        })
        .catch((error) => {
            console.log(error);
            toastr.error(t('user_deleted_error'));
        });
}

const selectedUsers = ref([]);
const toggleBulkDelete = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    console.log(index);
    if (index !== -1) {
        selectedUsers.value.splice(index, 1);
        return;
    }
    selectedUsers.value.push(user.id);
}

const deleteUserBulk = () => {
    axios.delete(`/api/v1/users`, {
        data: {
            ids: selectedUsers.value
        }
    })
    .then(() => {
        users.value.data = users.value.data.filter(
            user => !selectedUsers.value.includes(user.id)
        );
        toastr.success(t('user_deleted_success'));
    })
    .catch((error) => {
        console.log(error);
        toastr.error(t('user_deleted_error'));
    });
}
//--- End Delete User ---//

const searchUserQuery = ref(null);

const searchUser = () => {
    axios.get('/api/v1/users/search', {
        params: {
            query: searchUserQuery.value
        }
    })
    .then(resp => {
        users.value = resp.data;
    })
    .catch((error) => {
        console.log(error);
        toastr.error(t('user_search_error'));
    });
}

watch(searchUserQuery, debounce(searchUser, 300));

onMounted(() => {
    getUsers();
});
</script>

<template>
    <!-- Start Users Table  -->
    <div class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <div>
                    <button @click="openAddUserForm" type="button" class="mb-2 btn btn-primary" >
                        {{ t('user_add') }}
                    </button>

                    <button v-if="selectedUsers.length > 0" @click="deleteUserBulk" type="button" class="ml-2 mb-2 btn btn-danger" >
                        {{ t('general_delete_selected') }}
                    </button>
                </div>

                <div>
                    <input type="text" v-model="searchUserQuery" class="form-control" :placeholder="t('general_search')" />
                </div>
            </div>
        </div>

        <!-- Start Users Table  -->
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <th><input type="checkbox" name="" id=""></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                        <th>Role</th>
                        <th>Option</th>
                    </thead>
                    <tbody v-if="users.data.length > 0">
                        <UserListItem v-for="(user, index ) in users.data"
                            :key="user.id"
                            :user=user
                            :index=index
                            @edit-user="openEditUser"
                            @user-deleted="openConfirmUserDeletion"
                            @toggle-bulk-delete="toggleBulkDelete"
                        />
                    </tbody>

                    <tbody v-else>
                        <tr>
                            <td class="text-center" colspan="6">{{ t("general_search_no_results_found") }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Pagination :data="users" @pagination-change-page="getUsers"/>
    </div>
    <!-- End Users Table  -->

    <!-- Modal Form  -->
    <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">
                        <span v-if="editing"> {{ t('user_edit') }}</span>
                        <span v-else>{{ t('user_add') }}</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema" v-slot="{ errors }" :initial-values="formValues">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <Field name="name" type="text" class="form-control" :class="{'is-invalid': errors.name }"  />
                                <span class="invalid-feedback ">{{ errors.name }}</span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <Field name="email" type="email" class="form-control" id="email" :class="{'is-invalid': errors.email }" />
                                <span class="invalid-feedback ">{{ errors.email }}</span>
                            </div>
                            <div class="form-group">
                                <label for="password">
                                    Password
                                    <small v-if="editing" class="text-muted">(Leave empty to keep current password)</small>
                                </label>
                                <Field  name="password" type="password" class="form-control" :class="{'is-invalid': errors.password }" />
                                <span class="invalid-feedback ">{{ errors.password }}</span>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Form  -->
    <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <span>{{ t('user_delete') }}</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">
                    <h5> {{ t('user_delete_confirmation') }}</h5>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click.prevent="deleteUser" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>


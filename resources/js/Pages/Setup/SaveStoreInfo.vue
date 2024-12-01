<script setup>
import {useForm} from "@inertiajs/vue3";
import AppLayout from '@/Layouts/AppLayout.vue';
import JetButton from '@/Components/PrimaryButton.vue';
import JetInput from '@/Components/TextInput.vue';
import JetLabel from '@/Components/InputLabel.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';
import SimpleCard from '@/Components/SimpleCard.vue';
import InputError from "@/Components/InputError.vue";

const form = useForm({
  store_alias: '',
  store_name: '',
  access_token: '',
});

const submit = () => {
  form.post(route('setup.save-credentials'), {
    onFinish: () => form.reset(),
  });
};
</script>

<template>
  <AppLayout title="Save Store Information">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Save Store Information
      </h2>
    </template>
    <SimpleCard>

      <template #title>
        <h5 class="text-gray-100 text-xl leading-tight font-medium mb-2">
          Store Information
        </h5>
      </template>

      <JetValidationErrors class="mb-4"/>

      <form @submit.prevent="submit">
        <div>
          <JetLabel for="store_alias" value="Store Alias Name"/>
          <JetInput
              id="store_alias"
              v-model="form.store_alias"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
          />
          <InputError :message="form.errors.store_alias" class="mt-2" />
        </div>
        <div class="mt-4">
          <JetLabel for="store_name" value="Store Domain Name"/>
          <JetInput
              id="store_name"
              v-model="form.store_name"
              type="text"
              class="mt-1 block w-full"
              required
          />
          <InputError :message="form.errors.store_name" class="mt-2" />
        </div>

        <div class="mt-4">
          <JetLabel for="access_token" value="Access Token"/>
          <JetInput
              id="access_token"
              v-model="form.access_token"
              type="text"
              class="mt-1 block w-full"
              required
          />
          <InputError :message="form.errors.access_token" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
          <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Save Credentials
          </JetButton>
        </div>
      </form>
    </SimpleCard>
  </AppLayout>
</template>

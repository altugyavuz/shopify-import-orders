<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import JetButton from '@/Components/PrimaryButton.vue';

defineProps({
  orders: Object,
  importingProcess: Boolean,
});

const formatMoney = (money) => {
  const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  });

  return formatter.format(money);
}

const importOrders = async () => {
  window.location.href = route('orders.import');
}
</script>

<template>
  <AppLayout title="Orders">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Orders
      </h2>
    </template>

    <div class="bg-gray-100 dark:bg-gray-900">
      <div class="relative min-h-screen flex flex-col items-center selection:bg-[#FF2D20] selection:text-white">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">

          <div class="flex justify-between px-5 pt-5">
            <div>
              <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Total Orders: {{ orders.total }}
              </p>
            </div>
            <JetButton @click="importOrders" :disabled="importingProcess">
              <svg v-if="importingProcess" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <template v-if="importingProcess">Processing...</template>
              <template v-else>Import Orders</template>
            </JetButton>
          </div>

          <main class="px-5 mt-12">
            <div class="overflow-x-auto" v-if="orders.data.length">
              <table class="min-w-full dark:bg-gray-800 text-white border-collapse rounded-lg">
                <thead>
                <tr>
                  <th class="px-3 py-3 rounded-tl-lg border-b-2 border-gray-200 bg-gray-800 !text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                    Order ID
                  </th>
                  <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-800 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                    Customer Email
                  </th>
                  <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-800 text-center text-xs font-semibold text-gray-100 uppercase tracking-wider">
                    Total Price
                  </th>
                  <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-800 text-center text-xs font-semibold text-gray-100 uppercase tracking-wider">
                    Order Status
                  </th>
                  <th class="px-3 py-3 rounded-tr-lg border-b-2 border-gray-200 bg-gray-800 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                    Ordered At
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr class="hover:bg-gray-100 border-gray-600" v-for="order in orders.data" :key="order.id">
                  <td class="px-3 py-5 border-b border-gray-600 bg-gray-700 text-sm">
                    {{ order.shopify_order_id }}
                  </td>
                  <td class="px-3 py-5 border-b border-gray-600 bg-gray-700 text-sm">
                    {{ order.customer_email !== null && order.customer_email !== "" ? order.customer_email : "N/A" }}
                  </td>
                  <td class="px-3 py-5 border-b border-gray-600 bg-gray-700 text-sm text-center">
                    {{ formatMoney(order.total_price) }}
                  </td>
                  <td class="px-3 py-5 border-b border-gray-600 bg-gray-700 text-sm text-center">
                    {{ order.status.toUpperCase() }}
                  </td>
                  <td class="px-3 py-5 border-b border-gray-600 bg-gray-700 text-sm text-right">
                    {{ order.ordered_at }}
                  </td>
                </tr>
                <!-- Repeat the row as needed -->
                </tbody>
              </table>

              <div class="py-10 dark:bg-gray-800">
                <Pagination :pagination="orders" />
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

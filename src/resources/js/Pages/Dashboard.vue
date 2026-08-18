<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';
import { Bar, Line } from 'vue-chartjs';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

const props = defineProps({
  chartData: Object
});

const formatter = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

const stats = [
  { label: 'Clientes Ativos', value: props.chartData?.stats.clientes_ativos || 0, color: 'text-blue-600', icon: '👤' },
  { label: 'Contratos Ativos', value: props.chartData?.stats.contratos_ativos || 0, color: 'text-emerald-600', icon: '📄' },
  { label: 'Volume Total (Ativos)', value: formatter.format(props.chartData?.stats.valor_total_contratos || 0), color: 'text-purple-600', icon: '💰' },
];

const clientesChart = computed(() => ({
  labels: props.chartData?.labels || [],
  datasets: [
    {
      label: 'Novos Clientes Cadastrados',
      backgroundColor: 'rgba(59, 130, 246, 0.8)',
      borderRadius: 4,
      data: props.chartData?.clientes || []
    }
  ]
}));

const contratosChart = computed(() => ({
  labels: props.chartData?.labels || [],
  datasets: [
    {
      label: 'Novos Contratos Fechados',
      borderColor: 'rgba(16, 185, 129, 1)',
      backgroundColor: 'rgba(16, 185, 129, 0.15)',
      borderWidth: 2,
      pointBackgroundColor: 'rgba(16, 185, 129, 1)',
      tension: 0.4,
      fill: true,
      data: props.chartData?.contratos_total || []
    }
  ]
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { precision: 0 }
    }
  }
};
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <div class="py-8 bg-gray-50/50 min-h-screen">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold tracking-tight text-gray-900">
            Métricas Principais
          </h2>
        </div>

        <!-- KPIs Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="stat in stats" :key="stat.label" class="bg-white border rounded-xl p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-500 uppercase tracking-widest">{{ stat.label }}</p>
              <div class="text-2xl opacity-60">{{ stat.icon }}</div>
            </div>
            <p :class="['mt-4 text-4xl font-extrabold tracking-tight', stat.color]">{{ stat.value }}</p>
          </div>
        </div>

        <!-- Charts Grids -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
          <!-- Clientes Bar Chart -->
          <div class="bg-white border shadow-sm rounded-xl p-6">
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-gray-800">Crescimento de Base (Clientes)</h3>
              <p class="text-sm text-gray-500">Adesão de novos clientes nos últimos 12 meses</p>
            </div>
            <div class="h-80 w-full">
              <Bar :data="clientesChart" :options="chartOptions" />
            </div>
          </div>
          
          <!-- Contratos Line Chart -->
          <div class="bg-white border shadow-sm rounded-xl p-6">
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-gray-800">Assinatura de Contratos</h3>
              <p class="text-sm text-gray-500">Volume de contratos validados nos últimos 12 meses</p>
            </div>
            <div class="h-80 w-full">
              <Line :data="contratosChart" :options="chartOptions" />
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

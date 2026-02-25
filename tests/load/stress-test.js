/**
 * ╔══════════════════════════════════════════════════════════════════╗
 * ║  STRESS TEST — k6 Load Testing Script                           ║
 * ║  Simula 200 usuários simultâneos acessando a biblioteca         ║
 * ╚══════════════════════════════════════════════════════════════════╝
 *
 * Pré-requisitos:
 *   1. Instalar k6: https://k6.io/docs/getting-started/installation/
 *      - Linux: sudo snap install k6
 *      - Mac: brew install k6
 *
 *   2. Rodar o StressTestSeeder antes:
 *      sail artisan db:seed --class=StressTestSeeder
 *
 *   3. Garantir que o app está rodando:
 *      sail up -d && npm run dev
 *
 * Uso:
 *   k6 run tests/load/stress-test.js
 *
 *   Com mais/menos carga:
 *   k6 run --vus 50  --duration 30s tests/load/stress-test.js  (leve)
 *   k6 run --vus 500 --duration 5m  tests/load/stress-test.js  (pesado)
 */

import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Rate, Trend } from 'k6/metrics';

// ╔══════════════════════════════════════════════════════════════════╗
// ║  CONFIGURAÇÃO DO CENÁRIO                                        ║
// ╚══════════════════════════════════════════════════════════════════╝

const BASE_URL = __ENV.BASE_URL || 'http://localhost';

// Métricas personalizadas
const errorRate = new Rate('errors');
const searchDuration = new Trend('search_duration', true);
const pdfDuration = new Trend('pdf_download_duration', true);

export const options = {
    // Cenário de carga progressiva (ramp-up → sustentação → ramp-down)
    stages: [
        { duration: '30s', target: 50 },   // Aquecimento: 0 → 50 usuários
        { duration: '1m', target: 200 },  // Pico: 50 → 200 usuários
        { duration: '2m', target: 200 },  // Sustentação: mantém 200 por 2 minutos
        { duration: '30s', target: 0 },    // Resfriamento: 200 → 0
    ],

    thresholds: {
        http_req_duration: ['p(95)<3000'],     // 95% das requisições < 3s
        http_req_failed: ['rate<0.05'],        // Menos de 5% de erros
        errors: ['rate<0.1'],                  // Taxa de erro geral < 10%
        search_duration: ['p(95)<2000'],       // Busca: 95% < 2s
        pdf_download_duration: ['p(95)<10000'], // PDF: 95% < 10s (arquivo grande)
    },
};

// ╔══════════════════════════════════════════════════════════════════╗
// ║  DADOS DE TESTE                                                 ║
// ╚══════════════════════════════════════════════════════════════════╝

const SEARCH_TERMS = [
    'Cálculo', 'Direito', 'Psicologia', 'Anatomia', 'Algoritmos',
    'Banco de Dados', 'Gestão', 'Sociologia', 'Filosofia', 'Economia',
    'Enfermagem', 'Engenharia', 'Marketing', 'Contabilidade', 'Fisiologia',
    'Inteligência', 'Redes', 'Sistemas', 'Estruturas', 'Hidráulica',
    'Farmacologia', 'Estatística', 'Fisioterapia', 'Administração',
];

const COURSES = [
    'Engenharias',
    'Ciências Humanas e Sociais',
    'Área da Saúde',
    'Tecnologia e TI',
    'Conteúdos Gerais',
];

function randomItem(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
}

// ╔══════════════════════════════════════════════════════════════════╗
// ║  FLUXO DO USUÁRIO VIRTUAL                                       ║
// ╚══════════════════════════════════════════════════════════════════╝

export default function () {
    // --- 1. Acessar a vitrine de livros (API pública) ---
    group('Vitrine de Livros', () => {
        const res = http.get(`${BASE_URL}/api/livros`);
        check(res, {
            'vitrine: status 200': (r) => r.status === 200,
            'vitrine: retornou JSON': (r) => r.headers['Content-Type']?.includes('application/json'),
        }) || errorRate.add(1);
    });

    sleep(randomThinkTime());

    // --- 2. Buscar por termo aleatório ---
    group('Busca por Termo', () => {
        const term = randomItem(SEARCH_TERMS);
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/livros?search=${encodeURIComponent(term)}`);
        searchDuration.add(Date.now() - start);

        check(res, {
            'busca: status 200': (r) => r.status === 200,
            'busca: retornou JSON': (r) => r.headers['Content-Type']?.includes('application/json'),
        }) || errorRate.add(1);
    });

    sleep(randomThinkTime());

    // --- 3. Filtrar por curso ---
    group('Filtro por Curso', () => {
        const course = randomItem(COURSES);
        const res = http.get(`${BASE_URL}/api/livros?course=${encodeURIComponent(course)}`);
        check(res, {
            'filtro: status 200': (r) => r.status === 200,
        }) || errorRate.add(1);
    });

    sleep(randomThinkTime());

    // --- 4. Requisitar o PDF pesado (simula "Ler" um livro) ---
    group('Download de PDF', () => {
        const start = Date.now();
        const res = http.get(`${BASE_URL}/storage/livros/test-book.pdf`, {
            // Timeout mais alto para PDFs grandes
            timeout: '30s',
            // Não carrega o body completo na memória do k6
            responseType: 'none',
        });
        pdfDuration.add(Date.now() - start);

        check(res, {
            'pdf: status 200': (r) => r.status === 200,
            'pdf: sem timeout': (r) => r.timings.duration < 30000,
        }) || errorRate.add(1);
    });

    sleep(randomThinkTime());
}

// ╔══════════════════════════════════════════════════════════════════╗
// ║  HELPERS                                                        ║
// ╚══════════════════════════════════════════════════════════════════╝

/** Tempo de "pensamento" entre ações (1-3 segundos, distribuição realista) */
function randomThinkTime() {
    return 1 + Math.random() * 2;
}

// ╔══════════════════════════════════════════════════════════════════╗
// ║  RELATÓRIO FINAL                                                ║
// ╚══════════════════════════════════════════════════════════════════╝

export function handleSummary(data) {
    const avg = data.metrics.http_req_duration?.values?.avg?.toFixed(0) || 'N/A';
    const p95 = data.metrics.http_req_duration?.values?.['p(95)']?.toFixed(0) || 'N/A';
    const total = data.metrics.http_reqs?.values?.count || 0;
    const failed = data.metrics.http_req_failed?.values?.passes || 0;

    console.log('\n╔══════════════════════════════════════╗');
    console.log('║  📊 RELATÓRIO DE STRESS TEST         ║');
    console.log('╠══════════════════════════════════════╣');
    console.log(`║  Total de requisições: ${String(total).padStart(12)} ║`);
    console.log(`║  Tempo médio:     ${String(avg + 'ms').padStart(16)} ║`);
    console.log(`║  P95:             ${String(p95 + 'ms').padStart(16)} ║`);
    console.log(`║  Falhas:          ${String(failed).padStart(16)} ║`);
    console.log('╚══════════════════════════════════════╝\n');

    return {};
}

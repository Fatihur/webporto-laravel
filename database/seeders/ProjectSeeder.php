<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'OpsFlow Dashboard Suite',
                'slug' => 'opsflow-dashboard-suite',
                'description' => 'Unified operational dashboard for monitoring sales, support, and fulfillment in one interface.',
                'content' => '<p>OpsFlow menggabungkan data lintas departemen ke dalam satu dashboard real-time untuk membantu tim mengambil keputusan lebih cepat.</p>',
                'case_study_problem' => 'Tim operasional menggunakan beberapa spreadsheet dan tools terpisah sehingga reporting harian lambat dan sering tidak sinkron.',
                'case_study_process' => 'Mendesain data pipeline sederhana, membangun dashboard modular per departemen, lalu menerapkan role-based access untuk manajer dan staff.',
                'case_study_result' => 'Pelaporan harian menjadi otomatis, sinkronisasi data lebih konsisten, dan proses monitoring KPI jauh lebih cepat.',
                'case_study_metrics' => [
                    ['label' => 'Reporting Time', 'value' => '-65%'],
                    ['label' => 'Data Accuracy', 'value' => '+29%'],
                    ['label' => 'Team Adoption', 'value' => '92%'],
                ],
                'category' => 'software-dev',
                'thumbnail' => null,
                'project_date' => '2025-09-20',
                'tags' => ['Dashboard', 'Analytics', 'SaaS'],
                'tech_stack' => ['Laravel', 'Livewire', 'MySQL', 'Tailwind CSS'],
                'stats' => [
                    ['label' => 'Active Users', 'value' => '1.2K'],
                    ['label' => 'Data Refresh', 'value' => '15s'],
                ],
                'gallery' => [],
                'is_featured' => true,
                'meta_title' => 'OpsFlow Dashboard Suite',
                'meta_description' => 'Dashboard operasional real-time untuk monitoring KPI lintas tim.',
                'meta_keywords' => 'dashboard, laravel, livewire, analytics',
                'link' => 'https://example.com/opsflow',
            ],
            [
                'title' => 'SignalNet Infrastructure Revamp',
                'slug' => 'signalnet-infrastructure-revamp',
                'description' => 'Network architecture refresh to improve reliability and security across branch offices.',
                'content' => '<p>Perombakan infrastruktur jaringan untuk meningkatkan stabilitas koneksi antar cabang dan memperketat kontrol keamanan.</p>',
                'case_study_problem' => 'Gangguan konektivitas antar cabang menyebabkan downtime layanan internal dan keterlambatan proses bisnis.',
                'case_study_process' => 'Audit topology jaringan, segmentasi ulang VLAN, implementasi monitoring alerting, serta hardening policy firewall.',
                'case_study_result' => 'Downtime berkurang signifikan dan tim IT dapat mendeteksi serta menangani anomali lebih cepat.',
                'case_study_metrics' => [
                    ['label' => 'Network Downtime', 'value' => '-48%'],
                    ['label' => 'Incident Response', 'value' => '-37%'],
                    ['label' => 'Security Alerts', 'value' => '+100% visibility'],
                ],
                'category' => 'networking',
                'thumbnail' => null,
                'project_date' => '2025-06-11',
                'tags' => ['Network', 'Security', 'Infrastructure'],
                'tech_stack' => ['Cisco', 'Fortinet', 'Zabbix'],
                'stats' => [
                    ['label' => 'Sites', 'value' => '18'],
                    ['label' => 'Uptime', 'value' => '99.95%'],
                ],
                'gallery' => [],
                'is_featured' => false,
                'meta_title' => 'SignalNet Infrastructure Revamp',
                'meta_description' => 'Revamp infrastruktur jaringan untuk stabilitas dan keamanan enterprise.',
                'meta_keywords' => 'network, security, infrastructure, monitoring',
                'link' => null,
            ],
            [
                'title' => 'Retail Insight Engine',
                'slug' => 'retail-insight-engine',
                'description' => 'Data analysis workflow for retail demand forecasting and inventory optimization.',
                'content' => '<p>Mesin analisis data penjualan untuk memprediksi demand mingguan dan mengoptimalkan level stok per kategori produk.</p>',
                'case_study_problem' => 'Stockout dan overstock terjadi bersamaan karena perencanaan inventory tidak berbasis data historis yang kuat.',
                'case_study_process' => 'Membangun pipeline data transaksi, membuat model forecasting, dan dashboard insight untuk tim procurement.',
                'case_study_result' => 'Akurasi planning inventory meningkat dan biaya stok mati berhasil ditekan.',
                'case_study_metrics' => [
                    ['label' => 'Forecast Accuracy', 'value' => '91%'],
                    ['label' => 'Stockout', 'value' => '-22%'],
                    ['label' => 'Dead Stock Cost', 'value' => '-18%'],
                ],
                'category' => 'data-analysis',
                'thumbnail' => null,
                'project_date' => '2025-03-14',
                'tags' => ['Data', 'Forecasting', 'Retail'],
                'tech_stack' => ['Python', 'Pandas', 'SQL', 'Tableau'],
                'stats' => [
                    ['label' => 'Data Rows', 'value' => '4.8M'],
                    ['label' => 'Stores', 'value' => '120'],
                ],
                'gallery' => [],
                'is_featured' => true,
                'meta_title' => 'Retail Insight Engine',
                'meta_description' => 'Forecasting dan inventory optimization berbasis data retail historis.',
                'meta_keywords' => 'data analysis, forecasting, retail, inventory',
                'link' => null,
            ],
        ];

        $hasCaseStudyColumns = Schema::hasColumn('projects', 'case_study_problem')
            && Schema::hasColumn('projects', 'case_study_process')
            && Schema::hasColumn('projects', 'case_study_result')
            && Schema::hasColumn('projects', 'case_study_metrics');

        foreach ($projects as $project) {
            if (! $hasCaseStudyColumns) {
                unset(
                    $project['case_study_problem'],
                    $project['case_study_process'],
                    $project['case_study_result'],
                    $project['case_study_metrics'],
                );
            }

            Project::updateOrCreate(
                ['slug' => $project['slug']],
                $project,
            );
        }

        $this->command->info('ProjectSeeder: '.count($projects).' projects seeded.');
    }
}

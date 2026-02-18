<?php

namespace Database\Seeders;

use App\Models\KnowledgeEntry;
use Illuminate\Database\Seeder;

class KnowledgeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [
            [
                'title' => 'Tech Stack & Keahlian Fatih',
                'content' => <<<'TEXT'
Fatih adalah Full-Stack Developer dengan keahlian utama di:

**Backend:**
- Laravel (expert) - 5+ tahun pengalaman
- PHP 8.x - OOP, Design Patterns
- Node.js - Express, NestJS
- Database: MySQL, PostgreSQL, MongoDB
- API: RESTful, GraphQL

**Frontend:**
- React.js & Next.js
- Vue.js & Nuxt.js
- Tailwind CSS (favorite!)
- TypeScript

**DevOps & Tools:**
- Docker & Docker Compose
- AWS (EC2, S3, RDS)
- Git, GitHub Actions
- Linux Server Administration

**Design:**
- Figma (UI/UX Design)
- Adobe Illustrator

Fatih sangat passionate tentang clean code, test-driven development, dan scalable architecture.
TEXT,
                'category' => 'skills',
                'tags' => ['laravel', 'php', 'react', 'vue', 'fullstack', 'backend', 'frontend'],
                'is_active' => true,
            ],
            [
                'title' => 'Layanan yang Ditawarkan',
                'content' => <<<'TEXT'
Fatih menyediakan beberapa layanan profesional:

**1. Web Application Development**
- Company Profile Website
- E-Commerce Platform
- Custom CRM/ERP Systems
- SaaS Applications
- API Development & Integration

**2. UI/UX Design**
- Wireframing & Prototyping
- Responsive Web Design
- Design System Creation
- Mobile App Design

**3. Consulting & Technical Advisor**
- Code Review & Refactoring
- System Architecture Design
- Performance Optimization
- Security Audit

**4. Training & Mentoring**
- Laravel Workshop
- Full-Stack Development Course
- Private Mentoring

Semua project menggunakan metodologi agile dengan transparansi progress yang tinggi.
TEXT,
                'category' => 'services',
                'tags' => ['web development', 'design', 'consulting', 'training', 'laravel', 'jasa', 'layanan', 'service', 'pembuatan website', 'aplikasi'],
                'is_active' => true,
            ],
            [
                'title' => 'Range Harga Project',
                'content' => <<<'TEXT'
Estimasi harga layanan Fatih (bisa disesuaikan):

**Company Profile Website:**
- Starter: Rp 5.000.000 - 10.000.000
- Business: Rp 10.000.000 - 25.000.000
- Enterprise: Rp 25.000.000+

**Web Application:**
- MVP (Minimum Viable Product): Rp 15.000.000 - 30.000.000
- Medium Complexity: Rp 30.000.000 - 75.000.000
- Enterprise Level: Rp 75.000.000+

**UI/UX Design:**
- Landing Page Design: Rp 3.000.000 - 8.000.000
- Full Website Design: Rp 10.000.000 - 25.000.000
- Mobile App Design: Rp 15.000.000 - 40.000.000

**Consulting:**
- Hourly Rate: Rp 500.000 - 1.000.000/jam
- Daily Rate: Rp 3.000.000 - 5.000.000/hari

*Note: Harga final bergantung pada scope, complexity, dan timeline project.*
TEXT,
                'category' => 'pricing',
                'tags' => ['harga', 'budget', 'cost', 'rate', 'range', 'biaya', 'tarif', 'pricing', 'estimasi', 'project'],
                'is_active' => true,
            ],
            [
                'title' => 'Alur Kerja & Proses Development',
                'content' => <<<'TEXT'
Fatih menggunakan alur kerja yang terstruktur dan transparan:

**Fase 1: Discovery (1-2 minggu)**
- Kickoff meeting
- Requirement gathering
- User research
- Technical feasibility analysis

**Fase 2: Design (2-4 minggu)**
- Wireframing & prototyping
- UI Design
- Design review & feedback
- Design approval

**Fase 3: Development (4-12 minggu)**
- Sprint planning
- Agile development (2-week sprints)
- Weekly progress demo
- Continuous testing
- Code review

**Fase 4: Testing & QA (1-2 minggu)**
- Functional testing
- Performance testing
- Security testing
- User acceptance testing (UAT)

**Fase 5: Deployment & Launch**
- Server setup
- CI/CD configuration
- Production deployment
- Monitoring setup

**Fase 6: Maintenance & Support**
- Bug fixing (30 hari gratis)
- Performance monitoring
- Feature enhancements

Client akan selalu di-update via weekly meeting dan project management tool (Trello/Notion).
TEXT,
                'category' => 'process',
                'tags' => ['workflow', 'agile', 'development', 'process', 'timeline'],
                'is_active' => true,
            ],
            [
                'title' => 'Ketersediaan & Kontak',
                'content' => <<<'TEXT'
**Status Ketersediaan:**
Saat ini Fatih AVAILABLE untuk project baru!

**Waktu Kerja:**
- Senin - Jumat: 09:00 - 18:00 WIB
- Sabtu: 09:00 - 14:00 WIB (optional)
- Minggu: Libur (kecuali urgent)

**Response Time:**
- WhatsApp: 1-2 jam (jam kerja)
- Email: 24 jam
- Meeting: Bisa di-schedule via Calendly

**Cara Kerja Sama:**
1. Hubungi via WhatsApp/Email
2. Jelaskan project requirements
3. Diskusi scope & timeline
4. Review proposal & quotation
5. Sign contract
6. Mulai development!

**Payment Terms:**
- 30% DP (Down Payment) saat kontrak
- 40% di tengah project
- 30% setelah final delivery

Fatih juga terbuka untuk:
- Remote full-time position
- Part-time consulting
- Project-based freelance
TEXT,
                'category' => 'availability',
                'tags' => ['contact', 'available', 'booking', 'schedule', 'remote'],
                'is_active' => true,
            ],
            [
                'title' => 'Portofolio Unggulan',
                'content' => <<<'TEXT'
Beberapa project terbaik Fatih:

**E-Commerce Platform (2024)**
- Tech: Laravel, React, Midtrans Payment
- Features: Multi-vendor, real-time inventory, AI recommendation
- Result: 300% increase in sales for client

**SaaS HR Management System (2023)**
- Tech: Laravel, Vue.js, AWS
- Features: Attendance, payroll, recruitment module
- Scale: 5000+ employees managed

**Company Profile + CMS (2024)**
- Tech: Laravel, Livewire, Tailwind
- Features: Custom CMS, blog, portfolio gallery
- Performance: 99/100 PageSpeed score

**Mobile App Backend API (2023)**
- Tech: Laravel API, PostgreSQL, Redis
- Features: JWT Auth, push notification, real-time chat
- Scale: 50k+ active users

**Data Visualization Dashboard (2024)**
- Tech: Laravel, Python (pandas), Chart.js
- Features: ETL pipeline, automated reports, ML predictions

Lihat detail project di: /projects/software-dev
TEXT,
                'category' => 'portfolio',
                'tags' => ['projects', 'case study', 'ecommerce', 'saas', 'api'],
                'is_active' => true,
            ],
            [
                'title' => 'Keunggulan Bekerja dengan Fatih',
                'content' => <<<'TEXT'
Kenapa memilih Fatih untuk project Anda?

**1. Technical Excellence**
- Clean, maintainable code
- Follow industry best practices
- Test-driven development (TDD)
- CI/CD automation

**2. Communication**
- Bahasa Indonesia & English fluent
- Regular progress updates
- Transparent about challenges
- Good documentation

**3. Business Understanding**
- Bukan sekedar "coding"
- Paham business goals client
- Memberikan saran strategis
- Focus on ROI

**4. After-Sales Support**
- 30 hari bug fixing gratis
- 3 bulan support ringan
- Documentation lengkap
- Training untuk tim client

**5. Modern Tech Stack**
- Always up-to-date dengan tech terbaru
- Performance & security optimized
- Scalable architecture
- Mobile-first responsive design

**Testimoni Client:**
"Fatih sangat professional, komunikatif, dan hasilnya melebihi ekspektasi!" - CEO Startup ABC

"Code quality-nya tinggi, maintenance jadi mudah." - Tech Lead XYZ Corp
TEXT,
                'category' => 'general',
                'tags' => ['benefits', 'why', 'testimonial', 'advantage', 'professional'],
                'is_active' => true,
            ],
            [
                'title' => 'Game & Fun Facts',
                'content' => <<<'TEXT'
**Game Interaktif di Chatbot Fay:**

Fay punya beberapa game seru yang bisa dimainkan:

**1. Math Quiz 🧮**
- Quiz matematika dengan soal penjumlahan, pengurangan, perkalian
- Difficulty meningkat seiring streak
- +10 poin untuk setiap jawaban benar
- Streak bonus untuk jawaban berturut-turut
- Ketik "math" atau "matematika" untuk mulai

**2. Teka-Teki 🧩**
- Teka-teki logika dan riddles
- Pilihan ganda dengan 4 opsi
- Soal random setiap sesi
- Ketik "puzzle" atau "teka-teki" untuk mulai

**3. Tech Quiz 📚**
- Quiz pengetahuan teknologi
- Tentang HTML, CSS, JavaScript, Laravel, Git, dll
- Pilihan ganda dengan 4 opsi
- Ketik "quiz" untuk mulai

**Cara Main:**
- Ketik "main game" untuk lihat menu
- Pilih game yang mau dimainkan
- Jawab dengan input yang tersedia atau ketik angka
- Ketik "stop game" untuk berhenti

**Fun Facts:**
- Fay adalah AI assistant yang dibuat khusus untuk website portfolio Fatih
- Fay bisa ingat nama dan info user selama session
- Fay menggunakan teknologi Laravel AI (Laravel\Ai)
TEXT,
                'category' => 'general',
                'tags' => ['game', 'fun', 'interactive', 'quiz', 'math', 'puzzle', 'main', 'seru'],
                'is_active' => true,
            ],
        ];

        foreach ($entries as $entry) {
            KnowledgeEntry::updateOrCreate(
                ['title' => $entry['title']],
                $entry
            );
        }

        $this->command->info('Successfully seeded '.count($entries).' knowledge entries!');
    }
}

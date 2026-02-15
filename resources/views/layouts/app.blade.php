<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{ $seo ?? '' }}

    <!-- Critical Resource Hints -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Critical CSS - Minimal Reset + Utility Classes Only -->
    <style>
        /* Reset */
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4}
        body{line-height:1.5;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#fff;color:#09090b;min-height:100vh}
        a{text-decoration:none;color:inherit}
        button{font-family:inherit;cursor:pointer;border:none;background:none}
        img,svg{display:block;max-width:100%}
        ul,ol{list-style:none}

        /* Dark Mode */
        .dark body{background:#09090b;color:#fff}
        .dark nav{background:rgba(9,9,11,.8);border-color:#27272a}

        /* Colors */
        .text-mint{color:#76D7A4}
        .text-violet{color:#C4A1FF}
        .text-lime{color:#E8FF8E}
        .bg-mint{background:#76D7A4}
        .bg-violet{background:#C4A1FF}
        .bg-lime{background:#E8FF8E}
        .text-white{color:#fff}
        .text-zinc-50{color:#fafafa}
        .text-zinc-100{color:#f4f4f5}
        .text-zinc-400{color:#a1a1aa}
        .text-zinc-500{color:#71717a}
        .text-zinc-600{color:#52525b}
        .text-zinc-950{color:#09090b}
        .bg-white{background:#fff}
        .bg-zinc-50{background:#fafafa}
        .bg-zinc-100{background:#f4f4f5}
        .bg-zinc-900{background:#18181b}
        .bg-zinc-950{background:#09090b}
        .dark .text-zinc-950{color:#fff}
        .dark .bg-zinc-950{background:#fff}
        .dark .bg-zinc-900{background:#27272a}

        /* Layout */
        .fixed{position:fixed}
        .absolute{position:absolute}
        .relative{position:relative}
        .inset-0{inset:0}
        .top-0{top:0}
        .left-0{left:0}
        .right-0{right:0}
        .bottom-0{bottom:0}
        .z-10{z-index:10}
        .z-50{z-index:50}
        .z-\[60\]{z-index:60}
        .z-\[70\]{z-index:70}
        .-z-10{z-index:-10}

        /* Flex */
        .flex{display:flex}
        .inline-flex{display:inline-flex}
        .hidden{display:none}
        .flex-col{flex-direction:column}
        .items-start{align-items:flex-start}
        .items-center{align-items:center}
        .items-end{align-items:flex-end}
        .justify-center{justify-content:center}
        .justify-between{justify-content:space-between}
        .gap-1{gap:.25rem}
        .gap-2{gap:.5rem}
        .gap-3{gap:.75rem}
        .gap-4{gap:1rem}
        .gap-6{gap:1.5rem}
        .gap-8{gap:2rem}
        .space-y-1>:not([hidden])~:not([hidden]){margin-top:.25rem}
        .space-y-4>:not([hidden])~:not([hidden]){margin-top:1rem}
        .space-y-6>:not([hidden])~:not([hidden]){margin-top:1.5rem}
        .space-y-8>:not([hidden])~:not([hidden]){margin-top:2rem}
        .space-x-4>:not([hidden])~:not([hidden]){margin-left:1rem}
        .space-x-10>:not([hidden])~:not([hidden]){margin-left:2.5rem}

        /* Grid */
        .grid{display:grid}
        .grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}
        .grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
        .grid-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}

        /* Sizing */
        .w-full{width:100%}
        .w-8{width:2rem}
        .w-10{width:2.5rem}
        .w-12{width:3rem}
        .w-14{width:3.5rem}
        .w-80{width:20rem}
        .w-\[1px\]{width:1px}
        .h-full{height:100%}
        .h-8{height:2rem}
        .h-10{height:2.5rem}
        .h-12{height:3rem}
        .h-14{height:3.5rem}
        .h-20{height:5rem}
        .h-\[calc\(100vh-5rem\)\]{height:calc(100vh - 5rem)}
        .min-h-\[700px\]{min-height:700px}
        .min-h-screen{min-height:100vh}
        .max-w-3xl{max-width:48rem}
        .max-w-4xl{max-width:56rem}
        .max-w-7xl{max-width:80rem}
        .max-w-full{max-width:100%}
        .max-w-md{max-width:28rem}
        .max-w-xl{max-width:36rem}

        /* Spacing */
        .p-4{padding:1rem}
        .p-6{padding:1.5rem}
        .p-8{padding:2rem}
        .px-2{padding-left:.5rem;padding-right:.5rem}
        .px-4{padding-left:1rem;padding-right:1rem}
        .px-5{padding-left:1.25rem;padding-right:1.25rem}
        .px-6{padding-left:1.5rem;padding-right:1.5rem}
        .px-8{padding-left:2rem;padding-right:2rem}
        .py-2{padding-top:.5rem;padding-bottom:.5rem}
        .py-2\.5{padding-top:.625rem;padding-bottom:.625rem}
        .py-3{padding-top:.75rem;padding-bottom:.75rem}
        .py-4{padding-top:1rem;padding-bottom:1rem}
        .pt-8{padding-top:2rem}
        .pt-32{padding-top:8rem}
        .pb-20{padding-bottom:5rem}
        .mb-2{margin-bottom:.5rem}
        .mb-4{margin-bottom:1rem}
        .mb-6{margin-bottom:1.5rem}
        .mb-8{margin-bottom:2rem}
        .mb-10{margin-bottom:2.5rem}
        .mb-12{margin-bottom:3rem}
        .mb-16{margin-bottom:4rem}
        .mb-20{margin-bottom:5rem}
        .mb-40{margin-bottom:10rem}
        .mt-2{margin-top:.5rem}
        .mt-3{margin-top:.75rem}
        .mt-20{margin-top:5rem}
        .mr-auto{margin-right:auto}
        .ml-auto{margin-left:auto}
        .mx-auto{margin-left:auto;margin-right:auto}

        /* Typography */
        .text-xs{font-size:.75rem;line-height:1rem}
        .text-sm{font-size:.875rem;line-height:1.25rem}
        .text-base{font-size:1rem;line-height:1.5rem}
        .text-lg{font-size:1.125rem;line-height:1.75rem}
        .text-xl{font-size:1.25rem;line-height:1.75rem}
        .text-2xl{font-size:1.5rem;line-height:2rem}
        .text-3xl{font-size:1.875rem;line-height:2.25rem}
        .text-4xl{font-size:2.25rem;line-height:2.5rem}
        .text-6xl{font-size:3.75rem;line-height:1}
        .text-\[10px\]{font-size:10px}
        .text-\[25vw\]{font-size:25vw}
        .font-medium{font-weight:500}
        .font-semibold{font-weight:600}
        .font-bold{font-weight:700}
        .font-black{font-weight:900}
        .uppercase{text-transform:uppercase}
        .capitalize{text-transform:capitalize}
        .leading-none{line-height:1}
        .leading-tight{line-height:1.25}
        .leading-relaxed{line-height:1.625}
        .leading-\[1\.1\]{line-height:1.1}
        .tracking-tight{letter-spacing:-.025em}
        .tracking-tighter{letter-spacing:-.05em}
        .tracking-widest{letter-spacing:.1em}
        .tracking-\[0\.2em\]{letter-spacing:.2em}
        .tracking-\[0\.3em\]{letter-spacing:.3em}
        .tracking-\[0\.4em\]{letter-spacing:.4em}
        .text-center{text-align:center}
        .line-clamp-1{overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:1}
        .whitespace-nowrap{white-space:nowrap}

        /* Backgrounds */
        .bg-gradient-to-b{background-image:linear-gradient(to bottom,var(--tw-gradient-stops))}
        .from-zinc-300{--tw-gradient-from:#d4d4d8;--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to,rgba(212,212,216,0))}
        .to-transparent{--tw-gradient-to:transparent}

        /* Borders */
        .border{border-width:1px}
        .border-b{border-bottom-width:1px}
        .border-zinc-100{border-color:#f4f4f5}
        .border-zinc-200{border-color:#e4e4e7}
        .border-zinc-800{border-color:#27272a}
        .dark .border-zinc-800{border-color:#27272a}
        .dark .border-zinc-100{border-color:#27272a}
        .rounded-full{border-radius:9999px}
        .rounded-lg{border-radius:.5rem}
        .rounded-xl{border-radius:.75rem}
        .rounded-2xl{border-radius:1rem}
        .rounded-3xl{border-radius:1.5rem}
        .rounded-\[2\.5rem\]{border-radius:2.5rem}
        .rounded-\[3rem\]{border-radius:3rem}
        .rounded-\[2rem\]{border-radius:2rem}

        /* Effects */
        .blur-\[80px\]{--tw-blur:blur(80px);filter:var(--tw-blur)}
        .opacity-30{opacity:.3}
        .opacity-40{opacity:.4}
        .opacity-60{opacity:.6}
        .opacity-80{opacity:.8}
        .shadow-2xl{--tw-shadow:0 25px 50px -12px rgba(0,0,0,.25);box-shadow:var(--tw-shadow)}

        /* Transforms */
        .translate-x-full{--tw-translate-x:100%;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}
        .-translate-x-1\/2{--tw-translate-x:-50%;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}

        /* Transitions */
        .transition-all{transition-property:all;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}
        .transition-colors{transition-property:color,background-color,border-color,fill,stroke;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}
        .transition-transform{transition-property:transform;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}
        .transition-opacity{transition-property:opacity;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}
        .duration-200{transition-duration:.2s}
        .duration-300{transition-duration:.3s}
        .duration-500{transition-duration:.5s}
        .duration-\[0\.3s\]{transition-duration:.3s}

        /* Hover States */
        .hover\:scale-105:hover{--tw-scale-x:1.05;--tw-scale-y:1.05;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}
        .hover\:scale-\[1\.02\]:hover{--tw-scale-x:1.02;--tw-scale-y:1.02;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}
        .hover\:border-mint:hover{border-color:#76D7A4}
        .hover\:bg-zinc-50:hover{background-color:#fafafa}
        .hover\:bg-zinc-100:hover{background-color:#f4f4f5}
        .hover\:bg-zinc-900:hover{background-color:#18181b}
        .hover\:text-mint:hover{color:#76D7A4}

        /* Focus States */
        .focus\:outline-none:focus{outline:2px solid transparent;outline-offset:2px}
        .focus\:border-mint:focus{border-color:#76D7A4}

        /* Shrink */
        .shrink-0{flex-shrink:0}

        /* Overflow */
        .overflow-hidden{overflow:hidden}
        .overflow-x-hidden{overflow-x:hidden}
        .overflow-y-auto{overflow-y:auto}

        /* Pointer Events */
        .pointer-events-none{pointer-events:none}

        /* Select */
        .select-none{user-select:none}

        /* Contain */
        .contain-paint{contain:paint}

        /* Container */
        .container{width:100%}

        /* Responsive - lg */
        @media(min-width:1024px){
            .lg\:block{display:block}
            .lg\:flex{display:flex}
            .lg\:hidden{display:none}
            .lg\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
            .lg\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
            .lg\:grid-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}
            .lg\:grid-cols-12{grid-template-columns:repeat(12,minmax(0,1fr))}
            .lg\:col-span-5{grid-column:span 5/span 5}
            .lg\:col-span-7{grid-column:span 7/span 7}
            .lg\:flex-row{flex-direction:row}
            .lg\:items-end{align-items:flex-end}
            .lg\:px-12{padding-left:3rem;padding-right:3rem}
        }

        /* Responsive - md */
        @media(min-width:768px){
            .md\:block{display:block}
            .md\:hidden{display:none}
            .md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
            .md\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
            .md\:p-12{padding:3rem}
            .md\:p-24{padding:6rem}
            .md\:px-10{padding-left:2.5rem;padding-right:2.5rem}
            .md\:py-5{padding-top:1.25rem;padding-bottom:1.25rem}
            .md\:text-6xl{font-size:3.75rem;line-height:1}
        }

        /* Responsive - sm */
        @media(min-width:640px){
            .sm\:block{display:block}
            .sm\:flex{display:flex}
            .sm\:hidden{display:none}
            .sm\:flex-row{flex-direction:row}
            .sm\:items-center{align-items:center}
            .sm\:p-12{padding:3rem}
            .sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}
            .sm\:text-5xl{font-size:3rem;line-height:1}
            .sm\:text-8xl{font-size:6rem;line-height:1}
        }

        /* Reduced Motion */
        @media(prefers-reduced-motion:reduce){
            html{scroll-behavior:auto}
            *,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}
        }
    </style>

    <!-- Async Font Loading -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"></noscript>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Non-Critical Styles -->
    <style>
        /* Scrollbar */
        .custom-scrollbar::-webkit-scrollbar{width:6px}
        .custom-scrollbar::-webkit-scrollbar-track{background:transparent}
        .custom-scrollbar::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:10px}
        .dark .custom-scrollbar::-webkit-scrollbar-thumb{background:#334155}

        /* Code Blocks */
        .prose pre{background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:.75rem;overflow-x:auto;font-family:monospace;font-size:.875rem}

        /* Backdrop Blur Fallback */
        @supports(backdrop-filter:blur(12px)){
            .backdrop-blur-md{backdrop-filter:blur(12px)}
        }
        @supports not (backdrop-filter:blur(12px)){
            .backdrop-blur-md{background:rgba(255,255,255,.95)}
            .dark .backdrop-blur-md{background:rgba(9,9,11,.95)}
        }
    </style>

    @hasSection('mathjax')
    <script>
        window.MathJax = {tex:{inlineMath:[['$','$'],['\\(','\\)']]},svg:{fontCache:'global'}};
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    @endif
</head>
<body class="min-h-screen antialiased">
    <livewire:navigation />

    <main>
        {{ $slot }}
    </main>

    @include('components.footer')

    <!-- Theme Script -->
    <script>
        (function(){
            const t=localStorage.getItem('theme');
            const d=window.matchMedia('(prefers-color-scheme:dark)').matches;
            if(t==='dark'||(!t&&d))document.documentElement.classList.add('dark');
        })();
    </script>

    @livewireScripts

    <script>
        document.addEventListener('livewire:initialized',()=>{
            Livewire.on('theme-changed',d=>{
                document.documentElement.classList.toggle('dark',d.theme==='dark');
                localStorage.setItem('theme',d.theme);
            });
        });
    </script>
</body>
</html>

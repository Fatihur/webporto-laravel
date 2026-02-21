<div x-data="{
    notifications: [],
    nextId: 0,
    init() {
        // Support both Livewire v2 and v3 syntax
        if (typeof Livewire !== 'undefined' && Livewire.on) {
            Livewire.on('notify', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                this.add(data.type, data.message);
            });
            Livewire.on('redirect-to-blog', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                if (data.url) {
                    setTimeout(() => window.open(data.url, '_blank'), 500);
                }
            });
        }
        // Also listen for browser events
        window.addEventListener('notify', (event) => {
            const data = event.detail || event;
            this.add(data.type, data.message);
        });
        window.addEventListener('redirect-to-blog', (event) => {
            const data = event.detail || event;
            if (data.url) {
                setTimeout(() => window.open(data.url, '_blank'), 500);
            }
        });
    },
    add(type, message) {
        this.nextId += 1;
        const id = `${Date.now()}-${this.nextId}`;
        this.notifications.push({ id, type, message });
        setTimeout(() => this.remove(id), 5000);
    },
    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}" x-cloak class="fixed top-4 right-4 z-50 space-y-2">
    <template x-for="notification in notifications" :key="notification.id ?? `${notification.type}-${notification.message}`">
        <div x-show.transition="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             :class="{
                'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950': notification.type === 'success',
                'bg-red-500 text-white': notification.type === 'error',
                'bg-amber-500 text-white': notification.type === 'warning',
                'bg-blue-500 text-white': notification.type === 'info'
             }"
             class="px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 min-w-[300px]"
        >
            <!-- Success Icon -->
            <template x-if="notification.type === 'success'">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            </template>

            <!-- Error Icon -->
            <template x-if="notification.type === 'error'">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="m15 9-6 6"/>
                    <path d="m9 9 6 6"/>
                </svg>
            </template>

            <!-- Warning Icon -->
            <template x-if="notification.type === 'warning'">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/>
                    <path d="M12 9v4"/>
                    <path d="M12 17h.01"/>
                </svg>
            </template>

            <!-- Info Icon -->
            <template x-if="notification.type === 'info'">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4"/>
                    <path d="M12 8h.01"/>
                </svg>
            </template>

            <span class="font-bold" x-text="notification.message || notification"></span>

            <button @click="remove(notification.id)" class="ml-auto opacity-70 hover:opacity-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

<?php require_once APPPATH . 'views/layout/header.php'; ?>

<!-- FullCalendar CSS & JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<!-- AlpineJS -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    /* Light Mode Calendar Customizations */
    .fc-theme-standard .fc-scrollgrid { border-color: #e2e8f0; }
    .fc-theme-standard td, .fc-theme-standard th { border-color: #e2e8f0; }
    .fc .fc-col-header-cell-cushion { color: #1e293b; font-weight: 600; padding: 12px; }
    .fc .fc-daygrid-day-number { color: #475569; padding: 8px; }
    .fc .fc-daygrid-day.fc-day-today { background-color: rgba(37, 99, 235, 0.05) !important; }
    .fc .fc-button-primary {
        background-color: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    .fc .fc-button-primary:hover {
        background-color: #e2e8f0 !important;
        border-color: #cbd5e1 !important;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active, 
    .fc .fc-button-primary:not(:disabled):active {
        background-color: #cbd5e1 !important;
        border-color: #94a3b8 !important;
    }
    .fc-event {
        border: none !important;
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.85em;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        color: #ffffff !important;
    }
    
    /* Fix List View text color (make it dark) */
    .fc-theme-standard .fc-list-day-cushion {
        background-color: #f8fafc;
        color: #1e293b;
    }
    .fc-list-event .fc-list-event-title a,
    .fc-list-event .fc-list-event-time {
        color: #1e293b !important;
    }
    .fc-list-event:hover td {
        background-color: #f1f5f9 !important;
    }
    
    /* Mobile Responsive Overrides for Calendar Toolbar */
    @media (max-width: 768px) {
        .fc .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }
        .fc .fc-toolbar-title {
            font-size: 1.1rem !important;
        }
        .fc .fc-button {
            padding: 0.2rem 0.5rem !important;
            font-size: 0.8rem !important;
        }
        /* Force calendar to have a minimum width so it triggers horizontal scroll instead of squishing */
        .fc-scrollgrid, .fc-list-table {
            min-width: 600px;
        }
    }
    
    /* Popover (More Events) Styling */
    .fc-theme-standard .fc-popover {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        z-index: 50;
        width: 300px !important; /* Make popover larger */
    }
    .fc-theme-standard .fc-popover-header {
        background: #f8fafc;
        padding: 10px 14px;
        font-weight: bold;
        color: #1e293b;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .fc-theme-standard .fc-popover-body {
        padding: 10px;
    }
    .fc-popover-body .fc-daygrid-event {
        color: #1e293b !important; /* Ensure text is dark inside the popover */
        padding: 4px 8px;
        margin-bottom: 4px;
        background-color: #f1f5f9;
        border-left: 4px solid var(--fc-event-border-color, #3788d8) !important;
    }
</style>

<div x-data="scheduleApp()" x-init="initCalendar()" class="min-h-[600px] h-full flex-1 flex flex-col md:flex-row gap-6 text-slate-800">
    
    <!-- Sidebar -->
    <div class="w-full md:w-64 flex flex-col gap-6 shrink-0">
        <button @click="openModal()" class="w-full py-3 bg-edrive-accent hover:bg-blue-600 text-white font-semibold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> Agenda Baru
        </button>

        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex-1">
            <h3 class="font-semibold text-slate-800 mb-4 text-lg">Filter Agenda</h3>
            
            <!-- Search -->
            <div class="relative mb-6">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" x-model="searchQuery" @input="filterEvents()" 
                       class="w-full pl-10 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-edrive-accent focus:border-edrive-accent outline-none"
                       placeholder="Cari judul...">
            </div>

            <!-- Categories -->
            <div class="space-y-2">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Visibilitas</p>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" value="private" x-model="filters.visibility" @change="filterEvents()" class="w-4 h-4 rounded border-slate-300 text-edrive-accent focus:ring-edrive-accent bg-white">
                    <span class="text-sm text-slate-600 group-hover:text-slate-900"><i class="fa-solid fa-lock text-slate-400 w-4"></i> Private Saya</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" value="drive" x-model="filters.visibility" @change="filterEvents()" class="w-4 h-4 rounded border-slate-300 text-edrive-accent focus:ring-edrive-accent bg-white">
                    <span class="text-sm text-slate-600 group-hover:text-slate-900"><i class="fa-solid fa-users text-slate-400 w-4"></i> Drive Shared</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" value="public" x-model="filters.visibility" @change="filterEvents()" class="w-4 h-4 rounded border-slate-300 text-edrive-accent focus:ring-edrive-accent bg-white">
                    <span class="text-sm text-slate-600 group-hover:text-slate-900"><i class="fa-solid fa-globe text-slate-400 w-4"></i> Public</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Calendar Area -->
    <div class="flex-1 bg-white border border-slate-200 rounded-xl p-5 shadow-sm overflow-x-auto flex flex-col">
        <div id="calendar" class="flex-1 text-slate-800 min-w-[600px] md:min-w-0"></div>
    </div>

    <!-- Modal Form -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center">
        <!-- Backdrop -->
        <div x-show="isModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeModal()"></div>
        
        <!-- Modal Content -->
        <div x-show="isModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/80">
                <h3 class="text-lg font-bold text-slate-800" x-text="form.id ? 'Edit Agenda' : 'Agenda Baru'"></h3>
                <div class="flex items-center gap-2">
                    <button x-show="form.id" @click="deleteEvent()" class="w-8 h-8 rounded-full hover:bg-red-50 text-red-500 flex items-center justify-center transition" title="Hapus Agenda">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    <button @click="closeModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <form id="eventForm" @submit.prevent="saveEvent" class="space-y-5">
                    
                    <div>
                        <input type="text" x-model="form.title" required placeholder="Tambahkan Judul" 
                               class="w-full text-2xl bg-transparent border-0 border-b-2 border-transparent hover:border-slate-300 focus:border-edrive-accent focus:ring-0 text-slate-800 placeholder-slate-400 px-0 transition-colors font-medium">
                    </div>

                    <div class="flex gap-4 items-center">
                        <i class="fa-regular fa-clock text-slate-400 w-5 text-center"></i>
                        <div class="flex-1 grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-slate-500 mb-1 block">Mulai</label>
                                <div class="flex gap-2">
                                    <input type="date" x-model="form.start_date" required class="flex-1 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-edrive-accent">
                                    <input type="time" x-model="form.start_time" x-show="!form.all_day" class="w-24 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-edrive-accent">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-slate-500 mb-1 block">Selesai</label>
                                <div class="flex gap-2">
                                    <input type="date" x-model="form.end_date" required class="flex-1 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-edrive-accent">
                                    <input type="time" x-model="form.end_time" x-show="!form.all_day" class="w-24 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-edrive-accent">
                                </div>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer mt-5">
                            <input type="checkbox" x-model="form.all_day" class="rounded border-slate-300 text-edrive-accent focus:ring-edrive-accent">
                            <span class="text-sm text-slate-600">Seharian</span>
                        </label>
                    </div>

                    <div class="flex gap-4 items-start">
                        <i class="fa-solid fa-align-left text-slate-400 w-5 text-center mt-3"></i>
                        <textarea x-model="form.description" rows="3" placeholder="Tambahkan deskripsi..." 
                                  class="w-full bg-white border border-slate-300 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-edrive-accent resize-none"></textarea>
                    </div>

                    <div class="flex gap-4 items-center">
                        <i class="fa-solid fa-location-dot text-slate-400 w-5 text-center"></i>
                        <input type="text" x-model="form.location" placeholder="Tambahkan lokasi" 
                               class="w-full bg-white border border-slate-300 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-edrive-accent">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="flex gap-4 items-center">
                            <i class="fa-solid fa-eye text-slate-400 w-5 text-center"></i>
                            <select x-model="form.visibility" class="w-full bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-edrive-accent">
                                <option value="private">Private (Hanya Saya)</option>
                                <option value="drive">Drive (Shared)</option>
                                <option value="public">Public (Semua User)</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-4 items-center" x-show="form.visibility === 'drive'">
                            <i class="fa-solid fa-folder text-slate-400 w-5 text-center"></i>
                            <select x-model="form.drive_id" class="w-full bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-edrive-accent">
                                <option value="">Pilih Drive...</option>
                                <?php foreach($drives as $drive): ?>
                                    <option value="<?= $drive->id ?>"><?= htmlspecialchars($drive->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4 items-center">
                        <i class="fa-solid fa-palette text-slate-400 w-5 text-center"></i>
                        <div class="flex gap-2">
                            <template x-for="c in colors">
                                <button type="button" @click="form.color = c" 
                                        :class="{'ring-2 ring-slate-800 ring-offset-2 ring-offset-white': form.color === c}"
                                        class="w-6 h-6 rounded-full transition-transform hover:scale-110" 
                                        :style="'background-color: ' + c"></button>
                            </template>
                        </div>
                    </div>

                </form>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/80 flex justify-end gap-3">
                <button type="button" @click="closeModal()" class="px-5 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-200 transition">Batal</button>
                <button type="button" @click="saveEvent()" class="px-5 py-2 rounded-lg text-sm font-medium bg-edrive-accent hover:bg-blue-600 text-white shadow shadow-blue-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function scheduleApp() {
    return {
        calendar: null,
        isModalOpen: false,
        searchQuery: '',
        filters: {
            visibility: ['private', 'drive', 'public']
        },
        colors: ['#3788d8', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#64748B'],
        form: {
            id: null,
            title: '',
            description: '',
            location: '',
            start_date: '',
            start_time: '09:00',
            end_date: '',
            end_time: '10:00',
            all_day: false,
            visibility: 'private',
            drive_id: '',
            color: '#3788d8'
        },
        
        initCalendar() {
            // Document background body color to match Dark Mode for this page
            document.body.style.backgroundColor = '#0f172a'; // slate-900
            
            let calendarEl = document.getElementById('calendar');
            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                events: '<?= site_url('schedule/api_events') ?>',
                editable: true, // Allow drag & drop
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                
                select: (arg) => {
                    this.openModal({
                        start_date: arg.startStr.split('T')[0],
                        end_date: arg.endStr.split('T')[0],
                        all_day: arg.allDay
                    });
                    this.calendar.unselect();
                },
                eventClick: (arg) => {
                    let ev = arg.event;
                    let props = ev.extendedProps;
                    
                    this.openModal({
                        id: ev.id,
                        title: ev.title,
                        start_date: ev.start ? ev.start.toISOString().split('T')[0] : '',
                        start_time: ev.start && !ev.allDay ? ev.start.toTimeString().substring(0,5) : '',
                        end_date: ev.end ? ev.end.toISOString().split('T')[0] : (ev.start ? ev.start.toISOString().split('T')[0] : ''),
                        end_time: ev.end && !ev.allDay ? ev.end.toTimeString().substring(0,5) : '',
                        all_day: ev.allDay,
                        description: props.description,
                        location: props.location,
                        visibility: props.visibility,
                        drive_id: props.drive_id,
                        color: ev.backgroundColor
                    });
                },
                eventDrop: (arg) => this.quickUpdate(arg.event),
                eventResize: (arg) => this.quickUpdate(arg.event)
            });
            this.calendar.render();
        },
        
        openModal(data = null) {
            this.resetForm();
            if (data) {
                this.form = { ...this.form, ...data };
            }
            this.isModalOpen = true;
        },
        
        closeModal() {
            this.isModalOpen = false;
        },
        
        resetForm() {
            this.form = {
                id: null, title: '', description: '', location: '',
                start_date: new Date().toISOString().split('T')[0],
                start_time: '09:00',
                end_date: new Date().toISOString().split('T')[0],
                end_time: '10:00',
                all_day: false, visibility: 'private', drive_id: '', color: '#3788d8'
            };
        },
        
        async saveEvent() {
            if (!this.form.title) {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Judul harus diisi!', background: '#1e293b', color: '#fff' });
                return;
            }
            
            let formData = new FormData();
            for (let key in this.form) {
                formData.append(key, this.form[key]);
            }
            
            try {
                let res = await fetch('<?= site_url('schedule/api_save') ?>', { method: 'POST', body: formData });
                let json = await res.json();
                
                if (json.status) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: json.message, timer: 1500, showConfirmButton: false, background: '#1e293b', color: '#fff' });
                    this.closeModal();
                    this.calendar.refetchEvents();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: json.message, background: '#1e293b', color: '#fff' });
                }
            } catch (e) {
                console.error(e);
            }
        },
        
        async quickUpdate(event) {
            let formData = new FormData();
            formData.append('id', event.id);
            formData.append('title', event.title);
            formData.append('start_date', event.start ? event.start.toISOString().split('T')[0] : '');
            if(event.start && !event.allDay) formData.append('start_time', event.start.toTimeString().substring(0,5));
            formData.append('end_date', event.end ? event.end.toISOString().split('T')[0] : (event.start ? event.start.toISOString().split('T')[0] : ''));
            if(event.end && !event.allDay) formData.append('end_time', event.end.toTimeString().substring(0,5));
            formData.append('all_day', event.allDay ? 1 : 0);
            
            // Retain existing extended props
            formData.append('description', event.extendedProps.description || '');
            formData.append('location', event.extendedProps.location || '');
            formData.append('visibility', event.extendedProps.visibility || 'private');
            formData.append('drive_id', event.extendedProps.drive_id || '');
            formData.append('color', event.backgroundColor);
            
            try {
                let res = await fetch('<?= site_url('schedule/api_save') ?>', { method: 'POST', body: formData });
                let json = await res.json();
                if (!json.status) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: json.message, background: '#1e293b', color: '#fff' });
                    event.revert();
                }
            } catch(e) {
                event.revert();
            }
        },
        
        async deleteEvent() {
            if (!this.form.id) return;
            
            let result = await Swal.fire({
                title: 'Hapus Agenda?',
                text: "Agenda ini akan dipindahkan ke Recycle Bin.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Ya, Hapus!',
                background: '#1e293b', color: '#fff'
            });
            
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('id', this.form.id);
                
                try {
                    let res = await fetch('<?= site_url('schedule/api_delete') ?>', { method: 'POST', body: formData });
                    let json = await res.json();
                    
                    if (json.status) {
                        Swal.fire({ icon: 'success', title: 'Terhapus', text: json.message, timer: 1500, showConfirmButton: false, background: '#1e293b', color: '#fff' });
                        this.closeModal();
                        this.calendar.refetchEvents();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: json.message, background: '#1e293b', color: '#fff' });
                    }
                } catch(e) {}
            }
        },

        filterEvents() {
            // Frontend filtering hack for FullCalendar
            let events = this.calendar.getEvents();
            events.forEach(ev => {
                let props = ev.extendedProps;
                let matchVisibility = this.filters.visibility.includes(props.visibility);
                let matchSearch = ev.title.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  (props.description && props.description.toLowerCase().includes(this.searchQuery.toLowerCase()));
                
                ev.setProp('display', (matchVisibility && matchSearch) ? 'auto' : 'none');
            });
        }
    }
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>

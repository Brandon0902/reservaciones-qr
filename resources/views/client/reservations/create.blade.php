<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#6d28d9] text-white shadow">
          <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M12 2l7 4v6c0 5-3 8-7 10C8 20 5 17 5 12V6l7-4zM7 8v4c0 3 2 5 5 6c3-1 5-3 5-6V8l-5-3l-5 3z"/></svg>
        </span>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Crear reservación</h2>
      </div>

      <a href="{{ route('client.dashboard') }}"
         class="px-3 py-1.5 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300
                dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
        ← Volver
      </a>
    </div>
  </x-slot>

  <style>
    :root { --brand:#6d28d9; --brand-2:#a78bfa; }
    .flatpickr-calendar{ background:#0f172a; border:1px solid rgba(255,255,255,.08); box-shadow:0 10px 30px rgba(0,0,0,.5); color:#e5e7eb; border-radius:14px; overflow:hidden; }
    .flatpickr-months, .flatpickr-weekdays{ background:#111827; }
    .flatpickr-months .flatpickr-month .flatpickr-current-month { color:#fff; }
    .flatpickr-current-month .cur-month,
    .flatpickr-current-month .numInputWrapper input.cur-year,
    .flatpickr-current-month select.flatpickr-monthDropdown-months{ color:#fff !important; background:transparent; }
    .flatpickr-months .flatpickr-prev-month svg,.flatpickr-months .flatpickr-next-month svg{ fill:#e5e7eb; }
    .flatpickr-months .flatpickr-prev-month:hover svg,.flatpickr-months .flatpickr-next-month:hover svg{ fill:#fff; }
    .flatpickr-day{ color:#cbd5e1; border-radius:10px; }
    .flatpickr-day.disabled,.flatpickr-day.disabled:hover{ color:#64748b; opacity:.55; cursor:not-allowed; }
    .flatpickr-day.today{ border:1px solid rgba(167,139,250,.6); }
    .flatpickr-day.selected,.flatpickr-day.startRange,.flatpickr-day.endRange{ background:#6d28d9 !important; color:#fff !important; }
    .fp-muted{ background:transparent !important; color:#64748b !important; }
    .fp-busy{ background:#7f1d1d !important; color:#fecaca !important; }
    .fp-free{ background:#064e3b !important; color:#bbf7d0 !important; }

    /* Apariencia y bloqueo de inputs de hora */
    .readonly-time { pointer-events:none; opacity:.75; }
    .shift-card[aria-disabled="true"] { opacity:.45; filter:grayscale(0.3); pointer-events:none; }
    .hidden-card { display:none; }
  </style>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

  <div class="bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 lg:py-14"
         x-data="reservaForm({
            dayBase: {{ (float)$dayBase }},
            nightBase: {{ (float)$nightBase }},
            extras: {{ $extras->map(fn($e)=>['id'=>$e->id,'name'=>$e->name,'day_price'=>(float)$e->day_price,'night_price'=>(float)$e->night_price])->values()->toJson() }}
         })"
         x-init="init()">

      {{-- Alerts --}}
      @if ($errors->any())
        <div class="mb-6 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-rose-100">
          <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif
      @if (session('error'))
        <div class="mb-6 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-rose-100">
          {{ session('error') }}
        </div>
      @endif
      <template x-if="clientError">
        <div class="mb-6 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-rose-100" x-text="clientError"></div>
      </template>

      <div class="mb-6">
        <div class="inline-flex items-center gap-2 text-xs tracking-wide uppercase text-[--brand-2]">
          <span class="h-1.5 w-1.5 rounded-full bg-[--brand-2]"></span> Detalles del evento
        </div>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold">Completa tu reservación</h1>
        <p class="mt-1 text-slate-300">Elige horario, agrega extras y confirma tu total.</p>
      </div>

      <div class="grid lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- ===== Form ===== --}}
        <form method="POST" action="{{ route('client.reservations.store') }}" class="lg:col-span-2 space-y-6" x-ref="form">
          @csrf

          {{-- Datos base --}}
          <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-lg font-semibold">Información del evento</div>
            <div class="mt-4 grid sm:grid-cols-2 gap-4">
              <div>
                <x-input-label for="event_name" value="Nombre del evento" class="text-slate-300" />
                <x-text-input id="event_name" name="event_name" type="text"
                              class="mt-1 block w-full dark:bg-slate-900/40" required maxlength="120" />
              </div>

              {{-- Fecha: visible + oculto ISO --}}
              <div>
                <x-input-label for="date_display" value="Fecha" class="text-slate-300" />
                <input id="date_display" type="text"
                       class="mt-1 block w-full rounded-md border border-white/10 bg-slate-900/40 px-3 py-2"
                       placeholder="dd/mm/aaaa" required autocomplete="off" />
                <input id="date_iso" name="date" type="hidden" />
                <small class="text-slate-400 text-xs">
                  Debe reservarse con 8 días de anticipación. <strong>Si un turno está ocupado, solo verás el turno disponible.</strong>
                </small>
              </div>

              <div>
                <x-input-label for="start_time" value="Hora inicio" class="text-slate-300" />
                <x-text-input id="start_time" name="start_time" type="time"
                              class="mt-1 block w-full dark:bg-slate-900/40 readonly-time" required readonly />
              </div>
              <div>
                <x-input-label for="end_time" value="Hora fin" class="text-slate-300" />
                <x-text-input id="end_time" name="end_time" type="time"
                              class="mt-1 block w-full dark:bg-slate-900/40 readonly-time" required readonly />
              </div>
              <div>
                <x-input-label for="headcount" value="No. de personas" class="text-slate-300" />
                <x-text-input id="headcount"
                              name="headcount"
                              type="number"
                              min="1"
                              max="70"
                              step="1"
                              inputmode="numeric"
                              class="mt-1 block w-full dark:bg-slate-900/40"
                              required
                              x-ref="headcount"
                              @keydown="blockInvalidNumberKeys($event)"
                              @input="validateHeadcount()"
                              @paste.prevent />
                <p class="mt-1 text-xs text-rose-300" x-show="headcountError" x-text="headcountError"></p>
                <small class="text-slate-400 text-xs">Capacidad máxima: 70 personas.</small>
              </div>

              {{-- Horario (turno) --}}
              <div>
                <x-input-label value="Horario" class="text-slate-300" />
                <div class="mt-2 grid grid-cols-2 gap-3">
                  <label id="card-day" class="shift-card rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 px-3 py-3 cursor-pointer flex items-start gap-2">
                    <input id="shift_day" type="radio" name="shift" value="day" x-model="shift" class="mt-1" @change="applyShiftTimes(true)">
                    <div>
                      <div class="font-medium">Matutino</div>
                      <div class="text-xs text-slate-400">(10am–4pm)</div>
                      <div class="mt-1 text-xs text-emerald-300">Base: <span x-text="money(dayBase)"></span></div>
                    </div>
                  </label>

                  <label id="card-night" class="shift-card rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 px-3 py-3 cursor-pointer flex items-start gap-2">
                    <input id="shift_night" type="radio" name="shift" value="night" x-model="shift" class="mt-1" @change="applyShiftTimes(true)">
                    <div>
                      <div class="font-medium">Nocturno</div>
                      <div class="text-xs text-slate-400">(7pm–2am)</div>
                      <div class="mt-1 text-xs text-emerald-300">Base: <span x-text="money(nightBase)"></span></div>
                    </div>
                  </label>
                </div>
                <p class="mt-1 text-xs text-slate-400">Los campos de hora están fijos por turno.</p>
              </div>
            </div>
          </div>

          {{-- Extras (1 unidad por seleccionado) --}}
          <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-lg font-semibold">Servicios extra</div>
            <div class="mt-4 grid sm:grid-cols-2 xl:grid-cols-3 gap-3">
              <template x-for="(ex, idx) in pickableExtras" :key="ex.id">
                <label class="rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 px-3 py-3 cursor-pointer flex items-start gap-3">
                  <input type="checkbox"
                         class="mt-1 rounded"
                         :value="ex.id"
                         :name="`extras[${idx}][id]`"
                         @change="toggleExtra(idx)">
                  <div class="flex-1">
                    <div class="font-medium" x-text="ex.name"></div>
                    <div class="text-xs text-slate-400">
                      Mat: $<span x-text="ex.day_price.toFixed(2)"></span> ·
                      Noc: $<span x-text="ex.night_price.toFixed(2)"></span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">* Se cobra una unidad por servicio seleccionado.</div>
                  </div>
                </label>
              </template>
            </div>
          </div>

          {{-- Origen / Notas --}}
          <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-lg font-semibold">Más detalles</div>
            <div class="mt-4 grid sm:grid-cols-2 gap-4">
              <div>
                <x-input-label for="source" value="¿Dónde nos conociste?" class="text-slate-300" />
                <select id="source" name="source" class="mt-1 block w-full rounded border-white/10 bg-slate-900/40">
                  @foreach($sourceOptions as $k=>$v)
                    <option value="{{ $k }}">{{ $v }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <x-input-label for="discount_amount" value="Descuento (opcional)" class="text-slate-300" />
                <x-text-input id="discount_amount" name="discount_amount" type="number" step="0.01" min="0"
                              class="mt-1 block w-full dark:bg-slate-900/40"
                              x-model.number="discount" @input="recalc()" />
              </div>
              <div class="sm:col-span-2">
                <x-input-label for="notes" value="Notas" class="text-slate-300" />
                <textarea id="notes" name="notes" rows="3"
                          class="mt-1 block w-full rounded border-white/10 bg-slate-900/40"></textarea>
              </div>
            </div>
          </div>

          {{-- Botones --}}
          <div class="flex items-center justify-end gap-3">
            <a href="{{ route('client.dashboard') }}" class="px-4 py-2 rounded-xl border border-white/10 hover:bg-white/5">Cancelar</a>
            <x-primary-button class="rounded-xl px-5" x-bind:disabled="submitting">
              <span x-show="!submitting">Confirmar reservación</span>
              <span x-show="submitting" class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity=".25"/><path d="M22 12a10 10 0 0 1-10 10" fill="currentColor"/></svg>
                Procesando…
              </span>
            </x-primary-button>
          </div>
        </form>

        {{-- Resumen --}}
        <aside class="lg:sticky lg:top-24 space-y-4">
          <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-lg font-semibold">Resumen</div>
            <div class="mt-3 space-y-2 text-sm">
              <div class="flex justify-between"><span class="text-slate-300">Base</span><span class="font-medium" x-text="money(base)"></span></div>
              <div class="flex justify-between"><span class="text-slate-300">Extras</span><span class="font-medium" x-text="money(extras)"></span></div>
              <div class="flex justify-between"><span class="text-slate-300">Descuento</span><span class="font-medium" x-text="money(discount)"></span></div>
              <div class="h-px my-2 bg-white/10"></div>
              <div class="flex justify-between text-base"><span class="font-semibold">Total (MXN)</span><span class="font-extrabold" x-text="money(total)"></span></div>
            </div>
          </div>

          <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-100">
            <div class="text-sm">
              El total puede variar según disponibilidad y validación de fecha.
              Recibirás confirmación por correo.
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>

  <script>
  function reservaForm({ dayBase, nightBase, extras }) {
    return {
      // Precios
      dayBase, nightBase,

      // Estado
      shift: 'day',
      discount: 0,
      submitting: false,
      clientError: '',
      pickableExtras: extras.map(e => ({...e, checked: false})),
      headcountError: '', // ← nuevo estado

      // Horarios por defecto
      dayStart:   '10:00',
      dayEnd:     '16:00',
      nightStart: '19:00',
      nightEnd:   '02:00',

      // Ocupación por fecha
      busyMap: {},          // { 'YYYY-MM-DD': ['day','night'] }
      fullSet: new Set(),   // dias con ambos turnos ocupados

      init(){
        this.applyShiftTimes(true);
        this.initDatepicker();
        this.lockTimeInputs();
        this.validateHeadcount(); // ← valida al iniciar
      },

      lockTimeInputs(){
        const st = document.getElementById('start_time');
        const et = document.getElementById('end_time');
        if (!st || !et) return;
        st.readOnly = true; et.readOnly = true;
        st.classList.add('readonly-time'); et.classList.add('readonly-time');
        ['keydown','wheel','mousedown','focus','click'].forEach(evt => {
          st.addEventListener(evt, e => e.preventDefault(), {passive:false});
          et.addEventListener(evt, e => e.preventDefault(), {passive:false});
        });
      },

      applyShiftTimes(force = false){
        const st = document.getElementById('start_time');
        const et = document.getElementById('end_time');
        if (!st || !et) return;

        const s = (this.shift === 'day') ? this.dayStart : this.nightStart;
        const e = (this.shift === 'day') ? this.dayEnd   : this.nightEnd;

        if (force || true) {
          st.value = s;
          et.value = e;
        }
      },

      updateShiftCardsFor(dateYmd){
        const turnsBusy = this.busyMap?.[dateYmd] || [];
        const dayBusy   = turnsBusy.includes('day');
        const nightBusy = turnsBusy.includes('night');

        const cardDay     = document.getElementById('card-day');
        const cardNight   = document.getElementById('card-night');
        const inputDay    = document.getElementById('shift_day');
        const inputNight  = document.getElementById('shift_night');

        if (cardDay) {
          cardDay.classList.toggle('hidden-card', dayBusy);
          cardDay.setAttribute('aria-disabled', dayBusy ? 'true' : 'false');
        }
        if (cardNight) {
          cardNight.classList.toggle('hidden-card', nightBusy);
          cardNight.setAttribute('aria-disabled', nightBusy ? 'true' : 'false');
        }

        if (inputDay)   inputDay.disabled   = !!dayBusy;
        if (inputNight) inputNight.disabled = !!nightBusy;

        if (dayBusy && !nightBusy) {
          this.shift = 'night';
          if (inputNight) inputNight.checked = true;
        } else if (!dayBusy && nightBusy) {
          this.shift = 'day';
          if (inputDay) inputDay.checked = true;
        }

        this.applyShiftTimes(true);
      },

      async initDatepicker(){
        const inputDisplay = document.getElementById('date_display');
        const inputISO     = document.getElementById('date_iso');
        if (!inputDisplay || !inputISO) return;

        const api = @json(route('client.reservations.booked-dates'));
        let data = {};
        try {
          const res = await fetch(api, { headers: { 'Accept': 'application/json' }});
          data = await res.json();
        } catch (_) {}

        this.busyMap = (data.busy && typeof data.busy === 'object') ? data.busy : {};
        this.fullSet = new Set(Array.isArray(data.full) ? data.full : []);

        function parseYmd(s) {
          if (typeof s !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(s)) return null;
          const [Y,M,D] = s.split('-').map(Number);
          return new Date(Y, (M||1)-1, D||1);
        }

        const todayServer  = parseYmd(data.today) || new Date();
        const minDaysAhead = Number(data.min_days_ahead ?? 8);
        const minDate      = new Date(todayServer.getFullYear(), todayServer.getMonth(), todayServer.getDate() + minDaysAhead);

        const ymdKey = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

        flatpickr.localize(flatpickr.l10ns.es);

        const fp = flatpickr(inputDisplay, {
          dateFormat: 'Y-m-d',
          altInput: true,
          altFormat: 'd/m/Y',
          allowInput: false,
          disableMobile: true,
          defaultDate: null,
          minDate,
          disable: [ (date) => date < minDate || this.fullSet.has(ymdKey(date)) ],
          onDayCreate: (_, __, instance, dayElem) => {
            const d = dayElem.dateObj;
            const key = ymdKey(d);
            if (!instance.isEnabled(d)) {
              dayElem.classList.add(this.fullSet.has(key) ? 'fp-busy' : 'fp-muted', 'flatpickr-disabled');
              dayElem.setAttribute('aria-disabled','true');
              return;
            }
            dayElem.classList.add('fp-free');
          },
          onReady: (selectedDates, dateStr) => {
            if (dateStr) this.updateShiftCardsFor(dateStr);
          },
          onChange: (selectedDates, dateStr) => {
            inputISO.value = dateStr || '';
            if (dateStr) this.updateShiftCardsFor(dateStr);
          }
        });

        const form = document.querySelector('form[x-ref="form"]') || document.querySelector('form');
        form?.addEventListener('submit', (ev) => {
          if (fp && fp.selectedDates && fp.selectedDates[0]) {
            inputISO.value = fp.formatDate(fp.selectedDates[0], 'Y-m-d');
          }
          const iso = (inputISO.value || '').trim();
          if (!/^\d{4}-\d{2}-\d{2}$/.test(iso)) {
            ev.preventDefault();
            const errorBox = document.createElement('div');
            errorBox.className = 'mb-6 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-rose-100';
            errorBox.textContent = 'Fecha inválida. Selecciona una fecha del calendario.';
            form.parentElement.prepend(errorBox);
            inputDisplay.focus();
            return false;
          }

          // Validar número de personas antes de enviar
          this.validateHeadcount();
          const head = this.$refs.headcount;
          if (this.headcountError || !head || !head.checkValidity()) {
            ev.preventDefault();
            head?.reportValidity?.();
            head?.focus?.();
            return false;
          }

          this.applyShiftTimes(true);
          this.submitting = true;
        });
      },

      // Bloquea teclas inválidas para campo numérico
      blockInvalidNumberKeys(e) {
        const bad = ['e','E','+','-','.'];
        if (bad.includes(e.key)) e.preventDefault();
      },

      // Valida rango permitido para número de personas
      validateHeadcount() {
        const el = this.$refs.headcount;
        if (!el) return;
        const v = Number(el.value);

        if (!Number.isFinite(v) || el.value === '') {
          this.headcountError = 'Ingresa un número válido.';
        } else if (v < 1) {
          this.headcountError = 'El mínimo es 1 persona.';
        } else if (v > 70) {
          this.headcountError = 'No se permite más de 70 personas por capacidad del salón.';
        } else {
          this.headcountError = '';
        }

        if (this.headcountError) {
          el.setCustomValidity(this.headcountError);
        } else {
          el.setCustomValidity('');
        }
      },

      // Cálculos
      get base(){ return this.shift === 'day' ? this.dayBase : this.nightBase; },
      get extras(){
        return this.pickableExtras.reduce((acc, e) => {
          if (!e.checked) return acc;
          const unit = this.shift === 'day' ? e.day_price : e.night_price;
          return acc + unit;
        }, 0);
      },
      get total(){ return Math.max(0, this.base + this.extras - (Number(this.discount)||0)); },
      money(v){ return '$' + Number(v||0).toFixed(2); },
      toggleExtra(idx){ this.pickableExtras[idx].checked = !this.pickableExtras[idx].checked; this.recalc(); },
      recalc(){},
    }
  }
</script>

</x-app-layout>

<x-filament-panels::page>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- QR Code Scanner --}}
        <x-filament::section heading="Scan QR Code">
            <div x-data="qrScanner()" class="space-y-4">
                
                {{-- Camera reader container (hidden by default) --}}
                <div id="qr-reader" x-show="cameraActive" class="w-full rounded-lg overflow-hidden border-2 border-gray-300 mb-4"></div>

                {{-- Text input for manual entry or camera result --}}
                <form wire:submit="scanByQR" class="space-y-4">
                    {{ $this->qrForm }}
                    
                    {{-- Camera toggle buttons --}}
                    <div class="flex gap-3">
                        <x-filament::button 
                            type="button" 
                            @click="startCamera()" 
                            x-show="!cameraActive"
                            icon="heroicon-o-camera" 
                            class="flex-1">
                            Start Camera Scan
                        </x-filament::button>
                        
                        <x-filament::button 
                            type="button" 
                            @click="stopCamera()" 
                            x-show="cameraActive"
                            color="red"
                            icon="heroicon-o-stop" 
                            class="flex-1">
                            Stop Camera
                        </x-filament::button>
                        
                        <x-filament::button 
                            type="submit" 
                            icon="heroicon-o-qr-code" 
                            class="flex-1">
                            Verify QR Code
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </x-filament::section>

        {{-- Plate Number Lookup --}}
        <x-filament::section heading="Search by Plate Number">
            <form wire:submit="searchByPlate" class="space-y-4">
                {{ $this->plateForm }}
                <x-filament::button type="submit" color="gray" icon="heroicon-o-magnifying-glass" class="w-full">
                    Search Vehicle
                </x-filament::button>
            </form>
        </x-filament::section>

    </div>

    {{-- Result Panel --}}
    @if ($vehicleResult)
        <div class="mt-6" x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start' }))">

            {{-- Access Status Banner --}}
            @if ($accessStatus === 'granted')
                <div class="rounded-2xl bg-green-500 dark:bg-green-600 p-5 mb-4 flex items-center gap-5 shadow-md">
                    <div class="flex-shrink-0 bg-white/20 rounded-full p-2.5">
                        <x-heroicon-o-check-circle class="w-12 h-12 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-3xl font-black text-white tracking-wide leading-none">ACCESS GRANTED</p>
                        <p class="text-green-100 text-sm mt-1">Valid sticker &mdash; Allow vehicle entry</p>
                    </div>
                    <x-filament::button wire:click="clearResult" color="gray" size="sm" icon="heroicon-o-x-mark" class="flex-shrink-0">
                        Clear
                    </x-filament::button>
                </div>
            @else
                <div class="rounded-2xl bg-red-600 dark:bg-red-700 p-5 mb-4 flex items-center gap-5 shadow-md">
                    <div class="flex-shrink-0 bg-white/20 rounded-full p-2.5">
                        <x-heroicon-o-x-circle class="w-12 h-12 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-3xl font-black text-white tracking-wide leading-none">ACCESS DENIED</p>
                        <p class="text-red-100 text-sm mt-1">Invalid or expired sticker &mdash; Do not allow entry</p>
                    </div>
                    <x-filament::button wire:click="clearResult" color="gray" size="sm" icon="heroicon-o-x-mark" class="flex-shrink-0">
                        Clear
                    </x-filament::button>
                </div>
            @endif

            {{-- 3-column detail cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Card 1: Vehicle --}}
                <div class="bg-white dark:bg-gray-900 rounded-xl border @if($accessStatus === 'granted') border-green-300 dark:border-green-700 @else border-red-300 dark:border-red-700 @endif p-5 flex flex-col items-center gap-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 self-start">Vehicle</p>

                    {{-- License plate --}}
                    <div class="w-full bg-white border-4 border-gray-800 dark:border-gray-300 rounded-lg px-5 py-3 text-center shadow-inner">
                        <p class="text-xs text-gray-500 font-semibold tracking-widest uppercase mb-0.5">Malaysia</p>
                        <p class="text-3xl font-black font-mono tracking-widest text-gray-900">{{ $vehicleResult['plate'] }}</p>
                    </div>

                    <div class="w-full space-y-1.5 text-sm text-center text-gray-600 dark:text-gray-400">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $vehicleResult['manufacturer'] }} {{ $vehicleResult['model'] }}</p>
                        <p>{{ $vehicleResult['color'] }} &middot; {{ $vehicleResult['type'] }}</p>
                    </div>
                </div>

                {{-- Card 2: Owner --}}
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Owner</p>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 bg-gray-100 dark:bg-gray-800 rounded-full p-2.5 mt-0.5">
                            <x-heroicon-o-user class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900 dark:text-white leading-tight">{{ $vehicleResult['student_name'] }}</p>
                            <p class="text-sm text-gray-500 font-mono mt-0.5">{{ $vehicleResult['matric'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Sticker --}}
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Sticker</p>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                            @if($vehicleResult['sticker_status'] === 'Valid')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 text-sm font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    Valid
                                </span>
                            @elseif($vehicleResult['sticker_status'] === 'No Sticker')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    No Sticker
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400 text-sm font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    {{ $vehicleResult['sticker_status'] }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Valid Until</span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $vehicleResult['valid_until'] }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    function qrScanner() {
        return {
            cameraActive: false,
            scanner: null,

            async startCamera() {
                this.cameraActive = true;
                await this.$nextTick();
                
                this.scanner = new Html5Qrcode("qr-reader");
                
                try {
                    await this.scanner.start(
                        { facingMode: "environment" }, // rear camera
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => this.onQRDetected(decodedText),
                        (error) => { /* ignore errors */ }
                    );
                } catch (err) {
                    console.error('Camera error:', err);
                    @this.call('notifyCameraError');
                    this.cameraActive = false;
                }
            },

            async stopCamera() {
                if (this.scanner) {
                    await this.scanner.stop();
                    this.scanner = null;
                }
                this.cameraActive = false;
            },

            onQRDetected(decodedText) {
                const token = this.extractToken(decodedText);
                
                // Stop camera and set the token
                this.stopCamera();
                
                // Update Livewire component state and submit
                @this.set('data.qr_token', token).then(() => {
                    @this.call('scanByQR');
                });
            },

            extractToken(raw) {
                raw = raw.trim();

                // URL format: https://domain.com/sticker/{uuid}
                if (raw.includes('/sticker/')) {
                    const parts = raw.split('/sticker/');
                    if (parts[1]) {
                        return parts[1].split('?')[0].trim();
                    }
                }

                // Raw UUID (from hardware scanner, no extraction needed)
                return raw;
            }
        };
    }
</script>

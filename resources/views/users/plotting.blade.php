<x-layout>
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-center mb-6">MAC Laboratory Layout</h1>
        
        {{-- Main Layout Container --}}
        <div class="layout-container" style="position: relative; width: 100%; max-width: 1200px; margin: 0 auto;">

            {{-- Stage and TV Section --}}
            <div class="stage" style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
                <div class="door" style="flex: 1; text-align: center; border: 1px solid #000; padding: 5px;">DOOR (EXIT)</div>
                <div class="tv" style="flex: 2; border: 1px solid #000; padding: 5px; text-align: center;">TV</div>
                <div class="door" style="flex: 1; text-align: center; border: 1px solid #000; padding: 5px;">DOOR (ENTRANCE)</div>
            </div>
            <div class="stage-label" style="text-align: center; margin-bottom: 20px;">STAGE</div>

            {{-- Main Layout Box with Square Containers --}}
            <div class="mac-layout" style="display: flex; gap: 150px; justify-content: center; align-items: flex-start;">
                
                {{-- Left Column for MACs (6-10, 16-20, ...) --}}
                <div class="mac-column" style="display: flex; flex-direction: column; gap: 20px;">
                    {{-- Dynamically Render Left Column Rows --}}
                    @for ($start = 5; $start < $macs->count(); $start += 10)
                        @php
                            $leftMacs = $macs->slice($start, 4);
                        @endphp
                        @if ($leftMacs->isNotEmpty())
                            <div class="mac-row-container" style="border: 1px solid #000; padding: 10px;">
                                <div class="mac-row" style="display: flex; gap: 10px; flex-direction: row-reverse;">
                                    @foreach($leftMacs as $mac)
                                        <div class="mac-box {{ $mac->status === 'unavailable' ? 'unavailable' : '' }}" 
                                             style="width: 120px; height: 80px; border: 1px solid #000; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 5px;">
                                            <span style="font-weight: bold;">{{ $mac->mac_number }}</span>
                                            {{-- Display Linked Student Name if available --}}
                                            @if ($mac->linked_student_name)
                                                <span style="font-size: 0.8rem; text-align: center;">{{ $mac->linked_student_name }}</span>
                                            @endif
                                            @if ($mac->status === 'unavailable')
                                                <span style="position: absolute; color: red; font-size: 1.5rem;">X</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endfor
                </div>

                {{-- Right Column for MACs (1-5, 11-15, 21-25, ...) --}}
                <div class="mac-column" style="display: flex; flex-direction: column; gap: 20px;">
                    {{-- Dynamically Render Right Column Rows --}}
                    @for ($start = 0; $start < $macs->count(); $start += 10)
                        @php
                            $rightMacs = $macs->slice($start, 4);
                        @endphp
                        @if ($rightMacs->isNotEmpty())
                            <div class="mac-row-container" style="border: 1px solid #000; padding: 10px;">
                                <div class="mac-row" style="display: flex; gap: 10px; flex-direction: row-reverse;">
                                    @foreach($rightMacs as $mac)
                                        <div class="mac-box {{ $mac->status === 'unavailable' ? 'unavailable' : '' }}" 
                                             style="width: 120px; height: 80px; border: 1px solid #000; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 5px;">
                                            <span style="font-weight: bold;">{{ $mac->mac_number }}</span>
                                            {{-- Display Linked Student Name if available --}}
                                            @if ($mac->linked_student_name)
                                                <span style="font-size: 0.8rem; text-align: center;">{{ $mac->linked_student_name }}</span>
                                            @endif
                                            @if ($mac->status === 'unavailable')
                                                <span style="position: absolute; color: red; font-size: 1.5rem;">X</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>
</x-layout>

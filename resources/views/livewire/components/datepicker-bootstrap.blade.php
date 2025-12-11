<div class="input-group">
    <input 
        type="text" 
        id="{{ $model_name }}" 
        class="form-control"
        style="background-color: white !important; cursor: pointer !important;"
        placeholder="{{ $data_placeholder }}"
        data-format="{{ $format }}"
        data-autoclose="{{ $autoclose ? 'true' : 'false' }}"
        data-today-highlight="{{ $today_highlight ? 'true' : 'false' }}"
        @if($start_date) data-start-date="{{ $start_date }}" @endif
        @if($end_date) data-end-date="{{ $end_date }}" @endif
        datepicker-livewire="{{ $model_name }}"
        readonly
    />
    <span class="input-group-text"><i class="ti ti-calendar"></i></span>
</div>

@push('scripts')
    <script>
        (function() {
            const inputId = '{{ $model_name }}';
            
            function initDatepickerComponent(inputIdModel) {
                const $input = $('#' + inputIdModel);
                
                if ($input.length === 0) return;

                if ($input.data('datepicker')) {
                    $input.off('changeDate');
                    $input.datepicker('destroy');
                }

                const datepickerOptions = {
                    format: $input.attr('data-format') || 'dd/mm/yyyy',
                    autoclose: $input.attr('data-autoclose') === 'true',
                    todayHighlight: $input.attr('data-today-highlight') === 'true',
                    orientation: 'bottom auto'
                };

                if ($input.attr('data-start-date')) {
                    datepickerOptions.startDate = new Date($input.attr('data-start-date'));
                }

                if ($input.attr('data-end-date')) {
                    datepickerOptions.endDate = new Date($input.attr('data-end-date'));
                }

                $input.datepicker(datepickerOptions).on('hide', function (e) {
                    e.stopPropagation();
                });

                $input.off('changeDate.datepicker-livewire').on('changeDate.datepicker-livewire', function (e) {
                    const dateValue = $input.val();
                    
                    if (dateValue) {
                        const $wrapper = $input.closest('.input-group');
                        let $componentElement = null;
                        
                        $wrapper.parents().each(function() {
                            const $el = $(this);
                            if ($el.attr('wire:id')) {
                                if (!$el.is($wrapper.parent())) {
                                    $componentElement = $el;
                                    return false;
                                }
                            }
                        });

                        if (!$componentElement || $componentElement.length === 0) {
                            $componentElement = $wrapper.parent().closest('[wire\\:id]');
                        }

                        if ($componentElement && $componentElement.length) {
                            const componentId = $componentElement.attr('wire:id');
                            
                            if (componentId) {
                                try {
                                    const component = Livewire.find(componentId);
                                    if (component) {
                                        const modelName = $input.attr('datepicker-livewire') || '{{ $model_name }}';
                                        const currentValue = component.get(modelName);
                                        
                                        if (currentValue !== dateValue) {
                                            component.set(modelName, dateValue);
                                        }
                                    }
                                } catch (e) {
                                    console.warn('Livewire component not found:', componentId, e);
                                }
                            }
                        }
                    }
                });

                const $wrapper = $input.closest('.input-group');
                let $componentElement = null;
                
                $wrapper.parents().each(function() {
                    const $el = $(this);
                    if ($el.attr('wire:id')) {
                        if (!$el.is($wrapper.parent())) {
                            $componentElement = $el;
                            return false;
                        }
                    }
                });

                if (!$componentElement || $componentElement.length === 0) {
                    $componentElement = $wrapper.parent().closest('[wire\\:id]');
                }

                if ($componentElement && $componentElement.length) {
                    const componentId = $componentElement.attr('wire:id');
                    
                    if (componentId) {
                        try {
                            const component = Livewire.find(componentId);
                            if (component) {
                                const modelName = $input.attr('datepicker-livewire') || '{{ $model_name }}';
                                const currentValue = component.get(modelName);
                                
                                if (currentValue !== null && currentValue !== undefined && currentValue !== '') {
                                    $input.datepicker('setDate', currentValue);
                                }
                            }
                        } catch (e) {
                            console.warn('Livewire component not found for sync:', componentId, e);
                        }
                    }
                }
            }

            let previousWireId = null;

            function getCurrentWireId() {
                const $input = $('#' + inputId);
                if ($input.length === 0) return null;
                
                const $datepickerComponent = $input.closest('[wire\\:id]').first();
                return $datepickerComponent.length ? $datepickerComponent.attr('wire:id') : null;
            }

            function initializeDatepickerComponent() {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        initDatepickerComponent(inputId);
                    });
                });
            }
            
            document.addEventListener('livewire:init', () => {
                // previousWireId = getCurrentWireId();
                // initializeDatepickerComponent();
            });

            document.addEventListener('livewire:navigated', () => {
                previousWireId = getCurrentWireId();
                initializeDatepickerComponent();

                Livewire.hook('morphed', () => {
                    const currentWireId = getCurrentWireId();
                    
                    // Only init if wire:id has changed
                    if (currentWireId && currentWireId !== previousWireId) {
                        previousWireId = currentWireId;
                        initializeDatepickerComponent();
                    }
                });
            });
        })();
    </script>
@endpush

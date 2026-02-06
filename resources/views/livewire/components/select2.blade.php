<div>
    <select 
        id="{{ $model_name }}" 
        class="form-select" 
        data-placeholder="{{ $data_placeholder }}"
        data-allow-clear="{{ $allow_clear ? 'true' : 'false' }}"
        data-tags="{{ $tags ? 'true' : 'false' }}" 
        select2-livewire="{{ $model_name }}"
    >
    <option value="">-- {{ $data_placeholder }} --</option>
    @foreach ($list_data as $key => $item)
        <option value="{{ $item->{$value_name} ?? $item['value'] }}" {{ $value == ($item->{$value_name} ?? $item['value']) ? 'selected' : '' }}>
            {{ $item->{$value_label} ?? $item['label'] }}
        </option>
    @endforeach
    </select>
</div>

@push('scripts')
    <script>
        (function() {
            const selectId = '{{ $model_name }}';
            let isInitialized = false;
            let initTimer = null;
            
            function initSelect2Component(selectId) {
                const $select = $('#' + selectId);
                
                if ($select.length === 0) return;

                // Destroy existing select2 instance if exists
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                // Remove any orphaned select2 containers
                $select.next('.select2-container').remove();
                $select.removeAttr('data-select2-id tabindex aria-hidden');
                $select.parent().removeAttr('data-select2-id');

                if (!$select.parent().hasClass('position-relative')) {
                    $select.wrap('<div class="position-relative w-100"></div>');
                }

                $select.select2({
                    tags: $select.attr('data-tags') === 'true' ?? false,
                    allowClear: $select.attr('data-allow-clear') === 'true' ?? false,
                    placeholder: $select.attr('data-placeholder') ?? null,
                    dropdownAutoWidth: true,
                    width: '100%',
                    dropdownParent: $select.parents('.position-relative').first(),
                });

                $select.off('select2:select.select2-livewire').on('select2:select.select2-livewire', function (e) {
                    const selectedValue = $(this).val();
                    const modelName = selectId;
                    
                    const componentElement = $select.closest('[wire\\:id]').first();
                    if (componentElement.length) {
                        const componentId = componentElement.attr('wire:id');
                        
                        if (componentId) {
                            try {
                                const component = Livewire.find(componentId);
                                if (component) {
                                    const currentValue = component.get('value');
                                    if (currentValue !== selectedValue) {
                                        component.set('value', selectedValue);
                                        
                                        // Emit Livewire event untuk handling custom
                                        component.dispatch('select2-changed', { 
                                            value: selectedValue, 
                                            modelName: modelName 
                                        });
                                    }
                                }
                            } catch (e) {
                                console.warn('Livewire component not found:', componentId);
                            }
                        }
                    }
                });

                $select.off('select2:clear.select2-livewire').on('select2:clear.select2-livewire', function () {
                    const modelName = selectId;
                    
                    const componentElement = $select.closest('[wire\\:id]').first();
                    if (componentElement.length) {
                        const componentId = componentElement.attr('wire:id');
                        if (componentId) {
                            try {
                                const component = Livewire.find(componentId);
                                if (component) {
                                    component.set('value', null);
                                    
                                    // Emit Livewire event untuk handling custom
                                    component.dispatch('select2-changed', { 
                                        value: null, 
                                        modelName: modelName 
                                    });
                                }
                            } catch (e) {
                                console.warn('Livewire component not found:', componentId);
                            }
                        }
                    }
                });

                const componentElement = $select.closest('[wire\\:id]').first();
                if (componentElement.length) {
                    const componentId = componentElement.attr('wire:id');
                    
                    if (componentId) {
                        try {
                            const component = Livewire.find(componentId);
                            if (component) {
                                const currentValue = component.get('value');
                                if (currentValue !== null && currentValue !== undefined) {
                                    $select.val(currentValue).trigger('change.select2');
                                }
                            }
                        } catch (e) {
                            console.warn('Livewire component not found for sync:', componentId);
                        }
                    }
                }

                isInitialized = true;
            }

            let previousWireId = null;

            function getCurrentWireId() {
                const $select = $('#' + selectId);
                if ($select.length === 0) return null;
                
                const $select2Component = $select.closest('[wire\\:id]').first();
                return $select2Component.length ? $select2Component.attr('wire:id') : null;
            }

            function initializeSelect2Component(forceReinit = false) {
                // Clear any pending initialization
                if (initTimer) {
                    clearTimeout(initTimer);
                }
                
                // Use debounce to prevent multiple rapid initializations
                initTimer = setTimeout(() => {
                    if (!isInitialized || forceReinit) {
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                initSelect2Component(selectId);
                            });
                        });
                    }
                }, 50);
            }
            
            document.addEventListener('livewire:init', () => {
                previousWireId = getCurrentWireId();
                initializeSelect2Component();
            });

            document.addEventListener('livewire:navigated', () => {
                // Reset initialization flag on navigation
                isInitialized = false;
                previousWireId = getCurrentWireId();
                initializeSelect2Component();

                Livewire.hook('morphed', () => {
                    const currentWireId = getCurrentWireId();
                    // Only init if wire:id has changed
                    if (currentWireId !== previousWireId) {
                        previousWireId = currentWireId;
                        initializeSelect2Component(true);
                    }
                });
            });
        })();
    </script>
@endpush
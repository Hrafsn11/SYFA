<div wire:ignore>
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
        <option value="{{ $item->{$value_name} }}" {{ $value == $item->{$value_name} ? 'selected' : '' }}>
            {{ $item->{$value_label} }}
        </option>
    @endforeach
    </select>
</div>

@push('scripts')
    <script>
        (function() {
            const selectId = '{{ $model_name }}';
            
            function initSelect2Component(selectId) {
                const $select = $('#' + selectId);
                
                if ($select.length === 0) return;

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.removeClass('select2-hidden-accessible').next('.select2-container').remove();
                    $select.removeAttr('data-select2-id tabindex aria-hidden');
                    $select.parent().removeAttr('data-select2-id');
                }

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
                                    }
                                }
                            } catch (e) {
                                console.warn('Livewire component not found:', componentId);
                            }
                        }
                    }
                });

                $select.off('select2:clear.select2-livewire').on('select2:clear.select2-livewire', function () {
                    const componentElement = $select.closest('[wire\\:id]').first();
                    if (componentElement.length) {
                        const componentId = componentElement.attr('wire:id');
                        if (componentId) {
                            try {
                                const component = Livewire.find(componentId);
                                if (component) {
                                    component.set('value', null);
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
            }

            function initializeSelect2Component() {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        initSelect2Component(selectId);
                    });
                });
            }
            document.addEventListener('livewire:init', () => {
                // initializeSelect2Component();
            });

            document.addEventListener('livewire:navigated', () => {
                initializeSelect2Component();

                Livewire.hook('morphed', () => {
                    initializeSelect2Component();
                });
            });
        })();
    </script>
@endpush
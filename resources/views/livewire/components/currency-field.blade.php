<div class="currency-field-wrapper">
    <input
        type="text"
        id="{{ $model_name }}"
        class = 'form-control'
        placeholder = "{{ $placeholder }}"
        name = "{{ $model_name }}"
        data-prefix = '{!! $prefix !!}'
        data-model-name = "{{ $model_name }}"
        value = "{{ $value }}"
        autocomplete = 'off'
    />
</div>

@push('scripts')
    <script>
        (function () {
            const inputId = '{{ $model_name }}';

            function initCleaveForCurrencyField(inputId) {
                const input = document.getElementById(inputId);

                if (!input) {
                    return null;
                }

                if (input.dataset.cleaveInitialized === 'true' && input._cleaveInstance) {
                    return input._cleaveInstance;
                }

                const cleaveInstance = new Cleave(input, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalScale: 0,
                    prefix: input.dataset.prefix || 'Rp ',
                    rawValueTrimPrefix: true,
                    noImmediatePrefix: false
                });

                input.dataset.cleaveInitialized = 'true';
                input._cleaveInstance = cleaveInstance;

                return cleaveInstance;
            }

            function syncInputToComponent() {
                const $input = $('#' + inputId);

                if ($input.length === 0) {
                    return;
                }

                const input = $input.get(0);
                const cleaveInstance = input._cleaveInstance || initCleaveForCurrencyField(inputId);
                
                if (!cleaveInstance) {
                    return;
                }

                const $wrapper = $input.closest('.currency-field-wrapper');
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

                if (!$componentElement || $componentElement.length === 0) {
                    return;
                }

                const componentId = $componentElement.attr('wire:id');

                if (!componentId) {
                    return;
                }

                try {
                    const component = Livewire.find(componentId);

                    if (!component) {
                        return;
                    }

                    const modelName = input.dataset.modelName || '{{ $model_name }}';
                    
                    const currentValue = component.get(modelName);

                    if (String(currentValue || '') === String($input.val() || '')) {
                        return;
                    }

                    component.set(modelName, $input.val());
                } catch (e) {
                    console.warn('Livewire component not found:', componentId, e);
                }
            }

            function syncComponentToInput() {
                const $input = $('#' + inputId);

                if ($input.length === 0) {
                    return;
                }

                const input = $input.get(0);
                
                const $wrapper = $input.closest('.currency-field-wrapper');
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

                if (!$componentElement || $componentElement.length === 0) {
                    return;
                }

                const componentId = $componentElement.attr('wire:id');

                if (!componentId) {
                    return;
                }

                try {
                    const component = Livewire.find(componentId);

                    if (!component) {
                        return;
                    }

                    const modelName = input.dataset.modelName || '{{ $model_name }}';
                    const value = component.get(modelName);
                    const cleaveInstance = input._cleaveInstance || initCleaveForCurrencyField(inputId);

                    if (!cleaveInstance) {
                        return;
                    }

                    if (value !== null && value !== undefined && value !== '') {
                        cleaveInstance.setRawValue(value);
                    } else {
                        cleaveInstance.setRawValue('');
                    }
                } catch (e) {
                    console.warn('Livewire component not found for sync:', componentId, e);
                }
            }

            function attachInputListeners() {
                const $input = $('#' + inputId);

                if ($input.length === 0) {
                    return;
                }

                $input.off('blur.currency-field');
                $input.on('blur.currency-field', () => {
                    syncInputToComponent();
                });
            }

            let previousWireId = null;

            function getCurrentWireId() {
                const $input = $('#' + inputId);
                if ($input.length === 0) return null;
                
                const $currencyComponent = $input.closest('[wire\\:id]').first();
                return $currencyComponent.length ? $currencyComponent.attr('wire:id') : null;
            }

            function initializeCurrencyField() {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        initCleaveForCurrencyField(inputId);
                        syncComponentToInput();
                        attachInputListeners();
                    });
                });
            }

            document.addEventListener('livewire:init', () => {
                previousWireId = getCurrentWireId();
                initializeCurrencyField();
            });

            document.addEventListener('livewire:navigated', () => {
                previousWireId = getCurrentWireId();
                initializeCurrencyField();

                Livewire.hook('morphed', () => {
                    const currentWireId = getCurrentWireId();
                    
                    // Only init if wire:id has changed
                    if (currentWireId && currentWireId !== previousWireId) {
                        previousWireId = currentWireId;
                        initializeCurrencyField();
                    }
                });
            });

        })();
    </script>
@endpush

{{-- Stepper Component - Matching Original Design --}}
<div class="stepper-container mb-4">
    <div class="stepper-wrapper">

        <div class="stepper-item {{ $currentStep >= 1 ? ($currentStep > 1 ? 'completed' : 'active') : '' }}" data-step="1">
            <div class="stepper-node">
            </div>
            <div class="stepper-content">
                <div class="step-label">STEP 1</div>
                <div class="step-name">Pengajuan Pinjaman</div>
            </div>
        </div>

        <div class="stepper-item {{ $currentStep >= 2 ? ($currentStep > 2 ? 'completed' : 'active') : '' }}" data-step="2">
            <div class="stepper-node">
            </div>
            <div class="stepper-content">
                <div class="step-label">STEP 2</div>
                <div class="step-name">Validasi Dokumen</div>
            </div>
        </div>

        <div class="stepper-item {{ $currentStep >= 3 ? ($currentStep > 3 ? 'completed' : 'active') : '' }}" data-step="3">
            <div class="stepper-node"></div>
            <div class="stepper-content">
                <div class="step-label">STEP 3</div>
                <div class="step-name">Persetujuan Debitur</div>
            </div>
        </div>

        <div class="stepper-item {{ $currentStep >= 4 ? ($currentStep > 4 ? 'completed' : 'active') : '' }}" data-step="4">
            <div class="stepper-node"></div>
            <div class="stepper-content">
                <div class="step-label">STEP 4</div>
                <div class="step-name">Validasi CEO SKI</div>
            </div>
        </div>

        <div class="stepper-item {{ $currentStep >= 5 ? ($currentStep > 5 ? 'completed' : 'active') : '' }}" data-step="5">
            <div class="stepper-node"></div>
            <div class="stepper-content">
                <div class="step-label">STEP 5</div>
                <div class="step-name">Validasi Direktur</div>
            </div>
        </div>

        <div class="stepper-item {{ $currentStep >= 6 ? ($currentStep > 6 ? 'completed' : 'active') : '' }}" data-step="6">
            <div class="stepper-node"></div>
            <div class="stepper-content">
                <div class="step-label">STEP 6</div>
                <div class="step-name">Generate Kontrak</div>
            </div>
        </div>

        <div class="stepper-item {{ $currentStep >= 7 ? ($currentStep > 7 ? 'completed' : 'active') : '' }}" data-step="7">
            <div class="stepper-node"></div>
            <div class="stepper-content">
                <div class="step-label">STEP 7</div>
                <div class="step-name">Upload Dokumen Transfer</div>
            </div>
        </div>

        <div class="stepper-item {{ $currentStep >= 8 ? ($currentStep > 8 ? 'completed' : 'active') : '' }}" data-step="8">
            <div class="stepper-node"></div>
            <div class="stepper-content">
                <div class="step-label">STEP 8</div>
                <div class="step-name">Konfirmasi Debitur</div>
            </div>
        </div>

        <div class="stepper-item {{ $currentStep >= 9 ? 'completed' : '' }}" data-step="9">
            <div class="stepper-node"></div>
            <div class="stepper-content">
                <div class="step-label">STEP 9</div>
                <div class="step-name">Selesai</div>
            </div>
        </div>

    </div>
</div>

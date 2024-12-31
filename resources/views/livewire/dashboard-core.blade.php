<div class="mt-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-group gap-3">
            <label for="companySelect" class="primary-color-text"><b>Select Company : </b></label>
            <select id="companySelect" class="form-control" wire:model="selectedCompany">
                @foreach ($companies as $company)
                    <option value="{{ $company->ticker }}" {{ $company->ticker == $selectedCompany ? 'selected' : '' }}>
                        {{ $company->ticker }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-outline-secondary d-none">Download</button>
    </div>

    <hr class="mt-3">

    <ul class="nav nav-tabs custom-nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'general-information' ? 'active' : '' }}" href="#"
                wire:click.prevent="setActiveTab('general-information')">General Information</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'key-statics' ? 'active' : '' }}" href="#"
                wire:click.prevent="setActiveTab('key-statics')">Key Statistics</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'key-ratio' ? 'active' : '' }}" href="#"
                wire:click.prevent="setActiveTab('key-ratio')">Key Ratio</a>
        </li>
    </ul>

    <div class="mt-4">
        @if ($activeTab === 'general-information')
            <livewire:dashboard.general-information :company="$companyData" />
        @elseif ($activeTab === 'key-statics')
            <livewire:dashboard.key-statics :company="$companyData"/>
        @elseif ($activeTab === 'key-ratio')
            <livewire:dashboard.key-ratio :company="$companyData"/>
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#companySelect').select2({
            placeholder: "Search and select a company",
            allowClear: true,
            width: '100%'
        });

        // Untuk Livewire integration
        $('#companySelect').on('change', function(e) {
            @this.set('selectedCompany', $(this).val());
        });
    });

    // Reinitialize pada Livewire update
    document.addEventListener('livewire:load', function() {
        $('#companySelect').select2();
    });

    document.addEventListener('livewire:update', function() {
        $('#companySelect').select2();
    });
</script>

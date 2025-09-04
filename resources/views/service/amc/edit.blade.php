@extends('layouts.app')
@section('page-title', 'Edit AMC')

@section('content')
    <div class="container">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('amc.update', $amc->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card mb-4">
                <div class="card-body">
                    <input type="hidden" value={{ $amc->id }} name="amc_id">
                    <!-- Team and Project Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="team_name" class="form-label">Team Name</label>
                                <select class="form-select" id="team_name" name="team_name" required>
                                    <option value="ac" {{ $amc->team_name == 'ac' ? 'selected' : '' }}>AC</option>
                                    <option value="dc" {{ $amc->team_name == 'dc' ? 'selected' : '' }}>DC</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="project_id" class="form-label">Project Name</label>
                                <select class="form-select" id="project_id" name="project_id" required>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ $amc->project_id == $project->id ? 'selected' : '' }}>
                                            {{ $project->project_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sites Section -->
                    <h5 class="mb-3">Sites Information</h5>
                    <div id="sites-container">
                        @foreach ($amc->sites as $siteIndex => $site)
                            <div class="site-entry card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center mb-3">
                                    <span>Site #{{ $siteIndex + 1 }}</span>
                                    <button type="button" class="btn btn-sm btn-danger remove-site"
                                        {{ $loop->first ? 'disabled' : '' }}>
                                        Remove Site
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Site Details -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Site Name</label>
                                                <input type="text" class="form-control"
                                                    name="sites[{{ $siteIndex }}][name]" value="{{ $site->name }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Site Address</label>
                                                <input type="text" class="form-control"
                                                    name="sites[{{ $siteIndex }}][address]"
                                                    value="{{ $site->address }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Google Maps Link</label>
                                                <input type="url" class="form-control"
                                                    name="sites[{{ $siteIndex }}][map_link]"
                                                    value="{{ $site->map_link }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contacts Section -->
                                    <h6 class="mb-3">Site Contacts</h6>
                                    <div class="contacts-container">
                                        @foreach ($site->contacts as $contactIndex => $contact)
                                            <div class="contact-entry mb-3 p-3 border rounded">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control"
                                                            name="sites[{{ $siteIndex }}][contacts][{{ $contactIndex }}][name]"
                                                            value="{{ $contact->name }}" placeholder="Name" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control"
                                                            name="sites[{{ $siteIndex }}][contacts][{{ $contactIndex }}][organization]"
                                                            value="{{ $contact->organization }}"
                                                            placeholder="Organization">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="tel" class="form-control"
                                                            name="sites[{{ $siteIndex }}][contacts][{{ $contactIndex }}][mobile]"
                                                            value="{{ $contact->mobile }}" placeholder="Mobile" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="email" class="form-control"
                                                            name="sites[{{ $siteIndex }}][contacts][{{ $contactIndex }}][email]"
                                                            value="{{ $contact->email }}" placeholder="Email">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-danger remove-contact"
                                                            {{ $loop->first ? 'disabled' : '' }}>
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-sm btn-secondary add-contact"
                                        data-site-index="{{ $siteIndex }}">
                                        Add Contact
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Add Site Button -->
                    <button type="button" class="btn btn-primary mb-3" id="add-site">
                        Add Another Site
                    </button>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="service_frequency" class="form-label">Service Frequency</label>
                                <select class="form-select" id="service_frequency" name="service_frequency" required>
                                    <option value="weekly" {{ $amc->service_frequency == 'weekly' ? 'selected' : '' }}>
                                        Weekly</option>
                                    <option value="monthly" {{ $amc->service_frequency == 'monthly' ? 'selected' : '' }}>
                                        Monthly</option>
                                    <option value="quarterly"
                                        {{ $amc->service_frequency == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="yearly" {{ $amc->service_frequency == 'yearly' ? 'selected' : '' }}>
                                        Yearly</option>
                                    <option value="custom" {{ $amc->service_frequency == 'custom' ? 'selected' : '' }}>
                                        Custom</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="amc_start_date" class="form-label">AMC Start Date</label>
                                <input type="date" class="form-control" id="amc_start_date" name="amc_start_date"
                                    value="{{ $amc->amc_start_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="amc_end_date" class="form-label">AMC End Date</label>
                                <input type="date" class="form-control" id="amc_end_date" name="amc_end_date"
                                    value="{{ $amc->amc_end_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="bill_frequency" class="form-label">Bill Frequency</label>
                                <select class="form-select" id="bill_frequency" name="bill_frequency" required>
                                    <option value="monthly" {{ $amc->bill_frequency == 'monthly' ? 'selected' : '' }}>
                                        Monthly</option>
                                    <option value="quarterly" {{ $amc->bill_frequency == 'quarterly' ? 'selected' : '' }}>
                                        Quarterly</option>
                                    <option value="annual" {{ $amc->bill_frequency == 'annual' ? 'selected' : '' }}>Annual
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Bill Value</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bill_type"
                                        id="bill_type_constant" value="constant"
                                        {{ $amc->bill_type == 'constant' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bill_type_constant">
                                        Constant
                                    </label>
                                    <input type="number" class="form-control d-inline-block ms-2"
                                        id="constant_bill_value" name="constant_bill_value" style="width: 200px;"
                                        placeholder="Amount" value="{{ $amc->bill_value }}">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="bill_type"
                                        id="bill_type_variable" value="variable"
                                        {{ $amc->bill_type == 'variable' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bill_type_variable">
                                        Variable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="variable-bill-table" class="mb-3"
                        style="display: {{ $amc->bill_type == 'variable' ? 'block' : 'none' }};">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Bill Date</th>
                                    <th>Bill Value (Pre-GST)</th>
                                </tr>
                            </thead>
                            <tbody id="variable-bill-rows">
                                @if ($amc->bill_type == 'variable' && $amc->variable_bills)
                                    @foreach ($amc->variable_bills as $billIndex => $bill)
                                        <tr>
                                            <td>
                                                <input type="date" class="form-control"
                                                    name="variable_bills[{{ $billIndex }}][date]"
                                                    value="{{ $bill['date'] }}" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control"
                                                    name="variable_bills[{{ $billIndex }}][amount]"
                                                    value="{{ $bill['amount'] }}" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-bill-row">
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-bill-row">+ Add Bill Row</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Service Engineer Assigned</label>
                        <div id="engineer-container">
                            @foreach ($amc->engineers as $engineerIndex => $engineer)
                                <div class="engineer-entry mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control"
                                                name="engineers[{{ $engineerIndex }}][name]"
                                                value="{{ $engineer->name }}" placeholder="Name" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control"
                                                name="engineers[{{ $engineerIndex }}][organization]"
                                                value="{{ $engineer->organization }}" placeholder="Organization">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="tel" class="form-control"
                                                name="engineers[{{ $engineerIndex }}][mobile]"
                                                value="{{ $engineer->mobile }}" placeholder="Mobile" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="email" class="form-control"
                                                name="engineers[{{ $engineerIndex }}][email]"
                                                value="{{ $engineer->email }}" placeholder="Email">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-engineer">+ Add Engineer</button>
                    </div>

                    <div class="mb-3">
                        <label for="amc_po" class="form-label">Upload AMC PO</label>
                        <input type="file" class="form-control" id="amc_po" name="amc_po">
                        @if ($amc->amc_po_path)
                            <div class="mt-2">
                                <a href="{{ Storage::url($amc->amc_po_path) }}" target="_blank"
                                    class="btn btn-sm btn-info">
                                    View Current PO
                                </a>
                            </div>
                        @endif
                    </div>

                    <table class="table table-bordered" id="products-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Serial No.</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($amc->products as $productIndex => $product)
                                <tr>
                                    <td>
                                        <select class="form-select" name="products[{{ $productIndex }}][item_id]"
                                            required>
                                            <option value="">Select Item</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ $product->item_id == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            name="products[{{ $productIndex }}][description]"
                                            value="{{ $product->description }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            name="products[{{ $productIndex }}][make]" value="{{ $product->make }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            name="products[{{ $productIndex }}][model]" value="{{ $product->model }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            name="products[{{ $productIndex }}][serial_no]"
                                            value="{{ $product->serial_no }}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control"
                                            name="products[{{ $productIndex }}][quantity]"
                                            value="{{ $product->quantity }}" min="1">
                                    </td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-danger remove-product">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-secondary" id="add-product">+ Add Product</button>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <a href="{{ route('amc.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Include the same JavaScript as create.blade.php -->
@section('scripts')
    <script>
        // Site Management
        let siteCounter = {{ count($amc->sites) }};
        document.getElementById('add-site').addEventListener('click', function() {
            const container = document.getElementById('sites-container');
            const newSite = document.createElement('div');
            newSite.className = 'site-entry card mb-4';
            newSite.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center mb-3">
                    <span>Site #${siteCounter + 1}</span>
                    <button type="button" class="btn btn-sm btn-danger remove-site">
                        Remove Site
                    </button>
                </div>
                <div class="card-body">
                    <!-- Site Details -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="sites[${siteCounter}][name]" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Site Address</label>
                                <input type="text" class="form-control" name="sites[${siteCounter}][address]" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Google Maps Link</label>
                                <input type="url" class="form-control" name="sites[${siteCounter}][map_link]">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contacts Section -->
                    <h6 class="mb-3">Site Contacts</h6>
                    <div class="contacts-container">
                        <div class="contact-entry mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="sites[${siteCounter}][contacts][0][name]" placeholder="Name" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="sites[${siteCounter}][contacts][0][organization]" placeholder="Organization">
                                </div>
                                <div class="col-md-3">
                                    <input type="tel" class="form-control" name="sites[${siteCounter}][contacts][0][mobile]" placeholder="Mobile" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="email" class="form-control" name="sites[${siteCounter}][contacts][0][email]" placeholder="Email">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-contact" disabled>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary add-contact" data-site-index="${siteCounter}">
                        Add Contact
                    </button>
                </div>
            `;
            container.appendChild(newSite);
            siteCounter++;

            // Enable remove buttons for all sites except first if multiple exist
            document.querySelectorAll('.remove-site').forEach(btn => {
                if (document.querySelectorAll('.site-entry').length > 1) {
                    btn.disabled = false;
                }
            });
        });

        // Contact Management
        document.addEventListener('click', function(e) {
            // Add Contact
            if (e.target.classList.contains('add-contact') || e.target.closest('.add-contact')) {
                const btn = e.target.classList.contains('add-contact') ? e.target : e.target.closest(
                    '.add-contact');
                const siteIndex = btn.getAttribute('data-site-index');
                const container = btn.closest('.card-body').querySelector('.contacts-container');
                const contactCount = container.querySelectorAll('.contact-entry').length;

                const newContact = document.createElement('div');
                newContact.className = 'contact-entry mb-3 p-3 border rounded';
                newContact.innerHTML = `
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="sites[${siteIndex}][contacts][${contactCount}][name]" placeholder="Name" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="sites[${siteIndex}][contacts][${contactCount}][organization]" placeholder="Organization">
                        </div>
                        <div class="col-md-3">
                            <input type="tel" class="form-control" name="sites[${siteIndex}][contacts][${contactCount}][mobile]" placeholder="Mobile" required>
                        </div>
                        <div class="col-md-3">
                            <input type="email" class="form-control" name="sites[${siteIndex}][contacts][${contactCount}][email]" placeholder="Email">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger remove-contact">
                                Remove
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(newContact);

                // Enable remove buttons if multiple contacts
                container.querySelectorAll('.remove-contact').forEach(btn => {
                    if (container.querySelectorAll('.contact-entry').length > 1) {
                        btn.disabled = false;
                    }
                });
            }

            // Remove Site
            if (e.target.classList.contains('remove-site') || e.target.closest('.remove-site')) {
                const btn = e.target.classList.contains('remove-site') ? e.target : e.target.closest(
                    '.remove-site');
                if (document.querySelectorAll('.site-entry').length > 1) {
                    btn.closest('.site-entry').remove();

                    // Reindex remaining sites
                    document.querySelectorAll('.site-entry').forEach((site, index) => {
                        site.querySelector('.card-header span').textContent = `Site #${index + 1}`;
                        // Update data-site-index for all add-contact buttons in this site
                        site.querySelectorAll('.add-contact').forEach(contactBtn => {
                            contactBtn.setAttribute('data-site-index', index);
                        });
                    });
                }
            }

            // Remove Contact
            if (e.target.classList.contains('remove-contact') || e.target.closest('.remove-contact')) {
                const btn = e.target.classList.contains('remove-contact') ? e.target : e.target.closest(
                    '.remove-contact');
                const container = btn.closest('.contacts-container');
                if (container.querySelectorAll('.contact-entry').length > 1) {
                    btn.closest('.contact-entry').remove();
                }
            }
        });

        // Dynamic Engineer Addition
        let engineerCounter = {{ count($amc->engineers) }};
        document.getElementById('add-engineer').addEventListener('click', function() {
            const container = document.getElementById('engineer-container');
            const newEntry = document.createElement('div');
            newEntry.className = 'engineer-entry mb-3 p-3 border rounded';
            newEntry.innerHTML = `
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="engineers[${engineerCounter}][name]" placeholder="Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="engineers[${engineerCounter}][organization]" placeholder="Organization">
                    </div>
                    <div class="col-md-2">
                        <input type="tel" class="form-control" name="engineers[${engineerCounter}][mobile]" placeholder="Mobile" required>
                    </div>
                    <div class="col-md-2">
                        <input type="email" class="form-control" name="engineers[${engineerCounter}][email]" placeholder="Email">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-engineer">Remove</button>
                    </div>
                </div>
            `;
            container.appendChild(newEntry);
            engineerCounter++;
        });

        // Dynamic Product Addition
        let productCounter = {{ count($amc->products) }};
        document.getElementById('add-product').addEventListener('click', function() {
            const tbody = document.querySelector('#products-table tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select class="form-select" name="products[${productCounter}][item_id]" required>
                        <option value="">Select Item</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" class="form-control" name="products[${productCounter}][description]"></td>
                <td><input type="text" class="form-control" name="products[${productCounter}][make]"></td>
                <td><input type="text" class="form-control" name="products[${productCounter}][model]"></td>
                <td><input type="text" class="form-control" name="products[${productCounter}][serial_no]"></td>
                <td><input type="number" class="form-control" name="products[${productCounter}][quantity]" value="1" min="1"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
            `;
            tbody.appendChild(newRow);
            productCounter++;
        });

        // Remove buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-engineer')) {
                e.target.closest('.engineer-entry').remove();
            }
            if (e.target.classList.contains('remove-product')) {
                e.target.closest('tr').remove();
            }
            if (e.target.classList.contains('remove-bill-row')) {
                e.target.closest('tr').remove();
            }
        });

        // Variable Bill Table Toggle
        document.querySelectorAll('input[name="bill_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const variableTable = document.getElementById('variable-bill-table');
                variableTable.style.display = this.value === 'variable' ? 'block' : 'none';
            });
        });

        // Add Bill Row
        let billCounter = {{ $amc->bill_type == 'variable' && $amc->variable_bills ? count($amc->variable_bills) : 0 }};
        document.getElementById('add-bill-row').addEventListener('click', function() {
            const tbody = document.getElementById('variable-bill-rows');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="date" class="form-control" name="variable_bills[${billCounter}][date]" required></td>
                <td><input type="number" class="form-control" name="variable_bills[${billCounter}][amount]" required></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-bill-row">Remove</button></td>
            `;
            tbody.appendChild(newRow);
            billCounter++;
        });
    </script>
@endsection
@endsection

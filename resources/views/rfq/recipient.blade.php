@extends('layouts.app')
@section('page-title', 'Quotation Received Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('rfq.index') }}" class="btn btn-primary btn-sm">View All RFQs</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')

                        <form action="{{ route('rfq.recipient', $rfq->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Quotation Receipt Date and Time</label>
                                    <input type="datetime-local" name="receipt_datetime" class="form-control" required>
                                </div>
                            </div>

                            <!-- Dynamic Items Table -->
                            <div class="table-responsive mb-3">
                                <table class="table-bordered" id="items-table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">Sr. No.</th>
                                            <th style="width: 250px;">Item Name</th>
                                            <th>Item Description</th>
                                            <th style="width: 100px;">Quantity</th>
                                            <th style="width: 100px;">Unit</th>
                                            <th style="width: 200px;">Unit Price</th>
                                            <th style="width: 200px;">Amount</th>
                                            <th style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <select name="items[0][item_id]" class="form-select" required>
                                                    <option value="">Select Item</option>
                                                    @foreach ($tenderItems as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][description]" class="form-control">
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][quantity]"
                                                    class="form-control quantity" required>
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][unit]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][unit_price]"
                                                    class="form-control unit-price" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][amount]" class="form-control amount"
                                                    readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary btn-sm" id="add-row">Add Row</button>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">GST Percentage</label>
                                    <input type="number" name="gst_percentage" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">GST Type</label>
                                    <select name="gst_type" class="form-select" required>
                                        <option value="">Select GST Type</option>
                                        <option value="inclusive">Inclusive</option>
                                        <option value="extra">Extra</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Delivery Time (in days)</label>
                                    <input type="number" name="delivery_time" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Freight</label>
                                    <select name="freight_type" class="form-select" required>
                                        <option value="">Select Freight Type</option>
                                        <option value="inclusive">Inclusive</option>
                                        <option value="extra">Extra</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Quotation Document</label>
                                    <input type="file" id="quotation_document" name="quotation_document[]"
                                        class="form-control" required multiple>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Technical Documents</label>
                                    <input type="file" id="technical_documents" name="technical_documents[]"
                                        class="form-control" multiple>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">MAF Document</label>
                                    <input type="file" id="maf_document" name="maf_document[]" class="form-control"
                                        multiple>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">MII Document</label>
                                    <input type="file" id="mii_document[]" name="mii_document" class="form-control"
                                        multiple>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        #items-table th,
        #items-table td {
            padding: 8px;
        }

        #items-table tbody tr td:last-child {
            text-align: center;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Add new row
            $('#add-row').click(function() {
                var rowCount = $('#items-table tbody tr').length;
                var newRow = $('#items-table tbody tr:first').clone();
                newRow.find('input').val('');
                newRow.find('td:first').text(rowCount + 1);
                newRow.find('select').val('');
                newRow.find('input, select').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace('[0]', '[' + rowCount + ']'));
                    }
                });
                $('#items-table tbody').append(newRow);
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                if ($('#items-table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateSerialNumbers();
                }
            });

            // Calculate amount
            $(document).on('input', '.quantity, .unit-price', function() {
                var row = $(this).closest('tr');
                var quantity = parseFloat(row.find('.quantity').val()) || 0;
                var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
                row.find('.amount').val(quantity * unitPrice);
            });

            // Update serial numbers
            function updateSerialNumbers() {
                $('#items-table tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            FilePond.registerPlugin(FilePondPluginFileValidateType);

            $('#quotation_document,#technical_documents,#maf_document,#mii_document').filepond({
                credits: false,
                storeAsFile: true,
                maxTotalFileSize: '10MB',
                allowMultiple: true,
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
        });
    </script>
@endpush

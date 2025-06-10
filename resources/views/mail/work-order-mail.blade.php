<style>
    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black
    }

    table th {
        border: 1px solid black;
        padding: 8px;
    }

    table td {

        padding: 8px;
    }
</style>

<div>
    <p>Dear Team Leader,</p>

    <p>Congratulations…!!</p>

    <p>Our team has received an Order from {{ $data['organization_name'] }}.</p>
    <p>The project name for future reference is {{ $data['tender_name'] }}.</p>
    <p>The details of the Order have been represented in the Table below:</p>

    @php
        $basic = \App\Models\Basic_detail::with('wo_details', 'wo_acceptance_yes')->findOrFail($data['id']);
        $wo = $basic->wo_details;
        $tender = $basic->tenderName;
        $rfq = $tender?->rfqs?->first();
        $woDetails = $basic->wo_details;

        if ($woDetails) {
            $woData = [
                'departments' => json_decode($woDetails->departments, true),
                'name' => json_decode($woDetails->name, true),
                'designation' => json_decode($woDetails->designation, true),
                'phone' => json_decode($woDetails->phone, true),
                'email' => json_decode($woDetails->email, true),
            ];
        } else {
            $woData = [];
            Log::warning('Wodetails not found for basic detail ID.', ['basic_id' => $id]);
        }
    @endphp

    <div class="card-body">
        <table style="width:75%; border: 1px solid black; text-align: left; border-collapse: collapse;">
            <tr>
                <th style="border: 1px solid black;">FOA/SAP PO/Detailed WO</th>
                <td style="border: 1px solid black;">
                    @if ($basic->foa_sap_image)
                        <a href="{{ asset("upload/basicdetails/{$basic->foa_sap_image}") }}" target="_blank">
                            View Document
                        </a>
                    @else
                        No Document Found
                    @endif
                </td>
            </tr>
        </table>
        <table style="width:75%; border: 1px solid black; text-align: left; border-collapse: collapse;">
            <tbody>
                <tr>
                    <th colspan="6" style="text-align: center; text-transform: uppercase;">Project Summary Sheet</th>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Project Name</th>
                    <td style="border: 1px solid black;">{{ $basic->tenderName->tender_name }}</td>
                    <th style="border: 1px solid black;">WO No.</th>
                    <td style="border: 1px solid black;">{{ $basic->number }}</td>
                    <th style="border: 1px solid black;">WO Date</th>
                    <td style="border: 1px solid black;">{{ Carbon\Carbon::parse($basic->date)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <th colspan="6" style="text-align: center; text-transform: uppercase;">Client Details</th>
                </tr>
                @if (isset($woData['departments']) && count($woData['departments']))
                    <tr>
                        <th style="border: 1px solid black;">#</th>
                        <th style="border: 1px solid black;">Department</th>
                        <th style="border: 1px solid black;">Name</th>
                        <th style="border: 1px solid black;">Designation</th>
                        <th style="border: 1px solid black;">Phone</th>
                        <th style="border: 1px solid black;">Email</th>
                    </tr>
                    @for ($i = 0; $i < count($woData['departments']); $i++)
                        <tr>
                            <td style="border: 1px solid black;">{{ $i + 1 }}</td>
                            <td style="border: 1px solid black;">{{ $woData['departments'][$i] ?? '' }}</td>
                            <td style="border: 1px solid black;">{{ $woData['name'][$i] ?? '' }}</td>
                            <td style="border: 1px solid black;">{{ $woData['designation'][$i] ?? '' }}</td>
                            <td style="border: 1px solid black;">{{ $woData['phone'][$i] ?? '' }}</td>
                            <td style="border: 1px solid black;">{{ $woData['email'][$i] ?? '' }}</td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>
        <table style="width:75%; border: 1px solid black; text-align: left; border-collapse: collapse;">
            <tbody>
                <tr>
                    <th colspan="6" style="text-align: center; text-transform: uppercase;">WO Details</th>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">WO Value (Pre GST)</th>
                    <td style="border: 1px solid black;">{{ $basic->par_gst }}</td>
                    <th style="border: 1px solid black;">WO Value (GST Amount)</th>
                    <td style="border: 1px solid black;">{{ format_inr($basic->par_amt) }}</td>
                    <th style="border: 1px solid black;">Budget</th>
                    <td style="border: 1px solid black;">{{ format_inr($wo->budget) }}</td>
                </tr>
                <tr>
                    <th colspan="6" style="text-align: center; text-transform: uppercase;">Completion Period</th>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Total</th>
                    <td style="border: 1px solid black;">{{ $tender->supply + $tender->installation }}</td>
                    <th style="border: 1px solid black;">Supply</th>
                    <td style="border: 1px solid black;">{{ $tender->supply }}</td>
                    <th style="border: 1px solid black;">I&C</th>
                    <td style="border: 1px solid black;">{{ $tender->installation }}</td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Max LD%</th>
                    <td style="border: 1px solid black;">{{ $wo->max_ld }}%</td>
                    <th style="border: 1px solid black;">LD Start Date</th>
                    <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($wo->ldstartdate)->format('d-m-Y') }}
                    </td>
                    <th style="border: 1px solid black;">Max LD Date</th>
                    <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($wo->maxlddate)->format('d-m-Y') }}
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width:75%; border: 1px solid black; text-align: left; border-collapse: collapse;">
            <tbody>
                <tr>
                    <th colspan="5" style="text-align: center; text-transform: uppercase;">WO Documents</th>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Original LOA/Gem PO/LOI/Draft PO</th>
                    <td style="border: 1px solid black;">
                        @if ($basic->image)
                            <a href="{{ asset("upload/basicdetails/{$basic->image}") }}" target="_blank">
                                View Document
                            </a>
                        @else
                            No Document Found
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Contract Agreement Format</th>
                    <td style="border: 1px solid black;">
                        @if ($wo->file_agreement)
                            <a href="{{ asset("upload/applicable/{$wo->file_agreement}") }}" target="_blank">
                                View Document
                            </a>
                        @else
                            No Document Found
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Filled BG Format</th>
                    <td style="border: 1px solid black;">
                        @if ($wo->file_applicable)
                            <a href="{{ asset("upload/applicable/{$wo->file_applicable}") }}" target="_blank">
                                View Document
                            </a>
                        @else
                            No Document Found
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width:75%; border: 1px solid black; text-align: left; border-collapse: collapse;">
            <tbody>
                <tr>
                    <th colspan="5" style="text-align: center; text-transform: uppercase;">Tender Documents</th>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Scope of Work</th>
                    <td style="border: 1px solid black;">
                        @if ($rfq?->scopes)
                            @foreach ($rfq->scopes as $file)
                                <a href="{{ asset("uploads/rfqdocs{$file->file_path}") }}" target="_blank">
                                    {{ $loop->iteration }} - {{ $file->file_path }}
                                </a><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Tech Specs</th>
                    <td style="border: 1px solid black;">
                        @if ($rfq?->technicals)
                            @foreach ($rfq->technicals as $file)
                                <a href="{{ asset("uploads/rfqdocs{$file->file_path}") }}" target="_blank">
                                    {{ $loop->iteration }} - {{ $file->file_path }}
                                </a><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">Detailed BOQ</th>
                    <td style="border: 1px solid black;">
                        @if ($rfq?->boqs)
                            @foreach ($rfq->boqs as $file)
                                <a href="{{ asset("uploads/rfqdocs{$file->file_path}") }}" target="_blank">
                                    {{ $loop->iteration }} - {{ $file->file_path }}
                                </a><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">MAF Format</th>
                    <td style="border: 1px solid black;">
                        @if ($rfq?->scopes)
                            @foreach ($rfq->scopes as $file)
                                <a href="{{ asset("uploads/rfqdocs{$file->file_path}") }}" target="_blank">
                                    {{ $loop->iteration }} - {{ $file->file_path }}
                                </a><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">MAF from Vendor</th>
                    <td style="border: 1px solid black;">
                        @if ($rfq?->mafs)
                            @foreach ($rfq->mafs as $file)
                                <a href="{{ asset("uploads/rfqdocs{$file->file_path}") }}" target="_blank">
                                    {{ $loop->iteration }} - {{ $file->file_path }}
                                </a><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">MII Format</th>
                    <td style="border: 1px solid black;">
                        @if ($rfq?->miis)
                            @foreach ($rfq->miis as $file)
                                <a href="{{ asset("uploads/rfqdocs{$file->file_path}") }}" target="_blank">
                                    {{ $loop->iteration }} - {{ $file->file_path }}
                                </a><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">MII from Vendor</th>
                    <td style="border: 1px solid black;">
                        @if ($rfq?->scopes)
                            @foreach ($rfq->scopes as $file)
                                <a href="{{ asset("uploads/rfqdocs{$file->file_path}") }}" target="_blank">
                                    {{ $loop->iteration }} - {{ $file->file_path }}
                                </a><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="border: 1px solid black;">List of Documents from OEM</th>
                    <td style="border: 1px solid black;">{{ $rfq?->docs_list }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <p>
        Also, please find attached a copy of the order received. Please review the details carefully and either approve
        the WO or else suggest the changes required in the Order copy using the WO Acceptance Form (
        <a href="{{ $data['wo_dashboard_link'] }}">click here</a>)
    </p>

    <p>“Persistence begets progress”</p>

    <p>Best Regards,</p>
    <p>{{ $data['te_name'] }},
        <br>Volks Energie Pvt. Ltd.
        <br>{{ $data['te_mob'] }}
        <br>{{ $data['te_mail'] }}
        <br>B1/D8 2nd Floor,
        <br>Mohan Co-Operative Industrial Estate,
        <br>New Delhi - 110044
    </p>
</div>

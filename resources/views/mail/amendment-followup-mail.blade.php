<div>
    <p>Dear {{ $data['name'] }} sir,</p>
    <p>
        We thank you for placing your trust in us and releasing the WO no. {{ $data['wo_no'] }}, dated
        {{ \Carbon\Carbon::parse($data['wo_date'])->format('d-m-Y') }}.
    </p>
    @php
        $basic = \App\Models\Basic_detail::with('wo_acceptance_yes')->findOrFail($data['id']);
        $woAcceptance = $basic->wo_acceptance_yes;
        if ($woAcceptance->wo_yes) {
            $changes = [
                'page' => json_decode($woAcceptance->page_no, true),
                'clause' => json_decode($woAcceptance->clause_no, true),
                'current' => json_decode($woAcceptance->current_statement, true),
                'correct' => json_decode($woAcceptance->corrected_statement, true),
            ];
        } else {
            $changes = [];
            Log::warning('Wo_acceptance_yes not found for basic detail ID.', ['basic_id' => $id]);
        }
    @endphp
    <p>
        After carefully reviewing the order, we identified a few clauses requiring amendment. Please find these in the
        table below:
        Please make the above changes and reissue the WO to us.
    </p>

    <table style="width:75%; border: 1px solid black; text-align: left; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border: 1px solid black;">Date</th>
                <td style="border: 1px solid black;">
                    {{ Carbon\Carbon::parse($basic->date)->format('d-m-Y') }}
                </td>
                <th style="border: 1px solid black;">PO/WO No.</th>
                <th style="border: 1px solid black;">
                    {{ $basic->number }}
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black;">Page No.</th>
                <th style="border: 1px solid black;">Clause No.</th>
                <th style="border: 1px solid black;">Present Text</th>
                <th style="border: 1px solid black;">Corrected Text</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($changes['page']) && count($changes['page']))
                @for ($i = 0; $i < count($changes['page']); $i++)
                    <tr>
                        <td style="border: 1px solid black;">{{ $changes['page'][$i] ?? '' }}</td>
                        <td style="border: 1px solid black;">{{ $changes['clause'][$i] ?? '' }}</td>
                        <td style="border: 1px solid black;">{{ $changes['current'][$i] ?? '' }}</td>
                        <td style="border: 1px solid black;">{{ $changes['correct'][$i] ?? '' }}</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <p>We are grateful for the opportunity given by your organization.</p>

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

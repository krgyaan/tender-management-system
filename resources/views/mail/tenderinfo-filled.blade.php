<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear Team Leader,</p>

    <p>
        Please find the tender info sheet filled, please review the details and decide if the tender should be bid.
    </p>

    <table style="border: 1px solid black; text-align: left; border-collapse: collapse;">
        <tbody>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Organization</th>
                <td style="border: 1px solid black; text-align: left;" colspan="6">{{ $data['organization'] }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Tender Name</th>
                <td style="border: 1px solid black; text-align: left;" colspan="6">{{ $data['tender_name'] }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Tender</th>
                <td style="border: 1px solid black; text-align: left;" colspan="6">{{ $data['tender_no'] }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Website</th>
                <td style="border: 1px solid black; text-align: left;" colspan="6">{{ $data['website'] }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Bill Due Date & Time</th>
                <td style="border: 1px solid black; text-align: left;" colspan="6">{{ $data['due_date'] }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Recommendation By TE</th>
                <td style="border: 1px solid black; text-align: left;">
                    {{ $data['recommendation_by_te'] }}</td>
                <th style="border: 1px solid black; text-align: left;">Reason</th>
                <td style="border: 1px solid black; text-align: left;" colspan="4">{{ $data['reason'] }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; text-align: center;" colspan="7">
                    <b>Tender Information</b>
                </td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Tender Fees</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['tender_fees'] }}</td>
                <td style="border: 1px solid black; text-align: left;">Rs.</td>
                <th style="border: 1px solid black; text-align: left;">Tender Fees in form of</th>
                <td style="border: 1px solid black; text-align: left;" colspan="3">
                    {{ $data['tender_fees_in_form_of'] }}
                </td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">EMD</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['emd'] }}</td>
                <td style="border: 1px solid black; text-align: left;">Rs.</td>
                <th style="border: 1px solid black; text-align: left;">EMD Required</th>
                <td style="border: 1px solid black; text-align: left;" colspan="3">{{ $data['emd_required'] }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Tender Value (GST Incl.)</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['tender_value'] }}</td>
                <td style="border: 1px solid black; text-align: left;">Rs.</td>
                <th style="border: 1px solid black; text-align: left;">EMD in form of</th>
                <td style="border: 1px solid black; text-align: left;" colspan="3">
                    {{ $data['emd_in_form_of'] }}
                </td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Bid Validity</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['bid_validity'] }}</td>
                <td style="border: 1px solid black; text-align: left;">Days</td>
                <th style="border: 1px solid black; text-align: left;">Commercial Evaluation</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['commercial_evaluation'] }}</td>
                <th style="border: 1px solid black; text-align: left;">RA Applicable</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['ra_applicable'] }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">MAF Required</th>
                <td style="border: 1px solid black; text-align: left;" colspan="2">{{ $data['maf_required'] }}</td>
                <th style="border: 1px solid black; text-align: left;">Delivery Time (supply/total)</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['delivery_time'] }} Days</td>
                <th style="border: 1px solid black; text-align: left;">Delivery Time (I&C)</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['delivery_time_ic'] }} Days</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">PBG %age</th>
                <td style="border: 1px solid black; text-align: left;" colspan="2">{{ $data['pbg_percentage'] }}%
                </td>
                <th style="border: 1px solid black; text-align: left;">Payment Terms (supply)</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['payment_terms'] }} %</td>
                <th style="border: 1px solid black; text-align: left;">Payment Terms (I&C)</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['payment_terms_ic'] }} %</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">PBG Duration</th>
                <td style="border: 1px solid black; text-align: left;" colspan="2">{{ $data['pbg_duration'] }}
                    Months</td>
                <th style="border: 1px solid black; text-align: left;">LD % (per week)</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['ld_percentage'] }}%</td>
                <th style="border: 1px solid black; text-align: left;">Max LD%</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['max_ld'] }} %</td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Physical Docs Submission Required</th>
                <td style="border: 1px solid black; text-align: left;">
                    {{ $data['phydocs_submission_required'] }}</td>
                <td style="border: 1px solid black; text-align: left;"></td>
                <th style="border: 1px solid black; text-align: left;">Physical Docs Submission Deadline</th>
                <td style="border: 1px solid black; text-align: left;" colspan="3">
                    @if ($data['phydocs_submission_required'] == 'Yes')
                        {{ $data['phydocs_submission_deadline'] }}
                    @else
                        NA
                    @endif
                </td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Eligibility Criterion</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['eligibility_criterion'] }}</td>
                <td style="border: 1px solid black; text-align: left;"> Age (in yrs)</td>
                <td style="border: 1px solid black; text-align: center;" colspan="4">
                    <b>Financial Criterion</b>
                </td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">3 Work Value</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['work_value1'] }}</td>
                <td style="border: 1px solid black; text-align: left;">{{ $data['name1'] }}</td>
                <th style="border: 1px solid black; text-align: left;">Annual Avg. Turnover</th>
                <td style="border: 1px solid black; text-align: left;">
                    {{ $data['aat'] == 'amt' ? 'Amount' : ucfirst($data['aat']) }}
                </td>
                <td style="border: 1px solid black; text-align: left;">{{ $data['aat_amt'] }}</td>
                <td style="border: 1px solid black; text-align: left;"></td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">2 Work Value</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['work_value2'] }}</td>
                <td style="border: 1px solid black; text-align: left;">{{ $data['name2'] }}</td>
                <th style="border: 1px solid black; text-align: left;">Working Capital</th>
                <td style="border: 1px solid black; text-align: left;">
                    {{ $data['wc'] == 'amt' ? 'Amount' : ucfirst($data['wc']) }}
                </td>
                <td style="border: 1px solid black; text-align: left;">{{ $data['wc_amt'] }}</td>
                <td style="border: 1px solid black; text-align: left;"></td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">1 Work Value</th>
                <td style="border: 1px solid black; text-align: left;">{{ $data['work_value3'] }}</td>
                <td style="border: 1px solid black; text-align: left;">{{ $data['name3'] }}</td>
                <th style="border: 1px solid black; text-align: left;">Net Worth</th>
                <td style="border: 1px solid black; text-align: left;">
                    {{ $data['nw'] == 'amt' ? 'Amount' : ucfirst($data['nw']) }}
                </td>
                <td style="border: 1px solid black; text-align: left;">{{ $data['nw_amt'] }}</td>
                <td style="border: 1px solid black; text-align: left;"></td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Document for Technical Eligibility</th>
                <td style="border: 1px solid black; text-align: left;" colspan="2">
                    <ul>
                        {{ $data['te_docs'] }}
                    </ul>
                </td>
                <th style="border: 1px solid black; text-align: left;">Solvency Certificate</th>
                <td style="border: 1px solid black; text-align: left;">
                    {{ $data['sc'] == 'amt' ? 'Amount' : ucfirst($data['sc']) }}
                </td>
                <td style="border: 1px solid black; text-align: left;">{{ $data['sc_amt'] }}</td>
                <td style="border: 1px solid black; text-align: left;"></td>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Tender Documents</th>
                <td style="border: 1px solid black; text-align: left;" colspan="3">
                    <ul>
                        {{ $data['tender_docs'] }}
                    </ul>
                </td>
                <th style="border: 1px solid black; text-align: left;">Document for CommercialEligibility</th>
                <td style="border: 1px solid black; text-align: left;" colspan="3">
                    <ul>
                        {{ $data['ce_docs'] }}
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
    
    <table style="border: 1px solid black; text-align: left; border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th colspan="4" style="text-align: center;">Client Details</th>
            </tr>
            <tr>
                <th style="border: 1px solid black; text-align: left;">Name</th>
                <th style="border: 1px solid black; text-align: left;">Designation</th>
                <th style="border: 1px solid black; text-align: left;">Email</th>
                <th style="border: 1px solid black; text-align: left;">Phone</th>
            </tr>
            @if (!empty($data['clients']))
                @php
                    $clients = json_decode($data['clients'], true);
                @endphp
                @foreach ($clients as $client)
                    <tr>
                        <td style="border: 1px solid black; text-align: left;">{{ $client['client_name'] }}</td>
                        <td style="border: 1px solid black; text-align: left;">{{ $client['client_designation'] }}</td>
                        <td style="border: 1px solid black; text-align: left;">{{ $client['client_email'] }}</td>
                        <td style="border: 1px solid black; text-align: left;">{{ $client['client_mobile'] }}</td>
                    </tr>
                @endforeach
            @endif
        </thead>
    </table>
    
    <p>
        Please take your decision using the form link: <a href="{{ $data['link'] }}">click here</a>
        Please decide within the next 24 hours, so that I can prepare and bid this tender on time.
    </p>

    <br>
    <b>Regards,</b><br>
    {{ $data['assignee'] }}
</div>

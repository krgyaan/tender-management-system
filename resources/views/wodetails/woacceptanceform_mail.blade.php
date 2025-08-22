<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Display</title>
</head>
<body>
    

@php
    $basicDetails = App\Models\Basic_detail::whereIn('id', array_column($wodata, 'basic_detail_id'))->get()->keyBy('id');
@endphp

@foreach($wodata as $item)
    @php
        $basicdata = $basicDetails[$item['basic_detail_id']] ?? null;
    @endphp 
   
    
    <div style="padding: 0px 30px" >
    <h3>Dear Sir ,</h3>
    <p style="margin-top: -13px;" > We thank you for placing your trust in us and releasing the WO no. {{ $basicdata->number ?? 'N/A' }}, Date {{ $basicdata->date ?? 'N/A' }} .</p>
    <p style="" > After carefully reviewing the order, we identified a few clauses requiring amendment. Please find these in the table below:</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        
        <thead>
            <tr style="text-align: center;">
      
                <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">Date</th>
                <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">{{ $basicdata->date ?? 'N/A' }}</th>
                <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">PO/WO No.</th>
                <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">{{ $basicdata->number ?? 'N/A' }}</th>
            </tr>
        </thead>
        <thead>
            <tr style="background-color: #FF971D; text-align: center;">
      
                <th style="padding: 10px; border: 1px solid #ddd;color: white;">Page No</th>
                <th style="padding: 10px; border: 1px solid #ddd;color: white;">Clause No</th>
                <th style="padding: 10px; border: 1px solid #ddd;color: white;">Present Text</th>
                <th style="padding: 10px; border: 1px solid #ddd;color: white;">Corrected Text</th>
            </tr>
        </thead>
        <tbody>
       
               
            @if(is_object($item) && isset($item->page_no))
                @php
                    
                    $pageNos = is_string($item->page_no) ? json_decode($item->page_no, true) : [$item->page_no];
                    if (!is_array($pageNos)) {
                        $pageNos = explode(',', $item->page_no);
                    }
        
                    $clauseNos = is_string($item->clause_no) ? json_decode($item->clause_no, true) : [$item->clause_no];
                    if (!is_array($clauseNos)) {
                        $clauseNos = explode(',', $item->clause_no);
                    }
        
                    $currentStatements = is_string($item->current_statement) ? json_decode($item->current_statement, true) : [$item->current_statement];
                    if (!is_array($currentStatements)) {
                        $currentStatements = explode(',', $item->current_statement);
                    }
        
                    $correctedStatements = is_string($item->corrected_statement) ? json_decode($item->corrected_statement, true) : [$item->corrected_statement];
                    if (!is_array($correctedStatements)) {
                        $correctedStatements = explode(',', $item->corrected_statement);
                    }
        
                    // Ensure all arrays have same size
                    $maxCount = max(count($pageNos), count($clauseNos), count($currentStatements), count($correctedStatements));
                @endphp
        
                @for ($i = 0; $i < $maxCount; $i++)
                    <tr style=" background-color: #00000091;">
                        <td style="text-align: center; padding: 8px;border: 1px solid #ddd;color: white;">
                            {{ trim($pageNos[$i] ?? '') }}
                        </td>
                        <td style="text-align: center; padding: 8px;border: 1px solid #ddd;color: white;">
                            {{ trim($clauseNos[$i] ?? '') }}
                        </td>
                        <td style="text-align: center; padding: 8px;border: 1px solid #ddd;color: white;">
                            {{ trim($currentStatements[$i] ?? '') }}
                        </td>
                        <td style="text-align: center; padding: 8px;border: 1px solid #ddd;color: white;">
                            {{ trim($correctedStatements[$i] ?? '') }}
                        </td>
                    </tr>
                @endfor
            @else
                <pre>{{ dd($wodata) }}</pre>
            @endif
       


            
        </tbody>
    </table>
     <div style="    padding: 0px 30px" >
        <p style="" >Plase make the above changes and reissue the WO to us .</p>
        <p style="" > Ww are grateful for the opportunity given by your organization . </p>
        <p style="" > Best Regards, </p>
        <p style="margin-top: -13px;" > Tender Executive, </p>
        <p style="margin-top: -13px;" > Volks Energie Pvt. Ltd. </p>
        <p style="" > TE Mobile no.</p>
        <p style="margin-top: -13px;" > TE Email id. </p>
        <p style="" > VE Address </p>
        
        
        
        
    </div>
@endforeach
</body>
</html>

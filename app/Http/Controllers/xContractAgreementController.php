<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Email;
use App\Mail\Acceptance;
use App\Mail\Acceptance_no;
use App\Models\Basic_detail;
use App\Models\Wo_acceptance;
use App\Models\Wo_acceptance_yes;
use App\Models\Wodetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Crypt;

class ContractAgreementController extends Controller
{
    //
    
    
    function contract_dashboardview(){
             $data=Wodetails::where('status','1')->get();
             $basic_data= Basic_detail::where('status','1')->get();
             $tendername = DB::table('tender_infos')->get();
     
         return view('contract_agreement.contract_dashboardview',['wo_data'=>$data,'basic_data'=>$basic_data,'tendername'=>$tendername]);
        }
        
     public function uplade_contract_agereement(Request $request)
        {
         
            $data= Wodetails::where('id',$request->id)->first();
                if ($request->contract_agreement) {
                            $img = time() . 'contract.' . $request->contract_agreement->extension();
                            $request->contract_agreement->move(public_path('uploads/applicable'), $img);
                            $data->contract_agreement = $img;
                }
                if ($request->client_signed) {
                            $img = time() . 'signed.' . $request->client_signed->extension();
                            $request->client_signed->move(public_path('uploads/applicable'), $img);
                            $data->client_signed = $img;
                }
             $data->save();
           
             return redirect()->back();
        }
        
        
     function viewbuttencontract($id){
        
            $datawo= Wodetails::where('id', Crypt::decrypt($id))->first();
            $wo_acceptance_yes= Wo_acceptance_yes::where('basic_detail_id', $datawo->basic_detail_id)->first();
    
            $data['tender_info'] = DB::table('tender_infos')->get();
            
                $basic=Basic_detail::where('id',$datawo->basic_detail_id)->first();
                
                $datawo->organization = json_decode($datawo->organization, true);
                $datawo->departments = json_decode($datawo->departments, true);
                $datawo->name = json_decode($datawo->name, true);
                $datawo->designation = json_decode($datawo->designation, true);
                $datawo->phone = json_decode($datawo->phone, true);
                $datawo->email = json_decode($datawo->email, true);
                
                // $wo_acceptance_yes->page_no = json_decode($wo_acceptance_yes->page_no, true);
                // $wo_acceptance_yes->clause_no = json_decode($wo_acceptance_yes->clause_no, true);
                // $wo_acceptance_yes->current_statement = json_decode($wo_acceptance_yes->current_statement, true);
                // $wo_acceptance_yes->corrected_statement = json_decode($wo_acceptance_yes->corrected_statement, true);
                if ($wo_acceptance_yes !== null) { 
                    $wo_acceptance_yes->page_no = !empty($wo_acceptance_yes->page_no) ? json_decode($wo_acceptance_yes->page_no, true) : [];
                    $wo_acceptance_yes->clause_no = !empty($wo_acceptance_yes->clause_no) ? json_decode($wo_acceptance_yes->clause_no, true) : [];
                    $wo_acceptance_yes->current_statement = !empty($wo_acceptance_yes->current_statement) ? json_decode($wo_acceptance_yes->current_statement, true) : [];
                    $wo_acceptance_yes->corrected_statement = !empty($wo_acceptance_yes->corrected_statement) ? json_decode($wo_acceptance_yes->corrected_statement, true) : [];
                } else {
                    $wo_acceptance_yes = new Wo_acceptance_yes();
                    $wo_acceptance_yes->page_no = [];
                    $wo_acceptance_yes->clause_no = [];
                    $wo_acceptance_yes->current_statement = [];
                    $wo_acceptance_yes->corrected_statement = [];
                }
             
            return view('contract_agreement.view_butten',$data, ['basic'=>$basic,'wodetails'=>$datawo,'acceptance_yes'=>$wo_acceptance_yes]);
        }
    
}

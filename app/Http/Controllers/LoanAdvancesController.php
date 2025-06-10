<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanAdvancesRequest;
use App\Http\Requests\UpdateLoanAdvancesRequest;
use App\Models\Loanadvances;
use App\Models\Loanpartname;
use App\Models\Dueemi;
use App\Models\Tdsrecovery;
use Carbon\Carbon;
use Crypt;
use URL;
use File;
use DB;
use Illuminate\Http\Request; // Import the Request class

use Validator, Redirect, Response;

class LoanAdvancesController extends Controller
{
   
    public function loanadvances()
    
    {
        
        $data['loanadvances']=Loanadvances::where('status','1')->with('loanadvances')->with('dueemi')->get();
       
     return view('loanadvances.loanadvances',$data);
    }
   


   
    public function loanadvancesadd()
    
    {
        
    $data['loanpartname']=  Loanpartname::where('status','1')->get();  
     return view('loanadvances.loanadvancesadd',$data);
    }
    
   public function loanadvancescreate(Request $request)
    {
         $request->validate([
      
        'emipayment_date' => 'required|date',
        'bankloan_schedule' => 'required|mimes:jpg,png,|max:10240', 
         'loan_schedule' => 'required|mimes:xlsx,xls,pdf|max:10240'
        
    ]);
  
        $loanadvances = new Loanadvances();
        $loanadvances->loanparty_name = $request->loanparty_name;
        $loanadvances->bank_name = $request->bank_name;
        $loanadvances->typeof_loan = $request->typeof_loan;
        $loanadvances->loanamount = $request->loanamount;
        $loanadvances->sanctionletter_date = $request->sanctionletter_date;
        $loanadvances->emipayment_date = $request->emipayment_date;
        $loanadvances->lastemi_date = $request->lastemi_date;
        $loanadvances->chargemca_website = $request->chargemca_website;
        $loanadvances->tdstobedeductedon_interest = $request->tdstobedeductedon_interest;

        if ($request->sanction_letter) {
            $img = time() . 'sanction_letter.' . $request->sanction_letter->extension();
            $request->sanction_letter->move(public_path('upload/loanadvances'), $img);
            $loanadvances->sanction_letter = $img;
        }

        if ($request->bankloan_schedule) {
            $img = time() . 'bankloan_schedule.' . $request->bankloan_schedule->extension();
            $request->bankloan_schedule->move(public_path('upload/loanadvances'), $img);
            $loanadvances->bankloan_schedule = $img;
        }

        if ($request->loan_schedule) {
            $img = time() . 'loan_schedule.' . $request->loan_schedule->extension();
            $request->loan_schedule->move(public_path('upload/loanadvances'), $img);
            $loanadvances->loan_schedule = $img;
        }

        $loanadvances->ip = $_SERVER['REMOTE_ADDR'];
        $loanadvances->strtotime = Carbon::parse($loanadvances->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $loanadvances->save();

        return redirect(route('loanadvances'))->with('success', 'Data successfully added.');;
    }
    function loanadvancesdelete($id){
         $id=Crypt::decrypt($id);
        $loanadvancesdelete = Loanadvances::findOrFail($id);
      $image_path_loan_schedule = public_path("upload/loanadvances/{$loanadvancesdelete->loan_schedule}");
$image_path_bankloan_schedule = public_path("upload/loanadvances/{$loanadvancesdelete->bankloan_schedule}");
$image_path_sanction_letter = public_path("upload/loanadvances/{$loanadvancesdelete->sanction_letter}");

// Check and delete each file if it exists
if (File::exists($image_path_loan_schedule) && !File::isDirectory($image_path_loan_schedule)) {
    unlink($image_path_loan_schedule);
}

if (File::exists($image_path_bankloan_schedule) && !File::isDirectory($image_path_bankloan_schedule)) {
    unlink($image_path_bankloan_schedule);
}

if (File::exists($image_path_sanction_letter) && !File::isDirectory($image_path_sanction_letter)) {
    unlink($image_path_sanction_letter);
}

// Delete the loan advance record
$loanadvancesdelete->delete();
        
      
         
         return redirect()->back()->with('success', 'Data successfully Delete.');;
    }
    
    function loanadvancesupdate(Request $request){
         $data['loanpartname']=  Loanpartname::where('status','1')->get(); 
         $data['loanadvances'] = Loanadvances::where('id', Crypt::decrypt($request->id))->first();
    return view('loanadvances.loanadvancesedit', $data);
    }
    
    
    function loanadvancesedit(Request $request){
   
    //   $request->validate([
      
    //     'bankloan_schedule' => 'required', 
    //      'loan_schedule' => 'required|mimes:xlsx,xls,pdf|max:10240'
        
    // ]);
          $loanadvances = Loanadvances::where('id', $request->id)->first();
        
        $loanadvances->loanparty_name = $request->loanparty_name;
        $loanadvances->bank_name = $request->bank_name;
        $loanadvances->typeof_loan = $request->typeof_loan;
        $loanadvances->loanamount = $request->loanamount;
        $loanadvances->sanctionletter_date = $request->sanctionletter_date;
        $loanadvances->emipayment_date = $request->emipayment_date;
        $loanadvances->lastemi_date = $request->lastemi_date;
        $loanadvances->chargemca_website = $request->chargemca_website;
        $loanadvances->tdstobedeductedon_interest = $request->tdstobedeductedon_interest;

        if ($request->sanction_letter) {
            $img = time() . 'sanction_letter.' . $request->sanction_letter->extension();
            $request->sanction_letter->move(public_path('upload/loanadvances'), $img);
            $loanadvances->sanction_letter = $img;
        }

        if ($request->bankloan_schedule) {
            $img = time() . 'bankloan_schedule.' . $request->bankloan_schedule->extension();
            $request->bankloan_schedule->move(public_path('upload/loanadvances'), $img);
            $loanadvances->bankloan_schedule = $img;
        }

        if ($request->loan_schedule) {
            $img = time() . 'loan_schedule.' . $request->loan_schedule->extension();
            $request->loan_schedule->move(public_path('upload/loanadvances'), $img);
            $loanadvances->loan_schedule = $img;
        }

      
        $loanadvances->save();

        return redirect(route('loanadvances'))->with('success', 'Data successfully Update.');;
    }
    
  
     
      
      /// DUE ///
    
     public function dueview($id) {
       $ID = Crypt::decrypt($id);
         $loneid=Loanadvances::where('id',$ID)->first();
       $data=Dueemi::where('status','1')->where('loneid',$ID)->get();
     return view('loanadvances.dueview',['due_id'=>$ID,'viewdata'=>$data,'loneid'=>$loneid]);
    }
    
    
    
    /// DUE END ///
      
       //Dueemi//
    
    function dueemiadd(Request $request){
        
        $loneid=Loanadvances::where('id',$request->loneid)->first();
        $loneid->emipayment_date=$request->emi_date;
        $loneid->save();
      
        $dueemi=new Dueemi();
        $dueemi->loneid=$request->loneid;
        $dueemi->emi_date=$request->emi_date;
        $dueemi->principle_paid=$request->principle_paid;
        $dueemi->interest_paid=$request->interest_paid;
        $dueemi->tdstobe_recovered=$request->tdstobe_recovered;
        $dueemi->penal_charges_paid=$request->penal_charges_paid;
         $dueemi->ip = $_SERVER['REMOTE_ADDR'];
        $dueemi->strtotime = Carbon::parse($dueemi->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $dueemi->save();
        return redirect()->back()->with('success', 'Data successfully added.');
    }
    
     public function dueemiupdate($id) {
       $dueid = Crypt::decrypt($id);
       $datafirst=Dueemi::where('id',$dueid)->first();
        $data=Dueemi::where('status','1')->where('loneid',$datafirst->loneid)->get();
           $loneid=Loanadvances::where('id',$datafirst->loneid)->first();
     return view('loanadvances.dueview',['updatedata'=>$datafirst,'viewdata'=>$data,'loneid'=>$loneid]);
    }
    
     public function dueemiupdatepost(Request $request, $id) {
         $datafirst=Dueemi::where('id',$id)->first();
         
         $loneid=Loanadvances::where('id',$datafirst->loneid)->first();
        $loneid->emipayment_date=$request->emi_date;
        $loneid->save();
         
        $datafirst->emi_date=$request->emi_date;
        $datafirst->principle_paid=$request->principle_paid;
        $datafirst->interest_paid=$request->interest_paid;
        $datafirst->tdstobe_recovered=$request->tdstobe_recovered;
        $datafirst->penal_charges_paid=$request->penal_charges_paid;
      $datafirst->save();

     return redirect()->route('dueview', ['id' => Crypt::encrypt($datafirst->loneid)])->with('success', 'Data successfully Update.');;

    }
    
    function dueemidelete($id){
         $id=Crypt::decrypt($id);
        $duedelete = Dueemi::findOrFail($id);
        $duedelete->delete();
         return redirect()->back()->with('success', 'Data successfully Delete.');;
    }
      //Dueemi//
      
      
     //TDS Recovery//
     
      public function tdsrecoveryview($id) {
       $ID = Crypt::decrypt($id);
       $data=Tdsrecovery::where('status','1')->where('loneid',$ID)->get();
     return view('loanadvances.tdsrecoveryview',['due_id'=>$ID,'viewdata'=>$data]);
    }
 
     
     
    function tdsrecoveryadd(Request $request){
        
        
        $tdsrecovery=new Tdsrecovery();
        $tdsrecovery->loneid=$request->loneid;
        $tdsrecovery->tds_amount=$request->tds_amount;
        
         if ($request->tds_document) {
            $img = time() . 'tdsdocument.' . $request->tds_document->extension();
            $request->tds_document->move(public_path('upload/tdsrecovery'), $img);
            $tdsrecovery->tds_document = $img;
        }
      
        $tdsrecovery->tds_date=$request->tds_date;
        $tdsrecovery->tdsrecoverybank_details=$request->tdsrecoverybank_details;
       
         $tdsrecovery->ip = $_SERVER['REMOTE_ADDR'];
        $tdsrecovery->strtotime = Carbon::parse($tdsrecovery->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $tdsrecovery->save();
        
         return redirect()->back()->with('success', 'Data successfully added.');;
    }
    
    
    public function tdsrecoveryupdate($id) {
       $dueid = Crypt::decrypt($id);
       $datafirst=Tdsrecovery::where('id',$dueid)->first();
        $data=Tdsrecovery::where('status','1')->where('loneid',$datafirst->loneid)->get();
      
     return view('loanadvances.tdsrecoveryview',['updatedata'=>$datafirst,'viewdata'=>$data]);
    }
      
      
       public function tdsrecoveryupdatepost(Request $request, $id) {
         $datafirst=Tdsrecovery::where('id',$id)->first();
         $datafirst->loneid=$request->loneid;
        $datafirst->tds_amount=$request->tds_amount;
        
         if ($request->tds_document) {
            $img = time() . 'tdsdocument.' . $request->tds_document->extension();
            $request->tds_document->move(public_path('upload/tdsrecovery'), $img);
            $datafirst->tds_document = $img;
        }
      
        $datafirst->tds_date=$request->tds_date;
        $datafirst->tdsrecoverybank_details=$request->tdsrecoverybank_details;
      
        $datafirst->save();

     return redirect()->route('tdsrecoveryview', ['id' => Crypt::encrypt($datafirst->loneid)])->with('success', 'Data successfully Update.');;

    }
    
      function tdsrecoverydelete($id){
         $id=Crypt::decrypt($id);
        $duedelete = Tdsrecovery::findOrFail($id);
        $duedelete->delete();
         return redirect()->back()->with('success', 'Data successfully Delete.');;
    }
      
      
      //TDS Recovery//
      
      
      // Loan Close
      
       public function loancloseupdate($id) {
           $ID = Crypt::decrypt($id);
           $data=Loanadvances::where('id',$ID)->first();
         return view('loanadvances.loancloseupdate',['loanclose'=>$data]);
        }
        
        function loancloseupdate_post(Request $request){
   
          $loanclose = Loanadvances::where('id', $request->id)->first();
        
        $loanclose->loan_close_status = '1';

        if ($request->banknoc_document) {
            $img = time() . 'loanclose.' . $request->banknoc_document->extension();
            $request->banknoc_document->move(public_path('upload/loanclose'), $img);
            $loanclose->banknoc_document = $img;
        }

        if ($request->closurecreated_mca) {
            $img = time() . 'loanclose.' . $request->closurecreated_mca->extension();
            $request->closurecreated_mca->move(public_path('upload/loanclose'), $img);
            $loanclose->closurecreated_mca = $img;
        }


      
        $loanclose->save();

        return redirect(route('loanadvances'))->with('success', 'Data successfully Update.');;
    }
      
      // Loan Close End

  
}

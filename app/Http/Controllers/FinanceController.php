<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Mail\RentAgreementMail;
use Illuminate\Http\Request;
use App\Models\Financialyear;
use App\Models\Documenttype;
use App\Models\Finance;
use App\Models\Rentagreement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    public function finance()
    {
        $data['financedata'] = Finance::with('financialyear')->with('documenttype')->get();
        return view('finance.finance', $data);
    }
    public function finance_add()
    {
        $data['financialyear'] = Financialyear::get();
        $data['documenttype'] = Documenttype::get();
        return view('finance.finance_add', $data);
    }

    public function finance_post(Request $request)
    {
        $finance_post = new Finance();
        $finance_post->document_name = $request->document_name;
        $finance_post->document_type = $request->document_type;
        $finance_post->financial_year = $request->financial_year;
        $finance_post->ip = $_SERVER['REMOTE_ADDR'];
        $finance_post->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
        if ($request->hasFile('image')) {
            $imagePaths = [];

            foreach ($request->file('image') as $file) {
                $imageName = time() . rand(1000, 9999) . '.' . $file->extension();
                $file->move(public_path('upload/finance/'), $imageName);
                $imagePaths[] = $imageName;
            }

            $finance_post->image = json_encode($imagePaths);
            $finance_post->save();
        }

        $finance_post->save();
        return redirect(route('finance'))->with('success', 'Record added successfully.');
    }


    public function finance_delete($id)
    {
        $finance_delete = Finance::where('id', Crypt::decrypt($id))->delete();
        return redirect()->back();
    }

    public function finance_edit(Request $request)
    {
        $data['financialyear'] = Financialyear::get();
        $data['documenttype'] = Documenttype::get();
        $data['finance_edit'] = Finance::where('id', Crypt::decrypt($request->id))->first();
        return view('finance.finance_edit', $data);
    }

    public function finance_update(Request $request)
    {
        $update = Finance::where('id', $request->id)->first();
        $update->document_name = $request->document_name;
        $update->document_type = $request->document_type;
        $update->financial_year = $request->financial_year;
        $update->ip = $_SERVER['REMOTE_ADDR'];
        $update->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
        if ($request->hasFile('image')) {
            $imagePaths = [];

            foreach ($request->file('image') as $file) {
                $imageName = time() . rand(1000, 9999) . '.' . $file->extension();
                $file->move(public_path('upload/finance/'), $imageName);
                $imagePaths[] = $imageName;
            }

            $update->image = json_encode($imagePaths);
            $update->save();
        }
        $update->save();
        return redirect(route('finance'))->with('success', 'Record updated successfully.');
    }

    public function image_uplode(Request $request)
    {
        $update = Finance::where('id', $request->ac_id)->first();
        if (!$update) {
            return redirect()->back()->withErrors('Record not found');
        }
        if ($request->hasFile('image')) {
            $existingImages = $update->image ? json_decode($update->image, true) : [];

            foreach ($request->file('image') as $file) {
                $imgName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('upload/finance/'), $imgName);
                $existingImages[] = $imgName;
            }

            $update->image = json_encode($existingImages);
            $update->save();
        }
        return redirect()->back()->with('success', 'Images uploaded successfully.');
    }


    public function RentExpiryMail()
    {
        $rentAgreement = Rentagreement::get();
        Log::info('All Rent Aggreement: ' . json_encode($rentAgreement));
        $formattedEndDates = [];
        $daysDifferences = [];
        $currentDate = Carbon::now();
        $adminMail = User::where('role', 'admin')->first()->email;
        $coo = User::where('role', 'coordinator')->first();
        $cooName = $coo->name;
        $cooMail = $coo->email;
        $cooPass = $coo->app_password;

        $emaildata = ['accounts@volksenergie.in'];

        $mailer = MailHelper::configureMailer($cooMail, $cooPass, $cooName) ? 'dynamic' : 'smtp';

        foreach ($rentAgreement as $rentdata) {
            Log::info('Rent Aggreement Data: ' . json_encode($rentdata));

            $endDate = $rentdata->end_date;
            $endDateTimestamp = Carbon::createFromFormat('Y-m-d', $endDate, 'UTC')->startOfDay();
            $currentDate = Carbon::now('UTC')->startOfDay();
            $daysDifference = $currentDate->diffInDays($endDateTimestamp, false);
            $daysDifference = (int) $daysDifference;
            $daysDifferences[] = $daysDifference;

            Log::info('Dates differences: ' . json_encode($daysDifferences));

            if ($daysDifference == 90) {
                $maildata = [
                    'subject' => 'Rent Agreement expiring in ' . $daysDifference . ' days.',
                    'firstparty' => $rentdata->first_party,
                    'days' => $daysDifference,
                    'secondparty' => $rentdata->second_party,
                    'rentamount' => $rentdata->rent_amount,
                    'rentincrementatexpiry' => $rentdata->rent_increment_at_expiry,
                    'email_file' => url('admin/rent_edit/' . Crypt::encrypt($rentdata->id)),
                    'coordinator' => $cooName,
                ];

                $mail = Mail::mailer($mailer)
                    ->to('accounts@volksenergie.in')
                    ->cc([$adminMail, $cooMail])
                    ->send(new RentAgreementMail($maildata));
                if ($mail) {
                    Log::info('Mail sent to admin for rent expiry: ' . json_encode($maildata));
                } else {
                    Log::error('Mail not sent: ' . $mail);
                }
            } elseif ($daysDifference == 60) {
                $maildata = [
                    'subject' => 'Rent Agreement expiring in ' . $daysDifference . ' days.',
                    'firstparty' => $rentdata->first_party,
                    'secondparty' => $rentdata->second_party,
                    'days' => $daysDifference,
                    'rentamount' => $rentdata->rent_amount,
                    'rentincrementatexpiry' => $rentdata->rent_increment_at_expiry,
                    'email_file' => url('admin/rent_edit/' . Crypt::encrypt($rentdata->id)),
                    'coordinator' => $cooName,
                ];

                $mail = Mail::mailer($mailer)
                    ->to('accounts@volksenergie.in')
                    ->cc([$adminMail, $cooMail])
                    ->send(new RentAgreementMail($maildata));
                if ($mail) {
                    Log::info('Mail sent to admin for rent expiry: ' . json_encode($maildata));
                } else {
                    Log::error('Mail not sent: ' . $mail);
                }
            } elseif ($daysDifference == 45) {
                $maildata = [
                    'subject' => 'Rent Agreement expiring in ' . $daysDifference . ' days.',
                    'firstparty' => $rentdata->first_party,
                    'secondparty' => $rentdata->second_party,
                    'days' => $daysDifference,
                    'rentamount' => $rentdata->rent_amount,
                    'rentincrementatexpiry' => $rentdata->rent_increment_at_expiry,
                    'email_file' => url('admin/rent_edit/' . Crypt::encrypt($rentdata->id)),
                    'coordinator' => $cooName,
                ];

                $mail = Mail::mailer($mailer)
                    ->to('accounts@volksenergie.in')
                    ->cc([$adminMail, $cooMail])
                    ->send(new RentAgreementMail($maildata));
                if ($mail) {
                    Log::info('Mail sent to admin for rent expiry: ' . json_encode($maildata));
                } else {
                    Log::error('Mail not sent: ' . $mail);
                }
            } elseif ($daysDifference == 30) {
                $maildata = [
                    'subject' => 'Rent Agreement expiring in ' . $daysDifference . ' days.',
                    'firstparty' => $rentdata->first_party,
                    'secondparty' => $rentdata->second_party,
                    'days' => $daysDifference,
                    'rentamount' => $rentdata->rent_amount,
                    'rentincrementatexpiry' => $rentdata->rent_increment_at_expiry,
                    'email_file' => url('admin/rent_edit/' . Crypt::encrypt($rentdata->id)),
                    'coordinator' => $cooName,
                ];

                $mail = Mail::mailer($mailer)
                    ->to('accounts@volksenergie.in')
                    ->cc([$adminMail, $cooMail])
                    ->send(new RentAgreementMail($maildata));
                if ($mail) {
                    Log::info('Mail sent to admin for rent expiry: ' . json_encode($maildata));
                } else {
                    Log::error('Mail not sent: ' . $mail);
                }
            } elseif ($daysDifference == 15) {
                $maildata = [
                    'subject' => 'Rent Agreement expiring in ' . $daysDifference . ' days.',
                    'firstparty' => $rentdata->first_party,
                    'secondparty' => $rentdata->second_party,
                    'days' => $daysDifference,
                    'rentamount' => $rentdata->rent_amount,
                    'rentincrementatexpiry' => $rentdata->rent_increment_at_expiry,
                    'email_file' => url('admin/rent_edit/' . Crypt::encrypt($rentdata->id)),
                    'coordinator' => $cooName,
                ];

                $mail = Mail::mailer($mailer)
                    ->to('accounts@volksenergie.in')
                    ->cc([$adminMail, $cooMail])
                    ->send(new RentAgreementMail($maildata));
                if ($mail) {
                    Log::info('Mail sent to admin for rent expiry: ' . json_encode($maildata));
                } else {
                    Log::error('Mail not sent: ' . $mail);
                }
            } elseif ($daysDifference == 7) {
                $maildata = [
                    'subject' => 'Rent Agreement expiring in ' . $daysDifference . ' days.',
                    'firstparty' => $rentdata->first_party,
                    'secondparty' => $rentdata->second_party,
                    'days' => $daysDifference,
                    'rentamount' => $rentdata->rent_amount,
                    'rentincrementatexpiry' => $rentdata->rent_increment_at_expiry,
                    'email_file' => url('admin/rent_edit/' . Crypt::encrypt($rentdata->id)),
                    'coordinator' => $cooName,
                ];

                $mail = Mail::mailer($mailer)
                    ->to('accounts@volksenergie.in')
                    ->cc([$adminMail, $cooMail])
                    ->send(new RentAgreementMail($maildata));
                if ($mail) {
                    Log::info('Mail sent to admin for rent expiry: ' . json_encode($maildata));
                } else {
                    Log::error('Mail not sent: ' . $mail);
                }
            } elseif ($daysDifference == 3) {
                $maildata = [
                    'subject' => 'Rent Agreement expiring in ' . $daysDifference . ' days.',
                    'firstparty' => $rentdata->first_party,
                    'secondparty' => $rentdata->second_party,
                    'days' => $daysDifference,
                    'rentamount' => $rentdata->rent_amount,
                    'rentincrementatexpiry' => $rentdata->rent_increment_at_expiry,
                    'email_file' => url('admin/rent_edit/' . Crypt::encrypt($rentdata->id)),
                    'coordinator' => $cooName,
                ];

                $mail = Mail::mailer($mailer)
                    ->to('accounts@volksenergie.in')
                    ->cc([$adminMail, $cooMail])
                    ->send(new RentAgreementMail($maildata));
                if ($mail) {
                    Log::info('Mail sent to admin for rent expiry: ' . json_encode($maildata));
                } else {
                    Log::error('Mail not sent: ' . $mail);
                }
            } elseif ($daysDifference == 1) {
                $maildata = [
                    'subject' => 'Rent Agreement expiring in ' . $daysDifference . ' days.',
                    'firstparty' => $rentdata->first_party,
                    'days' => $daysDifference,
                    'secondparty' => $rentdata->second_party,
                    'rentamount' => $rentdata->rent_amount,
                    'rentincrementatexpiry' => $rentdata->rent_increment_at_expiry,
                    'email_file' => url('admin/rent_edit/' . Crypt::encrypt($rentdata->id)),
                    'coordinator' => $cooName,
                ];

                $mail = Mail::mailer($mailer)
                    ->to('accounts@volksenergie.in')
                    ->cc([$adminMail, $cooMail])
                    ->send(new RentAgreementMail($maildata));
                if ($mail) {
                    Log::info('Mail sent to admin for rent expiry: ' . json_encode($maildata));
                } else {
                    Log::error('Mail not sent: ' . $mail);
                }
            } else {
                Log::info('No mail sent for rent expiry: ' . json_encode($daysDifference));
            }
        }
        return true;
    }

    public function rent()
    {
        $data['rentdata'] = Rentagreement::get();
        return view('finance.rent', $data);
    }

    public function rent_add()
    {
        return view('finance.rent_add');
    }

    public function rent_post(Request $request)
    {
        $rent_post = new Rentagreement();
        $rent_post->first_party = $request->first_party;
        $rent_post->second_party = $request->second_party;
        $rent_post->rent_amount = $request->rent_amount;
        $rent_post->security_deposit = $request->security_deposit;
        $rent_post->start_date = $request->start_date;
        $rent_post->end_date = $request->end_date;
        $rent_post->rent_increment_at_expiry = $request->rent_increment_at_expiry;
        if ($request->image) {
            $img3 = time() . '.' . $request->image->extension();
            $request->image->move(public_path('upload/finance/'), $img3);
            $rent_post->image = $img3;
            $rent_post->save();
        }
        $rent_post->remarks = $request->remarks;
        $rent_post->ip = $_SERVER['REMOTE_ADDR'];
        $rent_post->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $rent_post->save();

        // $mail = $this->RentExpiryMail();
        // if ($mail) {
        //     Log::info('Mail sent to admin for rent expiry');
        // } else {
        //     Log::error('Mail not sent: ' . $mail);
        // }
        return redirect(route('rent'))->with('success', 'Record added successfully.');
    }

    public function rent_delete($id)
    {
        $rent_delete = Rentagreement::where('id', Crypt::decrypt($id))->delete();
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

    public function rent_edit(Request $request)
    {
        $data['rent_edit'] = Rentagreement::where('id', Crypt::decrypt($request->id))->first();
        return view('finance.rent_edit', $data);
    }

    public function rent_update(request $request)
    {
        $rent_update = Rentagreement::where('id', $request->id)->first();
        $rent_update->first_party = $request->first_party;
        $rent_update->second_party = $request->second_party;
        $rent_update->rent_amount = $request->rent_amount;
        $rent_update->security_deposit = $request->security_deposit;
        $rent_update->start_date = $request->start_date;
        $rent_update->end_date = $request->end_date;
        $rent_update->rent_increment_at_expiry = $request->rent_increment_at_expiry;
        if ($request->image) {
            $img3 = time() . '.' . $request->image->extension();
            $request->image->move(public_path('upload/finance/'), $img3);
            $rent_update->image = $img3;
            $rent_update->save();
        }
        $rent_update->remarks = $request->remarks;
        $rent_update->save();

        // $mail = $this->RentExpiryMail();
        // if ($mail) {
        //     Log::info('Mail sent to admin for rent expiry');
        // } else {
        //     Log::error('Mail not sent: ' . $mail);
        // }

        return redirect(route('rent'))->with('success', 'Record updated successfully.');
    }

    // public function email(Request $request)
    // {
    //     $email = new Email($request->subject, $request->message, $request->to_email);
    //     Mail::to($request->to_email)->send($email);
    //     return back()->with('success', 'Email sent successfully!');
    // }

    public function updateRentStatus()
    {
        $rentdataItems = Rentagreement::all();

        foreach ($rentdataItems as $rentdataItem) {
            $currentDate = Carbon::now('Asia/Karachi');
            $endDate = Carbon::parse($rentdataItem->end_date)->timezone('Asia/Karachi');
            if ($currentDate->gt($endDate)) {
                $rentdataItem->status = 0;
            } else {
                $rentdataItem->status = 1;
            }
            $rentdataItem->save();
        }
        return redirect()->back();
    }

    public function documenttype()
    {
        $data['documenttype'] = Documenttype::get();
        return view('master.documenttype', $data);
    }

    public function documenttype_add(Request $request)
    {
        $request->validate([
            'document_type' => 'required|unique:documenttypes,document_type',
        ], [
            'document_type.unique' => 'The Document type already exists.',
        ]);
        $documenttype = new Documenttype();
        $documenttype->document_type = $request->document_type;
        $documenttype->ip = $_SERVER['REMOTE_ADDR'];
        $documenttype->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $documenttype->save();
        return redirect()->back()->with('success', 'Record added successfully.');
    }

    public function documenttype_del($id)
    {
        $documenttypedel = Documenttype::where('id', Crypt::decrypt($id))->delete();
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

    public function documenttype_edit(Request $request)
    {
        $documenttype = Documenttype::where('id', $request->id)->first();
        $documenttype->document_type = $request->document_type;
        $documenttype->save();
        return redirect()->back();
    }

    public function financialyear()
    {
        $data['financialyear'] = Financialyear::get();
        return view('master.financialyear', $data);
    }

    public function financialyear_add(Request $request)
    {
        $request->validate([
            'financial_year' => 'required|unique:financialyears,financial_year',
        ], [
            'financial_year.unique' => 'The FinancialYear  already exists.',
        ]);
        $financialyear = new Financialyear();
        $financialyear->financial_year = $request->financial_year;
        $financialyear->ip = $_SERVER['REMOTE_ADDR'];
        $financialyear->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;

        $financialyear->save();
        return redirect()->back()->with('success', 'Record added successfully.');
    }

    public function financialyear_del($id)
    {
        $financialyeardel = Financialyear::where('id', Crypt::decrypt($id))->delete();
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

    public function financialyear_edit(Request $request)
    {
        $update = Financialyear::where('id', $request->id)->first();
        $update->financial_year = $request->financial_year;
        $update->save();
        return redirect()->back()->with('success', 'Record updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Frontend;
use App\Models\InvoiceItem;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\Transaction;
use App\Lib\SendServiceEmail;
use App\Models\Deposit;
use PDF;
 
class InvoiceController extends Controller{ 
    
    protected function with($with = []){
        $array = ['order', 'user', 'payments.gateway'];
        return array_merge($array, $with);  
    }  
 
    public function invoices(){  
        $pageTitle = 'All Invoice'; 
        $invoices = $this->invoiceData();
        return view('admin.invoice.all', compact('pageTitle', 'invoices'));
    }

    public function cancelled(){
        $pageTitle = 'Cancelled Invoice'; 
        $invoices = $this->invoiceData('cancelled');
        return view('admin.invoice.all', compact('pageTitle', 'invoices'));
    }

    public function paid(){  
        $pageTitle = 'Paid Invoices'; 
        $invoices = $this->invoiceData('paid');
        return view('admin.invoice.all', compact('pageTitle', 'invoices'));
    }

    public function unpaid(){  
        $pageTitle = 'Unpaid Invoices'; 
        $invoices = $this->invoiceData('unpaid');
        return view('admin.invoice.all', compact('pageTitle', 'invoices'));
    }

    public function paymentPending(){  
        $pageTitle = 'Payment Pending Invoices'; 
        $invoices = $this->invoiceData('paymentPending');
        return view('admin.invoice.all', compact('pageTitle', 'invoices'));
    }

    public function refunded(){  
        $pageTitle = 'Refunded Invoices'; 
        $invoices = $this->invoiceData('refunded');
        return view('admin.invoice.all', compact('pageTitle', 'invoices'));
    }

    public function details($id){   
        $pageTitle = 'Invoice Details';
        $invoice = Invoice::findOrFail($id);
        return view('admin.invoice.details', compact('pageTitle', 'invoice'));
    }
    
    public function updateInvoice(Request $request){
 
        $request->validate([
            'invoice_id'=>'required',
            'created'=>'required|date_format:d-m-Y',
            'due_date'=>'required|date_format:d-m-Y',
            'paid_date'=>'nullable|date_format:d-m-Y',
            'items'=>'sometimes|array',
            'items.amount*'=>'required|numeric',
            'status'=>'required|integer|in:'.Invoice::status(true),
        ]);
        /**
        * For knowing about status 
        * @see \App\Models\Invoice go to status method 
        */
        $invoice = Invoice::where('status', '!=', 5)->findOrFail($request->invoice_id); //5 means Refunded
      
        foreach(@$request->items ?? [] as $id => $value){ 
            $item = InvoiceItem::where('id', $id)->where('invoice_id', $invoice->id)->first(); 
          
            if(!$item){
                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->user_id = $invoice->user_id;
            }

            if(substr($value['amount'], 0, 1) == '-'){
                $item->trx_type = '-';
            }else{
                $item->trx_type = '+';
            }

            $item->amount = $value['amount']; 
            $item->description = $value['description'];
            $item->save();
        }
  
        $invoice->amount = $invoice->items->sum('amount');
 
        $invoice->status = $request->status;
        $invoice->created = $request->created;
        $invoice->due_date = $request->due_date;
        $invoice->paid_date = $request->paid_date;
        $invoice->admin_notes = $request->admin_notes;

        $invoice->save();

        $notify[] = ['success', 'Invoice items updated successfully'];
        return back()->withNotify($notify);
    }

    public function download($id, $view = null){
    
        $invoice = Invoice::findOrFail($id);
        $items = $invoice->items->groupBy(['hosting_id', 'domain_id']);

        $address = Frontend::where('data_keys','invoice_address.content')->first();
        $user = $invoice->user;
        $pageTitle = 'Invoice';

        $data = [
            'pageTitle' => $pageTitle, 
            'invoice' => $invoice,
            'user' => $user,
            'address' => $address,
            'items' => $items,
        ];

        $pdf = PDF::loadView('invoice', $data)->setOptions(['defaultFont' => 'sans-serif', 'enable_remote' => true]);

        if($view){
            return $pdf->stream('invoice.pdf');
        }

        return $pdf->download('invoice.pdf');
    }

    public function deleteInvoiceItem(Request $request){

        $request->validate([
            'invoice_id'=>'required',
            'id'=>'required',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        InvoiceItem::where('id', $request->id)->where('invoice_id', $invoice->id)->firstOrFail()->delete();
        $totalAmount = InvoiceItem::where('invoice_id', $invoice->id)->sum('amount');

        $invoice->amount = $totalAmount;        
        $invoice->save();        

        $notify[] = ['success', 'Invoice item deleted successfully'];
        return back()->withNotify($notify);
    }

    public function refundInvoice(Request $request){
    
        $request->validate([
            'invoice_id'=>'required',
            'amount'=>'nullable|numeric|gt:0',
        ]);

        $invoice = Invoice::paid()->findOrFail($request->invoice_id);
        $amount = $request->amount;
        $user = $invoice->user;

        if($amount > $invoice->amount){
            $notify[] = ['error', 'Sorry, Refund amount must be less than invoice amount'];
            return back()->withNotify($notify);
        }

        $refundAmount = $amount ?? $invoice->amount;

        $invoice->status = 5; //5 means Refunded
        /**
        * For knowing about status 
        * @see \App\Models\Invoice go to status method 
        */
        $invoice->refund_amount = $refundAmount;
        $invoice->save();

        $user->balance += $refundAmount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $refundAmount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Invoice refund';
        $transaction->trx =  getTrx();
        $transaction->save();

        SendServiceEmail::invoiceRefund($invoice, $refundAmount, $transaction->trx);

        $notify[] = ['success', 'Invoice refunded successfully'];
        return back()->withNotify($notify);
    }

    public function domainInvoices($id){
        $domain = Domain::findOrFail($id);
        $pageTitle = 'All Invoices - '.$domain->domain;

        $invoicesItems = InvoiceItem::where('domain_id', $domain->id)->groupBy('invoice_id')
                        ->where('item_type', 1)->with('invoice', 'domain', 'user')->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('admin.invoice.all_item', compact('pageTitle', 'invoicesItems', 'domain'));
    }

    public function hostingInvoices($id){ 
        $hosting = Hosting::findOrFail($id);
        $pageTitle = 'All Invoices';

        $invoicesItems = InvoiceItem::where('hosting_id', $hosting->id)->where('item_type', 0)->groupBy('invoice_id')
                        ->with('invoice', 'hosting', 'user')->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('admin.invoice.all_item', compact('pageTitle', 'invoicesItems', 'hosting'));
    }

    protected function invoiceData($scope = null){

        if($scope){
            $invoices = Invoice::$scope()->with($this->with());
        }else{
            $invoices = Invoice::with($this->with());
        }

        $invoices = $invoices->searchable(['user:username'])->dateFilter()->orderBy('id','desc')->paginate(getPaginate());
        return $invoices;
    }

    public function paymentTransactions($id){
        $invoice = Invoice::findOrFail($id);
        $pageTitle = 'Payment History of Invoice #'.$invoice->id;
        $deposits = Deposit::where('invoice_id', $invoice->id)->with(['user', 'gateway', 'invoice'])->paginate(getPaginate());
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'invoice'));
    }

} 

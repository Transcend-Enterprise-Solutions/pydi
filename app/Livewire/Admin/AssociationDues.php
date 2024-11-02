<?php

namespace App\Livewire\Admin;

use App\Exports\MonthlyPaymentsExport;
use App\Models\MonthlyAssociationDues;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Association Dues')]

class AssociationDues extends Component
{
    use WithPagination;

    public $search;
    public $paymentStatus = 'all';
    public $month;
    public $userId;
    public $homeowner;
    public $thisMonth;
    public $datePaid;
    public $paymentMode;
    public $status;
    public $refNumber;
    public $editStatus;
    public $receivedBy;
    public $viewMonthlyPayment;


    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 

    public function mount(){
        $this->month = now()->format('Y-m');
    }

    public function render()
    {
        $homeowners = User::select([
                'users.id as userId',
                'users.name',
                'users.email',
                'users.profile_photo_path',
                'users.user_role',
                'positions.position',
                'user_data.*',
                'monthly_association_dues.status as payment_status',
                'monthly_association_dues.date_paid',
                'monthly_association_dues.payment_mode'
            ])
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->where('users.user_role', 'homeowner')
            ->leftJoin('positions', 'positions.id', 'users.position_id')
            ->leftJoin('monthly_association_dues', function($join) {
                $join->on('monthly_association_dues.user_id', '=', 'users.id')
                ->where('monthly_association_dues.year', '=', Carbon::parse($this->month)->year)
                ->where('monthly_association_dues.month', '=', Carbon::parse($this->month)->month);
            })
            ->when($this->search, function ($query) {
                return $query->search(trim($this->search));
            })
            ->when($this->paymentStatus !== 'all', function ($query) {
                if ($this->paymentStatus === 'paid') {
                    return $query->whereNotNull('monthly_association_dues.status')
                               ->where('monthly_association_dues.status', '1');
                } else {
                    return $query->where(function($query) {
                        $query->whereNull('monthly_association_dues.status')
                              ->orWhere('monthly_association_dues.status', '!=', '1');
                    });
                }
            })
            ->orderByRaw('CAST(user_data.block AS UNSIGNED) ASC')
            ->orderByRaw('CAST(user_data.lot AS UNSIGNED) ASC')
            ->paginate($this->pageSize);

        return view('livewire.admin.association-dues',[
            'homeowners' => $homeowners,
        ]);
    }

    public function toggleEditPayment($id){
        $this->editStatus = true;
        $this->userId = $id;
        
        $user = User::findOrFail($this->userId);
        $this->homeowner = $user->name;
    
        $monthlyAssocDue = MonthlyAssociationDues::where('user_id', $this->userId)
            ->where('year', Carbon::parse($this->month)->year)
            ->where('month', Carbon::parse($this->month)->month)
            ->first();
    
        if ($monthlyAssocDue) {
            $this->thisMonth = Carbon::create($monthlyAssocDue->year, $monthlyAssocDue->month)->format('Y-m');
            $this->status = $monthlyAssocDue->status;
            $this->datePaid = $monthlyAssocDue->date_paid ? Carbon::parse($monthlyAssocDue->date_paid)->format('Y-m-d') : null;
            $this->paymentMode = $monthlyAssocDue->payment_mode;
            $this->refNumber = $monthlyAssocDue->reference_number ?? '';
        } else {
            $this->thisMonth = $this->month;
            $this->status = '';
            $this->datePaid = now()->format('Y-m-d');
            $this->paymentMode = '';
            $this->refNumber = '';
        }
    }

    public function savePayment(){
        try{
            $admin = Auth::user();
            $user = User::findOrFail($this->userId);
            $monthlyAssocDue = MonthlyAssociationDues::where('user_id', $this->userId)
                    ->where('year', '=', Carbon::parse($this->thisMonth)->year)
                    ->where('month', '=', Carbon::parse($this->thisMonth)->month)
                    ->first();
            if($monthlyAssocDue){
                if($this->status == 1){
                    $monthlyAssocDue->update([
                        'year' => Carbon::parse($this->thisMonth)->year,
                        'month' => Carbon::parse($this->thisMonth)->month,
                        'status' => $this->status,
                        'date_paid' => $this->datePaid,
                        'payment_mode' => $this->paymentMode,
                        'reference_number' => $this->refNumber,
                    ]);
                }else{
                    $monthlyAssocDue->delete();
                }

                $message = 'Payment updated successfully!';
                $icon = 'success';
            }else{
                MonthlyAssociationDues::create([
                    'user_id' => $user->id,
                    'year' => Carbon::parse($this->thisMonth)->year,
                    'month' => Carbon::parse($this->thisMonth)->month,
                    'status' => $this->status,
                    'date_paid' => $this->datePaid,
                    'payment_mode' => $this->paymentMode,
                    'reference_number' => $this->refNumber,
                    'received_by' => $admin->id,
                ]);
                
                $message = 'Payment updated successfully!';
                $icon = 'success';
            }
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => $icon
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function viewPayment($id){
        $this->viewMonthlyPayment = true;
        $this->userId = $id;

        $user = User::findOrFail($this->userId);
        $this->homeowner = $user->name;

        $monthlyAssocDue = MonthlyAssociationDues::where('user_id', $this->userId)
            ->where('year', Carbon::parse($this->month)->year)
            ->where('month', Carbon::parse($this->month)->month)
            ->first();

        $this->thisMonth = Carbon::create($monthlyAssocDue->year, $monthlyAssocDue->month)->format('F Y');
        $this->status = $monthlyAssocDue->status;
        $this->datePaid = $monthlyAssocDue->date_paid ? Carbon::parse($monthlyAssocDue->date_paid)->format('F m, Y') : null;
        $this->paymentMode = $monthlyAssocDue->payment_mode;
        $this->refNumber = $monthlyAssocDue->reference_number ?? '';
        
        $reciever = User::findOrFail($monthlyAssocDue->received_by);
        $this->receivedBy = $reciever->name;
    }

    public function exportPayments(){
        $query = User::select([
                'users.id as userId',
                'users.name',
                'users.email',
                'users.profile_photo_path',
                'users.user_role',
                'positions.position',
                'user_data.*',
                'monthly_association_dues.status as payment_status',
                'monthly_association_dues.date_paid',
                'monthly_association_dues.payment_mode',
                'monthly_association_dues.reference_number',
            ])
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->where('users.user_role', 'homeowner')
            ->leftJoin('positions', 'positions.id', 'users.position_id')
            ->leftJoin('monthly_association_dues', function($join) {
                $join->on('monthly_association_dues.user_id', '=', 'users.id')
                ->where('monthly_association_dues.year', '=', Carbon::parse($this->month)->year)
                ->where('monthly_association_dues.month', '=', Carbon::parse($this->month)->month);
            })
            ->when($this->search, function ($query) {
                return $query->search(trim($this->search));
            })
            ->when($this->paymentStatus !== 'all', function ($query) {
                if ($this->paymentStatus === 'paid') {
                    return $query->whereNotNull('monthly_association_dues.status')
                               ->where('monthly_association_dues.status', '1');
                } else {
                    return $query->where(function($query) {
                        $query->whereNull('monthly_association_dues.status')
                              ->orWhere('monthly_association_dues.status', '!=', '1');
                    });
                }
            })
            ->orderByRaw('CAST(user_data.block AS UNSIGNED) ASC')
            ->orderByRaw('CAST(user_data.lot AS UNSIGNED) ASC');

        $filters = [
            'query' => $query,
            'title' => 'Association Due for the Month of ' . Carbon::parse($this->month)->format('F Y'),
        ];
    
        try {
            $exporter = new MonthlyPaymentsExport($filters);
            $result = $exporter->export();

            return response()->streamDownload(function () use ($result) {
                echo $result['content'];
            }, $result['filename']);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function resetVariables(){
        $this->resetValidation();
        $this->userId = null;
        $this->homeowner = null;
        $this->status = null;
        $this->thisMonth = null;
        $this->paymentMode = null;
        $this->refNumber = null;
        $this->editStatus = null;
        $this->viewMonthlyPayment = null;
    }
}

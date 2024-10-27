<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Positions;
use App\Models\Committees;
use Exception;

#[Layout('layouts.app')]
#[Title('Association')]

class Association extends Component
{
    use WithPagination, WithFileUploads;
    public $addRole;
    public $editRole;
    public $employees;
    public $roleEmployees;
    public $positionsByUnit;
    public $positions;
    public $officeDivisions;
    public $unit;
    public $unitName;
    public $divsUnits;
    public $userId;
    public $name;
    public $employee_number;
    public $position;
    public $user_role;
    public $admin_email;
    public $committee;
    public $committees;
    public $password;
    public $cpassword;
    public $search;
    public $search2;
    public $search3;
    public $search4;
    public $deleteId;
    public $approveId;
    public $deleteMessage;
    public $approveMessage;
    public $add;
    public $data;
    public $settings;
    public $settingsId;
    public $settings_data;
    public $settingsData = [['value' => '']];
    public $units = [['value' => '']];
    public $salaryGrades;
    public $editingId = null;
    public $isEditing = false;
    public $editedData = [];
    public $addPosition;
    public $editPosition;
    public $dropdownForStatus;
    public $allStat = true;
    public $activeStatus = 1;
    public $positionId;
    public $file;
    public $commId;
    public $registering;
    public $bods;
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 

    public function mount(){
        $this->employees = User::where('user_role', '=', 'emp')->get();

        $this->roleEmployees = User::where('user_role', '=', 'emp')
            ->whereDoesntHave('adminAccount')
            ->get();

        $this->positions = Positions::where('position', '!=', 'Super Admin')->get();
        $this->registering = User::where('user_role', 'homeowner')
            ->where('active_status', 0)
            ->count();

        $this->bods = Committees::leftJoin('users', 'users.committee_id', 'committees.id')
                        ->leftJoin('user_data', 'user_data.user_id', 'users.id')
                        ->join('positions', 'positions.committee_id', 'committees.id')
                        ->where('committees.committee', 'bod')
                        ->select('committees.id', 'users.name', 'user_data.block', 'user_data.lot', 'positions.position')
                        ->get();
    }

    public function render(){
        $admins = User::join('positions', 'positions.id', 'users.position_id')
                ->where('positions.position', '!=', 'Super Admin')
                ->join('committees', 'committees.id', 'users.committee_id')
                ->where('users.user_role', '!=', 'emp')
                ->where('users.active_status', '!=', 4)
                ->when($this->search, function ($query) {
                    return $query->search(trim($this->search));
                })
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.user_role',
                    'positions.position',
                    'committees.committee',
                )->paginate($this->pageSize);

        $homeowners = User::select(
                    'users.id as user_id',
                    'users.name',
                    'users.email',
                    'users.profile_photo_path',
                    'users.user_role',
                    'user_data.*'
                )->join('user_data', 'user_data.user_id', 'users.id')
                ->where('users.user_role', 'homeowner')
                ->where('users.active_status', '=', $this->activeStatus)
                ->when($this->search2, function ($query) {
                    return $query->search(trim($this->search2));
                })->paginate($this->pageSize);

        $organizations = User::where('user_role', 'emp')
                ->join('user_data', 'user_data.user_id', 'users.id')
                ->join('positions', 'positions.id', 'users.position_id')
                ->join('committees', 'committees.id', 'users.committee_id')
                ->where('users.active_status', '!=', 4)
                ->select(
                    'users.name', 
                    'users.active_status', 
                    'positions.position', 
                    'user_data.*', 
                    'committees.committee',
                )
                ->when($this->search2, function ($query) {
                    return $query->search(trim($this->search2));
                })
                ->when(!$this->allStat, function ($query) {
                    return $query->where(function ($subQuery) {
                        if ($this->status['active']) {
                            $subQuery->orWhere('active_status', 1);
                        }
                        if ($this->status['inactive']) {
                            $subQuery->orWhere('active_status', 0);
                        }
                        if ($this->status['resigned']) {
                            $subQuery->orWhere('active_status', 2);
                        }
                        if ($this->status['retired']) {
                            $subQuery->orWhere('active_status', 3);
                        }
                    });
                })
                ->get()
                ->groupBy('committee');

        $this->committees = Committees::with('positions')
            ->when($this->search4, function ($query) {
                return $query->search(trim($this->search4));
            })
            ->get();

        return view('livewire.admin.association',[
            'organizations' => $organizations,
            'admins' => $admins,
            'homeowners' => $homeowners,
        ]);
    }

    public function setActiveStatus($status){
        $this->activeStatus = $status;
    }

    public function exportRoles(){
        try{
            $admins = User::join('positions', 'positions.id', 'users.position_id')
                ->where('positions.position', '!=', 'Super Admin')
                ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                ->where('users.user_role', '!=', 'emp')
                ->where('users.active_status', '!=', 4)
                ->when($this->search, function ($query) {
                    return $query->search(trim($this->search));
                })
                ->select(
                    'users.id',
                    'users.name',
                    'users.user_role',
                    'users.emp_code',
                    'positions.position',
                    'office_divisions.office_division',
                    'office_division_units.unit'
                );

            $filters = [
                'admins' => $admins,
            ];
            return Excel::download(new AdminRolesExport($filters), 'Admin_Roles_List.xlsx');
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function exportEmployees($division)
    {
        try {
            $organizations = User::where('user_role', 'emp')
                ->join('user_data', 'user_data.user_id', 'users.id')
                ->join('positions', 'positions.id', 'users.position_id')
                ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                ->leftJoin('payrolls', 'payrolls.user_id', 'users.id')
                ->leftJoin('cos_sk_payrolls', 'cos_sk_payrolls.user_id', 'users.id')
                ->leftJoin('cos_reg_payrolls', 'cos_reg_payrolls.user_id', 'users.id')
                ->where('users.active_status', '!=', 4)
                ->select(
                    'users.name', 
                    'users.email', 
                    'users.emp_code', 
                    'users.active_status', 
                    'positions.position', 
                    'user_data.appointment', 
                    'user_data.date_hired', 
                    'office_divisions.office_division',
                    'office_division_units.unit',
                    'payrolls.sg_step as plantilla_sg_step',
                    'payrolls.rate_per_month as plantilla_rate',
                    'cos_sk_payrolls.sg_step as cos_sk_sg_step',
                    'cos_sk_payrolls.rate_per_month as cos_sk_rate',
                    'cos_reg_payrolls.sg_step as cos_reg_sg_step',
                    'cos_reg_payrolls.rate_per_month as cos_reg_rate',
                )
                ->where('office_divisions.office_division', $division)
                ->when($this->search2, function ($query) {
                    return $query->search(trim($this->search2));
                })
                ->when(!$this->allStat, function ($query) {
                    return $query->where(function ($subQuery) {
                        if ($this->status['active']) {
                            $subQuery->orWhere('active_status', 1);
                        }
                        if ($this->status['inactive']) {
                            $subQuery->orWhere('active_status', 0);
                        }
                        if ($this->status['resigned']) {
                            $subQuery->orWhere('active_status', 2);
                        }
                        if ($this->status['retired']) {
                            $subQuery->orWhere('active_status', 3);
                        }
                    });
                });

            $selectedStatuses = $this->allStat ? ['All'] : array_keys(array_filter($this->status));
            $statusLabels = [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'resigned' => 'Resigned',
                'retired' => 'Retired',
                'promoted' => 'Promoted'
            ];
    
            $filters = [
                'organizations' => $organizations,
                'office_division' => $division,
                'statuses' => $selectedStatuses == ['All'] ? ['All'] : array_map(function($status) use ($statusLabels) {
                    return $statusLabels[$status];
                }, $selectedStatuses)
            ];
            return Excel::download(new PerOfficeDivisionExport($filters), $division . '_EmployeesList.xlsx');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleAddSettings($data)
    {
        $this->data = $data;
        $this->settings = true;
        $this->add = true;
        $this->settingsData = [['value' => '']];
    }

    public function toggleAddPos($id, $data){
        $this->commId = $id;
        $this->data = $data;
        $this->settings = true;
        $this->add = true;
        $this->settingsData = [['value' => '']];
    }

    public function toggleEditPos($id, $data){
        $this->commId = $id;
        $positions = Positions::where('committee_id', $id)
                        ->get();
        $this->data = $data;
        $this->settings = true;
        if ($positions->isNotEmpty()) {
            $this->settingsData = $positions->map(function($pos) {
                return ['value' => $pos->position];
            })->toArray();
        } else {
            $this->settingsData = [['value' => '']];
        }
    }

    public function addNewSetting()
    {
        $this->settingsData[] = ['value' => ''];
    }

    public function removeSetting($index)
    {
        unset($this->settingsData[$index]);
        $this->settingsData = array_values($this->settingsData);
    }

    public function toggleDeleteSettings($id, $data){ 
        $this->deleteId = $id;
        $this->data = $data;
        $this->deleteMessage = $data;
    }

    public function toggleEditSettings($id, $data){
        $this->settings = true;  
        $this->settingsId = $id;
        $this->data = $data;
        if($data == "committee"){
            $committees = Committees::where('id', $this->settingsId)->first();
            $this->settings_data = $committees->committee;
        }else if($data == "position"){
            $positions = Positions::where('id', $this->settingsId)->first();
            $this->settings_data = $positions->position;
        }
    }

    public function saveSettings(){
        try {
            $message = null;
            if($this->add){
                if ($this->data == "committee") {
                    $this->validate([
                        'settings_data' => 'required'
                    ]);

                    Committees::create([
                        'committee' => $this->settings_data,
                    ]);

                    $message = "Committee added successfully!";
                } else if ($this->data == "position") {
                    $this->validate([
                        'settingsData.*.value' => 'required|string|max:255',
                    ]);

                    foreach ($this->settingsData as $setting) {
                        Positions::create([
                            'committee_id' => $this->commId,
                            'position' => $setting['value'],
                        ]);
                    }
                    $message = "Position/s added successfully!";
                }
            }else{
                  // Update existing committee or Position
                if ($this->data == "committee") {
                    $this->validate([
                        'settings_data' => 'required'
                    ]);

                    $committees = Committees::where('id', $this->settingsId)->first();
                    $committees->update([
                        'committee' => $this->settings_data,
                    ]);

                    $message = "Committee updated successfully!";
                } else if ($this->data == "position") {
                    $this->validate([
                        'settingsData.*.value' => 'required|string|max:255',
                    ]);
                    
                    $committees = Committees::where('id', $this->commId)->first();
                    
                    // Track existing positions
                    $existingPositionIds = $committees->positions->pluck('id')->toArray();
                    $updatedPositionIds = [];

                    foreach($this->settingsData as $index => $data) {
                        if (isset($committees->positions[$index])) {
                            $position = $committees->positions[$index];
                            $position->update([
                                'position' => $data['value'],
                            ]);
                            $updatedPositionIds[] = $position->id;
                        } else {
                            $newPosition = Positions::create([
                                'committee_id' => $committees->id,
                                'position' => $data['value'],
                            ]);
                            $updatedPositionIds[] = $newPosition->id;
                        }
                    }

                    // Detect removed positions and delete them
                    $removedPositionIds = array_diff($existingPositionIds, $updatedPositionIds);
                    Positions::whereIn('id', $removedPositionIds)->delete();

                    $message = "Position/s updated successfully!";
                }
            }

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        } catch(Exception $e) {
            throw $e;
        }
    }

    public function toggleEditRole($userId){
        $this->editRole = true;
        $this->userId = $userId;
        try {
            $admin = User::where('users.id', $userId)
                ->join('positions', 'positions.id', 'users.position_id')
                ->where('positions.position', '!=', 'Super Admin')
                ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                ->where('users.user_role', '!=', 'emp')
                ->where('users.active_status', '!=', 4)
                ->when($this->search, function ($query) {
                    return $query->search(trim($this->search));
                })
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.user_role',
                    'users.emp_code',
                    'users.unit_id',
                    'positions.position',
                    'office_divisions.office_division',
                    'office_divisions.id as divId',
                    'office_division_units.unit',
                    'office_division_units.id as unitId'
                )
                ->first();
            if ($admin) {
                $this->divsUnits = OfficeDivisionUnits::where('office_division_id' , $admin->divId)->get();
                $this->name = $admin->name;
                $this->user_role = $admin->user_role;
                $this->admin_email = $admin->email;
                $this->office_division = $admin->office_division;
                $this->unitName = $admin->unit;
                $this->unit = $admin->unitId;
                $this->position = $admin->position;
                $this->divId = $admin->divId;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleAddRole(){
        $this->editRole = true;
        $this->addRole = true;
    }

    public function toggleEditPosition($userId){
        $this->editPosition = true;
        $this->userId = $userId;
        try {
            $empPos = User::where('users.id', $this->userId)
                    ->join('positions', 'positions.id', 'users.position_id')
                    ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                    ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                    ->select('users.*', 'positions.position', 'office_divisions.office_division', 'office_division_units.unit')
                    ->first();
            if ($empPos) {
                $this->userId = $empPos->id;
                $this->name = $empPos->name;
                $this->position = $empPos->position;
                $this->office_division = $empPos->office_division;
                $this->positionId = $empPos->position_id;
                $this->officeDivisionId = $empPos->office_division_id;
                $this->activeStatus = $empPos->active_status;
                $this->unitName = $empPos->unit;
                $this->unit = $empPos->unit_id;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function saveRole(){
        try {
            $user = User::where('users.id', $this->userId)
                ->join('positions', 'positions.id', 'users.position_id')
                ->select('users.id', 'users.name', 'users.emp_code','positions.id as posId')
                ->first();
            if($user){
                if($this->addRole){
                    $this->validate([
                        'user_role' => 'required',
                        'divId' => 'required',
                        'admin_email' => 'required|email|unique:users,email',
                        'password' => 'required|min:8',
                        'cpassword' => 'required|same:password',
                    ]);

                    if (!$this->isPasswordComplex($this->password)) {
                        $this->addError('password', 'The password must contain at least one uppercase letter, one number, and one special character.');
                        return;
                    }

                    // $payrollId = null;
                    // $payrolls = Payrolls::where('user_id', $user->id)->first();
                    // $cosRegPayrolls = CosRegPayrolls::where('user_id', $user->id)->first();
                    // $cosSkPayrolls = CosSkPayrolls::where('user_id', $user->id)->first();

                    // if($payrolls){
                    //     $payrollId = $payrolls->user_id;
                    // }else if($cosRegPayrolls){
                    //     $payrollId = $cosRegPayrolls->user_id;
                    // }else if($cosSkPayrolls){
                    //     $payrollId = $cosSkPayrolls->user_id;
                    // }else{
                    //     $this->dispatch('swal', [
                    //         'title' => "This employee don't have a payroll yet!",
                    //         'icon' => 'error'
                    //     ]);
                    //     return;
                    // }

                    $admin = User::create([
                        'name' => $user->name,
                        'email' => $this->admin_email,
                        'password' => $this->password,
                        'emp_code' => $this->user_role . '-' .$user->emp_code,
                        'user_role' => $this->user_role,
                        'active_status' => 1,
                        'position_id' => $user->posId,
                        'office_division_id' => $this->divId,
                        'unit_id' => $this->unit,
                    ]);
                }else{
                    $admin = User::where('users.id', $this->userId)
                            ->first();

                    $this->validate([
                        'user_role' => 'required',
                        'office_division' => 'required',
                        'admin_email' => 'required|email',
                    ]);

                    $admin->update([
                        'email' => $this->admin_email,
                        'user_role' => $this->user_role,
                        'office_division_id' => $this->divId,
                        'unit_id' => $this->unit,
                    ]);
                }
            }
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => "Account role updated successfully!",
                'icon' => 'success'
            ]);
    
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => "Account role update was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function savePosition(){
        try {
            $empPos = User::where('users.id', $this->userId)->first();
            if ($empPos) {
                $empPos->update([
                    'position_id' => $this->positionId,
                    'office_division_id' => $this->officeDivisionId,
                    'unit_id' => $this->unit,
                    'active_status' => $this->activeStatus,
                ]);
                $this->dispatch('swal', [
                    'title' => 'Employee settings updated successfully!',
                    'icon' => 'success'
                ]);
                $this->resetVariables();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleDelete($userId, $message){
        $this->deleteMessage = $message;
        $this->deleteId = $userId;
    }

    public function toggleApprove($userId, $message){
        $this->approveId = $userId;
        $this->approveMessage = $message;
    }

    public function deleteData(){
        try {
            $message = null;

            if($this->data){
                if($this->data == "committee"){
                    $committees = Committees::where('id', $this->deleteId)->first();
                    $committees->delete();
                    $message = "Committee deleted successfully!";
                }else if($this->data == "position"){
                    $positions = Positions::where('id', $this->deleteId)->first();
                    $positions->delete();
                    $message = "Position deleted successfully!";
                }
            }else{
                $user = User::where('id', $this->deleteId)->first();
                if ($user) {
                    switch($this->deleteMessage){
                        case "role":
                            $user->update([
                                'committee_id' => null,
                                'position_id' => null,
                            ]);
                            $message = "Role deleted successfully!";
                            break;
                        case "homeowner":
                            $user->delete();
                            $message = "Homeowner deleted successfully!";
                            break;
                        default:
                            break;
                    }             
                }
            }

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => "Deletion of " . $this->deleteMessage . "was unsuccessful!",
                'icon' => 'error'
            ]);
            $this->resetVariables();
            throw $e;
        }
    }

    public function approveUser(){
        try{
            $user = User::where('id', $this->approveId)->first();
            $message = '';
            if ($user) {
                switch($this->approveMessage){
                    case 'deactivate':
                        $user->update([
                            'active_status' => 2,
                        ]);
                        $message = "Homeowner deactivated successfully!";
                        break;
                    case 'activate':
                        $user->update([
                            'active_status' => 1,
                        ]);
                        $message = "Homeowner activated successfully!";
                        break;
                    case 'approve':
                        $user->update([
                            'active_status' => 1,
                        ]);
                        $message = "Homeowner approved successfully!";
                        break;
                    default:
                        break;
                }
            }else{
                $message = "Homeowner status change was unsuccessful!";
            }

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function resetVariables(){
        $this->resetValidation();
        $this->userId = null;
        $this->name = null;
        $this->position = null;
        $this->editRole = null;
        $this->addRole = null;
        $this->admin_email = null;
        $this->password = null;
        $this->cpassword = null;
        $this->approveId = null;
        $this->deleteId = null;
        $this->deleteMessage = null;
        $this->settings = null;
        $this->settingsId = null;
        $this->add = null;
        $this->settings_data = null;
        $this->settingsData = [['value' => '']];
        $this->data = null;
        $this->editingId = null;
        $this->editPosition = null;
        $this->approveMessage = null;
    }

    private function isPasswordComplex($password){
        $containsUppercase = preg_match('/[A-Z]/', $password);
        $containsNumber = preg_match('/\d/', $password);
        $containsSpecialChar = preg_match('/[^A-Za-z0-9]/', $password); // Changed regex to include special characters
        return $containsUppercase && $containsNumber && $containsSpecialChar;
    }
}

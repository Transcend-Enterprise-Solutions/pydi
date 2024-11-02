<?php

namespace App\Livewire\Admin;

use App\Exports\UserListExport;
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
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Association')]

class Association extends Component
{
    use WithPagination, WithFileUploads;
    public $addRole;
    public $editRole;
    public $hos;
    public $roleHomeowners;
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
    public $pos;
    public $posId;
    public $comId;
    public $setPos;
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 


    public function mount(){
        $this->activeStatus = request()->query('activeStatus', 1);
        $this->hos = User::where('user_role', '=', 'homeowner')
                    ->whereNull(['position_id', 'committee_id'])
                    ->get();

        $this->roleHomeowners = User::where('user_role', '=', 'homeowner')
                                ->get();

        $this->positions = Positions::where('position', '!=', 'Super Admin')->get();
        $this->registering = User::where('user_role', 'homeowner')
            ->where('active_status', 0)
            ->count();
    }

    public function render(){
        $admins = User::leftJoin('positions', 'positions.id', 'users.position_id')
                ->leftJoin('committees', 'committees.id', 'users.committee_id')
                ->join('user_data', 'user_data.user_id', 'users.homeowner_id')
                ->where('users.user_role', '!=', 'homeowner')
                ->where('users.user_role', '!=', 'sa')
                ->where('users.active_status', '=', 1)
                ->when($this->search, function ($query) {
                    return $query->search(trim($this->search));
                })
                ->select(
                    'users.id as userId',
                    'users.name',
                    'users.email',
                    'users.profile_photo_path',
                    'users.user_role',
                    'positions.position',
                    'user_data.*'
                )->paginate($this->pageSize);

        $homeowners = User::select(
                    'users.id as user_id',
                    'users.name',
                    'users.email',
                    'users.profile_photo_path',
                    'users.user_role',
                    'positions.position',
                    'user_data.*'
                )->join('user_data', 'user_data.user_id', 'users.id')
                ->where('users.user_role', 'homeowner')
                ->leftJoin('positions', 'positions.id', 'users.position_id')
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

            $this->bods = Committees::join('positions', 'positions.committee_id', 'committees.id')
                ->leftJoin('users', function($join) {
                    $join->on('users.committee_id', '=', 'committees.id')
                         ->on('users.position_id', '=', 'positions.id');
                })
                ->leftJoin('user_data', 'user_data.user_id', 'users.id')
                ->where(function($query) {
                    $query->whereIn('committees.committee', [
                        'bod', 
                        'board of directors', 
                        'Board of Directors', 
                        'Board Of Directors', 
                        'BOARD OF DIRECTORS'
                    ]);
                })
                ->where('users.user_role', '=', 'homeowner')
                ->select('committees.id', 'users.name', 'users.id as userId', 'user_data.block', 
                         'user_data.lot', 'positions.position', 'positions.id as posId', 'users.profile_photo_path')
                ->get();

        $this->committees = Committees::with('positions')
            ->when($this->search4, function ($query) {
                return $query->search(trim($this->search4));
            })
            ->get();
        
        $comms = Committees::join('positions', 'positions.committee_id', 'committees.id')
                ->leftJoin('users', function($join) {
                    $join->on('users.committee_id', '=', 'committees.id')
                            ->on('users.position_id', '=', 'positions.id');
                })
                ->leftJoin('user_data', 'user_data.user_id', 'users.id')
                ->where('committees.committee', '!=', 'bod')
                ->where('committees.committee', '!=', 'board of directors')
                ->where('committees.committee', '!=', 'Board of Directors')
                ->where('committees.committee', '!=', 'Board Of Directors')
                ->where('committees.committee', '!=', 'BOARD OF DIRECTORS')
                ->select('committees.id', 'committees.committee', 'users.name', 'users.id as userId', 'user_data.block', 
                        'user_data.lot', 'positions.position', 'positions.id as posId', 'users.profile_photo_path')
                ->get()
                ->groupBy('committee');

        return view('livewire.admin.association',[
            'organizations' => $organizations,
            'admins' => $admins,
            'homeowners' => $homeowners,
            'comms' => $comms,
        ]);
    }

    public function setActiveStatus($status){
        $this->activeStatus = $status;
    }

    public function exportList(){
        $filters = [
            'search' => $this->search2,
            'activeStatus' => $this->activeStatus,
        ];
    
        try {
            $exporter = new UserListExport($filters);
            $result = $exporter->export();

            return response()->streamDownload(function () use ($result) {
                echo $result['content'];
            }, $result['filename']);
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

    public function toggleAddRole(){
        $this->editRole = true;
        $this->addRole = true;
    }

    public function saveRole(){
        try {
            $user = User::findOrFail( $this->userId);
            if($user){
                $this->validate([
                    'admin_email' => 'required|email|unique:users,email',
                    'password' => 'required|min:8',
                    'cpassword' => 'required|same:password',
                ]);

                if (!$this->isPasswordComplex($this->password)) {
                    $this->addError('password', 'The password must contain at least one uppercase letter, one number, and one special character.');
                    return;
                }

                User::create([
                    'name' => $user->name,
                    'email' => $this->admin_email,
                    'password' => $this->password,
                    'user_role' => 'admin',
                    'homeowner_id' => $user->id,
                    'active_status' => 1,
                    'position_id' => $user->position_id ?: null,
                    'committee_id' => $user->committee_id ?: null,
                ]);
            }
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => "System admin added successfully!",
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
                $user = User::findOrFail($this->deleteId);
                if ($user) {
                    $user->delete();
                    $message = "Homeowner deleted successfully!";
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

    public function toggleSetPos($comId, $posId, $userId = null, $pos){
        $this->setPos = $pos;
        $this->posId = $posId;
        $this->comId = $comId;
        $this->userId = $userId;
    }

    public function savePos(){
        try{
            $message = '';
            $icon = '';
            if($this->pos == 0){
                $user = User::where('committee_id', $this->comId)
                        ->where('position_id', $this->posId)
                        ->first();
                if($user){
                    $user->update([
                        'committee_id' =>  null,
                        'position_id' => null,
                    ]);
    
                    $message = 'Position vacated successfully!';
                    $icon = 'success';
                }else{
                    $message = 'Position update was unsuccessful!';
                    $icon = 'error';
                }
            }else{
                if($this->pos == $this->userId || $this->userId == 0){
                    $user = User::findOrFail($this->pos);
                    if($user){
                        $user->update([
                            'committee_id' =>  $this->comId,
                            'position_id' => $this->posId,
                        ]);
        
                        $message = 'Position saved successfully!';
                        $icon = 'success';
                    }else{
                        $message = 'Position update was unsuccessful!';
                        $icon = 'error';
                    }
                }else{
                    $oldOfficer = User::findOrFail($this->userId);
                    $user = User::findOrFail($this->pos);
                    if($user){
                        $oldOfficer->update([
                            'committee_id' =>  null,
                            'position_id' => null,
                        ]);

                        $user->update([
                            'committee_id' =>  $this->comId,
                            'position_id' => $this->posId,
                        ]);
        
                        $message = 'Position saved successfully!';
                        $icon = 'success';
                    }else{
                        $message = 'Position update was unsuccessful!';
                        $icon = 'error';
                    }
                }
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
        $this->setPos = null;
        $this->posId = null;
        $this->comId = null;
    }

    private function isPasswordComplex($password){
        $containsUppercase = preg_match('/[A-Z]/', $password);
        $containsNumber = preg_match('/\d/', $password);
        $containsSpecialChar = preg_match('/[^A-Za-z0-9]/', $password); // Changed regex to include special characters
        return $containsUppercase && $containsNumber && $containsSpecialChar;
    }
}

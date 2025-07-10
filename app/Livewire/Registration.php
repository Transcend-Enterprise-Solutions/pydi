<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserData;
use App\Models\SubdivisionLots;

class Registration extends Component
{
    // Personal Info
    public $first_name;
    public $middle_name;
    public $last_name;
    public $name_extension;

    // Contact Info
    public $email;
    public $mobile_number;

    // Address Info
    public $lots;
    public $lot;
    public $block;
    public $street;

    // Government Info
    public $position_designation;
    public $government_agency;
    public $office_department_division;
    public $office_address;

    // Account Info
    public $password;
    public $c_password;

    // UI State
    public $showModal = false;
    private $subdivisionLots;

    protected $rules = [
        // Personal Info
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',

        // Contact Info
        'email' => 'required|email|unique:users,email',
        'mobile_number' => 'required|string|max:20',

        // Government Info
        'position_designation' => 'required|string|max:255',
        'government_agency' => 'required|string|max:255',
        'office_department_division' => 'required|string|max:255',
        'office_address' => 'required|string|max:500',

        // Account Info
        'password' => 'required|min:8',
        'c_password' => 'required|same:password',
    ];

    protected $messages = [
        'first_name.required' => 'The first name field is required.',
        'last_name.required' => 'The last name field is required.',
        'email.required' => 'The email field is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already registered.',
        'mobile_number.required' => 'The mobile number field is required.',
        'position_designation.required' => 'The position/designation field is required.',
        'government_agency.required' => 'The government agency field is required.',
        'office_department_division.required' => 'The office/department/division field is required.',
        'office_address.required' => 'The office address field is required.',
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 8 characters.',
        'c_password.required' => 'Please confirm your password.',
        'c_password.same' => 'The passwords do not match.',
    ];

    public function submit()
    {
        $this->validate();

        if (!$this->isPasswordComplex($this->password)) {
            $this->addError('password', 'The password must include at least one uppercase letter, one number, and one special character.');
            return;
        }

        // Create user
        $name = $this->first_name . " " .
                ($this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . ". " : '') .
                $this->last_name .
                ($this->name_extension ? (" " . $this->name_extension) : '');

        $user = User::create([
            'name' => $name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'user_role' => 'user',
            'active_status' => 0,
        ]);

        // Create user data
        UserData::create([
            'user_id' => $user->id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name ?: null,
            'name_extension' => $this->name_extension ?: null,
            'mobile_number' => $this->mobile_number,
            'position_designation' => $this->position_designation,
            'government_agency' => $this->government_agency,
            'office_department_division' => $this->office_department_division,
            'office_address' => $this->office_address,
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'registration',
            'notif' => 'register',
            'read' => 0,
        ]);

        // Reset form and show success modal
        $this->resetVariables();

        $this->showModal = true;
    }

    public function resetVariables()
    {
        $this->first_name = '';
        $this->middle_name = '';
        $this->last_name = '';
        $this->name_extension = '';
        $this->email = '';
        $this->mobile_number = '';
        $this->position_designation = '';
        $this->government_agency = '';
        $this->office_department_division = '';
        $this->office_address = '';
        $this->password = '';
        $this->c_password = '';
    }

    private function isPasswordComplex($password)
    {
        $containsUppercase = preg_match('/[A-Z]/', $password);
        $containsNumber = preg_match('/\d/', $password);
        $containsSpecialChar = preg_match('/[^A-Za-z0-9]/', $password);
        return $containsUppercase && $containsNumber && $containsSpecialChar;
    }

    public function render()
    {
        $this->subdivisionLots = new SubdivisionLots();
        $blocks = $this->subdivisionLots->getBlocks();

        if ($this->block) {
            $this->lots = $this->subdivisionLots->getLotsInBlock($this->block);
        }

        return view('livewire.registration', [
            'blocks' => $blocks,
            'positions' => UserData::getPositionDesignations(),
            'agencies' => UserData::getGovernmentAgencies(),
            'departments' => UserData::getOfficeDepartments(),
        ]);
    }
}

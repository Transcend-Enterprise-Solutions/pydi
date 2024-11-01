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
    public $password;
    public $c_password;

    public $first_name;
    public $middle_name;
    public $last_name;
    public $name_extension;
    public $showModal = false;
    public $tel_number;
    public $mobile_number;
    public $email;
    public $lots;
    public $lot;
    public $block;
    public $street;
    private $subdivisionLots;

    protected $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'c_password' => 'required|same:password',
        'block' => 'required',
        'lot' => 'required',
        'street' => 'required',
        'mobile_number' => 'required',
    ];

    protected $messages = [
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 8 characters long.',
        'c_password.required' => 'The password confirmation field is required.',
        'c_password.same' => 'The password confirmation does not match the password.',
        'block' => 'The block field is required.',
        'lot' => 'The lot field is required.',
        'street' => 'The street field is required.',
        'mobile_number' => 'The mobile number field is required.',
        'first_name' => 'The first name field is required.',
        'last_name' => 'The last name field is required.',
        'email' => 'The email field is required.',
    ];


    public function submit(){
        
        $this->validate();

        if (!$this->isPasswordComplex($this->password)) {
            $this->addError('password', 'The password must include at least one uppercase letter, one number, and one special character.');
            return;
        }
        sleep(1);
        
        // Create new user
        $name = $this->first_name . " " . 
                ($this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . ". " : '') . 
                $this->last_name . 
                ($this->name_extension ? (" " . $this->name_extension) : '');
        $user = User::create([
            'name' => $name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'user_role' => 'homeowner',
            'active_status' => 0,
        ]);

        UserData::create([
            'user_id' => $user->id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name ?: null,
            'name_extension' => $this->name_extension ?: null,
            'mobile_number' => $this->mobile_number,
            'block' => $this->block,
            'lot' => $this->lot,
            'street' => $this->street,
        ]);

        // Create a notification entry
        Notification::create([
            'user_id' => $user->id,
            'type' => 'registration',
            'notif' => 'register',
            'read' => 0,
        ]);

        // Reset form fields
        $this->reset(['password', 'c_password', 'first_name', 'last_name', 'middle_name', 'email', 'mobile_number', 'name_extension', 'block', 'lot', 'street']);
        $this->showModal = true;
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
        if($this->block){
            $this->lots = $this->subdivisionLots->getLotsInBlock($this->block);
        }

        return view('livewire.registration', [
            'blocks' => $blocks
        ]);
    }

    public function resetVariables(){
        return;
    }
}
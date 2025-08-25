<?php

namespace App\Livewire\Admin;

use App\Models\EmailTemplate;
use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
#[Title('Email Management')]
class EmailTemplateManager extends Component
{
    public $templates;
    public $selectedTemplate;
    public $isEditing = false;
    public $showModal = false;
    public $name = '';
    public $subject = '';
    public $header = '';
    public $greetings = '';
    public $message_body = '';
    public $footer = '';
    public $action_button_text = '';
    public $action_button_url = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
        'header' => 'required|string|max:255',
        'greetings' => 'required|string|max:255',
        'message_body' => 'required|string',
        'footer' => 'nullable|string',
        'action_button_text' => 'nullable|string|max:255',
        'action_button_url' => 'nullable|url',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $this->templates = EmailTemplate::orderBy('name')->get();
    }

    public function createTemplate()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function editTemplate($id)
    {
        $template = EmailTemplate::find($id);
        if ($template) {
            $this->selectedTemplate = $template;
            $this->fillForm($template);
            $this->isEditing = true;
            $this->showModal = true;
        }
    }

    public function fillForm(EmailTemplate $template)
    {
        $this->name = $template->name;
        $this->subject = $template->subject;
        $this->header = $template->header;
        $this->greetings = $template->greetings;
        $this->message_body = $template->message_body;
        $this->footer = $template->footer;
        $this->action_button_text = $template->action_button_text;
        $this->action_button_url = $template->action_button_url;
        $this->is_active = $template->is_active;
    }

    public function saveTemplate()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'subject' => $this->subject,
            'header' => $this->header,
            'greetings' => $this->greetings,
            'message_body' => $this->message_body,
            'footer' => $this->footer,
            'action_button_text' => $this->action_button_text,
            'action_button_url' => $this->action_button_url,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            $this->selectedTemplate->update($data);
            session()->flash('message', 'Template updated successfully!');
        } else {
            EmailTemplate::create($data);
            session()->flash('message', 'Template created successfully!');
        }

        $this->closeModal();
        $this->loadTemplates();
    }

    public function deleteTemplate($id)
    {
        $template = EmailTemplate::find($id);
        if ($template) {
            $template->delete();
            session()->flash('message', 'Template deleted successfully!');
            $this->loadTemplates();
        }
    }

    public function toggleStatus($id)
    {
        $template = EmailTemplate::find($id);
        if ($template) {
            $template->update(['is_active' => !$template->is_active]);
            $this->loadTemplates();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->subject = '';
        $this->header = '';
        $this->greetings = '';
        $this->message_body = '';
        $this->footer = '';
        $this->action_button_text = '';
        $this->action_button_url = '';
        $this->is_active = true;
        $this->selectedTemplate = null;
    }

    public function render()
    {
        return view('livewire.admin.email-template-manager');
    }
}

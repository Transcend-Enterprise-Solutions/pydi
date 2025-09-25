<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmailTemplate::create(attributes: [
            'name' => 'pydp_dataset_submission',
            'subject' => 'PYDP Submission for Approval',
            'header' => 'National Youth Commission',
            'greetings' => 'Good day',
            'message_body' => '
                    A new PYDP dataset has been submitted for your approval. 
                    Kindly review and provide the necessary feedback.',
            'footer' => '© 2025 National Youth Commission. All rights reserved.',
            'action_button_text' => 'View Submissions',
            'action_button_url' => 'https://pydi.transcend-enterprise.com/login',
            'is_active' => true,
        ]);
    }
}
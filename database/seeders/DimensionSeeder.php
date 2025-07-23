<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DimensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, insert the dimensions
        $dimensions = [
            [
                'name' => 'Active Citizenship',
                'description' => 'Programs promoting civic responsibility, volunteerism, and nation-building.',
                'image' => '/images/advocacies/active-citizenship.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Agriculture',
                'description' => 'Support for farming, agri-business, and sustainable food production.',
                'image' => '/images/advocacies/agriculture.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Digital Media Citizenship',
                'description' => 'Digital literacy and responsible use of technology and social media.',
                'image' => '/images/advocacies/digital-media.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Economic Empowerment',
                'description' => 'Programs for youth livelihood, employment, and entrepreneurship.',
                'image' => '/images/advocacies/economic-empowerment.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Education',
                'description' => 'Access to quality education, scholarships, and learning programs.',
                'image' => '/images/advocacies/education.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Environment',
                'description' => 'Environmental conservation, climate action, and sustainable living.',
                'image' => '/images/advocacies/environment.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Global Mobility',
                'description' => 'International exchange, travel opportunities, and cultural immersion.',
                'image' => '/images/advocacies/global-mobility.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Governance',
                'description' => 'Promoting transparency, accountability, and participatory governance.',
                'image' => '/images/advocacies/governance.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Health',
                'description' => 'Initiatives for physical, mental, and community health awareness.',
                'image' => '/images/advocacies/health.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Peace-Building and Security',
                'description' => 'Youth involvement in conflict resolution and security initiatives.',
                'image' => '/images/advocacies/peace-security.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Social Inclusion and Equity',
                'description' => 'Promoting equality, diversity, and inclusion among youth.',
                'image' => '/images/advocacies/social-inclusion.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];


        DB::table('dimensions')->insert($dimensions);

        // Get dimension IDs for reference
        $dimensionIds = [];
        foreach ($dimensions as $dimension) {
            $dimensionIds[$dimension['name']] = DB::table('dimensions')
                ->where('name', $dimension['name'])
                ->value('id');
        }

        // Now insert indicators for each dimension
        $indicators = [
            // Active Citizenship
            [
                'dimension_id' => $dimensionIds['Active Citizenship'],
                'name' => 'Percentage who are members of youth organizations',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Active Citizenship'],
                'name' => 'Youth volunteerism rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Active Citizenship'],
                'name' => 'Youth attendance in organizational meetings',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Active Citizenship'],
                'name' => 'Percentage of youth accessing internet for interaction and posting of opinions for discussing civic and political issues',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Agriculture
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Percentage of young people who have accessed agricultural programs and services',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Percentage of young people who have accessed business capital and livelihood projects (in agriculture)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Percentage of youth who own agricultural land titles',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Percentage of youth employment in agriculture',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Percentage of youth taking agriculture-related courses',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Digital Media Citizenship
            [
                'dimension_id' => $dimensionIds['Digital Media Citizenship'],
                'name' => 'Uses or skillful in using the internet, computers, social media, or ICT in general',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Digital Media Citizenship'],
                'name' => 'Understands social and broadcast media content or has digital literacy',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Digital Media Citizenship'],
                'name' => 'Percentage of youth with access to computers or ICT devices',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Economic Empowerment
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth entrepreneurship rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth unemployment rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth underemployment rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth vulnerable employment Rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth not in education, employment, or training (NEET) as percent of youth population',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth mean hours of work in a week (targeted to be at least 40 hours per week)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Percentage of youth in professional, technical, managerial, and administrative jobs',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Average hourly earnings of female and male employees',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Number of female and male children aged 5-17 years old in child labor by age',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Education
            [
                'dimension_id' => $dimensionIds['Education'],
                'name' => 'Proportion among youth who completed tertiary education (college education)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Education'],
                'name' => 'Percentage of youth grantees of scholarships and educational assistance at the tertiary level',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Education'],
                'name' => 'Youth functional literacy rate; numeracy rate; ages 15-29',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Environment
            [
                'dimension_id' => $dimensionIds['Environment'],
                'name' => 'Percentage of youth organizations that actively participate in advocating for and implementing environment-related laws, policies, ordinances, and programs',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Environment'],
                'name' => 'Youth involvement in environmental activities',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Global Mobility
            [
                'dimension_id' => $dimensionIds['Global Mobility'],
                'name' => 'Student outbound mobility rate at the tertiary level',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Global Mobility'],
                'name' => 'Youth exchange students',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Global Mobility'],
                'name' => 'Youth volunteers abroad',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Global Mobility'],
                'name' => 'International youth professionals',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Governance
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'Youth voter registration rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'SK youth voter registration rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'Youth voter turnout rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'SK voter turnout rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'Ratio of youth and non-youth workers in government service',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'Percentage of youth who are represented in the key decision-making positions in the public sector',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Health
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Use of illegal drugs; Years of life lost 15-39 years old (ASEAN)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Proportion with HIV infection',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Coverage of essential health services by age',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Proportion suffering from significant mental health problems; Years of life lost 15-39 (ASEAN)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Road traffic accident injuries and drowning/submersion incidents',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Teenage pregnancy rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Suicide deaths',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Peace-Building and Security
            [
                'dimension_id' => $dimensionIds['Peace-Building and Security'],
                'name' => 'Percentage of youth who are involved in peace-making and peace-building activities',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Peace-Building and Security'],
                'name' => 'Number and percentage of youth in the leadership of relevant organizations involved in preventing and addressing conflict',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Peace-Building and Security'],
                'name' => 'Youth offender rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Peace-Building and Security'],
                'name' => 'Youth recidivism rate',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Social Inclusion and Equity
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Proportion of seats held by young women in Congress and LGUs',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Proportion of youth population with severe disabilities receiving disability cash benefit',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Youth poverty rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Youth subsistence rate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Proportion of women aged 15-30 years who make their own informed decisions regarding sexual relations, contraceptive use, and reproductive health care',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Percentage of population with access to electricity',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Percentage of population with access to toilet facilities',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Youth hunger incidence',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Ratio of young men to young women in professional and management positions',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Ratio of young men to young women who are represented in governance structures in school',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Proportion of young women elected as SK chairpersons',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Percentage of women aged 20-24 years married or in union before age 18',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('indicators')->insert($indicators);
    }
}

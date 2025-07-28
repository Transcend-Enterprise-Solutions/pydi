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


            ],
            [
                'name' => 'Agriculture',
                'description' => 'Support for farming, agri-business, and sustainable food production.',
                'image' => '/images/advocacies/agriculture.png',


            ],
            [
                'name' => 'Digital Media Citizenship',
                'description' => 'Digital literacy and responsible use of technology and social media.',
                'image' => '/images/advocacies/digital-media.png',


            ],
            [
                'name' => 'Economic Empowerment',
                'description' => 'Programs for youth livelihood, employment, and entrepreneurship.',
                'image' => '/images/advocacies/economic-empowerment.png',


            ],
            [
                'name' => 'Education',
                'description' => 'Access to quality education, scholarships, and learning programs.',
                'image' => '/images/advocacies/education.png',


            ],
            [
                'name' => 'Environment',
                'description' => 'Environmental conservation, climate action, and sustainable living.',
                'image' => '/images/advocacies/environment.png',


            ],
            [
                'name' => 'Global Mobility',
                'description' => 'International exchange, travel opportunities, and cultural immersion.',
                'image' => '/images/advocacies/global-mobility.png',


            ],
            [
                'name' => 'Governance',
                'description' => 'Promoting transparency, accountability, and participatory governance.',
                'image' => '/images/advocacies/governance.png',


            ],
            [
                'name' => 'Health',
                'description' => 'Initiatives for physical, mental, and community health awareness.',
                'image' => '/images/advocacies/health.png',


            ],
            [
                'name' => 'Peace-Building and Security',
                'description' => 'Youth involvement in conflict resolution and security initiatives.',
                'image' => '/images/advocacies/peace-security.png',


            ],
            [
                'name' => 'Social Inclusion and Equity',
                'description' => 'Promoting equality, diversity, and inclusion among youth.',
                'image' => '/images/advocacies/social-inclusion.png',


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
                'name' => 'Youth organization membership',
                'description' => 'Percentage who are members of youth organizations',
            ],
            [
                'dimension_id' => $dimensionIds['Active Citizenship'],
                'name' => 'Youth volunteerism',
                'description' => 'Youth volunteerism rate',
            ],
            [
                'dimension_id' => $dimensionIds['Active Citizenship'],
                'name' => 'Organizational meeting attendance',
                'description' => 'Youth attendance in organizational meetings',
            ],
            [
                'dimension_id' => $dimensionIds['Active Citizenship'],
                'name' => 'Civic digital engagement',
                'description' => 'Percentage of youth accessing internet for interaction and posting of opinions for discussing civic and political issues',
            ],

            // Agriculture
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Agricultural program access',
                'description' => 'Percentage of young people who have accessed agricultural programs and services',
            ],
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Agricultural capital access',
                'description' => 'Percentage of young people who have accessed business capital and livelihood projects (in agriculture)',
            ],
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Youth agricultural land ownership',
                'description' => 'Percentage of youth who own agricultural land titles',
            ],
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Youth in agricultural employment',
                'description' => 'Percentage of youth employment in agriculture',
            ],
            [
                'dimension_id' => $dimensionIds['Agriculture'],
                'name' => 'Agriculture course enrollment',
                'description' => 'Percentage of youth taking agriculture-related courses',
            ],

            // Digital Media Citizenship
            [
                'dimension_id' => $dimensionIds['Digital Media Citizenship'],
                'name' => 'Digital skills',
                'description' => 'Uses or skillful in using the internet, computers, social media, or ICT in general',
            ],
            [
                'dimension_id' => $dimensionIds['Digital Media Citizenship'],
                'name' => 'Digital literacy',
                'description' => 'Understands social and broadcast media content or has digital literacy',
            ],
            [
                'dimension_id' => $dimensionIds['Digital Media Citizenship'],
                'name' => 'ICT device access',
                'description' => 'Percentage of youth with access to computers or ICT devices',
            ],

            // Economic Empowerment
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth entrepreneurship',
                'description' => 'Youth entrepreneurship rate',
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth unemployment',
                'description' => 'Youth unemployment rate',
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth underemployment',
                'description' => 'Youth underemployment rate',
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Vulnerable youth employment',
                'description' => 'Youth vulnerable employment Rate',
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'NEET youth rate',
                'description' => 'Youth not in education, employment, or training (NEET) as percent of youth population',
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth weekly work hours',
                'description' => 'Youth mean hours of work in a week (targeted to be at least 40 hours per week)',
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Youth in professional jobs',
                'description' => 'Percentage of youth in professional, technical, managerial, and administrative jobs',
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Gender earnings comparison',
                'description' => 'Average hourly earnings of female and male employees',
            ],
            [
                'dimension_id' => $dimensionIds['Economic Empowerment'],
                'name' => 'Child labor statistics',
                'description' => 'Number of female and male children aged 5-17 years old in child labor by age',
            ],

            // Education
            [
                'dimension_id' => $dimensionIds['Education'],
                'name' => 'Tertiary education completion',
                'description' => 'Proportion among youth who completed tertiary education (college education)',
            ],
            [
                'dimension_id' => $dimensionIds['Education'],
                'name' => 'Scholarship recipients',
                'description' => 'Percentage of youth grantees of scholarships and educational assistance at the tertiary level',
            ],
            [
                'dimension_id' => $dimensionIds['Education'],
                'name' => 'Youth literacy rates',
                'description' => 'Youth functional literacy rate; numeracy rate; ages 15-29',
            ],

            // Environment
            [
                'dimension_id' => $dimensionIds['Environment'],
                'name' => 'Youth environmental advocacy',
                'description' => 'Percentage of youth organizations that actively participate in advocating for and implementing environment-related laws, policies, ordinances, and programs',
            ],
            [
                'dimension_id' => $dimensionIds['Environment'],
                'name' => 'Environmental activity participation',
                'description' => 'Youth involvement in environmental activities',
            ],

            // Global Mobility
            [
                'dimension_id' => $dimensionIds['Global Mobility'],
                'name' => 'Outbound student mobility',
                'description' => 'Student outbound mobility rate at the tertiary level',
            ],
            [
                'dimension_id' => $dimensionIds['Global Mobility'],
                'name' => 'Youth exchange students',
                'description' => 'Youth exchange students',
            ],
            [
                'dimension_id' => $dimensionIds['Global Mobility'],
                'name' => 'Youth volunteering abroad',
                'description' => 'Youth volunteers abroad',
            ],
            [
                'dimension_id' => $dimensionIds['Global Mobility'],
                'name' => 'International youth professionals',
                'description' => 'International youth professionals',
            ],

            // Governance
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'Youth voter registration',
                'description' => 'Youth voter registration rate',
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'SK voter registration',
                'description' => 'SK youth voter registration rate',
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'Youth voter turnout',
                'description' => 'Youth voter turnout rate',
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'SK voter turnout',
                'description' => 'SK voter turnout rate',
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'Youth in government workforce',
                'description' => 'Ratio of youth and non-youth workers in government service',
            ],
            [
                'dimension_id' => $dimensionIds['Governance'],
                'name' => 'Youth in public leadership',
                'description' => 'Percentage of youth who are represented in the key decision-making positions in the public sector',
            ],

            // Health
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Illegal drug use',
                'description' => 'Use of illegal drugs; Years of life lost 15-39 years old (ASEAN)',
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'HIV infection rate',
                'description' => 'Proportion with HIV infection',
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Essential health coverage',
                'description' => 'Coverage of essential health services by age',
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Youth mental health issues',
                'description' => 'Proportion suffering from significant mental health problems; Years of life lost 15-39 (ASEAN)',
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Traffic accidents and drowning',
                'description' => 'Road traffic accident injuries and drowning/submersion incidents',
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Teen pregnancy rate',
                'description' => 'Teenage pregnancy rate',
            ],
            [
                'dimension_id' => $dimensionIds['Health'],
                'name' => 'Youth suicide rate',
                'description' => 'Suicide deaths',
            ],

            // Peace-Building and Security
            [
                'dimension_id' => $dimensionIds['Peace-Building and Security'],
                'name' => 'Youth peace engagement',
                'description' => 'Percentage of youth who are involved in peace-making and peace-building activities',
            ],
            [
                'dimension_id' => $dimensionIds['Peace-Building and Security'],
                'name' => 'Youth in conflict resolution',
                'description' => 'Number and percentage of youth in the leadership of relevant organizations involved in preventing and addressing conflict',
            ],
            [
                'dimension_id' => $dimensionIds['Peace-Building and Security'],
                'name' => 'Youth offending rate',
                'description' => 'Youth offender rate',
            ],
            [
                'dimension_id' => $dimensionIds['Peace-Building and Security'],
                'name' => 'Youth reoffending rate',
                'description' => 'Youth recidivism rate',
            ],

            // Social Inclusion and Equity
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Young women in government',
                'description' => 'Proportion of seats held by young women in Congress and LGUs',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Disabled youth benefits',
                'description' => 'Proportion of youth population with severe disabilities receiving disability cash benefit',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Youth poverty rate',
                'description' => 'Youth poverty rate',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Youth subsistence rate',
                'description' => 'Youth subsistence rate',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => "Women's reproductive autonomy",
                'description' => "Proportion of women aged 15-30 years who make their own informed decisions regarding sexual relations, contraceptive use, and reproductive health care",
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Electricity access rate',
                'description' => 'Percentage of population with access to electricity',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Sanitation access',
                'description' => 'Percentage of population with access to toilet facilities',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Youth hunger rate',
                'description' => 'Youth hunger incidence',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Gender leadership parity',
                'description' => 'Ratio of young men to young women in professional and management positions',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'School governance gender parity',
                'description' => 'Ratio of young men to young women who are represented in governance structures in school',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Female SK leaders',
                'description' => 'Proportion of young women elected as SK chairpersons',
            ],
            [
                'dimension_id' => $dimensionIds['Social Inclusion and Equity'],
                'name' => 'Child marriage rate',
                'description' => 'Percentage of women aged 20-24 years married or in union before age 18',
            ],
        ];

        DB::table('indicators')->insert($indicators);
    }
}

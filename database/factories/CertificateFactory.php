<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition()
    {
        $issueDate = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'certificate_number' => 'TR-' . $this->faker->unique()->numerify('########'),
            'certificate_type' => 'Certificate',
            'participant_name' => $this->faker->name(),
            'passport_nid' => $this->faker->numerify('##########'),
            'company' => $this->faker->company(),
            'training_name' => $this->faker->randomElement(['Fire Safety', 'First Aid', 'Internal Auditor']),
            'location' => 'Dhaka',
            'trainer' => $this->faker->name(),
            'training_date' => $issueDate->format('Y-m-d'),
            'training_end' => $issueDate->format('Y-m-d'),
            'issue_date' => $issueDate->format('Y-m-d'),
            'status' => 'Pending Review',
            'created_by' => 'Test User',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function approved()
    {
        return $this->state(function () {
            return [
                'status' => 'Approved',
                'approved_at' => now(),
            ];
        });
    }
}

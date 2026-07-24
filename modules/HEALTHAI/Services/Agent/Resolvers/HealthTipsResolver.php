<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;

final class HealthTipsResolver implements DataResolverInterface
{
    /**
     * Map common health issues to suggested departments at AMZ Hospital.
     * Used to enrich the AI's response with relevant department suggestions.
     */
    private const ISSUE_DEPARTMENT_MAP = [
        // Head / Neurological
        'headache'          => ['Neurology', 'Medicine'],
        'matha betha'       => ['Neurology', 'Medicine'],
        'মাথা ব্যথা'        => ['Neurology', 'Medicine'],
        'মাথাব্যথা'         => ['Neurology', 'Medicine'],
        'migraine'          => ['Neurology'],
        'matha ghura'       => ['Neurology', 'Medicine'],
        'dizziness'         => ['Neurology', 'Medicine'],
        'matha betha'       => ['Neurology', 'Medicine'],

        // Stomach / Digestive
        'stomach'           => ['Gastroenterology', 'Medicine'],
        'pet betha'         => ['Gastroenterology', 'Medicine'],
        'pete betha'        => ['Gastroenterology', 'Medicine'],
        'পেট ব্যথা'         => ['Gastroenterology', 'Medicine'],
        'পেটের সমস্যা'      => ['Gastroenterology', 'Medicine'],
        'diarrhea'          => ['Gastroenterology', 'Medicine'],
        'constipation'      => ['Gastroenterology', 'Medicine'],
        'acidity'           => ['Gastroenterology', 'Medicine'],
        'gas'               => ['Gastroenterology', 'Medicine'],
        'ulcer'             => ['Gastroenterology'],
        'gastric'           => ['Gastroenterology', 'Medicine'],

        // Body / Orthopedic
        'leg pain'          => ['Orthopedics', 'Physical Medicine'],
        'back pain'         => ['Orthopedics', 'Physical Medicine'],
        'knee pain'         => ['Orthopedics'],
        'knee'              => ['Orthopedics'],
        'joint pain'        => ['Orthopedics', 'Rheumatology'],
        'bone'              => ['Orthopedics'],
        'fracture'          => ['Orthopedics'],
        'hat betha'         => ['Orthopedics', 'Medicine'],
        'pa betha'          => ['Orthopedics', 'Medicine'],
        'hatu'              => ['Orthopedics'],
        'hatute'            => ['Orthopedics'],
        'hantu'             => ['Orthopedics'],
        'hatu betha'        => ['Orthopedics'],
        'পিঠে ব্যথা'        => ['Orthopedics', 'Physical Medicine'],
        'হাঁটু ব্যথা'        => ['Orthopedics'],
        'হাঁটু'              => ['Orthopedics'],
        'হাটু'               => ['Orthopedics'],
        'গাঁটে ব্যথা'        => ['Orthopedics', 'Rheumatology'],
        'গাটে ব্যথা'         => ['Orthopedics', 'Rheumatology'],
        'body pain'         => ['Medicine', 'Orthopedics'],
        'shorir betha'      => ['Medicine', 'Orthopedics'],
        'শরীরে ব্যথা'        => ['Medicine', 'Orthopedics'],
        'কোমরে ব্যথা'        => ['Orthopedics'],
        'komor betha'       => ['Orthopedics'],
        'ঘাড়ে ব্যথা'         => ['Orthopedics'],
        'ghar betha'        => ['Orthopedics'],
        'kal betha'         => ['Orthopedics'],
        'shoulder'          => ['Orthopedics'],
        'কাঁধে ব্যথা'         => ['Orthopedics'],
        'kandh betha'       => ['Orthopedics'],

        // Heart / Cardiovascular
        'chest pain'        => ['Cardiology', 'Emergency Medicine'],
        'heart'             => ['Cardiology'],
        'blood pressure'    => ['Cardiology', 'Medicine'],
        'high bp'           => ['Cardiology', 'Medicine'],
        'buk betha'         => ['Cardiology', 'Medicine'],
        'বুকে ব্যথা'         => ['Cardiology', 'Medicine'],

        // Respiratory
        'cough'             => ['Pulmonology', 'Medicine'],
        'cold'              => ['Medicine', 'ENT'],
        'kashi'             => ['Pulmonology', 'Medicine'],
        'কাশি'              => ['Pulmonology', 'Medicine'],
        'breathing'         => ['Pulmonology'],
        'asthma'            => ['Pulmonology'],
        'allergy'           => ['Medicine', 'Dermatology'],
        'shordi'            => ['Medicine', 'ENT'],
        'সর্দি'              => ['Medicine', 'ENT'],
        'nak diye pani'     => ['ENT', 'Medicine'],

        // Skin
        'skin'              => ['Dermatology'],
        'rash'              => ['Dermatology'],
        'itching'           => ['Dermatology'],
        'chulkani'          => ['Dermatology'],
        'চুলকানি'           => ['Dermatology'],
        'boshi'             => ['Dermatology'],
        'acne'              => ['Dermatology'],
        'eczema'            => ['Dermatology'],

        // Eye
        'eye'               => ['Ophthalmology'],
        'vision'            => ['Ophthalmology'],
        'chokh'             => ['Ophthalmology'],
        'চোখ'               => ['Ophthalmology'],

        // Ear / Nose / Throat
        'ear'               => ['ENT'],
        'nose'              => ['ENT'],
        'throat'            => ['ENT'],
        'kan'               => ['ENT'],
        'kan betha'         => ['ENT'],

        // Urinary / Kidney
        'kidney'            => ['Nephrology', 'Urology'],
        'urine'             => ['Urology', 'Nephrology'],
        'prashab'           => ['Urology', 'Nephrology'],
        'প্রস্রাব'           => ['Urology', 'Nephrology'],

        // Diabetes / Endocrine
        'diabetes'          => ['Endocrinology', 'Medicine'],
        'sugar'             => ['Endocrinology', 'Medicine'],

        // Fever
        'fever'             => ['Medicine'],
        'jor'               => ['Medicine'],
        'জ্বর'               => ['Medicine'],

        // Women's health
        'pregnancy'         => ['Gynecology', 'Obstetrics'],
        'period'            => ['Gynecology'],
        'masik'             => ['Gynecology'],
        'মাসিক'              => ['Gynecology'],

        // Children
        'child'             => ['Pediatrics'],
        'baby'              => ['Pediatrics'],
        'shishu'            => ['Pediatrics'],
        'শিশু'               => ['Pediatrics'],

        // Mental health
        'depression'        => ['Psychiatry'],
        'anxiety'           => ['Psychiatry'],
        'sleep'             => ['Psychiatry', 'Medicine'],
        'insomnia'          => ['Psychiatry', 'Medicine'],

        // Dental
        'tooth'             => ['Dental'],
        'dant'              => ['Dental'],
        'দাঁত'               => ['Dental'],
        'dant betha'        => ['Dental'],
    ];

    public function resolve(string $keyword): array
    {
        $lower = mb_strtolower(trim($keyword));

        // Find matching departments
        $suggestedDepartments = [];
        foreach (self::ISSUE_DEPARTMENT_MAP as $issue => $departments) {
            if (mb_stripos($lower, $issue) !== false) {
                foreach ($departments as $dept) {
                    if (! in_array($dept, $suggestedDepartments)) {
                        $suggestedDepartments[] = $dept;
                    }
                }
            }
        }

        // If no specific match found, suggest general Medicine
        if (empty($suggestedDepartments)) {
            $suggestedDepartments = ['Medicine'];
        }

        return [[
            'Type'                 => 'Health Tips Request',
            'HealthIssue'          => $keyword,
            'SuggestedDepartments' => $suggestedDepartments,
            'Instructions'         => 'Provide primary home-care tips for this health issue, then suggest the relevant AMZ Hospital departments and recommend consulting a specialist doctor.',
        ]];
    }
}

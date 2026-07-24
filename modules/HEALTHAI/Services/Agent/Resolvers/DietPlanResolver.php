<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;

final class DietPlanResolver implements DataResolverInterface
{
    /**
     * Common diet-related keywords and conditions.
     */
    private const CONDITIONS = [
        'diabetes'   => 'ডায়াবেটিস রোগীদের জন্য',
        'sugar'      => 'ডায়াবেটিস রোগীদের জন্য',
        'high bp'    => 'উচ্চ রক্তচাপ রোগীদের জন্য',
        'blood pressure' => 'উচ্চ রক্তচাপ রোগীদের জন্য',
        'heart'      => 'হৃদরোগীদের জন্য',
        'kidney'     => 'কিডনি রোগীদের জন্য',
        'obesity'    => 'ওজন কমানোর জন্য',
        'weight loss' => 'ওজন কমানোর জন্য',
        'weight gain' => 'ওজন বাড়ানোর জন্য',
        'pregnancy'  => 'গর্ভাবস্থায়',
        'pregnant'   => 'গর্ভাবস্থায়',
        'child'      => 'শিশুদের জন্য',
        'baby'       => 'শিশুদের জন্য',
        'elderly'    => 'বয়স্কদের জন্য',
        'gastric'    => 'গ্যাস্ট্রিক সমস্যায়',
        'ulcer'      => 'আলসার রোগীদের জন্য',
        'thyroid'    => 'থাইরয়েড রোগীদের জন্য',
        'anemia'     => 'রক্তশূন্যতায়',
        'general'    => 'সাধারণ স্বাস্থ্যের জন্য',
    ];

    public function resolve(string $keyword): array
    {
        $lower = mb_strtolower(trim($keyword));

        // Detect specific condition
        $detectedCondition = 'general';
        $conditionLabel   = 'সাধারণ স্বাস্থ্যের জন্য';

        foreach (self::CONDITIONS as $key => $label) {
            if (mb_stripos($lower, $key) !== false) {
                $detectedCondition = $key;
                $conditionLabel    = $label;
                break;
            }
        }

        return [[
            'Type'               => 'Diet Plan Request',
            'UserQuery'          => $keyword,
            'DetectedCondition'  => $detectedCondition,
            'ConditionLabel'     => $conditionLabel,
            'Instructions'       => 'Generate a professional, practical Bengali diet plan suitable for a Bangladeshi patient. Include meal times, specific Bengali food items, portion guidance, foods to avoid, and recommend consulting AMZ Hospital Nutrition department for a personalized plan.',
        ]];
    }
}

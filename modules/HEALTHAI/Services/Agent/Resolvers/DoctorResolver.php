<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\WEBSITE\Services\DoctorService;

final class DoctorResolver implements DataResolverInterface
{
    private const STRIP_WORDS = ['"', "'", 'er', 'doctor', 'daktar', 'kara', 'keo', 'ache', 'ki', '?'];

    public function __construct(private readonly DoctorService $doctorService) {}

    public function resolve(string $keyword): array
    {
        $clean      = KeywordHelper::clean($keyword, self::STRIP_WORDS);
        $allDoctors = $this->doctorService->getAllDoctors();
        $doctors    = KeywordHelper::isGeneric($clean)
            ? $allDoctors
            : $allDoctors->filter(fn ($d) => $this->matches($d, $clean));

        if ($doctors->isEmpty()) {
            return [[
                'Notice' => "বর্তমানে '{$keyword}' বিভাগের কোনো ডাক্তারের শিডিউল পাওয়া যায়নি। অনুগ্রহ করে হেল্পলাইনে যোগাযোগ করুন।",
            ]];
        }

        return $doctors->map(fn ($doctor) => [
            'Type'        => 'Doctor',
            'Name'        => $doctor->name,
            'Designation' => $doctor->designation ?? 'Consultant',
            'Department'  => $doctor->department->name ?? 'General',
            'Schedule'    => $this->formatSchedule($doctor->timetables ?? []),
        ])->values()->all();
    }

    private function matches(object $doctor, string $keyword): bool
    {
        $name  = mb_strtolower($doctor->name ?? '');
        $desig = mb_strtolower($doctor->designation ?? '');
        $dept  = $doctor->department ? mb_strtolower($doctor->department->name) : '';

        if (stripos($name, $keyword) !== false)  return true;
        if (stripos($desig, $keyword) !== false) return true;

        if (!empty($dept)) {
            if (stripos($dept, $keyword) !== false || stripos($keyword, $dept) !== false) {
                return true;
            }

            // Token-level match: "Orthopedics & Trauma" → individual words
            $tokens = array_filter(
                explode(' ', str_replace(['&', 'and', 'or', ','], ' ', $keyword)),
                fn ($w) => strlen($w) > 3,
            );
            foreach ($tokens as $token) {
                if (stripos($dept, $token) !== false) return true;
            }
        }

        return false;
    }

    private function formatSchedule(iterable $timetables): string
    {
        $slots = [];
        foreach ($timetables as $slot) {
            $slots[] = "{$slot->day}: {$slot->start_time} - {$slot->end_time}";
        }
        return $slots ? implode(', ', $slots) : 'শিডিউল জানতে কল করুন';
    }
}

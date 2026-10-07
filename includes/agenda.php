<?php

declare(strict_types=1);

/**
 * Laadt de clubagenda uit pdf/fotogroep-agenda.csv en groepeert per datum.
 */

function wmb_agenda_month_label(int $month): string
{
    static $labels = [
        1 => 'JAN', 2 => 'FEB', 3 => 'MRT', 4 => 'APR',
        5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AUG',
        9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DEC',
    ];

    return $labels[$month] ?? '';
}

function wmb_agenda_join_list(array $parts): string
{
    $parts = array_values(array_filter(array_map('trim', $parts), static fn(string $v): bool => $v !== ''));
    $count = count($parts);
    if ($count === 0) {
        return '';
    }
    if ($count === 1) {
        return $parts[0];
    }
    if ($count === 2) {
        return $parts[0] . ' en ' . $parts[1];
    }

    return implode(', ', array_slice($parts, 0, -1)) . ' en ' . $parts[$count - 1];
}

/**
 * @return list<array{day:string,month:string,iso:string,type:string,title:string,detail:string}>
 */
function wmb_load_agenda(string $csvPath, ?string $fromDate = null): array
{
    $fromDate = $fromDate ?? date('Y-m-d');
    if (!is_readable($csvPath)) {
        return [];
    }

    $handle = fopen($csvPath, 'rb');
    if ($handle === false) {
        return [];
    }

    $header = fgetcsv($handle, 0, ';');
    if (!is_array($header)) {
        fclose($handle);
        return [];
    }

    /** @var array<string, list<array<string, string>>> $grouped */
    $grouped = [];

    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        if (count($row) < 7) {
            continue;
        }

        $iso = trim((string) ($row[0] ?? ''));
        if ($iso === '' || $iso < $fromDate) {
            continue;
        }

        $grouped[$iso][] = [
            'start' => trim((string) ($row[1] ?? '')),
            'end' => trim((string) ($row[2] ?? '')),
            'meeting' => trim((string) ($row[3] ?? '')),
            'type' => trim((string) ($row[4] ?? '')),
            'part' => trim((string) ($row[5] ?? '')),
            'theme' => trim((string) ($row[6] ?? '')),
            'owner' => trim((string) ($row[7] ?? '')),
        ];
    }

    fclose($handle);
    ksort($grouped);

    $items = [];
    foreach ($grouped as $iso => $rows) {
        $ts = strtotime($iso);
        if ($ts === false) {
            continue;
        }

        $themes = [];
        $intros = [];
        $activities = [];
        $trips = [];
        $hasPhoto = false;

        foreach ($rows as $row) {
            $type = strtolower($row['type']);
            if ($type === 'uitstapje') {
                $label = $row['meeting'] !== '' ? $row['meeting'] : ($row['part'] !== '' ? $row['part'] : 'Uitstapje');
                $trips[] = $label;
            } elseif ($type === 'introductie') {
                $label = $row['theme'] !== '' ? $row['theme'] : 'Introductie';
                if ($row['owner'] !== '') {
                    $label .= ' (' . $row['owner'] . ')';
                }
                $intros[] = $label;
            } elseif ($type === 'activiteit') {
                $label = $row['part'] !== '' ? $row['part'] : ($row['meeting'] !== '' ? $row['meeting'] : 'Activiteit');
                $activities[] = $label;
            } elseif ($type === 'fotobespreking') {
                $hasPhoto = true;
                if ($row['theme'] !== '') {
                    $themes[] = $row['theme'];
                }
            }
        }

        if ($trips !== []) {
            $badge = 'Uitstapje';
            $title = wmb_agenda_join_list(array_values(array_unique($trips)));
            $detail = '';
        } elseif ($activities !== [] && !$hasPhoto) {
            $joined = wmb_agenda_join_list(array_values(array_unique($activities)));
            $badge = str_contains(strtolower($joined), 'doe-avond') ? 'Doe-avond' : 'Activiteit';
            $title = $joined;
            $detail = $intros !== []
                ? 'Introductie: ' . wmb_agenda_join_list(array_values(array_unique($intros)))
                : '';
        } else {
            $badge = 'Clubavond';
            $parts = [];
            if ($hasPhoto) {
                $themeList = wmb_agenda_join_list(array_values(array_unique($themes)));
                $parts[] = $themeList !== '' ? 'Fotobespreking: ' . $themeList : 'Fotobespreking';
            }
            if ($activities !== []) {
                $parts[] = wmb_agenda_join_list(array_values(array_unique($activities)));
            }
            if ($intros !== []) {
                $parts[] = 'Introductie: ' . wmb_agenda_join_list(array_values(array_unique($intros)));
            }
            $title = $parts[0] ?? 'Clubavond';
            $detail = count($parts) > 1 ? implode(' · ', array_slice($parts, 1)) : '';
        }

        $items[] = [
            'day' => date('j', $ts),
            'month' => wmb_agenda_month_label((int) date('n', $ts)),
            'iso' => $iso,
            'type' => $badge,
            'title' => $title,
            'detail' => $detail,
        ];
    }

    return $items;
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Stakeholder;

class UniqueStakeholderRole implements Rule
{
    protected string $role;
    protected ?int $stakeholderId;
    protected ?int $chapterId;
    protected ?int $zoneId;
    protected ?int $fieldId;
    protected ?string $portfolio;

    public function __construct(array $data, ?int $stakeholderId = null)
    {
        $this->role = $data['role'];
        $this->chapterId = $data['chapter_id'] ?? null;
        $this->zoneId = $data['zone_id'] ?? null;
        $this->fieldId = $data['field_id'] ?? null;
        $this->portfolio = $data['portfolio'] ?? null;
        $this->stakeholderId = $stakeholderId; // null for create
    }

    public function passes($attribute, $value)
    {
        $query = Stakeholder::where('role', $this->role);

        switch (true) {
            case in_array($this->role, ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary']):
                $query->where('chapter_id', $this->chapterId);
                break;
            case $this->role === 'Zonal Pastor':
                $query->where('zone_id', $this->zoneId);
                break;
            case $this->role === 'Field Pastor':
                $query->where('field_id', $this->fieldId);
                break;
            case $this->role === 'Portfolio':
                $query->where('portfolio', $this->portfolio);
                break;
        }

        if ($this->stakeholderId) {
            $query->where('id', '!=', $this->stakeholderId);
        }

        return !$query->exists();
    }

    public function message()
    {
        return "The role '{$this->role}' already exists in the selected scope.";
    }
}

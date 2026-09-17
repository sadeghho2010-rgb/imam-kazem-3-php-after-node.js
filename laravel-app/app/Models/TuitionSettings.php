<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuitionSettings extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'isBaseTuitionEqualForMarried',
        'singleBaseTuition',
        'marriedBaseTuition',
        'hasMarriageBonus',
        'marriageBonusType',
        'marriageBonusAmount',
        'marriageBonusPercent',
        'hasChildAllowance',
        'childAllowance',
        'hasTurbanAllowance',
        'turbanAllowance',
        'hasHousingAllowance',
        'housingAllowanceRented',
        'housingAllowanceDorm',
        'studyBonusEnabled',
        'studyBonusBase',
        'studyBonusTiered',
        'studyBonusTiers',
        'studyBonusPerHour',
        'studyBonusFixedAmount',
    ];

    protected $casts = [
        'isBaseTuitionEqualForMarried' => 'boolean',
        'hasMarriageBonus' => 'boolean',
        'hasChildAllowance' => 'boolean',
        'hasTurbanAllowance' => 'boolean',
        'hasHousingAllowance' => 'boolean',
        'studyBonusEnabled' => 'boolean',
        'studyBonusTiered' => 'boolean',
        'studyBonusTiers' => 'array',
        'singleBaseTuition' => 'decimal:2',
        'marriedBaseTuition' => 'decimal:2',
        'marriageBonusAmount' => 'decimal:2',
        'marriageBonusPercent' => 'decimal:2',
        'childAllowance' => 'decimal:2',
        'turbanAllowance' => 'decimal:2',
        'housingAllowanceRented' => 'decimal:2',
        'housingAllowanceDorm' => 'decimal:2',
        'studyBonusPerHour' => 'decimal:2',
        'studyBonusFixedAmount' => 'decimal:2',
    ];
}

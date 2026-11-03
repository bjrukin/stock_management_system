<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalQualification extends Model
{
    protected $fillable = [
        'professional_id', 
	'level_id', 
	'name', 
	'board_university', 
	'passed_year', 
	'admission_year', 
	'college_name', 
	'registration_number', 
	'result_type', 
	'gpa', 
	'percentage', 
	'physics_marks', 
	'chemistry_marks', 
	'biology_marks', 
	'average', 
	'transcript_image', 
	'provisional_image', 
	'character_image', 
	'ojt_image', 
	'visa_image', 
	'noc_image', 
	'intership_image', 
	'passport_image', 
	'licence', 
	'council_registration_certificate', 
	'ojt_pcl_community_1_image', 
	'ojt_pcl_community_2_image', 
	'transcript_mas_marksheet', 
	'transcript_bac_1', 
	'transcript_bac_2', 
	'transcript_bac_3', 
	'transcript_bac_4', 
	'transcript_bac_5', 
	'transcript_bac_6', 
	'transcript_bac_7', 
	'transcript_bac_8', 
	'migration_image', 
	'equivalence_certificate', 
    ];

    protected $appends = [
        'full_transcript_image',
        'full_provisional_image',
        'full_character_image',
        'full_ojt_image',
        'full_equivalence_certificate',
        'full_council_registration_certificate',
        'full_ojt_pcl_community_1_image',
        'full_ojt_pcl_community_2_image',
        'full_transcript_bac_1',
        'full_transcript_bac_2',
        'full_transcript_bac_3',
        'full_transcript_bac_4',
        'full_transcript_bac_5',
        'full_transcript_bac_6',
        'full_transcript_bac_7',
        'full_transcript_bac_8',
        'full_migration_image',
        'full_visa_image',
        'full_noc_image',
        'full_intership_image',
        'full_passport_image',
        'transcript_mas_marksheet',
    ];

    public function getFullTranscriptImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_image);
    }

    public function getFullProvisionalImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->provisional_image);
    }

    public function getFullCharacterImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->character_image);
    }
    public function getFullOjtImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->ojt_image);
    }

    public function getFullEquivalenceCertificateAttribute()
    {
        return asset('storage/uploads/student/' . $this->equivalence_certificate);
    }
    public function getFullCouncilRegistrationCertificateAttribute()
    {
        return asset('storage/uploads/student/' . $this->council_registration_certificate);
    }
    public function getFullOjtPclCommunity1ImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->ojt_pcl_community_1_image);
    }
    public function getFullOjtPclCommunity2ImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->ojt_pcl_community_2_image);
    }

    public function getFullTranscriptBac1Attribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_bac_1);
    }

    public function getFullTranscriptBac2Attribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_bac_2);
    }

    public function getFullTranscriptBac3Attribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_bac_3);
    }

    public function getFullTranscriptBac4Attribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_bac_4);
    }

    public function getFullTranscriptBac5Attribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_bac_5);
    }

    public function getFullTranscriptBac6Attribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_bac_6);
    }

    public function getFullTranscriptBac7Attribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_bac_7);
    }

    public function getFullTranscriptBac8Attribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_bac_8);
    }
    public function getFullMigrationImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->migration_image);
    }

    public function getFullIntershipImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->intership_image);
    }
    public function getFullNocImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->noc_image);
    }

    public function getFullVisaImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->visa_image);
    }

    public function getFullPassportImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->passport_image);
    }

    public function getFullTranscriptMasMarksheetAttribute()
    {
        return asset('storage/uploads/student/' . $this->transcript_mas_marksheet);
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'name', 'id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
    public function eligibility_mark()
    {
        return $this->belongsTo(EligibilityMark::class, 'level_id', 'level_id');
    }
}

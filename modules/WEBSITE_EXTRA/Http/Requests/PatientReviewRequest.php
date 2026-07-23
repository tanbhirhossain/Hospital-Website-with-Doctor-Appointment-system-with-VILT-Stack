<?php
namespace Modules\WEBSITE_EXTRA\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class PatientReviewRequest extends FormRequest
{
    public function rules(): array { return ['patient_name'=>'required|string|max:255','patient_phone'=>'nullable|string|max:30','doctor_id'=>'nullable|exists:doctors,id','department'=>'nullable|string|max:255','review_text'=>'required|string','rating'=>'required|integer|min:1|max:5','status'=>'nullable|in:pending,approved,rejected','is_featured'=>'boolean','admin_note'=>'nullable|string|max:500','avatar'=>'nullable|image|max:2048']; }
}

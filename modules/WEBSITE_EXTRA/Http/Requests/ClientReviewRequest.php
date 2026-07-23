<?php
namespace Modules\WEBSITE_EXTRA\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ClientReviewRequest extends FormRequest
{
    public function rules(): array { return ['client_name'=>'required|string|max:255','client_designation'=>'nullable|string|max:255','client_company'=>'nullable|string|max:255','review_text'=>'required|string','rating'=>'required|integer|min:1|max:5','sort_order'=>'nullable|integer','is_active'=>'boolean','is_featured'=>'boolean','avatar'=>'nullable|image|max:2048']; }
}

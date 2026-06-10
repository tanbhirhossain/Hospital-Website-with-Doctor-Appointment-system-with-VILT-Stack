<?php
namespace Modules\WEBSITE_EXTRA\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class HealthPackageRequest extends FormRequest
{
    public function rules(): array { return ['name'=>'required|string|max:255','slug'=>['nullable','string','max:255',Rule::unique('health_packages','slug')->ignore($this->route('id'))],'badge'=>'nullable|string|max:100','short_description'=>'nullable|string|max:500','description'=>'nullable|string','includes'=>'nullable|string','original_price'=>'required|numeric|min:0','discounted_price'=>'nullable|numeric|min:0','duration_days'=>'nullable|integer|min:1','category'=>'required|in:general,cardiac,diabetes,cancer,maternity,pediatric,executive,other','sort_order'=>'nullable|integer','is_active'=>'boolean','is_featured'=>'boolean','meta_title'=>'nullable|string|max:255','meta_description'=>'nullable|string|max:500','indexable'=>'boolean','thumbnail'=>'nullable|image|max:4096']; }
}

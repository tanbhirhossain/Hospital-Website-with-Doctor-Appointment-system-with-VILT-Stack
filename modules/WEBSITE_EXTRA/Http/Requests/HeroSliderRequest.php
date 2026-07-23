<?php
namespace Modules\WEBSITE_EXTRA\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class HeroSliderRequest extends FormRequest
{
    public function rules(): array { 
        
    return [
        
        'title'=>'required|string|max:255',
        'subtitle'=>'nullable|string|max:255',
        'description'=>'nullable|string',
        'button_text'=>'nullable|string|max:100',
        'button_url'=>'nullable|string|max:500',
        'button_text_secondary'=>'nullable|string|max:100',
        'button_url_secondary'=>'nullable|string|max:500',
        'badge_text'=>'nullable|string|max:100',
        'sort_order'=>'nullable|integer',
        'is_active'=>'boolean',
        'slide_image'=>'nullable|image|max:4096',
        'buttons' => 'nullable|string|max:500',
        'gradients' => 'nullable|string|max:256'
        
        ]; 
        
    }
}
